<?php
/**
 * Builds the Brackenmoor Roastery demo on the demo install.
 *
 * Re-runnable: everything it creates carries the demo's own slugs, and each run
 * removes the previous one first, so the site can be rebuilt after a copy edit
 * without collecting duplicates.
 *
 * The page layouts come from the theme's own registered patterns rather than
 * from hand-written markup: a demo whose pages drift from the patterns stops
 * being a demonstration of the theme.
 *
 * Usage: php build.php
 */

require_once 'C:/xampp/htdocs/kivora-demo/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$data      = require __DIR__ . '/content.php';
// The published uploads are the images: one copy in the repo, and the copy a
// buyer actually receives.
$image_dir = dirname( __DIR__, 2 ) . '/assets/roastery/uploads/2026/08';

/*
 * Alt text for the photographs lives with the choice of photograph, in
 * ../img/chosen-roastery.php, and comes through here in the credits file the
 * asset builder writes. Keeping it in one place is the point: alt text that
 * describes a picture somebody swapped out last month is worse than none.
 * The drawn logos are not in there and keep the alt text set in content.php.
 */
$credits_file = dirname( __DIR__ ) . '/img/credits-roastery.json';

if ( is_readable( $credits_file ) ) {
	foreach ( (array) json_decode( (string) file_get_contents( $credits_file ), true ) as $slot => $credit ) {
		if ( isset( $data['images'][ $slot ] ) && isset( $credit['alt'] ) ) {
			$data['images'][ $slot ]['alt'] = (string) $credit['alt'];
		}
	}
}

$log = static function ( string $line ): void {
	echo $line . "\n";
};

/* ------------------------------------------------------------------ reset */

$log( 'Removing anything from a previous run...' );

$existing = get_posts(
	array(
		'post_type'      => array( 'post', 'page', 'attachment' ),
		'post_status'    => 'any',
		'posts_per_page' => -1,
	)
);

foreach ( $existing as $post ) {
	wp_delete_post( $post->ID, true );
}

foreach ( wp_get_nav_menus() as $menu ) {
	wp_delete_nav_menu( $menu->term_id );
}

/*
 * Global styles are a post too. A stale one left by another demo would leave
 * this site the wrong colour, and the resolver would keep creating more.
 */
