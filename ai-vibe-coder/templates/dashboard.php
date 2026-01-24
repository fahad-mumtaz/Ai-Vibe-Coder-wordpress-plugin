<div class="ai-vibe-coder-wrap">
    <div class="ai-vibe-coder-header">
        <h1>
            <span class="dashicons dashicons-code-standards"></span>
            <?php _e('AI Vibe Coder', 'ai-vibe-coder'); ?>
        </h1>
        <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-settings'); ?>" class="button">
            <span class="dashicons dashicons-admin-settings" style="margin-top: 3px;"></span>
            <?php _e('Settings', 'ai-vibe-coder'); ?>
        </a>
    </div>

    <?php
    // Get stats
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_vibe_snippets';
    $total_snippets = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $deployed_snippets = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE deployed = 1");

    // Check API configuration
    $api = AI_Vibe_Coder_API::get_instance();
    $api_configured = $api->is_configured();
    ?>

    <!-- Stats Cards -->
    <div class="ai-vibe-coder-stats">
        <div class="ai-vibe-coder-stat">
            <div class="ai-vibe-coder-stat-number"><?php echo esc_html($total_snippets); ?></div>
            <div class="ai-vibe-coder-stat-label"><?php _e('Total Snippets', 'ai-vibe-coder'); ?></div>
        </div>
        <div class="ai-vibe-coder-stat">
            <div class="ai-vibe-coder-stat-number"><?php echo esc_html($deployed_snippets); ?></div>
            <div class="ai-vibe-coder-stat-label"><?php _e('Deployed', 'ai-vibe-coder'); ?></div>
        </div>
        <div class="ai-vibe-coder-stat">
            <div class="ai-vibe-coder-stat-number"><?php echo $api_configured ? '✓' : '✗'; ?></div>
            <div class="ai-vibe-coder-stat-label"><?php _e('API Status', 'ai-vibe-coder'); ?></div>
        </div>
        <div class="ai-vibe-coder-stat">
            <div class="ai-vibe-coder-stat-number"><?php echo esc_html($api->get_provider()); ?></div>
            <div class="ai-vibe-coder-stat-label"><?php _e('Provider', 'ai-vibe-coder'); ?></div>
        </div>
    </div>

    <?php if (!$api_configured): ?>
        <div class="ai-vibe-coder-alert ai-vibe-coder-alert-warning">
            <strong><?php _e('API Key Not Configured', 'ai-vibe-coder'); ?></strong>
            <?php _e('To use AI Vibe Coder, you need to configure your API key in the Settings page.', 'ai-vibe-coder'); ?>
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-settings'); ?>">
                <?php _e('Go to Settings', 'ai-vibe-coder'); ?>
            </a>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="ai-vibe-coder-card">
        <div class="ai-vibe-coder-card-header">
            <h2><?php _e('Quick Actions', 'ai-vibe-coder'); ?></h2>
        </div>
        <div class="ai-vibe-coder-quick-actions">
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-generator'); ?>" class="ai-vibe-coder-action-card">
                <span class="dashicons dashicons-editor-code"></span>
                <h3><?php _e('Code Generator', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('Generate WordPress code using AI', 'ai-vibe-coder'); ?></p>
            </a>
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-content'); ?>" class="ai-vibe-coder-action-card">
                <span class="dashicons dashicons-pressthis"></span>
                <h3><?php _e('Content AI', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('Generate content with AI assistance', 'ai-vibe-coder'); ?></p>
            </a>
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-snippets'); ?>" class="ai-vibe-coder-action-card">
                <span class="dashicons dashicons-database"></span>
                <h3><?php _e('My Snippets', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('View and manage saved code', 'ai-vibe-coder'); ?></p>
            </a>
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-settings'); ?>" class="ai-vibe-coder-action-card">
                <span class="dashicons dashicons-admin-generic"></span>
                <h3><?php _e('Settings', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('Configure API and options', 'ai-vibe-coder'); ?></p>
            </a>
        </div>
    </div>

    <!-- Recent Snippets -->
    <div class="ai-vibe-coder-card">
        <div class="ai-vibe-coder-card-header">
            <h2><?php _e('Recent Snippets', 'ai-vibe-coder'); ?></h2>
            <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-snippets'); ?>" class="button button-secondary">
                <?php _e('View All', 'ai-vibe-coder'); ?>
            </a>
        </div>
        
        <?php
        $snippet_manager = AI_Vibe_Coder_Snippet_Manager::get_instance();
        $recent_snippets = $snippet_manager->get_recent(5);
        ?>

        <?php if (empty($recent_snippets)): ?>
            <div class="ai-vibe-coder-empty">
                <span class="dashicons dashicons-database"></span>
                <h3><?php _e('No Snippets Yet', 'ai-vibe-coder'); ?></h3>
                <p><?php _e('Start generating code to build your library!', 'ai-vibe-coder'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=ai-vibe-coder-generator'); ?>" class="ai-vibe-coder-btn ai-vibe-coder-btn-primary">
                    <?php _e('Start Coding', 'ai-vibe-coder'); ?>
                </a>
            </div>
        <?php else: ?>
            <table class="ai-vibe-coder-table">
                <thead>
                    <tr>
                        <th><?php _e('Name', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Type', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Status', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Created', 'ai-vibe-coder'); ?></th>
                        <th><?php _e('Actions', 'ai-vibe-coder'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_snippets as $snippet): ?>
                        <tr>
                            <td><strong><?php echo esc_html($snippet['name']); ?></strong></td>
                            <td>
                                <span class="ai-vibe-coder-badge ai-vibe-coder-badge-<?php echo esc_attr($snippet['type']); ?>">
                                    <?php echo esc_html($snippet['type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="ai-vibe-coder-badge <?php echo $snippet['deployed'] ? 'ai-vibe-coder-badge-deployed' : 'ai-vibe-coder-badge-not-deployed'; ?>">
                                    <?php echo $snippet['deployed'] ? __('Deployed', 'ai-vibe-coder') : __('Not Deployed', 'ai-vibe-coder'); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($snippet['created_at']); ?></td>
                            <td>
                                <button class="button button-secondary button-sm ai-vibe-coder-deploy-btn" data-id="<?php echo esc_attr($snippet['id']); ?>">
                                    <?php _e('Deploy', 'ai-vibe-coder'); ?>
                                </button>
                                <button class="button button-secondary button-sm ai-vibe-coder-delete-btn" data-id="<?php echo esc_attr($snippet['id']); ?>">
                                    <?php _e('Delete', 'ai-vibe-coder'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Help Section -->
    <div class="ai-vibe-coder-card">
        <div class="ai-vibe-coder-card-header">
            <h2><?php _e('How to Use', 'ai-vibe-coder'); ?></h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <h3 style="margin-top: 0; color: #2271b1;">
                    <span class="dashicons dashicons-editor-code" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('1. Generate Code', 'ai-vibe-coder'); ?>
                </h3>
                <p style="color: #646970; font-size: 14px; line-height: 1.6;">
                    <?php _e('Describe what you need in plain English. The AI will generate WordPress code tailored to your requirements.', 'ai-vibe-coder'); ?>
                </p>
            </div>
            <div>
                <h3 style="margin-top: 0; color: #2271b1;">
                    <span class="dashicons dashicons-save" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('2. Save Snippet', 'ai-vibe-coder'); ?>
                </h3>
                <p style="color: #646970; font-size: 14px; line-height: 1.6;">
                    <?php _e('Save your generated code to the snippets library for future use. Organize with names and descriptions.', 'ai-vibe-coder'); ?>
                </p>
            </div>
            <div>
                <h3 style="margin-top: 0; color: #2271b1;">
                    <span class="dashicons dashicons-controls-play" style="font-size: 20px; width: 20px; height: 20px;"></span>
                    <?php _e('3. Deploy', 'ai-vibe-coder'); ?>
                </h3>
                <p style="color: #646970; font-size: 14px; line-height: 1.6;">
                    <?php _e('Deploy your code to your WordPress site. Generated shortcodes and widgets can be used immediately.', 'ai-vibe-coder'); ?>
                </p>
            </div>
        </div>
    </div>
</div>
