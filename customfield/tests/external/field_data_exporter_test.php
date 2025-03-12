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

namespace core_customfield\external;

use advanced_testcase;
use core_customfield_generator;

/**
 * Unit tests for custom field data exporter
 *
 * @package     core_customfield
 * @covers      \core_customfield\external\field_data_exporter
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class field_data_exporter_test extends advanced_testcase {

    /**
     * Test exported data/structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $category = $generator->create_category();
        $generator->create_field([
            'categoryid' => $category->get('id'),
            'name' => 'My field',
            'shortname' => 'myfield',
            'type' => 'number',
        ]);

        $courseone = $this->getDataGenerator()->create_course(['customfield_myfield' => 42]);
        $coursetwo = $this->getDataGenerator()->create_course();

        $output = $PAGE->get_renderer('core_customfield');

        // Course one has our custom field populated.
        $courseoneexport = (new field_data_exporter(null, [
            'component' => 'core_course',
            'area' => 'course',
            'instanceid' => (int) $courseone->id,
        ]))->export($output);

        $this->assertEquals([
            [
                'value' => 42,
                'type' => 'number',
                'shortname' => 'myfield',
                'name' => 'My field',
                'hasvalue' => true,
                'instanceid' => $courseone->id,
            ],
        ], array_values($courseoneexport->data));

        // Course two does not have our custom field populated.
        $coursetwoexport = (new field_data_exporter(null, [
            'component' => 'core_course',
            'area' => 'course',
            'instanceid' => (int) $coursetwo->id,
        ]))->export($output);

        $this->assertEquals([
            [
                'value' => null,
                'type' => 'number',
                'shortname' => 'myfield',
                'name' => 'My field',
                'hasvalue' => false,
                'instanceid' => $coursetwo->id,
            ],
        ], array_values($coursetwoexport->data));
    }
}
