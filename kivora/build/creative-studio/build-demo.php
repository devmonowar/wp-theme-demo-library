<?php
/**
 * Builds the Northline Studio demo on the demo install.
 *
 * Re-runnable: everything it creates carries the demo's own slugs, and each run
 * removes the previous one first, so the site can be rebuilt after a copy edit
 * without collecting duplicates.
 *
 * The page layouts come from the theme's own registered patterns rather than
 * from hand-written markup: a demo whose pages drift from the patterns stops
 * being a demonstration of the theme.
 *
 * Usage: php build-demo.php
 */

require_once 'C:/xampp/htdocs/kivora-demo/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$data      = require __DIR__ . '/content.php';
// The published uploads are the images: one copy in the repo, and the copy a
// buyer actually receives.
$image_dir = dirname( __DIR__, 2 ) . '/assets/creative-studio/uploads/2026/08';

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

$log( sprintf( '  %d posts and %d menus removed', count( $existing ), 0 ) );

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
$use_image = static function ( string $markup, string $name ) use ( $media, $placeholder, $data ): string {
	if ( ! isset( $media[ $name ] ) ) {
		return $markup;
	}

	$id  = $media[ $name ]['id'];
	$url = $media[ $name ]['url'];
	$alt = $data['images'][ $name ]['alt'];

	$position = strpos( $markup, $placeholder );

	if ( false === $position ) {
		return $markup;
	}

	// The <img> itself: real file, real alt, and the class WordPress uses to
	// find the attachment again.
	$img_start = strrpos( substr( $markup, 0, $position ), '<img' );
	$img_end   = strpos( $markup, '/>', $position ) + 2;
	$old_img   = substr( $markup, $img_start, $img_end - $img_start );

	$new_img = str_replace(
		array( 'src="' . $placeholder . '"', 'alt=""' ),
		array( 'src="' . $url . '"', 'alt="' . esc_attr( $alt ) . '"' ),
		$old_img
	);

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
 * the theme's own marketing copy in a client demo.
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

// Hero: the theme's own studio copy already fits; it needs the photograph and
// a button that goes somewhere.
$hero = $use_image( $pattern( 'kivora/hero-split' ), 'studio-team-reviewing-work' );
$hero = $rewrite(
	$hero,
	array(
		'<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Start a project</a></div>'
			=> '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . $home_url . '/contact/">Start a project</a></div>',
	)
);

// Features: the pattern sells the theme; the demo has to sell the studio.
$features = $rewrite(
	$pattern( 'kivora/content-features-3col' ),
	array(
		'Everything you need, nothing you don&#8217;t' => 'How we work',
		'Fast by default'  => 'Three people, no account managers',
		'No frameworks, no bundled libraries. Pages ship the markup they need and nothing more.'
			=> 'You talk to the people doing the work. Nothing is passed down a chain, and nothing gets lost on the way.',
		'Accessible from day one' => 'Fixed price, fixed scope',
		'Keyboard navigation, visible focus, and semantic landmarks are built in rather than bolted on.'
			=> 'We quote after the discovery week, when we both know what is being built. The number does not move afterwards.',
		'Yours to change' => 'Yours to run afterwards',
		'Every color, size, and spacing step is a design token you can edit in one place.'
			=> 'You get the site, the source, and notes on how it fits together. No licence to renew, and nothing locked to us.',
	)
);

$services = $pattern( 'kivora/content-services-grid' );

$about = $use_image( $pattern( 'kivora/content-about-media-text' ), 'studio-planning-session' );

$stats = $pattern( 'kivora/content-stats' );

$testimonials = $pattern( 'kivora/content-testimonials' );

$latest = $pattern( 'kivora/content-latest-posts' );

// FAQ: same story as the features — questions a client asks, not questions a
// theme user asks.
$faq = $rewrite(
	$pattern( 'kivora/content-faq' ),
	array(
		'Do I need a page builder plugin?' => 'How long does a project take?',
		'No. Every layout in this theme is built from core WordPress blocks, so the block editor is the only tool you need.'
			=> 'A marketing site of ten to twenty pages takes eight to ten weeks from the discovery week to launch. Larger projects we split into phases so something useful ships early.',
		'Can I change the colors and fonts?' => 'What does it cost?',
		'Yes. Colors, spacing, and type sizes are design tokens you can edit in the Styles panel — every pattern picks the change up automatically.'
			=> 'Most projects land between £12,000 and £30,000. We quote a fixed price after discovery, so you are agreeing to a number rather than an hourly rate with a shrug attached.',
		'Will this work with my existing content?' => 'Can you work with our developers?',
		'It should. The theme follows the standard WordPress template hierarchy and styles core blocks, classic content, and widgets alike.'
			=> 'Often, yes. We have handed work to in-house teams, worked alongside them, and taken over sites built by somebody else. The handover notes are the same either way.',
		'Is anything held back for a paid version?' => 'What happens after launch?',
		'No. There is no pro tier and no upgrade prompts — the whole theme is free and GPL-licensed.'
			=> 'Either you run it and we stay available by the hour, or you take the support retainer: updates, monitoring, a quarterly performance check and a person who answers.',
	)
);

$cta = $rewrite(
	$pattern( 'kivora/cta-banner' ),
	array( '>Get in touch<' => ' href="' . $home_url . '/contact/">Get in touch<' )
);

/* ------------------------------------------------------------------ pages */

$team = $pattern( 'kivora/content-team' );
$team = $use_image( $team, 'team-mari-lindqvist' );
$team = $use_image( $team, 'team-tom-becker' );
$team = $use_image( $team, 'team-daniel-reyes' );
$team = $rewrite(
	$team,
	array(
		'A. Rahman'          => 'Mari Lindqvist',
		'Principal, strategy' => 'Principal — strategy and delivery',
		'J. Okonkwo'         => 'Tom Becker',
		'Design lead'        => 'Design lead',
		'M. Lindqvist'       => 'Daniel Reyes',
		'Front-end engineering' => 'Front-end engineering',
	)
);

$pages = array();

$pages['home'] = array(
	'title'      => 'Home',
	'hide_title' => true,
	'content'    => implode(
		"\n\n",
		array( $hero, $features, $services, $about, $stats, $testimonials, $latest, $faq, $cta )
	),
);

$pages['about'] = array(
	'title'   => 'About',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Northline Studio is three people in one room, building websites for organisations that have outgrown the site they started with.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We opened in 2013 doing overflow work for larger agencies. It taught us what we did not want to be: a place where the people who sell the work are never the people who do it. So we stayed small on purpose. Every project is run by the person who will build it, and there has never been an account manager between you and the work.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Most of what we make is unglamorous and long-lived: a charity that needs its grant applications to make sense, a manufacturer whose product catalogue has been in a spreadsheet since 2009, a publisher whose editors file twelve stories a day and would like to stop fighting the editor to do it.</p>
<!-- /wp:paragraph -->',
			$about,
			$stats,
			$team,
			$cta,
		)
	),
);

