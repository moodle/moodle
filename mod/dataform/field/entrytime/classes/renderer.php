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
 * @subpackage entrytime
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_entrytime_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;

        // No edit mode.
        $replacements = array();

        foreach ($patterns as $pattern) {
            // Display nothing on new entries.
            if ($entry->id < 0) {
                $replacements[$pattern] = '';

            } else {
                list(, $timevar, $format) = explode(':', trim($pattern, '[]')) + array(null, null, null);

                switch ($format) {
                    case 'date':
                        $format = get_string('strftimedate');
                        break;
                    case 'minute':
                        $format = '%M';
                        break;
                    case 'hour':
                        $format = '%H';
                        break;
                    case 'day':
                        $format = '%a';
                        break;
                    case 'week':
                        $format = '%W';
                        break;
                    case 'month':
                        $format = '%b';
                        break;
                    case 'm':
                        $format = '%m';
                        break;
                    case 'year':
                    case 'Y':
                        $format = '%Y';
                        break;
                }
                $replacements[$pattern] = userdate($entry->$timevar, $format);
            }
        }

        return $replacements;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;
        $cat = get_string('pluginname', 'dataformfield_entrytime');

        $patterns = array();
        foreach (array('timecreated', 'timemodified') as $timevar) {
            $patterns["[[$fieldname:$timevar]]"] = array(true, $cat);
            $patterns["[[$fieldname:$timevar:date]]"] = array(true, $cat);
            // Minute (M).
            $patterns["[[$fieldname:$timevar:minute]]"] = array(false);
            // Hour (H).
            $patterns["[[$fieldname:$timevar:hour]]"] = array(false);
            // Day (a).
            $patterns["[[$fieldname:$timevar:day]]"] = array(false);
            $patterns["[[$fieldname:$timevar:d]]"] = array(false);
            // Week (W).
            $patterns["[[$fieldname:$timevar:week]]"] = array(false);
            // Month (b).
            $patterns["[[$fieldname:$timevar:month]]"] = array(false);
            $patterns["[[$fieldname:$timevar:m]]"] = array(false);
            // Year (G).
            $patterns["[[$fieldname:$timevar:year]]"] = array(false);
            $patterns["[[$fieldname:$timevar:Y]]"] = array(false);
        }

        return $patterns;
    }
}
