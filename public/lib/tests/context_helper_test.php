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

namespace core;

/**
 * Unit tests for context helper class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context_helper
 */
final class context_helper_test extends \advanced_testcase {
    /**
     * Tests covered method.
     * @covers ::parse_external_level
     */
    public function test_parse_external_level(): void {
        $this->assertSame(context\system::class, context_helper::parse_external_level('system'));
        $this->assertSame(context\system::class, context_helper::parse_external_level(CONTEXT_SYSTEM));
        $this->assertSame(context\system::class, context_helper::parse_external_level((string)CONTEXT_SYSTEM));

        $this->assertSame(context\user::class, context_helper::parse_external_level('user'));
        $this->assertSame(context\user::class, context_helper::parse_external_level(CONTEXT_USER));
        $this->assertSame(context\user::class, context_helper::parse_external_level((string)CONTEXT_USER));

        $this->assertSame(context\coursecat::class, context_helper::parse_external_level('coursecat'));
        $this->assertSame(context\coursecat::class, context_helper::parse_external_level(CONTEXT_COURSECAT));
        $this->assertSame(context\coursecat::class, context_helper::parse_external_level((string)CONTEXT_COURSECAT));

        $this->assertSame(context\course::class, context_helper::parse_external_level('course'));
        $this->assertSame(context\course::class, context_helper::parse_external_level(CONTEXT_COURSE));
        $this->assertSame(context\course::class, context_helper::parse_external_level((string)CONTEXT_COURSE));

        $this->assertSame(context\module::class, context_helper::parse_external_level('module'));
        $this->assertSame(context\module::class, context_helper::parse_external_level(CONTEXT_MODULE));
        $this->assertSame(context\module::class, context_helper::parse_external_level((string)CONTEXT_MODULE));

        $this->assertSame(context\block::class, context_helper::parse_external_level('block'));
        $this->assertSame(context\block::class, context_helper::parse_external_level(CONTEXT_BLOCK));
        $this->assertSame(context\block::class, context_helper::parse_external_level((string)CONTEXT_BLOCK));

        $this->assertNull(context_helper::parse_external_level('core_system'));
        $this->assertNull(context_helper::parse_external_level('xsystem'));
        $this->assertNull(context_helper::parse_external_level(1));
        $this->assertNull(context_helper::parse_external_level(''));
    }

    /**
     * Tests covered method.
     * @covers ::resolve_behat_reference
     */
    public function test_resolve_behat_reference(): void {
        $this->assertNull(context_helper::resolve_behat_reference('blahbla', 'blahbla'));
        $this->assertNull(context_helper::resolve_behat_reference('', ''));
        $this->assertNull(context_helper::resolve_behat_reference('0', ''));

        $syscontext = context\system::instance();
        $result = context_helper::resolve_behat_reference('System', '');
        $this->assertSame($syscontext->id, $result->id);

        $syscontext = context\system::instance();
        $result = context_helper::resolve_behat_reference('10', '');
        $this->assertSame($syscontext->id, $result->id);

        // The rest is tested in each context class test.
    }

