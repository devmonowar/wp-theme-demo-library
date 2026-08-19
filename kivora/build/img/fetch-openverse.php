<?php
/**
 * Fetch CC0 candidates from Openverse for each image slot in Demo 2, and build
 * one numbered contact sheet per slot.
 *
 *     php fetch-openverse.php [slot]
 *
 * Openverse is where Demo 1's photographs came from and is worth the trouble:
 * Wikimedia Commons has plenty of CC0 but almost all of it is documentary --
 * roadworks and flat fields -- while Openverse reaches the stock libraries.
 *
 * The trouble is that it sits behind Cloudflare, which answers a burst of
 * anonymous searches with a 429 and then a challenge page for several minutes.
 * One search per slot, twenty-five seconds apart, stays under it. Image
 * downloads go to the provider's own CDN and do not count against that.
 *
 * Writes:
 *   cand/<slot>/<n>.jpg      candidate, downscaled for review
 *   cand/<slot>/meta.json    title, creator, licence, source page, dimensions
 *   sheets/<slot>.jpg        3-across contact sheet, numbered
 */

const UA         = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36';
const API        = 'https://api.openverse.org/v1/images/';
const PER_SLOT   = 6;
const MIN_W      = 1200;
const REVIEW_W   = 460;
const SHEET_COLS = 3;
const GAP        = 25;

$base = __DIR__;

/**
 * Slot => search variants, tried in order until the sheet is full.
 */
$slots = array(
	'walk-footpath' => array( 'hiking trail hills', 'countryside path', 'trail landscape' ),
	'morning-mist'  => array( 'foggy field morning', 'mist landscape', 'fog trees' ),
	'desk-notebook' => array( 'notebook pen desk', 'journal writing', 'notebook coffee' ),
	'coast-cliffs'  => array( 'coastal cliffs ocean', 'sea cliffs', 'rocky coast' ),
	'winter-woods'  => array( 'winter forest path', 'snowy woods', 'forest winter' ),
	'stone-wall'    => array( 'stone wall countryside', 'old stone wall', 'rural fence field' ),
	'rain-window'   => array( 'rain window drops', 'rainy window', 'water droplets glass' ),
	'river-dusk'    => array( 'river sunset', 'lake evening light', 'estuary water dusk' ),
	'about-walking' => array( 'hiker backpack trail', 'person walking nature', 'walking outdoors' ),
	// No portrait slot: the CC0 portraits on offer are identifiable people, and
	// a shipped theme demo should not carry someone's face around the world.
	// The About page wants a place, not a desk: every CC0 "writing desk" is a
	// laptop on white melamine, which belongs to a different website entirely.
	'about-field'   => array( 'moorland heather', 'field gate countryside', 'meadow hills path' ),
);

$only  = $argv[1] ?? '';
$first = true;

/**
 * GET a URL once.
 *
 * @param string   $url  URL.
 * @param int|null $code Filled with the HTTP status.
 * @return string|null
 */
function get_once( string $url, ?int &$code = null ): ?string {
	$ch = curl_init( $url );
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_USERAGENT      => UA,
		)
	);
	$body = curl_exec( $ch );
	$code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );

	return ( false !== $body && 200 === $code ) ? $body : null;
}

/**
 * GET, backing off when Cloudflare says no.
 *
 * @param string $url   URL.
 * @param int    $tries Attempts.
 * @return string|null
 */
function get( string $url, int $tries = 3 ): ?string {
	for ( $i = 0; $i < $tries; $i++ ) {
		$body = get_once( $url, $code );

		if ( null !== $body ) {
			return $body;
		}

		if ( 429 !== $code && 403 !== $code ) {
			return null;
		}

		sleep( 60 * ( $i + 1 ) );
	}

	return null;
}

/**
 * Downscale to a width, keeping aspect.
 *
 * @param \GdImage $src   Source.
 * @param int      $width Target width.
 * @return \GdImage
 */
function scale_to( $src, int $width ) {
	$w = imagesx( $src );
	$h = imagesy( $src );

	if ( $w <= $width ) {
		return $src;
	}

	$nh  = (int) round( $h * ( $width / $w ) );
	$dst = imagecreatetruecolor( $width, $nh );
	imagecopyresampled( $dst, $src, 0, 0, 0, 0, $width, $nh, $w, $h );

	return $dst;
}

/**
 * Write the numbered contact sheet for a slot.
 *
 * @param string                        $path  Output path.
 * @param array<int, \GdImage>          $tiles Review images.
 * @param array<int, array<string, mixed>> $meta  Metadata, for the captions.
 */
