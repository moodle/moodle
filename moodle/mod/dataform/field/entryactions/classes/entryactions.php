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
 * @subpackage entryactions
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_entryactions_entryactions extends \mod_dataform\pluginbase\dataformfield_internal {

    const INTERNALID = -1;

    /**
     *
     */
    public function get_target_view($action) {
        return null;
    }

    /**
     * Returns instance defaults for for the field
     * (because internal fields do not have DB record).
     *
     * @return stdClass
     */
    public static function get_default_data($dfid) {
        $field = (object) array(
            'id' => self::INTERNALID,
            'dataid' => $dfid,
            'type' => 'entryactions',
            'name' => get_string('fieldname', 'dataformfield_entryactions'),
            'description' => '',
            'visible' => 2,
            'editable' => -1,
        );
        return $field;
    }

    /**
     * Overriding parent to return no sort/search options.
     *
     * @return array
     */
    public function get_sort_options_menu() {
        return array();
    }
}