foreach ( get_posts( array( 'post_type' => 'wp_global_styles', 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $styles ) {
	wp_delete_post( $styles->ID, true );
}

WP_Theme_JSON_Resolver::clean_cached_data();

$log( sprintf( '  %d posts, the menus and the global styles removed', count( $existing ) ) );

/* ------------------------------------------------------------------ media */

$media = array();

foreach ( $data['images'] as $name => $meta ) {
	$file = null;

	foreach ( array( 'jpg', 'png' ) as $ext ) {
		if ( file_exists( "{$image_dir}/{$name}.{$ext}" ) ) {
			$file = "{$image_dir}/{$name}.{$ext}";
			break;
		}
	}

	if ( ! $file ) {
		$log( "  MISSING image: {$name}" );
		continue;
	}

	$upload = wp_upload_bits( basename( $file ), null, (string) file_get_contents( $file ) );

	if ( ! empty( $upload['error'] ) ) {
		$log( "  UPLOAD FAILED {$name}: {$upload['error']}" );
		continue;
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => (string) wp_check_filetype( $upload['file'] )['type'],
			'post_title'     => $meta['title'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $meta['alt'] );

	$media[ $name ] = array( 'id' => $id, 'url' => $upload['url'] );
}

$log( sprintf( '%d images in the media library', count( $media ) ) );

/* -------------------------------------------------------------- patterns */

/**
 * One registered pattern's block markup.
 */
$pattern = static function ( string $slug ): string {
	$registered = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );

	if ( ! $registered ) {
		throw new RuntimeException( "Pattern not registered: {$slug}" );
	}

	return (string) $registered['content'];
};

$placeholder = get_theme_file_uri( 'assets/images/placeholder.svg' );

/**
 * Swap the next placeholder image in a pattern for a real one.
 *
 * The patterns ship a placeholder so they look sane on a fresh install; a demo
 * has actual photographs, and each one needs its attachment ID on the block so
 * WordPress can serve responsive sizes and the buyer can edit it.
 */
$use_image = static function ( string $markup, string $name ) use ( $media, $placeholder, $data, $log ): string {
	if ( ! isset( $media[ $name ] ) ) {
		$log( "  WARNING: no image for slot {$name}" );

		return $markup;
	}

	$id  = $media[ $name ]['id'];
	$url = $media[ $name ]['url'];
	$alt = $data['images'][ $name ]['alt'];

	$position = strpos( $markup, $placeholder );

	if ( false === $position ) {
		$log( "  WARNING: no placeholder left for {$name}" );

		return $markup;
	}

	// The <img> itself: real file, real alt, and the class WordPress uses to
	// find the attachment again.
	$img_start = strrpos( substr( $markup, 0, $position ), '<img' );
	$img_end   = strpos( $markup, '/>', $position ) + 2;
	$old_img   = substr( $markup, $img_start, $img_end - $img_start );

	$new_img = str_replace( 'src="' . $placeholder . '"', 'src="' . $url . '"', $old_img );

	// The logo row ships alt text of its own; everything else ships alt="".
	$new_img = (string) preg_replace( '/alt="[^"]*"/', 'alt="' . esc_attr( $alt ) . '"', $new_img, 1 );
	$new_img = str_replace( '<img ', '<img class="wp-image-' . $id . '" ', $new_img );

	$markup = substr_replace( $markup, $new_img, $img_start, $img_end - $img_start );

	// ...and the block comment above it, so the editor knows the attachment.
	$comment_start = strrpos( substr( $markup, 0, $img_start ), '<!-- wp:' );
	$comment_end   = strpos( $markup, '-->', $comment_start );
	$comment       = substr( $markup, $comment_start, $comment_end - $comment_start );

	if ( str_contains( $comment, 'wp:image' ) && ! str_contains( $comment, '"id"' ) ) {
		$new_comment = str_replace( '<!-- wp:image {', '<!-- wp:image {"id":' . $id . ',', $comment );
		$markup      = substr_replace( $markup, $new_comment, $comment_start, strlen( $comment ) );
	}

	if ( str_contains( $comment, 'wp:media-text' ) && ! str_contains( $comment, '"mediaId"' ) ) {
		$new_comment = str_replace( '<!-- wp:media-text {', '<!-- wp:media-text {"mediaId":' . $id . ',"mediaType":"image",', $comment );
		$markup      = substr_replace( $markup, $new_comment, $comment_start, strlen( $comment ) );
	}

	return $markup;
};

/**
 * Replace exact strings, and say so loudly when one no longer matches: a
 * pattern edited in the theme should break the demo build, not silently leave
 * the theme's own marketing copy in a customer's demo.
 */
$rewrite = static function ( string $markup, array $pairs ) use ( $log ): string {
	foreach ( $pairs as $from => $to ) {
		if ( ! str_contains( $markup, $from ) ) {
			$log( '  WARNING: nothing matched "' . substr( $from, 0, 60 ) . '"' );
			continue;
		}

		$markup = str_replace( $from, $to, $markup );
	}

	return $markup;
};

$home_url = untrailingslashit( home_url() );

/* ----------------------------------------------------------- home sections */

$hero = $use_image( $pattern( 'kivora/hero-split' ), 'hero-roastery' );
$hero = $rewrite(
	$hero,
	array(
		'Design work that earns its keep' => 'Roasted Tuesday, on your counter Thursday',
		'We help small teams ship clear, fast websites — and keep them that way long after launch day.'
			=> 'A small roastery in Brackenmoor supplying cafés, delis and offices within fifty miles — and posting the rest the day it is packed.',
		'<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Start a project</a></div>'
			=> '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . $home_url . '/contact/">Order a sample box</a></div>',
	)
);

$services = $rewrite(
	$pattern( 'kivora/content-services-grid' ),
	array(
		'What we do'  => 'What we do',
		'Strategy'    => 'Wholesale coffee',
		'We start with the question behind the project, then decide what is actually worth building.'
			=> 'Two house blends and a rotating single origin, roasted to order and delivered on our own van.',
		'Design'      => 'Equipment and servicing',
		'Interfaces that stay legible at every size, on every connection, for every visitor.'
			=> 'Grinders and espresso machines, installed, plumbed, and back working within one working day when they fail.',
		'Development' => 'Training',
		'Standards-based front-end work you can hand to any developer a year from now.'
			=> 'A morning behind your own machine, with your own water. Included with a wholesale account, once a year.',
		'Support'     => 'Subscriptions',
		'Updates, monitoring, and a person who answers when something needs attention.'
			=> 'A bag every fortnight for people who would rather not think about it. Change or cancel from the email itself.',
	)
);

$features = $rewrite(
	$pattern( 'kivora/content-features-3col' ),
	array(
		'Everything you need, nothing you don&#8217;t' => 'Why accounts stay',
		'Fast by default' => 'Roasted to order',
		'No frameworks, no bundled libraries. Pages ship the markup they need and nothing more.'
			=> 'Nothing sits in a warehouse. We roast on Tuesday and Friday and deliver the next day, so what you brew is between two and five days off the roaster.',
		'Accessible from day one' => 'One number to ring',
		'Keyboard navigation, visible focus, and semantic landmarks are built in rather than bolted on.'
			=> 'Whoever answers has stood in your shop. A grinder down on a Saturday is a phone call, not a ticket in a queue.',
		'Yours to change' => 'Prices that hold',
		'Every color, size, and spacing step is a design token you can edit in one place.'
			=> 'Wholesale prices are fixed for the quarter and published on this site. Nobody is quoted one number and invoiced another.',
	)
);

$work = $pattern( 'kivora/content-work-grid' );

foreach ( array( 'work-corner-cafe', 'work-bakery', 'work-office-kitchen', 'work-deli', 'work-market-stall', 'work-hotel-bar' ) as $slot ) {
	$work = $use_image( $work, $slot );
}

$work = $rewrite(
	$work,
	array(
		'Recent work'        => 'Where you will find it',
		'Riverside Trust'    => 'The Corner House',
		'A grant-funded charity, and the twelve documents nobody could find.'
			=> 'Our first account, and still the one that gets through the most: 14 kg a week, house blend, two deliveries.',
		'Harbour Press'      => 'Fold Lane Bakery',
		'Twelve stories a day, filed by editors who had stopped fighting the editor.'
			=> 'Batch brew from seven in the morning, and a grinder we service twice a year because of the flour.',
		'Meadow Lane School' => 'Kestrel Works',
		'One term to replace a site that only worked on the office computer.'
			=> 'Forty desks, one superautomatic, and a blend roasted a shade longer because nobody there will ever dial it in.',
		'Kestrel Tools'      => 'Meadow Lane Deli',
		'A product catalogue that had lived in a spreadsheet since 2009.'
			=> 'Beans on the shelf as well as in the cup — they sell more retail bags than some cafés sell drinks.',
		'Ashgrove Clinic'    => 'Brackenmoor Market',
		'Appointments, directions, and nothing else competing for attention.'
			=> 'A stall every Saturday since 2019. It is where half the wholesale accounts first tasted the coffee.',
		'Fell &amp; Field'   => 'The Harbour Rooms',
		'A small producer who needed the shop to look like the packaging.'
			=> 'Breakfast service for ninety rooms, which means the single origin has to survive being made in a hurry.',
	)
);

$stats = $rewrite(
	$pattern( 'kivora/content-stats' ),
	array(
		'12 yrs'                    => '9 yrs',
		'Building for the web'      => 'Roasting in Brackenmoor',
		'240+'                      => '140',
		'Projects delivered'        => 'Accounts on the van',
		'&lt;50 KB'                 => '18 kg',
		'Typical page weight'       => 'Batch size',
		'100/100'                   => 'Tue &amp; Fri',
		'Average performance score' => 'Roast days',
	)
);

$pricing = $rewrite(
	$pattern( 'kivora/content-pricing' ),
	array(
		'What it costs' => 'What it costs',
		'Every price below is the whole price. There is no separate licence, and nothing is billed by the hour without being agreed first.'
			=> 'Wholesale prices are per kilogram, fixed for the quarter, and the same whether you take one bag a week or twenty. Delivery is free inside the van radius.',

		'Starter' => 'House blend',
		'£950'    => '£19',
		'one-off' => 'per kilogram',
		'For a single site that needs to look after itself.'
			=> 'The everyday one: two thirds Brazilian, a third Colombian, chocolate and not much drama.',
		'Half-day discovery call'    => 'Roasted to order, twice a week',
		'Up to five pages'           => '1 kg bags, valve sealed, dated',
		'Contact form and analytics' => 'Ground to your machine on request',
		'Thirty days of fixes'       => 'Free delivery within fifty miles',

		'Standard' => 'Single origin',
		'£2,400'   => '£26',
		'What most projects turn out to need.' => 'Rotating, and never the same for very long.',
		'Discovery week, fixed price after'    => 'A new lot every six to eight weeks',
		'Up to twenty pages'                   => 'Tasting notes and roast date on the bag',
		'Content migration'                    => 'Sample before you commit to a lot',
		'Training session and handover notes'  => 'First refusal on the small lots',
		'Ninety days of fixes'                 => 'Free delivery within fifty miles',

		'Ongoing'    => 'Trade account',
		'£180'       => 'Free',
		'per month'  => 'to open',
		'For a site somebody has to keep an eye on.' => 'For a shop that wants the rest of it as well as the coffee.',
		'Core and plugin updates'          => 'Both of the above at the same prices',
		'Offsite backups, checked monthly' => 'Machine servicing within a working day',
		'Uptime and performance reports'   => 'A morning of training every year',
		'Two hours of changes a month'     => 'Loan grinder while yours is away',

		'Most projects'  => 'Most shops',
		'Ask about this' => 'Ask about this',
	)
);

$testimonials = $rewrite(
	$pattern( 'kivora/content-testimonials' ),
	array(
		'They cut our page weight by two thirds and our bounce rate followed it down.'
			=> 'The roast date on the bag is the actual roast date. That should not be remarkable and somehow it is.',
		'Operations lead, logistics company' => 'Owner, corner café',
		'The handover documentation was better than what our previous agency shipped as a product.'
			=> 'Our grinder died at half past seven on a Saturday and somebody answered the phone.',
		'Founder, design studio'             => 'Manager, hotel restaurant',
		'Our editors stopped filing tickets. That is the highest praise I can give a redesign.'
			=> 'We stopped trying to explain the coffee to customers and started pointing at the bag instead.',
		'Managing editor, trade publication' => 'Buyer, village deli',
	)
);

$latest = $rewrite(
	$pattern( 'kivora/content-latest-posts' ),
	array( 'Latest writing' => 'From the roastery' )
);

$logos = $pattern( 'kivora/content-logos' );

foreach ( array( 'client-corner-house', 'client-fold-lane', 'client-kestrel-works', 'client-meadow-lane', 'client-harbour-rooms' ) as $slot ) {
	$logos = $use_image( $logos, $slot );
}

$logos = $rewrite( $logos, array( 'Working with' => 'Poured at' ) );

$cta = $rewrite(
	$pattern( 'kivora/cta-banner' ),
	array(
		'Have a project in mind?' => 'Want to taste it first?',
		'Tell us what you are working on. We reply to every message within two business days.'
			=> 'We will send a sample box: three 250g bags, a roast date, and nothing to sign. Tell us what your machine is and we will grind for it.',
		'>Get in touch<' => ' href="' . $home_url . '/contact/">Order a sample box<',
	)
);

$process = $rewrite(
	$pattern( 'kivora/content-process' ),
	array(
		'How the work runs' => 'How an account starts',
		'Ask'  => 'Taste',
		'A conversation about what is not working. No proposal yet, and no obligation either way.'
			=> 'A sample box: three bags, nothing to sign. Tell us the machine and the water and we will grind for both.',
		'Plan' => 'Set up',
		'We write down what is being built, what it costs, and when it is finished. You agree to that, not to an estimate.'
			=> 'We come and dial in on your machine, in your shop, with your water. Half a morning, and it is not charged for.',
		'Build' => 'Deliver',
		'You see the work every week, at real sizes, with your own words in it rather than placeholder text.'
			=> 'Roast days are Tuesday and Friday. You set a standing order and change it whenever you like, from the email.',
		'Hand over' => 'Look after',
		'Training, notes, and the source. Nothing is locked to us, and there is no licence to renew.'
			=> 'Servicing within a working day, a loan grinder if yours goes away, and a morning of training every year.',
	)
);

$about = $use_image( $pattern( 'kivora/content-about-media-text' ), 'about-roasting' );
$about = $rewrite(
	$about,
	array(
		'A small studio with a long memory' => 'Nine years in a unit off Fold Lane',
		'We have been building for the web since before responsive design had a name. That history shows up as restraint: fewer moving parts, clearer handover notes, and sites that survive their third redesign.'
			=> 'Brackenmoor Roastery started in 2017 with a 5 kg drum roaster in a unit that also housed a joiner. We roast on an 18 kg machine now, in the same building, with the joiner still next door and still complaining about the smell.',
		'Most of our work starts as a conversation. Tell us what is not working and we will tell you honestly whether we are the right people to fix it.'
			=> 'About a hundred and forty accounts are inside the van radius. Everything beyond it goes in the post on the day it is packed, which is the only part of this we would change if we could.',
	)
);

$cupping = $use_image( $pattern( 'kivora/content-about-media-text' ), 'services-cupping' );
$cupping = $rewrite(
	$cupping,
	array(
		'A small studio with a long memory' => 'Every lot is tasted before it is sold',
		'We have been building for the web since before responsive design had a name. That history shows up as restraint: fewer moving parts, clearer handover notes, and sites that survive their third redesign.'
			=> 'A sack is cupped when it lands, roasted three ways, and cupped again. Roughly one lot in six does not make it onto the price list — usually because it is fine rather than because it is bad, which is a harder call to make.',
		'Most of our work starts as a conversation. Tell us what is not working and we will tell you honestly whether we are the right people to fix it.'
			=> 'What does not make the list goes into the office blend or gets sold on at cost, and we say which on the bag. Nothing is quietly upgraded with an adjective.',
	)
);

$workshop = $use_image( $pattern( 'kivora/content-about-media-text' ), 'contact-shopfront' );
$workshop = $rewrite(
	$workshop,
	array(
		'A small studio with a long memory' => 'Finding us',
		'We have been building for the web since before responsive design had a name. That history shows up as restraint: fewer moving parts, clearer handover notes, and sites that survive their third redesign.'
			=> 'Number 7 is where the roaster lives and where the van is loaded. There is no café and no counter — if you are coming to taste something, ring first so somebody is expecting you and the machine is on.',
		'Most of our work starts as a conversation. Tell us what is not working and we will tell you honestly whether we are the right people to fix it.'
			=> 'Fold Lane runs off the market square and is too narrow to park in; the nearest car park is behind the square, two minutes away. We are the green door halfway down, between the bike shop and the launderette.',
	)
);

$faq = $rewrite(
	$pattern( 'kivora/content-faq' ),
	array(
		'Do I need a page builder plugin?' => 'How fresh is it when it arrives?',
		'No. Every layout in this theme is built from core WordPress blocks, so the block editor is the only tool you need.'
			=> 'Between two and five days off the roaster on a van delivery, and three to six by post. We do not roast to stock, so a large order may wait for the next roast day rather than come out of a bin.',
		'Can I change the colors and fonts?' => 'Can you grind for our machine?',
		'Yes. Colors, spacing, and type sizes are design tokens you can edit in the Styles panel — every pattern picks the change up automatically.'
			=> 'Yes, and we will ask what it is rather than guess. Ground coffee stales far faster than beans, though, so for more than a fortnight\'s worth we would rather sell you beans and set your grinder up properly.',
		'Will this work with my existing content?' => 'Do we have to sign anything?',
		'It should. The theme follows the standard WordPress template hierarchy and styles core blocks, classic content, and widgets alike.'
			=> 'No. There is no minimum order, no tie and no contract. If you want to stop, stop — the loan grinder comes back and that is the end of it.',
		'Is anything held back for a paid version?' => 'What happens if the machine breaks?',
		'No. There is no pro tier and no upgrade prompts — the whole theme is free and GPL-licensed.'
			=> 'Ring. If it is something we can talk you through we will, and if it is not, somebody comes out — usually the same day, always within one working day, and there is a loan grinder in the van.',
	)
);

/*
 * There is no team section on this demo. The theme's content-team pattern wants
 * three portraits, and the CC0 portraits Openverse offers are photographs of
 * identifiable people. Creative Studio already demonstrates that pattern, with
 * models from a stock library; here the About page uses the process steps
 * instead, which suit this business better anyway.
 */

/* ------------------------------------------------------------------ pages */

$pages = array();

$pages['home'] = array(
	'title'      => 'Home',
	'hide_title' => true,
	'content'    => implode(
		"\n\n",
		array( $hero, $services, $features, $work, $stats, $pricing, $testimonials, $latest, $logos, $cta )
	),
);

$pages['about'] = array(
	'title'   => 'About',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Brackenmoor Roastery is four people, one roaster and one van. We roast twice a week and sell almost all of it within fifty miles of the building it was roasted in.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The roastery exists because of a bad flat white. Nell had been managing a café that bought from a large roaster three counties away, where the coffee arrived on a monthly pallet with no date on it and tasted like whatever it had been doing in the meantime. Nobody could tell her when it had been roasted. That turned out to be the whole business.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>So the rule we started with is the rule we still have: nothing leaves here that we would not serve, and everything carries the day it was roasted. It makes the logistics worse and the coffee better, and after nine years it is the only thing every account has in common.</p>
<!-- /wp:paragraph -->',
			$about,
			$stats,
			$cupping,
			$process,
			$cta,
		)
	),
);

