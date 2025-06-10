<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
require_once(dirname(dirname(dirname(__FILE__))) . "/config.php");
require_once(dirname(__FILE__) . "/lib.php");
defined("MOODLE_INTERNAL") || die();
$respondusws_id = optional_param("id", 0, PARAM_INT);
$respondusws_a = optional_param("a", 0, PARAM_INT);
$respondusws_dbman = $DB->get_manager();
if ($respondusws_id) {
    $respondusws_cm = get_coursemodule_from_id("respondusws", $respondusws_id);
    if (!$respondusws_cm) {
        print_error("invalidcoursemodule");
    }
    $respondusws_course = $DB->get_record("course", array("id" => $respondusws_cm->course));
    if ($respondusws_course === false) {
        print_error("coursemisconf");
    }
    if ($respondusws_dbman->table_exists("respondusws")) {
        $respondusws_module = $DB->get_record("respondusws", array("id" => $respondusws_cm->instance));
    } else {
        $respondusws_module = false;
    }
    if ($respondusws_module === false) {
        print_error("invalidcminstance", "respondusws");
    }
} else if ($respondusws_a) {
    if ($respondusws_dbman->table_exists("respondusws")) {
        $respondusws_module = $DB->get_record("respondusws", array("id" => $respondusws_a));
    } else {
        $respondusws_module = false;
    }
    if ($respondusws_module === false) {
        print_error("invalidcminstance", "respondusws");
    }
    $respondusws_course = $DB->get_record("course", array("id" => $respondusws_module->course));
    if ($respondusws_course === false) {
        print_error("coursemisconf");
    }
    $respondusws_cm = get_coursemodule_from_instance(
      "respondusws", $respondusws_module->id, $respondusws_course->id);
    if ($respondusws_cm === false) {
        print_error("invalidcoursemodule");
    }
} else {
    print_error("invalidcminstance", "respondusws");
}
$PAGE->set_url("$CFG->wwwroot/mod/respondusws/view.php",
  array("id" => $respondusws_cm->id));
require_login($respondusws_course, true, $respondusws_cm);
if (respondusws_floatcompare($CFG->version, 2013111800, 2) >= 0) {
    $respondusws_context = context_module::instance($respondusws_cm->id);
} else {
    $respondusws_context = get_context_instance(CONTEXT_MODULE, $respondusws_cm->id);
}
$PAGE->set_context($respondusws_context);
if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
    $respondusws_event_params = array(
        'context' => $respondusws_context,
        'objectid' => $respondusws_module->id
        );
    $respondusws_event = \mod_respondusws\event\course_module_viewed::create($respondusws_event_params);
    $respondusws_event->add_record_snapshot('course_modules', $respondusws_cm);
    $respondusws_event->add_record_snapshot('course', $respondusws_course);
    $respondusws_event->add_record_snapshot('respondusws', $respondusws_module);
    $respondusws_event->trigger();
} else {
    if ($respondusws_id) {
        add_to_log($respondusws_course->id, "respondusws", "view", "view.php?id=$respondusws_cm->id",
          "$respondusws_module->id", $respondusws_cm->id);
    } else {
        add_to_log($respondusws_course->id, "respondusws", "view", "view.php?a=$respondusws_module->id",
          "$respondusws_module->id");
    }
}
$respondusws_strmodule = get_string("modulename", "respondusws");
$respondusws_renderer_file = dirname(__FILE__) . "/renderer.php";
if (is_readable($respondusws_renderer_file)) {
    $respondusws_output = $PAGE->get_renderer("mod_respondusws");
} else {
    $respondusws_output = $OUTPUT;
}
$PAGE->set_title($respondusws_strmodule);
$PAGE->set_heading($respondusws_course->fullname);
echo $respondusws_output->header();
$respondusws_module->intro = trim($respondusws_module->intro);
if (!empty($respondusws_module->intro)) {
    echo $respondusws_output->box(format_module_intro("respondusws", $respondusws_module, $respondusws_cm->id),
      "generalbox", "intro");
} else {
    echo $respondusws_output->box("No module instance data currently available");
}
echo $respondusws_output->footer();
