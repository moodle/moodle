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
 * Component representing a table cell.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core_table
 * @category output
 */
class html_table_cell {
    /**
     * @var string Value to use for the id attribute of the cell.
     */
    public $id = null;

    /**
     * @var string The contents of the cell.
     */
    public $text;

    /**
     * @var string Abbreviated version of the contents of the cell.
     */
    public $abbr = null;

    /**
     * @var int Number of columns this cell should span.
     */
    public $colspan = null;

    /**
     * @var int Number of rows this cell should span.
     */
    public $rowspan = null;

    /**
     * @var string Defines a way to associate header cells and data cells in a table.
     */
    public $scope = null;

    /**
     * @var bool Whether or not this cell is a header cell.
     */
    public $header = null;

    /**
     * @var string Value to use for the style attribute of the table cell
     */
    public $style = null;

    /**
     * @var array Attributes of additional HTML attributes for the <td> element
     */
    public $attributes = [];

    /**
     * Constructs a table cell
     *
     * @param string $text
     */
    public function __construct($text = null) {
        $this->text = $text;
        $this->attributes['class'] = '';
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(html_table_cell::class, \html_table_cell::class);
