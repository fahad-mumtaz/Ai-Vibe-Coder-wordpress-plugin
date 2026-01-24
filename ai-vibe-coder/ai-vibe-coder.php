<?php
/**
 * Plugin Name: AI Vibe Coder
 * Plugin URI: https://example.com/ai-vibe-coder
 * Description: AI-powered coding assistant for WordPress. Generate code snippets, shortcodes, widgets, and website tools using natural language.
 * Version: 1.0.0
 * Author: AI Vibe Coder Team
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-vibe-coder
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AI_VIBE_CODER_VERSION', '1.0.0');
define('AI_VIBE_CODER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_VIBE_CODER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AI_VIBE_CODER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class AI_Vibe_Coder {

    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Add menu items
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Add action links
        add_filter('plugin_action_links_' . AI_VIBE_CODER_PLUGIN_BASENAME, array($this, 'add_action_links'));

        // Register AJAX handlers
        add_action('wp_ajax_ai_vibe_generate_code', array($this, 'ajax_generate_code'));
        add_action('wp_ajax_ai_vibe_save_snippet', array($this, 'ajax_save_snippet'));
        add_action('wp_ajax_ai_vibe_get_snippets', array($this, 'ajax_get_snippets'));
        add_action('wp_ajax_ai_vibe_delete_snippet', array($this, 'ajax_delete_snippet'));
        add_action('wp_ajax_ai_vibe_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_ai_vibe_deploy_snippet', array($this, 'ajax_deploy_snippet'));

        // Initialize components
        $this->init_components();
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        require_once AI_VIBE_CODER_PLUGIN_PATH . 'includes/class-api-handler.php';
        require_once AI_VIBE_CODER_PLUGIN_PATH . 'includes/class-code-generator.php';
        require_once AI_VIBE_CODER_PLUGIN_PATH . 'includes/class-snippet-manager.php';
        require_once AI_VIBE_CODER_PLUGIN_PATH . 'includes/class-content-generator.php';

        // Initialize API Handler
        AI_Vibe_Coder_API::get_instance();

        // Initialize Code Generator
        AI_Vibe_Coder_Code_Generator::get_instance();

        // Initialize Snippet Manager
        AI_Vibe_Coder_Snippet_Manager::get_instance();

        // Initialize Content Generator
        AI_Vibe_Coder_Content_Generator::get_instance();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
            'api_provider' => 'openai',
            'api_key' => '',
            'model' => 'gpt-4',
            'temperature' => 0.7,
            'max_tokens' => 2000,
            'enable_shortcodes' => true,
            'enable_widgets' => true,
            'enable_content' => true,
            'enable_functions' => true,
            'auto_save' => true,
        );

        if (!get_option('ai_vibe_coder_options')) {
            add_option('ai_vibe_coder_options', $default_options);
        }

        // Create necessary database tables
        $this->create_tables();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup if needed
        flush_rewrite_rules();
    }

    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('ai-vibe-coder', false, dirname(AI_VIBE_CODER_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Create necessary database tables
     */
    private function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ai_vibe_snippets';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            code longtext NOT NULL,
            description text,
            tags text,
            deployed boolean DEFAULT false,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Add admin menu items
     */
    public function add_admin_menu() {
        add_menu_page(
            __('AI Vibe Coder', 'ai-vibe-coder'),
            __('AI Vibe Coder', 'ai-vibe-coder'),
            'manage_options',
            'ai-vibe-coder',
            array($this, 'render_main_page'),
            'dashicons-code-standards',
            30
        );

        add_submenu_page(
            'ai-vibe-coder',
            __('Code Generator', 'ai-vibe-coder'),
            __('Code Generator', 'ai-vibe-coder'),
            'manage_options',
            'ai-vibe-coder-generator',
            array($this, 'render_generator_page')
        );

        add_submenu_page(
            'ai-vibe-coder',
            __('My Snippets', 'ai-vibe-coder'),
            __('My Snippets', 'ai-vibe-coder'),
            'manage_options',
            'ai-vibe-coder-snippets',
            array($this, 'render_snippets_page')
        );

        add_submenu_page(
            'ai-vibe-coder',
            __('Content AI', 'ai-vibe-coder'),
            __('Content AI', 'ai-vibe-coder'),
            'manage_options',
            'ai-vibe-coder-content',
            array($this, 'render_content_page')
        );

        add_submenu_page(
            'ai-vibe-coder',
            __('Settings', 'ai-vibe-coder'),
            __('Settings', 'ai-vibe-coder'),
            'manage_options',
            'ai-vibe-coder-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Add action links on plugin page
     */
    public function add_action_links($links) {
        $action_links = array(
            '<a href="' . admin_url('admin.php?page=ai-vibe-coder') . '">' . __('Dashboard', 'ai-vibe-coder') . '</a>',
            '<a href="' . admin_url('admin.php?page=ai-vibe-coder-settings') . '">' . __('Settings', 'ai-vibe-coder') . '</a>',
        );

        return array_merge($action_links, $links);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'ai-vibe-coder-frontend',
            AI_VIBE_CODER_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            AI_VIBE_CODER_VERSION
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'ai-vibe-coder') === false) {
            return;
        }

        wp_enqueue_style(
            'ai-vibe-coder-admin',
            AI_VIBE_CODER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AI_VIBE_CODER_VERSION
        );

        wp_enqueue_script(
            'ai-vibe-coder-admin',
            AI_VIBE_CODER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            AI_VIBE_CODER_VERSION,
            true
        );

        wp_localize_script('ai-vibe-coder-admin', 'aiVibeCoder', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ai_vibe_coder_nonce'),
            'i18n' => array(
                'generating' => __('Generating...', 'ai-vibe-coder'),
                'generated' => __('Code Generated!', 'ai-vibe-coder'),
                'error' => __('Error occurred', 'ai-vibe-coder'),
                'save' => __('Save Snippet', 'ai-vibe-coder'),
                'saved' => __('Saved!', 'ai-vibe-coder'),
                'deploy' => __('Deploy', 'ai-vibe-coder'),
                'deployed' => __('Deployed!', 'ai-vibe-coder'),
                'copy' => __('Copy Code', 'ai-vibe-coder'),
                'copied' => __('Copied!', 'ai-vibe-coder'),
                'delete' => __('Delete', 'ai-vibe-coder'),
                'deleted' => __('Deleted!', 'ai-vibe-coder'),
                'confirm_delete' => __('Are you sure you want to delete this snippet?', 'ai-vibe-coder'),
                'loading' => __('AI is thinking...', 'ai-vibe-coder'),
            ),
        ));
    }

    /**
     * Render main dashboard page
     */
    public function render_main_page() {
        include AI_VIBE_CODER_PLUGIN_PATH . 'templates/dashboard.php';
    }

    /**
     * Render code generator page
     */
    public function render_generator_page() {
        include AI_VIBE_CODER_PLUGIN_PATH . 'templates/generator.php';
    }

    /**
     * Render snippets page
     */
    public function render_snippets_page() {
        include AI_VIBE_CODER_PLUGIN_PATH . 'templates/snippets.php';
    }

    /**
     * Render content generator page
     */
    public function render_content_page() {
        include AI_VIBE_CODER_PLUGIN_PATH . 'templates/content.php';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        include AI_VIBE_CODER_PLUGIN_PATH . 'templates/settings.php';
    }

    /**
     * AJAX: Generate code
     */
    public function ajax_generate_code() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $prompt = sanitize_text_field($_POST['prompt'] ?? '');
        $type = sanitize_text_field($_POST['type'] ?? 'shortcode');
        $context = sanitize_text_field($_POST['context'] ?? '');

        if (empty($prompt)) {
            wp_send_json_error(__('Please enter a prompt', 'ai-vibe-coder'));
        }

        $code_generator = AI_Vibe_Coder_Code_Generator::get_instance();
        $result = $code_generator->generate($prompt, $type, $context);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Save snippet
     */
    public function ajax_save_snippet() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $name = sanitize_text_field($_POST['name'] ?? '');
        $type = sanitize_text_field($_POST['type'] ?? 'shortcode');
        $code = wp_unslash($_POST['code'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $tags = sanitize_text_field($_POST['tags'] ?? '');

        if (empty($name) || empty($code)) {
            wp_send_json_error(__('Name and code are required', 'ai-vibe-coder'));
        }

        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $result = $snippet_manager->save($name, $type, $code, $description, $tags);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(array(
            'message' => __('Snippet saved successfully', 'ai-vibe-coder'),
            'id' => $result,
        ));
    }

    /**
     * AJAX: Get snippets
     */
    public function ajax_get_snippets() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $snippets = $snippet_manager->get_all();

        wp_send_json_success($snippets);
    }

    /**
     * AJAX: Delete snippet
     */
    public function ajax_delete_snippet() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $id = intval($_POST['id'] ?? 0);

        if (empty($id)) {
            wp_send_json_error(__('Invalid snippet ID', 'ai-vibe-coder'));
        }

        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $result = $snippet_manager->delete($id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(array(
            'message' => __('Snippet deleted', 'ai-vibe-coder'),
        ));
    }

    /**
     * AJAX: Generate content
     */
    public function ajax_generate_content() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $prompt = sanitize_text_field($_POST['prompt'] ?? '');
        $content_type = sanitize_text_field($_POST['content_type'] ?? 'post');

        if (empty($prompt)) {
            wp_send_json_error(__('Please enter a prompt', 'ai-vibe-coder'));
        }

        $content_generator = AI_Vibe_Coder_Content_Generator::get_instance();
        $result = $content_generator->generate($prompt, $content_type);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Deploy snippet
     */
    public function ajax_deploy_snippet() {
        check_ajax_referer('ai_vibe_coder_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-vibe-coder'));
        }

        $id = intval($_POST['id'] ?? 0);

        if (empty($id)) {
            wp_send_json_error(__('Invalid snippet ID', 'ai-vibe-coder'));
        }

        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $result = $snippet_manager->deploy($id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(array(
            'message' => __('Snippet deployed successfully', 'ai-vibe-coder'),
        ));
    }
}

// Initialize the plugin
AI_Vibe_Coder::get_instance();

// Shortcode for displaying AI Vibe Coder interface on frontend
function ai_vibe_coder_frontend_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'generator',
    ), $atts);

    ob_start();
    include AI_VIBE_CODER_PLUGIN_PATH . 'templates/frontend-generator.php';
    return ob_get_clean();
}
add_shortcode('ai_vibe_coder', 'ai_vibe_coder_frontend_shortcode');
