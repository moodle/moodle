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
 * External function unit tests.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

/**
 * External function unit tests.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Checks the get_relevant_users function used when selecting users in search filter.
     */
    public function test_get_relevant_users() {
        // Set up two users to search for and one to do the searching.
        $generator = $this->getDataGenerator();
        $student1 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Aardvark']);
        $student2 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Beetle']);
        $student3 = $generator->create_user(['firstname' => 'Zebedee', 'lastname' => 'Boing']);
        $course = $generator->create_course();
        $generator->enrol_user($student1->id, $course->id, 'student');
        $generator->enrol_user($student2->id, $course->id, 'student');
        $generator->enrol_user($student3->id, $course->id, 'student');

        // As student 3, search for the other two.
        $this->setUser($student3);
        $result = external::clean_returnvalue(
            external::get_relevant_users_returns(),
            external::get_relevant_users('Amelia', 0)
        );

        // Check we got the two expected users back.
        $this->assertEquals([
            $student1->id,
            $student2->id,
        ], array_column($result, 'id'));

        // Check that the result contains all the expected fields.
        $this->assertEquals($student1->id, $result[0]['id']);
        $this->assertEquals('Amelia Aardvark', $result[0]['fullname']);
        $this->assertStringContainsString('/u/f2', $result[0]['profileimageurlsmall']);

        // Check we aren't leaking information about user email address (for instance).
        $this->assertArrayNotHasKey('email', $result[0]);

        // Note: We are not checking search permissions, search by different fields, etc. as these
        // are covered by the core_user::search unit test.
    }

    /**
     * test external api
     *
     * @return void
     */
    public function test_external_get_results() {
        global $USER, $DB, $CFG;

        require_once($CFG->dirroot . '/lib/externallib.php');

        set_config('enableglobalsearch', true);
        set_config('searchengine', 'simpledb');

        $course = $this->getDataGenerator()->create_course();

        $this->setAdminUser();

        // Filters with defaults.
        $filters = array(
            'title' => null,
            'areaids' => array(),
            'courseids' => array(),
            'timestart' => 0,
            'timeend' => 0
        );

        // We need to execute the return values cleaning process to simulate the web service server.
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('one', $filters));
        $this->assertEquals(0, $return['totalcount']);

        $search = \core_search\manager::instance();
        $search->index();


                $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results($course->shortname));

        print_r($return);

        $this->assertEquals(0, $return['totalcount']);

        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(2, $return['totalcount']);
        $this->assertEquals($USER->id, $return['results'][0]['userid']);
        $this->assertEquals(\context_system::instance()->id, $return['results'][0]['contextid']);

        sleep(1);
        $beforeadding = time();
        sleep(1);
        $this->generator->create_record();
        $this->search->index();

        // Timestart.
        $filters['timestart'] = $beforeadding;
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(1, $return['totalcount']);

        // Timeend.
        $filters['timestart'] = 0;
        $filters['timeend'] = $beforeadding;
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(2, $return['totalcount']);

        // Title.
        $filters['timeend'] = 0;
        $filters['title'] = 'Special title';
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(1, $return['totalcount']);

        // Course IDs.
        $filters['title'] = null;
        $filters['courseids'] = array(SITEID + 1);
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(0, $return['totalcount']);

        $filters['courseids'] = array(SITEID);
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(3, $return['totalcount']);

        // Reset filters once again.
        $filters['courseids'] = array();

        // Now try some area-id combinations.
        $forumpostareaid = \core_search\manager::generate_areaid('mod_forum', 'post');
        $mockareaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');

        $filters['areaids'] = array($forumpostareaid);
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(0, $return['totalcount']);

        $filters['areaids'] = array($forumpostareaid, $mockareaid);
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(3, $return['totalcount']);

        $filters['areaids'] = array($mockareaid);
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(3, $return['totalcount']);

        // All records now.
        $filters['areaids'] = array();
        $return = \external_api::clean_returnvalue(\core_search\external::get_results_returns(),
            \core_search\external::get_results('message', $filters));
        $this->assertEquals(3, $return['totalcount']);
    }
}
