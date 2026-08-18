<?php
/**
 * Export the site you are on into this library, as one demo.
 *
 *     wp eval-file tools/export-demo.php <demo-id> [path-to-this-repo]
 *
 * Writes into <repo>/<theme>/assets/<demo-id>/:
 *
 *   content.xml       the WXR export, with every media URL rewritten to this
 *                     library so the importer can fetch the images
 *   widgets.wie       Widget Importer & Exporter format
 *   customizer.dat    Customizer Export/Import format, image mods rewritten
 *                     the same way -- a logo left pointing at localhost is a
 *                     logo the buyer never sees
 *   attachments.json  old attachment ID => file name, which is what lets the
 *                     theme repair ACF image fields after an import
 *   uploads/          the original files (the buyer's site regenerates sizes)
 *
 * It does not write demos/<demo-id>.json, demo-library.json, or the preview
 * image: those carry wording and a screenshot, and are better written by hand.
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

$demo_id = isset($args[0]) ? $args[0] : '';
$repo    = isset($args[1]) ? rtrim(str_replace('\\', '/', $args[1]), '/') : getcwd();

if (!$demo_id) {
    WP_CLI::error('Usage: wp eval-file tools/export-demo.php <demo-id> [repo-path]');
}

$theme     = get_template();
$base_url  = "https://devmonowar.github.io/wp-theme-demo-library/{$theme}/assets/{$demo_id}";
$asset_dir = "{$repo}/{$theme}/assets/{$demo_id}";
$uploads   = wp_upload_dir();
$local_url = $uploads['baseurl'];
$local_dir = trailingslashit(str_replace('\\', '/', $uploads['basedir']));

if (!is_dir($asset_dir)) {
    mkdir($asset_dir, 0777, true);
}

// ---------------------------------------------------------------- content
$export = "{$asset_dir}/content.xml";

ob_start();
require_once ABSPATH . 'wp-admin/includes/export.php';
export_wp(['content' => 'all']);
$xml = ob_get_clean();

$trashed = 0;

/**
 * Drop anything in the trash.
 *
 * WordPress's exporter takes every status but auto-draft, so a page thrown away
 * months ago still travels to the buyer's site and lands in their trash, which
 * is a strange thing to find in a demo you just imported. WordPress's own
 * default Privacy Policy page is usually the one that does it.
 *
 * Done on the string rather than through DOMDocument on purpose: a parse and
 * re-serialise rewrites the whole file -- indentation gone, CDATA sections
 * turned into escaped text -- and this is a file buyers import. WXR never nests
 * <item>, so cutting whole items out is exact, and every byte of what is left
 * is what WordPress wrote.
 */
$xml = preg_replace_callback(
    '#\t<item>.*?</item>\r?\n#s',
    static function ($match) use (&$trashed) {
        if (false === strpos($match[0], '<wp:status><![CDATA[trash]]></wp:status>')) {
            return $match[0];
        }

        $trashed++;

        return '';
    },
    $xml
);

file_put_contents($export, str_replace($local_url, $base_url . '/uploads', $xml));
WP_CLI::log(sprintf(
    'content.xml     %s media URLs rewritten, %s trashed item(s) dropped',
    substr_count($xml, $local_url),
    $trashed
));

// ---------------------------------------------------------------- widgets
global $wp_registered_widget_updates;

$instances = [];

foreach (array_keys((array) $wp_registered_widget_updates) as $id_base) {
    foreach ((array) get_option('widget_' . $id_base) as $number => $settings) {
        if (is_numeric($number)) {
            $instances[$id_base . '-' . $number] = $settings;
        }
    }
}

$widgets = [];

foreach (get_option('sidebars_widgets') as $sidebar => $ids) {
    if ('wp_inactive_widgets' === $sidebar || !is_array($ids) || !$ids) {
        continue;
    }

    foreach ($ids as $widget_id) {
        if (isset($instances[$widget_id])) {
            $widgets[$sidebar][$widget_id] = $instances[$widget_id];
        }
    }
}

file_put_contents("{$asset_dir}/widgets.wie", wp_json_encode($widgets));
WP_CLI::log(sprintf('widgets.wie     %d sidebars, %d widgets', count($widgets), array_sum(array_map('count', $widgets))));

// ------------------------------------------------------------- customizer
$mods = get_theme_mods();
unset($mods['nav_menu_locations'], $mods['sidebars_widgets']);

array_walk_recursive($mods, function (&$value) use ($local_url, $base_url) {
    if (is_string($value)) {
        $value = str_replace($local_url, $base_url . '/uploads', $value);
    }
});

/*
 * Not every theme keeps its settings in theme mods. Kivora, for one, stores all
 * of them in a single prefixed option, so a demo exported with mods alone
 * arrives on the buyer's site with the theme back at its defaults -- sticky
 * header off, sidebar on the wrong side, live search disabled. Anything named
 * after the theme travels with the demo.
 */
global $wpdb;

$options     = [];
$option_rows = $wpdb->get_col(
    $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like($theme . '_') . '%'
    )
);

foreach ((array) $option_rows as $option_name) {
    $options[$option_name] = get_option($option_name);
}

array_walk_recursive($options, function (&$value) use ($local_url, $base_url) {
    if (is_string($value)) {
        $value = str_replace($local_url, $base_url . '/uploads', $value);
    }
});

file_put_contents("{$asset_dir}/customizer.dat", serialize([
    'template' => $theme,
    'mods'     => $mods,
    'options'  => $options,
]));
WP_CLI::log(sprintf(
    'customizer.dat  %d theme mods, %d %s_* option(s)',
    count($mods),
    count($options),
    $theme
));

// ------------------------------------------------ attachments and uploads
$attachments = [];
$copied      = 0;

foreach (get_posts(['post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'inherit']) as $attachment) {
    $file = get_post_meta($attachment->ID, '_wp_attached_file', true);

    if (!$file) {
        continue;
    }

    $attachments[$attachment->ID] = $file;

    $source = $local_dir . $file;
    $target = "{$asset_dir}/uploads/{$file}";

    if (file_exists($source)) {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        copy($source, $target);
        $copied++;
    }
}

file_put_contents("{$asset_dir}/attachments.json", wp_json_encode($attachments, JSON_PRETTY_PRINT));
WP_CLI::log(sprintf('attachments     %d mapped, %d files copied', count($attachments), $copied));

WP_CLI::success("Exported to {$asset_dir}");
WP_CLI::log('Still to do by hand: demos/' . $demo_id . '.json, the demo-library.json entry, previews/' . $demo_id . '.jpg, and the CREDITS.md rows.');