    /**
     * Tests covered method.
     * @covers ::get_class_for_level
     */
    public function test_get_class_for_level(): void {
        $this->assertSame(context\system::class, context_helper::get_class_for_level(CONTEXT_SYSTEM));
        $this->assertSame(context\system::class, context_helper::get_class_for_level((string)CONTEXT_SYSTEM));

        $this->assertSame(context\user::class, context_helper::get_class_for_level(CONTEXT_USER));
        $this->assertSame(context\user::class, context_helper::get_class_for_level((string)CONTEXT_USER));

        $this->assertSame(context\coursecat::class, context_helper::get_class_for_level(CONTEXT_COURSECAT));
        $this->assertSame(context\coursecat::class, context_helper::get_class_for_level((string)CONTEXT_COURSECAT));

        $this->assertSame(context\course::class, context_helper::get_class_for_level(CONTEXT_COURSE));
        $this->assertSame(context\course::class, context_helper::get_class_for_level((string)CONTEXT_COURSE));

        $this->assertSame(context\module::class, context_helper::get_class_for_level(CONTEXT_MODULE));
        $this->assertSame(context\module::class, context_helper::get_class_for_level((string)CONTEXT_MODULE));

        $this->assertSame(context\block::class, context_helper::get_class_for_level(CONTEXT_BLOCK));
        $this->assertSame(context\block::class, context_helper::get_class_for_level((string)CONTEXT_BLOCK));

        try {
            context_helper::get_class_for_level(1);
            $this->fail('Exception expected if level does not exist');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\coding_exception::class, $e);
            $this->assertSame('Coding error detected, it must be fixed by a programmer: Invalid context level specified',
                $e->getMessage());
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_all_levels
     */
    public function test_get_all_levels(): void {
        $levels = context_helper::get_all_levels();

        $this->assertArrayHasKey(CONTEXT_SYSTEM, $levels);
        $this->assertSame(context\system::class, $levels[CONTEXT_SYSTEM]);

        $this->assertArrayHasKey(CONTEXT_USER, $levels);
        $this->assertSame(context\user::class, $levels[CONTEXT_USER]);

        $this->assertArrayHasKey(CONTEXT_COURSECAT, $levels);
        $this->assertSame(context\coursecat::class, $levels[CONTEXT_COURSECAT]);

        $this->assertArrayHasKey(CONTEXT_COURSE, $levels);
        $this->assertSame(context\course::class, $levels[CONTEXT_COURSE]);

        $this->assertArrayHasKey(CONTEXT_MODULE, $levels);
        $this->assertSame(context\module::class, $levels[CONTEXT_MODULE]);

        $this->assertArrayHasKey(CONTEXT_BLOCK, $levels);
        $this->assertSame(context\block::class, $levels[CONTEXT_BLOCK]);

        $sorted = $levels;
        ksort($sorted, SORT_NUMERIC);
        $block = $sorted[CONTEXT_BLOCK];
        unset($sorted[CONTEXT_BLOCK]);
        $sorted[CONTEXT_BLOCK] = $block;
        $this->assertSame(array_keys($sorted), array_keys($levels));

        // Make sure level is set properly.
        foreach ($levels as $level => $classname) {
            $this->assertEquals($level, $classname::LEVEL);
            if ($level != CONTEXT_SYSTEM) {
                $this->assertGreaterThan(CONTEXT_SYSTEM, $level);
            }
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_child_levels
     */
    public function test_get_child_levels(): void {
        $alllevels = context_helper::get_all_levels();

        $childlevels = context_helper::get_child_levels(CONTEXT_SYSTEM);
        $this->assertSame(count($alllevels) - 1, count($childlevels));

        $childlevels = context_helper::get_child_levels(CONTEXT_USER);
        $this->assertNotContains(CONTEXT_SYSTEM, $childlevels);
        $this->assertNotContains(CONTEXT_USER, $childlevels);
        $this->assertNotContains(CONTEXT_COURSECAT, $childlevels);
        $this->assertNotContains(CONTEXT_COURSE, $childlevels);
        $this->assertNotContains(CONTEXT_MODULE, $childlevels);
        $this->assertContains(CONTEXT_BLOCK, $childlevels);

        $childlevels = context_helper::get_child_levels(CONTEXT_COURSECAT);
        $this->assertNotContains(CONTEXT_SYSTEM, $childlevels);
        $this->assertNotContains(CONTEXT_USER, $childlevels);
        $this->assertContains(CONTEXT_COURSECAT, $childlevels);
        $this->assertContains(CONTEXT_COURSE, $childlevels);
        $this->assertContains(CONTEXT_MODULE, $childlevels);
        $this->assertContains(CONTEXT_BLOCK, $childlevels);

        $childlevels = context_helper::get_child_levels(CONTEXT_COURSE);
        $this->assertNotContains(CONTEXT_SYSTEM, $childlevels);
        $this->assertNotContains(CONTEXT_USER, $childlevels);
        $this->assertNotContains(CONTEXT_COURSECAT, $childlevels);
        $this->assertNotContains(CONTEXT_COURSE, $childlevels);
        $this->assertContains(CONTEXT_MODULE, $childlevels);
        $this->assertContains(CONTEXT_BLOCK, $childlevels);

        $childlevels = context_helper::get_child_levels(CONTEXT_MODULE);
        $this->assertNotContains(CONTEXT_SYSTEM, $childlevels);
        $this->assertNotContains(CONTEXT_USER, $childlevels);
        $this->assertNotContains(CONTEXT_COURSECAT, $childlevels);
        $this->assertNotContains(CONTEXT_COURSE, $childlevels);
        $this->assertNotContains(CONTEXT_MODULE, $childlevels);
        $this->assertContains(CONTEXT_BLOCK, $childlevels);

        $childlevels = context_helper::get_child_levels(CONTEXT_BLOCK);
        $this->assertCount(0, $childlevels);
    }

    /**
     * Tests covered method.
     * @covers ::get_compatible_levels
     */
    public function test_get_compatible_levels(): void {
        $levels = context_helper::get_compatible_levels('manager');
        $this->assertContains(CONTEXT_SYSTEM, $levels);
        $this->assertNotContains(CONTEXT_USER, $levels);
        $this->assertContains(CONTEXT_COURSECAT, $levels);
        $this->assertContains(CONTEXT_COURSE, $levels);
        $this->assertNotContains(CONTEXT_MODULE, $levels);
        $this->assertNotContains(CONTEXT_BLOCK, $levels);

        $levels = context_helper::get_compatible_levels('coursecreator');
        $this->assertContains(CONTEXT_SYSTEM, $levels);
        $this->assertNotContains(CONTEXT_USER, $levels);
        $this->assertContains(CONTEXT_COURSECAT, $levels);
        $this->assertNotContains(CONTEXT_COURSE, $levels);
        $this->assertNotContains(CONTEXT_MODULE, $levels);
        $this->assertNotContains(CONTEXT_BLOCK, $levels);

        $levels = context_helper::get_compatible_levels('editingteacher');
        $this->assertNotContains(CONTEXT_SYSTEM, $levels);
        $this->assertNotContains(CONTEXT_USER, $levels);
        $this->assertNotContains(CONTEXT_COURSECAT, $levels);
        $this->assertContains(CONTEXT_COURSE, $levels);
        $this->assertContains(CONTEXT_MODULE, $levels);
        $this->assertNotContains(CONTEXT_BLOCK, $levels);

        $levels = context_helper::get_compatible_levels('teacher');
        $this->assertNotContains(CONTEXT_SYSTEM, $levels);
        $this->assertNotContains(CONTEXT_USER, $levels);
        $this->assertNotContains(CONTEXT_COURSECAT, $levels);
        $this->assertContains(CONTEXT_COURSE, $levels);
        $this->assertContains(CONTEXT_MODULE, $levels);
        $this->assertNotContains(CONTEXT_BLOCK, $levels);

        $levels = context_helper::get_compatible_levels('student');
        $this->assertNotContains(CONTEXT_SYSTEM, $levels);
        $this->assertNotContains(CONTEXT_USER, $levels);
        $this->assertNotContains(CONTEXT_COURSECAT, $levels);
        $this->assertContains(CONTEXT_COURSE, $levels);
        $this->assertContains(CONTEXT_MODULE, $levels);
        $this->assertNotContains(CONTEXT_BLOCK, $levels);

        $levels = context_helper::get_compatible_levels('user');
        $this->assertCount(0, $levels);

        $levels = context_helper::get_compatible_levels('guest');
        $this->assertCount(0, $levels);

        $levels = context_helper::get_compatible_levels('frontpage');
        $this->assertCount(0, $levels);
    }

    /**
     * Tests covered method.
     * @covers ::cleanup_instances
     */
    public function test_cleanup_instances(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $prevcount = $DB->count_records('context', []);
        context_helper::cleanup_instances();
        $count = $DB->count_records('context', []);
        $this->assertSame($prevcount, $count);

        // Insert bogus records for each level and see if they get removed,
        // more test should be in tests for each context level.
        $alllevels = context_helper::get_all_levels();
        foreach ($alllevels as $classname) {
            if ($classname::LEVEL == CONTEXT_SYSTEM) {
                continue;
            }
            $record = new \stdClass();
            $record->contextlevel = $classname::LEVEL;
            $record->instanceid = SQL_INT_MAX;
            $record->path = null;
            $record->depth = '2';
            $record->id = $DB->insert_record('context', $record);
            $DB->set_field('context', 'path', SYSCONTEXTID . '/' . $record->id, ['id' => $record->id]);
        }
        context_helper::cleanup_instances();
        $count = $DB->count_records('context', []);
        $this->assertSame($prevcount, $count);
    }

    /**
     * Tests covered method.
     * @covers ::create_instances
     */
    public function test_create_instances(): void {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = context\user::instance($user->id);
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context\coursecat::instance($category->id);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context\course::instance($course->id);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $pagecontext = context\module::instance($page->cmid);

        $prevcount = $DB->count_records('context', []);
        $DB->delete_records('context', ['id' => $usercontext->id]);
        $DB->delete_records('context', ['id' => $categorycontext->id]);
        $DB->delete_records('context', ['id' => $coursecontext->id]);
        $DB->delete_records('context', ['id' => $pagecontext->id]);

        context_helper::create_instances();
        $count = $DB->count_records('context', []);
        $this->assertSame($prevcount, $count);
    }

    /**
     * Tests covered method.
     * @covers ::build_all_paths
     */
    public function test_build_all_paths(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        // Just make sure there are no fatal errors for now.
        context_helper::build_all_paths(true);
        context_helper::build_all_paths();
    }

    /**
     * Tests covered method.
     * @covers ::reset_caches
     */
    public function test_reset_caches(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        // Just make sure there are no fatal errors for now.
        context_helper::reset_caches();
    }

    /**
     * Tests covered method.
     * @covers ::get_preload_record_columns
     */
    public function test_get_preload_record_columns(): void {
        $expected = array (
            'testalias.id' => 'ctxid',
            'testalias.path' => 'ctxpath',
            'testalias.depth' => 'ctxdepth',
            'testalias.contextlevel' => 'ctxlevel',
            'testalias.instanceid' => 'ctxinstance',
            'testalias.locked' => 'ctxlocked',
        );
        $result = context_helper::get_preload_record_columns('testalias');
        $this->assertSame($expected, $result);
    }

    /**
     * Tests covered method.
     * @covers ::get_preload_record_columns_sql
     */
    public function test_get_preload_record_columns_sql(): void {
        global $DB;

        $result = context_helper::get_preload_record_columns_sql('testalias');
        $expected = 'testalias.id AS ctxid, testalias.path AS ctxpath, testalias.depth AS ctxdepth,'
            .' testalias.contextlevel AS ctxlevel, testalias.instanceid AS ctxinstance, testalias.locked AS ctxlocked';
        $this->assertSame($expected, $result);

        $sql = "SELECT id, $result
                  FROM {context} testalias";
        $DB->get_records_sql($sql, []);
    }

    /**
     * Tests covered method.
     * @covers ::preload_from_record
     */
    public function test_preload_from_record(): void {
        global $DB;

        $select = context_helper::get_preload_record_columns_sql('testalias');
        $sql = "SELECT id, $select
                  FROM {context} testalias";
        $records = $DB->get_records_sql($sql, []);
        foreach ($records as $record) {
            $this->assertNull(context_helper::preload_from_record($record));
        }

        $this->assertDebuggingNotCalled();
        $record = reset($records);
        unset($record->ctxlevel);
        $this->assertNull(context_helper::preload_from_record($record));
    }

    /**
     * Tests covered method.
     * @covers ::preload_contexts_by_id
     */
    public function test_preload_contexts_by_id(): void {
        global $DB;

        $contextids = $DB->get_fieldset_sql("SELECT id FROM {context}", []);
        context_helper::preload_contexts_by_id($contextids);
        context_helper::preload_contexts_by_id($contextids);

        context_helper::reset_caches();
        context_helper::preload_contexts_by_id($contextids);
    }

    /**
     * Tests covered method.
     * @covers ::preload_course
     */
    public function test_preload_course(): void {
        global $SITE;
        context_helper::preload_course($SITE->id);
    }

    /**
     * Tests covered method.
     * @covers ::delete_instance
     */
    public function test_delete_instance(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));

        // This is a bit silly test, it might start failing in the future
        // because the instances are not deleted before deleting the contexts.
        context_helper::delete_instance(CONTEXT_USER, $user->id);
        context_helper::delete_instance(CONTEXT_COURSECAT, $category->id);
        context_helper::delete_instance(CONTEXT_COURSE, $course->id);
        context_helper::delete_instance(CONTEXT_MODULE, $page->cmid);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name(): void {
        $allevels = context_helper::get_all_levels();
        foreach ($allevels as $level => $classname) {
            $name = context_helper::get_level_name($level);
            $this->assertIsString($name);
        }

        $this->assertSame('System', context_helper::get_level_name(CONTEXT_SYSTEM));
        $this->assertSame('User', context_helper::get_level_name(CONTEXT_USER));
        $this->assertSame('Category', context_helper::get_level_name(CONTEXT_COURSECAT));
        $this->assertSame('Course', context_helper::get_level_name(CONTEXT_COURSE));
        $this->assertSame('Activity module', context_helper::get_level_name(CONTEXT_MODULE));
        $this->assertSame('Block', context_helper::get_level_name(CONTEXT_BLOCK));
    }

    /**
     * Tests covered method.
     * @covers ::get_navigation_filter_context
     */
    public function test_get_navigation_filter_context(): void {
        global $CFG;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = context\user::instance($user->id);
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context\coursecat::instance($category->id);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context\course::instance($course->id);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $pagecontext = context\module::instance($page->cmid);
        $systemcontext = context\system::instance();

        // First test passed values are returned if disabled.
        set_config('filternavigationwithsystemcontext', '0');

        $this->assertNull(context_helper::get_navigation_filter_context(null));

        $filtercontext = context_helper::get_navigation_filter_context($systemcontext);
        $this->assertSame($systemcontext, $filtercontext);

        $filtercontext = context_helper::get_navigation_filter_context($usercontext);
        $this->assertSame($usercontext, $filtercontext);

        $filtercontext = context_helper::get_navigation_filter_context($categorycontext);
        $this->assertSame($categorycontext, $filtercontext);

        $filtercontext = context_helper::get_navigation_filter_context($coursecontext);
        $this->assertSame($coursecontext, $filtercontext);

        $filtercontext = context_helper::get_navigation_filter_context($pagecontext);
        $this->assertSame($pagecontext, $filtercontext);

        // Now test that any input returns system context if enabled.
        set_config('filternavigationwithsystemcontext', '1');

        $filtercontext = context_helper::get_navigation_filter_context(null);
        $this->assertSame($systemcontext->id, $filtercontext->id);

        $filtercontext = context_helper::get_navigation_filter_context($systemcontext);
        $this->assertSame($systemcontext->id, $filtercontext->id);

        $filtercontext = context_helper::get_navigation_filter_context($usercontext);
        $this->assertSame($systemcontext->id, $filtercontext->id);

        $filtercontext = context_helper::get_navigation_filter_context($categorycontext);
        $this->assertSame($systemcontext->id, $filtercontext->id);

        $filtercontext = context_helper::get_navigation_filter_context($coursecontext);
        $this->assertSame($systemcontext->id, $filtercontext->id);

        $filtercontext = context_helper::get_navigation_filter_context($pagecontext);
        $this->assertSame($systemcontext->id, $filtercontext->id);
    }
}
