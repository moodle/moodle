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
 * Unit tests for coursecat context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\coursecat
 */
final class coursecat_test extends \advanced_testcase {
    /**
     * Tests legacy class name.
     * @coversNothing
     */
    public function test_legacy_classname(): void {
        $category = \core_course_category::get_default();
        $context = \context_coursecat::instance($category->id);
        $this->assertInstanceOf(coursecat::class, $context);
        $this->assertInstanceOf(\context_coursecat::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods(): void {
        $category = \core_course_category::get_default();
        $context = coursecat::instance($category->id);
        $this->assertInstanceOf(coursecat::class, $context);
        $this->assertSame($category->id, $context->instanceid);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(coursecat::class, $context);
        $this->assertSame($category->id, $context->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name(): void {
        $this->assertSame('coursecat', coursecat::get_short_name());
    }

    /**
     * Tests levels.
     * @coversNothing
     */
    public function test_level(): void {
        $this->assertSame(40, coursecat::LEVEL);
        $this->assertSame(CONTEXT_COURSECAT, coursecat::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name(): void {
        $this->assertSame('Category', coursecat::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name(): void {
        $category = \core_course_category::get_default();
        $context = coursecat::instance($category->id);
        $this->assertSame('Category: Category 1', $context->get_context_name());
        $this->assertSame('Category: Category 1', $context->get_context_name(true));
        $this->assertSame('Category 1', $context->get_context_name(false));
        $this->assertSame('Category 1', $context->get_context_name(false, true));
        $this->assertSame('Category: Category 1', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url(): void {
        $category = \core_course_category::get_default();
        $context = coursecat::instance($category->id);
        $expected = new \moodle_url('/course/index.php', ['categoryid' => $category->id]);
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
    public function test_resolve_behat_reference(): void {
        $this->resetAfterTest();

        $instance = $this->getDataGenerator()->create_category(['idnumber' => 'xyz']);
        $context = context\coursecat::instance($instance->id);

        $result = context_helper::resolve_behat_reference('Category', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('coursecat', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('40', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('Category', 'dshjkdshjkhjsadjhdsa');
        $this->assertNull($result);

        $result = context_helper::resolve_behat_reference('Category', '');
        $this->assertNull($result);
    }

    /**
     * Tests covered method.
     * @covers ::get_compatible_role_archetypes
     */
    public function test_get_compatible_role_archetypes(): void {
        global $DB;

        $allarchetypes = $DB->get_fieldset_select('role', 'DISTINCT archetype', 'archetype IS NOT NULL');
        foreach ($allarchetypes as $allarchetype) {
            $levels = context_helper::get_compatible_levels($allarchetype);
            if ($allarchetype === 'manager' || $allarchetype === 'coursecreator') {
                $this->assertContains(coursecat::LEVEL, $levels, "$allarchetype is expected to be compatible with context");
            } else {
                $this->assertNotContains(coursecat::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
            }
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels(): void {
        $this->assertSame([system::LEVEL, coursecat::LEVEL], coursecat::get_possible_parent_levels());
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities(): void {
        $category = \core_course_category::get_default();

        $context = coursecat::instance($category->id);
        $capabilities = $context->get_capabilities();
        $capabilities = convert_to_array($capabilities);
        $capabilities = array_column($capabilities, 'name');
        $this->assertContains('moodle/category:manage', $capabilities);
        $this->assertContains('moodle/course:view', $capabilities);
        $this->assertNotContains('moodle/user:viewalldetails', $capabilities);
    }

    /**
     * Tests covered method.
     * @covers ::create_level_instances
     */
    public function test_create_level_instances(): void {
        global $DB;
        $this->resetAfterTest();

        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = coursecat::instance($coursecat->id);

        $DB->delete_records('context', ['id' => $coursecatcontext->id]);
        context_helper::create_instances(coursecat::LEVEL);
        $record = $DB->get_record('context', ['contextlevel' => coursecat::LEVEL, 'instanceid' => $coursecat->id], '*', MUST_EXIST);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts(): void {
        $this->resetAfterTest();

        $category = \core_course_category::get_default();

        $context = coursecat::instance($category->id);
        $children = $context->get_child_contexts();
        $this->assertCount(0, $children);

        $course = $this->getDataGenerator()->create_course(['categoryid' => $category->id]);

        // This may fail if some plugin auto-creates activities.
        $children = $context->get_child_contexts();
        $this->assertCount(1, $children);
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql(): void {
        global $DB;
        $this->resetAfterTest();

        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = coursecat::instance($coursecat->id);

        $DB->delete_records('course_categories', ['id' => $coursecat->id]);

        context_helper::cleanup_instances();
        $this->assertFalse($DB->record_exists('context', ['contextlevel' => coursecat::LEVEL, 'instanceid' => $coursecat->id]));
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths(): void {
        global $DB;
        $this->resetAfterTest();

        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = coursecat::instance($coursecat->id);
        $syscontext = system::instance();

        $DB->set_field('context', 'depth', 1, ['id' => $coursecatcontext->id]);
        $DB->set_field('context', 'path', '/0', ['id' => $coursecatcontext->id]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => $coursecatcontext->id]);
        $this->assertSame('2', $record->depth);
        $this->assertSame('/' . $syscontext->id . '/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked(): void {
        global $DB;
        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category(['parent' => $category1->id]);
        $context1 = coursecat::instance($category1->id);
        $context2 = coursecat::instance($category2->id);

        $context1->set_locked(true);
        $context1 = coursecat::instance($category1->id);
        $context2 = coursecat::instance($category2->id);
        $this->assertTrue($context1->locked);
        $this->assertTrue($context2->locked);
        $record = $DB->get_record('context', ['id' => $context1->id]);
        $this->assertSame('1', $record->locked);
        $record = $DB->get_record('context', ['id' => $context2->id]);
        $this->assertSame('0', $record->locked);

        $context1->set_locked(false);
        $context1 = coursecat::instance($category1->id);
        $context2 = coursecat::instance($category2->id);
        $this->assertFalse($context1->locked);
        $this->assertFalse($context2->locked);
        $record = $DB->get_record('context', ['id' => $context1->id]);
        $this->assertSame('0', $record->locked);
        $record = $DB->get_record('context', ['id' => $context2->id]);
        $this->assertSame('0', $record->locked);
    }
}
