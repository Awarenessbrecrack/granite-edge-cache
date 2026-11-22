<?php
/**
 * Uninstall routine.
 *
 * @package Granite Edge Cache
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

foreach (array('gec_stats', 'gec_stats_url', 'gec_report_at', 'gec_install_at', 'gec_settings', 'gec_consent', 'gec_consent_secrets', 'gec_report_v', 'gec_touch', 'gec_metrics_due') as $option) {
    delete_option($option);
}

foreach (array('gec_seen', 'gec_due', 'gec_sent', 'gec_since', 'gec_env_check', 'gec_cloud', 'gec_policy') as $key) {
    delete_transient($key);
}

foreach (array('gec_cache_expire', 'gec_cache_preload', 'gec_db_maintenance') as $hook) {
    wp_clear_scheduled_hook($hook);
}

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'granite-edge-cache_redirects');
delete_option('gec_redirects_db');

$dir = WP_CONTENT_DIR . '/cache/granite-edge-cache';

foreach ((array) glob($dir . '/*') as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}

if (is_dir($dir)) {
    @rmdir($dir);
}
