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

namespace core_badges\external;

use core_badges_generator;
use core_badges\badge;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/badgeslib.php');

/**
 * Tests for external function disable_badges.
 *
 * @package    core_badges
 * @copyright  2024 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.5
 * @covers     \core_badges\external\disable_badges
 */
final class disable_badges_test extends \core_external\tests\externallib_testcase {
    /**
     * Test execute method.
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->prepare_test_data();

        $this->assertTrue($data['sitebadge']->is_active());
        $this->assertTrue($data['coursebadge']->is_active());

        $result = disable_badges::execute([
            $data['sitebadge']->id,
            $data['coursebadge']->id,
        ]);
        $result = \core_external\external_api::clean_returnvalue(disable_badges::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertEmpty($result['warnings']);

        $sitebadge = new badge($data['sitebadge']->id);
        $coursebadge = new badge($data['coursebadge']->id);
        $this->assertFalse($sitebadge->is_active());
        $this->assertFalse($coursebadge->is_active());
    }

    /**
     * Test execute method when badges are disabled.
     */
    public function test_execute_badgesdisabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Disable course badges.
        set_config('enablebadges', 0);

        $data = $this->prepare_test_data();

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('badgesdisabled', 'core_badges'));
        $result = disable_badges::execute([
            $data['sitebadge']->id,
            $data['coursebadge']->id,
        ]);
        $result = \core_external\external_api::clean_returnvalue(disable_badges::execute_returns(), $result);
    }

    /**
     * Test execute method when course badges are disabled.
     */
    public function test_execute_coursebadgesdisabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Disable course badges.
        set_config('badges_allowcoursebadges', 0);

        $data = $this->prepare_test_data();

        $this->assertTrue($data['sitebadge']->is_active());
        $this->assertTrue($data['coursebadge']->is_active());

        $result = disable_badges::execute([
            $data['sitebadge']->id,
            $data['coursebadge']->id,
        ]);
        $result = \core_external\external_api::clean_returnvalue(disable_badges::execute_returns(), $result);
        // Course badge can't be disabled because course badges are disabled.
        $this->assertFalse($result['result']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals($data['coursebadge']->id, $result['warnings'][0]['item']);
        $this->assertEquals('coursebadgesdisabled', $result['warnings'][0]['warningcode']);

        $sitebadge = new badge($data['sitebadge']->id);
        $coursebadge = new badge($data['coursebadge']->id);
        $this->assertFalse($sitebadge->is_active());
        $this->assertTrue($coursebadge->is_active());
    }

    /**
     * Test execute method when the user doesn't have the capability to disable badges.
     */
    public function test_execute_without_capability(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->prepare_test_data();
        $this->setUser($data['teacher']);

        $this->assertTrue($data['sitebadge']->is_active());
        $this->assertTrue($data['coursebadge']->is_active());

        $result = disable_badges::execute([
            $data['sitebadge']->id,
            $data['coursebadge']->id,
        ]);
        $result = \core_external\external_api::clean_returnvalue(disable_badges::execute_returns(), $result);
        // Teacher doesn't have capability to disable site badges.
        $this->assertFalse($result['result']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals($data['sitebadge']->id, $result['warnings'][0]['item']);
        $this->assertEquals('nopermissions', $result['warnings'][0]['warningcode']);

        $sitebadge = new badge($data['sitebadge']->id);
        $coursebadge = new badge($data['coursebadge']->id);
        $this->assertTrue($sitebadge->is_active());
        $this->assertFalse($coursebadge->is_active());
    }

    /**
     * Test execute method when the badge is already disabled.
     */
    public function test_execute_disabledisabledbadge(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $data = $this->prepare_test_data();

        $this->assertTrue($data['coursebadge']->is_active());
        $data['coursebadge']->set_status(BADGE_STATUS_INACTIVE);
        $this->assertFalse($data['coursebadge']->is_active());

        $result = disable_badges::execute([
            $data['coursebadge']->id,
        ]);
        $result = \core_external\external_api::clean_returnvalue(disable_badges::execute_returns(), $result);
        // Disabled badges can be disabled again.
        $this->assertTrue($result['result']);
        $this->assertEmpty($result['warnings']);

        $coursebadge = new badge($data['coursebadge']->id);
        $this->assertFalse($coursebadge->is_active());
    }

    /**
     * Prepare the test, creating a few users and badges.
     *
     * @return array Test data.
     */
    private function prepare_test_data(): array {
        global $DB;

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();

        // Create users and enrolments.
        $student1 = $this->getDataGenerator()->create_and_enrol($course);
        $student2 = $this->getDataGenerator()->create_and_enrol($course);
        $teacher  = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');
        $sitebadge = $generator->create_badge([
            'name' => 'Site badge',
            'description' => 'Site badge',
            'status' => BADGE_STATUS_ACTIVE,
        ]);
        $coursebadge = $generator->create_badge([
            'name' => 'Course badge',
            'description' => 'Course badge',
            'type' => BADGE_TYPE_COURSE,
            'courseid' => $course->id,
            'status' => BADGE_STATUS_ACTIVE,
        ]);

        // Create criteria for manually awarding by role.
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $generator->create_criteria(['badgeid' => $sitebadge->id, 'roleid' => $managerrole]);
        $generator->create_criteria(['badgeid' => $coursebadge->id, 'roleid' => $managerrole]);

        // Issue badges to student1.
        $sitebadge->issue($student1->id, true);
        $coursebadge->issue($student1->id, true);

        return [
            'course'          => $course,
            'student1'        => $student1,
            'student2'        => $student2,
            'teacher'         => $teacher,
            'sitebadge'       => $sitebadge,
            'coursebadge'     => $coursebadge,
        ];
    }
}
