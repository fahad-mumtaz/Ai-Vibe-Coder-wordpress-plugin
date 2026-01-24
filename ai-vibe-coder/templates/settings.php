<div class="ai-vibe-coder-wrap">
    <div class="ai-vibe-coder-header">
        <h1>
            <span class="dashicons dashicons-admin-generic"></span>
            <?php _e('Settings', 'ai-vibe-coder'); ?>
        </h1>
        <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder'); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="margin-top: 3px;"></span>
            <?php _e('Back to Dashboard', 'ai-vibe-coder'); ?>
        </a>
    </div>

    <form id="ai-vibe-coder-settings-form">
        <?php
        $options = get_option('ai_vibe_coder_options', array());
        $api = AI_Vibe_Coder_API::get_instance();
        ?>

        <!-- API Configuration -->
        <div class="ai-vibe-coder-card">
            <div class="ai-vibe-coder-card-header">
                <h2>
                    <span class="dashicons dashicons-admin-network" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('API Configuration', 'ai-vibe-coder'); ?>
                </h2>
            </div>

            <div class="ai-vibe-coder-settings-section">
                <h3><?php _e('AI Provider', 'ai-vibe-coder'); ?></h3>
                <p style="color: #646970; font-size: 14px; margin-bottom: 15px;">
                    <?php _e('Choose which AI service you want to use for code generation.', 'ai-vibe-coder'); ?>
                </p>

                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-api-provider"><?php _e('Provider', 'ai-vibe-coder'); ?></label>
                    <select id="ai-vibe-api-provider" name="api_provider" class="ai-vibe-coder-input ai-vibe-coder-select">
                        <?php foreach ($api->get_available_providers() as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($options['api_provider'] ?? 'openai', $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-api-key">
                        <?php _e('API Key', 'ai-vibe-coder'); ?>
                        <span class="ai-vibe-coder-help-tip" title="<?php _e('Your API key is stored securely and never shared', 'ai-vibe-coder'); ?>">?</span>
                    </label>
                    <input type="password" id="ai-vibe-api-key" name="api_key" class="regular-text" 
                           value="<?php echo esc_attr($options['api_key'] ?? ''); ?>"
                           placeholder="<?php _e('Enter your API key', 'ai-vibe-coder'); ?>">
                    <small><?php _e('Get your API key from your AI provider\'s dashboard.', 'ai-vibe-coder'); ?></small>
                </div>

                <button type="button" class="button ai-vibe-coder-test-api">
                    <span class="dashicons dashicons-yes-alt" style="margin-top: 3px;"></span>
                    <?php _e('Test Connection', 'ai-vibe-coder'); ?>
                </button>
            </div>

            <div class="ai-vibe-coder-settings-section">
                <h3><?php _e('Model Settings', 'ai-vibe-coder'); ?></h3>

                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-model"><?php _e('Model', 'ai-vibe-coder'); ?></label>
                    <select id="ai-vibe-model" name="model" class="ai-vibe-coder-input ai-vibe-coder-select">
                        <?php foreach ($api->get_available_models() as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($options['model'] ?? 'gpt-4', $key); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small><?php _e('Different models have different capabilities and pricing.', 'ai-vibe-coder'); ?></small>
                </div>

                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-temperature">
                        <?php _e('Temperature', 'ai-vibe-coder'); ?>
                        <span class="ai-vibe-coder-help-tip" title="<?php _e('Lower values produce more focused output, higher values produce more creative output', 'ai-vibe-coder'); ?>">?</span>
                    </label>
                    <input type="range" id="ai-vibe-temperature" name="temperature" 
                           min="0" max="1" step="0.1" 
                           value="<?php echo esc_attr($options['temperature'] ?? 0.7); ?>"
                           style="width: 100%; max-width: 300px;">
                    <span id="ai-vibe-temperature-value"><?php echo esc_html($options['temperature'] ?? 0.7); ?></span>
                    <small><?php _e('Recommended: 0.7 for balanced creativity', 'ai-vibe-coder'); ?></small>
                </div>

                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-max-tokens"><?php _e('Max Tokens', 'ai-vibe-coder'); ?></label>
                    <input type="number" id="ai-vibe-max-tokens" name="max_tokens" class="regular-text" 
                           value="<?php echo esc_attr($options['max_tokens'] ?? 2000); ?>"
                           min="100" max="16000" step="100">
                    <small><?php _e('Maximum number of tokens for response. Higher values allow longer outputs.', 'ai-vibe-coder'); ?></small>
                </div>
            </div>
        </div>

        <!-- Feature Settings -->
        <div class="ai-vibe-coder-card">
            <div class="ai-vibe-coder-card-header">
                <h2>
                    <span class="dashicons dashicons-admin-plugins" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('Features', 'ai-vibe-coder'); ?>
                </h2>
            </div>

            <div class="ai-vibe-coder-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="enable_shortcodes" value="1" 
                           <?php checked($options['enable_shortcodes'] ?? true, true); ?>>
                    <span><?php _e('Enable Shortcode Generator', 'ai-vibe-coder'); ?></span>
                </label>
                <small style="margin-left: 28px;"><?php _e('Generate custom shortcodes for your site', 'ai-vibe-coder'); ?></small>
            </div>

            <div class="ai-vibe-coder-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="enable_widgets" value="1" 
                           <?php checked($options['enable_widgets'] ?? true, true); ?>>
                    <span><?php _e('Enable Widget Generator', 'ai-vibe-coder'); ?></span>
                </label>
                <small style="margin-left: 28px;"><?php _e('Generate custom widgets for sidebars', 'ai-vibe-coder'); ?></small>
            </div>

            <div class="ai-vibe-coder-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="enable_content" value="1" 
                           <?php checked($options['enable_content'] ?? true, true); ?>>
                    <span><?php _e('Enable Content AI', 'ai-vibe-coder'); ?></span>
                </label>
                <small style="margin-left: 28px;"><?php _e('Generate and improve content with AI', 'ai-vibe-coder'); ?></small>
            </div>

            <div class="ai-vibe-coder-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="enable_functions" value="1" 
                           <?php checked($options['enable_functions'] ?? true, true); ?>>
                    <span><?php _e('Enable Function Generator', 'ai-vibe-coder'); ?></span>
                </label>
                <small style="margin-left: 28px;"><?php _e('Generate custom PHP functions and hooks', 'ai-vibe-coder'); ?></small>
            </div>
        </div>

        <!-- Saving Options -->
        <div class="ai-vibe-coder-card">
            <div class="ai-vibe-coder-card-header">
                <h2>
                    <span class="dashicons dashicons-save" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('Auto-Save', 'ai-vibe-coder'); ?>
                </h2>
            </div>

            <div class="ai-vibe-coder-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="auto_save" value="1" 
                           <?php checked($options['auto_save'] ?? true, true); ?>>
                    <span><?php _e('Auto-save generated code', 'ai-vibe-coder'); ?></span>
                </label>
                <small style="margin-left: 28px;"><?php _e('Automatically save generated code to snippets library', 'ai-vibe-coder'); ?></small>
            </div>
        </div>

        <!-- Save Button -->
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="submit" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary ai-vibe-coder-settings-save">
                <span class="dashicons dashicons-saved" style="margin-top: 3px;"></span>
                <?php _e('Save Settings', 'ai-vibe-coder'); ?>
            </button>
        </div>
    </form>

    <!-- Help Section -->
    <div class="ai-vibe-coder-card" style="margin-top: 30px;">
        <div class="ai-vibe-coder-card-header">
            <h2>
                <span class="dashicons dashicons-info" style="font-size: 20px; width: 20px; height: 20px;"></span>
                <?php _e('Getting Help', 'ai-vibe-coder'); ?>
            </h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <h4 style="color: #2271b1; margin: 0 0 10px 0;">
                    <span class="dashicons dashicons-book" style="font-size: 16px; width: 16px; height: 16px;"></span>
                    <?php _e('Documentation', 'ai-vibe-coder'); ?>
                </h4>
                <p style="margin: 0; color: #646970; font-size: 14px;">
                    <?php _e('Check our documentation for detailed guides and tutorials.', 'ai-vibe-coder'); ?>
                </p>
            </div>
            <div>
                <h4 style="color: #2271b1; margin: 0 0 10px 0;">
                    <span class="dashicons dashicons-admin-users" style="font-size: 16px; width: 16px; height: 16px;"></span>
                    <?php _e('Community', 'ai-vibe-coder'); ?>
                </h4>
                <p style="margin: 0; color: #646970; font-size: 14px;">
                    <?php _e('Join our community forum to connect with other users.', 'ai-vibe-coder'); ?>
                </p>
            </div>
            <div>
                <h4 style="color: #2271b1; margin: 0 0 10px 0;">
                    <span class="dashicons dashicons-email" style="font-size: 16px; width: 16px; height: 16px;"></span>
                    <?php _e('Support', 'ai-vibe-coder'); ?>
                </h4>
                <p style="margin: 0; color: #646970; font-size: 14px;">
                    <?php _e('Need help? Contact our support team.', 'ai-vibe-coder'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Temperature slider
    $('#ai-vibe-temperature').on('input', function() {
        $('#ai-vibe-temperature-value').text($(this).val());
    });

    // Provider change - update models
    $('#ai-vibe-api-provider').on('change', function() {
        var provider = $(this).val();
        var models = <?php echo json_encode($api->get_available_models()); ?>;
        var $modelSelect = $('#ai-vibe-model');
        
        $modelSelect.empty();
        
        if (models[provider]) {
            $.each(models[provider], function(key, label) {
                $modelSelect.append($('<option></option>').val(key).text(label));
            });
        }
    });
});
</script>
