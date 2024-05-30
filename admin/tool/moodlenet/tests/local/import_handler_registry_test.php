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

namespace tool_moodlenet\local;

use tool_moodlenet\local\import_handler_registry;
use tool_moodlenet\local\import_handler_info;
use tool_moodlenet\local\import_strategy_file;
use tool_moodlenet\local\import_strategy_link;
use tool_moodlenet\local\remote_resource;
use tool_moodlenet\local\url;

/**
 * Class tool_moodlenet_import_handler_registry_testcase, providing test cases for the import_handler_registry class.
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_handler_registry_test extends \advanced_testcase {

    /**
     * Test confirming the behaviour of get_resource_handlers_for_strategy with different params.
     */
    public function test_get_resource_handlers_for_strategy(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $ihr = new import_handler_registry($course, $teacher);
        $resource = new remote_resource(
            new \curl(),
            new url('http://example.org'),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );

        $handlers = $ihr->get_resource_handlers_for_strategy($resource, new import_strategy_file());
        $this->assertIsArray($handlers);
        foreach ($handlers as $handler) {
            $this->assertInstanceOf(import_handler_info::class, $handler);
        }
    }

    /**
     * Test confirming that the results are scoped to the provided user.
     */
    public function test_get_resource_handlers_for_strategy_user_scoping(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $studentihr = new import_handler_registry($course, $student);
        $teacherihr = new import_handler_registry($course, $teacher);
        $resource = new remote_resource(
            new \curl(),
            new url('http://example.org'),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );

        $this->assertEmpty($studentihr->get_resource_handlers_for_strategy($resource, new import_strategy_file()));
        $this->assertNotEmpty($teacherihr->get_resource_handlers_for_strategy($resource, new import_strategy_file()));
    }

    /**
     * Test confirming that we can find a unique handler based on the module and strategy name.
     */
    public function test_get_resource_handler_for_module_and_strategy(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $ihr = new import_handler_registry($course, $teacher);
        $resource = new remote_resource(
            new \curl(),
            new url('http://example.org'),
            (object) [
                'name' => 'Resource name',
                'description' => 'Resource description'
            ]
        );

        // Resource handles every file type, so we'll always be able to find that unique handler when looking.
        $handler = $ihr->get_resource_handler_for_mod_and_strategy($resource, 'resource', new import_strategy_file());
        $this->assertInstanceOf(import_handler_info::class, $handler);

        // URL handles every resource, so we'll always be able to find that unique handler when looking with a link strategy.
        $handler = $ihr->get_resource_handler_for_mod_and_strategy($resource, 'url', new import_strategy_link());
        $this->assertInstanceOf(import_handler_info::class, $handler);
        $this->assertEquals('url', $handler->get_module_name());
        $this->assertInstanceOf(import_strategy_link::class, $handler->get_strategy());
    }
}
