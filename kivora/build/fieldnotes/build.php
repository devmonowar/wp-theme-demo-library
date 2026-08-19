<?php
/**
 * Build the "Fieldnotes" demo (Demo 2) on the demo site. Re-runnable: it
 * clears everything the previous run created before it starts.
 *
 *     php build.php
 *
 * Copy lives in content.php. Images come from ../img/final/ with their credits
 * in ../img/credits-fieldnotes.json, both produced by ../img/build-assets.php.
 *
 * The homepage is assembled from the theme's own registered patterns rather
 * than hand-written markup: a demo that drifts from the patterns stops
 * demonstrating the theme. Where the demo needs its own words, the pattern's
 * sample string is replaced by name, and a string that no longer matches stops
 * the build instead of silently shipping the theme's marketing copy.
 */

const DEMO_SITE = 'C:/xampp/htdocs/kivora-demo';

require DEMO_SITE . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// This script deletes everything it finds. It runs on the build site or nowhere.
if ( ! str_contains( home_url(), 'kivora-demo' ) ) {
	fwrite( STDERR, 'Refusing to run: home_url() is ' . home_url() . ", not the demo build site.\n" );
	exit( 1 );
}

$content = require __DIR__ . '/content.php';
// The published uploads are the images: one copy in the repo, and the copy a
// buyer actually receives.
$img_dir = dirname( __DIR__, 2 ) . '/assets/fieldnotes/uploads/2026/08';
$credits = json_decode( (string) file_get_contents( dirname( __DIR__ ) . '/img/credits-fieldnotes.json' ), true );

if ( ! is_array( $credits ) || ! $credits ) {
	fwrite( STDERR, "No credits.json -- run img/build-assets.php first.\n" );
	exit( 1 );
}

$warnings = array();

/**
 * Replace one exact string, and complain loudly if it is no longer there.
 *
 * @param string               $haystack Markup.
 * @param string               $needle   The pattern's own sample string.
 * @param string               $with     The demo's wording.
 * @param string               $where    Label for the warning.
 * @param array<int, string>   $warnings Accumulator, by reference.
 * @return string
 */
function swap( string $haystack, string $needle, string $with, string $where, array &$warnings ): string {
	if ( ! str_contains( $haystack, $needle ) ) {
		$warnings[] = sprintf( '%s: pattern no longer contains "%s"', $where, substr( $needle, 0, 60 ) );

		return $haystack;
	}

	return str_replace( $needle, $with, $haystack );
}

/**
 * Expand a registered pattern, following any patterns nested inside it.
 *
 * @param string $slug  Pattern slug.
 * @param int    $depth Recursion guard.
 * @return string
 */
