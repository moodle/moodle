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
 * Availability password - Test the privacy API functions
 *
 * @package    availability_password
 * @copyright  2019 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_password\privacy;

/**
 * Class availability_password_privacy_test.
 *
 * @package    availability_password
 * @copyright  2019 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class privacy_test extends \core_privacy\tests\provider_testcase {
    /**
     * Set up the unit tests.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        // Call parent setup.
        parent::setUp();
    }

    /**
     * Create courses, users and pages.
     * Create 'password entered' records for some of the users.
     *
     * @return array [$array_of_page_records, $array_of_user_records]
     * @throws coding_exception
     * @throws dml_exception
     */
    private function setup_data() {
        global $DB;
        $gen = self::getDataGenerator();
        $c1 = $gen->create_course();
        $c2 = $gen->create_course();
        $u1 = $gen->create_user();
        $u2 = $gen->create_user();
        $u3 = $gen->create_user();

        /** @var mod_page_generator $pagegen */
        $pagegen = $gen->get_plugin_generator('mod_page');
        $p1 = $pagegen->create_instance(['course' => $c1]);
        $p2 = $pagegen->create_instance(['course' => $c1]);
        $p3 = $pagegen->create_instance(['course' => $c2]);
        $p4 = $pagegen->create_instance(['course' => $c2]);

        // Create availability password records for these users.
        // Page 1 (u1 + u2).
        $DB->insert_record('availability_password_grant', (object)[
            'courseid' => $p1->course,
            'cmid' => $p1->cmid,
            'userid' => $u1->id,
            'password' => 'fred',
        ]);
        $DB->insert_record('availability_password_grant', (object)[
            'courseid' => $p1->course,
            'cmid' => $p1->cmid,
            'userid' => $u2->id,
            'password' => 'fred',
        ]);
        // Page 2 (u1).
        $DB->insert_record('availability_password_grant', (object)[
            'courseid' => $p2->course,
            'cmid' => $p2->cmid,
            'userid' => $u1->id,
            'password' => 'fred',
        ]);
        // Page 3 (u1 + u3).
        $DB->insert_record('availability_password_grant', (object)[
            'courseid' => $p3->course,
            'cmid' => $p3->cmid,
            'userid' => $u1->id,
            'password' => 'fred',
        ]);
        $DB->insert_record('availability_password_grant', (object)[
            'courseid' => $p3->course,
            'cmid' => $p3->cmid,
            'userid' => $u3->id,
            'password' => 'fred',
        ]);
        // Nothing for p4.

        return [[$p1, $p2, $p3, $p4], [$u1, $u2, $u3]];
    }

    /**
     * Given a page instance, return the associated userlist (using the privacy API).
     *
     * @param object $page
     * @return \core_privacy\local\request\userlist
     */
    private function get_users_on_page($page) {
        $ctx = \context_module::instance($page->cmid);
        $userlist = new \core_privacy\local\request\userlist($ctx, 'availability_password');
        provider::get_users_in_context($userlist);
        return $userlist;
    }

    /**
     * Test get_users_in_context() function returns the expected users for each page.
     *
     * @covers \availability_password\privacy\privacy_test::get_users_on_page()
     */
    public function test_get_users_in_context(): void {
        list($pages, ) = $this->setup_data();
        list($p1, $p2, $p3, $p4) = $pages;

        $this->assertCount(2, $this->get_users_on_page($p1));
        $this->assertCount(1, $this->get_users_on_page($p2));
        $this->assertCount(2, $this->get_users_on_page($p3));
        $this->assertCount(0, $this->get_users_on_page($p4));
    }

    /**
     * Test delete_data_for_users() removes the expected data from the specified users (in the specified context) and
     * no other data is affected.
     *
     * @covers ::delete_data_for_users()
     */
    public function test_delete_data_for_users(): void {
        list($pages, $users) = $this->setup_data();
        list($p1, $p2, $p3, $p4) = $pages;
        list($u1, $u2, $u3) = $users;

        // Delete u1 + u3 from page 1.
        $ctx = \context_module::instance($p1->cmid);
        $userlist = new \core_privacy\local\request\userlist($ctx, 'availability_password');
        provider::get_users_in_context($userlist);
        $approvedlist = new \core_privacy\local\request\approved_userlist($ctx, 'availability_password', [$u1->id, $u3->id]);
        provider::delete_data_for_users($approvedlist);

        // Check that there is now only 1 user for page 1, but the other counts are unaffected.
        $this->assertCount(1, $this->get_users_on_page($p1));
        $this->assertCount(1, $this->get_users_on_page($p2));
        $this->assertCount(2, $this->get_users_on_page($p3));
        $this->assertCount(0, $this->get_users_on_page($p4));

        // Delete u1 + u3 from page 2.
        $ctx = \context_module::instance($p2->cmid);
        $userlist = new \core_privacy\local\request\userlist($ctx, 'availability_password');
        provider::get_users_in_context($userlist);
        $approvedlist = new \core_privacy\local\request\approved_userlist($ctx, 'availability_password', [$u1->id, $u3->id]);
        provider::delete_data_for_users($approvedlist);

        // Check that there are now no users for page 2, but the other counts are unaffected.
        $this->assertCount(1, $this->get_users_on_page($p1));
        $this->assertCount(0, $this->get_users_on_page($p2));
        $this->assertCount(2, $this->get_users_on_page($p3));
        $this->assertCount(0, $this->get_users_on_page($p4));

        // Delete u1 + u3 from page 3.
        $ctx = \context_module::instance($p3->cmid);
        $userlist = new \core_privacy\local\request\userlist($ctx, 'availability_password');
        provider::get_users_in_context($userlist);
        $approvedlist = new \core_privacy\local\request\approved_userlist($ctx, 'availability_password', [$u1->id, $u3->id]);
        provider::delete_data_for_users($approvedlist);

        // Check that there are now no users for page 3, but the other counts are unaffected.
        $this->assertCount(1, $this->get_users_on_page($p1));
        $this->assertCount(0, $this->get_users_on_page($p2));
        $this->assertCount(0, $this->get_users_on_page($p3));
        $this->assertCount(0, $this->get_users_on_page($p4));
    }
}
