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

declare(strict_types=1);

namespace core_courseformat\external;

use moodle_exception;
use stdClass;

/**
 * Tests for the create_module class.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2024 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(create_module::class)]
final class create_module_test extends \core_external\tests\externallib_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        self::load_fixture('courseformat', 'format_theunittest.php');
        self::load_fixture('courseformat', 'format_theunittest_output_course_format_state.php');
        self::load_fixture('courseformat', 'format_theunittest_stateactions.php');
    }

    /**
     * Test the webservice can execute the create_module action.
     */
    public function test_execute(): void {
        $this->resetAfterTest();

        $modname = 'subsection';
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin($modname, 1);

        // Create a course with an activity.
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $activity = $this->getDataGenerator()->create_module('book', ['course' => $course->id]);
        $targetsection = get_fast_modinfo($course->id)->get_section_info(1);

        $this->setAdminUser();

        // Execute course action.
        $results = json_decode(create_module::execute((int)$course->id, $modname, (int)$targetsection->id, (int)$activity->id));
        $this->assertDebuggingCalled();

        // Check result.
        $cmupdate = $this->find_update_by_fieldname($results, 'put', 'cm', get_string('quickcreatename', 'mod_' . $modname));
        $this->assertNotEmpty($cmupdate);
        $this->assertEquals($modname, $cmupdate->fields->module);
        $this->assertEquals($targetsection->id, $cmupdate->fields->sectionnumber);
    }

    /**
     * Test the webservice can execute the create_module action with a format override.
     */
    public function test_execute_with_format_override(): void {
        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course(['format' => 'theunittest', 'numsections' => 1, 'initsections' => 1]);
        $targetsection = get_fast_modinfo($course->id)->get_section_info(1);

        $this->setAdminUser();

        // Execute course action.
        $modname = 'subsection';
        $results = json_decode(create_module::execute((int)$course->id, $modname, (int)$targetsection->id));

        // Some course formats doesn't have the renderer file, so a debugging message will be displayed.
        $this->assertDebuggingCalled();

        // Check result.
        $this->assertEmpty($results);
    }

    /**
     * Test the webservice can execute the create_module action with an invalid module.
     */
    public function test_execute_with_invalid_module(): void {
        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $targetsection = get_fast_modinfo($course->id)->get_section_info(1);

        $this->setAdminUser();

        // Expect exception. Book module doesn't support quickcreate feature.
        $this->expectException(moodle_exception::class);

        // Execute course action.
        $modname = 'book';
        create_module::execute((int)$course->id, $modname, (int)$targetsection->id);
        $this->assertDebuggingCalled();
    }

    /**
     * Helper methods to find a specific update in the updadelist.
     *
     * @param array $updatelist the update list
     * @param string $action the action to find
     * @param string $name the element name to find
     * @param string $fieldname the element identifiername
     * @return stdClass|null the object found, if any.
     */
    private function find_update_by_fieldname(
        array $updatelist,
        string $action,
        string $name,
        string $fieldname,

    ): ?stdClass {
        foreach ($updatelist as $update) {
            if ($update->action != $action || $update->name != $name) {
                continue;
            }
            if (!isset($update->fields->name)) {
                continue;
            }
            if ($update->fields->name == $fieldname) {
                return $update;
            }
        }
        return null;
    }
}