function expand_pattern( string $slug, int $depth = 0 ): string {
	if ( $depth > 4 ) {
		return '';
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	$pattern  = $registry->get_registered( $slug );

	if ( ! $pattern ) {
		fwrite( STDERR, "Pattern not registered: $slug\n" );
		exit( 1 );
	}

	$markup = (string) $pattern['content'];

	return (string) preg_replace_callback(
		'#<!--\s*wp:pattern\s*(\{.*?\})\s*/-->#s',
		static function ( array $m ) use ( $depth ): string {
			$args = json_decode( $m[1], true );

			return isset( $args['slug'] ) ? expand_pattern( (string) $args['slug'], $depth + 1 ) : '';
		},
		$markup
	);
}

/**
 * Delete everything a previous run left behind.
 */
function wipe(): void {
	$ids = get_posts(
		array(
			'post_type'      => array( 'post', 'page', 'attachment', 'nav_menu_item', 'wp_block' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}

	foreach ( wp_get_nav_menus() as $menu ) {
		wp_delete_nav_menu( $menu->term_id );
	}

	foreach ( array( 'category', 'post_tag' ) as $taxonomy ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		foreach ( (array) $terms as $term ) {
			if ( (int) get_option( 'default_category' ) === (int) $term->term_id ) {
				continue;
			}

			wp_delete_term( $term->term_id, $taxonomy );
		}
	}

	// Uploads on disk, so a rebuild does not leave orphans behind.
	$dir = wp_upload_dir();

	foreach ( (array) glob( trailingslashit( $dir['basedir'] ) . '*/*/*' ) as $file ) {
		if ( is_file( $file ) ) {
			unlink( $file );
		}
	}

	foreach ( array( 'sidebars_widgets' ) as $option ) {
		delete_option( $option );
	}

	foreach ( (array) get_option( 'widget_block', array() ) as $key => $value ) {
		if ( is_numeric( $key ) ) {
			update_option( 'widget_block', array( '_multiwidget' => 1 ) );
			break;
		}
	}
}

/**
 * Put one local file into the media library.
 *
 * @param string $path Absolute path to the file.
 * @param string $title Attachment title.
 * @param string $alt   Alt text.
 * @return int Attachment ID.
 */
function sideload( string $path, string $title, string $alt ): int {
	$upload = wp_upload_bits( basename( $path ), null, (string) file_get_contents( $path ) );

	if ( ! empty( $upload['error'] ) ) {
		fwrite( STDERR, 'Upload failed: ' . $upload['error'] . "\n" );
		exit( 1 );
	}

	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );

	return (int) $id;
}

/**
 * Wrap paragraphs as core/paragraph blocks.
 *
 * @param array<int, string> $paragraphs Text.
 * @return string
 */
function paragraphs( array $paragraphs ): string {
	$out = '';

	foreach ( $paragraphs as $text ) {
		$out .= "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}

	return $out;
}

echo "Clearing the previous run\n";
wipe();

/* ---------------------------------------------------------------- site --- */

$site = $content['site'];

update_option( 'blogname', $site['title'] );
update_option( 'blogdescription', $site['tagline'] );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'timezone_string', 'Europe/London' );
update_option( 'date_format', 'j F Y' );
update_option( 'posts_per_page', 6 );
update_option( 'default_comment_status', 'open' );
update_option( 'thread_comments', 1 );

wp_update_user(
	array(
		'ID'           => 1,
		'display_name' => $site['author_name'],
		'first_name'   => strtok( $site['author_name'], ' ' ),
		'last_name'    => trim( (string) strstr( $site['author_name'], ' ' ) ),
		'description'  => $site['author_bio'],
	)
);

/* -------------------------------------------------------------- images --- */

$media = array();

foreach ( $credits as $slot => $credit ) {
	$path = "$img_dir/" . $credit['file'];

	if ( ! file_exists( $path ) ) {
		fwrite( STDERR, "Missing image: $path\n" );
		exit( 1 );
	}

	$media[ $slot ] = sideload( $path, (string) $credit['title'], (string) $credit['alt'] );
}

printf( "Imported %d image(s)\n", count( $media ) );

/* --------------------------------------------------------------- terms --- */

$cat_ids = array();

foreach ( $content['categories'] as $key => $category ) {
	$term = wp_insert_term(
		$category['name'],
		'category',
		array( 'description' => $category['description'] )
	);

	$cat_ids[ $key ] = is_wp_error( $term ) ? (int) get_cat_ID( $category['name'] ) : (int) $term['term_id'];
}

/* --------------------------------------------------------------- posts --- */

$post_ids = array();
$sticky   = array();
$when     = time() - ( count( $content['posts'] ) + 1 ) * DAY_IN_SECONDS * 6;

foreach ( $content['posts'] as $key => $post ) {
	$when += DAY_IN_SECONDS * 6;

	$id = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $post['title'],
			'post_excerpt'  => $post['excerpt'],
			'post_content'  => paragraphs( $post['content'] ),
			'post_author'   => 1,
			'post_date'     => gmdate( 'Y-m-d H:i:s', $when ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $when ),
			'post_category' => array( $cat_ids[ $post['category'] ] ),
			'tags_input'    => $post['tags'] ?? array(),
			'comment_status' => 'open',
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, 'Post failed: ' . $id->get_error_message() . "\n" );
		exit( 1 );
	}

	$post_ids[ $key ] = (int) $id;

	if ( ! empty( $post['image'] ) && isset( $media[ $post['image'] ] ) ) {
		set_post_thumbnail( $id, $media[ $post['image'] ] );
	}

	if ( ! empty( $post['sticky'] ) ) {
		$sticky[] = (int) $id;
	}

	foreach ( $post['comments'] ?? array() as $i => $comment ) {
		wp_insert_comment(
			array(
				'comment_post_ID'      => $id,
				'comment_author'       => $comment['author'],
				'comment_author_email' => sanitize_title( $comment['author'] ) . '@example.com',
				'comment_content'      => $comment['content'],
				'comment_approved'     => 1,
				'user_id'              => empty( $comment['is_site'] ) ? 0 : 1,
				'comment_date'         => gmdate( 'Y-m-d H:i:s', $when + ( $i + 1 ) * HOUR_IN_SECONDS * 5 ),
				'comment_date_gmt'     => gmdate( 'Y-m-d H:i:s', $when + ( $i + 1 ) * HOUR_IN_SECONDS * 5 ),
			)
		);
	}
}

