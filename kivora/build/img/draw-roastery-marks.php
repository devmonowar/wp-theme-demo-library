<?php
/**
 * Draw the flat artwork Brackenmoor Roastery needs: its own logo and site icon,
 * and the five customer wordmarks that fill the theme's logo-row pattern.
 *
 *     php draw-roastery-marks.php
 *
 * These are not photographs and there is nothing to license: they are drawn
 * here, in code, and carry the same GPL as the theme. Drawing them rather than
 * finding them is the only honest way to put five customer logos on a demo
 * page — every real logo is somebody's trademark, and a demo that lands on
 * thousands of sites is the last place to borrow one.
 *
 * Writes into ../../assets/roastery/uploads/2026/08/.
 */

const FONT_BOLD  = 'C:/Windows/Fonts/segoeuib.ttf';
const FONT_LIGHT = 'C:/Windows/Fonts/segoeui.ttf';

$out = dirname( __DIR__, 2 ) . '/assets/roastery/uploads/2026/08';

if ( ! is_dir( $out ) ) {
	mkdir( $out, 0777, true );
}

foreach ( array( FONT_BOLD, FONT_LIGHT ) as $font ) {
	if ( ! is_readable( $font ) ) {
		fwrite( STDERR, "Missing font: $font\n" );
		exit( 1 );
	}
}

/**
 * A transparent canvas.
 *
 * @param int $w Width.
 * @param int $h Height.
 * @return \GdImage
 */
function canvas( int $w, int $h ) {
	$im = imagecreatetruecolor( $w, $h );
	imagealphablending( $im, false );
	imagesavealpha( $im, true );
	imagefilledrectangle( $im, 0, 0, $w, $h, imagecolorallocatealpha( $im, 0, 0, 0, 127 ) );
	imagealphablending( $im, true );

	return $im;
}

/**
 * Allocate a colour from a hex string.
 *
 * @param \GdImage $im  Image.
 * @param string   $hex Six-digit hex.
 * @return int
 */
function hex( $im, string $hex ): int {
	return imagecolorallocate(
		$im,
		(int) hexdec( substr( $hex, 0, 2 ) ),
		(int) hexdec( substr( $hex, 2, 2 ) ),
		(int) hexdec( substr( $hex, 4, 2 ) )
	);
}

/**
 * Draw text and return the width it took.
 *
 * @param \GdImage $im       Image.
 * @param int      $size     Point size.
 * @param int      $x        Left.
 * @param int      $y        Baseline.
 * @param int      $colour   Colour.
 * @param string   $font     Font file.
 * @param string   $text     Text.
 * @param float    $tracking Extra pixels between characters.
 * @return int
 */
function text( $im, int $size, int $x, int $y, int $colour, string $font, string $text, float $tracking = 0.0 ): int {
	if ( 0.0 === $tracking ) {
		$box = imagettftext( $im, $size, 0, $x, $y, $colour, $font, $text );

		return (int) ( $box[2] - $box[0] );
	}

	$cursor = $x;

	foreach ( preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY ) as $char ) {
		$box     = imagettftext( $im, $size, 0, (int) round( $cursor ), $y, $colour, $font, $char );
		$cursor += ( $box[2] - $box[0] ) + $tracking;
	}

	return (int) round( $cursor - $x - $tracking );
}

/**
 * A coffee bean: an ellipse with the crease cut through it.
 *
 * @param \GdImage $im     Image.
 * @param int      $cx     Centre x.
 * @param int      $cy     Centre y.
 * @param int      $w      Width.
 * @param int      $h      Height.
 * @param int      $fill   Bean colour.
 * @param int      $crease Crease colour, drawn over the fill.
 */
function bean( $im, int $cx, int $cy, int $w, int $h, int $fill, int $crease ): void {
	imagefilledellipse( $im, $cx, $cy, $w, $h, $fill );

	// The crease: a narrow ellipse in the background colour, inset top and
	// bottom so it reads as a groove rather than a slice.
	imagefilledellipse( $im, $cx, $cy, (int) round( $w * 0.16 ), (int) round( $h * 0.82 ), $crease );
}

/* ------------------------------------------------------------------- logo */

/*
 * Tight. The theme caps a header logo at 2.5rem tall, so a canvas with
 * comfortable margins around the type arrives on the page as a smudge: the
 * whole image scales, not just the words. Everything here is sized for what it
 * looks like at 40 pixels high.
 */
