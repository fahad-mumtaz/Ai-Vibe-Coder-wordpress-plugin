<?php
/**
 * Uninstall script for AI Vibe Coder
 * Cleans up all plugin data when uninstalled
 */

// If not uninstalling, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('ai_vibe_coder_options');

// Drop the snippets table
global $wpdb;
$table_name = $wpdb->prefix . 'ai_vibe_snippets';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

// Clear any cached data
wp_cache_flush();
