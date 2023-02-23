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
 * Class area_test_base provides some utility functions that can be used by testing.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class area_test_base extends \advanced_testcase {
    /** @var string Message for failed area test. */
    protected $areatestmessage = 'Expected %component% area not found';

    /**
     * Create and return an array from a recordset. Recordset is destroyed.
     * @param \moodle_recordset $rs
     * @return array
     */
    public function array_from_recordset(\moodle_recordset $rs): array {
        $records = [];
        foreach ($rs as $record) {
            $records[] = $record;
        }
        // Can't rewind a recordset, so might as well close it.
        $rs->close();
        return $records;
    }

    /**
     * Test for specified component information present in area recordset. Recordset cannot be reused.
     * @param \moodle_recordset $areasrs
     * @param string $component
     * @param int $contextid
     * @param int $itemid
     * @param int|null $courseid
     * @param int|null $categoryid
     * @return void
     */
    public function assert_area_in_recordset(\moodle_recordset $areasrs, string $component, int $contextid, int $itemid,
                                             ?int $courseid, ?int $categoryid): void {
        $this->assert_area_in_array(
            $this->array_from_recordset($areasrs),
            $component,
            $contextid,
            $itemid,
            $courseid,
            $categoryid
        );
    }

    /**
     * Test for specified component information present in area array.
     * @param array $areas
     * @param string $component
     * @param int $contextid
     * @param int $itemid
     * @param int|null $courseid
     * @param int|null $categoryid
     * @return void
     */
    public function assert_area_in_array(array $areas, string $component, int $contextid, int $itemid,
                                         ?int $courseid, ?int $categoryid): void {
        $found = false;
        $message = str_replace('%component%', $component, $this->areatestmessage);
        foreach ($areas as $area) {
            if (($area->component == $component) &&
                ($area->contextid == $contextid) &&
                ((empty($courseid) ? empty($area->courseid) : ($area->courseid == $courseid))) &&
                ((empty($categoryid) ? empty($area->categoryid) : ($area->categoryid == $categoryid))) &&
                ($area->itemid == $itemid)
            ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, $message);
    }
}
