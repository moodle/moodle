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
 * Privacy tests for core_favourites.
 *
 * @package    core_favourites
 * @category   test
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use core_favourites\privacy\provider;
use core_privacy\local\request\transform;

/**
 * Unit tests for favourites/classes/privacy/provider
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Helper to set up some sample users and courses.
     */
    protected function set_up_courses_and_users() {
        $user1 = self::getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);
        $user2 = self::getDataGenerator()->create_user();
        $user2context = \context_user::instance($user2->id);
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        return [$user1, $user2, $user1context, $user2context, $course1context, $course2context];
    }

    /**
     * Test confirming that contexts of favourited items can be added to the contextlist.
     */
    public function test_add_contexts_for_userid(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2, all at the site context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $systemcontext = \context_system::instance();
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $systemcontext);
        $ufservice1->create_favourite('core_course', 'courses', $course2context->instanceid, $systemcontext);
        $ufservice2->create_favourite('core_course', 'courses', $course2context->instanceid, $systemcontext);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));

        // Now, just for variety, let's assume you can favourite a course at user context, and do so for user1.
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $user1context);

        // Now, ask the favourites privacy api to export contexts for favourites of the type we just created, for user1.
        $contextlist = new \core_privacy\local\request\contextlist();
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $user1->id, 'core_course', 'courses');

        // Verify we have two contexts in the list for user1.
        $this->assertCount(2, $contextlist->get_contextids());

        // And verify we only have the system context returned for user2.
        $contextlist = new \core_privacy\local\request\contextlist();
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $user2->id, 'core_course', 'courses');
        $this->assertCount(1, $contextlist->get_contextids());
    }

    /**
     * Test deletion of user favourites based on an approved_contextlist and component area.
     */
    public function test_delete_favourites_for_user(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2, all at the user context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $user1context);
        $ufservice1->create_favourite('core_course', 'courses', $course2context->instanceid, $user1context);
        $ufservice2->create_favourite('core_course', 'courses', $course2context->instanceid, $user2context);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));

        // Now, delete the favourites for user1 only.
        $approvedcontextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_course', [$user1context->id]);
        provider::delete_favourites_for_user($approvedcontextlist, 'core_course', 'courses');

        // Verify that we have no favourite courses for user1 but that the records are in tact for user2.
        $this->assertCount(0, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));
    }

    public function test_delete_favourites_for_all_users(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 course modules for user1 and 1 course module for user2 all in course 1 context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $ufservice1->create_favourite('core_course', 'modules', 1, $course1context);
        $ufservice1->create_favourite('core_course', 'modules', 2, $course1context);
        $ufservice2->create_favourite('core_course', 'modules', 3, $course1context);

        // Now, favourite a different course module for user2 in course 2.
        $ufservice2->create_favourite('core_course', 'modules', 5, $course2context);

        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'modules'));
        $this->assertCount(2, $ufservice2->find_favourites_by_type('core_course', 'modules'));

        // Now, delete all course module favourites in the 'course1' context only.
        provider::delete_favourites_for_all_users($course1context, 'core_course', 'modules');

        // Verify that only a single favourite for user1 in course 1 remains.
        $this->assertCount(0, $ufservice1->find_favourites_by_type('core_course', 'modules'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'modules'));
    }

    /**
     * Test confirming that user ID's of favourited items can be added to the userlist.
     */
    public function test_add_userids_for_context(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2, all at the site context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $systemcontext = \context_system::instance();
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $systemcontext);
        $ufservice1->create_favourite('core_course', 'courses', $course2context->instanceid, $systemcontext);
        $ufservice2->create_favourite('core_course', 'courses', $course2context->instanceid, $systemcontext);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));

        // Now, just for variety, let's assume you can favourite a course at user context, and do so for user1.
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $user1context);

        // Now, ask the favourites privacy api to export userids for favourites of the type we just created, in the system context.
        $userlist = new \core_privacy\local\request\userlist($systemcontext, 'core_course');
        provider::add_userids_for_context($userlist, 'courses');
        // Verify we have two userids in the list for system context.
        $this->assertCount(2, $userlist->get_userids());
        $expected = [
            $user1->id,
            $user2->id
        ];
        $this->assertEqualsCanonicalizing($expected, $userlist->get_userids());

        // Ask the favourites privacy api to export userids for favourites of the type we just created, in the user1 context.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_course');
        provider::add_userids_for_context($userlist, 'courses');
        // Verify we have one userid in the list for user1 context.
        $this->assertCount(1, $userlist->get_userids());
        $expected = [$user1->id];
        $this->assertEquals($expected, $userlist->get_userids());

        // Ask the favourites privacy api to export userids for favourites of the type we just created, in the user2 context.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_favourites');
        provider::add_userids_for_context($userlist, 'core_course', 'courses');
        // Verify we do not have any userids in the list for user2 context.
        $this->assertCount(0, $userlist->get_userids());
    }

    /**
     * Test deletion of user favourites based on an approved_userlist, component area and item type.
     */
    public function test_delete_favourites_for_userlist(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2.
        $systemcontext = \context_system::instance();
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $ufservice1->create_favourite('core_course', 'courses', $course1context->instanceid, $systemcontext);
        $ufservice1->create_favourite('core_course', 'courses', $course2context->instanceid, $user1context);
        $ufservice2->create_favourite('core_course', 'courses', $course2context->instanceid, $systemcontext);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));

        // Ask the favourites privacy api to export userids for favourites of the type we just created, in the system context.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, 'core_course');
        provider::add_userids_for_context($userlist1, 'courses');
        // Verify we have two userids in the list for system context.
        $this->assertCount(2, $userlist1->get_userids());

        // Ask the favourites privacy api to export userids for favourites of the type we just created, in the user1 context.
        $userlist2 = new \core_privacy\local\request\userlist($user1context, 'core_course');
        provider::add_userids_for_context($userlist2, 'courses');
        // Verify we have one userid in the list for user1 context.
        $this->assertCount(1, $userlist2->get_userids());

        // Now, delete the favourites for user1 only in the system context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($systemcontext, 'core_course',
                [$user1->id]);
        provider::delete_favourites_for_userlist($approveduserlist, 'courses');
        // Ensure user1's data was deleted and user2 is still returned for system context.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, 'core_course');
        provider::add_userids_for_context($userlist1, 'courses');
        $this->assertCount(1, $userlist1->get_userids());
        // Verify that user2 is still in the list for system context.
        $expected = [$user2->id];
        $this->assertEquals($expected, $userlist1->get_userids());
        // Verify that the data of user1 was not deleted in the user1context.
        $userlist2 = new \core_privacy\local\request\userlist($user1context, 'core_course');
        provider::add_userids_for_context($userlist2, 'courses');
        $expected = [$user1->id];
        $this->assertEquals($expected, $userlist2->get_userids());

        // Now, delete the favourites for user2 only in the user1 context.
        // Make sure favourites are only being deleted in the right context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($user1context, 'core_course',
                [$user2->id]);
        provider::delete_favourites_for_userlist($approveduserlist, 'courses');
        // Verify we have one userid in the list for system context.
        $userlist2 = new \core_privacy\local\request\userlist($systemcontext, 'core_course');
        provider::add_userids_for_context($userlist2, 'courses');
        $this->assertCount(1, $userlist2->get_userids());
        // Verify that user2 is still in the list for system context.
        $expected = [$user2->id];
        $this->assertEquals($expected, $userlist2->get_userids());

        // Verify that user1 is still present in the list for user1 context.
        $userlist3 = new \core_privacy\local\request\userlist($user1context, 'core_course');
        provider::add_userids_for_context($userlist3, 'courses');
        $this->assertCount(1, $userlist3->get_userids());
        // Verify that user1 is still in the list for user1 context.
        $expected = [$user1->id];
        $this->assertEquals($expected, $userlist3->get_userids());
    }

    /**
     * Test fetching the favourites data for a specified user in a specified component, item type and item ID.
     */
    public function test_get_favourites_info_for_user(): void {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $coursefavourite1 = $ufservice1->create_favourite('core_course', 'courses',
                $course1context->instanceid, $course1context);
        $this->waitForSecond();
        $coursefavourite2 = $ufservice1->create_favourite('core_course', 'courses',
                $course2context->instanceid, $course2context);
        $this->waitForSecond();
        $coursefavourite3 = $ufservice2->create_favourite('core_course', 'courses',
                $course2context->instanceid, $course2context);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'courses'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'courses'));

        // Get the favourites info for user1 in the course1 context.
        $favouriteinfo1 = (object) provider::get_favourites_info_for_user($user1->id, $course1context,
                'core_course', 'courses', $course1context->instanceid);
        // Ensure the correct data has been returned.
        $this->assertEquals(transform::yesno(true), $favouriteinfo1->starred);
        $this->assertEquals('', $favouriteinfo1->ordering);
        $this->assertEquals(transform::datetime($coursefavourite1->timecreated), $favouriteinfo1->timecreated);
        $this->assertEquals(transform::datetime($coursefavourite1->timemodified), $favouriteinfo1->timemodified);

        // Get the favourites info for user1 in the course2 context.
        $favouriteinfo2 = (object) provider::get_favourites_info_for_user($user1->id, $course2context,
                'core_course', 'courses', $course2context->instanceid);
        // Ensure the correct data has been returned.
        $this->assertEquals(transform::yesno(true), $favouriteinfo2->starred);
        $this->assertEquals('', $favouriteinfo2->ordering);
        $this->assertEquals(transform::datetime($coursefavourite2->timecreated), $favouriteinfo2->timecreated);
        $this->assertEquals(transform::datetime($coursefavourite2->timemodified), $favouriteinfo2->timemodified);

        // Get the favourites info for user2 in the course2 context.
        $favouriteinfo3 = (object) provider::get_favourites_info_for_user($user2->id, $course2context,
                'core_course', 'courses', $course2context->instanceid);
        // Ensure the correct data has been returned.
        $this->assertEquals(transform::yesno(true), $favouriteinfo3->starred);
        $this->assertEquals('', $favouriteinfo3->ordering);
        $this->assertEquals(transform::datetime($coursefavourite3->timecreated), $favouriteinfo3->timecreated);
        $this->assertEquals(transform::datetime($coursefavourite3->timemodified), $favouriteinfo3->timemodified);

        // Get the favourites info for user2 in the course1 context (user2 has not favourited course1).
        $favouriteinfo4 = provider::get_favourites_info_for_user($user2->id, $course1context,
                'core_course', 'courses', $course1context->instanceid);
        // Ensure that data has not been returned.
        $this->assertEmpty($favouriteinfo4);
    }
}
