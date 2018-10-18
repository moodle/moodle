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

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \core_favourites\privacy\provider;

/**
 * Unit tests for favourites/classes/privacy/provider
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class privacy_test extends provider_testcase {

    public function setUp() {
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
        $course1context = context_course::instance($course1->id);
        $course2context = context_course::instance($course2->id);
        return [$user1, $user2, $user1context, $user2context, $course1context, $course2context];
    }

    /**
     * Test confirming that contexts of favourited items can be added to the contextlist.
     */
    public function test_add_contexts_for_userid() {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2, all at the site context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $systemcontext = context_system::instance();
        $ufservice1->create_favourite('core_course', 'course', $course1context->instanceid, $systemcontext);
        $ufservice1->create_favourite('core_course', 'course', $course2context->instanceid, $systemcontext);
        $ufservice2->create_favourite('core_course', 'course', $course2context->instanceid, $systemcontext);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'course'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'course'));

        // Now, just for variety, let's assume you can favourite a course at user context, and do so for user1.
        $ufservice1->create_favourite('core_course', 'course', $course1context->instanceid, $user1context);

        // Now, ask the favourites privacy api to export contexts for favourites of the type we just created, for user1.
        $contextlist = new \core_privacy\local\request\contextlist();
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $user1->id, 'core_course', 'course');

        // Verify we have two contexts in the list for user1.
        $this->assertCount(2, $contextlist->get_contextids());

        // And verify we only have the system context returned for user2.
        $contextlist = new \core_privacy\local\request\contextlist();
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $user2->id, 'core_course', 'course');
        $this->assertCount(1, $contextlist->get_contextids());
    }

    /**
     * Test deletion of user favourites based on an approved_contextlist and component area.
     */
    public function test_delete_favourites_for_user() {
        list($user1, $user2, $user1context, $user2context, $course1context, $course2context) = $this->set_up_courses_and_users();

        // Favourite 2 courses for user1 and 1 course for user2, all at the user context.
        $ufservice1 = \core_favourites\service_factory::get_service_for_user_context($user1context);
        $ufservice2 = \core_favourites\service_factory::get_service_for_user_context($user2context);
        $ufservice1->create_favourite('core_course', 'course', $course1context->instanceid, $user1context);
        $ufservice1->create_favourite('core_course', 'course', $course2context->instanceid, $user1context);
        $ufservice2->create_favourite('core_course', 'course', $course2context->instanceid, $user2context);
        $this->assertCount(2, $ufservice1->find_favourites_by_type('core_course', 'course'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'course'));

        // Now, delete the favourites for user1 only.
        $approvedcontextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_course', [$user1context->id]);
        provider::delete_favourites_for_user($approvedcontextlist, 'core_course', 'course');

        // Verify that we have no favourite courses for user1 but that the records are in tact for user2.
        $this->assertCount(0, $ufservice1->find_favourites_by_type('core_course', 'course'));
        $this->assertCount(1, $ufservice2->find_favourites_by_type('core_course', 'course'));
    }

    public function test_delete_favourites_for_all_users() {
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
}
