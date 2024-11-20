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
 * Unit tests for {@printable tool_brickfield\local\tool\printable\tool}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay.churchward@poetopensource.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class printable_test extends \advanced_testcase {

    /**
     * Test tool name.
     */
    public function test_toolname(): void {
        $this->resetAfterTest();

        $object = new printable();
        $output = $object->toolname();
        $this->assertEquals($output, 'Summary report');
    }

    /**
     * Test tool short name.
     */
    public function test_toolshortname(): void {
        $this->resetAfterTest();

        $object = new printable();
        $output = $object->toolshortname();
        $this->assertEquals($output, 'Summary report');
    }

    /**
     * Test plugin name.
     */
    public function test_pluginname(): void {
        $this->resetAfterTest();

        $object = new printable();
        $output = $object->pluginname();
        $this->assertEquals($output, 'printable');
    }

    /**
     * Can access.
     */
    public function can_access() {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();

        $filter = new filter(1, $category->id, 'tab', 3, 4);
        $filter->courseids = [];

        $object = new printable();
        $output = $object->can_access($filter);
        $this->assertFalse($output);
    }

    /**
     * Test get output.
     */
    public function test_get_output(): void {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();

        $filter = new filter(1, $category->id, 'printable', 3, 4);
        $filter->courseids = [];

        $filter->target = 'html';
        $object = new printable();
        $object->set_filter($filter);
        $output = $object->get_output();
        $this->assertStringContainsString('<h3>Course PHPUnit test site</h3><div id=', $output);

        $filter->target = '';
        $object = new printable();
        $object->set_filter($filter);
        $output = $object->get_output();
        $this->assertStringContainsString('<h3>Course PHPUnit test site</h3><a href=', $output);

        $filter->target = 'html';
        $object = new printable();
        $object->set_filter($filter);
        $output = $object->get_output();
        $this->assertStringContainsString('<i class="icon fa fa-trophy fa-fw "  title="Total activities" '.
            'role="img" aria-label="Total activities">', $output);
    }
}