update_option( 'sticky_posts', $sticky );

printf( "Created %d post(s), %d sticky\n", count( $post_ids ), count( $sticky ) );

/* --------------------------------------------------------------- pages --- */

$pages = $content['pages'];
$page_ids = array();

// Home, from the theme's own patterns.
$home = expand_pattern( 'kivora/page-blog-home' );

$home = swap( $home, 'Writing &amp; notes', 'Since 2015', 'hero eyebrow', $warnings );
$home = swap( $home, 'Notes on craft, tools, and the work in between', 'Walking, writing, and paying attention', 'hero heading', $warnings );
$home = swap(
	$home,
	'Short essays published most weeks. No newsletter pop-ups, no tracking — just the writing.',
	'A journal of the same twenty square miles, walked again and again, and what turns up each time. New writing most weeks.',
	'hero text',
	$warnings
);

$home = swap( $home, 'Latest writing', 'Recently', 'latest posts heading', $warnings );

$home = swap( $home, 'A small studio with a long memory', 'Eleven years of the same hills', 'about heading', $warnings );
$home = swap(
	$home,
	'We have been building for the web since before responsive design had a name. That history shows up as restraint: fewer moving parts, clearer handover notes, and sites that survive their third redesign.',
	'Going somewhere new is easy to write about, because everything in it is new. Going the same way for the eleventh time and still finding something is the harder problem, and it is the one this journal is about.',
	'about paragraph 1',
	$warnings
);
$home = swap(
	$home,
	'Most of our work starts as a conversation. Tell us what is not working and we will tell you honestly whether we are the right people to fix it.',
	'Most of what is here started as four words written standing up, in weather, and was finished later at a desk with the coat still on the back of the chair.',
	'about paragraph 2',
	$warnings
);

$home = swap( $home, 'One email, once a month', 'One letter, once a month', 'newsletter heading', $warnings );
$home = swap(
	$home,
	'New writing, useful links, and the occasional half-finished idea. Unsubscribe whenever you like.',
	'What I walked, what I read, and the photographs that did not make it onto the site. Unsubscribe whenever you like.',
	'newsletter text',
	$warnings
);

// The pattern ships a placeholder; the demo ships a photograph.
$placeholder = get_theme_file_uri( 'assets/images/placeholder.svg' );
$about_id    = $media['about-walking'];
$about_url   = wp_get_attachment_image_url( $about_id, 'large' );
$about_alt   = (string) $credits['about-walking']['alt'];

$home = swap(
	$home,
	'<img src="' . esc_url( $placeholder ) . '" alt=""/>',
	'<img src="' . esc_url( (string) $about_url ) . '" alt="' . esc_attr( $about_alt ) . '" class="wp-image-' . $about_id . ' size-large"/>',
	'about image',
	$warnings
);

$page_ids['home'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $pages['home']['title'],
		'post_content' => $home,
		'post_author'  => 1,
	)
);

if ( ! empty( $pages['home']['hide_title'] ) ) {
	update_post_meta( $page_ids['home'], \Kivora\Page\Settings::META_HIDE_TITLE, true );
}

/*
 * About: a wide photograph, then the words. There is no portrait here on
 * purpose -- the CC0 portraits available are identifiable people, and a demo
 * that ships to thousands of sites should not carry someone's face with it.
 * The page is about a place anyway.
 */
