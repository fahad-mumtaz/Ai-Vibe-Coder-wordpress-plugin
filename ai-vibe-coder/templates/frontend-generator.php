<div class="ai-vibe-coder-container">
    <div class="ai-vibe-coder-card">
        <div class="ai-vibe-coder-card-header">
            <h2>
                <span class="dashicons dashicons-superhero" style="font-size: 24px; width: 24px; height: 24px;"></span>
                <?php _e('AI Code Generator', 'ai-vibe-coder'); ?>
            </h2>
            <p style="margin: 5px 0 0 0; color: #646970; font-size: 14px;">
                <?php _e('Describe what you need and let AI create the code for you!', 'ai-vibe-coder'); ?>
            </p>
        </div>

        <!-- Code Type Selector -->
        <div class="ai-vibe-coder-form-group">
            <label class="ai-vibe-coder-label">
                <span class="dashicons dashicons-menu-alt" style="font-size: 18px; width: 18px; height: 18px;"></span>
                <?php _e('What do you want to create?', 'ai-vibe-coder'); ?>
            </label>
            <div class="ai-vibe-coder-type-selector">
                <?php
                $code_types = array(
                    'shortcode' => __('Shortcode', 'ai-vibe-coder'),
                    'widget' => __('Widget', 'ai-vibe-coder'),
                    'function' => __('Function', 'ai-vibe-coder'),
                    'css' => __('CSS Styles', 'ai-vibe-coder'),
                    'javascript' => __('JavaScript', 'ai-vibe-coder'),
                );
                $first = true;
                foreach ($code_types as $type => $label):
                    $active = $first ? 'active' : '';
                    $first = false;
                ?>
                    <button type="button" class="ai-vibe-coder-type-btn <?php echo $active; ?>" data-type="<?php echo esc_attr($type); ?>">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Prompt Input -->
        <div class="ai-vibe-coder-form-group">
            <label class="ai-vibe-coder-label" for="ai-vibe-frontend-prompt">
                <span class="dashicons dashicons-editor-help" style="font-size: 18px; width: 18px; height: 18px;"></span>
                <?php _e('Describe what you need', 'ai-vibe-coder'); ?>
            </label>
            <textarea id="ai-vibe-frontend-prompt" class="ai-vibe-coder-input ai-vibe-coder-textarea" 
                      placeholder="<?php _e('e.g., Create a contact form shortcode with name, email, and message fields...', 'ai-vibe-coder'); ?>"></textarea>
        </div>

        <!-- Generate Button -->
        <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary ai-vibe-coder-frontend-generate" style="width: 100%;">
            <span class="dashicons dashicons-superhero"></span>
            <?php _e('Generate Code with AI', 'ai-vibe-coder'); ?>
        </button>

        <!-- Example Prompts -->
        <div class="ai-vibe-coder-examples">
            <div class="ai-vibe-coder-examples-title"><?php _e('Try these examples:', 'ai-vibe-coder'); ?></div>
            <div class="ai-vibe-coder-example-pills">
                <span class="ai-vibe-coder-pill" data-prompt="<?php _e('Create a pricing table shortcode', 'ai-vibe-coder'); ?>">
                    <?php _e('Pricing table', 'ai-vibe-coder'); ?>
                </span>
                <span class="ai-vibe-coder-pill" data-prompt="<?php _e('Create a social media icons widget', 'ai-vibe-coder'); ?>">
                    <?php _e('Social icons', 'ai-vibe-coder'); ?>
                </span>
                <span class="ai-vibe-coder-pill" data-prompt="<?php _e('Create a responsive button CSS', 'ai-vibe-coder'); ?>">
                    <?php _e('Responsive button', 'ai-vibe-coder'); ?>
                </span>
                <span class="ai-vibe-coder-pill" data-prompt="<?php _e('Create a smooth scroll JavaScript', 'ai-vibe-coder'); ?>">
                    <?php _e('Smooth scroll', 'ai-vibe-coder'); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="ai-vibe-frontend-loading" class="ai-vibe-coder-card" style="display: none;">
        <div class="ai-vibe-coder-loading">
            <div class="ai-vibe-coder-loading-spinner"></div>
            <div class="ai-vibe-coder-loading-text"><?php _e('AI is thinking...', 'ai-vibe-coder'); ?></div>
            <p style="color: #646970; font-size: 13px; margin-top: 10px;">
                <?php _e('Generating code based on your requirements...', 'ai-vibe-coder'); ?>
            </p>
        </div>
    </div>

    <!-- Result Card -->
    <div id="ai-vibe-frontend-result" class="ai-vibe-coder-card" style="display: none;">
        <div class="ai-vibe-coder-card-header">
            <h2>
                <span class="dashicons dashicons-yes-alt" style="font-size: 24px; width: 24px; height: 24px; color: #00a32a;"></span>
                <?php _e('Your Code is Ready!', 'ai-vibe-coder'); ?>
            </h2>
        </div>

        <!-- Code Output -->
        <div style="position: relative;">
            <button type="button" class="ai-vibe-coder-copy-btn" id="ai-vibe-frontend-copy">
                <span class="dashicons dashicons-admin-page" style="font-size: 14px;"></span>
                <?php _e('Copy', 'ai-vibe-coder'); ?>
            </button>
            <div class="ai-vibe-coder-code-output">
                <pre id="ai-vibe-frontend-code"></pre>
            </div>
        </div>

        <!-- Explanation -->
        <div id="ai-vibe-frontend-explanation" style="display: none;">
            <div class="ai-vibe-coder-explanation">
                <h4><?php _e('How to use this code:', 'ai-vibe-coder'); ?></h4>
                <p id="ai-vibe-explanation-text"></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="ai-vibe-coder-actions">
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-generator'); ?>" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary">
                <span class="dashicons dashicons-admin-tools" style="font-size: 16px;"></span>
                <?php _e('Advanced Editor', 'ai-vibe-coder'); ?>
            </a>
            <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-secondary" id="ai-vibe-frontend-new">
                <span class="dashicons dashicons-plus" style="font-size: 16px;"></span>
                <?php _e('Generate Another', 'ai-vibe-coder'); ?>
            </button>
        </div>
    </div>

    <!-- Error Card -->
    <div id="ai-vibe-frontend-error" class="ai-vibe-coder-card" style="display: none;">
        <div class="ai-vibe-coder-error">
            <h3 style="margin: 0 0 10px 0;">
                <span class="dashicons dashicons-warning" style="font-size: 20px; width: 20px; height: 20px;"></span>
                <?php _e('Something went wrong', 'ai-vibe-coder'); ?>
            </h3>
            <p id="ai-vibe-frontend-error-text" style="margin: 0;"></p>
        </div>
        <div class="ai-vibe-coder-actions">
            <button type="button" class="ai-vibe-coder-btn ai-vibe-coder-btn-secondary" id="ai-vibe-frontend-retry">
                <span class="dashicons dashicons-update" style="font-size: 16px;"></span>
                <?php _e('Try Again', 'ai-vibe-coder'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="ai-vibe-frontend-toast" class="ai-vibe-coder-toast"></div>

<script type="text/javascript">
(function($) {
    'use strict';

    var currentType = 'shortcode';

    // Initialize
    $(document).ready(function() {
        // Type selector
        $('.ai-vibe-coder-type-btn').on('click', function() {
            $('.ai-vibe-coder-type-btn').removeClass('active');
            $(this).addClass('active');
            currentType = $(this).data('type');
        });

        // Example pills
        $('.ai-vibe-coder-pill').on('click', function() {
            $('#ai-vibe-frontend-prompt').val($(this).data('prompt'));
        });

        // Generate button
        $('.ai-vibe-coder-frontend-generate').on('click', generateCode);

        // Copy button
        $('#ai-vibe-frontend-copy').on('click', copyCode);

        // New generation
        $('#ai-vibe-frontend-new').on('click', function() {
            $('#ai-vibe-frontend-result').hide();
            $('#ai-vibe-frontend-prompt').val('').focus();
        });

        // Retry
        $('#ai-vibe-frontend-retry').on('click', function() {
            $('#ai-vibe-frontend-error').hide();
            generateCode();
        });

        // Enter key in textarea
        $('#ai-vibe-frontend-prompt').on('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                generateCode();
            }
        });
    });

    function generateCode() {
        var prompt = $('#ai-vibe-frontend-prompt').val().trim();

        if (!prompt) {
            showToast('Please describe what you need', 'error');
            return;
        }

        // Show loading
        $('#ai-vibe-frontend-loading').show();
        $('#ai-vibe-frontend-result').hide();
        $('#ai-vibe-frontend-error').hide();

        // Make AJAX request
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'ai_vibe_generate_code',
                nonce: '<?php echo wp_create_nonce('ai_vibe_coder_nonce'); ?>',
                prompt: prompt,
                type: currentType,
                context: ''
            },
            success: function(response) {
                $('#ai-vibe-frontend-loading').hide();

                if (response.success) {
                    var result = response.data;
                    $('#ai-vibe-frontend-code').text(result.code);
                    
                    if (result.explanation) {
                        $('#ai-vibe-explanation-text').text(result.explanation);
                        $('#ai-vibe-frontend-explanation').show();
                    } else {
                        $('#ai-vibe-frontend-explanation').hide();
                    }
                    
                    $('#ai-vibe-frontend-result').show();
                    showToast('Code generated successfully!', 'success');
                } else {
                    $('#ai-vibe-frontend-error-text').text(response.data);
                    $('#ai-vibe-frontend-error').show();
                }
            },
            error: function() {
                $('#ai-vibe-frontend-loading').hide();
                $('#ai-vibe-frontend-error-text').text('An error occurred. Please try again.');
                $('#ai-vibe-frontend-error').show();
            }
        });
    }

    function copyCode() {
        var code = $('#ai-vibe-frontend-code').text();
        
        navigator.clipboard.writeText(code).then(function() {
            showToast('Code copied to clipboard!', 'success');
        }).catch(function() {
            // Fallback
            var textarea = document.createElement('textarea');
            textarea.value = code;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast('Code copied to clipboard!', 'success');
        });
    }

    function showToast(message, type) {
        type = type || 'info';
        var $toast = $('#ai-vibe-frontend-toast');
        
        $toast.text(message)
               .removeClass('success error')
               .addClass(type)
               .addClass('show');
        
        setTimeout(function() {
            $toast.removeClass('show');
        }, 3000);
    }
})(jQuery);
</script>
