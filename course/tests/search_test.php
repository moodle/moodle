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
 * Course global search unit tests.
 *
 * @package     core
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for course global search.
 *
 * @package     core
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_search_testcase extends advanced_testcase {

    /**
     * @var string Area id
     */
    protected $mycoursesareaid = null;

    public function setUp() {
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->mycoursesareaid = \core_search\manager::generate_areaid('core_course', 'mycourse');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();
    }

    /**
     * Indexing my courses contents.
     *
     * @return void
     */
    public function test_mycourses_indexing() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);
        $this->assertInstanceOf('\core_course\search\mycourse', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');

        $record = new stdClass();
        $record->course = $course1->id;

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(3, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_mycourses_document() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);
        $this->assertInstanceOf('\core_course\search\mycourse', $searcharea);

        $user = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'teacher');

        $doc = $searcharea->get_document($course);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals($course->id, $doc->get('itemid'));
        $this->assertEquals($this->mycoursesareaid . '-' . $course->id, $doc->get('id'));
        $this->assertEquals($course->id, $doc->get('courseid'));
        $this->assertFalse($doc->is_set('userid'));
        $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));
        $this->assertEquals($course->fullname, $doc->get('title'));

        // Not nice. Applying \core_search\document::set line breaks clean up.
        $summary = preg_replace("/\s+/u", " ", content_to_text($course->summary, $course->summaryformat));
        $this->assertEquals($summary, $doc->get('content'));
        $this->assertEquals($course->shortname, $doc->get('description1'));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_mycourses_access() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course(array('visible' => 0));
        $course3 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course3->id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-123));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course1->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course3->id));
    }
}
