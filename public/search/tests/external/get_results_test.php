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

namespace core_search\external;

use core_external\external_api;

/**
 * Tests for the get_results external function.
 *
 * @package    core_search
 * @category   test
 * @copyright  2023 Juan Leyva (juan@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_search\external\get_results
 */
final class get_results_test extends \core_external\tests\externallib_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * test external api
     * @covers ::execute
     * @return void
     */
    public function test_external_get_results(): void {

        set_config('enableglobalsearch', true);
        set_config('searchengine', 'simpledb');

        $this->setAdminUser();

        // Test search not returning anything (nothing in the index yet).
        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('one'));
        $this->assertEquals(0, $return['totalcount']);

        // Create an index of searchable things.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'SearchTest course']);
        $anothercourse = $generator->create_course(['fullname' => 'Another']);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $studentothercourse = $this->getDataGenerator()->create_and_enrol($anothercourse, 'student');
        $page = $generator->create_module('page', ['course' => $course->id, 'name' => 'SearchTest page']);
        $forum = $generator->create_module('forum', ['course' => $course->id]);

        $fgenerator = $generator->get_plugin_generator('mod_forum');

        for ($i = 0; $i < 15; $i++) {
            $fgenerator->create_discussion(
                [
                    'course' => $course->id,
                    'forum' => $forum->id,
                    'userid' => $student->id,
                ]
            );
        }

        $search = \core_search\manager::instance();
        $search->index();

        // Basic search, by text.
        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('page'));
        $this->assertEquals(1, $return['totalcount']);
        $this->assertEquals('activity', $return['results'][0]['areaname']);
        $this->assertEquals($page->name, $return['results'][0]['title']);

        // Basic search, by name containing text.
        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('SearchTest'));
        $this->assertEquals(2, $return['totalcount']);

        // Test pagination.
        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('discussion', [], 0));
        $this->assertCount(10, $return['results']);  // The first 10 posts of a total of 15 for the second page.
        $this->assertEquals(15, $return['totalcount']);

        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('discussion', [], 1));
        $this->assertCount(5, $return['results']);  // The last 5 posts of a total of 15 for the second page.
        $this->assertEquals(15, $return['totalcount']);

        // Test some filters.
        $return = external_api::clean_returnvalue(get_results::execute_returns(),
            get_results::execute('discussion', ['title' => 'Discussion 11']));
        $this->assertEquals(1, $return['totalcount']);

        // No discussions created in the future.
        $return = external_api::clean_returnvalue(get_results::execute_returns(),
            get_results::execute('discussion', ['timestart' => time() + DAYSECS]));
        $this->assertEquals(0, $return['totalcount']);

        // Basic permissions check.
        $this->setUser($studentothercourse);
        $return = external_api::clean_returnvalue(get_results::execute_returns(), get_results::execute('discussion', [], 1));
        $this->assertCount(0, $return['results']);  // I should not see other courses discussions.
    }
}
