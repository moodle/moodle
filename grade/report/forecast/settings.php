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
 * Defines site settings for the user grade forecast gradebook report
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$_s = function($key) { return get_string($key, 'gradereport_forecast'); };

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox('grade_report_forecast_showlettergrade', get_string('showlettergrade', 'grades'), get_string('showlettergrade', 'grades'), 1));
    $settings->add(new admin_setting_configcheckbox('grade_report_forecast_showgradepercentage', get_string('showpercentage', 'grades'), get_string('showpercentage_help', 'grades'), 1));
    $settings->add(new admin_setting_configselect('grade_report_forecast_rangedecimals', get_string('rangedecimals', 'grades'),
            get_string('rangedecimals_help', 'grades'), 0,array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5)));

    $options = array(0 => get_string('shownohidden', 'grades'),
                     1 => get_string('showhiddenuntilonly', 'grades'),
                     2 => get_string('showallhidden', 'grades'));
    $settings->add(new admin_setting_configselect('grade_report_forecast_showhiddenitems', get_string('showhiddenitems', 'grades'), get_string('showhiddenitems_help', 'grades'), 1, $options));

    $settings->add(new admin_setting_configselect('grade_report_forecast_showtotalsifcontainhidden', get_string('hidetotalifhiddenitems', 'grades'),
                                                      get_string('hidetotalifhiddenitems_help', 'grades'), GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN,
                                                      array(GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN => get_string('hide'),
                                                            GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowexhiddenitems', 'grades'),
                                                            GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowinchiddenitems', 'grades'))));

    $settings->add(new admin_setting_configcheckbox('grade_report_forecast_enabledforstudents', $_s('enabled_for_students'), $_s('enabled_for_students_desc'), 1));

    $settings->add(new admin_setting_configcheckbox('grade_report_forecast_mustmakeenabled', $_s('must_make_enabled'), $_s('must_make_enabled_desc'), 0));

    $settings->add(new admin_setting_configtext('grade_report_forecast_debouncewaittime',
        $_s('debounce_wait_time'), $_s('debounce_wait_time_desc'), '1000'));
}
