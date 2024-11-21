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
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\local\utils\user_utils;
use block_xp\tests\base_testcase;

/**
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_utils_test extends base_testcase {

    /**
     * Test.
     *
     * @covers \block_xp\local\utils\user_utils::can_earn_points
     */
    public function test_can_earn_points_without_context(): void {
        $u1 = $this->getDataGenerator()->create_user();
        $adminuser = get_admin();

        $this->assertFalse(user_utils::can_earn_points(null, 0));
        $this->assertFalse(user_utils::can_earn_points(null, $u1->id));
        $this->assertFalse(user_utils::can_earn_points(null, $adminuser->id));

        $this->assertFalse(user_utils::can_earn_points(false, 0));
        $this->assertFalse(user_utils::can_earn_points(false, $u1->id));
        $this->assertFalse(user_utils::can_earn_points(false, $adminuser->id));
    }

    /**
     * Test.
     *
     * @covers \block_xp\local\utils\user_utils::can_earn_points
     */
    public function test_can_earn_points_with_excluded_users(): void {
        $c1 = $this->getDataGenerator()->create_course();

        $guestuser = guest_user();
        $noreplyuser = \core_user::get_noreply_user();
        $adminuser = get_admin();

        $this->assertFalse(\core_user::is_real_user($noreplyuser->id));

        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), 0));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $guestuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $noreplyuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $adminuser->id));

        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), 0));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $guestuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $noreplyuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $adminuser->id));
    }

    /**
     * Test.
     *
     * @covers \block_xp\local\utils\user_utils::can_earn_points
     */
    public function test_can_earn_points_for_students(): void {
        $c1 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();

        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $u1->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $u2->id));

        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $u1->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $u2->id));

        $this->getDataGenerator()->enrol_user($u1->id, $c1->id, 'student');

        $this->assertTrue(user_utils::can_earn_points(\context_course::instance($c1->id), $u1->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $u2->id));
    }

    /**
     * Test.
     *
     * @covers \block_xp\local\utils\user_utils::can_earn_points
     */
    public function test_can_earn_points_with_global_perm(): void {
        global $DB;

        $guestuser = guest_user();
        $noreplyuser = \core_user::get_noreply_user();
        $adminuser = get_admin();

        role_change_permission($DB->get_field('role', 'id', ['shortname' => 'user']),
            \context_system::instance(), 'block/xp:earnxp', CAP_ALLOW);

        $c1 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();

        $this->assertTrue(user_utils::can_earn_points(\context_system::instance(), $u1->id));
        $this->assertTrue(user_utils::can_earn_points(\context_system::instance(), $u2->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $guestuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $noreplyuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $adminuser->id));

        $this->assertTrue(user_utils::can_earn_points(\context_course::instance($c1->id), $u1->id));
        $this->assertTrue(user_utils::can_earn_points(\context_course::instance($c1->id), $u2->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $guestuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $noreplyuser->id));
        $this->assertFalse(user_utils::can_earn_points(\context_course::instance($c1->id), $adminuser->id));
    }

    /**
     * Test.
     *
     * @covers \block_xp\local\utils\user_utils::can_earn_points
     */
    public function test_can_earn_points_for_admins(): void {
        $adminuser = get_admin();

        $this->assertFalse(user_utils::can_earn_points(\context_system::instance(), $adminuser->id));
        $config = di::get('config');
        $config->set('adminscanearnxp', true);
        $this->assertTrue(user_utils::can_earn_points(\context_system::instance(), $adminuser->id));
    }

}
