<?php

function xmldb_feedback_install() {
    global $DB;

/// Disable this module by default (because it's not technically part of Moodle 2.0)
    $DB->set_field('modules', 'visible', 0, array('name'=>'feedback'));

/// Install logging support
    update_log_display_entry('feedback', 'startcomplete', 'feedback', 'name');
    update_log_display_entry('feedback', 'submit', 'feedback', 'name');
    update_log_display_entry('feedback', 'delete', 'feedback', 'name');
    update_log_display_entry('feedback', 'view', 'feedback', 'name');
    update_log_display_entry('feedback', 'view all', 'course', 'shortname');

}
