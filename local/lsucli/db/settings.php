<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    if ($tasksnode = $ADMIN->get_node('server')->get_node('tasks')) {
        $settings = new admin_settingpage(
            'local_lsucli_settings',
            get_string('pluginname', 'local_lsucli')
        );

        $url = new moodle_url('/local/lsucli/index.php');
        $lsuclinode = new admin_externalpage(
            'local_lsucli',
            get_string('lsucli', 'local_lsucli'),
            $url,
            'moodle/site:config'
        );

        $settings->add($lsuclinode);
        $tasksnode->add_node($settings);
    }
}
