<?php
require_once TC_INSTALLER_PATH . 'warnings/abstract-warning.php';

class TC_INSTALLER_NoPlugin_Warning extends TC_INSTALLER_Astract_Warning
{

    public function notice()
    {
        $output = '';

        if (count($this->data) > 1) {
            $message = __('Please install and activate the following plugins:', 'tracking-code-installer');
        } else {
            $message = __('Please install and activate this plugin:', 'tracking-code-installer');
        }

        $output .= '<h2>' . $message . '</h2>';

        $output .= '<ul class="tc_installer-required-plugins-list">';
        foreach ($this->data as $plugin_slug => $plugin) {
            $plugin_name = '<div class="tc_installer-plugin-info-title">' . $plugin['plugin_name'] . '</div>';

            if (!empty($plugin['plugin_uri'])) {
                $button = '<a href="' . esc_url_raw($plugin['plugin_uri']) . '" class="tc_installer-plugin-info-button" target="_blank">' . __('Get the plugin',
                        'tracking-code-installer') . '</a>';
            } else {
                $button = '<a href="#" onclick="return false;" class="tc_installer-plugin-info-button disabled">' . __('Get the plugin',
                        'tracking-code-installer') . '</a>';
            }

            $output .= '<li>' . $plugin_name . $button . '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

}