$logo  = canvas( 470, 96 );
$green = hex( $logo, '166534' );
$amber = hex( $logo, 'FCD34D' );
$dark  = hex( $logo, '14241A' );
$muted = hex( $logo, '5F6B62' );

imagefilledrectangle( $logo, 0, 0, 88, 96, $green );
bean( $logo, 44, 48, 48, 62, $amber, $green );

text( $logo, 31, 106, 54, $dark, FONT_BOLD, 'Brackenmoor' );
text( $logo, 15, 108, 82, $muted, FONT_LIGHT, 'ROASTERY', 5.5 );

imagepng( $logo, "$out/brackenmoor-roastery-logo.png" );
echo "brackenmoor-roastery-logo.png\n";

/* --------------------------------------------------------------- site icon */

$icon  = canvas( 512, 512 );
$green = hex( $icon, '166534' );
$amber = hex( $icon, 'FCD34D' );

imagefilledrectangle( $icon, 0, 0, 512, 512, $green );
bean( $icon, 256, 256, 250, 320, $amber, $green );

imagepng( $icon, "$out/brackenmoor-roastery-icon.png" );
echo "brackenmoor-roastery-icon.png\n";

/* ------------------------------------------------------- customer wordmarks */

/*
 * Five fictional wholesale accounts. Each is a plain wordmark with one
 * geometric mark, deliberately quiet: on the page they sit in a row under
 * "Poured at", and a row of shouting logos would pull the eye off everything
 * above it.
 */
$clients = array(
	'client-corner-house'  => array( 'The Corner House', 'CAFÉ', 'cup' ),
	'client-fold-lane'     => array( 'Fold Lane', 'BAKERY', 'wheat' ),
	'client-kestrel-works' => array( 'Kestrel Works', 'STUDIOS', 'square' ),
	'client-meadow-lane'   => array( 'Meadow Lane', 'DELI', 'circle' ),
	'client-harbour-rooms' => array( 'The Harbour Rooms', 'HOTEL', 'roof' ),
);

foreach ( $clients as $file => $client ) {
	list( $name, $sub, $mark ) = $client;

	/*
	 * Same reasoning as the logo: the pattern renders these at 160 pixels wide,
	 * so the canvas is only as big as the words need. A roomier one would give
	 * a tidier PNG and an unreadable row.
	 */
	$im    = canvas( 400, 88 );
	$ink   = hex( $im, '3F4A43' );
	$soft  = hex( $im, '7B8580' );
	$paper = imagecolorallocate( $im, 255, 255, 255 );

	$cx = 30;
	$cy = 44;

	switch ( $mark ) {
		case 'cup':
			imagefilledpolygon( $im, array( $cx - 20, $cy - 14, $cx + 15, $cy - 14, $cx + 10, $cy + 19, $cx - 15, $cy + 19 ), $ink );
			imagefilledellipse( $im, $cx + 20, $cy - 2, 20, 20, $ink );
			imagefilledellipse( $im, $cx + 20, $cy - 2, 9, 9, $paper );
			break;

		case 'wheat':
			imagefilledrectangle( $im, $cx - 2, $cy - 21, $cx + 2, $cy + 22, $ink );

			for ( $i = 0; $i < 4; $i++ ) {
				$y = $cy - 17 + ( $i * 10 );
				imagefilledellipse( $im, $cx - 9, $y, 14, 8, $ink );
				imagefilledellipse( $im, $cx + 9, $y, 14, 8, $ink );
			}
			break;

		case 'square':
			imagefilledrectangle( $im, $cx - 20, $cy - 20, $cx + 20, $cy + 20, $ink );
			imagefilledrectangle( $im, $cx - 8, $cy - 8, $cx + 8, $cy + 8, $paper );
			break;

		case 'circle':
			imagefilledellipse( $im, $cx, $cy, 43, 43, $ink );
			imagefilledellipse( $im, $cx, $cy, 19, 19, $paper );
			break;

		case 'roof':
		default:
			imagefilledpolygon( $im, array( $cx - 22, $cy + 3, $cx, $cy - 21, $cx + 22, $cy + 3 ), $ink );
			imagefilledrectangle( $im, $cx - 13, $cy + 6, $cx + 13, $cy + 22, $ink );
			break;
	}

	text( $im, 21, 64, 46, $ink, FONT_BOLD, $name );
	text( $im, 11, 66, 68, $soft, FONT_LIGHT, $sub, 3.0 );

	imagepng( $im, "$out/$file.png" );
	echo "$file.png\n";
}
