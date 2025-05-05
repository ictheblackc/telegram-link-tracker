<?php

/*
 * Plugin Name: Telegram Link Tracker
 */

defined('ABSPATH') || exit;

define('TLT_VERSION', '1.0');
define('TLT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TLT_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TLT_PLUGIN_DIR . 'includes/class-link-tracker.php';

add_action('plugins_loaded', ['Telegram_Link_Tracker', 'init']);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'tlt-tracker',
        TLT_PLUGIN_URL . 'public/js/tracker.js',
        [],
        TLT_VERSION,
        true
    );
});