$about    = $pages['about'];
$field_id = $media['about-field'];

$about_body = '<!-- wp:image {"id":' . $field_id . ',"sizeSlug":"large","linkDestination":"none","align":"wide"} -->' . "\n"
	. '<figure class="wp-block-image alignwide size-large"><img src="' . esc_url( (string) wp_get_attachment_image_url( $field_id, 'large' ) ) . '" alt="' . esc_attr( (string) $credits['about-field']['alt'] ) . '" class="wp-image-' . $field_id . '"/></figure>' . "\n"
	. '<!-- /wp:image -->' . "\n\n"
	. '<!-- wp:paragraph {"fontSize":"h5"} -->' . "\n"
	. '<p class="has-h-5-font-size">' . esc_html( $about['lead'] ) . '</p>' . "\n"
	. '<!-- /wp:paragraph -->' . "\n\n"
	. paragraphs( $about['body'] );

$page_ids['about'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $about['title'],
		'post_content' => $about_body,
		'post_author'  => 1,
	)
);

// Journal: the posts page, deliberately empty.
$page_ids['journal'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $pages['journal']['title'],
		'post_content' => '',
		'post_author'  => 1,
	)
);

// Photographs: a gallery of the images already in the library.
$gallery_slots = array( 'walk-footpath', 'morning-mist', 'coast-cliffs', 'winter-woods', 'stone-wall', 'river-dusk' );
$gallery_ids   = array();
$gallery_inner = '';

foreach ( $gallery_slots as $slot ) {
	$id            = $media[ $slot ];
	$gallery_ids[] = $id;
	$gallery_inner .= '<!-- wp:image {"id":' . $id . ',"sizeSlug":"large","linkDestination":"none"} -->' . "\n"
		. '<figure class="wp-block-image size-large"><img src="' . esc_url( (string) wp_get_attachment_image_url( $id, 'large' ) ) . '" alt="' . esc_attr( (string) $credits[ $slot ]['alt'] ) . '" class="wp-image-' . $id . '"/></figure>' . "\n"
		. '<!-- /wp:image -->' . "\n\n";
}

$photographs = $pages['photographs'];
$photo_body  = '<!-- wp:paragraph {"fontSize":"h5"} -->' . "\n<p class=\"has-h-5-font-size\">" . esc_html( $photographs['lead'] ) . "</p>\n<!-- /wp:paragraph -->\n\n"
	. paragraphs( $photographs['body'] )
	. '<!-- wp:gallery {"columns":3,"linkTo":"none","align":"wide"} -->' . "\n"
	. '<figure class="wp-block-gallery alignwide has-nested-images columns-3 is-cropped">' . "\n"
	. $gallery_inner
	. '</figure>' . "\n"
	. '<!-- /wp:gallery -->' . "\n\n"
	. paragraphs( array( $photographs['closing'] ) );

$page_ids['photographs'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $photographs['title'],
		'post_content' => $photo_body,
		'post_author'  => 1,
	)
);

// Contact.
$contact      = $pages['contact'];
$contact_body = '<!-- wp:paragraph {"fontSize":"h5"} -->' . "\n<p class=\"has-h-5-font-size\">" . esc_html( $contact['lead'] ) . "</p>\n<!-- /wp:paragraph -->\n\n"
	. paragraphs( $contact['body'] )
	. '<!-- wp:columns {"align":"wide"} -->' . "\n"
	. '<div class="wp-block-columns alignwide">' . "\n"
	. '<!-- wp:column -->' . "\n<div class=\"wp-block-column\">\n"
	. "<!-- wp:heading {\"level\":2,\"fontSize\":\"h5\"} -->\n<h2 class=\"wp-block-heading has-h-5-font-size\">Email</h2>\n<!-- /wp:heading -->\n"
	. "<!-- wp:paragraph -->\n<p><a href=\"mailto:" . esc_attr( $contact['email'] ) . '">' . esc_html( $contact['email'] ) . "</a></p>\n<!-- /wp:paragraph -->\n"
	. "</div>\n<!-- /wp:column -->\n"
	. '<!-- wp:column -->' . "\n<div class=\"wp-block-column\">\n"
	. "<!-- wp:heading {\"level\":2,\"fontSize\":\"h5\"} -->\n<h2 class=\"wp-block-heading has-h-5-font-size\">Post</h2>\n<!-- /wp:heading -->\n"
	. "<!-- wp:paragraph -->\n<p>" . implode( '<br>', array_map( 'esc_html', $contact['postal'] ) ) . "</p>\n<!-- /wp:paragraph -->\n"
	. "</div>\n<!-- /wp:column -->\n"
	. "</div>\n<!-- /wp:columns -->";

