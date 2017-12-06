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
 * @subpackage time
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_time_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;

        $edit = !empty($options['edit']);
        $haseditreplacement = false;

        $replacements = array_fill_keys(array_keys($patterns), '');

        foreach ($patterns as $pattern => $cleanpattern) {
            // Edit mode.
            if ($edit) {
                if (!$haseditreplacement and !$this->is_noedit($pattern)) {
                    $required = $this->is_required($pattern);
                    // Determine whether date only selector.
                    $date = (($cleanpattern == "[[$fieldname:date]]") or $field->date_only);
                    $options = array('required' => $required, 'date' => $date);
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry, $options));
                    $haseditreplacement = true;
                    continue;
                }
            }

            // Browse mod.
            // Determine display format.
            $format = (strpos($pattern, "$fieldname:") !== false ? str_replace("$fieldname:", '', trim($pattern, '[]')) : $field->display_format);
            // For specialized patterns convert format to the userdate format string.
            switch ($format) {
                case 'date':
                    $format = get_string('strftimedate', 'langconfig');
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
                default:
                    if (!$format and $field->date_only) {
                        $format = get_string('strftimedate', 'langconfig');
                    }
            }
            $replacements[$pattern] = $this->display_browse($entry, array('format' => $format));
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $content = 0;
        if ($entryid > 0 and !empty($entry->{"c{$fieldid}_content"})) {
            $content = $entry->{"c{$fieldid}_content"};
        }

        if ($field->masked) {
            $this->render_masked_selector($mform, $entry, $content, $options);
        } else {
            $this->render_standard_selector($mform, $entry, $content, $options);
        }
    }

    /**
     *
     */
    public function display_browse($entry, $params = null) {
        $field = $this->_field;
        $fieldid = $field->id;

        $strtime = '';
        if (isset($entry->{"c{$fieldid}_content"})) {
            if ($content = $entry->{"c{$fieldid}_content"}) {
                $format = !empty($params['format']) ? $params['format'] : '';
                $strtime = userdate($content, $format);
            }
        }

        return $strtime;
    }

    /**
     *
     */
    protected function render_standard_selector(&$mform, $entry, $content, array $options = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $includetime = empty($options['date']) ? true : false;

        // If date only don't add time to selector.
        $time = $includetime ? 'time_' : '';
        $tmoptions = array();
        // Optional.
        $tmoptions['optional'] = (!empty($options['required']) ? false : true);
        // Start year.
        if ($field->start_year) {
            $tmoptions['startyear'] = $field->start_year;
        }
        // End year.
        if ($field->stop_year) {
            $tmoptions['stopyear'] = $field->stop_year;
        }
        $mform->addElement("date_{$time}selector", $fieldname, null, $tmoptions);
        // $mform->addRule($fieldname, null, 'required', null, 'client');.
        $mform->setDefault($fieldname, $content);
    }

    /**
     *
     */
    protected function render_masked_selector(&$mform, $entry, $content, array $options = null) {
        $field = $this->_field;
        $entryid = $entry->id;
        $fieldid = $field->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $includetime = empty($options['date']) ? true : false;

        $step = 5;
        $startyear = $field->start_year ? $field->start_year : 1970;
        $stopyear = $field->stop_year ? $field->stop_year : 2020;
        $maskday = get_string('day', 'dataformfield_time');
        $maskmonth = get_string('month', 'dataformfield_time');
        $maskyear = get_string('year', 'dataformfield_time');

        $days = array();
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        $months = array();
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = userdate(mktime(0, 0, 0, $i, 10), "%B");
        }
        $years = array();
        for ($i = $startyear; $i <= $stopyear; $i++) {
            $years[$i] = $i;
        }

        $grp = array();
        $grp[] = &$mform->createElement('select', "{$fieldname}[day]", null, array(0 => $maskday) + $days);
        $grp[] = &$mform->createElement('select', "{$fieldname}[month]", null, array(0 => $maskmonth) + $months);
        $grp[] = &$mform->createElement('select', "{$fieldname}[year]", null, array(0 => $maskyear) + $years);

        // If time add hours and minutes.
        if ($includetime) {
            $maskhour = get_string('hour', 'dataformfield_time');
            $maskminute = get_string('minute', 'dataformfield_time');

            $hours = array();
            for ($i = 0; $i <= 23; $i++) {
                $hours[$i] = sprintf("%02d", $i);
            }
            $minutes = array();
            for ($i = 0; $i < 60; $i += $step) {
                $minutes[$i] = sprintf("%02d", $i);
            }

            $grp[] = &$mform->createElement('select', "{$fieldname}[hour]", null, array(0 => $maskhour) + $hours);
            $grp[] = &$mform->createElement('select', "{$fieldname}[minute]", null, array(0 => $maskminute) + $minutes);
        }

        $mform->addGroup($grp, "grp$fieldname", null, '', false);
        // Set field values.
        if ($content) {
            list($day, $month, $year, $hour, $minute) = explode(':', date('d:n:Y:G:i', $content));
            $mform->setDefault("{$fieldname}[day]", (int) $day);
            $mform->setDefault("{$fieldname}[month]", (int) $month);
            $mform->setDefault("{$fieldname}[year]", (int) $year);
            // Defaults for time.
            if ($includetime) {
                $mform->setDefault("{$fieldname}[hour]", (int) $hour);
                $mform->setDefault("{$fieldname}[minute]", (int) $minute);
            }
        }
        // Add enabled fake field.
        $mform->addElement('hidden', "{$fieldname}[enabled]", 1);
        $mform->setType("{$fieldname}[enabled]", PARAM_INT);
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:date]]"] = array(true, $fieldname);
        // Minute (M).
        $patterns["[[$fieldname:minute]]"] = array(false);
        // Hour (H).
        $patterns["[[$fieldname:hour]]"] = array(false);
        // Day (a).
        $patterns["[[$fieldname:day]]"] = array(false);
        $patterns["[[$fieldname:d]]"] = array(false);
        // Week (W).
        $patterns["[[$fieldname:week]]"] = array(false);
        // Month (b).
        $patterns["[[$fieldname:month]]"] = array(false);
        $patterns["[[$fieldname:m]]"] = array(false);
        // Year (G).
        $patterns["[[$fieldname:year]]"] = array(false);
        $patterns["[[$fieldname:Y]]"] = array(false);

        return $patterns;
    }
}
