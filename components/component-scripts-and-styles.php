<?php
/*
-------------------------------------------------------------------------------
Back-end scripts and styles
-------------------------------------------------------------------------------
*/
add_action('admin_enqueue_scripts', function () {
    tracking_code_installer()->addStyle('tc-installer-styles-admin', [
        'src'     => tracking_code_installer()->assetsURL('css/styles-admin.css'),
        'enqueue' => false,
    ]);

    tracking_code_installer()->addScript('tc-installer-config-admin', [
        'src'     => tracking_code_installer()->assetsURL('js/config-admin.js'),
        'deps'    => ['jquery'],
        'enqueue' => false,
    ]);

    /* SMK Accordion
    ---------------------*/

    tracking_code_installer()->addStyle('tc-installer-smk-accordion', [
        'src'     => tracking_code_installer()->assetsURL('assets/vendor/smk-accordion/smk-accordion'),
        'enqueue' => false,
    ]);

    tracking_code_installer()->addScript('tc-installer-smk-accordion', [
        'src'     => tracking_code_installer()->assetsURL('assets/vendor/smk-accordion/smk-accordion.js'),
        'deps'    => ['jquery'],
        'enqueue' => false,
    ]);
});

/*
-------------------------------------------------------------------------------
Front-end scripts and styles
-------------------------------------------------------------------------------
*/
add_action('wp_enqueue_scripts', function () {
    tracking_code_installer()->addStyle(tc_installer_config('id') . '-styles', [
        'src'     => tracking_code_installer()->assetsURL('css/styles.css'),
        'enqueue' => false,
    ]);

    tracking_code_installer()->addScript(tc_installer_config('id') . '-config', [
        'src'     => tracking_code_installer()->assetsURL('js/config.js'),
        'deps'    => ['jquery'],
        'enqueue' => false,
    ]);
});
