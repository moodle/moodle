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
 * The dataformfield_select user values helper for calculated grading support.
 *
 * @package dataformfield_select
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformfield_select\helper;

defined('MOODLE_INTERNAL') || die();

class contentperuser {
    /**
     * Returns the value replacement of the pattern for each user with content in the field.
     *
     * @param string $pattern
     * @param array $entryids The ids of entries the field values should be fetched from.
     *      If not provided the method should return values from all applicable entries.
     * @return null|array Array of userid => value pairs.
     */
    public static function get_content($field, $pattern, array $userentryids = null) {
        global $DB;

        $values = array();

        // Fix pattern enclosures if needed.
        $pattern = '[['. trim($pattern, '[]'). ']]';

        $fieldname = $field->name;

        if (!$userentryids and !$userentryids = $field->df->get_entry_ids_per_user()) {
            return $values;
        }

        foreach ($userentryids as $userid => $entryids) {
            $sqlselects = $params = array();

            $sqlselects[] = ' fieldid = ? ';
            $params[] = $field->id;

            list($inids, $eparams) = $DB->get_in_or_equal($entryids);
            $sqlselects[] = " entryid $inids ";
            $params = array_merge($params, $eparams);

            $contents = $DB->get_records_select('dataform_contents', implode(' AND ', $sqlselects), $params);
            if ($contents) {
                $values[$userid] = array();
                foreach ($contents as $content) {
                    if ($pattern == "[[$fieldname]]") {
                        $optionsmenu = $field->options_menu();
                        $optionvalue = $optionsmenu[$content->content];
                        $value = is_numeric($optionvalue) ? $optionvalue : 0;
                    } else if ($pattern == "[[$fieldname:key]]") {
                        $value = $content->content;
                    }
                    $values[$userid][] = $value;
                }
            }
        }

        return $values;
    }

}
