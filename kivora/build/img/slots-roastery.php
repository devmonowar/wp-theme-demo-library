<?php
/**
 * Brackenmoor Roastery: slot => search variants, tried in order until the sheet
 * is full.
 *
 * Restricted to StockSnap when fetched (--source=stocksnap). rawpixel is a
 * large share of Openverse's CC0 and prints a watermark across the file it
 * serves, so a slot can come back full and be entirely unusable.
 *
 * No portrait slot: the CC0 portraits on offer are identifiable people, and a
 * demo that lands on other people's sites is the wrong place to carry a face.
 *
 * @return array<string, array<int, string>>
 */

return array(
	// Hero, about, and the two page images.
	'hero-roastery'      => array( 'coffee roasting machine', 'coffee roastery', 'coffee beans roasted' ),
	'about-roasting'     => array( 'coffee beans hands', 'roasting coffee beans', 'coffee sack beans' ),
	'services-cupping'   => array( 'coffee cupping tasting', 'coffee scale brewing', 'pour over coffee' ),
	'contact-shopfront'  => array( 'cafe exterior street', 'shop front', 'brick building street' ),

	// The six accounts on the work grid.
	'work-corner-cafe'   => array( 'cafe interior', 'coffee shop interior', 'cafe tables chairs' ),
	'work-bakery'        => array( 'bakery counter bread', 'bakery shop', 'bread display' ),
	'work-office-kitchen' => array( 'office kitchen coffee', 'office break room', 'coffee cups office' ),
	'work-deli'          => array( 'deli counter food', 'grocery shop interior', 'food shop shelves' ),
	'work-market-stall'  => array( 'market stall food', 'farmers market stall', 'street food market' ),
	'work-hotel-bar'     => array( 'hotel lobby', 'restaurant interior', 'bar counter interior' ),

	// Featured images for the posts.
	'blog-green-beans'   => array( 'green coffee beans', 'coffee beans sack', 'raw coffee beans' ),
	'blog-grinder'       => array( 'coffee grinder', 'ground coffee', 'coffee grounds' ),
	'blog-kettle'        => array( 'pouring kettle coffee', 'pour over kettle', 'coffee filter brewing' ),
	'blog-latte'         => array( 'latte art', 'cappuccino cup', 'milk pouring coffee' ),
	'blog-farm'          => array( 'coffee plantation', 'coffee cherries farm', 'coffee farm harvest' ),
	'blog-espresso'      => array( 'espresso machine', 'espresso shot', 'barista espresso' ),
);
