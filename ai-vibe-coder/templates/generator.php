<div class="ai-vibe-coder-wrap">
    <div class="ai-vibe-coder-header">
        <h1>
            <span class="dashicons dashicons-editor-code"></span>
            <?php _e('Code Generator', 'ai-vibe-coder'); ?>
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
                    <h2><?php _e('Describe What You Need', 'ai-vibe-coder'); ?></h2>
                </div>

                <!-- Code Type Selector -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-code-type">
                        <?php _e('Code Type', 'ai-vibe-coder'); ?>
                    </label>
                    <div class="ai-vibe-coder-type-selector">
                        <?php
                        $code_types = array(
                            'shortcode' => __('Shortcode', 'ai-vibe-coder'),
                            'widget' => __('Widget', 'ai-vibe-coder'),
                            'function' => __('Function', 'ai-vibe-coder'),
                            'css' => __('CSS', 'ai-vibe-coder'),
                            'javascript' => __('JavaScript', 'ai-vibe-coder'),
                            'post_type' => __('Post Type', 'ai-vibe-coder'),
                            'taxonomy' => __('Taxonomy', 'ai-vibe-coder'),
                        );
                        foreach ($code_types as $type => $label):
                            $active = $type === 'shortcode' ? 'active' : '';
                        ?>
                            <button type="button" class="ai-vibe-coder-type-btn <?php echo $active; ?>" data-type="<?php echo esc_attr($type); ?>">
                                <?php echo esc_html($label); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Prompt Input -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-prompt">
                        <?php _e('Your Requirements', 'ai-vibe-coder'); ?>
                        <span class="ai-vibe-coder-help-tip" title="<?php _e('Describe what you want the code to do in detail', 'ai-vibe-coder'); ?>">?</span>
                    </label>
                    <textarea id="ai-vibe-prompt" class="ai-vibe-coder-input ai-vibe-coder-textarea" 
                              placeholder="<?php _e('e.g., Create a shortcode that displays a contact form with name, email, and message fields...', 'ai-vibe-coder'); ?>"></textarea>
                </div>

                <!-- Context Input -->
                <div class="ai-vibe-coder-form-group">
                    <label for="ai-vibe-context">
                        <?php _e('Additional Context (Optional)', 'ai-vibe-coder'); ?>
                    </label>
                    <textarea id="ai-vibe-context" class="ai-vibe-coder-input ai-vibe-coder-textarea" 
                              placeholder="<?php _e('Any additional information, specific requirements, or constraints...', 'ai-vibe-coder'); ?>"></textarea>
                </div>

                <!-- Generate Button -->
                <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary ai-vibe-coder-generate-btn" style="width: 100%;">
                    <span class="dashicons dashicons-superhero"></span>
                    <?php _e('Generate Code with AI', 'ai-vibe-coder'); ?>
                </button>

                <!-- Example Prompts -->
                <div class="ai-vibe-coder-prompts">
                    <p style="font-size: 12px; color: #646970; margin-bottom: 5px;">
                        <strong><?php _e('Try these examples:', 'ai-vibe-coder'); ?></strong>
                    </p>
                    <div class="ai-vibe-coder-prompt-item" data-prompt="<?php _e('Create a shortcode that displays a responsive image gallery with lightbox effect', 'ai-vibe-coder'); ?>">
                        <?php _e('Image gallery with lightbox', 'ai-vibe-coder'); ?>
                    </div>
                    <div class="ai-vibe-coder-prompt-item" data-prompt="<?php _e('Create a widget that shows social media icons with hover animations', 'ai-vibe-coder'); ?>">
                        <?php _e('Social media widget', 'ai-vibe-coder'); ?>
                    </div>
                    <div class="ai-vibe-coder-prompt-item" data-prompt="<?php _e('Create a custom post type for portfolio items with categories', 'ai-vibe-coder'); ?>">
                        <?php _e('Portfolio post type', 'ai-vibe-coder'); ?>
                    </div>
                    <div class="ai-vibe-coder-prompt-item" data-prompt="<?php _e('Create CSS for a modern button with gradient and shadow', 'ai-vibe-coder'); ?>">
                        <?php _e('Modern CSS button', 'ai-vibe-coder'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Output Column -->
        <div class="ai-vibe-coder-column">
            <div class="ai-vibe-coder-card">
                <div class="ai-vibe-coder-card-header">
                    <h2><?php _e('Generated Code', 'ai-vibe-coder'); ?></h2>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="button button-secondary ai-vibe-coder-copy-btn" title="<?php _e('Copy to clipboard', 'ai-vibe-coder'); ?>">
                            <span class="dashicons dashicons-admin-page" style="margin-top: 3px;"></span>
                            <?php _e('Copy', 'ai-vibe-coder'); ?>
                        </button>
                    </div>
                </div>

                <!-- Code Output -->
                <div style="position: relative;">
                    <textarea id="ai-vibe-code-output" class="ai-vibe-coder-code-editor" 
                              placeholder="<?php _e('Your generated code will appear here...', 'ai-vibe-coder'); ?>"></textarea>
                </div>

                <!-- Explanation -->
                <div id="ai-vibe-explanation" style="display: none;">
                    <div class="ai-vibe-coder-explanation">
                        <h4><?php _e('Explanation', 'ai-vibe-coder'); ?></h4>
                        <p id="ai-vibe-explanation-text"></p>
                    </div>
                </div>

                <!-- Save Snippet Form -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                    <h3 style="margin: 0 0 15px 0; font-size: 16px;">
                        <span class="dashicons dashicons-save" style="font-size: 18px; width: 18px; height: 18px;"></span>
                        <?php _e('Save as Snippet', 'ai-vibe-coder'); ?>
                    </h3>
                    
                    <div class="ai-vibe-coder-form-group" style="margin-bottom: 15px;">
                        <label for="ai-vibe-snippet-name"><?php _e('Name', 'ai-vibe-coder'); ?></label>
                        <input type="text" id="ai-vibe-snippet-name" class="regular-text" 
                               placeholder="<?php _e('e.g., Contact Form Shortcode', 'ai-vibe-coder'); ?>">
                    </div>
                    
                    <div class="ai-vibe-coder-form-group" style="margin-bottom: 15px;">
                        <label for="ai-vibe-snippet-description"><?php _e('Description', 'ai-vibe-coder'); ?></label>
                        <input type="text" id="ai-vibe-snippet-description" class="regular-text" 
                               placeholder="<?php _e('Brief description of what this code does', 'ai-vibe-coder'); ?>">
                    </div>
                    
                    <div class="ai-vibe-coder-form-group" style="margin-bottom: 15px;">
                        <label for="ai-vibe-snippet-tags"><?php _e('Tags', 'ai-vibe-coder'); ?></label>
                        <input type="text" id="ai-vibe-snippet-tags" class="regular-text" 
                               placeholder="<?php _e('comma, separated, tags', 'ai-vibe-coder'); ?>">
                    </div>
                    
                    <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-success ai-vibe-coder-save-btn">
                        <span class="dashicons dashicons-saved" style="margin-top: 3px;"></span>
                        <?php _e('Save Snippet', 'ai-vibe-coder'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Initialize type selector
    $('.ai-vibe-coder-type-btn').on('click', function() {
        $('.ai-vibe-coder-type-btn').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
