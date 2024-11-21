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
 * Leaderboard tests.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\local\leaderboard\anonymisable_leaderboard;
use block_xp\local\leaderboard\course_user_leaderboard;
use block_xp\local\leaderboard\neighboured_leaderboard;
use block_xp\local\leaderboard\relative_ranker;
use block_xp\local\leaderboard\null_ranker;
use block_xp\local\sql\limit;
use block_xp\local\xp\full_anonymiser;
use block_xp\tests\base_testcase;
use core_text;

/**
 * Leaderboard testcase.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class leaderboard_test extends base_testcase {

    /**
     * Get leaderboard.
     *
     * @param local\world $world The world.
     * @param int $groupid The group ID.
     * @param local\leaderboard\ranker|null $ranker The ranker.
     * @return local\leaderboard\leaderboard
     */
    protected function get_leaderboard($world, $groupid = 0, $ranker = null) {
        global $DB, $USER;
        return new course_user_leaderboard(
            $DB,
            $world->get_levels_info(),
            $world->get_courseid(),
            ['rank', 'fullname'],
            $ranker,
            $groupid
        );
    }

    /**
     * Basic test of the leaderboard.
     *
     * @covers \block_xp\local\leaderboard\course_user_leaderboard
     */
    public function test_basic_leaderboard(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);
        $world2 = $this->get_world($c2->id);
        $store2 = $world2->get_store();
        $store2->set($u4->id, 140);

        $lb = $this->get_leaderboard($world1);

        $this->assertEquals(3, $lb->get_count());
        $this->assertEquals(2, $lb->get_position($u1->id));
        $this->assertEquals(1, $lb->get_position($u2->id));
        $this->assertEquals(0, $lb->get_position($u3->id));
        $this->assertEquals(3, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(2, $lb->get_rank($u2->id)->get_rank());
        $this->assertEquals(1, $lb->get_rank($u3->id)->get_rank());

        $this->assertNull($lb->get_position($u4->id));
        $this->assertNull($lb->get_rank($u4->id));

        $store1->set($u5->id, 10);
        $store1->set($u6->id, 20);
        $store1->set($u7->id, 20);
        $store1->set($u8->id, 30);

        // Testing limits.
        $ranking = $lb->get_ranking(new limit(0, 0));
        $this->assertCount(7, $ranking);
        $expected = [
            [$u3, 1],
            [$u2, 2],
            [$u1, 3],
            [$u8, 4],
            [$u6, 5],
            [$u7, 5],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);

        $ranking = $lb->get_ranking(new limit(2, 0));
        $this->assertCount(2, $ranking);
        $expected = [
            [$u3, 1],
            [$u2, 2],
        ];
        $this->assert_ranking($ranking, $expected);

        $ranking = $lb->get_ranking(new limit(3, 2));
        $this->assertCount(3, $ranking);
        $expected = [
            [$u1, 3],
            [$u8, 4],
            [$u6, 5],
        ];
        $this->assert_ranking($ranking, $expected);

        $ranking = $lb->get_ranking(new limit(3, 10));
        $this->assertCount(0, $ranking);
        $expected = [];
        $this->assert_ranking($ranking, $expected);
    }

    /**
     * Basic test of the leaderboard.
     *
     * @covers \block_xp\local\leaderboard\course_user_leaderboard
     */
    public function test_group_leaderboard(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $g1 = $dg->create_group(['courseid' => $c1->id]);
        $g2 = $dg->create_group(['courseid' => $c1->id]);
        $g3 = $dg->create_group(['courseid' => $c2->id]);

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $dg->enrol_user($u1->id, $c1->id);
        $dg->enrol_user($u2->id, $c1->id);
        $dg->enrol_user($u3->id, $c1->id);
        $dg->enrol_user($u4->id, $c1->id);
        $dg->enrol_user($u5->id, $c1->id);
        $dg->enrol_user($u6->id, $c1->id);
        $dg->enrol_user($u7->id, $c1->id);
        $dg->enrol_user($u8->id, $c1->id);

        $dg->create_group_member(['groupid' => $g1->id, 'userid' => $u1->id]);
        $dg->create_group_member(['groupid' => $g1->id, 'userid' => $u2->id]);
        $dg->create_group_member(['groupid' => $g1->id, 'userid' => $u3->id]);
        $dg->create_group_member(['groupid' => $g1->id, 'userid' => $u4->id]);

        $dg->create_group_member(['groupid' => $g2->id, 'userid' => $u4->id]);
        $dg->create_group_member(['groupid' => $g2->id, 'userid' => $u5->id]);
        $dg->create_group_member(['groupid' => $g2->id, 'userid' => $u6->id]);

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 10);
        $store1->set($u2->id, 20);
        $store1->set($u3->id, 30);
        $store1->set($u4->id, 40);
        $store1->set($u5->id, 50);
        $store1->set($u6->id, 60);
        $store1->set($u7->id, 70);
        $store1->set($u8->id, 80);

        $lb = $this->get_leaderboard($world1, 0);
        $this->assertEquals(8, $lb->get_count());
        $this->assertEquals(8, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(1, $lb->get_rank($u8->id)->get_rank());

        $lb = $this->get_leaderboard($world1, $g3->id);
        $this->assertDebuggingCalled('The $groupid argument of the leaderboard is deprecated, use set_user_filter() instead.');
        $this->assertEquals(0, $lb->get_count());
        $this->assertEquals(null, $lb->get_rank($u1->id));
        $this->assertEquals(null, $lb->get_rank($u8->id));

        $lb = $this->get_leaderboard($world1, $g1->id);
        $this->assertDebuggingCalled('The $groupid argument of the leaderboard is deprecated, use set_user_filter() instead.');
        $this->assertEquals(4, $lb->get_count());
        $this->assertEquals(4, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(1, $lb->get_rank($u4->id)->get_rank());
        $this->assertEquals(null, $lb->get_rank($u8->id));
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [[$u4, 1], [$u3, 2], [$u2, 3], [$u1, 4]]);

        $lb = $this->get_leaderboard($world1, $g2->id);
        $this->assertDebuggingCalled('The $groupid argument of the leaderboard is deprecated, use set_user_filter() instead.');
        $this->assertEquals(3, $lb->get_count());
        $this->assertEquals(null, $lb->get_rank($u1->id));
        $this->assertEquals(3, $lb->get_rank($u4->id)->get_rank());
        $this->assertEquals(1, $lb->get_rank($u6->id)->get_rank());
        $this->assertEquals(null, $lb->get_rank($u8->id));
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [[$u6, 1], [$u5, 2], [$u4, 3]]);
    }

    /**
     * Anonymised leaderboard.
     *
     * @covers \block_xp\local\leaderboard\anonymisable_leaderboard
     */
    public function test_anonymisable_leaderboard(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1);
        $this->assertEquals($u1->firstname, $lb->get_rank($u1->id)->get_state()->get_user()->firstname);
        $this->assertEquals($u2->firstname, $lb->get_rank($u2->id)->get_state()->get_user()->firstname);
        $this->assertEquals($u3->firstname, $lb->get_rank($u3->id)->get_state()->get_user()->firstname);

        $guest = guest_user();
        $alb = new anonymisable_leaderboard($lb, new full_anonymiser($guest, [$u2->id]));
        $this->assertFalse(core_text::strpos($alb->get_rank($u1->id)->get_state()->get_name(), $u1->firstname) !== false);
        $this->assertTrue(core_text::strpos($alb->get_rank($u2->id)->get_state()->get_name(), $u2->firstname) !== false);
        $this->assertFalse(core_text::strpos($alb->get_rank($u3->id)->get_state()->get_name(), $u3->firstname) !== false);
        $this->assert_ranking($alb->get_ranking(new limit(0, 0)), [[$guest, 1], [$u2, 2], [$guest, 3]]);
    }

    /**
     * Neighboured leaderboard.
     *
     * @covers \block_xp\local\leaderboard\neighboured_leaderboard
     */
    public function test_neighboured_leaderboard(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u5->id, 20);
        $store1->set($u6->id, 30);
        $store1->set($u7->id, 40);
        $store1->set($u1->id, 100);
        $store1->set($u4->id, 110);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1, 0);
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $this->assertEquals(5, $nlb->get_count());
        $this->assertEquals(2, $nlb->get_position($u1->id));
        $this->assertEquals(null, $nlb->get_position($u2->id));
        $this->assertEquals(null, $nlb->get_position($u8->id));
        $this->assertEquals(4, $nlb->get_rank($u1->id)->get_rank());
        $this->assertEquals(null, $nlb->get_rank($u2->id));
        $this->assertEquals(null, $nlb->get_rank($u8->id));
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $this->assertCount(5, $ranking);
        $expected = [
            [$u2, 2],
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
            [$u6, 6],
        ];
        $this->assert_ranking($ranking, $expected);

        // Relative to the first person.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $expected = [
            [$u3, 1],
            [$u2, 2],
            [$u4, 3],
        ];
        $this->assert_ranking($ranking, $expected);

        // Relative to the second person.
        $nlb = new neighboured_leaderboard($lb, $u2->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $expected = [
            [$u3, 1],
            [$u2, 2],
            [$u4, 3],
            [$u1, 4],
        ];
        $this->assert_ranking($ranking, $expected);

        // Relative to the second last.
        $nlb = new neighboured_leaderboard($lb, $u6->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $expected = [
            [$u1, 4],
            [$u7, 5],
            [$u6, 6],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);

        // Relative to the last.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $expected = [
            [$u7, 5],
            [$u6, 6],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);
    }

    /**
     * Neighboured leaderboard count and position.
     *
     * @covers \block_xp\local\leaderboard\neighboured_leaderboard
     */
    public function test_neighboured_leaderboard_count_and_position(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u5->id, 20);
        $store1->set($u6->id, 30);
        $store1->set($u7->id, 40);
        $store1->set($u1->id, 100);
        $store1->set($u4->id, 110);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1, 0);
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $this->assertEquals(3, $nlb->get_count());
        $this->assertEquals(1, $nlb->get_position($u1->id));

        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $this->assertEquals(5, $nlb->get_count());
        $this->assertEquals(2, $nlb->get_position($u1->id));

        $nlb = new neighboured_leaderboard($lb, $u1->id, 3);
        $this->assertEquals(7, $nlb->get_count());
        $this->assertEquals(3, $nlb->get_position($u1->id));

        $nlb = new neighboured_leaderboard($lb, $u1->id, 4);
        $this->assertEquals(7, $nlb->get_count());
        $this->assertEquals(3, $nlb->get_position($u1->id));

        $nlb = new neighboured_leaderboard($lb, $u1->id, 5);
        $this->assertEquals(7, $nlb->get_count());
        $this->assertEquals(3, $nlb->get_position($u1->id));

        $nlb = new neighboured_leaderboard($lb, $u5->id, 1);
        $this->assertEquals(2, $nlb->get_count());
        $this->assertEquals(1, $nlb->get_position($u5->id));

        $nlb = new neighboured_leaderboard($lb, $u5->id, 3);
        $this->assertEquals(4, $nlb->get_count());
        $this->assertEquals(3, $nlb->get_position($u5->id));

        $nlb = new neighboured_leaderboard($lb, $u5->id, 5);
        $this->assertEquals(6, $nlb->get_count());
        $this->assertEquals(5, $nlb->get_position($u5->id));

        $nlb = new neighboured_leaderboard($lb, $u3->id, 1);
        $this->assertEquals(2, $nlb->get_count());
        $this->assertEquals(0, $nlb->get_position($u3->id));

        $nlb = new neighboured_leaderboard($lb, $u3->id, 3);
        $this->assertEquals(4, $nlb->get_count());
        $this->assertEquals(0, $nlb->get_position($u3->id));

        $nlb = new neighboured_leaderboard($lb, $u3->id, 5);
        $this->assertEquals(6, $nlb->get_count());
        $this->assertEquals(0, $nlb->get_position($u3->id));

        $nlb = new neighboured_leaderboard($lb, $u6->id, 1);
        $this->assertEquals(3, $nlb->get_count());
        $this->assertEquals(1, $nlb->get_position($u6->id));

        $nlb = new neighboured_leaderboard($lb, $u6->id, 3);
        $this->assertEquals(5, $nlb->get_count());
        $this->assertEquals(3, $nlb->get_position($u6->id));

        $nlb = new neighboured_leaderboard($lb, $u6->id, 5);
        $this->assertEquals(7, $nlb->get_count());
        $this->assertEquals(5, $nlb->get_position($u6->id));
    }

    /**
     * Neighboured leaderboard top fallback.
     *
     * @covers \block_xp\local\leaderboard\neighboured_leaderboard
     */
    public function test_neighboured_leaderboard_top_fallback(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $c3 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u5->id, 20);
        $store1->set($u6->id, 30);
        $store1->set($u7->id, 40);
        $store1->set($u1->id, 100);
        $store1->set($u4->id, 110);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $world2 = $this->get_world($c2->id);
        $store2 = $world2->get_store();
        $store2->set($u7->id, 40);
        $store2->set($u1->id, 100);

        $worldempty = $this->get_world($c3->id);

        // Note that the user must be the same in the constructor and get_position,
        // otherwise we will get unexpected results as it's not how the leaderboard
        // should be used.
        // Values are: World, neighbours, user, count, position.
        $tests = [
            [$world1, 1, -1, 2, 0],
            [$world1, 1, $u8->id, 2, 0],
            [$world1, 2, $u8->id, 3, 0],
            [$world1, 3, $u8->id, 4, 0],
            [$world1, 4, $u8->id, 5, 0],
            [$world1, 5, $u8->id, 6, 0],

            [$world2, 1, -1, 2, 0],
            [$world2, 1, $u8->id, 2, 0],
            [$world2, 2, $u8->id, 2, 0],
            [$world2, 3, $u8->id, 2, 0],
            [$world2, 4, $u8->id, 2, 0],
            [$world2, 5, $u8->id, 2, 0],

            [$worldempty, 1, -1, 0, null],
            [$worldempty, 1, $u8->id, 0, null],
            [$worldempty, 2, $u8->id, 0, null],
            [$worldempty, 3, $u8->id, 0, null],
            [$worldempty, 4, $u8->id, 0, null],
            [$worldempty, 5, $u8->id, 0, null],
        ];

        foreach ($tests as $i => $test) {
            $lb = $this->get_leaderboard($test[0], 0);
            $nlb = new neighboured_leaderboard($lb, $test[2], $test[1], true);
            $this->assertEquals($test[3], $nlb->get_count(), 'Assertion failed for item ' . $i);
            $this->assertEquals($test[4], $nlb->get_position($test[2]), 'Assertion failed for item ' . $i);
        }

        // The fallback on top will not influence the count, the latter is based on the reference user.
        $lb = $this->get_leaderboard($world1, 0);
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2, true);
        $this->assertEquals(2, $nlb->get_position($u1->id));
        $this->assertEquals(null, $nlb->get_position($u2->id));
        $this->assertEquals(5, $nlb->get_count());

        // When the reference user is not present in leaderboard, then we fallback on top.
        $nlb = new neighboured_leaderboard($lb, $u8->id, 2, true);
        $this->assertEquals(0, $nlb->get_position($u8->id));
        $this->assertEquals(null, $nlb->get_position($u2->id));
        $this->assertEquals(3, $nlb->get_count());

        $lb = $this->get_leaderboard($world2, 0);
        $nlb = new neighboured_leaderboard($lb, $u8->id, 2, true);
        $this->assertEquals(0, $nlb->get_position($u8->id));
        $this->assertEquals(null, $nlb->get_position($u2->id));
        $this->assertEquals(2, $nlb->get_count());

        $lb = $this->get_leaderboard($worldempty, 0);
        $nlb = new neighboured_leaderboard($lb, $u8->id, 2, true);
        $this->assertEquals(null, $nlb->get_position($u8->id));
        $this->assertEquals(null, $nlb->get_position($u2->id));
        $this->assertEquals(null, $nlb->get_count());
    }

    /**
     * Neighboured leaderboard with custom limit.
     *
     * @covers \block_xp\local\leaderboard\neighboured_leaderboard
     */
    public function test_neighboured_leaderboard_with_limit(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();
        $u7 = $dg->create_user();
        $u8 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u5->id, 20);
        $store1->set($u6->id, 30);
        $store1->set($u7->id, 40);
        $store1->set($u1->id, 100);
        $store1->set($u4->id, 110);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1, 0);

        // With 0/0 limit.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 0));
        $expected = [
            [$u2, 2],
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
            [$u6, 6],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit of 2.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(2, 0));
        $expected = [
            [$u2, 2],
            [$u4, 3],
        ];
        $this->assert_ranking($ranking, $expected);

        // With offset of 2.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 2));
        $expected = [
            [$u1, 4],
            [$u7, 5],
            [$u6, 6],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(2, 1));
        $expected = [
            [$u4, 3],
            [$u1, 4],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset for first person.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 2);
        $ranking = $nlb->get_ranking(new limit(2, 1));
        $expected = [
            [$u2, 2],
            [$u4, 3],
        ];

        // With limit and offset for first person, again.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 5);
        $ranking = $nlb->get_ranking(new limit(2, 1));
        $expected = [
            [$u2, 2],
            [$u4, 3],
        ];

        // With limit and offset for first person, again.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 5);
        $ranking = $nlb->get_ranking(new limit(0, 1));
        $expected = [
            [$u2, 2],
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
            [$u6, 6],
        ];

        // With limit and offset for first person, again.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 5);
        $ranking = $nlb->get_ranking(new limit(1, 7));
        $expected = [];

        // With limit and offset for last person.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 2);
        $ranking = $nlb->get_ranking(new limit(2, 1));
        $expected = [
            [$u6, 6],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset for last person, again.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 5);
        $ranking = $nlb->get_ranking(new limit(2, 1));
        $expected = [
            [$u4, 3],
            [$u1, 4],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset to get last record.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 4));
        $expected = [
            [$u6, 6],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset to get last record for first person.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 4));
        $expected = [
            [$u4, 3],
        ];

        // With limit and offset to get last record for last person.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 2);
        $ranking = $nlb->get_ranking(new limit(0, 4));
        $expected = [];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of middle person.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 2);
        $ranking = $nlb->get_ranking(new limit(3, $nlb->get_position($u1->id) - 1));
        $expected = [
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of first person.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 2);
        $ranking = $nlb->get_ranking(new limit(3, $nlb->get_position($u3->id) - 1));
        $expected = [
            [$u3, 1],
            [$u2, 2],
            [$u4, 3],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of last person.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 2);
        $ranking = $nlb->get_ranking(new limit(3, $nlb->get_position($u5->id) - 1));
        $expected = [
            [$u6, 6],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of middle person on narrower leaderboard.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $ranking = $nlb->get_ranking(new limit(5, $nlb->get_position($u1->id) - 2));
        $expected = [
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of first person on narrower leaderboard.
        $nlb = new neighboured_leaderboard($lb, $u3->id, 1);
        $ranking = $nlb->get_ranking(new limit(5, $nlb->get_position($u3->id) - 2));
        $expected = [
            [$u3, 1],
            [$u2, 2],
        ];
        $this->assert_ranking($ranking, $expected);

        // With limit and offset from position of last person on narrower leaderboard.
        $nlb = new neighboured_leaderboard($lb, $u5->id, 1);
        $ranking = $nlb->get_ranking(new limit(5, $nlb->get_position($u5->id) - 2));
        $expected = [
            [$u6, 6],
            [$u5, 7],
        ];
        $this->assert_ranking($ranking, $expected);

        // With offset exceeding max items.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $ranking = $nlb->get_ranking(new limit(0, 8));
        $expected = [];
        $this->assert_ranking($ranking, $expected);

        // With offset less than 0, it would be ignored.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $ranking = $nlb->get_ranking(new limit(0, -1));
        $expected = [
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
        ];

        // With count exceeding max items.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $ranking = $nlb->get_ranking(new limit(10, 0));
        $expected = [
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
        ];

        // With negative count, would be ignored.
        $nlb = new neighboured_leaderboard($lb, $u1->id, 1);
        $ranking = $nlb->get_ranking(new limit(-10, 0));
        $expected = [
            [$u4, 3],
            [$u1, 4],
            [$u7, 5],
        ];
        $this->assert_ranking($ranking, $expected);
    }

    /**
     * Test relative ranker.
     *
     * @covers \block_xp\local\leaderboard\relative_ranker
     */
    public function test_relative_ranker(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1, 0, new relative_ranker($store1->get_state($u2->id)));
        $this->assertEquals(0, $lb->get_rank($u2->id)->get_rank());
        $this->assertEquals(-20, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(10, $lb->get_rank($u3->id)->get_rank());
        $expected = [[$u3, 10], [$u2, 0], [$u1, -20]];
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), $expected);

        $lb = $this->get_leaderboard($world1, 0, new relative_ranker());
        $this->assertEquals(0, $lb->get_rank($u2->id)->get_rank());
        $this->assertEquals(0, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(0, $lb->get_rank($u3->id)->get_rank());
        $expected = [[$u3, 0], [$u2, -10], [$u1, -30]];
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), $expected);
    }

    /**
     * Test null ranker.
     *
     * @covers \block_xp\local\leaderboard\relative_ranker
     */
    public function test_null_ranker(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 120);
        $store1->set($u3->id, 130);

        $lb = $this->get_leaderboard($world1, 0, new null_ranker());
        $this->assertEquals(0, $lb->get_rank($u2->id)->get_rank());
        $this->assertEquals(0, $lb->get_rank($u1->id)->get_rank());
        $this->assertEquals(0, $lb->get_rank($u3->id)->get_rank());
        $expected = [[$u3, 0], [$u2, 0], [$u1, 0]];
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), $expected);
    }

    /**
     * Test leaderboard with deleted users.
     *
     * @covers \block_xp\local\leaderboard\course_user_leaderboard
     */
    public function test_leaderboard_with_deleted_users(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 90);
        $store1->set($u3->id, 110);

        delete_user($u1);

        $lb = $this->get_leaderboard($world1);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 1],
            [$u2, 2],
        ]);
        $this->assertNull($lb->get_position($u1->id));
        $this->assertNull($lb->get_rank($u1->id));
    }

    /**
     * Test leaderboard with suspended users.
     *
     * @covers \block_xp\local\leaderboard\course_user_leaderboard
     */
    public function test_leaderboard_with_suspended_users(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $world1 = $this->get_world($c1->id);
        $store1 = $world1->get_store();
        $store1->set($u1->id, 100);
        $store1->set($u2->id, 90);
        $store1->set($u3->id, 110);

        $u1->suspended = 1;
        user_update_user($u1, false);

        $lb = $this->get_leaderboard($world1);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 1],
            [$u2, 2],
        ]);
        $this->assertNull($lb->get_position($u1->id));
        $this->assertNull($lb->get_rank($u1->id));
    }

    /**
     * Assert the ranking.
     *
     * @param local\xp\rank[] $ranking The ranking.
     * @param array $expected
     */
    protected function assert_ranking($ranking, array $expected) {
        $i = 0;
        foreach ($ranking as $rank) {
            $this->assertEquals($expected[$i][0]->id, $rank->get_state()->get_id(), $i);
            $this->assertEquals($expected[$i][1], $rank->get_rank(), $i);
            $i++;
        }
        $this->assertEquals(count($expected), $i);
    }

}