$pages['services'] = array(
	'title'   => 'Services',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Four things, and three of them exist so that the first one arrives tasting the way it did when we sent it.</p>
<!-- /wp:paragraph -->',
			$services,
			'<!-- wp:paragraph -->
<p>Everything starts with a sample box and half a morning dialling in on your own machine. The detail on the coffee itself — blends, lots, bag sizes and what a delivery actually looks like — is on <a href="' . $home_url . '/services/wholesale-coffee/">the wholesale page</a>.</p>
<!-- /wp:paragraph -->',
			$process,
			$features,
			$cta,
		)
	),
);

$pages['wholesale-coffee'] = array(
	'title'   => 'Wholesale coffee',
	'parent'  => 'services',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">What we sell to shops, what it costs, and what happens between the roaster and your hopper.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What is on the list</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><strong>Fold Lane</strong> — the house blend. Two thirds Brazilian, a third Colombian, taken to the end of first crack. Chocolate, a bit of brown sugar, and it holds up in a sixteen-ounce latte.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Nightwork</strong> — the second blend, for shops whose espresso goes out mostly black. Same Brazilian base, a Guatemalan in place of the Colombian, a touch more development.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>The rotating single origin</strong> — one lot at a time, changing every six to eight weeks. Announced a fortnight before it lands so you can taste it before you switch.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Decaf</strong> — a Colombian sugarcane process, roasted for espresso. One blend only, because a decaf nobody orders is a decaf going stale.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Bags, dates and grind</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Everything ships in valve-sealed 1 kg bags with the roast date printed rather than a best-before. Retail is 250g, same coffee, same date. We will grind to your machine if you ask, and we will ask which machine — but for anything over a fortnight\'s worth we would rather come and set your grinder up.</p>
<!-- /wp:paragraph -->',
			$cupping,
			'<!-- wp:heading -->
<h2 class="wp-block-heading">Deliveries</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Roast days are Tuesday and Friday, and the van goes out the following morning. Inside fifty miles delivery is free at any order size; beyond it, everything goes by tracked post on the day it is packed. Standing orders can be changed from the confirmation email up to the evening before the roast.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Terms, such as they are</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>No minimum, no tie, no contract. Invoices are payment on delivery for the first three months and thirty days after that. If a delivery is wrong or a bag is off, tell us and it is replaced on the next van — we have never asked anyone to send coffee back.</p>
<!-- /wp:paragraph -->',
			$faq,
			$cta,
		)
	),
);

