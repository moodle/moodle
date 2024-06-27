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

namespace core_table\output;

/**
 * Component representing a table row.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core_table
 * @category output
 */
class html_table_row {
    /**
     * @var string Value to use for the id attribute of the row.
     */
    public $id = null;

    /**
     * @var array Array of html_table_cell objects
     */
    public $cells = [];

    /**
     * @var string Value to use for the style attribute of the table row
     */
    public $style = null;

    /**
     * @var array Attributes of additional HTML attributes for the <tr> element
     */
    public $attributes = [];

    /**
     * Constructor
     *
     * @param null|array $cells
     */
    public function __construct(?array $cells = null) {
        $this->attributes['class'] = '';
        $cells = (array)$cells;
        foreach ($cells as $cell) {
            if ($cell instanceof html_table_cell) {
                $this->cells[] = $cell;
            } else {
                $this->cells[] = new html_table_cell($cell);
            }
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(html_table_row::class, \html_table_row::class);
