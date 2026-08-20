<?php
/**
 * The candidate picked for each Brackenmoor Roastery slot, and the alt text
 * for it.
 *
 * `n` is the number on that slot's contact sheet in sheets/. Every choice was
 * made by looking at the sheet rather than by reading file names.
 *
 * Alt text describes the photograph that is actually there. It is written to be
 * useful to someone who cannot see it, not to repeat the caption or the post
 * title beside it. Where the picture and the caption disagreed, the caption was
 * rewritten — the copy is ours and the photograph is not.
 *
 * A slot may add 'from' to borrow another slot's candidate sheet.
 *
 * @return array<string, array{n:int, alt:string, from?:string}>
 */

return array(

	'hero-roastery' => array(
		'n'   => 3,
		'alt' => 'A close, shallow view across a bed of freshly roasted coffee beans, still glossy.',
	),

	'about-roasting' => array(
		'n'   => 1,
		'alt' => 'A hand held over a wide wooden tray of pale green unroasted coffee beans, picking one out.',
	),

	'services-cupping' => array(
		'n'   => 4,
		'alt' => 'A ceramic filter cone and glass carafe standing on a set of digital scales on a wooden bench.',
	),

	'contact-shopfront' => array(
		'n'   => 6,
		'alt' => 'A narrow cobbled lane between brick buildings, shopfronts and a few people at the far end.',
	),

	'work-corner-cafe' => array(
		'n'   => 6,
		'alt' => 'The inside of a café in daylight: people at wooden tables with cups in front of them, seen from behind.',
	),

	'work-bakery' => array(
		'n'   => 3,
		'alt' => 'Baskets of round sourdough loaves and dark rye stacked behind handwritten price cards.',
	),

	'work-office-kitchen' => array(
		'n'   => 3,
		'alt' => 'A bright office kitchen with a coffee machine on the counter and one person working at a laptop.',
	),

	'work-deli' => array(
		'n'   => 1,
		'alt' => 'A wall of pale blue shelves filled with labelled glass storage jars in a shop.',
	),

	'work-market-stall' => array(
		'n'   => 2,
		'alt' => 'A market stall stacked with crates of fruit and vegetables under handwritten cardboard signs.',
	),

	'work-hotel-bar' => array(
		'n'   => 6,
		'alt' => 'A restaurant table laid with wine glasses and folded napkins, the room dim behind it.',
	),

	'blog-green-beans' => array(
		'n'   => 4,
		'alt' => 'A folded hessian coffee sack, stencilled with a country of origin and a lot number.',
	),

	'blog-grinder' => array(
		'n'   => 1,
		'alt' => 'An old hand-cranked coffee grinder beside a bowl of roasted beans and a small cup.',
	),

	'blog-kettle' => array(
		'n'   => 2,
		'alt' => 'A gooseneck kettle pouring into a filter cone on a glass carafe, a window and a street behind it.',
	),

	'blog-latte' => array(
		'n'   => 4,
		'alt' => 'A latte seen from directly above, a leaf poured into the surface of the milk.',
	),

	'blog-farm' => array(
		'n'   => 1,
		'alt' => 'A hand reaching into the leaves of a coffee plant, picking a cluster of ripe red cherries.',
	),

	'blog-espresso' => array(
		'n'   => 3,
		'alt' => 'Espresso running from a portafilter spout in a dark, unbroken stream.',
	),
);
