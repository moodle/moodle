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

namespace mod_assign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/upgradelib.php');
require_once($CFG->dirroot . '/mod/assignment/lib.php');

/**
 * Unit tests for (some of) mod/assign/upgradelib.php.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgradelib_test extends \advanced_testcase {

    /**
     * Data provider for assignment upgrade.
     *
     * @return  array
     */
    public function assignment_upgrade_provider() {
        return [
            'upload' => [
                'type' => 'upload',
                'submissionplugins' => [
                    'onlinetext' => true,
                    'comments' => true,
                    'file' => false,
                ],
                'feedbackplugins' => [
                    'comments' => false,
                    'file' => false,
                    'offline' => true,
                ],
            ],
            'uploadsingle' => [
                'type' => 'uploadsingle',
                'submissionplugins' => [
                    'onlinetext' => true,
                    'comments' => true,
                    'file' => false,
                ],
                'feedbackplugins' => [
                    'comments' => false,
                    'file' => false,
                    'offline' => true,
                ],
            ],
            'online' => [
                'type' => 'online',
                'submissionplugins' => [
                    'onlinetext' => false,
                    'comments' => true,
                    'file' => true,
                ],
                'feedbackplugins' => [
                    'comments' => false,
                    'file' => true,
                    'offline' => true,
                ],
            ],
            'offline' => [
                'type' => 'offline',
                'submissionplugins' => [
                    'onlinetext' => true,
                    'comments' => true,
                    'file' => true,
                ],
                'feedbackplugins' => [
                    'comments' => false,
                    'file' => true,
                    'offline' => true,
                ],
            ],
        ];
    }

    /**
     * Test assigment upgrade.
     *
     * @dataProvider assignment_upgrade_provider
     * @param   string  $type The type of assignment
     * @param   array   $plugins Which plugins shuld or shoudl not be enabled
     */
    public function test_upgrade_assignment($type, $plugins) {
        global $DB, $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        // Create the old assignment.
        $this->setUser($teacher);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assignment');
        $assignment = $generator->create_instance([
                'course' => $course->id,
                'assignmenttype' => $type,
            ]);

        // Run the upgrade.
        $this->setAdminUser();
        $log = '';
        $upgrader = new \assign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_assignment($assignment->id, $log));
        $record = $DB->get_record('assign', ['course' => $course->id]);

        $cm = get_coursemodule_from_instance('assign', $record->id);
        $context = \context_module::instance($cm->id);

        $assign = new \assign($context, $cm, $course);

        foreach ($plugins as $plugin => $isempty) {
            $plugin = $assign->get_submission_plugin_by_type($plugin);
            $this->assertEquals($isempty, empty($plugin->is_enabled()));
        }
    }
}
