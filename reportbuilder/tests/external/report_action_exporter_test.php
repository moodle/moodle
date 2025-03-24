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
use core_reportbuilder\output\report_action;

/**
 * Unit tests for report action exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\report_action_exporter
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report_action_exporter_test extends advanced_testcase {

    /**
     * Test exported data/structure
     */
    public function test_export(): void {
        global $PAGE;

        $reportaction = new report_action('Add', ['class' => 'btn', 'data-action' => 'action']);

        $exporter = new report_action_exporter(null, ['reportaction' => $reportaction]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertEquals((object) [
            'tag' => 'button',
            'title' => 'Add',
            'attributes' => [
                ['name' => 'class', 'value' => 'btn'],
                ['name' => 'data-action', 'value' => 'action'],
            ],
        ], $export);
    }
}
