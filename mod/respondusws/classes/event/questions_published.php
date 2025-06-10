<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
namespace mod_respondusws\event;
defined('MOODLE_INTERNAL') || die();
class questions_published extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    public static function get_name() {
        return get_string('eventquestionspublished', 'mod_respondusws');
    }
    public function get_description() {
        return "The user with id '$this->userid' published questions from Respondus to the question category with id '"
            . $this->other['qcatid'] . "'.";
    }
    public function get_url() {
        return null;
    }
    public function get_legacy_logdata() {
        return array(
            $this->courseid, 'respondusws', 'publish',
            "index.php?id=$this->courseid",
            "qcatid=" . $this->other['qcatid']
            );
    }
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->userid)) {
            throw new \coding_exception('The user id must be set.');
        }
        if (!isset($this->courseid)) {
            throw new \coding_exception('The course id must be set.');
        }
        if (!isset($this->other['qcatid'])) {
            throw new \coding_exception('The question category id must be set.');
        }
    }
}
