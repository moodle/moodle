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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the get_top_results external function.
 *
 * @package    core_search
 * @category   test
 * @copyright  2023 Juan Leyva (juan@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_search\external\get_top_results
 */
class get_top_results_test extends \externallib_advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * test external api
     * @covers ::execute
     * @return void
     */
    public function test_external_get_top_results(): void {

        set_config('enableglobalsearch', true);
        set_config('searchenablecategories', true); // Required for top search.
        set_config('searchmaxtopresults', 5); // Change default.
        set_config('searchengine', 'simpledb');

        $this->setAdminUser();

        // Create an index of searchable things.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'SearchTest course']);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
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

        // Test top results.
        $return = external_api::clean_returnvalue(get_top_results::execute_returns(), get_top_results::execute('discussion', []));
        $this->assertCount(5, $return['results']);  // We get the 5 top results according to searchmaxtopresults setting value.

        set_config('searchmaxtopresults', 3); // Change to 3 top.
        $return = external_api::clean_returnvalue(get_top_results::execute_returns(), get_top_results::execute('discussion', []));
        $this->assertCount(3, $return['results']);

        // Test some filters.
        $return = external_api::clean_returnvalue(get_top_results::execute_returns(),
            get_top_results::execute('discussion', ['title' => 'Discussion 11']));
        $this->assertCount(1, $return['results']);

        set_config('searchenablecategories', false);    // Disable top search.
        $return = external_api::clean_returnvalue(get_top_results::execute_returns(), get_top_results::execute('discussion', []));
        $this->assertCount(0, $return['results']);
    }
}
