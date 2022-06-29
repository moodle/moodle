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

namespace tool_brickfield\local\htmlchecker\common;

/**
 * Special base class which provides helper methods for tables.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_table_test extends brickfield_accessibility_test {
    /**
     * Takes the element object of a main table and returns the number of rows and columns in it.
     * @param \stdClass $table
     * @return array An array with the 'rows' value showing the number of rows, and column showing the number of columns
     */
    public function get_table(\stdClass $table): array {
        $rows = 0;
        $columns = 0;
        $firstrow = true;
        if ($table->tagName != 'table') {
            return false;
        }
        foreach ($table->childNodes as $child) {
            if (property_exists($child, 'tagName') && $child->tagName == 'tr') {
                $rows++;
                if ($firstrow) {
                    foreach ($child->childNodes as $columnchild) {
                        if ($columnchild->tagName == 'th' || $columnchild->tagName == 'td') {
                            $columns++;
                        }
                    }
                    $firstrow = false;
                }
            }
        }

        return ['rows' => $rows, 'columns' => $columns];
    }

    /**
     * Finds whether or not the table is a data table. Checks that the
     * table has a logical order and uses 'th' or 'thead' tags to illustrate
     * the page author thought it was a data table.
     * @param object $table The DOMElement object of the table tag
     * @return bool TRUE if the element is a data table, otherwise false
     */
    public function is_data($table): bool {
        if ($table->tagName != 'table') {
            return false;
        }

        foreach ($table->childNodes as $child) {
            if (property_exists($child, 'tagName') && $child->tagName == 'tr') {
                foreach ($child->childNodes as $rowchild) {
                    if (property_exists($rowchild, 'tagName') && $rowchild->tagName == 'th') {
                        return true;
                    }
                }
            }
            if (property_exists($child, 'tagName') && $child->tagName == 'thead') {
                return true;
            }
        }
        return false;
    }
}
