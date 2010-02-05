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

/// Add settings for this module to the $settings object (it's already defined)

$settings->add(new admin_setting_configcheckbox('grade_report_user_showrank', get_string('showrank', 'grades'), get_string('configshowrank', 'grades'), 0, PARAM_INT));
$settings->add(new admin_setting_configcheckbox('grade_report_user_showpercentage', get_string('showpercentage', 'grades'), get_string('configshowpercentage', 'grades'), 2, PARAM_INT));

$options = array(0 => get_string('shownohidden', 'grades'),
                 1 => get_string('showhiddenuntilonly', 'grades'),
                 2 => get_string('showallhidden', 'grades'));
$settings->add(new admin_setting_configselect('grade_report_user_showhiddenitems', get_string('showhiddenitems', 'grades'), get_string('configshowhiddenitems', 'grades'), 1, $options));

$settings->add(new admin_setting_configselect('grade_report_user_showtotalsifcontainhidden', get_string('hidetotalifhiddenitems', 'grades'),
                                                  get_string('hidetotalifhiddenitemsdescription', 'grades'), GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN,
                                                  array(GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN => get_string('hide'),
                                                        GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowexhiddenitems', 'grades'),
                                                        GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowinchiddenitems', 'grades'))));

?>
