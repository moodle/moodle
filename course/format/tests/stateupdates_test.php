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

namespace core_courseformat;

use stdClass;

/**
 * Tests for the stateupdates class.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\stateupdates
 */
class stateupdates_test extends \advanced_testcase {

    /**
     * Test for add_course_put.
     *
     * @dataProvider add_course_put_provider
     * @covers ::add_course_put
     *
     * @param string $role the user role in the course
     */
    public function test_add_course_put(string $role): void {
        global $PAGE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics']);

        // Create and enrol user using given role.
        if ($role == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($role != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            }
            $this->setUser($user);
        }

        // Initialise stateupdates.
        $format = course_get_format($course);
        $updates = new stateupdates($format);

        // Get the expected export.
        $renderer = $format->get_renderer($PAGE);
        $stateclass = $format->get_output_classname("state\\course");
        $currentstate = new $stateclass($format);
        $expected = $currentstate->export_for_template($renderer);

        $updates->add_course_put();

        $updatelist = $updates->jsonSerialize();
        $this->assertCount(1, $updatelist);

        $update = array_pop($updatelist);
        $this->assertEquals('put', $update->action);
        $this->assertEquals('course', $update->name);
        $this->assertEquals($expected, $update->fields);
    }

    /**
     * Data provider for test_add_course_put.
     *
     * @return array testing scenarios
     */
    public function add_course_put_provider() {
        return [
            'Admin role' => [
                'admin',
            ],
            'Teacher role' => [
                'editingteacher',
            ],
            'Student role' => [
                'student',
            ],
        ];
    }

    /**
     * Helper methods to find a specific update in the updadelist.
     *
     * @param array $updatelist the update list
     * @param string $action the action to find
     * @param string $name the element name to find
     * @param int $identifier the element id value
     * @return stdClass|null the object found, if any.
     */
    private function find_update(
        array $updatelist,
        string $action,
        string $name,
        int $identifier
    ): ?stdClass {
        foreach ($updatelist as $update) {
            if ($update->action != $action || $update->name != $name) {
                continue;
            }
            if (!isset($update->fields->id)) {
                continue;
            }
            if ($update->fields->id == $identifier) {
                return $update;
            }
        }
        return null;
    }

