<div class="ai-vibe-coder-wrap">
    <div class="ai-vibe-coder-header">
        <h1>
            <span class="dashicons dashicons-database"></span>
            <?php _e('My Snippets', 'ai-vibe-coder'); ?>
        </h1>
        <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder'); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="margin-top: 3px;"></span>
            <?php _e('Back to Dashboard', 'ai-vibe-coder'); ?>
        </a>
    </div>

    <div class="ai-vibe-coder-card">
        <div class="ai-vibe-coder-card-header">
            <h2><?php _e('Saved Code Snippets', 'ai-vibe-coder'); ?></h2>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" class="regular-text ai-vibe-coder-search" 
                       placeholder="<?php _e('Search snippets...', 'ai-vibe-coder'); ?>">
                <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-generator'); ?>" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary">
                    <span class="dashicons dashicons-plus" style="margin-top: 3px;"></span>
                    <?php _e('New Snippet', 'ai-vibe-coder'); ?>
                </a>
            </div>
        </div>

        <?php
        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $snippets = $snippet_manager->get_all();
        ?>

        <?php if (empty($snippets)): ?>
            <div class="ai-vibe-coder-empty">
                <span class="dashicons dashicons-database"></span>
                <h3><?php _e('No Snippets Yet', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('Generate some code and save it to build your library!', 'ai-vibe-coder'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-generator'); ?>" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary">
                    <?php _e('Generate Your First Snippet', 'ai-vibe-coder'); ?>
                </a>
            </div>
        <?php else: ?>
            <table class="ai-vibe-coder-table" id="ai-vibe-snippets-table">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Type', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Description', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Tags', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Status', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Created', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Actions', 'ai-vibe-coder'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($snippets as $snippet): ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($snippet['name']); ?></strong>
                            </td>
                            <td>
                                <span class="ai-vibe-coder-badge ai-vibe-coder-badge-<?php echo esc_attr($snippet['type']); ?>">
                                    <?php echo esc_html($snippet['type']); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($snippet['description']); ?></td>
                            <td>
                                <?php
                                $tags = explode(',', $snippet['tags']);
                                $tags = array_filter(array_map('trim', $tags));
                                foreach ($tags as $tag):
                                ?>
                                    <span style="display: inline-block; background: #e0e0e0; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin: 2px;">
                                        <?php echo esc_html($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <span class="ai-vibe-coder-badge <?php echo $snippet['deployed'] ? 'ai-vibe-coder-badge-deployed' : 'ai-vibe-coder-badge-not-deployed'; ?>">
                                    <?php echo $snippet['deployed'] ? __('Deployed', 'ai-vibe-coder') : __('Not Deployed', 'ai-vibe-coder'); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($snippet['created_at']); ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <button class="button button-secondary button-sm ai-vibe-coder-view-btn" 
                                            data-id="<?php echo esc_attr($snippet['id']); ?>"
                                            data-name="<?php echo esc_attr($snippet['name']); ?>"
                                            data-code="<?php echo esc_attr($snippet['code']); ?>"
                                            data-type="<?php echo esc_attr($snippet['type']); ?>">
                                        <span class="dashicons dashicons-visibility" style="margin-top: 3px;"></span>
                                    </button>
                                    <button class="button button-secondary button-sm ai-vibe-coder-deploy-btn" 
                                            data-id="<?php echo esc_attr($snippet['id']); ?>"
                                            title="<?php _e('Deploy to your site', 'ai-vibe-coder'); ?>">
                                        <span class="dashicons dashicons-controls-play" style="margin-top: 3px;"></span>
                                    </button>
                                    <button class="button button-danger button-sm ai-vibe-coder-delete-btn" 
                                            data-id="<?php echo esc_attr($snippet['id']); ?>"
                                            title="<?php _e('Delete snippet', 'ai-vibe-coder'); ?>">
                                        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- View Snippet Modal -->
<div id="ai-vibe-snippet-modal" class="ai-vibe-coder-modal-overlay">
    <div class="ai-vibe-coder-modal" style="max-width: 800px;">
        <div class="ai-vibe-coder-modal-header">
            <h3 id="modal-snippet-name"><?php _e('Snippet Details', 'ai-vibe-coder'); ?></h3>
            <button class="ai-vibe-coder-modal-close">&times;</button>
        </div>
        <div class="ai-vibe-coder-modal-body">
            <div class="ai-vibe-coder-form-group">
                <label><?php _e('Type', 'ai-vibe-coder'); ?></label>
                <span id="modal-snippet-type" class="ai-vibe-coder-badge"></span>
            </div>
            <div class="ai-vibe-coder-form-group">
                <label><?php _e('Code', 'ai-vibe-coder'); ?></label>
                <div class="ai-vibe-coder-code-display">
                    <pre id="modal-snippet-code"></pre>
                </div>
            </div>
        </div>
        <div class="ai-vibe-coder-modal-footer">
            <button class="button ai-vibe-coder-modal-close"><?php _e('Close', 'ai-vibe-coder'); ?></button>
            <button class="button button-secondary" id="modal-copy-btn">
                <span class="dashicons dashicons-admin-page" style="margin-top: 3px;"></span>
                <?php _e('Copy Code', 'ai-vibe-coder'); ?>
            </button>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // View snippet modal
    $('.ai-vibe-coder-view-btn').on('click', function() {
        var btn = $(this);
        var modal = $('#ai-vibe-snippet-modal');
        
        $('#modal-snippet-name').text(btn.data('name'));
        $('#modal-snippet-type').text(btn.data('type')).removeClass().addClass('ai-vibe-coder-badge ai-vibe-coder-badge-' + btn.data('type'));
        $('#modal-snippet-code').text(btn.data('code'));
        
        modal.addClass('active');
    });

    // Close modal
    $('.ai-vibe-coder-modal-close').on('click', function() {
        $('#ai-vibe-snippet-modal').removeClass('active');
    });

    // Close on overlay click
    $('#ai-vibe-snippet-modal').on('click', function(e) {
        if ($(e.target).is('#ai-vibe-snippet-modal')) {
            $(this).removeClass('active');
        }
    });

    // Copy from modal
    $('#modal-copy-btn').on('click', function() {
        var code = $('#modal-snippet-code').text();
        navigator.clipboard.writeText(code).then(function() {
            alert('Code copied to clipboard!');
        });
    });
});
</script>
