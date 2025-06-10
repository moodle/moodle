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

namespace core_reportbuilder\local\aggregation;

use core_badges_generator;
use core_badges\reportbuilder\datasource\badges;
use core_reportbuilder_testcase;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for group concatenation aggregation
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\aggregation\base
 * @covers      \core_reportbuilder\local\aggregation\groupconcat
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupconcat_test extends core_reportbuilder_testcase {

    /**
     * Test aggregation when applied to column
     */
    public function test_column_aggregation(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Apple']);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Banana']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // First column, sorted.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname', 'sortenabled' => 1]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'aggregation' => groupconcat::get_class_name(),
        ]);

        // Assert lastname column was aggregated, and sorted predictably.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'c0_firstname' => 'Admin',
                'c1_lastname' => 'User',
            ],
            [
                'c0_firstname' => 'Bob',
                'c1_lastname' => 'Apple, Banana, Banana',
            ],
        ], $content);
    }

    /**
     * Test aggregation when applied to column with multiple fields
     */
    public function test_column_aggregation_multiple_fields(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Adam', 'lastname' => 'Apple']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullnamewithlink',
            'aggregation' => groupconcat::get_class_name(),
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        // Ensure users are sorted predictably (Adam -> Admin).
        [$userone, $usertwo] = explode(', ', reset($content[0]));
        $this->assertStringContainsString(fullname($user, true), $userone);
        $this->assertStringContainsString(fullname(get_admin(), true), $usertwo);
    }

    /**
     * Test aggregation when applied to column with callback
     */
    public function test_column_aggregation_with_callback(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 1]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 0]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'confirmed' => 1]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // First column, sorted.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname', 'sortenabled' => 1]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:confirmed',
            'aggregation' => groupconcat::get_class_name(),
        ]);

        // Assert confirmed column was aggregated, and sorted predictably with callback applied.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'c0_firstname' => 'Admin',
                'c1_confirmed' => 'Yes',
            ],
            [
                'c0_firstname' => 'Bob',
                'c1_confirmed' => 'No, Yes, Yes',
            ],
        ], $content);
    }

    /**
     * Test aggregation when applied to column with callback that expects/handles null values
     */
    public function test_datasource_aggregate_column_callback_with_null(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $userone = $this->getDataGenerator()->create_user(['description' => 'First user']);
        $usertwo = $this->getDataGenerator()->create_user(['description' => 'Second user']);

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        // Create course badge, issue to both users.
        $badgeone = $generator->create_badge(['name' => 'First badge']);
        $badgeone->issue($userone->id, true);
        $badgeone->issue($usertwo->id, true);

        // Create second badge, without issuing to anyone.
        $badgetwo = $generator->create_badge(['name' => 'Second badge']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Badges', 'source' => badges::class, 'default' => 0]);

        // First column, sorted.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'badge:name', 'sortenabled' => 1]);

        // This is the column we'll aggregate.
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:description',
            'aggregation' => groupconcat::get_class_name(),
        ]);

        // Assert description column was aggregated, with callbacks accounting for null values.
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'c0_name' => $badgeone->name,
                'c1_description' => "{$userone->description}, {$usertwo->description}",
            ],
            [
                'c0_name' => $badgetwo->name,
                'c1_description' => '',
            ],
        ], $content);
    }
}
