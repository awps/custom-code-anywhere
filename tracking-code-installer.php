<?php
/* 
 * Plugin Name: Tracking Code Installer
 * Plugin URI:  http://zerowp.com/
 * Description: An easy way to add tracking codes or inline javascript to your site without modifying the current theme.
 * Author:      ZeroWP Team
 * Author URI:  http://zerowp.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tracking-code-installer
 * Domain Path: /languages
 *
 * Version:     1.0
 * 
 */

/* No direct access allowed!
---------------------------------*/
if (!defined('ABSPATH')) {
    exit;
}

/* Plugin configuration
----------------------------*/
function tc_installer_config($key = false)
{
    $settings = apply_filters('tc_installer:config_args', [

        // Plugin data
        'version'          => '1.0',
        'min_php_version'  => '5.3',

        // The list of required plugins. 'plugin_slug' => array( 'plugin_name' => '', 'plugin_uri' => '' )
        // Example: 'example-plugin' => array( 'plugin_name' => 'Example Plug-in', 'plugin_uri' => 'http://example.com/' ),
        'required_plugins' => [],

        // The priority in plugins loaded. Only if has required plugins
        'priority'         => 10,

        // Plugin branding
        'plugin_name'      => __('Tracking Code Installer', 'tracking-code-installer'),
        'id'               => 'tracking-code-installer',
        'namespace'        => 'TrackingCodeInstaller',
        'uppercase_prefix' => 'TC_INSTALLER',
        'lowercase_prefix' => 'tc_installer',

        // Access to plugin directory
        'file'             => __FILE__,
        'lang_path'        => plugin_dir_path(__FILE__) . 'languages',
        'basename'         => plugin_basename(__FILE__),
        'path'             => plugin_dir_path(__FILE__),
        'url'              => plugin_dir_url(__FILE__),
        'uri'              => plugin_dir_url(__FILE__),
        //Alias

    ]);

    // Make sure that PHP version is set to 5.3+
    if (version_compare($settings['min_php_version'], '5.3', '<')) {
        $settings['min_php_version'] = '5.3';
    }

    // Get the value by key
    if (!empty($key)) {
        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        } else {
            return false;
        }
    } // Get settings
    else {
        return $settings;
    }
}

/* Define the current version of this plugin.
-----------------------------------------------------------------------------*/
define('TC_INSTALLER_VERSION', tc_installer_config('version'));

/* Plugin constants
------------------------*/
define('TC_INSTALLER_PLUGIN_FILE', tc_installer_config('file'));
define('TC_INSTALLER_PLUGIN_BASENAME', tc_installer_config('basename'));

define('TC_INSTALLER_PATH', tc_installer_config('path'));
define('TC_INSTALLER_URL', tc_installer_config('url'));
define('TC_INSTALLER_URI', tc_installer_config('url')); // Alias

/* Minimum PHP version required
------------------------------------*/
define('TC_INSTALLER_MIN_PHP_VERSION', tc_installer_config('min_php_version'));

/* Plugin Init
----------------------*/

final class TC_INSTALLER_Plugin_Init
{

    public function __construct()
    {
        $required_plugins = tc_installer_config('required_plugins');
        $missed_plugins = $this->missedPlugins();

        /* The installed PHP version is lower than required.
        ---------------------------------------------------------*/
        if (version_compare(PHP_VERSION, TC_INSTALLER_MIN_PHP_VERSION, '<')) {
            require_once TC_INSTALLER_PATH . 'warnings/php-warning.php';
            new TC_INSTALLER_PHP_Warning;
        } /* Required plugins are not installed/activated
		----------------------------------------------------*/
        elseif (!empty($required_plugins) && !empty($missed_plugins)) {
            require_once TC_INSTALLER_PATH . 'warnings/noplugin-warning.php';
            new TC_INSTALLER_NoPlugin_Warning($missed_plugins);
        } /* We require some plugins and all of them are activated
		-------------------------------------------------------------*/
        elseif (!empty($required_plugins) && empty($missed_plugins)) {
            add_action(
                'plugins_loaded',
                [$this, 'getSource'],
                tc_installer_config('priority')
            );
        } /* We don't require any plugins. Include the source directly
		----------------------------------------------------------------*/
        else {
            $this->getSource();
        }
    }

    //------------------------------------//--------------------------------------//

    /**
     * Get plugin source
     *
     * @return void
     */
    public function getSource()
    {
        require_once TC_INSTALLER_PATH . 'plugin.php';
    }

    //------------------------------------//--------------------------------------//

    /**
     * Missed plugins
     *
     * Get an array of missed plugins
     *
     * @return array
     */
    public function missedPlugins()
    {
        $required = tc_installer_config('required_plugins');
        $active = $this->activePlugins();
        $diff = array_diff_key($required, $active);

        return $diff;
    }

    //------------------------------------//--------------------------------------//

    /**
     * Active plugins
     *
     * Get an array of active plugins
     *
     * @return array
     */
    public function activePlugins()
    {
        $active = get_option('active_plugins');
        $slugs = [];

        if (!empty($active)) {
            $slugs = array_flip(array_map([$this, '_filterPlugins'], (array)$active));
        }

        return $slugs;
    }

    //------------------------------------//--------------------------------------//

    /**
     * Filter plugins callback
     *
     * @return string
     */
    protected function _filterPlugins($value)
    {
        $plugin = explode('/', $value);
        return $plugin[0];
    }

}

new TC_INSTALLER_Plugin_Init;
