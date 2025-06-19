<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
require_once(dirname(dirname(dirname(__FILE__))) . "/config.php");
require_once("$CFG->dirroot/course/lib.php");
require_once(dirname(__FILE__) . "/lib.php");
defined("MOODLE_INTERNAL") || die();
$respondusws_id = required_param("id", PARAM_INT);
$respondusws_course = $DB->get_record("course", array("id" => $respondusws_id));
if ($respondusws_course === false) {
    print_error("invalidcourseid");
}
$PAGE->set_url("$CFG->wwwroot/mod/respondusws/index.php",
  array("id" => $respondusws_id));
require_course_login($respondusws_course);
$respondusws_dbman = $DB->get_manager();
if ($respondusws_dbman->table_exists("respondusws")) {
    $respondusws_instances = $DB->get_records("respondusws", array("course" => $respondusws_id), "id");
} else {
    $respondusws_instances = array();
}
if (count($respondusws_instances) == 0) {
    print_error("notinstalled", "respondusws");
}
$PAGE->set_pagelayout("incourse");
if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
    $respondusws_course_context = context_course::instance($respondusws_course->id);
    $respondusws_event_params = array(
        'context' => $respondusws_course_context
        );
    $respondusws_event = \mod_respondusws\event\course_module_instance_list_viewed::create($respondusws_event_params);
    $respondusws_event->add_record_snapshot("course", $respondusws_course);
    $respondusws_event->trigger();
} else {
    add_to_log($respondusws_course->id, "respondusws", "view all", "index.php?id=$respondusws_course->id");
}
$respondusws_strmodules = get_string("modulenameplural", "respondusws");
$respondusws_strsectionname = get_string("sectionname", "format_" . $respondusws_course->format);
$respondusws_strname = get_string("name");
$respondusws_strintro = get_string("moduleintro");
$respondusws_strlastmodified = get_string("lastmodified");
$respondusws_renderer_file = dirname(__FILE__) . "/renderer.php";
if (is_readable($respondusws_renderer_file)) {
    $respondusws_output = $PAGE->get_renderer("mod_respondusws");
} else {
    $respondusws_output = $OUTPUT;
}
$PAGE->set_title($respondusws_strmodules);
$PAGE->set_heading($respondusws_course->fullname);
$PAGE->navbar->add($respondusws_strmodules);
echo $respondusws_output->header();
$respondusws_modules = get_all_instances_in_course("respondusws", $respondusws_course);
if (!$respondusws_modules) {
    print_error("noinstances", "respondusws",
      "$CFG->dirroot/course/view.php?id=$respondusws_course->id");
}
$respondusws_usesections = course_format_uses_sections($respondusws_course->format);
if ($respondusws_usesections) {
    if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
        $modinfo = get_fast_modinfo($respondusws_course->id);
        $respondusws_sections = $modinfo->get_section_info_all();
    } else {
        $respondusws_sections = get_all_sections($respondusws_course->id);
    }
}
$respondusws_table = new html_table();
$respondusws_table->attributes["class"] = "generaltable_mod_index";
if ($respondusws_usesections) {
    $respondusws_table->head = array($respondusws_strsectionname, $respondusws_strname, $strintro);
    $respondusws_table->align = array("center", "left", "left");
} else {
    $respondusws_table->head = array($respondusws_strlastmodified, $respondusws_strname, $strintro);
    $respondusws_table->align = array("left", "left", "left");
}
$respondusws_modinfo = get_fast_modinfo($respondusws_course);
$respondusws_currentsection = "";
foreach ($respondusws_modules as $respondusws_module) {
    $respondusws_cm = $respondusws_modinfo->cms[$respondusws_module->coursemodule];
    if ($respondusws_usesections) {
        $respondusws_printsection = "";
        if ($respondusws_module->section !== $respondusws_currentsection) {
            if ($respondusws_module->section) {
                if (respondusws_floatcompare($CFG->version, 2012120300, 2) >= 0) {
                    $respondusws_printsection = get_section_name($respondusws_course,
                      $respondusws_module->section);
                } else {
                    $respondusws_printsection = get_section_name($respondusws_course,
                      $respondusws_sections[$respondusws_module->section]);
                }
            }
            if ($respondusws_currentsection !== "") {
                $respondusws_table->data[] = "hr";
            }
            $respondusws_currentsection = $respondusws_module->section;
        }
    } else {
        $respondusws_printsection = "<span class=\"smallinfo\">"
                      . userdate($respondusws_module->timemodified)
                      . "</span>";
    }
    $respondusws_class = "";
    if (!$respondusws_module->visible) {
        $respondusws_class = "class=\"dimmed\"";
    }
    $respondusws_table->data[] = array(
        $respondusws_printsection,
        "<a $respondusws_class href=\"view.php?id=$respondusws_cm->id\">"
        . format_string($respondusws_module->name)
        . "</a>",
        format_module_intro("respondusws", $respondusws_module, $respondusws_cm->id)
        );
}
echo html_writer::table($respondusws_table);
echo $respondusws_output->footer();
