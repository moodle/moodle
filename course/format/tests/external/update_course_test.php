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

namespace core_courseformat\external;

use stdClass;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the update_course class.
 *
 * @package    core_course
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\external\update_course
 */
class update_course_test extends \externallib_advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_state.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_stateactions.php');
    }

    /**
     * Test the webservice can execute a core state action (cm_state).
     *
     * @dataProvider execute_course_state_provider
     * @covers ::execute
     *
     * @param string $format the course format
     * @param string $action the state action name
     * @param array $expected the expected results
     * @param bool $expectexception if an exception should happen.
     * @param bool $assertdebug if an debug message should happen.
     */
    public function test_execute_course_state(
        string $format,
        string $action,
        array $expected,
        bool $expectexception,
        bool $assertdebug
    ) {

        $this->resetAfterTest();

        // Create a course with two activities.
        $course = $this->getDataGenerator()->create_course(['format' => $format]);
        $activity = $this->getDataGenerator()->create_module('book', ['course' => $course->id]);

        $this->setAdminUser();

        // Expect exception.
        if ($expectexception) {
            $this->expectException(moodle_exception::class);
        }

        // Execute course action.
        $results = json_decode(update_course::execute($action, $course->id, [$activity->cmid]));

        if ($assertdebug) {
            // Some course formats hasn't the renderer file, so a debugging message will be displayed.
            $this->assertDebuggingCalled();
        }

        // Check result.
        $this->assertCount($expected['count'], $results);

        $update = $this->find_update($results, $expected['action'], 'cm', $activity->cmid);
        $this->assertNotEmpty($update);
        if ($expected['visible'] === null) {
            $this->assertObjectNotHasAttribute('visible', $update->fields);
        } else {
            $this->assertEquals($expected['visible'], $update->fields->visible);
        }
    }

    /**
     * Data provider for test_execute_course_state
     *
     * @return array of testing scenarios
     */
    public function execute_course_state_provider(): array {
        return [
            'Execute a core state action (cm_state)' => [
                'format' => 'topics',
                'action' => 'cm_state',
                'expected' => [
                    'count' => 2,
                    'action' => 'put',
                    'visible' => 1,
                ],
                'expectexception' => false,
                'assertdebug' => false,
            ],
            'Formats can override core state actions' => [
                'format' => 'theunittest',
                'action' => 'cm_state',
                'expected' => [
                    'count' => 1,
                    'action' => 'create',
                    'visible' => 1,
                ],
                'expectexception' => false,
                'assertdebug' => true,
            ],
            'Formats can create new state actions' => [
                'format' => 'theunittest',
                'action' => 'format_do_something',
                'expected' => [
                    'count' => 1,
                    'action' => 'delete',
                    'visible' => null,
                ],
                'expectexception' => false,
                'assertdebug' => true,
            ],
            'Innexisting state action' => [
                'format' => 'topics',
                'action' => 'Wrong_State_Action_Name',
                'expected' => [],
                'expectexception' => true,
                'assertdebug' => false,
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
     * Test a wrong course id.
     *
     * @covers ::execute
     *
     */
    public function test_execute_wrong_courseid() {

        $this->resetAfterTest();

        // Create a course with two activities.
        $course = $this->getDataGenerator()->create_course(['format' => 'topics']);
        $activity = $this->getDataGenerator()->create_module('book', ['course' => $course->id]);

        $this->setAdminUser();

        // Expect exception.
        $this->expectException(moodle_exception::class);

        // Execute course action.
        $results = json_decode(update_course::execute('cm_state', $course->id + 1, [$activity->cmid]));
    }

    /**
     * Test target params are passed to the state actions.
     *
     * @covers ::execute
     */
    public function test_execute_target_params() {

        $this->resetAfterTest();

        // Create a course with two activities.
        $course = $this->getDataGenerator()->create_course(['format' => 'theunittest', 'numsections' => 2]);
        $activity = $this->getDataGenerator()->create_module('book', ['course' => $course->id]);

        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(1);

        $this->setAdminUser();

        // Execute action with targetsectionid.
        $results = json_decode(update_course::execute('targetsection_test', $course->id, [], $section->id));
        $this->assertDebuggingCalled();
        $this->assertCount(1, $results);
        $update = $this->find_update($results, 'put', 'section', $section->id);
        $this->assertNotEmpty($update);

        // Execute action with targetcmid.
        $results = json_decode(update_course::execute('targetcm_test', $course->id, [], null, $activity->cmid));
        $this->assertDebuggingCalled();
        $this->assertCount(1, $results);
        $update = $this->find_update($results, 'put', 'cm', $activity->cmid);
        $this->assertNotEmpty($update);
    }
}
