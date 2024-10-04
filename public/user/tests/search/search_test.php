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
 * @package     core_user
 * @copyright   2016 Devang Gaur {@link http://www.devanggaur.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\search;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for course global search.
 *
 * @package     core
 * @copyright   2016 Devang Gaur {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class search_test extends \advanced_testcase {

    /**
     * @var string Area id
     */
    protected $userareaid = null;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->userareaid = \core_search\manager::generate_areaid('core_user', 'user');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();
    }

    /**
     * Indexing users contents.
     *
     * @return void
     */
    public function test_users_indexing(): void {
        global $SITE;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->userareaid);
        $this->assertInstanceOf('\core_user\search\user', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // All records.
        // Recordset will produce 4 user records:
        // Guest User, Admin User and two above generated users.
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
        $this->assertEquals(4, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();

        // Context support; first, try an unsupported context type.
        $coursecontext = \context_course::instance($SITE->id);
        $this->assertNull($searcharea->get_document_recordset(0, $coursecontext));

        // Try a specific user, will only return 1 record (that user).
        $rs = $searcharea->get_document_recordset(0, \context_user::instance($user1->id));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_users_document(): void {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->userareaid);
        $this->assertInstanceOf('\core_user\search\user', $searcharea);

        $user = self::getDataGenerator()->create_user();

        $doc = $searcharea->get_document($user);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals($user->id, $doc->get('itemid'));
        $this->assertEquals($this->userareaid . '-' . $user->id, $doc->get('id'));
        $this->assertEquals(SITEID, $doc->get('courseid'));
        $this->assertFalse($doc->is_set('userid'));
        $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));
        $this->assertEquals(content_to_text(fullname($user), false), $searcharea->get_document_display_title($doc));
        $this->assertEquals(content_to_text($user->description, $user->descriptionformat), $doc->get('content'));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_users_access(): void {
        global $CFG;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->userareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();
        $user5->id = 0; // Visitor (not guest).

        $deleteduser = self::getDataGenerator()->create_user(array('deleted' => 1));
        $unconfirmeduser = self::getDataGenerator()->create_user(array('confirmed' => 0));
        $suspendeduser = self::getDataGenerator()->create_user(array('suspended' => 1));

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($suspendeduser->id, $course1->id, 'student');

        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user3->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user4->id, 'groupid' => $group2->id));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user3->id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access($deleteduser->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($unconfirmeduser->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($suspendeduser->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access(2));

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user3->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user4->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access(1));// Guest user can't be accessed.
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access(2));// Admin user can't be accessed.
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-123));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($unconfirmeduser->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($suspendeduser->id));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user3->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user4->id));

        $this->setUser($user3);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user3->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($suspendeduser->id));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user3->id));

        $CFG->forceloginforprofiles = 0;
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user3->id));

        $this->setUser($user5);
        $CFG->forceloginforprofiles = 1;
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($user3->id));

        $CFG->forceloginforprofiles = 0;
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user2->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($user3->id));
    }

    /**
     * Test document icon.
     */
    public function test_get_doc_icon(): void {
        $searcharea = \core_search\manager::get_search_area($this->userareaid);
        $user = self::getDataGenerator()->create_user();
        $doc = $searcharea->get_document($user);

        $result = $searcharea->get_doc_icon($doc);

        $this->assertEquals('i/user', $result->get_name());
        $this->assertEquals('moodle', $result->get_component());
    }

    /**
     * Test assigned search categories.
     */
    public function test_get_category_names(): void {
        $searcharea = \core_search\manager::get_search_area($this->userareaid);

        $expected = ['core-users'];
        $this->assertEquals($expected, $searcharea->get_category_names());
    }
}
