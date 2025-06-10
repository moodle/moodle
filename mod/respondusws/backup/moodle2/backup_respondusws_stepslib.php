<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
class backup_respondusws_activity_structure_step extends backup_activity_structure_step {
    protected function define_structure() {
        $userinfo = $this->get_setting_value("userinfo");
        $respondusws = new backup_nested_element(
          "respondusws", array("id"), array(
          "course", "name", "intro", "introformat", "timecreated",
          "timemodified"
          ));
        $authusers = new backup_nested_element("authusers");
        $authuser = new backup_nested_element(
          "authuser", array("id"), array(
          "responduswsid", "userid", "authtoken", "timeissued"
        ));
        $respondusws->add_child($authusers);
        $authusers->add_child($authuser);
        $respondusws->set_source_table("respondusws",
          array("id" => backup::VAR_ACTIVITYID));
        if ($userinfo) {
            $authuser->set_source_table("respondusws_auth_users",
              array("responduswsid" => backup::VAR_PARENTID));
        }
        $authuser->annotate_ids("user", "userid");
        $respondusws->annotate_files("mod_respondusws", "intro", null);
        return $this->prepare_activity_structure($respondusws);
    }
}
