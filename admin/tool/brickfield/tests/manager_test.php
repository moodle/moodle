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

namespace tool_brickfield;

/**
 * Class tool_brickfield_manager_test
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_test extends \advanced_testcase {

    /**
     * Tests for the function manager::get_all_areas()
     */
    public function test_get_areas() {
        $this->resetAfterTest();
        $areas = manager::get_all_areas();
        $areaclassnames = array_map('get_class', $areas);

        // Make sure the list of areas contains some known areas.
        $this->assertContains(\tool_brickfield\local\areas\mod_assign\intro::class, $areaclassnames);
        $this->assertContains(\tool_brickfield\local\areas\core_question\questiontext::class, $areaclassnames);
        $this->assertContains(\tool_brickfield\local\areas\mod_choice\option::class, $areaclassnames);
    }
}
