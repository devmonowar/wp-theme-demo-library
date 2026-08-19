<?php
/** One sheet of everything in final/, to check the crops in a single look. */
$files = glob( __DIR__ . '/final-fieldnotes/*.jpg' );
sort( $files );

$cols = 2;
$cw   = 640;
$ch   = 430;
$pad  = 8;
$lab  = 20;
$rows = (int) ceil( count( $files ) / $cols );

$sheet = imagecreatetruecolor( $cols * ( $cw + $pad ) + $pad, $rows * ( $ch + $pad + $lab ) + $pad );
imagefill( $sheet, 0, 0, imagecolorallocate( $sheet, 22, 22, 22 ) );
$fg = imagecolorallocate( $sheet, 255, 255, 255 );

foreach ( $files as $i => $file ) {
	$im = imagecreatefromjpeg( $file );
	$x  = $pad + ( $i % $cols ) * ( $cw + $pad );
	$y  = $pad + intdiv( $i, $cols ) * ( $ch + $pad + $lab ) + $lab;
	$tw = imagesx( $im );
	$th = imagesy( $im );
	$r  = min( $cw / $tw, $ch / $th );
	$dw = (int) round( $tw * $r );
	$dh = (int) round( $th * $r );
	imagecopyresampled( $sheet, $im, $x + intdiv( $cw - $dw, 2 ), $y + intdiv( $ch - $dh, 2 ), 0, 0, $dw, $dh, $tw, $th );
	imagestring( $sheet, 4, $x, $y - $lab + 4, basename( $file, '.jpg' ), $fg );
}

imagejpeg( $sheet, __DIR__ . '/final-sheet-fieldnotes.jpg', 88 );
echo "written\n";
