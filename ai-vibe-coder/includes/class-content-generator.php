<?php
/**
 * Content Generator Class
 * Generates content using AI
 */

class AI_Vibe_Coder_Content_Generator {

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
     * Generate content based on prompt
     */
    public function generate($prompt, $content_type = 'post') {
        $api = AI_Vibe_Coder_API::get_instance();
        $system_prompt = $this->get_system_prompt($content_type);

        $user_message = $this->build_user_message($prompt, $content_type);

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

        // Parse the generated content
        $parsed = $this->parse_generated_content($result, $content_type);

        return $parsed;
    }

    /**
     * Get system prompt based on content type
     */
    private function get_system_prompt($type) {
        $prompts = array(
            'post' => __("You are an expert content writer for WordPress blogs.
                Generate engaging, SEO-friendly content.
                
                Requirements:
                - Use proper HTML structure with headings (h2, h3, etc.)
                - Include relevant keywords naturally
                - Write engaging introduction and conclusion
                - Use paragraphs, lists, and other formatting
                - Follow SEO best practices
                - Write in a professional but approachable tone
                - Output complete article content ready for WordPress
                
                Return ONLY the content in HTML format. No explanations before or after.", 'ai-vibe-coder'),

            'page' => __("You are an expert content writer for WordPress pages.
                Generate professional page content.
                
                Requirements:
                - Use proper HTML structure
                - Keep content focused and on-brand
                - Include appropriate calls-to-action
                - Use professional tone
                - Format content with headings, paragraphs, and lists
                - Output complete page content ready for WordPress
                
                Return ONLY the content in HTML format. No explanations before or after.", 'ai-vibe-coder'),

            'excerpt' => __("You are an expert at writing WordPress excerpts.
                Generate concise, engaging excerpts that encourage readers to continue.
                
                Requirements:
                - Keep under 200 words
                - Capture the essence of the content
                - End with a hook to read more
                - Use active voice
                - Include main keyword if relevant
                
                Return ONLY the excerpt text. No explanations.", 'ai-vibe-coder'),

            'title' => __("You are an expert at writing SEO-friendly titles.
                Generate compelling, keyword-rich titles.
                
                Requirements:
                - Keep under 60 characters for SEO
                - Include main keyword naturally
                - Make it compelling and click-worthy
                - Use power words when appropriate
                
                Return ONLY the title. No explanations.", 'ai-vibe-coder'),

            'meta_description' => __("You are an expert at writing meta descriptions.
                Generate SEO-friendly meta descriptions.
                
                Requirements:
                - Keep under 160 characters
                - Include main keyword
                - Include call-to-action
                - Make it compelling for searchers
                
                Return ONLY the meta description. No explanations.", 'ai-vibe-coder'),

            'product_description' => __("You are an expert e-commerce copywriter.
                Generate compelling product descriptions.
                
                Requirements:
                - Highlight key features and benefits
                - Use persuasive language
                - Include specifications when relevant
                - Make it scannable with bullet points
                - Include subtle call-to-action
                
                Return ONLY the product description in HTML format.", 'ai-vibe-coder'),
        );

        return $prompts[$type] ?? $prompts['post'];
    }

    /**
     * Build user message with context
     */
    private function build_user_message($prompt, $type) {
        $type_labels = array(
            'post' => __('blog post', 'ai-vibe-coder'),
            'page' => __('web page', 'ai-vibe-coder'),
            'excerpt' => __('excerpt', 'ai-vibe-coder'),
            'title' => __('title', 'ai-vibe-coder'),
            'meta_description' => __('meta description', 'ai-vibe-coder'),
            'product_description' => __('product description', 'ai-vibe-coder'),
        );

        $type_label = $type_labels[$type] ?? __('content', 'ai-vibe-coder');

        $message = sprintf(
            /* translators: %1$s: type of content, %2$s: user prompt */
            __('Write a %1$s about: %2$s', 'ai-vibe-coder'),
            $type_label,
            $prompt
        );

        return $message;
    }

    /**
     * Parse generated content
     */
    private function parse_generated_content($response, $type) {
        // If it's HTML content, return as-is
        if (strpos($response, '<') !== false) {
            return array(
                'content' => trim($response),
                'plain_text' => wp_strip_all_tags($response),
                'type' => $type,
            );
        }

        // For plain text content (like titles, excerpts)
        return array(
            'content' => wpautop(trim($response)),
            'plain_text' => trim($response),
            'type' => $type,
        );
    }

    /**
     * Generate and save as draft
     */
    public function generate_and_save($prompt, $content_type, $title = '') {
        $result = $this->generate($prompt, $content_type);

        if (is_wp_error($result)) {
            return $result;
        }

        // Create the post/page
        $post_data = array(
            'post_title' => $title ?: $this->generate_title($prompt),
            'post_content' => $result['content'],
            'post_status' => 'draft',
            'post_type' => $content_type === 'page' ? 'page' : 'post',
            'post_excerpt' => $result['type'] === 'excerpt' ? $result['plain_text'] : '',
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        return array(
            'post_id' => $post_id,
            'title' => $post_data['post_title'],
            'content' => $result['content'],
            'edit_url' => get_edit_post_link($post_id),
        );
    }

    /**
     * Generate title from prompt
     */
    private function generate_title($prompt) {
        $api = AI_Vibe_Coder_API::get_instance();

        $messages = array(
            array(
                'role' => 'user',
                'content' => sprintf(
                    /* translators: %s: user prompt */
                    __('Generate a short, SEO-friendly title (max 60 chars) for: %s', 'ai-vibe-coder'),
                    $prompt
                ),
            ),
        );

        $system_prompt = __("You are an expert at writing titles. Return ONLY the title text. No explanations, no quotes.", 'ai-vibe-coder');

        $result = $api->make_request($messages, $system_prompt);

        if (is_wp_error($result)) {
            // Fallback: use first 50 characters of prompt
            return ucfirst(substr($prompt, 0, 50));
        }

        return trim($result);
    }

    /**
     * Get content type options
     */
    public function get_content_types() {
        return array(
            'post' => __('Blog Post', 'ai-vibe-coder'),
            'page' => __('Page', 'ai-vibe-coder'),
            'excerpt' => __('Excerpt', 'ai-vibe-coder'),
            'title' => __('Title', 'ai-vibe-coder'),
            'meta_description' => __('Meta Description', 'ai-vibe-coder'),
            'product_description' => __('Product Description', 'ai-vibe-coder'),
        );
    }

    /**
     * Get example prompts for each type
     */
    public function get_example_prompts($type) {
        $examples = array(
            'post' => array(
                /* translators: example prompt for blog post */
                __('The benefits of AI in modern web development', 'ai-vibe-coder'),
                /* translators: example prompt for blog post */
                __('How to improve your WordPress site performance', 'ai-vibe-coder'),
                /* translators: example prompt for blog post */
                __('10 tips for better content marketing', 'ai-vibe-coder'),
            ),
            'page' => array(
                /* translators: example prompt for page */
                __('About our company and our mission', 'ai-vibe-coder'),
                /* translators: example prompt for page */
                __('Our services and what we offer', 'ai-vibe-coder'),
            ),
            'excerpt' => array(
                /* translators: example prompt for excerpt */
                __('AI is transforming how we build websites', 'ai-vibe-coder'),
            ),
            'title' => array(
                /* translators: example prompt for title */
                __('AI in web development', 'ai-vibe-coder'),
            ),
            'meta_description' => array(
                /* translators: example prompt for meta description */
                __('AI-powered web development services', 'ai-vibe-coder'),
            ),
            'product_description' => array(
                /* translators: example prompt for product description */
                __('Premium WordPress hosting with AI features', 'ai-vibe-coder'),
            ),
        );

        return $examples[$type] ?? array();
    }

    /**
     * Improve existing content
     */
    public function improve($content, $instructions) {
        $api = AI_Vibe_Coder_API::get_instance();

        $messages = array(
            array(
                'role' => 'user',
                'content' => sprintf(
                    /* translators: %1$s: original content, %2$s: improvement instructions */
                    __('Original content: %1$s
                    
                    Instructions for improvement: %2$s
                    
                    Improve the content following the instructions. Return ONLY the improved content in HTML format.', 'ai-vibe-coder'),
                    $content,
                    $instructions
                ),
            ),
        );

        $system_prompt = __("You are an expert content editor. Improve content while maintaining the original meaning. Return ONLY the improved content in HTML format.", 'ai-vibe-coder');

        $result = $api->make_request($messages, $system_prompt);

        if (is_wp_error($result)) {
            return $result;
        }

        return array(
            'content' => trim($result),
            'plain_text' => wp_strip_all_tags($result),
        );
    }

    /**
     * Translate content
     */
    public function translate($content, $target_language) {
        $api = AI_Vibe_Coder_API::get_instance();

        $messages = array(
            array(
                'role' => 'user',
                'content' => sprintf(
                    /* translators: %1$s: original content, %2$s: target language */
                    __('Content to translate: %1$s
                    
                    Translate to: %2$s
                    
                    Return ONLY the translated content in HTML format.', 'ai-vibe-coder'),
                    $content,
                    $target_language
                ),
            ),
        );

        $system_prompt = sprintf(
            /* translators: %s: target language */
            __("You are a professional translator. Translate content to %s while maintaining formatting and meaning. Return ONLY the translated content in HTML format.", 'ai-vibe-coder'),
            $target_language
        );

        $result = $api->make_request($messages, $system_prompt);

        if (is_wp_error($result)) {
            return $result;
        }

        return array(
            'content' => trim($result),
            'plain_text' => wp_strip_all_tags($result),
            'language' => $target_language,
        );
    }

    /**
     * Summarize content
     */
    public function summarize($content, $length = 'short') {
        $api = AI_Vibe_Coder_API::get_instance();

        $length_guide = array(
            'short' => __('1-2 sentences', 'ai-vibe-coder'),
            'medium' => __('1 paragraph', 'ai-vibe-coder'),
            'long' => __('2-3 paragraphs', 'ai-vibe-coder'),
        );

        $messages = array(
            array(
                'role' => 'user',
                'content' => sprintf(
                    /* translators: %1$s: content to summarize, %2$s: desired length */
                    __('Content: %1$s
                    
                    Summarize in: %2$s
                    
                    Return ONLY the summary.', 'ai-vibe-coder'),
                    $content,
                    $length_guide[$length] ?? $length_guide['short']
                ),
            ),
        );

        $system_prompt = __("You are an expert at summarizing content. Return ONLY the summary text.", 'ai-vibe-coder');

        $result = $api->make_request($messages, $system_prompt);

        if (is_wp_error($result)) {
            return $result;
        }

        return trim($result);
    }
}
