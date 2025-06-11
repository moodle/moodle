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

namespace core_message;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Tests for the message helper class.
 *
 * @package core_message
 * @category test
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_message\helper
 */
final class helper_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    public function test_get_member_info_ordering(): void {
        // Create a conversation with several users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
                $user3->id,
                $user4->id,
            ],
            'Group conversation'
        );

        // Verify that the member information comes back in the same order that we specified in the input array.
        $memberinfo = \core_message\helper::get_member_info($user1->id, [$user3->id, $user4->id, $user2->id]);
        $this->assertEquals($user3->id, array_shift($memberinfo)->id);
        $this->assertEquals($user4->id, array_shift($memberinfo)->id);
        $this->assertEquals($user2->id, array_shift($memberinfo)->id);
    }

    /**
     * Test search_get_user_details returns the correct profile data when $CFG->messagingallusers is disabled.
     */
    public function test_search_get_user_details_sitewide_disabled(): void {
        global $DB;
        set_config('messagingallusers', false);

        // Two students sharing course 1, visible profile within course (no groups).
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course((object) ['groupmode' => 0]);
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // A teacher in course 1.
        $user3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, 'editingteacher');

        // Two students sharing course 2, separate groups (profiles not visible to one another).
        // Note: no groups are created here, but separate groups mode alone is enough to restrict profile access.
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $course2 = $this->getDataGenerator()->create_course((object) ['groupmode' => 1]);
        $this->getDataGenerator()->enrol_user($user4->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user5->id, $course2->id);

        // A teacher in course 2.
        $user6 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user6->id, $course2->id, 'editingteacher');

        // Teacher and course contact in course 3.
        $user7 = $this->getDataGenerator()->create_user();
        $course3 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user7->id, $course3->id, 'editingteacher');
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Make teachers course contacts.
        set_config('coursecontact', $teacherrole->id);

        // User 1 should be able to see users within their course, but not course contacts or students in other courses.
        $this->setUser($user1);
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user2)); // Student in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user4)); // Student in another course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user3)); // Teacher in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user7)); // Teacher (course contact) in another course.

        // User 3 should be able to see the teacher in their own course, but not other students in that course nor course contacts
        // or students in other courses.
        $this->setUser($user4);
        $this->assertEmpty(\core_message\helper::search_get_user_details($user5)); // Student in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user1)); // Student in another course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user6)); // Teacher in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user7)); // Teacher (course contact) in another course.
    }

    /**
     * Test search_get_user_details returns the correct profile data we limit the data we wish to be returned.
     */
    public function test_search_get_user_details_limited_data(): void {
        set_config('messagingallusers', false);

        // Two students sharing course 1, visible profile within course (no groups).
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course((object) ['groupmode' => 0]);
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Calculate the minimum fields that can be returned.
        $namefields = \core_user\fields::for_name()->get_required_fields();
        $fields = array_intersect($namefields, user_get_default_fields());

        $minimaluser = (object) [
            'id' => $user2->id,
            'deleted' => $user2->deleted,
        ];

        foreach ($namefields as $field) {
            $minimaluser->$field = $user2->$field;
        }

        // Test that less data is returned using the filter.
        $this->setUser($user1);
        $fulldetails = helper::search_get_user_details($user2);
        $limiteddetails = helper::search_get_user_details($minimaluser, $fields);
        $fullcount = count($fulldetails);
        $limitedcount = count($limiteddetails);
        $this->assertLessThan($fullcount, $limitedcount);
        $this->assertNotEquals($fulldetails, $limiteddetails);
    }

    /**
     * Test search_get_user_details returns the correct profile data when $CFG->messagingallusers is enabled.
     */
    public function test_search_get_user_details_sitewide_enabled(): void {
        global $DB;
        set_config('messagingallusers', true);

        // Two students sharing course 1, visible profile within course (no groups).
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course((object) ['groupmode' => 0]);
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // A teacher in course 1.
        $user3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, 'editingteacher');

        // Two students sharing course 2, separate groups (profiles not visible to one another).
        // Note: no groups are created here, but separate groups mode alone is enough to restrict profile access.
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $course2 = $this->getDataGenerator()->create_course((object) ['groupmode' => 1]);
        $this->getDataGenerator()->enrol_user($user4->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user5->id, $course2->id);

        // A teacher in course 2.
        $user6 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user6->id, $course2->id, 'editingteacher');

        // Teacher and course contact in course 3.
        $user7 = $this->getDataGenerator()->create_user();
        $course3 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user7->id, $course3->id, 'editingteacher');
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Make teachers course contacts.
        set_config('coursecontact', $teacherrole->id);

        // User 1 should be able to see users within their course and course contacts, but not students in other courses.
        $this->setUser($user1);
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user2)); // Student in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user4)); // Student in another course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user3)); // Teacher in same course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user7)); // Teacher (course contact) in another course.

        // User 3 should be able to see the teacher in their own course, but not other students in that course nor course contacts
        // or students in other courses.
        $this->setUser($user4);
        $this->assertEmpty(\core_message\helper::search_get_user_details($user5)); // Student in same course.
        $this->assertEmpty(\core_message\helper::search_get_user_details($user1)); // Student in another course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user6)); // Teacher in same course.
        $this->assertNotEmpty(\core_message\helper::search_get_user_details($user7)); // Teacher (course contact) in another course.
    }

    /**
     * Test prevent_unclosed_html_tags returns the correct html.
     *
     * @dataProvider prevent_unclosed_html_tags_data
     * @param string $text text to preview unclosed html tags.
     * @param string $goodhtml html good structured.
     * @param bool $removebody true if we want to remove tag body.
     */
    public function test_prevent_unclosed_html_tags(string $message, string $goodhtml, bool $removebody): void {
        $this->setAdminUser();

        $html = \core_message\helper::prevent_unclosed_html_tags($message, $removebody);
        $this->assertSame($goodhtml, $html);
    }

    /**
     * Data provider for the test_prevent_unclosed_html_tags_data tests.
     *
     * @return  array
     */
    public static function prevent_unclosed_html_tags_data(): array {
        return [
            'Prevent unclosed html elements' => [
                '<h1>Title</h1><p>Paragraph</p><b>Bold', '<h1>Title</h1><p>Paragraph</p><b>Bold</b>', true
            ],
            'Prevent unclosed html elements including comments' => [
                '<h1>Title</h1><p>Paragraph</p><!-- Comments //--><b>Bold', '<h1>Title</h1><p>Paragraph</p><!-- Comments //--><b>Bold</b>', true
            ],
            'Prevent unclosed comments' => ['<h1>Title</h1><p>Paragraph</p><!-- Comments', '<h1>Title</h1><p>Paragraph</p>', true
            ],
            'Prevent unclosed html elements without removing tag body' => [
                '<body><h2>Title 2</h2><p>Paragraph</p><b>Bold</body>', '<body><h2>Title 2</h2><p>Paragraph</p><b>Bold</b></body>', false
            ],
            'Empty html' => [
                '', '', false
            ],
            'Check encoding UTF-8 is working' => [
                '<body><h1>Title</h1><p>السلام عليكم</p></body>', '<body><h1>Title</h1><p>السلام عليكم</p></body>', false
            ],
        ];
    }
}