$pages['stockists'] = array(
	'title'   => 'Stockists',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Six of the hundred and forty, picked because they are open to the public and because between them they cover most of what we do.</p>
<!-- /wp:paragraph -->',
			$work,
			$testimonials,
			$logos,
			$cta,
		)
	),
);

$pages['pricing'] = array(
	'title'   => 'Pricing',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">The whole wholesale list, published, and fixed until the end of the quarter. Retail bags are on the same page because there is no reason to hide the difference.</p>
<!-- /wp:paragraph -->',
			$pricing,
			'<!-- wp:heading -->
<h2 class="wp-block-heading">Retail</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>250g bags are £6.50 for either blend and £8.50 for the single origin, from the market stall, from the accounts that stock them, or by post at £3.95 a parcel however many bags are in it. The subscription is the same price with the postage taken off.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What moves a price</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Green coffee is a commodity and the exchange rate is not our friend, so the list is reset every quarter rather than every time something twitches. When it changes, accounts get four weeks\' notice and the reason, which is usually one sentence about the landed price of a sack.</p>
<!-- /wp:paragraph -->',
			$faq,
			$cta,
		)
	),
);

$pages['journal'] = array(
	'title'   => 'Journal',
	'content' => '',
);

$pages['contact'] = array(
	'title'   => 'Contact',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">The quickest way to get a real answer is to ring. If we are on the roaster the machine is loud and we will call you back inside the hour.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|lg","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":2,"fontSize":"h5"} -->
