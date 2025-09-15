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

namespace core_courseformat\external;

use core_courseformat\output\local\overview\overviewdialog;

/**
 * Tests for overviewdialog_exporter.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overviewdialog_exporter::class)]
final class overviewdialog_exporter_test extends \advanced_testcase {
    /**
     * Test export method.
     */
    public function test_exporter(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $dialog = new overviewdialog('button content', 'dialog title', 'dialog description');
        $dialog->add_item('Item 1', 'Value 1');
        $dialog->add_item('Item 2', 'Value 2');

        $exporter = new overviewdialog_exporter($dialog, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('buttoncontent', $data);
        $this->assertObjectHasProperty('title', $data);
        $this->assertObjectHasProperty('description', $data);
        $this->assertObjectHasProperty('disabled', $data);
        $this->assertObjectHasProperty('items', $data);
        $this->assertCount(5, get_object_vars($data));

        $this->assertEquals('button content', $data->buttoncontent);
        $this->assertEquals('dialog title', $data->title);
        $this->assertEquals('dialog description', $data->description);
        $this->assertFalse($data->disabled);

        $this->assertCount(2, $data->items);
        $this->assertEquals('Item 1', $data->items[0]->label);
        $this->assertEquals('Value 1', $data->items[0]->value);
        $this->assertEquals('Item 2', $data->items[1]->label);
        $this->assertEquals('Value 2', $data->items[1]->value);
    }
}
