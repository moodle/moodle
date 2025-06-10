<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
require_once("$CFG->dirroot/course/lib.php");
$respondusws_lib_file = dirname(dirname(__FILE__)) . "/lib.php";
require_once($respondusws_lib_file);
function xmldb_respondusws_install() {
    respondusws_install();
}
function xmldb_respondusws_install_recovery() {
    respondusws_install();
}
function respondusws_install() {
    global $DB;
    global $CFG;
    $dbman = $DB->get_manager();
    if (!isset($CFG->respondusws_initialdisable)) {
        if (!$dbman->table_exists("respondusws")
          || $DB->count_records("respondusws") == 0) {
            $DB->set_field("modules", "visible", 0,
              array("name" => "respondusws"));
            set_config("respondusws_initialdisable", 1);
        }
    }
    $module = $DB->get_record("modules", array("name" => "respondusws"));
    if ($module === false) {
        throw new moodle_exception("installmodulerecord", "respondusws");
    }
    $instance = new stdClass;
    $instance->course = SITEID;
    $instance->name = get_string("sharedname", "respondusws");
    $instance->intro = get_string("sharedintro", "respondusws");
    $instance->introformat = FORMAT_HTML;
    $instance->modulename = $module->name;
    $instance->module = $module->id;
    $instance->section = 0;
    $instance->coursemodule = "";
    $instance->instance = "";
    $instance->cmidnumber = "";
    $instance->groupmode = 0;
    $instance->groupingid = 0;
    $instance->groupmembersonly = 0;
    $instance->visible = false;
    $instance_id = respondusws_add_instance($instance);
    if (is_string($instance_id)) {
        $a = (object)array("detail" => $instance_id);
        throw new moodle_exception("installaddinstancedetail", "respondusws", "", $a);
    } else if ($instance_id === false) {
        throw new moodle_exception("installaddinstance", "respondusws");
    }
    $instance->instance = $instance_id;
    $cmid = add_course_module($instance);
    if (!$cmid) {
        throw new moodle_exception("installcoursemodule", "respondusws");
    }
    $instance->coursemodule = $cmid;
    if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
           $section = $DB->get_record("course_sections", array(
          "course" => $instance->course, "section" => $instance->section
          ));
        if ($section === false) {
            $section = new stdClass();
            $section->course = $instance->course;
            $section->section = $instance->section;
            $section->summary = "";
            $section->summaryformat = FORMAT_HTML;
            $section->sequence = "";
            $section->id = $DB->insert_record("course_sections", $section);
        }
        if (empty($section->sequence)) {
            $newsequence = "$instance->coursemodule";
        } else {
            $newsequence = "$section->sequence,$instance->coursemodule";
        }
           $DB->set_field("course_sections", "sequence", $newsequence,
          array("id" => $section->id));
        $section_id = $section->id;
    } else {
        $section_id = add_mod_to_section($instance);
    }
    if (!$section_id) {
        throw new moodle_exception("installmodsection", "respondusws");
    }
    $DB->set_field("course_modules", "section", $section_id, array("id" => $cmid));
    set_coursemodule_visible($cmid, $instance->visible);
    set_coursemodule_idnumber($cmid, $instance->cmidnumber);
    rebuild_course_cache(SITEID, true);
}
