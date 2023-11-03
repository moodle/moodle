<?php
defined('MOODLE_INTERNAL') || die();
$ADMIN->add('modules', new admin_category('block_learnerscript', get_string('pluginname', 'block_learnerscript')));

$setting = new admin_settingpage('learnerscriptreports', get_string('reports', 'block_learnerscript'), 'moodle/site:config');

	// $setting->add(new admin_setting_configtext('block_learnerscript/dbhost', get_string('dbhost', 'block_learnerscript'), get_string('dbhostinfo', 'block_learnerscript'), '', PARAM_URL, 30));
    // $setting->add(new admin_setting_configtext('block_learnerscript/dbname', get_string('dbname', 'block_learnerscript'), get_string('dbnameinfo', 'block_learnerscript'), '', PARAM_RAW, 30));
    // $setting->add(new admin_setting_configpasswordunmask('block_learnerscript/dbuser', get_string('dbuser', 'block_learnerscript'), get_string('dbuserinfo', 'block_learnerscript'), '', PARAM_RAW, 30));
    // $setting->add(new admin_setting_configpasswordunmask('block_learnerscript/dbpass', get_string('dbpass', 'block_learnerscript'), get_string('dbpassinfo', 'block_learnerscript'), '', PARAM_RAW, 30));
    $setting->add(new admin_setting_configcheckbox('block_learnerscript/sqlsecurity', get_string('sqlsecurity', 'block_learnerscript'), get_string('sqlsecurityinfo', 'block_learnerscript'), 1));
    $setting->add(new admin_setting_configcheckbox('block_learnerscript/exportfilesystem', get_string('exportfilesystem', 'block_learnerscript'), get_string('exportfilesystem', 'block_learnerscript'), 1));
    $setting->add(new admin_setting_configtext('block_learnerscript/exportfilesystempath', get_string('exportfilesystempath', 'block_learnerscript'), get_string('exportfilesystempathdesc', 'block_learnerscript'), 'learnerscript/reports', PARAM_URL, 40));
    $setting->add(new admin_setting_configcolourpicker('block_learnerscript/analytics_color', get_string('analytics_color', 'block_learnerscript'), get_string('analytics_color_desc', 'block_learnerscript'), '#FFFFFF'));
    $setting->add(new admin_setting_configstoredfile('block_learnerscript/logo', get_string('logo', 'block_learnerscript'), get_string('logo_desc', 'block_learnerscript'), 'logo', 0, array('maxfiles' => 1, 'accepted_types' => array('image'))));

$ADMIN->add('block_learnerscript', $setting);

    $ls_licence = new admin_settingpage('licence', get_string('licence', 'block_learnerscript'));

    $ls_licence->add(new block_learnerscript_licence_setting('block_learnerscript/serialkey', 'serialkey', get_string('serialkeyinfo', 'block_learnerscript'), '', PARAM_ALPHANUMEXT, 43));

$ADMIN->add('block_learnerscript', $ls_licence);