<?php

function xmldb_block_course_overview_install() {
    global $DB;

    //setup user info fields
    if (!$DB->get_record('user_info_field', array('shortname' => 'mynumber'))) {
        $field = new stdClass();
        $field->shortname = 'mynumber';
        $field->name = 'Number of courses on My Moodle';
        $field->datatype = 'text';
        $field->visible = 1;
        $field->categoryid = 1;

        $DB->insert_record('user_info_field', $field);
    }

    if (!$DB->get_record('user_info_field', array('shortname' => 'myorder'))) {
        $field = new stdClass();
        $field->shortname = 'myorder';
        $field->name = 'Order of courses on My Moodle';
        $field->datatype = 'text';
        $field->visible = 0;
        $field->categoryid = 1;

        $DB->insert_record('user_info_field', $field);
    }
}