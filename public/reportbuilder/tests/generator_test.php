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

namespace core_reportbuilder;

use advanced_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\models\{audience, column, filter, report, schedule};
use core_tag_tag;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for the test data generator
 *
 * Note that assertions of created data content is performed in other testcases of the relevant classes, in the majority of cases
 * here we just want to assert that the thing we created actually exists
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder_generator
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends advanced_testcase {

    /**
     * Test creating a report
     */
    public function test_create_report(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'tags' => ['cat', 'dog']]);

        $this->assertTrue(report::record_exists($report->get('id')));
        $this->assertEqualsCanonicalizing(
            ['cat', 'dog'],
            array_values(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report', $report->get('id'))),
        );
    }

    /**
     * Test creating a column
     */
    public function test_create_column(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:lastname']);

        $this->assertTrue(column::record_exists($column->get('id')));
    }

    /**
     * Test creating a column, specifying additional properties
     */
    public function test_create_column_additional_properties(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => 0]);
        $column = $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'heading' => 'My pants',
            'sortenabled' => 1,
        ]);

        $this->assertEquals('My pants', $column->get('heading'));
        $this->assertTrue($column->get('sortenabled'));
    }

    /**
     * Test creating a filter
     */
    public function test_create_filter(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $filter = $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:lastname']);

        $this->assertTrue(filter::record_exists($filter->get('id')));
    }

    /**
     * Test creating a filter, specifying additional properties
     */
    public function test_create_filter_additional_properties(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => 0]);
        $filter = $generator->create_filter([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'heading' => 'My pants',
        ]);

        $this->assertEquals('My pants', $filter->get('heading'));
    }

    /**
     * Test creating a condition
     */
    public function test_create_condition(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $condition = $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:lastname']);

        $this->assertTrue(filter::record_exists($condition->get('id')));
    }

    /**
     * Test creating a condition, specifying additional properties
     */
    public function test_create_condition_additional_properties(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => 0]);
        $condition = $generator->create_condition([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:lastname',
            'heading' => 'My pants',
        ]);

        $this->assertEquals('My pants', $condition->get('heading'));
    }

    /**
     * Test creating an audience
     */
    public function test_create_audience(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        $this->assertTrue(audience::record_exists($audience->get_persistent()->get('id')));
    }

    /**
     * Test creating a schedule
     */
    public function test_create_schedule(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $schedule = $generator->create_schedule(['reportid' => $report->get('id'), 'name' => 'My schedule']);

        $this->assertTrue(schedule::record_exists($schedule->get('id')));
    }
}
