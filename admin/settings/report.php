<?php  //$Id$

foreach (get_list_of_plugins($CFG->admin.'/report') as $plugin) {
    $settings_path = "$CFG->dirroot/$CFG->admin/report/$plugin/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
        continue;
    }

    $index_path = "$CFG->dirroot/$CFG->admin/report/$plugin/index.php";
    if (!file_exists($index_path)) {
        continue;
    }
    // old style 3rd party plugin without settings.php
    $ADMIN->add('reports', new admin_externalpage('report'.$plugin, $plugin, $index_path, 'moodle/site:viewreports'));
}

?>
