<?php
/**
 * Turn the chosen candidates into the files a demo ships.
 *
 *     php build-assets.php [--set=<demo>]
 *
 * Reads chosen-<demo>.php (slot => candidate number + alt text), fetches that
 * candidate's original from the provider, crops it to the shape the layout
 * wants, caps the width, and writes:
 *
 *   final-<demo>/<slot>.jpg   the file the demo imports
 *   credits-<demo>.json       title, creator, licence and source for CREDITS.md
 *
 * Cropping is centre-weighted and deliberate: a blog grid lines up only if
 * every thumbnail is the same shape, and letting WordPress crop for us would
 * mean shipping originals four times the size anyone needs.
 */

const UA      = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36';
const MAX_W   = 1600;
const QUALITY = 82;

$base = __DIR__;
$set  = 'fieldnotes';

foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( str_starts_with( $arg, '--set=' ) ) {
		$set = substr( $arg, 6 );
	}
}

$chosen_file = "$base/chosen-$set.php";

if ( ! is_readable( $chosen_file ) ) {
	fwrite( STDERR, "No choices for '$set' (expected " . basename( $chosen_file ) . ")\n" );
	exit( 1 );
}

$chosen = require $chosen_file;

/**
 * Target aspect per slot. 3:2 for the photographs, 1:1 for a portrait, and 16:9
 * where a section is wider than it is tall.
 */
$aspect = array(
	'portrait' => 1 / 1,
	'*'        => 3 / 2,
);

if ( is_readable( "$base/aspect-$set.php" ) ) {
	$aspect = array_merge( $aspect, require "$base/aspect-$set.php" );
}

/**
 * GET a URL.
 *
 * @param string $url URL.
 * @return string|null
 */
function get( string $url ): ?string {
	$ch = curl_init( $url );
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 90,
			CURLOPT_USERAGENT      => UA,
		)
	);
	$body = curl_exec( $ch );
	$code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );

	return ( false !== $body && 200 === $code ) ? $body : null;
}

/**
 * Centre-crop to an aspect ratio, then cap the width.
 *
 * @param \GdImage $src   Source image.
 * @param float    $ratio Width divided by height.
 * @param int      $max_w Maximum output width.
 * @return \GdImage
 */
function crop_to( $src, float $ratio, int $max_w ) {
	$w = imagesx( $src );
	$h = imagesy( $src );

	$target_h = (int) round( $w / $ratio );
	$target_w = $w;

	if ( $target_h > $h ) {
		$target_h = $h;
		$target_w = (int) round( $h * $ratio );
	}

	$x = intdiv( $w - $target_w, 2 );

	/*
	 * Landscapes are cropped a little above centre: the sky is usually the
	 * least interesting third of the frame, and a dead-centre crop keeps it.
	 */
	$y = $ratio > 1.2
		? (int) round( ( $h - $target_h ) * 0.38 )
		: intdiv( $h - $target_h, 2 );

	$out_w = min( $max_w, $target_w );
	$out_h = (int) round( $out_w / $ratio );

	$dst = imagecreatetruecolor( $out_w, $out_h );
	imagecopyresampled( $dst, $src, 0, 0, $x, $y, $out_w, $out_h, $target_w, $target_h );

	return $dst;
}

if ( ! is_dir( "$base/final-$set" ) ) {
	mkdir( "$base/final-$set", 0777, true );
}

$credits = array();

foreach ( $chosen as $slot => $pick ) {
	/*
	 * A slot may borrow another slot's contact sheet with 'from'. Some subjects
	 * come back with one candidate while their neighbours come back with six,
	 * and searching again for a picture already on disk is twenty-five seconds
	 * spent to arrive in the same place.
	 */
	$meta_path = "$base/cand/" . ( $pick['from'] ?? $slot ) . '/meta.json';

	if ( ! file_exists( $meta_path ) ) {
		printf( "%-16s NO META\n", $slot );
		continue;
	}

	$meta = json_decode( (string) file_get_contents( $meta_path ), true );
	$item = $meta[ (string) $pick['n'] ] ?? null;

	if ( ! $item ) {
		printf( "%-16s candidate #%s not in meta.json\n", $slot, $pick['n'] );
		continue;
	}

	$bytes = get( (string) $item['file'] );

	if ( null === $bytes ) {
		printf( "%-16s DOWNLOAD FAILED %s\n", $slot, $item['file'] );
		continue;
	}

	$im = @imagecreatefromstring( $bytes );

	if ( ! $im ) {
		printf( "%-16s NOT AN IMAGE\n", $slot );
		continue;
	}

	$ratio = $aspect[ $slot ] ?? $aspect['*'];
	$out   = crop_to( $im, $ratio, MAX_W );

	imagejpeg( $out, "$base/final-$set/$slot.jpg", QUALITY );

	$credits[ $slot ] = array(
		'file'    => "$slot.jpg",
		'alt'     => $pick['alt'],
		'title'   => $item['title'],
		'creator' => $item['creator'],
		'licence' => trim( (string) $item['licence'] ),
		'source'  => $item['source'],
		'via'     => $item['provider'] ?? '',
	);

	printf(
		"%-16s %dx%d  %d KB  (%s)\n",
		$slot,
		imagesx( $out ),
		imagesy( $out ),
		(int) round( filesize( "$base/final-$set/$slot.jpg" ) / 1024 ),
		$item['licence']
	);
}

file_put_contents( "$base/credits-$set.json", json_encode( $credits, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

printf( "\n%d file(s) in final-$set/, credits-$set.json written\n", count( $credits ) );