$pages['our-approach'] = array(
	'title'  => 'Our approach',
	'parent' => 'about',
	'content' => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Four stages, in the same order, on every project. The names are ordinary on purpose.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">1. Discovery</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A week with your team and your existing content. We come out of it with a sitemap, a page-by-page outline, a page-weight budget, and a fixed price. If the honest answer is that you do not need a new site, that is the week you find out — for the price of a week rather than a project.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">2. Design</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We design in the browser, at real widths, with your real words in it. You see it every Thursday. There is no forty-page presentation, because nobody has ever launched a presentation.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">3. Build</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Standards-based front-end work on top of WordPress, with the editing experience treated as part of the design rather than as something to be discovered after launch. Your editors get a walkthrough before the site goes live, not after.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">4. After launch</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two weeks of watching it closely, then a written handover: how it is put together, what to check quarterly, who owns the performance budget. After that, hourly when you need us or a retainer if you would rather not think about it.</p>
<!-- /wp:paragraph -->',
);

$pages['services'] = array(
	'title'   => 'Services',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Four things, done properly. We would rather turn work down than take on something we are not the right studio for.</p>
<!-- /wp:paragraph -->',
			$services,
			$use_image(
				'<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|xl","bottom":"var:preset|spacing|xl","left":"var:preset|spacing|sm","right":"var:preset|spacing|sm"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--xl);padding-right:var(--wp--preset--spacing--sm);padding-bottom:var(--wp--preset--spacing--xl);padding-left:var(--wp--preset--spacing--sm)">
<!-- wp:image {"sizeSlug":"large","linkDestination":"none","align":"wide","style":{"border":{"radius":"var:custom|radius|md"}}} -->
<figure class="wp-block-image alignwide size-large has-custom-border"><img src="' . $placeholder . '" alt="" style="border-radius:var(--wp--custom--radius--md)"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->',
				'services-working-session'
			),
			'<!-- wp:heading -->
<h2 class="wp-block-heading">What a project includes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>A discovery week, with a written plan and a fixed price at the end of it.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Design and build, shown weekly, at real widths with your real content.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Editor training for the people who will actually update the site.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A handover document covering hosting, updates, and the performance budget.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Two weeks of close attention after launch, included.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->',
			$faq,
			$cta,
		)
	),
);

