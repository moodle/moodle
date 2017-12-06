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
 * The dataformfield_ratingmdl user values helper for calculated grading support.
 *
 * @package dataformfield_ratingmdl
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformfield_ratingmdl\helper;

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
        $values = array();

        // Fix pattern enclosures if needed.
        $pattern = '[['. trim($pattern, '[]'). ']]';

        if (!$aggrs = $field->renderer->get_aggregations(array($pattern))) {
            return null;
        }

        $fieldname = $field->name;
        $aggregation = reset($aggrs);

        $options = new \stdClass;
        $options->component = 'mod_dataform';
        $options->ratingarea = $fieldname;
        $options->aggregationmethod = $aggregation;
        $options->itemtable = 'dataform_entries';
        $options->itemtableusercolumn = 'userid';
        $options->modulename = 'dataform';
        $options->moduleid   = $field->df->id;
        $options->scaleid = $field->get_scaleid();

        $rm = $field->rating_manager;

        if (!$userentryids and !$userentryids = $field->df->get_entry_ids_per_user()) {
            return $values;
        }

        $users = array_keys($userentryids);

        foreach ($users as $userid) {
            $options->userid = $userid;
            if ($usergrades = $rm->get_user_grades($options)) {
                $uservalues = array_map(
                    function($g) {
                        return $g->rawgrade;
                    },
                    $usergrades
                );
                $values[$userid] = $uservalues;
            }
        }

        return $values;
    }

}