$page_ids['contact'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $contact['title'],
		'post_content' => $contact_body,
		'post_author'  => 1,
	)
);

// Privacy policy.
$page_ids['privacy'] = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $pages['privacy']['title'],
		'post_content' => paragraphs( $pages['privacy']['body'] ),
		'post_author'  => 1,
	)
);

printf( "Created %d page(s)\n", count( $page_ids ) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'page_for_posts', $page_ids['journal'] );
update_option( 'wp_page_for_privacy_policy', $page_ids['privacy'] );

/* --------------------------------------------------------------- menus --- */

$locations = array();

foreach ( $content['menus'] as $location => $menu ) {
	$menu_id = wp_create_nav_menu( $menu['name'] );

	foreach ( $menu['items'] as $page_key ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => get_the_title( $page_ids[ $page_key ] ),
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_ids[ $page_key ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}

	$locations[ $location ] = (int) $menu_id;
}

set_theme_mod( 'nav_menu_locations', $locations );

printf( "Created %d menu(s)\n", count( $locations ) );

/* ------------------------------------------------------------- widgets --- */

/*
 * Classic widgets, not block widgets: this is what Widget Importer & Exporter
 * carries reliably, and it is the path Demo 1 was proved on. Three footer
 * columns rather than four, so the demo also shows the fourth collapsing.
 */
$widget_settings = array(
	'search'       => array( 2 => array( 'title' => 'Search' ) ),
	'text'         => array(
		2 => array(
			'title'  => 'About',
			'text'   => 'A journal of walking and writing, kept for eleven years by ' . $site['author_name'] . ".\n\nMost of it starts as four words written standing up, in weather.",
			'filter' => true,
		),
		3 => array(
			'title'  => 'Fieldnotes',
			'text'   => 'Walking, writing, and paying attention.<br><br>Words and photographs by ' . $site['author_name'] . ', and free to reuse with credit.',
			'filter' => true,
		),
	),
	'recent-posts' => array( 2 => array( 'title' => 'Recent writing', 'number' => 5 ) ),
	'categories'   => array(
		2 => array( 'title' => 'Categories', 'count' => 1, 'hierarchical' => 0 ),
		3 => array( 'title' => 'Browse', 'count' => 0, 'hierarchical' => 0 ),
	),
	'tag_cloud'    => array( 2 => array( 'title' => 'Tags', 'taxonomy' => 'post_tag' ) ),
);

foreach ( $widget_settings as $widget_base => $instances ) {
	$instances['_multiwidget'] = 1;
	update_option( 'widget_' . $widget_base, $instances );
}

update_option(
	'sidebars_widgets',
	array(
		'wp_inactive_widgets' => array(),
		'main-sidebar'        => array( 'search-2', 'text-2', 'recent-posts-2', 'categories-2' ),
		'footer-column-1'     => array( 'text-3' ),
		'footer-column-2'     => array( 'categories-3' ),
		'footer-column-3'     => array( 'tag_cloud-2' ),
		'array_version'       => 3,
	)
);

echo "Filled the sidebar and three footer columns\n";

/* ------------------------------------------------------------ settings --- */

update_option( 'kivora_theme_settings', $content['settings'] );

echo "\n";

if ( $warnings ) {
	echo "WARNINGS:\n";

	foreach ( $warnings as $warning ) {
		echo "  - $warning\n";
	}

	echo "\n";
}

printf( "Done. %s\n", home_url() );
exit( $warnings ? 1 : 0 );
