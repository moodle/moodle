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

/**
 * Grade object tests.
 *
 * @package    core_grades
 * @category   phpunit
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/lib.php');

/**
 * Grade object testcase.
 *
 * @package    core_grades
 * @category   phpunit
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grade_object_testcase extends grade_base_testcase {

    public function test_fetch_all_helper() {
        // Simple ID lookup.
        $params = array('id' => $this->grade_items[0]->id);
        $items = grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
        $this->assertCount(1, $items);
        $item = array_shift($items);
        $this->assertInstanceOf('grade_item', $item);
        $this->assertEquals($item->id, $this->grade_items[0]->id);

        // Various parameters lookup, multiple results.
        $params = array('courseid' => $this->course->id, 'categoryid' => $this->grade_categories[1]->id);
        $items = grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
        $this->assertCount(2, $items);
        $expecteditems = array($this->grade_items[0]->id => true, $this->grade_items[1]->id => true);
        foreach ($items as $item) {
            $this->assertInstanceOf('grade_item', $item);
            $this->assertArrayHasKey($item->id, $expecteditems);
            unset($expecteditems[$item->id]);
        }

        // Text column lookup.
        $params = array('iteminfo' => $this->grade_items[2]->iteminfo);
        $items = grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
        $this->assertCount(1, $items);
        $item = array_shift($items);
        $this->assertInstanceOf('grade_item', $item);
        $this->assertEquals($item->id, $this->grade_items[2]->id);

        // Lookup using non-existing columns.
        $params = array('doesnotexist' => 'ignoreme', 'id' => $this->grade_items[0]->id);
        $items = grade_object::fetch_all_helper('grade_items', 'grade_item', $params);
        $this->assertCount(1, $items);
        $item = array_shift($items);
        $this->assertInstanceOf('grade_item', $item);
        $this->assertEquals($item->id, $this->grade_items[0]->id);
    }

}
