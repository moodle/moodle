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

namespace core\external;

/**
 * Tests for pix_icon_exporter.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\external\pix_icon_exporter
 */
final class pix_icon_exporter_test extends \advanced_testcase {
    /**
     * Test export method.
     */
    public function test_export(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $pix = 'i/warning';
        $alt = get_string('warning');
        $component = 'moodle';
        $attributes = ['class' => 'me-0 pb-1'];

        $icon = new \core\output\pix_icon($pix, $alt, $component, $attributes);
        $exporter = new pix_icon_exporter($icon, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('pix', $data);
        $this->assertObjectHasProperty('component', $data);
        $this->assertObjectHasProperty('extras', $data);
        $this->assertCount(3, get_object_vars($data));

        $this->assertEquals($pix, $data->pix);
        $this->assertEquals($component, $data->component);

        $expectedattributes = [
            [
                'name' => 'class',
                'value' => 'me-0 pb-1',
            ],
            [
                'name' => 'alt',
                'value' => $alt,
            ],
            [
                'name' => 'title',
                'value' => $alt,
            ],
        ];
        $this->assertEquals($expectedattributes, $data->extras);
    }

    /**
     * Test export method without extras.
     */
    public function test_export_no_extras(): void {
        $renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

        $pix = 'i/warning';
        $component = 'moodle';

        $icon = new \core\output\pix_icon($pix, '', $component);
        $exporter = new pix_icon_exporter($icon, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('pix', $data);
        $this->assertObjectHasProperty('component', $data);
        $this->assertObjectHasProperty('extras', $data);
        $this->assertCount(3, get_object_vars($data));

        $this->assertEquals($pix, $data->pix);
        $this->assertEquals($component, $data->component);

        $expectedattributes = [
            [
                'name' => 'class',
                'value' => '',
            ],
            [
                'name' => 'alt',
                'value' => '',
            ],
            [
                'name' => 'aria-hidden',
                'value' => 'true',
            ],
        ];
        $this->assertEquals($expectedattributes, $data->extras);
    }
}
