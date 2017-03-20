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
 * Test plugin enrollib parts.
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2017 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test plugin enrollib parts.
 *
 * @package    core
 * @category   test
 * @copyright  2017 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_enrollib_special_testcase extends advanced_testcase {

    /**
     * Confirms that timemodified field was updated after modification of user enrollment
     */
    public function test_enrollment_update_timemodified() {
        global $DB;

        $this->resetAfterTest(true);
        $datagen = $this->getDataGenerator();

        /** @var enrol_manual_plugin $manualplugin */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotNull($manualplugin);

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $course = $datagen->create_course();
        $user = $datagen->create_user();

        $instanceid = null;
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $inst) {
            if ($inst->enrol == 'manual') {
                $instanceid = (int)$inst->id;
                break;
            }
        }
        if (empty($instanceid)) {
            $instanceid = $manualplugin->add_default_instance($course);
            if (empty($instanceid)) {
                $instanceid = $manualplugin->add_instance($course);
            }
        }
        $this->assertNotNull($instanceid);

        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $manualplugin->enrol_user($instance, $user->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $userenrolorig = (int)$DB->get_field(
            'user_enrolments',
            'timemodified',
            ['enrolid' => $instance->id, 'userid' => $user->id],
            MUST_EXIST
        );
        $this->waitForSecond();
        $this->waitForSecond();
        $manualplugin->update_user_enrol($instance, $user->id, ENROL_USER_SUSPENDED);
        $userenrolpost = (int)$DB->get_field(
            'user_enrolments',
            'timemodified',
            ['enrolid' => $instance->id, 'userid' => $user->id],
            MUST_EXIST
        );

        $this->assertGreaterThan($userenrolorig, $userenrolpost);
    }

}