$pages['contact'] = array(
	'title'   => 'Contact',
	'content' => implode(
		"\n\n",
		array(
			'<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Tell us what you are working on. We reply to every message within two business days, and we will say honestly if we are not the right studio for it.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|xl"},"blockGap":{"top":"var:preset|spacing|lg","left":"var:preset|spacing|xl"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--xl)">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":2,"fontSize":"h5"} -->
<h2 class="wp-block-heading has-h-5-font-size">Talk to us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="mailto:studio@northline.example">studio@northline.example</a><br>+44 20 7946 0813</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Monday to Thursday, 9am to 5pm.<br>Friday is for the studio&#8217;s own work.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":2,"fontSize":"h5"} -->
<h2 class="wp-block-heading has-h-5-font-size">Find us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Second floor, 14 Prince Street<br>Bristol BS1 4QF<br>United Kingdom</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Ten minutes from Temple Meads. There is no reception &#8212; ring the middle bell and someone will come down.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":2,"fontSize":"h5"} -->
<h2 class="wp-block-heading has-h-5-font-size">Before you write</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>It helps to know roughly what the project is for, when you need it live, and what you have budgeted. Any of the three is a useful start.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
			$use_image(
				'<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|xl"},"margin":{"top":"var:preset|spacing|xl"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:var(--wp--preset--spacing--xl);padding-top:var(--wp--preset--spacing--xl)">
<!-- wp:image {"sizeSlug":"large","linkDestination":"none","align":"wide","style":{"border":{"radius":"var:custom|radius|md"}}} -->
<figure class="wp-block-image alignwide size-large has-custom-border"><img src="' . $placeholder . '" alt="" style="border-radius:var(--wp--custom--radius--md)"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->',
				'contact-city-street'
			),
		)
	),
);

$pages['journal'] = array(
	'title'   => 'Journal',
	'content' => '',
);

