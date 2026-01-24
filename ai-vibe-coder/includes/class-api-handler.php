<?php
/**
 * API Handler Class
 * Manages connections to AI services
 */

class AI_Vibe_Coder_API {

    private static $instance = null;
    private $options;

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
        $this->options = get_option('ai_vibe_coder_options', array());
    }

    /**
     * Get API key
     */
    public function get_api_key() {
        return $this->options['api_key'] ?? '';
    }

    /**
     * Get API provider
     */
    public function get_provider() {
        return $this->options['api_provider'] ?? 'openai';
    }

    /**
     * Get model
     */
    public function get_model() {
        return $this->options['model'] ?? 'gpt-4';
    }

    /**
     * Get temperature
     */
    public function get_temperature() {
        return $this->options['temperature'] ?? 0.7;
    }

    /**
     * Get max tokens
     */
    public function get_max_tokens() {
        return $this->options['max_tokens'] ?? 2000;
    }

    /**
     * Check if API is configured
     */
    public function is_configured() {
        return !empty($this->get_api_key());
    }

    /**
     * Make API request
     */
    public function make_request($messages, $system_prompt = '') {
        if (!$this->is_configured()) {
            return new WP_Error('api_not_configured', __('API key not configured. Please add your API key in Settings.', 'ai-vibe-coder'));
        }

        $provider = $this->get_provider();

        switch ($provider) {
            case 'openai':
                return $this->request_openai($messages, $system_prompt);
            case 'anthropic':
                return $this->request_anthropic($messages, $system_prompt);
            default:
                return $this->request_openai($messages, $system_prompt);
        }
    }

    /**
     * OpenAI API request
     */
    private function request_openai($messages, $system_prompt = '') {
        $api_key = $this->get_api_key();
        $model = $this->get_model();
        $temperature = $this->get_temperature();
        $max_tokens = $this->get_max_tokens();

        // Prepare messages
        $chat_messages = array();
        
        if (!empty($system_prompt)) {
            $chat_messages[] = array(
                'role' => 'system',
                'content' => $system_prompt,
            );
        }

        foreach ($messages as $message) {
            $chat_messages[] = array(
                'role' => $message['role'] ?? 'user',
                'content' => $message['content'],
            );
        }

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => $chat_messages,
                'temperature' => $temperature,
                'max_tokens' => $max_tokens,
            )),
            'timeout' => 60,
        );

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

        if (is_wp_error($response)) {
            return new WP_Error('api_error', $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message'] ?? __('API error occurred', 'ai-vibe-coder'));
        }

        if (!isset($body['choices'][0]['message']['content'])) {
            return new WP_Error('api_error', __('Invalid API response', 'ai-vibe-coder'));
        }

        return $body['choices'][0]['message']['content'];
    }

    /**
     * Anthropic API request
     */
    private function request_anthropic($messages, $system_prompt = '') {
        $api_key = $this->get_api_key();
        $model = $this->get_model();
        $temperature = $this->get_temperature();
        $max_tokens = $this->get_max_tokens();

        // Prepare messages
        $chat_messages = array();
        
        if (!empty($system_prompt)) {
            $chat_messages[] = array(
                'role' => 'user',
                'content' => $system_prompt,
            );
        }

        foreach ($messages as $message) {
            $chat_messages[] = array(
                'role' => $message['role'] ?? 'user',
                'content' => $message['content'],
            );
        }

        $args = array(
            'headers' => array(
                'x-api-key' => $api_key,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => $chat_messages,
                'temperature' => $temperature,
                'max_tokens' => $max_tokens,
            )),
            'timeout' => 60,
        );

        $response = wp_remote_post('https://api.anthropic.com/v1/messages', $args);

        if (is_wp_error($response)) {
            return new WP_error('api_error', $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message'] ?? __('API error occurred', 'ai-vibe-coder'));
        }

        if (!isset($body['content'][0]['text'])) {
            return new WP_Error('api_error', __('Invalid API response', 'ai-vibe-coder'));
        }

        return $body['content'][0]['text'];
    }

    /**
     * Test API connection
     */
    public function test_connection() {
        $test_messages = array(
            array(
                'role' => 'user',
                'content' => 'Say "Hello" if you can hear me.',
            ),
        );

        $result = $this->make_request($test_messages);

        if (is_wp_error($result)) {
            return $result;
        }

        return true;
    }

    /**
     * Get available models for provider
     */
    public function get_available_models($provider = null) {
        $provider = $provider ?? $this->get_provider();

        $models = array(
            'openai' => array(
                'gpt-4' => 'GPT-4',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ),
            'anthropic' => array(
                'claude-sonnet-4-20250514' => 'Claude Sonnet 4',
                'claude-opus-4-20250514' => 'Claude Opus 4',
                'claude-haiku-3-20250514' => 'Claude Haiku 3',
            ),
        );

        return $models[$provider] ?? $models['openai'];
    }

    /**
     * Get available providers
     */
    public function get_available_providers() {
        return array(
            'openai' => __('OpenAI (GPT)', 'ai-vibe-coder'),
            'anthropic' => __('Anthropic (Claude)', 'ai-vibe-coder'),
        );
    }
}