function write_sheet( string $path, array $tiles, array $meta ): void {
	if ( ! $tiles ) {
		return;
	}

	$cols   = min( SHEET_COLS, count( $tiles ) );
	$rows   = (int) ceil( count( $tiles ) / $cols );
	$cell_w = REVIEW_W;
	$cell_h = (int) round( REVIEW_W * 0.72 );
	$pad    = 8;
	$label  = 22;

	$sheet = imagecreatetruecolor( $cols * ( $cell_w + $pad ) + $pad, $rows * ( $cell_h + $pad + $label ) + $pad );
	imagefill( $sheet, 0, 0, imagecolorallocate( $sheet, 24, 24, 24 ) );
	$fg = imagecolorallocate( $sheet, 255, 255, 255 );
	$i  = 0;

	foreach ( $tiles as $num => $tile ) {
		$col = $i % $cols;
		$row = intdiv( $i, $cols );
		$x   = $pad + $col * ( $cell_w + $pad );
		$y   = $pad + $row * ( $cell_h + $pad + $label ) + $label;

		$tw    = imagesx( $tile );
		$th    = imagesy( $tile );
		$ratio = min( $cell_w / $tw, $cell_h / $th );
		$dw    = (int) round( $tw * $ratio );
		$dh    = (int) round( $th * $ratio );

		imagecopyresampled( $sheet, $tile, $x + intdiv( $cell_w - $dw, 2 ), $y + intdiv( $cell_h - $dh, 2 ), 0, 0, $dw, $dh, $tw, $th );
		imagestring( $sheet, 5, $x, $y - $label + 4, "#$num  " . substr( (string) $meta[ $num ]['title'], 0, 44 ), $fg );
		++$i;
	}

	imagejpeg( $sheet, $path, 88 );
}

if ( ! is_dir( "$base/sheets" ) ) {
	mkdir( "$base/sheets", 0777, true );
}

foreach ( $slots as $slot => $variants ) {
	if ( '' !== $only && $only !== $slot ) {
		continue;
	}

	$dir = "$base/cand/$slot";

	if ( ! is_dir( $dir ) ) {
		mkdir( $dir, 0777, true );
	}

	array_map( 'unlink', (array) glob( "$dir/*.jpg" ) );

	$meta  = array();
	$tiles = array();
	$seen  = array();
	$n     = 0;

	foreach ( $variants as $variant ) {
		if ( $n >= PER_SLOT ) {
			break;
		}

		if ( ! $first ) {
			sleep( GAP );
		}

		$first = false;

		$url = API . '?' . http_build_query(
			array(
				'q'         => $variant,
				'license'   => 'cc0',
				'size'      => 'large',
				'page_size' => 20,
			)
		);

		$body = get( $url );

		if ( null === $body ) {
			printf( "%-16s search failed: %s\n", $slot, $variant );
			continue;
		}

		$results = json_decode( $body, true )['results'] ?? array();

		foreach ( $results as $r ) {
			if ( $n >= PER_SLOT ) {
				break;
			}

			$id = (string) ( $r['id'] ?? '' );

			if ( '' === $id || isset( $seen[ $id ] ) ) {
				continue;
			}

			$seen[ $id ] = true;

			$w = (int) ( $r['width'] ?? 0 );
			$h = (int) ( $r['height'] ?? 0 );

			if ( $w < MIN_W || $w <= $h ) {
				continue;
			}

			$bytes = get_once( (string) $r['url'] );

			if ( null === $bytes ) {
				continue;
			}

			$im = @imagecreatefromstring( $bytes );

			if ( ! $im ) {
				continue;
			}

			++$n;
			$review = scale_to( $im, REVIEW_W );
			imagejpeg( $review, "$dir/$n.jpg", 86 );
			$tiles[ $n ] = $review;

			$meta[ $n ] = array(
				'title'    => (string) ( $r['title'] ?? '' ),
				'creator'  => (string) ( $r['creator'] ?? '' ),
				'licence'  => strtoupper( (string) ( $r['license'] ?? '' ) ) . ' ' . (string) ( $r['license_version'] ?? '' ),
				'source'   => (string) ( $r['foreign_landing_url'] ?? '' ),
				'provider' => (string) ( $r['provider'] ?? '' ),
				'file'     => (string) ( $r['url'] ?? '' ),
				'width'    => $w,
				'height'   => $h,
				'query'    => $variant,
			);
		}
	}

	file_put_contents( "$dir/meta.json", json_encode( $meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	write_sheet( "$base/sheets/$slot.jpg", $tiles, $meta );

	printf( "%-16s %d candidate(s)\n", $slot, $n );
}
