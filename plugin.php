<?php

final class TC_INSTALLER_Plugin
{

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;

    //------------------------------------//--------------------------------------//

    /**
     * Assets injector
     *
     * @var string
     */
    public $assets;

    /**
     * This is the only instance of this class.
     *
     * @var string
     */
    protected static $_instance = null;

    //------------------------------------//--------------------------------------//

    /**
     * Plugin instance
     *
     * Makes sure that just one instance is allowed.
     *
     * @return object
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //------------------------------------//--------------------------------------//

    /**
     * Cloning is forbidden.
     *
     * @return void
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'tracking-code-installer'), '1.0');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @return void
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'tracking-code-installer'), '1.0');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Plugin configuration
     *
     * @param string $key Optional. Get the config value by key.
     * @return mixed
     */
    public function config($key = false)
    {
        return tc_installer_config($key);
    }

    //------------------------------------//--------------------------------------//

    /**
     * Build it!
     */
    public function __construct()
    {
        $this->version = TC_INSTALLER_VERSION;

        /* Include core
        --------------------*/
        include_once $this->rootPath() . "autoloader.php";
        include_once $this->rootPath() . "functions.php";

        $this->assets = new TrackingCodeInstaller\Assets\Manage;

        /* Activation and deactivation hooks
        -----------------------------------------*/
        register_activation_hook(TC_INSTALLER_PLUGIN_FILE, [$this, 'onActivation']);
        register_deactivation_hook(TC_INSTALLER_PLUGIN_FILE, [$this, 'onDeactivation']);

        /* Init core
        -----------------*/
        add_action('init', [$this, 'init'], 0);

        /* Load components, if any...
        ----------------------------------*/
        $this->loadComponents();

        /* Plugin fully loaded and executed
        ----------------------------------------*/
        do_action('tc_installer:loaded');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Init the plugin.
     *
     * Attached to `init` action hook. Init functions and classes here.
     *
     * @return void
     */
    public function init()
    {
        do_action('tc_installer:before_init');

        $this->loadTextDomain();

        // Call plugin classes/functions here.
        do_action('tc_installer:init');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Localize
     *
     * @return void
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain(
            'tracking-code-installer',
            false,
            $this->config('lang_path')
        );
    }

    //------------------------------------//--------------------------------------//

    /**
     * Load components
     *
     * @return void
     */
    public function loadComponents()
    {
        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::SKIP_DOTS;
        $iterator = new FilesystemIterator(TC_INSTALLER_PATH . 'components', $flags);

        foreach ($iterator as $path => $item) {
            if ($item->isDir()) {
                require_once trailingslashit($path) . 'component.php';
            } elseif ($item->isFile() && stripos($item->getFilename(), 'component-') !== false) {
                require_once $path;
            }
        }
    }

    /*
    -------------------------------------------------------------------------------
    Styles
    -------------------------------------------------------------------------------
    */
    public function addStyles($styles)
    {
        $this->assets->addStyles($styles);
    }

    public function addStyle($handle, $s = false)
    {
        $this->assets->addStyle($handle, $s);
    }

    /*
    -------------------------------------------------------------------------------
    Scripts
    -------------------------------------------------------------------------------
    */
    public function addScripts($scripts)
    {
        $this->assets->addScripts($scripts);
    }

    public function addScript($handle, $s = false)
    {
        $this->assets->addScript($handle, $s);
    }

    //------------------------------------//--------------------------------------//

    /**
     * Actions when the plugin is activated
     *
     * @return void
     */
    public function onActivation()
    {
        // Code to be executed on plugin activation
        do_action('tc_installer:on_activation');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Actions when the plugin is deactivated
     *
     * @return void
     */
    public function onDeactivation()
    {
        // Code to be executed on plugin deactivation
        do_action('tc_installer:on_deactivation');
    }

    //------------------------------------//--------------------------------------//

    /**
     * Get Root URL
     *
     * @return string
     */
    public function rootURL()
    {
        return TC_INSTALLER_URL;
    }

    //------------------------------------//--------------------------------------//

    /**
     * Get Root PATH
     *
     * @return string
     */
    public function rootPath()
    {
        return TC_INSTALLER_PATH;
    }

    //------------------------------------//--------------------------------------//

    /**
     * Get assets url.
     *
     * @param string $file Optionally specify a file name
     *
     * @return string
     */
    public function assetsURL($file = false)
    {
        $path = TC_INSTALLER_URL . 'assets/';

        if ($file) {
            $path = $path . $file;
        }

        return $path;
    }

}

/*
-------------------------------------------------------------------------------
Main plugin instance
-------------------------------------------------------------------------------
*/
function tracking_code_installer()
{
    return TC_INSTALLER_Plugin::instance();
}

/*
-------------------------------------------------------------------------------
Rock it!
-------------------------------------------------------------------------------
*/
tracking_code_installer();
