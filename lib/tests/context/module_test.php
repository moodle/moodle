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
 * Unit tests for module context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\module
 */
class module_test extends \advanced_testcase {
    /**
     * Tests legacy class.
     * @coversNothing
     */
    public function test_legacy_classname() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $context = module::instance($page->cmid);

        $this->assertInstanceOf(module::class, $context);
        $this->assertInstanceOf(\context_module::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $context = module::instance($page->cmid);

        $this->assertInstanceOf(module::class, $context);
        $this->assertSame((string)$page->cmid, $context->instanceid);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(module::class, $context);
        $this->assertSame((string)$page->cmid, $context->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name() {
        $this->assertSame('module', module::get_short_name());
    }

    /**
     * Tests context level.
     * @coversNothing
     */
    public function test_level() {
        $this->assertSame(70, module::LEVEL);
        $this->assertSame(CONTEXT_MODULE, module::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name() {
        $this->assertSame('Activity module', module::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $this->assertSame('Page: Pokus', $context->get_context_name());
        $this->assertSame('Page: Pokus', $context->get_context_name(true));
        $this->assertSame('Pokus', $context->get_context_name(false));
        $this->assertSame('Pokus', $context->get_context_name(false, true));
        $this->assertSame('Page: Pokus', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $expected = new \moodle_url('/mod/page/view.php', ['id' => $page->cmid]);
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
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'idnumber' => 'xyz']);
        $instance = $DB->get_record('course_modules', ['id' => $page->cmid], '*', MUST_EXIST);
        $context = module::instance($instance->id);

        $result = context_helper::resolve_behat_reference('Activity module', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('module', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('70', $instance->idnumber);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('Activity module', 'dshjkdshjkhjsadjhdsa');
        $this->assertNull($result);

        $result = context_helper::resolve_behat_reference('Activity module', '');
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
            if ($allarchetype === 'editingteacher' || $allarchetype === 'teacher' || $allarchetype === 'student') {
                $this->assertContains(module::LEVEL, $levels, "$allarchetype is expected to be compatible with context");
            } else {
                $this->assertNotContains(module::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
            }
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels() {
        $this->assertSame([course::LEVEL], module::get_possible_parent_levels());
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $capabilities = $context->get_capabilities();
        $capabilities = convert_to_array($capabilities);
        $capabilities = array_column($capabilities, 'name');
        $this->assertContains('mod/page:view', $capabilities);
        $this->assertNotContains('mod/url:view', $capabilities);
        $this->assertNotContains('moodle/course:view', $capabilities);
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
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $DB->delete_records('context', ['id' => $context->id]);
        context_helper::create_instances(module::LEVEL);
        $record = $DB->get_record('context', ['contextlevel' => module::LEVEL, 'instanceid' => $page->cmid], '*', MUST_EXIST);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $children = $context->get_child_contexts();
        $this->assertCount(0, $children);
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $DB->delete_records('course_modules', ['id' => $page->cmid]);
        $DB->delete_records('page', ['id' => $page->id]);

        context_helper::cleanup_instances();
        $this->assertFalse($DB->record_exists('context', ['contextlevel' => module::LEVEL, 'instanceid' => $page->cmid]));
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths() {
        global $DB;
        $this->resetAfterTest();

        $syscontext = system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $pagecontext = module::instance($page->cmid);

        $DB->set_field('context', 'depth', 1, ['id' => $pagecontext->id]);
        $DB->set_field('context', 'path', '/0', ['id' => $pagecontext->id]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => $pagecontext->id]);
        $categorycontext = coursecat::instance($course->category);
        $this->assertSame('4', $record->depth);
        $this->assertSame('/' . $syscontext->id . '/' . $categorycontext->id . '/'
            . $coursecontext->id . '/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'name' => 'Pokus']);
        $context = module::instance($page->cmid);

        $context->set_locked(true);
        $context = module::instance($page->cmid);
        $this->assertTrue($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('1', $record->locked);

        $context->set_locked(false);
        $context = module::instance($page->cmid);
        $this->assertFalse($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('0', $record->locked);
    }
}
