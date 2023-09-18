<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\context;

use core\context, core\context_helper;

/**
 * Unit tests for course context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\course
 */
class course_test extends \advanced_testcase {
    /**
     * Tests legacy class.
     * @coversNothing
     */
    public function test_legacy_classname() {
        global $SITE;
        $course = $SITE;
        $context = \context_course::instance($course->id);
        $this->assertInstanceOf(course::class, $context);
        $this->assertInstanceOf(\context_course::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods() {
        global $SITE;
        $course = $SITE;
        $context = course::instance($course->id);
        $this->assertInstanceOf(course::class, $context);
        $this->assertSame($course->id, $context->instanceid);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(course::class, $context);
        $this->assertSame($course->id, $context->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name() {
        $this->assertSame('course', course::get_short_name());
    }

    /**
     * Tests levels.
     * @coversNothing
     */
    public function test_level() {
        $this->assertSame(50, course::LEVEL);
        $this->assertSame(CONTEXT_COURSE, course::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name() {
        $this->assertSame('Course', course::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Test course', 'shortname' => 'TST']);
        $context = course::instance($course->id);

        $this->assertSame('Course: Test course', $context->get_context_name());
        $this->assertSame('Course: Test course', $context->get_context_name(true));
        $this->assertSame('Test course', $context->get_context_name(false));
        $this->assertSame('TST', $context->get_context_name(false, true));
        $this->assertSame('Course: TST', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = course::instance($course->id);

        $expected = new \moodle_url('/course/view.php', ['id' => $course->id]);
        $url = $context->get_url();
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertSame($expected->out(), $url->out());
    }

    /**
     * Tests covered methods.
     * @covers ::get_instance_table()
     * @covers ::get_behat_reference_columns()
     * @covers \core\context_helper::resolve_behat_reference
     */
    public function test_resolve_behat_reference() {
        $this->resetAfterTest();

        $instance = $this->getDataGenerator()->create_course(['shortname' => 'xyz']);
        $context = context\course::instance($instance->id);

        $result = context_helper::resolve_behat_reference('Course', $instance->shortname);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('course', $instance->shortname);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('50', $instance->shortname);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('Course', 'dshjkdshjkhjsadjhdsa');
        $this->assertNull($result);

        $result = context_helper::resolve_behat_reference('Course', '');
        $this->assertNull($result);
    }

    /**
     * Tests covered method.
     * @covers ::get_compatible_role_archetypes
     */
    public function test_get_compatible_role_archetypes() {
        global $DB;

        $allarchetypes = $DB->get_fieldset_select('role', 'DISTINCT archetype', 'archetype IS NOT NULL');
        foreach ($allarchetypes as $allarchetype) {
            $levels = context_helper::get_compatible_levels($allarchetype);
            if ($allarchetype === 'editingteacher' || $allarchetype === 'teacher'
                || $allarchetype === 'student' || $allarchetype === 'manager') {
                $this->assertContains(course::LEVEL, $levels, "$allarchetype is expected to be compatible with context");
            } else {
                $this->assertNotContains(course::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
            }
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels() {
        $this->assertSame([coursecat::LEVEL], course::get_possible_parent_levels());
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = course::instance($course->id);

        $capabilities = $context->get_capabilities();
        $capabilities = convert_to_array($capabilities);
        $capabilities = array_column($capabilities, 'name');
        $this->assertContains('moodle/course:view', $capabilities);
        $this->assertContains('mod/page:view', $capabilities);
        $this->assertContains('mod/url:view', $capabilities);
        $this->assertNotContains('moodle/category:manage', $capabilities);
        $this->assertNotContains('moodle/user:viewalldetails', $capabilities);
    }

    /**
     * Tests covered method.
     * @covers ::create_level_instances
     */
    public function test_create_level_instances() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);

        $DB->delete_records('context', ['id' => $coursecontext->id]);
        context_helper::create_instances(course::LEVEL);
        $record = $DB->get_record('context', ['contextlevel' => course::LEVEL, 'instanceid' => $course->id], '*', MUST_EXIST);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);

        $context = course::instance($course->id);
        $children = $context->get_child_contexts();
        $this->assertCount(1, $children);
        $childcontext = reset($children);
        $this->assertInstanceOf(module::class, $childcontext);
        $this->assertEquals($page->cmid, $childcontext->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);

        $DB->delete_records('course', ['id' => $course->id]);

        context_helper::cleanup_instances();
        $this->assertFalse($DB->record_exists('context', ['contextlevel' => course::LEVEL, 'instanceid' => $course->id]));
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);
        $syscontext = system::instance();

        $DB->set_field('context', 'depth', 1, ['id' => $coursecontext->id]);
        $DB->set_field('context', 'path', '/0', ['id' => $coursecontext->id]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => $coursecontext->id]);
        $categorycontext = coursecat::instance($course->category);
        $this->assertSame('3', $record->depth);
        $this->assertSame('/' . $syscontext->id . '/' . $categorycontext->id . '/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked() {
        global $DB;
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $context1 = course::instance($course1->id);

        $context1->set_locked(true);
        $context1 = course::instance($course1->id);
        $this->assertTrue($context1->locked);
        $record = $DB->get_record('context', ['id' => $context1->id]);
        $this->assertSame('1', $record->locked);

        $context1->set_locked(false);
        $context1 = course::instance($course1->id);
        $this->assertFalse($context1->locked);
        $record = $DB->get_record('context', ['id' => $context1->id]);
        $this->assertSame('0', $record->locked);
    }
}
