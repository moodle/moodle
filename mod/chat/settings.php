<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('chat_method_heading', get_string('generalconfig', 'chat'),
                       get_string('explaingeneralconfig', 'chat')));

    $options = array();
    $options['ajax']      = get_string('methodajax', 'chat');
    $options['header_js'] = get_string('methodnormal', 'chat');
    $options['sockets']   = get_string('methoddaemon', 'chat');
    $settings->add(new admin_setting_configselect('chat_method', get_string('method', 'chat'),
                       get_string('configmethod', 'chat'), 'ajax', $options));

    $settings->add(new admin_setting_configtext('chat_refresh_userlist', get_string('refreshuserlist', 'chat'),
                       get_string('configrefreshuserlist', 'chat'), 10, PARAM_INT));

    $settings->add(new admin_setting_configtext('chat_old_ping', get_string('oldping', 'chat'),
                       get_string('configoldping', 'chat'), 35, PARAM_INT));


    $settings->add(new admin_setting_heading('chat_normal_heading', get_string('methodnormal', 'chat'),
                       get_string('explainmethodnormal', 'chat')));

    $settings->add(new admin_setting_configtext('chat_refresh_room', get_string('refreshroom', 'chat'),
                       get_string('configrefreshroom', 'chat'), 5, PARAM_INT));

    $options = array();
    $options['jsupdate']  = get_string('normalkeepalive', 'chat');
    $options['jsupdated'] = get_string('normalstream', 'chat');
    $settings->add(new admin_setting_configselect('chat_normal_updatemode', get_string('updatemethod', 'chat'),
                       get_string('confignormalupdatemode', 'chat'), 'jsupdate', $options));


    $settings->add(new admin_setting_heading('chat_daemon_heading', get_string('methoddaemon', 'chat'),
                       get_string('explainmethoddaemon', 'chat')));

    $settings->add(new admin_setting_configtext('chat_serverhost', get_string('serverhost', 'chat'),
                       get_string('configserverhost', 'chat'), get_host_from_url($CFG->wwwroot)));

    $settings->add(new admin_setting_configtext('chat_serverip', get_string('serverip', 'chat'),
                       get_string('configserverip', 'chat'), '127.0.0.1'));

    $settings->add(new admin_setting_configtext('chat_serverport', get_string('serverport', 'chat'),
                       get_string('configserverport', 'chat'), 9111, PARAM_INT));

    $settings->add(new admin_setting_configtext('chat_servermax', get_string('servermax', 'chat'),
                       get_string('configservermax', 'chat'), 100, PARAM_INT));
}