<h2 class="wp-block-heading has-h-5-font-size">The roastery</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>7 Fold Lane<br>Brackenmoor<br>BM4 2QW</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Monday to Friday, 7am to 4pm.<br>Market stall: Saturdays, 8am to 1pm.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":2,"fontSize":"h5"} -->
<h2 class="wp-block-heading has-h-5-font-size">Getting hold of us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>01698 555 0142<br>hello@brackenmoorroastery.example</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Machine down, or a delivery that has not arrived: ring. Anything else, either is fine.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
			'<!-- wp:paragraph -->
<p>This page is where a contact form belongs. The theme styles form fields at zero specificity on purpose, so whichever form plugin you use already looks like the rest of the site — drop its block in below and it will match without any extra CSS.</p>
<!-- /wp:paragraph -->',
			$workshop,
			$cta,
		)
	),
);

$pages['privacy-policy'] = array(
	'title'   => 'Privacy policy',
	'content' => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">A short one, because we hold very little. This is demonstration content for a WordPress theme; replace it with a policy written for your own business.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you ask for a sample box we keep your name, your delivery address and what machine you have, because we cannot grind for a machine we do not know about. If you open an account we also keep the order history and the invoices, for as long as the tax rules make us.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Who else sees it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The courier gets a name, an address and a phone number for anything that goes by post. Our accountant sees the invoices. Nobody else gets anything, and we have never sold a mailing list — the newsletter goes out from the same machine that prints the labels.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Comments and cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Comments on the journal store the name, email address and text you submit, along with your IP address, which is how spam filtering works. WordPress sets a cookie so you do not have to type your details again; decline it and nothing else changes.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Asking for it back, or asking us to forget</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Email hello@brackenmoorroastery.example and we will send you everything we hold, or delete it, within a month. The exception is invoices, which we are obliged to keep for six years whatever anybody would prefer.</p>
<!-- /wp:paragraph -->',
);

