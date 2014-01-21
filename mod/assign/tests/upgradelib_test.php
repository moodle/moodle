<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for (some of) mod/assign/upgradelib.php.
 *
 * @package    mod_assign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/upgradelib.php');
require_once($CFG->dirroot . '/mod/assignment/lib.php');
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Unit tests for (some of) mod/assign/upgradelib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_upgradelib_testcase extends mod_assign_base_testcase {

    public function test_upgrade_upload_assignment() {
        global $DB, $CFG;

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'upload');
        $assignment = $generator->create_instance($params);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $CFG->usecomments = $commentconfig;
        course_delete_module($cm->id);
    }

    public function test_upgrade_uploadsingle_assignment() {
        global $DB, $CFG;

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'uploadsingle');
        $assignment = $generator->create_instance($params);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $CFG->usecomments = $commentconfig;
        course_delete_module($cm->id);
    }

    public function test_upgrade_onlinetext_assignment() {
        global $DB, $CFG;

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'online');
        $assignment = $generator->create_instance($params);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $CFG->usecomments = $commentconfig;
        course_delete_module($cm->id);
    }

    public function test_upgrade_offline_assignment() {
        global $DB, $CFG;

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        $this->setUser($this->editingteachers[0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $params = array('course'=>$this->course->id,
                        'assignmenttype'=>'offline');
        $assignment = $generator->create_instance($params);

        $this->setAdminUser();
        $log = '';
        $upgrader = new assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->id, $log));
        $record = $DB->get_record('assign', array('course'=>$this->course->id));

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = context_module::instance($cm->id);

        $assign = new assign($context, $cm, $this->course);

        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('comments');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_submission_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $this->assertNotEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertEmpty($plugin->is_enabled());
        $plugin = $assign->get_feedback_plugin_by_type('offline');
        $this->assertEmpty($plugin->is_enabled());

        $CFG->usecomments = $commentconfig;
        course_delete_module($cm->id);
    }
}
