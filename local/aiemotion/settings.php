<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage(
        'local_aiemotion_settings',
        get_string('pluginname', 'local_aiemotion')
    );

    // Gemini API Key
    $settings->add(new admin_setting_configtext(
        'local_aiemotion/geminiapikey',
        'Google Gemini API Key',
        'Paste your Gemini API key from Google AI Studio',
        '',
        PARAM_TEXT
    ));

    $ADMIN->add('localplugins', $settings);
}
