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

namespace core_search;

/**
 * Document icon unit tests.
 *
 * @package    core_search
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document_icon_test extends \advanced_testcase {
    /**
     * Test that default component gets returned correctly.
     */
    public function test_default_component(): void {
        $docicon = new \core_search\document_icon('test_name');
        $this->assertEquals('test_name', $docicon->get_name());
        $this->assertEquals('moodle', $docicon->get_component());
    }

    /**
     * Test that name and component get returned correctly.
     */
    public function test_can_get_name_and_component(): void {
        $docicon = new \core_search\document_icon('test_name', 'test_component');
        $this->assertEquals('test_name', $docicon->get_name());
        $this->assertEquals('test_component', $docicon->get_component());
    }

}
