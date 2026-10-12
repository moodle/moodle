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

namespace core_badges;

use context_course;
use context_system;
use core_badges\award_selector_base;
use core_badges\tests\badges_testcase;
use ReflectionException;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Unit tests for award_selector_base abstract class.
 *
 * @package     core_badges
 * @copyright   2025 Dai Nguyen Trong <ngtrdai@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_badges\award_manager::class)]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\award_selector_base::class, '__construct')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\award_selector_base::class, 'get_options')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\award_selector_base::class, 'get_groups_sql')]
final class award_selector_base_test extends badges_testcase {
    /**
     * Data provider for context types and badge configurations.
     */
    public static function context_badge_provider(): array {
        return [
            'System context with site badge' => [
                'contexttype' => 'system',
                'badgetype' => BADGE_TYPE_SITE,
                'courseid' => null,
            ],
            'Course context with course badge' => [
                'contexttype' => 'course',
                'badgetype' => BADGE_TYPE_COURSE,
                'courseid' => 'create_course',
            ],
        ];
    }

    /**
     * Data provider for group configurations.
     *
     * @return array Test scenarios with different group setups.
     */
    public static function group_configurations_provider(): array {
        return [
            'No group' => [
                'currentgroup' => 0,
                'expectedsql' => '',
                'expectedwhere' => '',
                'expectedparams' => [],
            ],
            'With group' => [
                'currentgroup' => 5,
                'expectedsql' => ' JOIN {groups_members} gm ON gm.userid = u.id ',
                'expectedwhere' => ' AND gm.groupid = :gr_grpid ',
                'expectedparams' => ['gr_grpid' => 5],
            ],
        ];
    }

    /**
     * Test constructor with different context types.
     *
     * @param string $contexttype The type of context to test.
     * @param int $badgetype The type of badge to create.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('context_badge_provider')]
    public function test_constructor_with_context(string $contexttype, int $badgetype, $courseid): void {
        $this->resetAfterTest();

        // Create course if needed.
        if ($courseid === 'create_course') {
            $course = $this->getDataGenerator()->create_course();
            $courseid = $course->id;
        }

        // Create a badge.
        $badgedata = ['type' => $badgetype];
        if ($badgetype === BADGE_TYPE_COURSE) {
            $badgedata['courseid'] = $courseid;
        }
        $badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge($badgedata);

        // Create appropriate context.
        if ($contexttype === 'system') {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($courseid);
        }

        $options = [
            'context' => $context,
            'badgeid' => $badge->id,
            'issuerid' => 1,
            'issuerrole' => 1,
            'url' => 'http://example.com',
            'currentgroup' => 0,
        ];

        $selector = $this->create_test_selector('test', $options);

        // Verify context handling.
        if ($contexttype === 'system') {
            // System context should be converted to frontpage course context.
            $this->assertInstanceOf(context_course::class, $this->get_protected_property($selector, 'context'));
            $this->assertEquals(SITEID, $this->get_protected_property($selector, 'context')->instanceid);
        } else {
            // Course context should be preserved.
            $this->assertEquals($context, $this->get_protected_property($selector, 'context'));
        }

        $this->assertEquals($badge->id, $this->get_protected_property($selector, 'badgeid'));
        $this->assertEquals(1, $this->get_protected_property($selector, 'issuerid'));
        $this->assertEquals(1, $this->get_protected_property($selector, 'issuerrole'));
        $this->assertEquals('http://example.com', $selector->url);
    }

    /**
     * Test constructor with default currentgroup from global COURSE.
     */
    public function test_constructor_with_default_currentgroup(): void {
        global $COURSE;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $COURSE = $course;

        $badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge([
            'type' => BADGE_TYPE_COURSE,
            'courseid' => $course->id,
        ]);

        $coursecontext = context_course::instance($course->id);
        $options = [
            'context' => $coursecontext,
            'badgeid' => $badge->id,
            'issuerid' => 1,
            'issuerrole' => 1,
        ];

        $selector = $this->create_test_selector('test', $options);

        $expectedgroup = groups_get_course_group($COURSE, true);
        $this->assertEquals($expectedgroup, $selector->currentgroup);
    }

    /**
     * Test get_options method.
     */
    public function test_get_options(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge([
            'type' => BADGE_TYPE_COURSE,
            'courseid' => $course->id,
        ]);

        $coursecontext = context_course::instance($course->id);
        $options = [
            'context' => $coursecontext,
            'badgeid' => $badge->id,
            'issuerid' => 1,
            'issuerrole' => 1,
            'currentgroup' => 3,
        ];

        $selector = $this->create_test_selector('test', $options);
        $getoptions = $this->call_protected_method($selector, 'get_options');

        $this->assertEquals('badges/classes/award_selector_base.php', $getoptions['file']);
        $this->assertEquals($coursecontext, $getoptions['context']);
        $this->assertEquals($badge->id, $getoptions['badgeid']);
        $this->assertEquals(1, $getoptions['issuerid']);
        $this->assertEquals(1, $getoptions['issuerrole']);
        $this->assertEquals(3, $getoptions['currentgroup']);
    }

    /**
     * Test get_groups_sql method with different group configurations.
     *
     * @param int $currentgroup The current group ID.
     * @param string $expectedsql Expected group SQL.
     * @param string $expectedwhere Expected where clause.
     * @param array $expectedparams Expected parameters.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('group_configurations_provider')]
    public function test_get_groups_sql(
        int $currentgroup,
        string $expectedsql,
        string $expectedwhere,
        array $expectedparams
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge([
            'type' => BADGE_TYPE_COURSE,
            'courseid' => $course->id,
        ]);

        $coursecontext = context_course::instance($course->id);
        $options = [
            'context' => $coursecontext,
            'badgeid' => $badge->id,
            'issuerid' => 1,
            'issuerrole' => 1,
            'currentgroup' => $currentgroup,
        ];

        $selector = $this->create_test_selector('test', $options);
        [$groupsql, $groupwheresql, $groupwheresqlparams] = $this->call_protected_method($selector, 'get_groups_sql');

        $this->assertEquals($expectedsql, $groupsql);
        $this->assertEquals($expectedwhere, $groupwheresql);
        $this->assertEquals($expectedparams, $groupwheresqlparams);
    }

    /**
     * Create a test implementation of award_selector_base.
     *
     * @param string $name The name of the selector.
     * @param array $options Configuration options for the selector.
     * @return award_selector_base Test implementation instance.
     */
    private function create_test_selector(string $name, array $options): award_selector_base {
        return new class ($name, $options) extends award_selector_base {
            /**
             * Implementation of abstract find_users method for testing.
             *
             * @param string $search The search string.
             * @return array Array of users.
             */
            public function find_users($search): array {
                return ['test' => []];
            }
        };
    }

    /**
     * Helper method to access protected properties.
     *
     * @param object $object The object instance.
     * @param string $property The property name.
     * @return mixed The property value.
     * @throws ReflectionException
     */
    private function get_protected_property(object $object, string $property): mixed {
        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        return $prop->getValue($object);
    }

    /**
     * Helper method to call protected methods.
     *
     * @param object $object The object instance.
     * @param string $method The method name.
     * @param array $args Method arguments.
     * @return mixed The method return value.
     * @throws ReflectionException
     */
    private function call_protected_method(object $object, string $method, array $args = []): mixed {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        return $method->invokeArgs($object, $args);
    }
}
