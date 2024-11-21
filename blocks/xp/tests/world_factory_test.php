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
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\local\course_world;
use block_xp\tests\base_testcase;
use context_course;
use context_system;

/**
 * Test case.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class world_factory_test extends base_testcase {

    /**
     * Test course world factory.
     *
     * @covers \block_xp\local\factory\course_world_factory
     * @covers \block_xp\local\factory\default_course_world_factory
     */
    public function test_course_world_factory(): void {
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $factory = di::get('course_world_factory');
        $config = di::get('config');

        // Confirm we're in course mode.
        $this->assertEquals($config->get('context'), CONTEXT_COURSE);

        $w1 = $factory->get_world($c1->id);
        $this->assertEquals($w1->get_courseid(), $c1->id);
        $this->assertEquals($w1->get_context()->id, context_course::instance($c1->id)->id);
        $this->assertInstanceOf(course_world::class, $w1);
        $w1b = $factory->get_world((string) $c1->id);
        $this->assertSame($w1, $w1b);

        $w2 = $factory->get_world($c2->id);
        $this->assertEquals($w2->get_courseid(), $c2->id);
        $this->assertEquals($w2->get_context()->id, context_course::instance($c2->id)->id);
        $this->assertInstanceOf(course_world::class, $w2);

        $w2b = $factory->get_world((string) $c2->id);
        $this->assertSame($w2, $w2b);

        $this->assertNotEquals($w1, $w2);
        $this->assertNotEquals($w1->get_courseid(), $w2->get_courseid());
        $this->assertNotEquals($w1->get_context()->id, $w2->get_context()->id);
    }

    /**
     * Test course world factory in system.
     *
     * @covers \block_xp\local\factory\course_world_factory
     * @covers \block_xp\local\factory\default_course_world_factory
     */
    public function test_course_world_factory_in_system(): void {
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $config = di::get('config');
        $config->set('context', CONTEXT_SYSTEM);
        $factory = di::get('course_world_factory');

        // Confirm that the course ID does not really matter.
        $w0 = $factory->get_world(SITEID);
        $w1 = $factory->get_world($c1->id);
        $w2 = $factory->get_world($c2->id);

        $this->assertEquals($w0->get_courseid(), SITEID);
        $this->assertEquals($w0->get_context()->id, context_system::instance()->id);
        $this->assertInstanceOf(course_world::class, $w0);
        $this->assertSame($w0, $w1);
        $this->assertSame($w1, $w2);
    }

    /**
     * Test context world factory.
     *
     * @covers \block_xp\local\factory\context_world_factory
     * @covers \block_xp\local\factory\default_context_world_factory
     */
    public function test_context_world_factory(): void {
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $ctx1 = context_course::instance($c1->id);
        $ctx2 = context_course::instance($c2->id);

        $factory = di::get('context_world_factory');
        $config = di::get('config');

        // Confirm we're in course mode.
        $this->assertEquals($config->get('context'), CONTEXT_COURSE);

        $w1 = $factory->get_world_from_context($ctx1);
        $this->assertEquals($w1->get_courseid(), $c1->id);
        $this->assertEquals($w1->get_context()->id, $ctx1->id);
        $this->assertInstanceOf(course_world::class, $w1);
        $w1b = $factory->get_world_from_context($ctx1);
        $this->assertSame($w1, $w1b);

        $w2 = $factory->get_world_from_context($ctx2);
        $this->assertEquals($w2->get_courseid(), $c2->id);
        $this->assertEquals($w2->get_context()->id, $ctx2->id);
        $this->assertInstanceOf(course_world::class, $w2);

        $this->assertNotEquals($w1, $w2);
        $this->assertNotEquals($w1->get_courseid(), $w2->get_courseid());
        $this->assertNotEquals($w1->get_context()->id, $w2->get_context()->id);

        // System context behaviour to return "frontpage".
        $w0 = $factory->get_world_from_context(context_system::instance());
        $this->assertEquals($w0->get_courseid(), SITEID);
        $this->assertEquals($w0->get_context()->id, context_system::instance()->id);
        $this->assertInstanceOf(course_world::class, $w0);
        $this->assertNotEquals($w0, $w1);
    }

    /**
     * Test context world factory in system.
     *
     * @covers \block_xp\local\factory\context_world_factory
     * @covers \block_xp\local\factory\default_context_world_factory
     */
    public function test_context_world_factory_in_system(): void {
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $ctx1 = context_course::instance($c1->id);
        $ctx2 = context_course::instance($c2->id);

        $config = di::get('config');
        $config->set('context', CONTEXT_SYSTEM);
        $factory = di::get('context_world_factory');

        // Confirm that the context does not really matter.
        $w0 = $factory->get_world_from_context(context_system::instance());
        $w1 = $factory->get_world_from_context($ctx1);
        $w2 = $factory->get_world_from_context($ctx2);

        $this->assertEquals($w0->get_courseid(), SITEID);
        $this->assertEquals($w0->get_context()->id, context_system::instance()->id);
        $this->assertInstanceOf(course_world::class, $w0);
        $this->assertSame($w0, $w1);
        $this->assertSame($w1, $w2);
    }

}
