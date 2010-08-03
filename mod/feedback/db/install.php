<?php

function xmldb_feedback_install() {
    global $DB;

/// Disable this module by default (because it's not technically part of Moodle 2.0)
    $DB->set_field('modules', 'visible', 0, array('name'=>'feedback'));

}