$page_ids = array();

foreach ( $pages as $slug => $page ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $page['title'],
			'post_content' => $page['content'],
			'post_parent'  => isset( $page['parent'] ) ? ( $page_ids[ $page['parent'] ] ?? 0 ) : 0,
			'menu_order'   => count( $page_ids ),
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		$log( "  PAGE FAILED {$slug}: " . $id->get_error_message() );
		continue;
	}

	$page_ids[ $slug ] = $id;

	if ( ! empty( $page['hide_title'] ) ) {
		update_post_meta( $id, '_kivora_hide_title', true );
	}
}

$log( sprintf( '%d pages created', count( $page_ids ) ) );

/* ----------------------------------------------------------------- author */

// Posts written by nobody show no byline and no author box, which is half the
// single-post layout missing. The demo's writer is the person on the roaster.
$author_id = 1;

wp_update_user(
	array(
		'ID'           => $author_id,
		'first_name'   => 'Nell',
		'last_name'    => 'Faraday',
		'display_name' => 'Nell Faraday',
		'nickname'     => 'Nell Faraday',
		'description'  => 'Nell opened Brackenmoor Roastery in 2017 and is still the one on the roaster twice a week. She writes here when the same question has come up in three different shops in a month.',
	)
);

/* ---------------------------------------------------------- posts + terms */

