<?php
/**
 * Code Generator Class
 * Generates WordPress code using AI
 */

class AI_Vibe_Coder_Code_Generator {

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
        // Private constructor to prevent direct instantiation
    }

    /**
     * Generate code based on prompt
     */
    public function generate($prompt, $type = 'shortcode', $context = '') {
        $api = AI_Vibe_Coder_API::get_instance();
        $system_prompt = $this->get_system_prompt($type);

        $user_message = $this->build_user_message($prompt, $type, $context);

        $messages = array(
            array(
                'role' => 'user',
                'content' => $user_message,
            ),
        );

        $result = $api->make_request($messages, $system_prompt);

        if (is_wp_error($result)) {
            return $result;
        }

        // Parse the generated code
        $parsed = $this->parse_generated_code($result, $type);

        return $parsed;
    }

    /**
     * Get system prompt based on code type
     */
    private function get_system_prompt($type) {
        $prompts = array(
            'shortcode' => __("You are an expert WordPress developer specializing in creating custom shortcodes. 
                Generate clean, well-commented, and secure WordPress shortcode code.
                
                Requirements:
                - Use proper escaping functions (esc_html__, esc_attr__, esc_url__, etc.)
                - Sanitize all inputs
                - Follow WordPress coding standards
                - Include proper documentation
                - Handle edge cases gracefully
                - Use WordPress core functions where possible
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'widget' => __("You are an expert WordPress developer specializing in creating custom widgets.
                Generate clean, well-structured WordPress widget code that extends WP_Widget class.
                
                Requirements:
                - Extend WP_Widget class properly
                - Use proper escaping functions
                - Include form() method with settings
                - Include widget() method for display
                - Include update() method for saving settings
                - Follow WordPress widget coding standards
                - Use WordPress core functions where possible
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'function' => __("You are an expert WordPress developer specializing in creating custom functions and hooks.
                Generate clean, reusable WordPress functions and hooks.
                
                Requirements:
                - Use proper escaping and sanitization
                - Follow WordPress coding standards
                - Include proper hooks/filters when appropriate
                - Make functions reusable and modular
                - Include PHPDoc comments
                - Handle edge cases gracefully
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'css' => __("You are an expert CSS developer specializing in WordPress themes.
                Generate clean, responsive CSS code that follows WordPress coding standards.
                
                Requirements:
                - Use proper CSS selectors and specificity
                - Include responsive design considerations
                - Use CSS custom properties (variables) when appropriate
                - Follow BEM or similar naming convention
                - Include comments for complex sections
                - Ensure cross-browser compatibility
                
                Return ONLY the CSS code inside ```css``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'javascript' => __("You are an expert JavaScript developer specializing in WordPress.
                Generate clean, modern JavaScript code compatible with WordPress.
                
                Requirements:
                - Use WordPress JavaScript coding standards
                - Properly enqueue scripts using wp_enqueue_script
                - Use jQuery as needed (with proper noConflict handling)
                - Include proper event handling
                - Use modern JavaScript (ES6+) when appropriate
                - Handle AJAX operations properly
                
                Return ONLY the JavaScript code inside ```js``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'post_type' => __("You are an expert WordPress developer specializing in custom post types.
                Generate complete custom post type registration code.
                
                Requirements:
                - Use register_post_type() properly
                - Include all necessary labels
                - Set appropriate capabilities
                - Include supports and features
                - Add rewrite rules if needed
                - Follow WordPress coding standards
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'taxonomy' => __("You are an expert WordPress developer specializing in custom taxonomies.
                Generate complete custom taxonomy registration code.
                
                Requirements:
                - Use register_taxonomy() properly
                - Include all necessary labels
                - Set appropriate object type
                - Add rewrite rules if needed
                - Include hierarchical support if specified
                - Follow WordPress coding standards
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),

            'plugin' => __("You are an expert WordPress plugin developer.
                Generate complete plugin structure with proper WordPress standards.
                
                Requirements:
                - Include proper plugin header
                - Use proper activation/deactivation hooks
                - Follow WordPress coding standards
                - Include security measures
                - Make code modular and maintainable
                - Include i18n support
                
                Return ONLY the PHP code inside ```php``` code blocks. No explanations before or after.", 'ai-vibe-coder'),
        );

        return $prompts[$type] ?? $prompts['shortcode'];
    }

    /**
     * Build user message with context
     */
    private function build_user_message($prompt, $type, $context) {
        $type_labels = array(
            'shortcode' => __('shortcode', 'ai-vibe-coder'),
            'widget' => __('WordPress widget', 'ai-vibe-coder'),
            'function' => __('custom function', 'ai-vibe-coder'),
            'css' => __('CSS styles', 'ai-vibe-coder'),
            'javascript' => __('JavaScript code', 'ai-vibe-coder'),
            'post_type' => __('custom post type', 'ai-vibe-coder'),
            'taxonomy' => __('custom taxonomy', 'ai-vibe-coder'),
            'plugin' => __('WordPress plugin', 'ai-vibe-coder'),
        );

        $type_label = $type_labels[$type] ?? __('code snippet', 'ai-vibe-coder');

        $message = sprintf(
            /* translators: %1$s: type of code, %2$s: user prompt, %3$s: additional context */
            __('Create a %1$s with the following requirements: %2$s', 'ai-vibe-coder'),
            $type_label,
            $prompt
        );

        if (!empty($context)) {
            $message .= "\n\n" . sprintf(
                /* translators: %s: additional context */
                __('Additional context: %s', 'ai-vibe-coder'),
                $context
            );
        }

        return $message;
    }

    /**
     * Parse generated code from AI response
     */
    private function parse_generated_code($response, $type) {
        // Extract code from markdown code blocks
        $code_blocks = array();
        
        // Match ```php or ```css or ```js code blocks
        preg_match_all('/```(?:php|css|js)?\s*([\s\S]*?)```/i', $response, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $code) {
                $code_blocks[] = trim($code);
            }
        } else {
            // If no code blocks found, use the entire response
            $code_blocks[] = trim($response);
        }

        // Determine language for syntax highlighting
        $language = 'php';
        if ($type === 'css') {
            $language = 'css';
        } elseif ($type === 'javascript') {
            $language = 'javascript';
        }

        return array(
            'code' => implode("\n\n", $code_blocks),
            'explanation' => $this->extract_explanation($response),
            'language' => $language,
            'type' => $type,
        );
    }

    /**
     * Extract explanation from response
     */
    private function extract_explanation($response) {
        // Remove code blocks to get explanation
        $explanation = preg_replace('/```(?:php|css|js)?\s*[\s\S]*?```/i', '', $response);
        
        // Clean up whitespace
        $explanation = trim($explanation);
        
        // If explanation is too short, it might not be useful
        if (strlen($explanation) < 10) {
            return '';
        }

        return $explanation;
    }

    /**
     * Get code type options
     */
    public function get_code_types() {
        return array(
            'shortcode' => __('Shortcode', 'ai-vibe-coder'),
            'widget' => __('Widget', 'ai-vibe-coder'),
            'function' => __('Custom Function', 'ai-vibe-coder'),
            'css' => __('CSS Styles', 'ai-vibe-coder'),
            'JavaScript' => __('JavaScript', 'ai-vibe-coder'),
            'post_type' => __('Custom Post Type', 'ai-vibe-coder'),
            'taxonomy' => __('Custom Taxonomy', 'ai-vibe-coder'),
            'plugin' => __('Plugin Template', 'ai-vibe-coder'),
        );
    }

    /**
     * Get example prompts for each type
     */
    public function get_example_prompts($type) {
        $examples = array(
            'shortcode' => array(
                /* translators: example prompt for shortcode */
                __('Create a shortcode that displays a responsive image gallery with lightbox effect', 'ai-vibe-coder'),
                /* translators: example prompt for shortcode */
                __('Create a shortcode for a pricing table with three tiers and a signup button', 'ai-vibe-coder'),
                /* translators: example prompt for shortcode */
                __('Create a shortcode that shows recent posts from a specific category', 'ai-vibe-coder'),
            ),
            'widget' => array(
                /* translators: example prompt for widget */
                __('Create a widget that displays social media icons with hover effects', 'ai-vibe-coder'),
                /* translators: example prompt for widget */
                __('Create a widget for a newsletter signup form with validation', 'ai-vibe-coder'),
            ),
            'function' => array(
                /* translators: example prompt for function */
                __('Create a function to add custom CSS to the head based on page template', 'ai-vibe-coder'),
                /* translators: example prompt for function */
                __('Create a function to modify the read more link in excerpts', 'ai-vibe-coder'),
            ),
            'css' => array(
                /* translators: example prompt for CSS */
                __('Create CSS for a modern button with gradient, shadow, and hover animation', 'ai-vibe-coder'),
                /* translators: example prompt for CSS */
                __('Create responsive card styles with hover effects and transitions', 'ai-vibe-coder'),
            ),
            'javascript' => array(
                /* translators: example prompt for JavaScript */
                __('Create JavaScript for smooth scroll to anchor links with offset', 'ai-vibe-coder'),
                /* translators: example prompt for JavaScript */
                __('Create JavaScript for form validation with custom error messages', 'ai-vibe-coder'),
            ),
            'post_type' => array(
                /* translators: example prompt for custom post type */
                __('Create a "Portfolio" custom post type with categories and tags support', 'ai-vibe-coder'),
                /* translators: example prompt for custom post type */
                __('Create a "Testimonials" custom post type with featured image support', 'ai-vibe-coder'),
            ),
            'taxonomy' => array(
                /* translators: example prompt for custom taxonomy */
                __('Create a "Project Categories" taxonomy for portfolio items', 'ai-vibe-coder'),
                /* translators: example prompt for custom taxonomy */
                __('Create a "Skills" taxonomy for team members', 'ai-vibe-coder'),
            ),
        );

        return $examples[$type] ?? array();
    }
}
