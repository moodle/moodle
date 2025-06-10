<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
class restore_respondusws_activity_structure_step extends restore_activity_structure_step {
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value("userinfo");
        $paths[] = new restore_path_element("respondusws", "/activity/respondusws");
        if ($userinfo) {
            $paths[] = new restore_path_element("respondusws_authuser",
              "/activity/respondusws/authusers/authuser");
        }
        return $this->prepare_activity_structure($paths);
    }
    protected function process_respondusws($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $newitemid = $DB->insert_record("respondusws", $data);
        $this->apply_activity_instance($newitemid);
    }
    protected function process_respondusws_authuser($data) {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;
        if (!isset($data->authtoken)) {
            return;
        }
        $data->responduswsid = $this->get_new_parentid("respondusws");
        $data->userid = $this->get_mapping_id("user", $data->userid);
        $data->timeissued = $this->apply_date_offset($data->timeissued);
        $newitemid = $DB->insert_record("respondusws_auth_users", $data);
        $this->set_mapping("respondusws_authuser", $oldid, $newitemid);
    }
    protected function after_execute() {
        $this->add_related_files("mod_respondusws", "intro", null);
    }
}
