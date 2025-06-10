<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
$respondusws_stepslib_file = dirname(__FILE__) . "/backup_respondusws_stepslib.php";
require_once($respondusws_stepslib_file);
$respondusws_settingslib_file = dirname(__FILE__) . "/backup_respondusws_settingslib.php";
if (is_readable($respondusws_settingslib_file)) {
    require_once($respondusws_settingslib_file);
}
class backup_respondusws_activity_task extends backup_activity_task {
    protected function define_my_settings() {
    }
    protected function define_my_steps() {
        $this->add_step(new backup_respondusws_activity_structure_step(
          "respondusws_structure", "respondusws.xml")
          );
    }
    static public function encode_content_links($content) {
        global $CFG;
        $result = $content;
        $base = preg_quote($CFG->wwwroot, "/");
        $search = "/(" . $base . "\/mod\/respondusws\/index.php\?id\=)([0-9]+)/";
        $result = preg_replace($search, '$@RESPONDUSWSINDEX*$2@$', $result);
        $search = "/(" . $base . "\/mod\/respondusws\/view.php\?id\=)([0-9]+)/";
        $result = preg_replace($search, '$@RESPONDUSVIEWBYID*$2@$', $result);
        return $result;
    }
}
