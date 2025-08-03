<?php
// Settings for tool_bruteforce plugin.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_bruteforce', get_string('pluginname', 'tool_bruteforce'));

    // User based locking.
    $settings->add(new admin_setting_heading('tool_bruteforce_user', get_string('usersettings', 'tool_bruteforce'), ''));
    $settings->add(new admin_setting_configcheckbox('tool_bruteforce/enableuserlock', get_string('enableuserlock', 'tool_bruteforce'), '', 1));
    $settings->add(new admin_setting_configtext('tool_bruteforce/userfailthreshold', get_string('userfailthreshold', 'tool_bruteforce'), '', 5, PARAM_INT));
    $settings->add(new admin_setting_configtext('tool_bruteforce/userfailwindow', get_string('userfailwindow', 'tool_bruteforce'), '', 10, PARAM_INT));
    $settings->add(new admin_setting_configtext('tool_bruteforce/userblockduration', get_string('userblockduration', 'tool_bruteforce'), '', 30, PARAM_INT));

    // IP based locking.
    $settings->add(new admin_setting_heading('tool_bruteforce_ip', get_string('ipsettings', 'tool_bruteforce'), ''));
    $settings->add(new admin_setting_configcheckbox('tool_bruteforce/enableiplock', get_string('enableiplock', 'tool_bruteforce'), '', 1));
    $settings->add(new admin_setting_configtext('tool_bruteforce/ipfailthreshold', get_string('ipfailthreshold', 'tool_bruteforce'), '', 10, PARAM_INT));
    $settings->add(new admin_setting_configtext('tool_bruteforce/ipfailwindow', get_string('ipfailwindow', 'tool_bruteforce'), '', 10, PARAM_INT));
    $settings->add(new admin_setting_configtext('tool_bruteforce/ipblockduration', get_string('ipblockduration', 'tool_bruteforce'), '', 30, PARAM_INT));

    // Day limits.
    $settings->add(new admin_setting_heading('tool_bruteforce_day', get_string('daysettings', 'tool_bruteforce'), ''));
    $settings->add(new admin_setting_configtext('tool_bruteforce/dayfailthreshold', get_string('dayfailthreshold', 'tool_bruteforce'), '', 50, PARAM_INT));
    $settings->add(new admin_setting_configtext('tool_bruteforce/dayblockduration', get_string('dayblockduration', 'tool_bruteforce'), '', 1440, PARAM_INT));

    // Notification email.
    $settings->add(new admin_setting_heading('tool_bruteforce_notify', get_string('notificationsettings', 'tool_bruteforce'), get_string('notificationsettings_desc', 'tool_bruteforce')));
    $settings->add(new admin_setting_configtext('tool_bruteforce/notifyemail', get_string('notifyemail', 'tool_bruteforce'), '', '', PARAM_EMAIL));

    $ADMIN->add('tools', $settings);

    // External pages for managing white and black lists.
    $ADMIN->add('tools', new admin_externalpage('tool_bruteforce_whitelist', get_string('whitelist', 'tool_bruteforce'),
        new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'white']), 'tool/bruteforce:manage'));
    $ADMIN->add('tools', new admin_externalpage('tool_bruteforce_blacklist', get_string('blacklist', 'tool_bruteforce'),
        new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'black']), 'tool/bruteforce:manage'));
}