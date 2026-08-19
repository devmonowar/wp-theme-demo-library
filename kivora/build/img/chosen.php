<?php
/**
 * The candidate picked for each slot, and the alt text for it.
 *
 * `n` is the number on that slot's contact sheet in sheets/. Every choice was
 * made by looking at the sheet; several were rejected for carrying a stock
 * library's watermark despite being listed as CC0.
 *
 * Alt text describes the photograph that is actually there. It is written to be
 * useful to someone who cannot see it, not to repeat the caption or the post
 * title beside it.
 *
 * @return array<string, array{n:int, alt:string}>
 */

return array(

	'walk-footpath' => array(
		'n'   => 5,
		'alt' => 'A footpath worn through rough grass climbs a green ridge under a low, clouded sky.',
	),

	'morning-mist' => array(
		'n'   => 4,
		'alt' => 'A rough track disappears into thick fog, a line of dark trees just visible across a frosted field.',
	),

	'desk-notebook' => array(
		'n'   => 4,
		'alt' => 'An open notebook and a slim pen lying on dark, worn floorboards.',
	),

	'coast-cliffs' => array(
		'n'   => 4,
		'alt' => 'A wide empty beach at low tide in black and white, with a wooded headland at the right and wet sand catching the light.',
	),

	'winter-woods' => array(
		'n'   => 2,
		'alt' => 'A path dusted with light snow runs between tall misty conifers, with a walker in yellow trousers standing on a fallen log beside it.',
	),

	'stone-wall' => array(
		'n'   => 4,
		'alt' => 'A mossy dry stone wall runs beside a green lane between fields, with a small red-roofed hut across the field.',
	),

	'rain-window' => array(
		'n'   => 1,
		'alt' => 'Rain beaded on a window pane, with green trees and a roof blurred behind the glass.',
	),

	'river-dusk' => array(
		'n'   => 2,
		'alt' => 'Wide dark water at dusk, the far bank a black line under a narrow band of orange afterglow reflected on the surface.',
	),

	'about-field' => array(
		'n'   => 1,
		'alt' => 'Moorland in flower, purple to the horizon, with one wind-shaped tree standing alone under a blue sky.',
	),

	'about-walking' => array(
		'n'   => 2,
		'alt' => 'A walker on a stony path crossing a meadow of yellow flowers towards a ridge of pale mountains.',
	),
);
