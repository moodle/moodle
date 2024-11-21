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
 * Leaderboard factory tests.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\di;
use block_xp\local\config\config_stack;
use block_xp\local\config\course_world_config;
use block_xp\local\config\static_config;
use block_xp\local\sql\limit;
use block_xp\tests\base_testcase;

/**
 * Leaderboard factory testcase.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class leaderboard_factory_test extends base_testcase {

    /**
     * Test the plain factory.
     *
     * @covers \block_xp\local\factory\default_course_world_leaderboard_factory
     */
    public function test_plain_course_world_factory_without_groups(): void {
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

        $world = $this->get_world($c1->id);
        $store = $world->get_store();
        $store->set($u1->id, 100);
        $store->set($u2->id, 120);
        $store->set($u3->id, 130);
        $store->set($u4->id, 140);
        $store->set($u5->id, 150);
        $store->set($u6->id, 160);
        $store->set($u7->id, 170);
        $store->set($u8->id, 180);

        $factory = di::get('course_world_leaderboard_factory');
        $lb = $factory->get_course_leaderboard($world);

        $this->assertEquals(8, $lb->get_count());
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 1],
            [$u7, 2],
            [$u6, 3],
            [$u5, 4],
            [$u4, 5],
            [$u3, 6],
            [$u2, 7],
            [$u1, 8],
        ]);
        $this->assertEquals(fullname($u1), $lb->get_rank($u1->id)->get_state()->get_name());
        $this->assertEquals(fullname($u8), $lb->get_rank($u8->id)->get_state()->get_name());

        // Without a rank.
        $world->get_config()->set('rankmode', course_world_config::RANK_OFF);
        $lb = $factory->get_course_leaderboard($world);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, 0],
            [$u6, 0],
            [$u5, 0],
            [$u4, 0],
            [$u3, 0],
            [$u2, 0],
            [$u1, 0],
        ]);

        // With a relative rank for u8.
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u8);
        $lb = $factory->get_course_leaderboard($world);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
            [$u6, -20],
            [$u5, -30],
            [$u4, -40],
            [$u3, -50],
            [$u2, -60],
            [$u1, -80],
        ]);

        // With a relative rank for u2.
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u2);
        $lb = $factory->get_course_leaderboard($world);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 60],
            [$u7, 50],
            [$u6, 40],
            [$u5, 30],
            [$u4, 20],
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u2.
        $world->get_config()->set('neighbours', 1);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u2);
        $lb = $factory->get_course_leaderboard($world);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u8.
        $world->get_config()->set('neighbours', 1);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u8);
        $lb = $factory->get_course_leaderboard($world);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
        ]);

        // With anonymity.
        $guestuser = guest_user();
        $world->get_config()->set('neighbours', 3);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $world->get_config()->set('identitymode', course_world_config::IDENTITY_OFF);
        $this->setUser($u4);
        $lb = $factory->get_course_leaderboard($world);
        $ranking = array_values(iterator_to_array($lb->get_ranking(new limit(0, 0))));
        $this->assertEquals(30, $ranking[0]->get_rank());
        $this->assertEquals(20, $ranking[1]->get_rank());
        $this->assertEquals(10, $ranking[2]->get_rank());
        $this->assertEquals(0, $ranking[3]->get_rank());
        $this->assertEquals(-10, $ranking[4]->get_rank());
        $this->assertEquals(-20, $ranking[5]->get_rank());
        $this->assertEquals(-40, $ranking[6]->get_rank());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[0]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[0]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[1]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[1]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[2]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[2]->get_state()->get_id());
        $this->assertEquals(fullname($u4), $ranking[3]->get_state()->get_name());
        $this->assertEquals($u4->id, $ranking[3]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[4]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[4]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[5]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[5]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[6]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[6]->get_state()->get_id());
    }

    /**
     * Test the plain factory.
     *
     * @covers \block_xp\local\factory\default_leaderboard_factory_maker
     * @covers \block_xp\local\factory\world_leaderboard_factory
     */
    public function test_leaderboard_factory_without_groups(): void {
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

        $world = $this->get_world($c1->id);
        $store = $world->get_store();
        $store->set($u1->id, 100);
        $store->set($u2->id, 120);
        $store->set($u3->id, 130);
        $store->set($u4->id, 140);
        $store->set($u5->id, 150);
        $store->set($u6->id, 160);
        $store->set($u7->id, 170);
        $store->set($u8->id, 180);

        $factory = di::get('leaderboard_factory_maker')->get_leaderboard_factory($world, null);
        $lb = $factory->get_leaderboard();

        $this->assertEquals(8, $lb->get_count());
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 1],
            [$u7, 2],
            [$u6, 3],
            [$u5, 4],
            [$u4, 5],
            [$u3, 6],
            [$u2, 7],
            [$u1, 8],
        ]);
        $this->assertEquals(fullname($u1), $lb->get_rank($u1->id)->get_state()->get_name());
        $this->assertEquals(fullname($u8), $lb->get_rank($u8->id)->get_state()->get_name());

        // Without a rank.
        $world->get_config()->set('rankmode', course_world_config::RANK_OFF);
        $lb = $factory->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, 0],
            [$u6, 0],
            [$u5, 0],
            [$u4, 0],
            [$u3, 0],
            [$u2, 0],
            [$u1, 0],
        ]);

        // With a relative rank for u8.
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u8);
        $lb = $factory->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
            [$u6, -20],
            [$u5, -30],
            [$u4, -40],
            [$u3, -50],
            [$u2, -60],
            [$u1, -80],
        ]);

        // With a relative rank for u2.
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u2);
        $lb = $factory->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 60],
            [$u7, 50],
            [$u6, 40],
            [$u5, 30],
            [$u4, 20],
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u2.
        $world->get_config()->set('neighbours', 1);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u2);
        $lb = $factory->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u8.
        $world->get_config()->set('neighbours', 1);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $this->setUser($u8);
        $lb = $factory->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
        ]);

        // With anonymity.
        $guestuser = guest_user();
        $world->get_config()->set('neighbours', 3);
        $world->get_config()->set('rankmode', course_world_config::RANK_REL);
        $world->get_config()->set('identitymode', course_world_config::IDENTITY_OFF);
        $this->setUser($u4);
        $lb = $factory->get_leaderboard();
        $ranking = array_values(iterator_to_array($lb->get_ranking(new limit(0, 0))));
        $this->assertEquals(30, $ranking[0]->get_rank());
        $this->assertEquals(20, $ranking[1]->get_rank());
        $this->assertEquals(10, $ranking[2]->get_rank());
        $this->assertEquals(0, $ranking[3]->get_rank());
        $this->assertEquals(-10, $ranking[4]->get_rank());
        $this->assertEquals(-20, $ranking[5]->get_rank());
        $this->assertEquals(-40, $ranking[6]->get_rank());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[0]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[0]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[1]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[1]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[2]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[2]->get_state()->get_id());
        $this->assertEquals(fullname($u4), $ranking[3]->get_state()->get_name());
        $this->assertEquals($u4->id, $ranking[3]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[4]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[4]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[5]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[5]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[6]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[6]->get_state()->get_id());
    }

    /**
     * Test the with_config factory.
     *
     * @covers \block_xp\local\factory\default_course_world_leaderboard_factory
     */
    public function test_course_world_factory_with_config_without_groups(): void {
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

        $world = $this->get_world($c1->id);
        $store = $world->get_store();
        $store->set($u1->id, 100);
        $store->set($u2->id, 120);
        $store->set($u3->id, 130);
        $store->set($u4->id, 140);
        $store->set($u5->id, 150);
        $store->set($u6->id, 160);
        $store->set($u7->id, 170);
        $store->set($u8->id, 180);

        $factory = di::get('course_world_leaderboard_factory_with_config');
        $config = new config_stack([$world->get_config()]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);

        $this->assertEquals(8, $lb->get_count());
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 1],
            [$u7, 2],
            [$u6, 3],
            [$u5, 4],
            [$u4, 5],
            [$u3, 6],
            [$u2, 7],
            [$u1, 8],
        ]);
        $this->assertEquals(fullname($u1), $lb->get_rank($u1->id)->get_state()->get_name());
        $this->assertEquals(fullname($u8), $lb->get_rank($u8->id)->get_state()->get_name());

        // Without a rank.
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_OFF,
        ]), $world->get_config(), ]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, 0],
            [$u6, 0],
            [$u5, 0],
            [$u4, 0],
            [$u3, 0],
            [$u2, 0],
            [$u1, 0],
        ]);

        // With a relative rank for u8.
        $this->setUser($u8);
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
            [$u6, -20],
            [$u5, -30],
            [$u4, -40],
            [$u3, -50],
            [$u2, -60],
            [$u1, -80],
        ]);

        // With a relative rank for u2.
        $this->setUser($u2);
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 60],
            [$u7, 50],
            [$u6, 40],
            [$u5, 30],
            [$u4, 20],
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u2.
        $this->setUser($u2);
        $config = new config_stack([new static_config([
            'neighbours' => 1,
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u8.
        $this->setUser($u8);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
        ]);

        // With anonymity.
        $guestuser = guest_user();
        $this->setUser($u4);
        $config = new config_stack([new static_config([
            'neighbours' => 3,
            'rankmode' => course_world_config::RANK_REL,
            'identitymode' => course_world_config::IDENTITY_OFF,
        ]), $world->get_config(), ]);
        $lb = $factory->get_course_leaderboard_with_config($world, $config);
        $ranking = array_values(iterator_to_array($lb->get_ranking(new limit(0, 0))));
        $this->assertEquals(30, $ranking[0]->get_rank());
        $this->assertEquals(20, $ranking[1]->get_rank());
        $this->assertEquals(10, $ranking[2]->get_rank());
        $this->assertEquals(0, $ranking[3]->get_rank());
        $this->assertEquals(-10, $ranking[4]->get_rank());
        $this->assertEquals(-20, $ranking[5]->get_rank());
        $this->assertEquals(-40, $ranking[6]->get_rank());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[0]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[0]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[1]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[1]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[2]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[2]->get_state()->get_id());
        $this->assertEquals(fullname($u4), $ranking[3]->get_state()->get_name());
        $this->assertEquals($u4->id, $ranking[3]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[4]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[4]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[5]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[5]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[6]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[6]->get_state()->get_id());
    }

    /**
     * Test with config override.
     *
     * @covers \block_xp\local\factory\default_leaderboard_factory_maker
     * @covers \block_xp\local\factory\world_leaderboard_factory
     */
    public function test_leaderboard_factory_with_config_without_groups(): void {
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

        $world = $this->get_world($c1->id);
        $store = $world->get_store();
        $store->set($u1->id, 100);
        $store->set($u2->id, 120);
        $store->set($u3->id, 130);
        $store->set($u4->id, 140);
        $store->set($u5->id, 150);
        $store->set($u6->id, 160);
        $store->set($u7->id, 170);
        $store->set($u8->id, 180);

        $config = new config_stack([$world->get_config()]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();

        $this->assertEquals(8, $lb->get_count());
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 1],
            [$u7, 2],
            [$u6, 3],
            [$u5, 4],
            [$u4, 5],
            [$u3, 6],
            [$u2, 7],
            [$u1, 8],
        ]);
        $this->assertEquals(fullname($u1), $lb->get_rank($u1->id)->get_state()->get_name());
        $this->assertEquals(fullname($u8), $lb->get_rank($u8->id)->get_state()->get_name());

        // Without a rank.
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_OFF,
        ]), $world->get_config(), ]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, 0],
            [$u6, 0],
            [$u5, 0],
            [$u4, 0],
            [$u3, 0],
            [$u2, 0],
            [$u1, 0],
        ]);

        // With a relative rank for u8.
        $this->setUser($u8);
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
            [$u6, -20],
            [$u5, -30],
            [$u4, -40],
            [$u3, -50],
            [$u2, -60],
            [$u1, -80],
        ]);

        // With a relative rank for u2.
        $this->setUser($u2);
        $config = new config_stack([new static_config([
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 60],
            [$u7, 50],
            [$u6, 40],
            [$u5, 30],
            [$u4, 20],
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u2.
        $this->setUser($u2);
        $config = new config_stack([new static_config([
            'neighbours' => 1,
            'rankmode' => course_world_config::RANK_REL,
        ]), $world->get_config(), ]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u3, 10],
            [$u2, 0],
            [$u1, -20],
        ]);

        // With neighbours for u8.
        $this->setUser($u8);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $this->assert_ranking($lb->get_ranking(new limit(0, 0)), [
            [$u8, 0],
            [$u7, -10],
        ]);

        // With anonymity.
        $guestuser = guest_user();
        $this->setUser($u4);
        $config = new config_stack([new static_config([
            'neighbours' => 3,
            'rankmode' => course_world_config::RANK_REL,
            'identitymode' => course_world_config::IDENTITY_OFF,
        ]), $world->get_config(), ]);
        $lb = di::get('leaderboard_factory_maker')
            ->get_leaderboard_factory($world, $config)
            ->get_leaderboard();
        $ranking = array_values(iterator_to_array($lb->get_ranking(new limit(0, 0))));
        $this->assertEquals(30, $ranking[0]->get_rank());
        $this->assertEquals(20, $ranking[1]->get_rank());
        $this->assertEquals(10, $ranking[2]->get_rank());
        $this->assertEquals(0, $ranking[3]->get_rank());
        $this->assertEquals(-10, $ranking[4]->get_rank());
        $this->assertEquals(-20, $ranking[5]->get_rank());
        $this->assertEquals(-40, $ranking[6]->get_rank());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[0]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[0]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[1]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[1]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[2]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[2]->get_state()->get_id());
        $this->assertEquals(fullname($u4), $ranking[3]->get_state()->get_name());
        $this->assertEquals($u4->id, $ranking[3]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[4]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[4]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[5]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[5]->get_state()->get_id());
        $this->assertEquals(get_string('someoneelse', 'block_xp'), $ranking[6]->get_state()->get_name());
        $this->assertEquals($guestuser->id, $ranking[6]->get_state()->get_id());
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
        $this->assertEquals($i, count($expected));
    }

}