$term_ids = array();

foreach ( $data['categories'] as $slug => $category ) {
	$term = term_exists( $slug, 'category' );

	if ( ! $term ) {
		$term = wp_insert_term( $category[0], 'category', array( 'slug' => $slug, 'description' => $category[1] ) );
	}

	$term_ids[ $slug ] = (int) $term['term_id'];
}

$post_ids = array();
$sticky   = array();

foreach ( $data['posts'] as $post ) {
	$id = wp_insert_post(
		array(
			'post_type'      => 'post',
			'post_author'    => $author_id,
			'post_status'    => 'publish',
			'post_name'      => $post['slug'],
			'post_title'     => $post['title'],
			'post_content'   => $post['content'],
			'post_excerpt'   => $post['excerpt'],
			'post_date'      => wp_date( 'Y-m-d H:i:s', strtotime( $post['date'] ) ),
			'post_category'  => array( $term_ids[ $post['category'] ] ),
			'tags_input'     => $post['tags'],
			'comment_status' => 'open',
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		$log( "  POST FAILED {$post['slug']}: " . $id->get_error_message() );
		continue;
	}

	$post_ids[ $post['slug'] ] = $id;

	if ( isset( $media[ $post['image'] ] ) ) {
		set_post_thumbnail( $id, $media[ $post['image'] ]['id'] );
	}

	if ( ! empty( $post['sticky'] ) ) {
		$sticky[] = $id;
	}
}

update_option( 'sticky_posts', $sticky );
$log( sprintf( '%d posts created, %d sticky', count( $post_ids ), count( $sticky ) ) );

foreach ( $data['comments'] as $comment ) {
	if ( ! isset( $post_ids[ $comment['post'] ] ) ) {
		continue;
	}

	wp_insert_comment(
		array(
			'comment_post_ID'      => $post_ids[ $comment['post'] ],
			'comment_author'       => $comment['author'],
			'comment_author_email' => $comment['email'],
			'comment_content'      => $comment['text'],
			'comment_date'         => wp_date( 'Y-m-d H:i:s', strtotime( $comment['date'] ) ),
			'comment_approved'     => 1,
		)
	);
}

$log( sprintf( '%d comments added', count( $data['comments'] ) ) );

/* ------------------------------------------------------------------ menus */

$primary = wp_create_nav_menu( 'Main Menu' );
$footer  = wp_create_nav_menu( 'Footer Menu' );

$add = static function ( int $menu, string $title, int $object_id, int $parent = 0 ): int {
	return (int) wp_update_nav_menu_item(
		$menu,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $object_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		)
	);
};

$add( $primary, 'Home', $page_ids['home'] );
$services_item = $add( $primary, 'Services', $page_ids['services'] );
$add( $primary, 'Wholesale coffee', $page_ids['wholesale-coffee'], $services_item );
$add( $primary, 'Stockists', $page_ids['stockists'] );
$add( $primary, 'Pricing', $page_ids['pricing'] );
$add( $primary, 'Journal', $page_ids['journal'] );
$add( $primary, 'About', $page_ids['about'] );
$add( $primary, 'Contact', $page_ids['contact'] );

$add( $footer, 'Wholesale coffee', $page_ids['wholesale-coffee'] );
$add( $footer, 'Pricing', $page_ids['pricing'] );
$add( $footer, 'Journal', $page_ids['journal'] );
$add( $footer, 'Privacy policy', $page_ids['privacy-policy'] );

set_theme_mod( 'nav_menu_locations', array( 'primary' => $primary, 'footer' => $footer ) );
$log( 'menus: Main Menu (8 items, one submenu) and Footer Menu (4 items)' );

/* ---------------------------------------------------------------- widgets */

