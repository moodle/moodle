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

namespace core\navigation\output;

use basic_testcase;
use core\output\renderer_base;
use stdClass;

/**
 * More menu navigation renderable test.
 *
 * @package     core
 * @category    navigation
 * @copyright   Stefan Topfstedt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \core\navigation\output\more_menu
 */
final class more_menu_test extends basic_testcase {
    /**
     * Checks that export_for_template() returns an empty array if the given content is empty.
     * See MDL-86416.
     *
     * @return void
     */
    public function test_export_for_template_returns_empty_array(): void {
        $moremenu = new more_menu(new stdClass(), 'whatever', false, false);
        $output = $this->createStub(renderer_base::class);
        $data = $moremenu->export_for_template($output);
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }
}
