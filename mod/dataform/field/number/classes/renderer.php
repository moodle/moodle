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
 * @package dataformfield
 * @subpackage number
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_number_renderer extends dataformfield_text_renderer {

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        parent::display_edit($mform, $entry, $options);

        $fieldid = $this->_field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_$entryid";
        $mform->addRule($fieldname, null, 'numeric', null, 'client');
    }

    /**
     *
     */
    public function display_browse($entry, $params = null) {
        $field = $this->_field;
        $fieldid = $field->id;

        $str = null;
        if (isset($entry->{"c{$fieldid}_content"})) {
            $number = (float) $entry->{"c{$fieldid}_content"};

            $decimals = (int) trim($field->param1);
            // Only apply number formatting if param1 contains an integer number >= 0:.
            if ($decimals) {
                // Removes leading zeros (eg. '007' -> '7'; '00' -> '0').
                $str = sprintf("%4.{$decimals}f", $number);
            } else {
                $str = (int) $number;
            }
        }

        return (string) $str;
    }
}