$widget_settings = array(
	'search'       => array( 2 => array( 'title' => 'Search the journal' ) ),
	'recent-posts' => array( 2 => array( 'title' => 'Recent writing', 'number' => 4 ), 3 => array( 'title' => 'From the roastery', 'number' => 3 ) ),
	'categories'   => array( 2 => array( 'title' => 'Topics', 'count' => 0, 'hierarchical' => 0 ) ),
	'tag_cloud'    => array( 2 => array( 'title' => 'Tags', 'taxonomy' => 'post_tag' ) ),
	'nav_menu'     => array( 2 => array( 'title' => 'The roastery', 'nav_menu' => $footer ) ),
	'text'         => array(
		2 => array(
			'title'  => 'Brackenmoor Roastery',
			'text'   => "Roasted Tuesday and Friday, delivered the next morning.\n\n7 Fold Lane, Brackenmoor BM4 2QW",
			'filter' => true,
		),
		3 => array(
			'title'  => 'Getting hold of us',
			'text'   => '01698 555 0142<br>hello@brackenmoorroastery.example<br><br>Monday to Friday, 7am to 4pm.',
			'filter' => true,
		),
	),
);

foreach ( $widget_settings as $base => $instances ) {
	$instances['_multiwidget'] = 1;
	update_option( 'widget_' . $base, $instances );
}

update_option(
	'sidebars_widgets',
	array(
		'wp_inactive_widgets' => array(),
		'main-sidebar'        => array( 'search-2', 'recent-posts-2', 'categories-2', 'tag_cloud-2' ),
		'footer-column-1'     => array( 'text-2' ),
		'footer-column-2'     => array( 'nav_menu-2' ),
		'footer-column-3'     => array( 'recent-posts-3' ),
		'footer-column-4'     => array( 'text-3' ),
		'array_version'       => 3,
	)
);

$log( 'widgets: sidebar (4) and four footer columns' );

/* --------------------------------------------------------- style variation */

/*
 * The demo is a colour scheme as much as it is content. On an imported site the
 * theme does this itself, from the style_variation named in the demo's setup
 * block; here it is done directly, so the build site looks like what a buyer
 * will get. Both routes have to lift the kses filter — see the theme's
 * Kivora\Demo\Library::apply_style_variation() for why.
 */
$variation = json_decode( (string) file_get_contents( get_theme_file_path( 'styles/forest.json' ) ), true );

unset( $variation['title'], $variation['slug'], $variation['description'] );

$variation['version']                     = WP_Theme_JSON::LATEST_SCHEMA;
$variation['isGlobalStylesUserThemeJSON'] = true;

$styles_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
$filtered  = has_filter( 'content_save_pre', 'wp_filter_global_styles_post' );

if ( false !== $filtered ) {
	remove_filter( 'content_save_pre', 'wp_filter_global_styles_post', (int) $filtered );
}

wp_update_post(
	array(
		'ID'           => $styles_id,
		'post_content' => wp_slash( (string) wp_json_encode( $variation ) ),
	)
);

if ( false !== $filtered ) {
	add_filter( 'content_save_pre', 'wp_filter_global_styles_post', (int) $filtered );
}

wp_set_object_terms( $styles_id, get_stylesheet(), 'wp_theme' );
WP_Theme_JSON_Resolver::clean_cached_data();

$log( 'style variation: Forest' );

/* --------------------------------------------------------------- settings */

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'page_for_posts', $page_ids['journal'] );
update_option( 'wp_page_for_privacy_policy', $page_ids['privacy-policy'] );
update_option( 'blogname', $data['site']['title'] );
update_option( 'blogdescription', $data['site']['description'] );
update_option( 'posts_per_page', 6 );

if ( isset( $media['brackenmoor-roastery-logo'] ) ) {
	set_theme_mod( 'custom_logo', $media['brackenmoor-roastery-logo']['id'] );
}

if ( isset( $media['brackenmoor-roastery-icon'] ) ) {
	update_option( 'site_icon', $media['brackenmoor-roastery-icon']['id'] );
}

/*
 * The theme's own settings live in one option (03-ui-system/03-theme-settings.md).
 * Deliberately unlike the other two demos: a sticky header, the sidebar on the
 * right, no sidebar on a single post, and live search off. Two demos set the
 * same way demonstrate nothing about the controls.
 */
/*
 * Start from the theme's own defaults rather than from whatever is saved. The
 * demo install is rebuilt over and over, and merging onto the saved option
 * carries the last demo's settings into this one — which is how Fieldnotes'
 * footer line ("words and photographs, free to reuse with credit") turned up
 * at the bottom of a coffee roastery.
 */
$settings = class_exists( 'Kivora\Theme_Settings' ) ? Kivora\Theme_Settings::defaults() : array();

update_option(
	'kivora_theme_settings',
	array_merge(
		$settings,
		array(
			'header_sticky'             => true,
			'header_transparent'        => false,
			'live_search_enabled'       => false,
			'sidebar_layout'            => 'right',
			'sidebar_layout_singular'   => 'none',
			'single_show_related_posts' => true,
			'single_show_author_bio'    => true,
			'single_show_reading_time'  => true,
		)
	)
);

$log( 'front page, journal page, logo, site icon and theme settings set' );

flush_rewrite_rules( false );

$log( "\nDone. " . home_url() );
