<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingaflex', get_string('configtitle', 'theme_aflex'));
    $page = new admin_settingpage('theme_aflex_branding', get_string('branding', 'theme_aflex'));

    $setting = new admin_setting_configstoredfile('theme_aflex/logo', get_string('logo', 'theme_aflex'),
        get_string('logodesc', 'theme_aflex'), 'logo', 0, ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.svg']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile('theme_aflex/favicon', get_string('favicon', 'theme_aflex'),
        get_string('favicondesc', 'theme_aflex'), 'favicon', 0, ['maxfiles' => 1, 'accepted_types' => ['.png', '.ico']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile('theme_aflex/loginbackgroundimage',
        get_string('loginbackgroundimage', 'theme_aflex'), get_string('loginbackgroundimagedesc', 'theme_aflex'),
        'loginbackgroundimage', 0, ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.webp']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker('theme_aflex/brandcolor', get_string('brandcolor', 'theme_aflex'),
        get_string('brandcolordesc', 'theme_aflex'), '#173f5f');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);

    $page = new admin_settingpage('theme_aflex_advanced', get_string('advanced', 'theme_aflex'));
    $page->add(new admin_setting_scsscode('theme_aflex/scsspre', get_string('rawscsspre', 'theme_aflex'),
        get_string('rawscsspredesc', 'theme_aflex'), '', PARAM_RAW));
    $page->add(new admin_setting_scsscode('theme_aflex/scss', get_string('rawscss', 'theme_aflex'),
        get_string('rawscssdesc', 'theme_aflex'), '', PARAM_RAW));
    $settings->add($page);
}

