<?php
require_once TC_INSTALLER_PATH . 'warnings/abstract-warning.php';

class TC_INSTALLER_PHP_Warning extends TC_INSTALLER_Astract_Warning
{

    public function notice()
    {
        // Thanks to Yoast SEO for text.
        $output = '';

        $output .= '<h2>' . sprintf(
                __('This plugin requires PHP version %s or higher!', 'tracking-code-installer'),
                TC_INSTALLER_MIN_PHP_VERSION
            ) . '</h2>';

        $output .= '<h3>' . __('Your site could be faster and more secure with a newer PHP version.',
                'tracking-code-installer') . '</h3>';

        $output .= '<p>' . __('Hey, we\'ve noticed that you\'re running an outdated version of PHP. PHP is the programming language that WordPress and this plugin are built on. The version that is currently used for your site is no longer supported. Newer versions of PHP are both faster and more secure. In fact, your version of PHP no longer receives security updates, which is why we\'re sending you to this notice.',
                'tracking-code-installer') . '</p>';

        $output .= '<p>' . __('Hosts have the ability to update your PHP version, but sometimes they don\'t dare to do that because they\'re afraid they\'ll break your site.',
                'tracking-code-installer') . '</p>';

        $output .= '<h3>' . __('To which version should I update?', 'tracking-code-installer') . '</h3>';

        $output .= '<p>' . sprintf(__('While this plugin requires at least %s, you should update your PHP version to either 5.6 or to 7.0 or 7.1. On a normal WordPress site, switching to PHP 5.6 should never cause issues. We would however actually recommend you switch to PHP7. There are some plugins that are not ready for PHP7 though, so do some testing first. PHP7 is much faster than PHP 5.6. It\'s also the only PHP version still in active development and therefore the better option for your site in the long run.',
                'tracking-code-installer'), TC_INSTALLER_MIN_PHP_VERSION) . '<p>';

        return $output;
    }

}