    /**
     * Add track about a section state update.
     *
     * @dataProvider add_section_provider
     * @covers ::add_section_create
     * @covers ::add_section_remove
     * @covers ::add_section_put
     *
     * @param string $action the action name
     * @param string $role the user role name
     * @param array $expected the expected results
     */
    public function test_add_section(string $action, string $role, array $expected): void {
        global $PAGE, $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2, 'format' => 'topics']);

        // Set section 2 hidden.
        set_section_visible($course->id, 2, 0);

        // Create and enrol user using given role.
        if ($role == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($role != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            }
            $this->setUser($user);
        }

        // Initialise stateupdates.
        $format = course_get_format($course);
        $updates = new stateupdates($format);

        $modinfo = $format->get_modinfo();

        // Get the expected export.
        $renderer = $format->get_renderer($PAGE);
        $stateclass = $format->get_output_classname("state\\section");

        // Execute method for both sections.
        $method = "add_section_{$action}";
        $sections = $modinfo->get_section_info_all();
        foreach ($sections as $section) {
            $updates->$method($section->id);
        }

        $updatelist = $updates->jsonSerialize();
        $this->assertCount(count($expected), $updatelist);

        foreach ($expected as $sectionnum) {
            $section = $sections[$sectionnum];
            $currentstate = new $stateclass($format, $section);
            $expected = $currentstate->export_for_template($renderer);

            $update = $this->find_update($updatelist, $action, 'section', $section->id);
            $this->assertEquals($action, $update->action);
            $this->assertEquals('section', $update->name);
            // Delete does not provide all fields.
            if ($action == 'remove') {
                $this->assertEquals($section->id, $update->fields->id);
            } else {
                $this->assertEquals($expected, $update->fields);
            }
        }
    }

    /**
     * Data provider for test_add_section.
     *
     * @return array testing scenarios
     */
    public function add_section_provider(): array {
        return array_merge(
            $this->add_section_provider_helper('put'),
            $this->add_section_provider_helper('create'),
            $this->add_section_provider_helper('remove'),
        );
    }

    /**
     * Helper for add_section_provider scenarios.
     *
     * @param string $action the action to perform
     * @return array testing scenarios
     */
    private function add_section_provider_helper(string $action): array {
        // Delete does not depends on user permissions.
        if ($action == 'remove') {
            $studentsections = [0, 1, 2];
        } else {
            $studentsections = [0, 1];
        }

        return [
            "$action admin role" => [
                'action' => $action,
                'role' => 'admin',
                'expected' => [0, 1, 2],
            ],
            "$action teacher role" => [
                'action' => $action,
                'role' => 'editingteacher',
                'expected' => [0, 1, 2],
            ],
            "$action student role" => [
                'action' => $action,
                'role' => 'student',
                'expected' => $studentsections,
            ],
        ];
    }


    /**
     * Add track about a course module state update.
     *
     * @dataProvider add_cm_provider
     * @covers ::add_cm_put
     * @covers ::add_cm_create
     * @covers ::add_cm_remove
     *
     * @param string $action the action name
     * @param string $role the user role name
     * @param array $expected the expected results
     */
    public function test_add_cm(string $action, string $role, array $expected): void {
        global $PAGE, $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2, 'format' => 'topics']);

        // Set section 2 hidden.
        set_section_visible($course->id, 2, 0);

        // Create 2 activities on each section.
        $activities = [];
        $activities[] = $this->getDataGenerator()->create_module(
            'book',
            ['course' => $course->id],
            ['section' => 1, 'visible' => true]
        );
        $activities[] = $this->getDataGenerator()->create_module(
            'book',
            ['course' => $course->id],
            ['section' => 1, 'visible' => false]
        );
        $activities[] = $this->getDataGenerator()->create_module(
            'book',
            ['course' => $course->id],
            ['section' => 2, 'visible' => true]
        );
        $activities[] = $this->getDataGenerator()->create_module(
            'book',
            ['course' => $course->id],
            ['section' => 2, 'visible' => false]
        );

        // Create and enrol user using given role.
        if ($role == 'admin') {
            $this->setAdminUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            if ($role != 'unenroled') {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            }
            $this->setUser($user);
        }

        // Initialise stateupdates.
        $format = course_get_format($course);
        $updates = new stateupdates($format);

        $modinfo = $format->get_modinfo();

        // Get the expected export.
        $renderer = $format->get_renderer($PAGE);
        $stateclass = $format->get_output_classname("state\\cm");

        // Execute method for both sections.
        $method = "add_cm_{$action}";

        foreach ($activities as $activity) {
            $updates->$method($activity->cmid);
        }

        $updatelist = $updates->jsonSerialize();
        $this->assertCount(count($expected), $updatelist);

        foreach ($expected as $cmnum) {
            $activity = $activities[$cmnum];

            $cm = $modinfo->get_cm($activity->cmid);
            $section = $modinfo->get_section_info($cm->sectionnum);

            $currentstate = new $stateclass($format, $section, $cm);
            $expected = $currentstate->export_for_template($renderer);

            $update = $this->find_update($updatelist, $action, 'cm', $cm->id);
            $this->assertEquals($action, $update->action);
            $this->assertEquals('cm', $update->name);
            // Delete does not provide all fields.
            if ($action == 'remove') {
                $this->assertEquals($cm->id, $update->fields->id);
            } else {
                $this->assertEquals($expected, $update->fields);
            }
        }
    }

    /**
     * Data provider for test_add_cm.
     *
     * @return array testing scenarios
     */
    public function add_cm_provider(): array {
        return array_merge(
            $this->add_cm_provider_helper('put'),
            $this->add_cm_provider_helper('create'),
            $this->add_cm_provider_helper('remove'),
        );
    }

    /**
     * Helper for add_cm_provider scenarios.
     *
     * @param string $action the action to perform
     * @return array testing scenarios
     */
    private function add_cm_provider_helper(string $action): array {
        // Delete does not depends on user permissions.
        if ($action == 'remove') {
            $studentcms = [0, 1, 2, 3];
        } else {
            $studentcms = [0];
        }

        return [
            "$action admin role" => [
                'action' => $action,
                'role' => 'admin',
                'expected' => [0, 1, 2, 3],
            ],
            "$action teacher role" => [
                'action' => $action,
                'role' => 'editingteacher',
                'expected' => [0, 1, 2, 3],
            ],
            "$action student role" => [
                'action' => $action,
                'role' => 'student',
                'expected' => $studentcms,
            ],
        ];
    }

    /**
     * Test components can add data to delegated section state updates.
     * @covers ::add_section_put
     */
    public function test_put_section_state_extra_updates(): void {
        global $DB, $CFG;
        $this->resetAfterTest();

        require_once($CFG->libdir . '/tests/fixtures/sectiondelegatetest.php');

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );

        // The test component section delegate will add the activity cm info into the state.
        $section = formatactions::section($course)->create_delegated('test_component', $activity->cmid);

        $format = course_get_format($course);
        $updates = new \core_courseformat\stateupdates($format);

        $updates->add_section_put($section->id);

        $data = $updates->jsonSerialize();

        $this->assertCount(2, $data);

        $sectiondata = $data[0];
        $this->assertEquals('section', $sectiondata->name);
        $this->assertEquals('put', $sectiondata->action);
        $this->assertEquals($section->id, $sectiondata->fields->id);

        $cmdata = $data[1];
        $this->assertEquals('cm', $cmdata->name);
        $this->assertEquals('put', $cmdata->action);
        $this->assertEquals($activity->cmid, $cmdata->fields->id);
    }
}
