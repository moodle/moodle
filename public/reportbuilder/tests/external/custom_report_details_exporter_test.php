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

namespace core_reportbuilder\external;

use advanced_testcase;
use core_customfield_generator;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests for custom report details exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_details_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_report_details_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $category = $generator->create_category(['component' => 'core_reportbuilder', 'area' => 'report']);
        $generator->create_field([
            'categoryid' => $category->get('id'),
            'name' => 'My field',
            'shortname' => 'myfield',
            'type' => 'number',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'tags' => ['cat', 'dog'],
            'customfield_myfield' => 42,
        ]);

        $exporter = new custom_report_details_exporter($report);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        // The exporter outputs the persistent details, plus two other properties.
        $this->assertEquals($report->get('name'), $export->name);
        $this->assertEquals($report->get('source'), $export->source);

        // Source name should be the name of the source.
        $this->assertObjectHasProperty('sourcename', $export);
        $this->assertEquals(users::get_name(), $export->sourcename);

        // We use the tag exporter for report tags.
        $this->assertObjectHasProperty('tags', $export);
        $this->assertEquals(['cat', 'dog'], array_column($export->tags, 'name'));

        // We use the custom field exporter for report custom fields.
        $this->assertObjectHasProperty('customfields', $export);
        $this->assertEquals(['42'], array_column($export->customfields->data, 'value'));

        // We use the user exporter for the modifier of the report.
        $this->assertObjectHasProperty('modifiedby', $export);
        $this->assertEquals('Admin User', $export->modifiedby->fullname);
    }
}
