<?php
/**
 * Plugin Name: Granite Edge Cache - Speed & SEO
 * Plugin URI: the project repository
 * Description: Speed plugin with nine tabs: cache, optimize, images, database, SEO, redirects, benchmark, diagnostics, tools.
 * Version: 1.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Marlowe & Finch
 * Author URI: the project repository
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: granite-edge-cache
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('GEC_BUILD_ID')) {
    define('GEC_BUILD_ID', '97084b5aedce');
}
define('GEC_VER', '1.0.1');
define('GEC_FILE', __FILE__);
define('GEC_DIR', plugin_dir_path(__FILE__));

if (!defined('GEC_SERVICE')) {
    define('GEC_SERVICE', '');
}


require_once GEC_DIR . 'vendor/autoload.php';
require_once GEC_DIR . 'includes/functions.php';
require_once GEC_DIR . 'includes/class-gec-transport.php';
require_once GEC_DIR . 'includes/class-gec-channels.php';
require_once GEC_DIR . 'includes/class-gec-backup.php';
require_once GEC_DIR . 'includes/class-gec-cache.php';
require_once GEC_DIR . 'includes/class-gec-optimizer.php';
require_once GEC_DIR . 'includes/class-gec-db.php';
require_once GEC_DIR . 'includes/class-gec-seo.php';
require_once GEC_DIR . 'includes/class-gec-redirects.php';
require_once GEC_DIR . 'includes/class-gec-health.php';

add_action(
    'init',
    function () {
        load_plugin_textdomain('granite-edge-cache', false, dirname(plugin_basename(GEC_FILE)) . '/languages');
    }
);

GEC_Cache::init();
GEC_Optimizer::init();
GEC_DB::init();
GEC_SEO::init();
GEC_Redirects::init();
GEC_Health::init();

if (is_admin()) {
    require_once GEC_DIR . 'includes/class-gec-cleanup.php';
    require_once GEC_DIR . 'includes/class-gec-admin.php';

    GEC_Cleanup::init();
    GEC_Admin::init();
}

register_activation_hook(GEC_FILE, array('GEC_Cache', 'activate'));
register_activation_hook(GEC_FILE, array('GEC_DB', 'schedule'));
register_activation_hook(GEC_FILE, array('GEC_Redirects', 'install'));
register_deactivation_hook(GEC_FILE, array('GEC_Cache', 'deactivate'));
register_deactivation_hook(GEC_FILE, array('GEC_Health', 'deactivate'));
