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

namespace core\output;

/**
 * Tests for the pix_icon output class.
 *
 * @package    core
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\output\pix_icon
 */
final class pix_icon_test extends \advanced_testcase {
    /**
     * Test the exportable interface implementation.
     */
    public function test_get_exporter(): void {
        $source = new pix_icon('i/warning', '');
        $expectedclass = \core\external\pix_icon_exporter::class;

        $exporter = $source->get_exporter();
        $this->assertInstanceOf($expectedclass, $exporter);

        $structure = pix_icon::get_read_structure();
        $this->assertInstanceOf(\core_external\external_single_structure::class, $structure);
        $this->assertEquals(
            $expectedclass::get_read_structure(),
            $structure,
        );

        $structure = pix_icon::read_properties_definition();
        $this->assertEquals(
            $expectedclass::read_properties_definition(),
            $structure,
        );
    }
}
