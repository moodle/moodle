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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use core\context\system;
use core_reportbuilder_generator;
use core_reportbuilder\{datasource, system_report_factory};
use core_reportbuilder\local\models\{audience, column, filter, schedule};
use core_reportbuilder\local\systemreports\report_access_list;
use core_tag_tag;
use core_user\reportbuilder\datasource\users;
use invalid_parameter_exception;

/**
 * Unit tests for the report helper class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\report
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report_test extends advanced_testcase {

    /**
     * Test creation report
     */
    public function test_create_report(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $report = report::create_report((object) [
            'name' => 'My report with tags',
            'source' => users::class,
            'tags' => ['cat', 'dog'],
        ]);

        $this->assertEquals('My report with tags', $report->get('name'));
        $this->assertEquals(datasource::TYPE_CUSTOM_REPORT, $report->get('type'));
        $this->assertEqualsCanonicalizing(
            ['cat', 'dog'],
            array_values(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report', $report->get('id'))),
        );

        $report = report::create_report((object) [
            'name' => 'My report without tags',
            'source' => users::class,
        ]);

        $this->assertEquals('My report without tags', $report->get('name'));
        $this->assertEquals(datasource::TYPE_CUSTOM_REPORT, $report->get('type'));
        $this->assertEmpty(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report',
            $report->get('id')));
    }

    /**
     * Test updating report
     */
    public function test_update_report(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'uniquerows' => 0]);

        $reportupdated = report::update_report((object) [
            'id' => $report->get('id'),
            'name' => 'My renamed report without add tags',
            'uniquerows' => 1,
        ]);

        $this->assertEquals('My renamed report without add tags', $reportupdated->get('name'));
        $this->assertTrue($reportupdated->get('uniquerows'));
        $this->assertEmpty(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report',
            $reportupdated->get('id')));

        $reportupdated = report::update_report((object) [
            'id' => $report->get('id'),
            'name' => 'My renamed report adding tags',
            'uniquerows' => 1,
            'tags' => ['cat', 'dog'],
        ]);

        $this->assertEquals('My renamed report adding tags', $reportupdated->get('name'));
        $this->assertTrue($reportupdated->get('uniquerows'));
        $this->assertEqualsCanonicalizing(
            ['cat', 'dog'],
            array_values(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report', $reportupdated->get('id'))),
        );
    }

    /**
     * Test duplicate report
     */
    public function test_duplicate_report(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create custom report containing single column, condition and filter..
        $report = $generator->create_report(['name' => 'Report 1', 'source' => users::class, 'default' => 0]);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:lastname']);
        $condition = $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);
        $filter = $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Add an audience and schedule.
        $audience = $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);
        $schedule = $generator->create_schedule([
            'reportid' => $report->get('id'),
            'name' => 'My schedule',
            'audiences' => json_encode([
                $audience->get_persistent()->get('id'),
            ]),
        ]);

        // Duplicate report with audiences and schedules.
        $newreport = report::duplicate_report($report, 'Report 1 copy', true, true);
        $this->assertNotEquals($report->get('id'), $newreport->get('id'));
        $this->assertEquals('Report 1 copy', $newreport->get('name'));
        $this->assertEquals(users::class, $newreport->get('source'));

        // Assert new report columns.
        $newcolumns = column::get_records(['reportid' => $newreport->get('id')]);
        $this->assertCount(1, $newcolumns);
        [$newcolumn] = $newcolumns;
        $this->assertNotEquals($column->get('id'), $newcolumn->get('id'));
        $this->assertEquals('user:lastname', $newcolumn->get('uniqueidentifier'));

        // Assert new report conditions.
        $newconditions = filter::get_condition_records($newreport->get('id'));
        $this->assertCount(1, $newconditions);
        [$newcondition] = $newconditions;
        $this->assertNotEquals($condition->get('id'), $newcondition->get('id'));
        $this->assertEquals('user:firstname', $newcondition->get('uniqueidentifier'));

        // Assert new report filters.
        $newfilters = filter::get_filter_records($newreport->get('id'));
        $this->assertCount(1, $newfilters);
        [$newfilter] = $newfilters;
        $this->assertNotEquals($filter->get('id'), $newfilter->get('id'));
        $this->assertEquals('user:email', $newfilter->get('uniqueidentifier'));

        // Assert new report audiences.
        $newaudiences = audience::get_records(['reportid' => $newreport->get('id')]);
        $this->assertCount(1, $newaudiences);
        [$newaudience] = $newaudiences;
        $this->assertNotEquals($audience->get_persistent()->get('id'), $newaudience->get('id'));

         // Assert new report schedules.
        $newschedules = schedule::get_records(['reportid' => $newreport->get('id')]);
        $this->assertCount(1, $newschedules);
        [$newschedule] = $newschedules;
        $this->assertNotEquals($schedule->get('id'), $newschedule->get('id'));
        $this->assertEquals([
            $newaudience->get('id'),
        ], (array) json_decode($newschedule->get('audiences')));

        // Duplicate report without schedules.
        $newreporttwo = report::duplicate_report($report, 'Report 1 copy #2', true, false);
        $this->assertEquals(1, column::count_records(['reportid' => $newreporttwo->get('id')]));
        $this->assertEquals(1, filter::count_records(['reportid' => $newreporttwo->get('id'), 'iscondition' => 1]));
        $this->assertEquals(1, filter::count_records(['reportid' => $newreporttwo->get('id'), 'iscondition' => 0]));
        $this->assertEquals(1, audience::count_records(['reportid' => $newreporttwo->get('id')]));
        $this->assertEquals(0, schedule::count_records(['reportid' => $newreporttwo->get('id')]));

        // Duplicate report without audiences or schedules.
        $newreportthree = report::duplicate_report($report, 'Report 1 copy #3', false, false);
        $this->assertEquals(1, column::count_records(['reportid' => $newreportthree->get('id')]));
        $this->assertEquals(1, filter::count_records(['reportid' => $newreportthree->get('id'), 'iscondition' => 1]));
        $this->assertEquals(1, filter::count_records(['reportid' => $newreportthree->get('id'), 'iscondition' => 0]));
        $this->assertEquals(0, audience::count_records(['reportid' => $newreportthree->get('id')]));
        $this->assertEquals(0, schedule::count_records(['reportid' => $newreportthree->get('id')]));
    }

    /**
     * Test deleting report
     */
    public function test_delete_report(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create Report1 and add some elements.
        $report1 = $generator->create_report(['name' => 'My report 1', 'source' => users::class, 'default' => false,
            'tags' => ['cat', 'dog']]);
        $column1 = $generator->create_column(['reportid' => $report1->get('id'), 'uniqueidentifier' => 'user:email']);
        $filter1 = $generator->create_filter(['reportid' => $report1->get('id'), 'uniqueidentifier' => 'user:email']);
        $condition1 = $generator->create_condition(['reportid' => $report1->get('id'), 'uniqueidentifier' => 'user:email']);

        // Create Report2 and add some elements.
        $report2 = $generator->create_report(['name' => 'My report 2', 'source' => users::class, 'default' => false]);
        $column2 = $generator->create_column(['reportid' => $report2->get('id'), 'uniqueidentifier' => 'user:email']);
        $filter2 = $generator->create_filter(['reportid' => $report2->get('id'), 'uniqueidentifier' => 'user:email']);
        $condition2 = $generator->create_condition(['reportid' => $report2->get('id'), 'uniqueidentifier' => 'user:email']);

        // Delete Report1.
        $result = report::delete_report($report1->get('id'));
        $this->assertTrue($result);

        // Make sure Report1, and all it's elements are deleted.
        $this->assertFalse($report1::record_exists($report1->get('id')));
        $this->assertFalse($column1::record_exists($column1->get('id')));
        $this->assertFalse($filter1::record_exists($filter1->get('id')));
        $this->assertFalse($condition1::record_exists($condition1->get('id')));
        $this->assertEmpty(core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report', $report1->get('id')));

        // Make sure Report2, and all it's elements still exist.
        $this->assertTrue($report2::record_exists($report2->get('id')));
        $this->assertTrue($column2::record_exists($column2->get('id')));
        $this->assertTrue($filter2::record_exists($filter2->get('id')));
        $this->assertTrue($condition2::record_exists($condition2->get('id')));
    }

    /**
     * Testing adding report column
     */
    public function test_add_report_column(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        // Add first column.
        $columnfullname = report::add_report_column($report->get('id'), 'user:fullname');
        $this->assertTrue(column::record_exists($columnfullname->get('id')));

        $this->assertEquals($report->get('id'), $columnfullname->get('reportid'));
        $this->assertEquals('user:fullname', $columnfullname->get('uniqueidentifier'));
        $this->assertEquals(1, $columnfullname->get('columnorder'));
        $this->assertEquals(1, $columnfullname->get('sortorder'));

        // Add second column.
        $columnemail = report::add_report_column($report->get('id'), 'user:email');
        $this->assertTrue(column::record_exists($columnemail->get('id')));

        $this->assertEquals($report->get('id'), $columnemail->get('reportid'));
        $this->assertEquals('user:email', $columnemail->get('uniqueidentifier'));
        $this->assertEquals(2, $columnemail->get('columnorder'));
        $this->assertEquals(2, $columnemail->get('sortorder'));
    }

    /**
     * Test adding invalid report column
     */
    public function test_add_report_column_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid column');
        report::add_report_column($report->get('id'), 'user:invalid');
    }

    /**
     * Testing deleting report column
     */
    public function test_delete_report_column(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        // Add two columns.
        $columnfullname = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Delete the first column.
        $result = report::delete_report_column($report->get('id'), $columnfullname->get('id'));
        $this->assertTrue($result);

        // Assert report columns.
        $columns = column::get_records(['reportid' => $report->get('id')]);
        $this->assertCount(1, $columns);

        $column = reset($columns);
        $this->assertEquals('user:email', $column->get('uniqueidentifier'));
        $this->assertEquals(1, $column->get('columnorder'));
    }

    /**
     * Testing deleting invalid report column
     */
    public function test_delete_report_column_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid column');
        report::delete_report_column($report->get('id'), 42);
    }

    /**
     * Testing re-ordering report column
     */
    public function test_reorder_report_column(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        // Add four columns.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:country']);
        $columncity = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:city']);

        // Move the city column to second position.
        $result = report::reorder_report_column($report->get('id'), $columncity->get('id'), 2);
        $this->assertTrue($result);

        // Assert report columns order.
        $columns = column::get_records(['reportid' => $report->get('id')], 'columnorder');

        $columnidentifiers = array_map(static function(column $column): string {
            return $column->get('uniqueidentifier');
        }, $columns);

        $this->assertEquals([
            'user:fullname',
            'user:city',
            'user:email',
            'user:country',
        ], $columnidentifiers);
    }

    /**
     * Testing re-ordering invalid report column
     */
    public function test_reorder_report_column_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid column');
        report::reorder_report_column($report->get('id'), 42, 1);
    }

    /**
     * Testing re-ordering report column sorting
     */
    public function test_reorder_report_column_sorting(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add four columns.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:country']);
        $columncity = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:city']);

        // Move the city column to second position.
        $result = report::reorder_report_column_sorting($report->get('id'), $columncity->get('id'), 2);
        $this->assertTrue($result);

        // Assert report columns order.
        $columns = column::get_records(['reportid' => $report->get('id')], 'sortorder');

        $columnidentifiers = array_map(static function(column $column): string {
            return $column->get('uniqueidentifier');
        }, $columns);

        $this->assertEquals([
            'user:fullname',
            'user:city',
            'user:email',
            'user:country',
        ], $columnidentifiers);
    }

    /**
     * Testing re-ordering invalid report column sorting
     */
    public function test_reorder_report_column_sorting_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid column');
        report::reorder_report_column_sorting($report->get('id'), 42, 1);
    }

    /**
     * Test toggling of report column sorting
     */
    public function test_toggle_report_column_sorting(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Toggle sort descending.
        $result = report::toggle_report_column_sorting($report->get('id'), $column->get('id'), true, SORT_DESC);
        $this->assertTrue($result);

        // Confirm column was updated.
        $columnupdated = new column($column->get('id'));
        $this->assertTrue($columnupdated->get('sortenabled'));
        $this->assertEquals(SORT_DESC, $columnupdated->get('sortdirection'));
    }

    /**
     * Test toggling of report column sorting with invalid column
     */
    public function test_toggle_report_column_sorting_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid column');
        report::toggle_report_column_sorting($report->get('id'), 42, false);
    }

    /**
     * Test adding report condition
     */
    public function test_add_report_condition(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add first condition.
        $conditionfullname = report::add_report_condition($report->get('id'), 'user:fullname');
        $this->assertTrue(filter::record_exists_select('id = :id AND iscondition = 1',
            ['id' => $conditionfullname->get('id')]));

        $this->assertEquals($report->get('id'), $conditionfullname->get('reportid'));
        $this->assertEquals('user:fullname', $conditionfullname->get('uniqueidentifier'));
        $this->assertEquals(1, $conditionfullname->get('filterorder'));

        // Add second condition.
        $conditionemail = report::add_report_condition($report->get('id'), 'user:email');
        $this->assertTrue(filter::record_exists_select('id = :id AND iscondition = 1',
            ['id' => $conditionemail->get('id')]));

        $this->assertEquals($report->get('id'), $conditionemail->get('reportid'));
        $this->assertEquals('user:email', $conditionemail->get('uniqueidentifier'));
        $this->assertEquals(2, $conditionemail->get('filterorder'));
    }

    /**
     * Test adding invalid report condition
     */
    public function test_add_report_condition_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid condition');
        report::add_report_condition($report->get('id'), 'user:invalid');
    }

    /**
     * Test adding duplicate report condition
     */
    public function test_add_report_condition_duplicate(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // First one is fine.
        report::add_report_condition($report->get('id'), 'user:email');

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Duplicate condition');
        report::add_report_condition($report->get('id'), 'user:email');
    }

    /**
     * Test deleting report condition
     */
    public function test_delete_report_condition(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add two conditions.
        $conditionfullname = $generator->create_condition([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullname',
        ]);
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Delete the first condition.
        $result = report::delete_report_condition($report->get('id'), $conditionfullname->get('id'));
        $this->assertTrue($result);

        // Assert report conditions.
        $conditions = filter::get_condition_records($report->get('id'));
        $this->assertCount(1, $conditions);

        $condition = reset($conditions);
        $this->assertEquals('user:email', $condition->get('uniqueidentifier'));
        $this->assertEquals(1, $condition->get('filterorder'));
    }

    /**
     * Test deleting invalid report condition
     */
    public function test_delete_report_condition_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid condition');
        report::delete_report_condition($report->get('id'), 42);
    }

    /**
     * Test re-ordering report condition
     */
    public function test_reorder_report_condition(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add four conditions.
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:country']);
        $conditioncity = $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:city']);

        // Move the city condition to second position.
        $result = report::reorder_report_condition($report->get('id'), $conditioncity->get('id'), 2);
        $this->assertTrue($result);

        // Assert report conditions order.
        $conditions = filter::get_condition_records($report->get('id'), 'filterorder');

        $conditionidentifiers = array_map(static function(filter $condition): string {
            return $condition->get('uniqueidentifier');
        }, $conditions);

        $this->assertEquals([
            'user:fullname',
            'user:city',
            'user:email',
            'user:country',
        ], $conditionidentifiers);
    }

    /**
     * Test re-ordering invalid report condition
     */
    public function test_reorder_report_condition_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid condition');
        report::reorder_report_condition($report->get('id'), 42, 1);
    }

    /**
     * Test adding report filter
     */
    public function test_add_report_filter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add first filter.
        $filterfullname = report::add_report_filter($report->get('id'), 'user:fullname');
        $this->assertTrue(filter::record_exists_select('id = :id AND iscondition = 0',
            ['id' => $filterfullname->get('id')]));

        $this->assertEquals($report->get('id'), $filterfullname->get('reportid'));
        $this->assertEquals('user:fullname', $filterfullname->get('uniqueidentifier'));
        $this->assertEquals(1, $filterfullname->get('filterorder'));

        // Add second filter.
        $filteremail = report::add_report_filter($report->get('id'), 'user:email');
        $this->assertTrue(filter::record_exists_select('id = :id AND iscondition = 0',
            ['id' => $filteremail->get('id')]));

        $this->assertEquals($report->get('id'), $filteremail->get('reportid'));
        $this->assertEquals('user:email', $filteremail->get('uniqueidentifier'));
        $this->assertEquals(2, $filteremail->get('filterorder'));
    }

    /**
     * Test adding invalid report filter
     */
    public function test_add_report_filter_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid filter');
        report::add_report_filter($report->get('id'), 'user:invalid');
    }

    /**
     * Test adding duplicate report filter
     */
    public function test_add_report_filter_duplicate(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // First one is fine.
        report::add_report_filter($report->get('id'), 'user:email');

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Duplicate filter');
        report::add_report_filter($report->get('id'), 'user:email');
    }

    /**
     * Test deleting report filter
     */
    public function test_delete_report_filter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add two filters.
        $filterfullname = $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);

        // Delete the first filter.
        $result = report::delete_report_filter($report->get('id'), $filterfullname->get('id'));
        $this->assertTrue($result);

        // Assert report filters.
        $filters = filter::get_filter_records($report->get('id'));
        $this->assertCount(1, $filters);

        $filter = reset($filters);
        $this->assertEquals('user:email', $filter->get('uniqueidentifier'));
        $this->assertEquals(1, $filter->get('filterorder'));
    }

    /**
     * Test deleting invalid report filter
     */
    public function test_delete_report_filter_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid filter');
        report::delete_report_filter($report->get('id'), 42);
    }

    /**
     * Test re-ordering report filter
     */
    public function test_reorder_report_filter(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class, 'default' => false]);

        // Add four filters.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:email']);
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:country']);
        $filtercity = $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:city']);

        // Move the city filter to second position.
        $result = report::reorder_report_filter($report->get('id'), $filtercity->get('id'), 2);
        $this->assertTrue($result);

        // Assert report filters order.
        $filters = filter::get_filter_records($report->get('id'), 'filterorder');

        $filteridentifiers = array_map(static function(filter $filter): string {
            return $filter->get('uniqueidentifier');
        }, $filters);

        $this->assertEquals([
            'user:fullname',
            'user:city',
            'user:email',
            'user:country',
        ], $filteridentifiers);
    }

    /**
     * Test re-ordering invalid report filter
     */
    public function test_reorder_report_filter_invalid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage('Invalid filter');
        report::reorder_report_filter($report->get('id'), 42, 1);
    }

    /**
     * Test getting row count for a custom report
     */
    public function test_get_report_row_count_custom_report(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_user();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        // There are two users, the admin plus the user we just created.
        $this->assertEquals(2, report::get_report_row_count($report->get('id')));
    }

    /**
     * Test getting row count for a system report
     */
    public function test_get_report_row_count_system_report(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->getDataGenerator()->create_user();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $generator->create_audience(['reportid' => $report->get('id'), 'configdata' => []]);

        $reportaccesslist = system_report_factory::create(
            report_access_list::class,
            system::instance(),
            parameters: ['id' => $report->get('id')],
        )->get_report_persistent();

        // There are two users, the admin plus the user we just created.
        $this->assertEquals(
            2,
            report::get_report_row_count($reportaccesslist->get('id'), ['id' => $report->get('id')],
        ));
    }
}
