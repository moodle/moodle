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

namespace core_search;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/testable_core_search.php');
require_once(__DIR__ . '/fixtures/mock_search_area.php');

/**
 * Test for top results
 *
 * @package core_search
 * @author  Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class top_result_test extends \advanced_testcase {

    /** @var stdClass course 1 */
    protected $course1;
    /** @var stdClass course 2 */
    protected $course2;
    /** @var stdClass user 1 */
    protected $user1;
    /** @var stdClass user 2 */
    protected $user2;
    /** @var stdClass user 3 */
    protected $user3;
    /** @var stdClass search engine */
    protected $search;

    /**
     * Prepare test and users.
     */
    private function prepare_test_courses_and_users(): void {
        global $DB;

        $this->setAdminUser();

        // Search engine.
        $this->search = \testable_core_search::instance(new \search_simpledb\engine());

        // Set default configurations.
        set_config('searchallavailablecourses', 1);
        set_config('searchincludeallcourses', 1);
        set_config('searchenablecategories', true);
        set_config('enableglobalsearch', true);
        set_config('searchmaxtopresults', 3);
        $teacher = $DB->get_record('role', ['shortname' => 'teacher']);
        $editingteacher = $DB->get_record('role', ['shortname' => 'editingteacher']);
        set_config('searchteacherroles', "$teacher->id, $editingteacher->id");

        // Generate test data.
        $generator = $this->getDataGenerator();

        // Courses.
        $this->course1 = $generator->create_course(['fullname' => 'Top course result 1']);
        // Ensure course 1 is indexed before course 2.
        $this->run_index();
        $this->course2 = $generator->create_course(['fullname' => 'Top course result 2']);

        // User 1.
        $urecord1 = new \stdClass();
        $urecord1->firstname = "User 1";
        $urecord1->lastname = "Test";
        $this->user1 = $generator->create_user($urecord1);

        // User 2.
        $urecord2 = new \stdClass();
        $urecord2->firstname = "User 2";
        $urecord2->lastname = "Test";
        $this->user2 = $generator->create_user($urecord2);

        // User 3.
        $urecord3 = new \stdClass();
        $urecord3->firstname = "User 3";
        $urecord3->lastname = "Test";
        $this->user3 = $generator->create_user($urecord3);
    }

    /**
     * Test course ranking
     */
    public function test_search_course_rank(): void {
        $this->resetAfterTest();
        $this->prepare_test_courses_and_users();
        $this->setUser($this->user1);

        // Search query.
        $data = new \stdClass();
        $data->q = 'Top course result';
        $data->cat = 'core-all';

        // Course 1 at the first index.
        $this->run_index();
        $docs = $this->search->search_top($data);
        $this->assertEquals('Top course result 1', $docs[0]->get('title'));
        $this->assertEquals('Top course result 2', $docs[1]->get('title'));

        // Enrol user to course 2.
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course2->id, 'student');

        // Course 2 at the first index.
        $this->run_index();
        $docs = $this->search->search_top($data);
        $this->assertEquals('Top course result 2', $docs[0]->get('title'));
        $this->assertEquals('Top course result 1', $docs[1]->get('title'));
    }

    /**
     * Test without teacher indexing
     */
    public function test_search_with_no_course_teacher_indexing(): void {
        $this->resetAfterTest();
        $this->prepare_test_courses_and_users();
        set_config('searchteacherroles', "");
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course1->id, 'teacher');

        // Search query.
        $data = new \stdClass();
        $data->q = 'Top course result';
        $data->cat = 'core-all';

        // Only return the course.
        $this->run_index();
        $docs = $this->search->search_top($data);
        $this->assertCount(2, $docs);
        $this->assertEquals('Top course result 1', $docs[0]->get('title'));
        $this->assertEquals('Top course result 2', $docs[1]->get('title'));
    }

    /**
     * Test with teacher indexing
     */
    public function test_search_with_course_teacher_indexing(): void {
        $this->resetAfterTest();
        $this->prepare_test_courses_and_users();

        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($this->user2->id, $this->course1->id, 'student');

        // Search query.
        $data = new \stdClass();
        $data->q = 'Top course result 1';
        $data->cat = 'core-all';

        // Return the course and the teachers.
        $this->run_index();
        $docs = $this->search->search_top($data);
        $this->assertEquals('Top course result 1', $docs[0]->get('title'));
        $this->assertEquals('User 1 Test', $docs[1]->get('title'));
    }

    /**
     * Test with teacher indexing
     */
    public function test_search_with_course_teacher_content_indexing(): void {
        $this->resetAfterTest();
        $this->prepare_test_courses_and_users();

        // Create forums as course content.
        $generator = $this->getDataGenerator();

        // Course Teacher.
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course1->id, 'teacher');

        // Forums.
        $generator->create_module('forum',
            ['course' => $this->course1->id, 'name' => 'Forum 1,  does not contain the keyword']);
        $generator->create_module('forum',
            ['course' => $this->course2->id, 'name' => 'Forum 2, contains keyword Top course result 1']);

        $this->run_index();

        // Search query.
        $data = new \stdClass();
        $data->q = 'Top course result 1';
        $data->cat = 'core-all';

        // Return the course and the teacher and the forum.
        $docs = $this->search->search_top($data);
        $this->assertEquals('Top course result 1', $docs[0]->get('title'));
        $this->assertEquals('User 1 Test', $docs[1]->get('title'));
        $this->assertEquals('Forum 2, contains keyword Top course result 1', $docs[2]->get('title'));
    }

    /**
     * Execute indexing
     */
    private function run_index(): void {
        // Indexing.
        $this->waitForSecond();
        $this->search->index(false, 0);
    }
}
