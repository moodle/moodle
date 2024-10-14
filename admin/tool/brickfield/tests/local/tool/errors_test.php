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

namespace tool_brickfield\local\tool;

/**
 * Unit tests for {@errors tool_brickfield\local\tool\errors\tool}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay.churchward@poetopensource.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class errors_test extends \advanced_testcase {

    public function test_toolname(): void {
        $this->resetAfterTest();

        $object = new errors();
        $output = $object->toolname();
        $this->assertEquals($output, 'Error list summary');
    }

    public function test_toolshortname(): void {
        $this->resetAfterTest();

        $object = new errors();
        $output = $object->toolshortname();
        $this->assertEquals($output, 'Error list');
    }

    public function test_pluginname(): void {
        $this->resetAfterTest();

        $object = new errors();
        $output = $object->pluginname();
        $this->assertEquals($output, 'errors');
    }

    public function test_get_output(): void {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();

        $filter = new filter(1, $category->id, 'errors', 3, 4);
        $filter->courseids = [];

        $object = new errors();
        $object->set_filter($filter);
        $output = $object->get_output();
        $this->assertIsString($output);
        $this->assertStringContainsString('Error details:', $output);
    }
}
