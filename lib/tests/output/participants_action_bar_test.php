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

namespace core\output;
use ReflectionMethod;

/**
 * Participants tertiary navigation renderable test
 *
 * @package     core
 * @category    output
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_action_bar_test extends \advanced_testcase {

    /**
     * Test the get_content_for_select function
     *
     * @dataProvider get_content_for_select_provider
     * @param string $type Whether we are checking content in the course/module
     * @param int    $expectedcount Expected number of 1st level tertiary items
     * @param array  $expecteditems Expected keys of the 1st level tertiary items.
     */
    public function test_get_content_for_select($type, $expectedcount, $expecteditems): void {
        global $PAGE;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id
        ]);
        if ($type == 'course') {
            $context = \context_course::instance($course->id);
            $url = new \moodle_url('/course/view.php', ['id' => $course->id]);
        } else {
            $url = new \moodle_url('/mod/assign/view.php', ['id' => $module->id]);
            $context = \context_module::instance($module->cmid);
            $cm = get_coursemodule_from_instance('assign', $module->id, $course->id);
            $PAGE->set_cm($cm);
        }

        $this->setAdminUser();
        $PAGE->set_url($url);
        $PAGE->set_context($context);
        $output = new participants_action_bar($course, $PAGE, null);
        $method = new ReflectionMethod('\core\output\participants_action_bar', 'get_content_for_select');
        $renderer = $PAGE->get_renderer('core');

        $response = $method->invoke($output, $renderer);
        $this->assertCount($expectedcount, $response);
        $this->assertSame($expecteditems, array_keys(array_merge(...$response)));
    }

    /**
     * Provider for test_get_content_for_select
     * @return array[]
     */
    public static function get_content_for_select_provider(): array {
        return [
            'Get dropdown content when in a course context' => [
                'course', 3, ['Enrolments', 'Groups', 'Permissions']
            ],
            'Get dropdown content when in a module context' => [
                'module', 1, ['Permissions']
            ]
        ];
    }
}
