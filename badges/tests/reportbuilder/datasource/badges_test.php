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

declare(strict_types=1);

namespace core_badges\reportbuilder\datasource;

use core_badges_generator;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for badges datasource
 *
 * @package     core_badges
 * @covers      \core_badges\reportbuilder\datasource\badges
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badges_test extends core_reportbuilder_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->libdir}/badgeslib.php");
    }

    /**
     * Test datasource
     */
    public function test_datasource(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Test users with a badge we can issue them with.
        $user1 = $this->getDataGenerator()->create_user(['firstname' => 'Alan', 'lastname' => 'Apple']);
        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'Barry', 'lastname' => 'Banana']);

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        $sitebadge = $generator->create_badge(['name' => 'Badge 1']);
        $sitebadge->issue($user1->id, true);
        $sitebadge->issue($user2->id, true);

        // Another badge, in a course, no issues.
        $course = $this->getDataGenerator()->create_course();
        $coursebadge = $generator->create_badge(['name' => 'Badge 2', 'type' => BADGE_TYPE_COURSE, 'courseid' => $course->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Badges', 'source' => badges::class, 'default' => 0]);

        // Badge course.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname'])
            ->set_many(['sortenabled' => true, 'sortdirection' => SORT_ASC])->update();

        // Badge name.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'badge:name'])
            ->set_many(['sortenabled' => true, 'sortdirection' => SORT_ASC])->update();

        // User fullname.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname'])
            ->set_many(['sortenabled' => true, 'sortdirection' => SORT_ASC])->update();

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        $this->assertEquals([
            ['PHPUnit test site', $sitebadge->name, fullname($user1, true)],
            ['PHPUnit test site', $sitebadge->name, fullname($user2, true)],
            [$course->fullname, $coursebadge->name, ''],
        ], array_map(static function(array $row): array {
            return array_values($row);
        }, $content));
    }

    /**
     * Test datasource using course/user entities that each contain tags
     */
    public function test_datasource_course_user_tags(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['tags' => ['horse']]);
        $user = $this->getDataGenerator()->create_user(['interests' => ['pie']]);

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        // Create course badge, issue to user.
        $badge = $generator->create_badge(['name' => 'Course badge', 'type' => BADGE_TYPE_COURSE, 'courseid' => $course->id]);
        $badge->issue($user->id, true);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create our report.
        $report = $generator->create_report(['name' => 'Badges', 'source' => badges::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'badge:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:tags']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:interests']);

        $content = $this->get_custom_report_content($report->get('id'));

        $this->assertCount(1, $content);
        $this->assertEquals([
            $badge->name,
            $course->fullname,
            'horse',
            fullname($user),
            'pie',
        ], array_values($content[0]));
    }
}
