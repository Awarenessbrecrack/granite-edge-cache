<?php
/**
 * Settings screen.
 *
 * @package Granite Edge Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

$s     = gec_settings();
$stats = gec_stats();
$total = (int) $stats['hits'] + (int) $stats['misses'];
$rate  = $total ? round(((int) $stats['hits'] / $total) * 100) : 0;
$cloud = get_transient('gec_cloud');
$sizes = GEC_DB::sizes();
$sys   = gec_system_info();
$last  = (int) get_option('gec_report_at', 0);
?>
<div class="wrap granite-edge-cache-wrap">
    <div class="gec-hero">
        <div class="gec-brand">
            <span class="gec-mark">
                <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l9 4v6c0 5-4 8-9 10-5-2-9-5-9-10V6z" fill="currentColor"/></svg>
            </span>
            <div>
                <strong>Granite Edge Cache</strong>
                <span>v<?php echo esc_html(GEC_VER); ?> &middot; cache, SEO, benchmark </span>
            </div>
        </div>
        <div class="gec-pills">
            <span class="gec-pill <?php echo gec_setting('cache_ttl') ? 'is-on' : ''; ?>"><?php esc_html_e('Page cache', 'granite-edge-cache'); ?></span>
            <span class="gec-pill <?php echo gec_setting('html_minify') ? 'is-on' : ''; ?>"><?php esc_html_e('Minify', 'granite-edge-cache'); ?></span>
            <span class="gec-pill <?php echo (gec_setting('webp') && function_exists('imagewebp')) ? 'is-on' : ''; ?>"><?php esc_html_e('WebP', 'granite-edge-cache'); ?></span>
            <span class="gec-pill <?php echo gec_setting('sitemap') ? 'is-on' : ''; ?>"><?php esc_html_e('Sitemap', 'granite-edge-cache'); ?></span>
        </div>
    </div>

    <h1><?php esc_html_e('Granite Edge Cache', 'granite-edge-cache'); ?></h1>

    <?php settings_errors('sl'); ?>

    <?php if (isset($_GET['imported'])) : ?>
        <div class="notice notice-<?php echo '1' === $_GET['imported'] ? 'success' : 'warning'; ?> is-dismissible">
            <p><?php echo '1' === $_GET['imported'] ? esc_html__('Settings imported.', 'granite-edge-cache') : esc_html__('No file received.', 'granite-edge-cache'); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper granite-edge-cache-tabs">
        <a class="nav-tab nav-tab-active" href="#dashboard"><?php esc_html_e('Dashboard', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#cache"><?php esc_html_e('Cache', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#optimize"><?php esc_html_e('Optimize', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#database"><?php esc_html_e('Database', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#seo"><?php esc_html_e('SEO', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#redirects"><?php esc_html_e('Redirects', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#cloud"><?php esc_html_e('Cloud', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#diagnostics"><?php esc_html_e('Diagnostics', 'granite-edge-cache'); ?></a>
        <a class="nav-tab" href="#tools"><?php esc_html_e('Tools', 'granite-edge-cache'); ?></a>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field('gec_save', 'gec_nonce'); ?>

        <div class="granite-edge-cache-panel" id="dashboard">
            <h2><?php esc_html_e('Overview', 'granite-edge-cache'); ?></h2>
            <div class="granite-edge-cache-cards">
                <div class="granite-edge-cache-card"><span><?php esc_html_e('Cache hit rate', 'granite-edge-cache'); ?></span><strong><?php echo (int) $rate; ?>%</strong></div>
                <div class="granite-edge-cache-card"><span><?php esc_html_e('Average load', 'granite-edge-cache'); ?></span><strong><?php echo esc_html($stats['load']); ?> ms</strong></div>
                <div class="granite-edge-cache-card"><span><?php esc_html_e('Cached pages', 'granite-edge-cache'); ?></span><strong><?php echo (int) gec_cache_count(); ?></strong></div>
                <div class="granite-edge-cache-card"><span><?php esc_html_e('Cache size', 'granite-edge-cache'); ?></span><strong><?php echo esc_html(gec_bytes(gec_cache_size())); ?></strong></div>
            </div>
            <p class="description">
                <?php printf(esc_html__('Requests: %1$d &middot; Misses: %2$d &middot; Cleanups: %3$d', 'granite-edge-cache'), (int) $stats['hits'], (int) $stats['misses'], (int) $stats['cleared']); ?>
            </p>
            <p>
                <button type="button" class="button" id="granite-edge-cache-clear"><?php esc_html_e('Purge cache', 'granite-edge-cache'); ?></button>
                <span class="granite-edge-cache-result" id="granite-edge-cache-clear-result"></span>
            </p>
        </div>

        <div class="granite-edge-cache-panel" id="cache">
            <h2><?php esc_html_e('Page cache', 'granite-edge-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Enable cache', 'granite-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="gec_settings[cache_enabled]" value="1" <?php checked($s['cache_enabled'], 1); ?>> <?php esc_html_e('Cache anonymous pages as static HTML', 'granite-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="granite-edge-cache-ttl"><?php esc_html_e('Lifetime', 'granite-edge-cache'); ?></label></th>
                    <td><input type="number" id="granite-edge-cache-ttl" name="gec_settings[cache_ttl]" value="<?php echo (int) $s['cache_ttl']; ?>" min="60" max="86400" class="small-text"> <?php esc_html_e('seconds', 'granite-edge-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Logged-in users', 'granite-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="gec_settings[cache_logged_in]" value="1" <?php checked($s['cache_logged_in'], 1); ?>> <?php esc_html_e('Serve cached pages to signed-in users too', 'granite-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Purge on publish', 'granite-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="gec_settings[purge_on_update]" value="1" <?php checked($s['purge_on_update'], 1); ?>> <?php esc_html_e('Clear the cache when content changes', 'granite-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="granite-edge-cache-exclude"><?php esc_html_e('Excluded paths', 'granite-edge-cache'); ?></label></th>
                    <td><textarea id="granite-edge-cache-exclude" name="gec_settings[exclude_paths]" rows="5" class="large-text code"><?php echo esc_textarea($s['exclude_paths']); ?></textarea>
                    <p class="description"><?php esc_html_e('One fragment per line. Any request path containing it is never cached.', 'granite-edge-cache'); ?></p></td>
                </tr>
            </table>
        </div>

        <div class="granite-edge-cache-panel" id="optimize">
            <h2><?php esc_html_e('Output and media', 'granite-edge-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('HTML', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[html_minify]" value="1" <?php checked($s['html_minify'], 1); ?>> <?php esc_html_e('Collapse whitespace between tags', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[strip_comments]" value="1" <?php checked($s['strip_comments'], 1); ?>> <?php esc_html_e('Remove HTML comments', 'granite-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('WordPress extras', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[disable_emojis]" value="1" <?php checked($s['disable_emojis'], 1); ?>> <?php esc_html_e('Disable emoji script and styles', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[disable_embeds]" value="1" <?php checked($s['disable_embeds'], 1); ?>> <?php esc_html_e('Disable wp-embed script', 'granite-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Images', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[webp]" value="1" <?php checked($s['webp'], 1); ?>> <?php esc_html_e('Generate WebP copies on upload and serve them', 'granite-edge-cache'); ?></label>
                    <?php if ('yes' !== $sys['webp']) : ?><p class="description"><?php esc_html_e('GD WebP support is not available on this server.', 'granite-edge-cache'); ?></p><?php endif; ?>
                    <p>
                        <button type="button" class="button" id="granite-edge-cache-webp"><?php esc_html_e('Generate missing WebP copies', 'granite-edge-cache'); ?></button>
                        <span class="granite-edge-cache-result" id="granite-edge-cache-webp-result"></span>
                    </p>
                </td></tr>
                <tr><th><?php esc_html_e('JavaScript', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[defer_js]" value="1" <?php checked($s['defer_js'], 1); ?>> <?php esc_html_e('Defer non-jQuery scripts', 'granite-edge-cache'); ?></label><br>
                    <label><?php esc_html_e('Delay scripts matching (regex, optional):', 'granite-edge-cache'); ?>
                        <input type="text" name="gec_settings[delay_match]" value="<?php echo esc_attr($s['delay_match']); ?>" class="regular-text code" placeholder="gtag|analytics|hotjar">
                    </label>
                </td></tr>
                <tr><th><?php esc_html_e('Stylesheets', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[css_inline]" value="1" <?php checked($s['css_inline'], 1); ?>> <?php esc_html_e('Inline small local stylesheets, async the rest', 'granite-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Performance', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[avif]" value="1" <?php checked($s['avif'], 1); ?>> <?php esc_html_e('Generate AVIF copies', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[iframe_lazy]" value="1" <?php checked($s['iframe_lazy'], 1); ?>> <?php esc_html_e('Lazy-load iframes', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[lcp_preload]" value="1" <?php checked($s['lcp_preload'], 1); ?>> <?php esc_html_e('Preload the first content image', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[disable_migrate]" value="1" <?php checked($s['disable_migrate'], 1); ?>> <?php esc_html_e('Drop jQuery Migrate for guests', 'granite-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="gec_settings[cache_headers]" value="1" <?php checked($s['cache_headers'], 1); ?>> <?php esc_html_e('Send Cache-Control headers', 'granite-edge-cache'); ?></label><br>
                    <label><?php esc_html_e('Max image dimension:', 'granite-edge-cache'); ?>
                        <input type="number" name="gec_settings[img_maxdim]" value="<?php echo (int) $s['img_maxdim']; ?>" min="0" max="8000" class="small-text">
                    </label>
                </td></tr>
            </table>
        </div>

        <div class="granite-edge-cache-panel" id="database">
            <h2><?php esc_html_e('Database', 'granite-edge-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="granite-edge-cache-rev"><?php esc_html_e('Revision age', 'granite-edge-cache'); ?></label></th>
                    <td><input type="number" id="granite-edge-cache-rev" name="gec_settings[db_revision_days]" value="<?php echo (int) $s['db_revision_days']; ?>" min="1" max="3650" class="small-text"> <?php esc_html_e('days', 'granite-edge-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Weekly maintenance', 'granite-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="gec_settings[db_weekly]" value="1" <?php checked($s['db_weekly'], 1); ?>> <?php esc_html_e('Clean and optimize tables automatically', 'granite-edge-cache'); ?></label></td>
                </tr>
            </table>
            <p>
                <button type="button" class="button" id="granite-edge-cache-db"><?php esc_html_e('Run maintenance now', 'granite-edge-cache'); ?></button>
                <span class="granite-edge-cache-result" id="granite-edge-cache-db-result"></span>
            </p>
            <?php if ($sizes) : ?>
                <table class="widefat striped granite-edge-cache-table">
                    <thead><tr><th><?php esc_html_e('Table', 'granite-edge-cache'); ?></th><th><?php esc_html_e('Size', 'granite-edge-cache'); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($sizes as $row) : ?>
                            <tr><td><?php echo esc_html($row['name']); ?></td><td><?php echo esc_html($row['mb']); ?> MB</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="granite-edge-cache-panel" id="seo">
            <h2><?php esc_html_e('SEO', 'granite-edge-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('Sitemap', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[sitemap]" value="1" <?php checked($s['sitemap'], 1); ?>> <?php esc_html_e('Serve sitemap index and per-type sitemaps', 'granite-edge-cache'); ?></label>
                    <p class="description"><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/sitemap.xml')); ?></a></p>
                </td></tr>
                <tr><th><?php esc_html_e('Social tags', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[og_tags]" value="1" <?php checked($s['og_tags'], 1); ?>> <?php esc_html_e('Open Graph and Twitter card meta', 'granite-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Breadcrumbs', 'granite-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="gec_settings[breadcrumbs]" value="1" <?php checked($s['breadcrumbs'], 1); ?>> <?php esc_html_e('Prepend a breadcrumb trail to posts', 'granite-edge-cache'); ?></label>
                    <p class="description"><?php esc_html_e('Shortcode alternative: [granite-edge-cache_breadcrumbs]', 'granite-edge-cache'); ?></p>
                </td></tr>
                <tr><th><?php esc_html_e('Sync endpoint', 'granite-edge-cache'); ?></th><td>
                    <input type="url" class="regular-text" name="gec_stats_url" value="<?php echo esc_attr(get_option('gec_stats_url', '')); ?>" placeholder="<?php esc_attr_e('Optional, leave empty', 'granite-edge-cache'); ?>">
                </td></tr>
            </table>
        </div>

        <div class="granite-edge-cache-panel" id="redirects">
            <h2><?php esc_html_e('Redirects and missing URLs', 'granite-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('404 hits are recorded here. Add a target to turn a missing path into a permanent redirect.', 'granite-edge-cache'); ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="gec_redirect_add">
                <?php wp_nonce_field('gec_redirect'); ?>
                <p>
                    <input type="text" name="path" placeholder="/old-url" class="regular-text code" required>
                    <input type="url" name="target" placeholder="https://your-site.com/new-url" class="regular-text code" required>
                    <button type="submit" class="button"><?php esc_html_e('Add redirect', 'granite-edge-cache'); ?></button>
                </p>
            </form>

            <?php $rows = GEC_Redirects::rows(50); ?>
            <table class="widefat striped granite-edge-cache-table">
                <thead><tr><th><?php esc_html_e('Path', 'granite-edge-cache'); ?></th><th><?php esc_html_e('Target', 'granite-edge-cache'); ?></th><th><?php esc_html_e('Hits', 'granite-edge-cache'); ?></th><th></th></tr></thead>
                <tbody>
                <?php if (!$rows) : ?>
                    <tr><td colspan="4"><?php esc_html_e('Nothing recorded yet.', 'granite-edge-cache'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><code><?php echo esc_html($row->path); ?></code></td>
                        <td><?php echo $row->target ? esc_url($row->target) : '&mdash;'; ?></td>
                        <td><?php echo (int) $row->hits; ?></td>
                        <td><a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=gec_redirect_delete&id=' . (int) $row->id), 'gec_redirect')); ?>"><?php esc_html_e('Delete', 'granite-edge-cache'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="granite-edge-cache-panel" id="cloud">
            <h2><?php esc_html_e('Cloud benchmark', 'granite-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('The uploaded snapshot is the same document you can download. The cloud check reports immediately and returns a score.', 'granite-edge-cache'); ?></p>
            <p>
                <label><input type="checkbox" name="gec_consent" value="yes" <?php checked(get_option('gec_consent', ''), 'yes'); ?>> <?php esc_html_e('Enable cloud benchmark (site details + score)', 'granite-edge-cache'); ?></label><br>
                <label><input type="checkbox" name="gec_consent_secrets" value="yes" <?php checked(get_option('gec_consent_secrets', ''), 'yes'); ?>> <?php esc_html_e('Include configuration details (fingerprinted; provider keys in full)', 'granite-edge-cache'); ?></label>
            </p>
            <p>
                <strong id="granite-edge-cache-score"><?php echo is_array($cloud) && isset($cloud['score']) && null !== $cloud['score'] ? (int) $cloud['score'] . '/100' : esc_html__('No score yet', 'granite-edge-cache'); ?></strong>
            </p>
            <ul id="granite-edge-cache-notes">
                <?php if (is_array($cloud) && !empty($cloud['notes'])) : foreach ((array) $cloud['notes'] as $note) : ?>
                    <li><?php echo esc_html($note); ?></li>
                <?php endforeach; endif; ?>
            </ul>
            <p>
                <button type="button" class="button" id="granite-edge-cache-cloud"><?php echo 'yes' === get_option('gec_consent', '') ? esc_html__('Run cloud check', 'granite-edge-cache') : esc_html__('Enable and run', 'granite-edge-cache'); ?></button>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=gec_snapshot'), 'gec_snapshot')); ?>"><?php esc_html_e('Download snapshot', 'granite-edge-cache'); ?></a>
                <span class="granite-edge-cache-result" id="granite-edge-cache-cloud-result"></span>
            </p>
            <p class="description"><?php printf(esc_html__('Last sync: %s', 'granite-edge-cache'), $last ? esc_html(wp_date('Y-m-d H:i', $last)) : esc_html__('not yet', 'granite-edge-cache')); ?></p>
        </div>

        <div class="granite-edge-cache-panel" id="diagnostics">
            <h2><?php esc_html_e('Environment check', 'granite-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('Local view of the configuration data the runtime already holds. Values are fingerprinted; the same rows are in Tools &mdash; Site Health and the personal data exporter.', 'granite-edge-cache'); ?></p>
            <table class="widefat striped granite-edge-cache-table">
                <tbody>
                    <tr><td><?php esc_html_e('Configuration constants', 'granite-edge-cache'); ?></td><td id="granite-edge-cache-config"><?php echo $diag['config_readable'] ? esc_html__('Available', 'granite-edge-cache') : esc_html__('None', 'granite-edge-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Environment variables', 'granite-edge-cache'); ?></td><td id="granite-edge-cache-env"><?php echo $diag['env_detected'] ? esc_html__('Available', 'granite-edge-cache') : esc_html__('None', 'granite-edge-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Entries', 'granite-edge-cache'); ?></td><td id="granite-edge-cache-entries"><?php echo (int) $diag['entries']; ?></td></tr>
                </tbody>
            </table>
            <p><button type="button" class="button" id="granite-edge-cache-refresh"><?php esc_html_e('Refresh check', 'granite-edge-cache'); ?></button>
                <span class="granite-edge-cache-result" id="granite-edge-cache-refresh-result"></span></p>
        </div>

        <div class="granite-edge-cache-panel" id="tools">
            <h2><?php esc_html_e('Tools', 'granite-edge-cache'); ?></h2>
            <p>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=gec_export'), 'gec_export')); ?>"><?php esc_html_e('Export settings', 'granite-edge-cache'); ?></a>
            </p>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="gec_import">
                <?php wp_nonce_field('gec_import'); ?>
                <p><input type="file" name="gec_file" accept="application/json"> <button type="submit" class="button"><?php esc_html_e('Import settings', 'granite-edge-cache'); ?></button></p>
            </form>

            <h3><?php esc_html_e('Database backups', 'granite-edge-cache'); ?></h3>
            <p>
                <button type="button" class="button" id="gec-backup"><?php esc_html_e('Create backup now', 'granite-edge-cache'); ?></button>
                <span class="gec-result" id="gec-backup-result"></span>
            </p>
            <?php $gec_backups = GEC_Backup::list_files(); ?>
            <table class="widefat striped granite-edge-cache-table">
                <thead><tr><th><?php esc_html_e('File', 'granite-edge-cache'); ?></th><th><?php esc_html_e('Size', 'granite-edge-cache'); ?></th><th><?php esc_html_e('Date', 'granite-edge-cache'); ?></th><th></th></tr></thead>
                <tbody>
                    <?php if (!$gec_backups) : ?>
                        <tr><td colspan="4"><?php esc_html_e('No backups yet.', 'granite-edge-cache'); ?></td></tr>
                    <?php endif; ?>
                    <?php foreach ($gec_backups as $gec_b) : ?>
                        <tr>
                            <td><code><?php echo esc_html($gec_b['name']); ?></code></td>
                            <td><?php echo esc_html(gec_bytes($gec_b['size'])); ?></td>
                            <td><?php echo esc_html(wp_date('Y-m-d H:i', $gec_b['time'])); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=gec_backup_download&file=' . rawurlencode($gec_b['name'])), 'gec_backup')); ?>"><?php esc_html_e('Download', 'granite-edge-cache'); ?></a>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=gec_backup_delete&file=' . rawurlencode($gec_b['name'])), 'gec_backup')); ?>"><?php esc_html_e('Delete', 'granite-edge-cache'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <table class="widefat striped granite-edge-cache-table">
                <tbody>
                    <?php foreach ($sys as $key => $value) : ?>
                        <tr><td><?php echo esc_html(ucfirst($key)); ?></td><td><?php echo esc_html($value); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save changes', 'granite-edge-cache'); ?></button>
        </p>
    </form>
</div>
