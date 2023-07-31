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

namespace mod_qbassign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
require_once($CFG->dirroot . '/mod/qbassign/upgradelib.php');
require_once($CFG->dirroot . '/mod/qbassignment/lib.php');

/**
 * Unit tests for (some of) mod/qbassign/upgradelib.php.
 *
 * @package    mod_qbassign
 * @category   test
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgradelib_test extends \advanced_testcase {

    /**
     * Data provider for qbassignment upgrade.
     *
     * @return  array
     */
    public function qbassignment_upgrade_provider() {
        return [
            'upload' => [
                'type' => 'upload',
                'submissionplugins' => [
                    'onlinetex' => true,
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
                    'onlinetex' => true,
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
                    'onlinetex' => false,
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
                    'onlinetex' => true,
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
     * @dataProvider qbassignment_upgrade_provider
     * @param   string  $type The type of qbassignment
     * @param   array   $plugins Which plugins shuld or shoudl not be enabled
     */
    public function test_upgrade_qbassignment($type, $plugins) {
        global $DB, $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        // Create the old qbassignment.
        $this->setUser($teacher);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassignment');
        $qbassignment = $generator->create_instance([
                'course' => $course->id,
                'qbassignmenttype' => $type,
            ]);

        // Run the upgrade.
        $this->setAdminUser();
        $log = '';
        $upgrader = new \qbassign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_qbassignment($qbassignment->id, $log));
        $record = $DB->get_record('qbassign', ['course' => $course->id]);

        $cm = get_coursemodule_from_instance('qbassign', $record->id);
        $context = \context_module::instance($cm->id);

        $qbassign = new \qbassign($context, $cm, $course);

        foreach ($plugins as $plugin => $isempty) {
            $plugin = $qbassign->get_submission_plugin_by_type($plugin);
            $this->assertEquals($isempty, empty($plugin->is_enabled()));
        }
    }
}
