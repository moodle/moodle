<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
namespace mod_respondusws\event;
defined('MOODLE_INTERNAL') || die();
class questions_retrieved extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }
    public static function get_name() {
        return get_string('eventquestionsretrieved', 'mod_respondusws');
    }
    public function get_description() {
        if (isset($this->other['quizcmid'])) {
            return "The user with id '$this->userid' retrieved questions from the quiz with the course module id '"
                . $this->other['quizcmid'] . "' into Respondus.";
        } else if (isset($this->other['qcatid'])) {
            return "The user with id '$this->userid' retrieved questions from the question category with id '"
                . $this->other['qcatid'] . "' into Respondus.";
        }
    }
    public function get_url() {
        return null;
    }
    public function get_legacy_logdata() {
        if (isset($this->other['quizcmid'])) {
            return array(
                $this->courseid, 'respondusws', 'retrieve',
                "index.php?id=$this->courseid",
                "quizid=" . $this->other['quizcmid']
                );
        } else if (isset($this->other['qcatid'])) {
            return array(
                $this->courseid, 'respondusws', 'retrieve',
                "index.php?id=$this->courseid",
                "qcatid=" . $this->other['qcatid']
                );
        }
    }
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->userid)) {
            throw new \coding_exception('The user id must be set.');
        }
        if (!isset($this->courseid)) {
            throw new \coding_exception('The course id must be set.');
        }
        if (!isset($this->other['quizcmid']) && !isset($this->other['qcatid'])) {
            throw new \coding_exception('Either the quiz course module id or the question category id must be set.');
        }
    }
}