$pages['privacy-policy'] = array(
	'title'   => 'Privacy policy',
	'content' => '<!-- wp:paragraph -->
<p>This is the sample privacy policy that ships with the demo. Replace it with your own before the site goes live — it describes what a small studio site normally collects, but it is not legal advice.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you email us, we keep the message and your address so we can reply and so we have a record of the conversation. If you leave a comment on a journal post, we store the name, address and message you submit, along with your IP address, to help us catch spam.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Analytics and cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This site sets a cookie only if you tick the box asking us to remember your details for your next comment. We measure traffic with a self-hosted, privacy-preserving tool that does not track individuals between sites.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Your rights</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ask us for a copy of anything we hold about you, or ask us to delete it, and we will do it within a month. Write to <a href="mailto:studio@northline.example">studio@northline.example</a>.</p>
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
// single-post layout missing. The demo's writer is the studio's principal.
$author_id = 1;

wp_update_user(
	array(
		'ID'           => $author_id,
		'first_name'   => 'Mari',
		'last_name'    => 'Lindqvist',
		'display_name' => 'Mari Lindqvist',
		'nickname'     => 'Mari Lindqvist',
		'description'  => 'Mari runs Northline Studio and writes most of what appears here. Twelve years of building for the web, and still the same opinion about page weight.',
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
			'post_type'     => 'post',
			'post_author'   => $author_id,
			'post_status'   => 'publish',
			'post_name'     => $post['slug'],
			'post_title'    => $post['title'],
			'post_content'  => $post['content'],
			'post_excerpt'  => $post['excerpt'],
			'post_date'     => wp_date( 'Y-m-d H:i:s', strtotime( $post['date'] ) ),
			'post_category' => array( $term_ids[ $post['category'] ] ),
			'tags_input'    => $post['tags'],
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
$about_item = $add( $primary, 'About', $page_ids['about'] );
$add( $primary, 'Our approach', $page_ids['our-approach'], $about_item );
$add( $primary, 'Services', $page_ids['services'] );
$add( $primary, 'Journal', $page_ids['journal'] );
$add( $primary, 'Contact', $page_ids['contact'] );

$add( $footer, 'About', $page_ids['about'] );
$add( $footer, 'Journal', $page_ids['journal'] );
$add( $footer, 'Contact', $page_ids['contact'] );
$add( $footer, 'Privacy policy', $page_ids['privacy-policy'] );

set_theme_mod( 'nav_menu_locations', array( 'primary' => $primary, 'footer' => $footer ) );
$log( 'menus: Main Menu (6 items, one submenu) and Footer Menu (4 items)' );

/* ---------------------------------------------------------------- widgets */

$widget_settings = array(
	'search'       => array( 2 => array( 'title' => 'Search the journal' ) ),
	'recent-posts' => array( 2 => array( 'title' => 'Recent writing', 'number' => 4 ), 3 => array( 'title' => 'From the journal', 'number' => 3 ) ),
	'categories'   => array( 2 => array( 'title' => 'Topics', 'count' => 0, 'hierarchical' => 0 ) ),
	'tag_cloud'    => array( 2 => array( 'title' => 'Tags', 'taxonomy' => 'post_tag' ) ),
	'nav_menu'     => array( 2 => array( 'title' => 'Studio', 'nav_menu' => $footer ) ),
	'text'         => array(
		2 => array(
			'title'  => 'Northline Studio',
			'text'   => "Web design and development for small teams.\n\nSecond floor, 14 Prince Street, Bristol BS1 4QF",
			'filter' => true,
		),
		3 => array(
			'title'  => 'Get in touch',
			'text'   => 'studio@northline.example<br>+44 20 7946 0813<br><br>Monday to Thursday, 9am to 5pm.',
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

/* --------------------------------------------------------------- settings */

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'page_for_posts', $page_ids['journal'] );
update_option( 'wp_page_for_privacy_policy', $page_ids['privacy-policy'] );
update_option( 'blogname', $data['site']['title'] );
update_option( 'blogdescription', $data['site']['description'] );
update_option( 'posts_per_page', 6 );

if ( isset( $media['northline-studio-logo'] ) ) {
	set_theme_mod( 'custom_logo', $media['northline-studio-logo']['id'] );
}

if ( isset( $media['northline-studio-icon'] ) ) {
	update_option( 'site_icon', $media['northline-studio-icon']['id'] );
}

// The theme's own settings live in one option (03-ui-system/03-theme-settings.md).
$settings = (array) get_option( 'kivora_theme_settings', array() );

update_option(
	'kivora_theme_settings',
	array_merge(
		$settings,
		array(
			'header_sticky'             => true,
			'header_transparent'        => false,
			'live_search_enabled'       => true,
			'sidebar_layout'            => 'right',
			'blog_layout'               => 'grid-3col',
			'single_show_related_posts' => true,
			'single_show_author_bio'    => true,
		)
	)
);

$log( 'front page, journal page, logo, site icon and theme settings set' );

flush_rewrite_rules( false );

$log( "\nDone. " . home_url() );
