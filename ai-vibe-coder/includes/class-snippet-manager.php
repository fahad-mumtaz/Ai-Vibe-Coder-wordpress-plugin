<?php
/**
 * Snippet Manager Class
 * Manages saved code snippets
 */

class AI_Vibe_Coder_Snippet_Manager {

    private static $instance = null;
    private $table_name;

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
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ai_vibe_snippets';
    }

    /**
     * Save a new snippet
     */
    public function save($name, $type, $code, $description = '', $tags = '') {
        global $wpdb;

        $data = array(
            'name' => $name,
            'type' => $type,
            'code' => $code,
            'description' => $description,
            'tags' => $tags,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );

        $format = array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        );

        $result = $wpdb->insert($this->table_name, $data, $format);

        if ($result === false) {
            return new WP_Error('db_error', __('Failed to save snippet', 'ai-vibe-coder'));
        }

        return $wpdb->insert_id;
    }

    /**
     * Update existing snippet
     */
    public function update($id, $data) {
        global $wpdb;

        $data['updated_at'] = current_time('mysql');

        $where = array('id' => $id);

        $result = $wpdb->update($this->table_name, $data, $where);

        if ($result === false) {
            return new WP_Error('db_error', __('Failed to update snippet', 'ai-vibe-coder'));
        }

        return true;
    }

    /**
     * Get snippet by ID
     */
    public function get($id) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id);
        $result = $wpdb->get_row($sql, ARRAY_A);

        if ($result === null) {
            return new WP_Error('not_found', __('Snippet not found', 'ai-vibe-coder'));
        }

        return $result;
    }

    /**
     * Get all snippets
     */
    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'type' => '',
            'deployed' => null,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 50,
            'offset' => 0,
        );

        $args = wp_parse_args($args, $defaults);

        $where = array();
        $format = array();

        if (!empty($args['type'])) {
            $where[] = 'type = %s';
            $format[] = $args['type'];
        }

        if ($args['deployed'] !== null) {
            $where[] = 'deployed = %d';
            $format[] = $args['deployed'] ? 1 : 0;
        }

        $where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $order_clause = sanitize_sql_orderby("{$args['orderby']} {$args['order']}");
        $limit_clause = $wpdb->prepare('LIMIT %d, %d', $args['offset'], $args['limit']);

        $sql = "SELECT * FROM {$this->table_name} {$where_clause} ORDER BY {$order_clause} {$limit_clause}";
        $sql = $wpdb->prepare($sql, $format);

        $results = $wpdb->get_results($sql, ARRAY_A);

        return $results ?: array();
    }

    /**
     * Search snippets
     */
    public function search($search_term) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            WHERE name LIKE %s 
            OR description LIKE %s 
            OR tags LIKE %s
            OR code LIKE %s
            ORDER BY created_at DESC",
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%'
        );

        $results = $wpdb->get_results($sql, ARRAY_A);

        return $results ?: array();
    }

    /**
     * Delete snippet
     */
    public function delete($id) {
        global $wpdb;

        $result = $wpdb->delete($this->table_name, array('id' => $id), array('%d'));

        if ($result === false) {
            return new WP_Error('db_error', __('Failed to delete snippet', 'ai-vibe-coder'));
        }

        return true;
    }

    /**
     * Deploy snippet (mark as deployed)
     */
    public function deploy($id) {
        global $wpdb;

        // Get the snippet
        $snippet = $this->get($id);

        if (is_wp_error($snippet)) {
            return $snippet;
        }

        // Execute the code based on type
        $result = $this->execute_snippet($snippet);

        if (is_wp_error($result)) {
            return $result;
        }

        // Mark as deployed
        $this->update($id, array('deployed' => true));

        return true;
    }

    /**
     * Execute snippet code
     */
    private function execute_snippet($snippet) {
        $type = $snippet['type'];
        $code = $snippet['code'];

        // Don't execute CSS or JavaScript snippets this way
        if ($type === 'css' || $type === 'javascript') {
            return new WP_Error('invalid_type', __('CSS and JavaScript snippets should be saved in theme files', 'ai-vibe-coder'));
        }

        // For PHP snippets, we need to be careful
        // We'll return the code for manual review rather than executing
        // This is a security measure
        return $code;
    }

    /**
     * Get snippet count by type
     */
    public function get_count_by_type() {
        global $wpdb;

        $sql = "SELECT type, COUNT(*) as count FROM {$this->table_name} GROUP BY type";
        $results = $wpdb->get_results($sql, ARRAY_A);

        $counts = array();
        foreach ($results as $result) {
            $counts[$result['type']] = intval($result['count']);
        }

        return $counts;
    }

    /**
     * Get total snippet count
     */
    public function get_total_count() {
        global $wpdb;

        return intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"));
    }

    /**
     * Get deployed count
     */
    public function get_deployed_count() {
        global $wpdb;

        return intval($wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE deployed = 1"));
    }

    /**
     * Export snippets
     */
    public function export($ids = array()) {
        global $wpdb;

        if (empty($ids)) {
            $snippets = $this->get_all(array('limit' => 1000));
        } else {
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            $sql = $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id IN ({$placeholders})", $ids);
            $snippets = $wpdb->get_results($sql, ARRAY_A);
        }

        return $snippets;
    }

    /**
     * Import snippets
     */
    public function import($snippets) {
        $imported = array();

        foreach ($snippets as $snippet) {
            // Remove ID to create new entry
            unset($snippet['id']);
            $snippet['created_at'] = current_time('mysql');
            $snippet['updated_at'] = current_time('mysql');
            $snippet['deployed'] = false;

            $result = $this->save(
                $snippet['name'],
                $snippet['type'],
                $snippet['code'],
                $snippet['description'] ?? '',
                $snippet['tags'] ?? ''
            );

            if (!is_wp_error($result)) {
                $imported[] = $result;
            }
        }

        return $imported;
    }

    /**
     * Get recent snippets
     */
    public function get_recent($limit = 5) {
        return $this->get_all(array(
            'limit' => $limit,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ));
    }

    /**
     * Clean up old snippets (optional maintenance)
     */
    public function cleanup($days_old = 365) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "DELETE FROM {$this->table_name} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days_old
        );

        return $wpdb->query($sql);
    }
}
