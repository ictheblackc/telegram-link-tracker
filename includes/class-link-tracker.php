<?php

defined('ABSPATH') || exit;

class Telegram_Link_Tracker {
    
    public static function init() {
        add_action('init', [self::class, 'register_cpt']);
        add_action('rest_api_init', [self::class, 'register_routes']);
    }
    
    public static function register_cpt() {
        register_post_type('telegram_bot_link', [
            'label' => 'Ссылки Telegram-бота',
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-admin-links',
        ]);
    }
    
    public static function register_routes() {
        register_rest_route('tlt/v1', '/generate/', [
            'methods' => 'POST',
            'callback' => [self::class, 'generate_link'],
            'permission_callback' => '__return_true',
        ]);
        
        register_rest_route('tlt/v1', '/info/(?P<id>[a-zA-Z0-9]+)', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_link'],
            'permission_callback' => '__return_true',
        ]);
    }
    
    /**
     * Generate a short link for the given full URL.
     *
     * @param [type] $request
     * @return void
     */
    public static function generate_link($request) {
        $params = $request->get_json_params();
        $full_url = esc_url_raw($params['full_url'] ?? '');
        
        if (!$full_url) {
            return new WP_Error('invalid_url', 'Invalid or missing full_url', ['status' => 400]);
        }
        
        $links = new WP_Query([
            'post_type' => 'telegram_bot_link',
            'meta_query' => [
                ['key' => 'full_url', 'value' => $full_url],
            ],
            'posts_per_page' => 1,
        ]);
        
        if ($inks->have_posts()) {
            return ['short_id' => $inks->posts[0]->post_title];
        }
        
        $short_id = substr(md5(uniqid('', true)), 0, 6);
        wp_insert_post([
            'post_type' => 'telegram_bot_link',
            'post_title' => $short_id,
            'post_status' => 'publish',
            'meta_input' => [
                'full_url' => $full_url,
            ],
        ]);
        
        return ['short_id' => $short_id];
    }
    
    /**
     * Get the link info for a given short ID.
     *
     * @param [type] $request
     * @return void
     */
    public static function get_link($request) {
        $id = sanitize_text_field($request['id']);
        
        $query = new WP_Query([
            'post_type' => 'telegram_bot_link',
            'name' => $id,
            'posts_per_page' => 1,
        ]);
        
        if ($query->have_posts()) {
            $post = $query->posts[0];
            return [
                'full_url' => get_post_meta($post->ID, 'full_url', true)
            ];
        }
        
        return new WP_Error('not_found', 'Short ID not found', ['status' => 404]);
    }
}