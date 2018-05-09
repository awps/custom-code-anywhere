<?php

abstract class TC_INSTALLER_Astract_Warning
{

    protected $page_slug = 'tc_installer-fail-notice';

    public function __construct($data = false)
    {
        $this->data = $data;
        $this->plugin_title = tc_installer_config('plugin_name');

        add_action('admin_menu', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'style']);
    }

    abstract public function notice();

    public function renderNotice()
    {
        echo '<div class="' . $this->page_slug . '">' .

            '<h1>' . tc_installer_config('plugin_name') . '</h1>' .
            $this->notice()

            . '</div>';
    }

    public function register()
    {
        add_menu_page(
            $this->plugin_title,
            $this->plugin_title,
            'manage_options',
            $this->page_slug,
            [$this, 'renderNotice'],
            'dashicons-warning',
            60
        );
    }

    public function style()
    {
        if (is_admin() && isset($_GET['page']) && ($this->page_slug === $_GET['page'])) {
            wp_enqueue_style('tc_installer-fail-notice', TC_INSTALLER_URL . 'warnings/style.css', false,
                TC_INSTALLER_VERSION);
        }
    }

}
