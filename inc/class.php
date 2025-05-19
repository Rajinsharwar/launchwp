<?php

namespace LaunchWP;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Main {
    /**
     * Initialize the plugin
     */
    public function __construct() {
        static $checked = false;

        // Only check once per request
        if ($checked) {
            return;
        }

        $checked = true;
        add_action( 'admin_bar_menu', [ $this, 'add_cache_flush_for_pages' ], 999 );
        add_action( 'admin_init', [ $this, 'admin_init_actions' ] );
        
        if ( ! $this->is_launchwp() ) {
            add_action( 'admin_notices', [ $this, 'display_not_launchwp_notice' ] );
            return;
        }

        add_action('admin_bar_menu', [$this, 'add_environment_indicator'], 1);
        // add_action('admin_bar_menu', [$this, 'add_cache_flush_for_pages'], 1);
        add_action('admin_head', [$this, 'add_environment_styles']);
        add_action('get_user_option_admin_color', [$this, 'set_default_admin_color']);
        add_action('save_post', [$this, 'flush_redis_cache_for_post'], 10, 3);
    }

    /**
     * Check if the site is powered by LaunchWP
     *
     * @return bool
     */
    private function is_launchwp() {
        $powered_by = isset($_SERVER['HTTP_X_POWERED_BY']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_POWERED_BY'])) : '';
        return !empty($powered_by) && stripos($powered_by, 'LaunchWP.io') !== false;
    }

    /**
     * Display admin notice if site is not powered by LaunchWP
     */
    public function display_not_launchwp_notice() {
        $class = 'notice notice-error';
        $message = sprintf(
            /* translators: 1: LaunchWP website URL, 2: Opening link tag for deactivation, 3: Closing link tag */
            __('This site is not powered by <a href="%1$s" target="_blank">LaunchWP</a>. The LaunchWP Helper plugin is designed to work exclusively with LaunchWP-powered websites. Please %2$sdeactivate this plugin%3$s.', 'launchwp'),
            'https://launchwp.io',
            '<a href="' . esc_url(wp_nonce_url(admin_url('plugins.php?action=deactivate&plugin=launchwp/launchwp.php'), 'deactivate-plugin_launchwp-helper-plugin/launchwp.php')) . '">',
            '</a>'
        );

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), wp_kses_post($message));
    }

    /**
     * Get the current environment type (staging or live)
     *
     * @return array Array containing environment type and label
     */
    private function get_environment() {
        if (!$this->is_launchwp()) {
            return [
                'type' => 'unknown',
                'label' => 'UNKNOWN'
            ];
        }

        $site_domain = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
        $env_type = preg_match('/^stg-\d+\.launchwp\.site$/', $site_domain) ? 'staging' : 'live';
        $env_label = $env_type === 'staging' ? 'STAGING' : 'LIVE';

        return [
            'type' => $env_type,
            'label' => $env_label
        ];
    }

    /**
     * Add environment indicator to admin bar
     *
     * @param \WP_Admin_Bar $admin_bar
     */
    public function add_environment_indicator($admin_bar) {
        $environment = $this->get_environment();

        $admin_bar->add_node([
            'id'    => 'launchwp-environment',
            'title' => sprintf('<span class="launchwp-env launchwp-env-%s">%s</span>', 
                             esc_attr($environment['type']), 
                             esc_html($environment['label'])),
            'href'  => '#',
            'meta'  => [
                'class' => 'launchwp-environment-indicator'
            ],
            'parent' => null,
            'priority' => 1
        ]);
    }

    /**
     * Add Cache Flush for indiviual pages
     */
    public function add_cache_flush_for_pages() {
        if ( current_user_can( 'manage_options' ) && ! is_admin() ) {
            global $wp_admin_bar;

            ?>
            <style>
                #wp-admin-bar-launchwp_cache_for_page {
                    background-color: green !important;
                }
            </style>
            <?php

            $current_url = 'http' . ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 's' : '' ) . '://' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . esc_url( wp_unslash( $_SERVER['REQUEST_URI'] ) );
            // Add a top-level menu item displaying parent.
            $wp_admin_bar->add_menu(
                array(
                    'id' => 'launchwp_cache_for_page',
                    'title' => 'LaunchWP Cache',
                    'href' => false, // No link for this.
                )
            );

            $flush_url = wp_nonce_url(admin_url('admin-post.php?action=flush_launchwp_cache&url=' . urlencode($current_url)), 'flush_launchwp_cache_flush_url_action', 'flush_launchwp_cache_nonce');
            $wp_admin_bar->add_menu(array(
                'parent' => 'launchwp_cache_for_page',
                'id' => 'launchwp_flush_for_page',
                'title' => 'Flush this URL',
                'href' => $flush_url,
            ));
        }
    }

    /**
     * Add styles for environment indicator
     */
    public function add_environment_styles() {
        wp_enqueue_style(
            'launchwp-environment-styles',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css',
            [],
            LAUNCHWP_HELPER_VERSION
        );
    }

    public function set_default_admin_color( $color_scheme ) {
        $color_scheme = 'modern';
        return $color_scheme;
    }

    /**
     * Flush Redis cache for a specific post URL when the post is saved
     *
     * @param int $post_id The post ID
     * @param WP_Post $post The post object
     * @param bool $update Whether this is an existing post being updated
     */
    public function flush_redis_cache_for_post($post_id, $post, $update) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        if (wp_is_post_autosave($post_id)) return;
        if ('publish' !== get_post_status($post_id)) return;

        try {
            $redis = new \Redis();
            $redis->connect('redis', 6379);

            $post_url = get_permalink($post_id);
            if (!$post_url) return;

            // Parse URL to extract path and query
            $parsed_url = wp_parse_url($post_url);
            $request_uri = $parsed_url['path'] ?? '/';
            if (!empty($parsed_url['query'])) {
                $request_uri .= '?' . $parsed_url['query'];
            }

            $host = wp_parse_url(home_url(), PHP_URL_HOST);
            
            // Generate cache keys for both HTTP and HTTPS variations
            $cache_keys = [
                $host . '_page' . ':httpGET' . $host . $request_uri,
                $host . '_page' . ':httpsGET' . $host . $request_uri,
            ];

            // Delete the cache for this URL
            foreach ($cache_keys as $cache_key) {
                $redis->del($cache_key);
            }

            // Also delete paginated versions of this URL
            for ($i = 2; $i <= 10; $i++) {
                foreach ($cache_keys as $cache_key) {
                    $paginated_key = $cache_key . "page/$i/";
                    $redis->del($paginated_key);
                }
            }

            $redis->close();
        } catch (\Exception $e) {
            if ( 'staging' === $this->get_environment() ) {
                error_log('LaunchWP Redis Cache Flush Error for ' . $post_url . ': ' . $e->getMessage() );
            }
        }
    }

    /**
     * Flush LaunchWP cache for a specific URL
     *
     * @param string $post_url The post URL
     */
    public function flush_launchwp_cache_for_url( $post_url ) {
        if ( ! $post_url || ! is_string( $post_url ) ) {
            return;
        }

        try {
            $redis = new \Redis();
            $redis->connect('redis', 6379);

            // Parse URL to extract path and query
            $parsed_url = wp_parse_url($post_url);
            $request_uri = $parsed_url['path'] ?? '/';
            if (!empty($parsed_url['query'])) {
                $request_uri .= '?' . $parsed_url['query'];
            }

            $host = wp_parse_url(home_url(), PHP_URL_HOST);
            
            // Generate cache keys for both HTTP and HTTPS variations
            $cache_keys = [
                $host . '_page' . ':httpGET' . $host . $request_uri,
                $host . '_page' . ':httpsGET' . $host . $request_uri,
            ];

            // Delete the cache for this URL
            foreach ($cache_keys as $cache_key) {
                $redis->del($cache_key);
            }

            // Also delete paginated versions of this URL
            for ($i = 2; $i <= 10; $i++) {
                foreach ($cache_keys as $cache_key) {
                    $paginated_key = $cache_key . "page/$i/";
                    $redis->del($paginated_key);
                }
            }

            $redis->close();
        } catch (\Exception $e) {
            if ( 'staging' === $this->get_environment() ) {
                error_log('LaunchWP Redis Cache Flush Error for ' . $post_url . ': ' . $e->getMessage() );
            }
        }
    }

    /**
     * Flush LaunchWP cache for a URLs
     *
     * @param array $post_urls Array of URLs to flush cache for.
     */
    public function flush_launchwp_cache_for_urls( $post_urls ) {
        if ( ! $post_urls || ! is_array( $post_urls ) ) {
            return;
        }

        try {
            $redis = new \Redis();
            $redis->connect('redis', 6379);

            $cache_keys = [];
            $host = wp_parse_url(home_url(), PHP_URL_HOST);
            foreach ( $post_urls as $post_url ) {
                // Parse URL to extract path and query
                $parsed_url = wp_parse_url($post_url);
                $request_uri = $parsed_url['path'] ?? '/';
                if (!empty($parsed_url['query'])) {
                    $request_uri .= '?' . $parsed_url['query'];
                }

                $cache_keys[] = [ $host . '_page' . ':httpGET' . $host . $request_uri ];
                $cache_keys[] = [ $host . '_page' . ':httpsGET' . $host . $request_uri ];
            }

            // Delete the cache for this URL
            foreach ($cache_keys as $cache_key) {
                $redis->del($cache_key);
            }

            // Also delete paginated versions of this URL
            for ($i = 2; $i <= 10; $i++) {
                foreach ($cache_keys as $cache_key) {
                    $paginated_key = $cache_key . "page/$i/";
                    $redis->del($paginated_key);
                }
            }

            $redis->close();
        } catch (\Exception $e) {
            if ( 'staging' === $this->get_environment() ) {
                error_log('LaunchWP Redis Cache Flush Error for multiple URLS: ' . $e->getMessage() );
            }
        }
    }

    /**
     * Flush full LaunchWP cache.
     */
    public function flush_full_launchwp_cache() {
        try {
            $redis = new \Redis();
            $redis->connect('redis', 6379);
            $redis->flushDB();
            $redis->close();
        } catch (\Exception $e) {
            if ( 'staging' === $this->get_environment() ) {
                error_log('LaunchWP Redis Cache Flush Error for Full cache: ' . $e->getMessage() );
            }
        }
    }

    /**
     * Admin Init Actions
     */
    public function admin_init_actions() {
        //Warm URL Cache from Admin Toolbar Single Page.
        if (isset($_GET['action']) && $_GET['action'] === 'flush_launchwp_cache' && isset($_GET['flush_launchwp_cache_nonce'])) {
    
            $nonce = isset( $_GET['flush_launchwp_cache_nonce'] ) ? sanitize_key( $_GET['flush_launchwp_cache_nonce'] ) : '';
            if ( wp_verify_nonce( $nonce, 'flush_launchwp_cache_flush_url_action' ) ) {
                $current_url = isset( $_GET['url'] ) ? esc_url_raw( wp_unslash( $_GET['url'] ) ) : '';

                $flush = $this->flush_launchwp_cache_for_url( $current_url );

                wp_safe_redirect( $current_url );
                exit; // Exit to prevent further execution
            } else {
                wp_die('Nonce verification failed');
            }
        }
    }
}