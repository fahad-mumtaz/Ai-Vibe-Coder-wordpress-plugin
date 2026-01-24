<div class="ai-vibe-coder-wrap">
    <div class="ai-vibe-coder-header">
        <h1>
            <span class="dashicons dashicons-pressthis"></span>
            <?php _e('Content AI', 'ai-vibe-coder'); ?>
        </h1>
        <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder'); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="margin-top: 3px;"></span>
            <?php _e('Back to Dashboard', 'ai-vibe-coder'); ?>
        </a>
    </div>

    <div class="ai-vibe-coder-two-col">
        <!-- Input Column -->
        <div class="ai-vibe-coder-column">
            <div class="ai-vibe-coder-card">
                <div class="ai-vibe-coder-card-header">
                    <h2><?php _e('Generate Content', 'ai-vibe-coder'); ?></h2>
                </div>

                <!-- Content Type Selector -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-content-type">
                        <?php _e('Content Type', 'ai-vibe-coder'); ?>
                    </label>
                    <select id="ai-vibe-content-type" class="ai-vibe-coder-input ai-vibe-coder-select">
                        <?php
                        $content_types = AI_Vibe_Coder_Content_Generator::get_instance()->get_content_types();
                        foreach ($content_types as $type => $label):
                        ?>
                            <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Title Input (optional) -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-content-title">
                        <?php _e('Title (Optional)', 'ai-vibe-coder'); ?>
                    </label>
                    <input type="text" id="ai-vibe-content-title" class="regular-text" 
                           placeholder="<?php _e('Enter a title for the content', 'ai-vibe-coder'); ?>">
                </div>

                <!-- Prompt Input -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-content-prompt">
                        <?php _e('What should the content be about?', 'ai-vibe-coder'); ?>
                    </label>
                    <textarea id="ai-vibe-content-prompt" class="ai-vibe-coder-input ai-vibe-coder-textarea" 
                              placeholder="<?php _e('Describe the content you want to generate...', 'ai-vibe-coder'); ?>"></textarea>
                </div>

                <!-- Generate Button -->
                <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary ai-vibe-coder-generate-content-btn" style="width: 100%;">
                    <span class="dashicons dashicons-superhero"></span>
                    <?php _e('Generate Content with AI', 'ai-vibe-coder'); ?>
                </button>

                <!-- Example Prompts -->
                <div id="ai-vibe-content-prompts-container">
                    <p style="font-size: 12px; color: #646970; margin-bottom: 5px; margin-top: 20px;">
                        <strong><?php _e('Try these examples:', 'ai-vibe-coder'); ?></strong>
                    </p>
                    <div class="ai-vibe-coder-example-pills">
                        <span class="ai-vibe-coder-pill" data-prompt="<?php _e('The benefits of using AI in web development', 'ai-vibe-coder'); ?>">
                            <?php _e('AI in web development', 'ai-vibe-coder'); ?>
                        </span>
                        <span class="ai-vibe-coder-pill" data-prompt="<?php _e('How to improve WordPress site performance', 'ai-vibe-coder'); ?>">
                            <?php _e('WP performance tips', 'ai-vibe-coder'); ?>
                        </span>
                        <span class="ai-vibe-coder-pill" data-prompt="<?php _e('10 tips for better content marketing', 'ai-vibe-coder'); ?>">
                            <?php _e('Content marketing tips', 'ai-vibe-coder'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Output Column -->
        <div class="ai-vibe-coder-column">
            <div class="ai-vibe-coder-card">
                <div class="ai-vibe-coder-card-header">
                    <h2><?php _e('Generated Content', 'ai-vibe-coder'); ?></h2>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="button button-secondary" id="ai-vibe-copy-content" title="<?php _e('Copy to clipboard', 'ai-vibe-coder'); ?>">
                            <span class="dashicons dashicons-admin-page" style="margin-top: 3px;"></span>
                            <?php _e('Copy', 'ai-vibe-coder'); ?>
                        </button>
                    </div>
                </div>

                <!-- Content Output -->
                <div class="ai-vibe-coder-form-group">
                    <textarea id="ai-vibe-content-output" class="ai-vibe-coder-input ai-vibe-coder-textarea" 
                              style="min-height: 400px;"
                              placeholder="<?php _e('Your generated content will appear here...', 'ai-vibe-coder'); ?>"></textarea>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-success ai-vibe-coder-save-draft-btn">
                        <span class="dashicons dashicons-saved" style="margin-top: 3px;"></span>
                        <?php _e('Save as Draft', 'ai-vibe-coder'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="ai-vibe-preview-content">
                        <span class="dashicons dashicons-visibility" style="margin-top: 3px;"></span>
                        <?php _e('Preview', 'ai-vibe-coder'); ?>
                    </button>
                </div>
            </div>

            <!-- Content Tips -->
            <div class="ai-vibe-coder-card">
                <div class="ai-vibe-coder-card-header">
                    <h2><?php _e('Tips for Better Results', 'ai-vibe-coder'); ?></h2>
                </div>
                <ul style="margin: 0; padding-left: 20px; color: #646970; line-height: 1.8;">
                    <li><?php _e('Be specific about your topic and target audience', 'ai-vibe-coder'); ?></li>
                    <li><?php _e('Include keywords you want to target for SEO', 'ai-vibe-coder'); ?></li>
                    <li><?php _e('Specify the tone you want (professional, casual, technical)', 'ai-vibe-coder'); ?></li>
                    <li><?php _e('Mention any specific points you want covered', 'ai-vibe-coder'); ?></li>
                    <li><?php _e('Specify the desired length if important', 'ai-vibe-coder'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
