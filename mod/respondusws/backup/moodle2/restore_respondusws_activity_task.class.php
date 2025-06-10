<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
$respondusws_stepslib_file = dirname(__FILE__) . "/restore_respondusws_stepslib.php";
require_once($respondusws_stepslib_file);
class restore_respondusws_activity_task extends restore_activity_task {
    protected function define_my_settings() {
    }
    protected function define_my_steps() {
        $this->add_step(new restore_respondusws_activity_structure_step(
          "respondusws_structure", "respondusws.xml")
          );
    }
    static public function define_decode_contents() {
        $contents = array();
        $contents[] = new restore_decode_content("respondusws", array("intro"),
          "respondusws");
        return $contents;
    }
    static public function define_decode_rules() {
        $rules = array();
        $rules[] = new restore_decode_rule("RESPONDUSVIEWBYID",
          "/mod/respondusws/view.php?id=$1", "course_module");
        $rules[] = new restore_decode_rule("RESPONDUSWSINDEX",
          "/mod/respondusws/index.php?id=$1", "course");
        return $rules;
    }
    static public function define_restore_log_rules() {
        $rules = array();
        $rules[] = new restore_log_rule("respondusws", "add",
          "view.php?id={course_module}", "{respondusws}");
        $rules[] = new restore_log_rule("respondusws", "update",
          "view.php?id={course_module}", "{respondusws}");
        $rules[] = new restore_log_rule("respondusws", "view",
          "view.php?id={course_module}", "{respondusws}");
        $rules[] = new restore_log_rule("respondusws", "publish",
          "index.php?id={course}", "qcatid={question_category}");
        $rules[] = new restore_log_rule("respondusws", "retrieve",
          "index.php?id={course}", "qcatid={question_category}");
        $rules[] = new restore_log_rule("respondusws", "retrieve",
          "index.php?id={course}", "quizid={quiz}");
        return $rules;
    }
    static public function define_restore_log_rules_for_course() {
        $rules = array();
        $rules[] = new restore_log_rule("respondusws", "view all",
          "index.php?id={course}", null);
        return $rules;
    }
}
