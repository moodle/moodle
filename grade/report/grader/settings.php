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

$strinherit             = get_string('inherit', 'grades');
$strpercentage          = get_string('percentage', 'grades');
$strreal                = get_string('real', 'grades');
$strletter              = get_string('letter', 'grades');
$strinherit             = get_string('inherit', 'grades');

/// Add settings for this module to the $settings object (it's already defined)
$settings->add(new admin_setting_configtext('grade_report_studentsperpage', get_string('studentsperpage', 'grades'),
                                        get_string('configstudentsperpage', 'grades'), 100));

$settings->add(new admin_setting_configcheckbox('grade_report_quickgrading', get_string('quickgrading', 'grades'),
                                            get_string('configquickgrading', 'grades'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_showquickfeedback', get_string('quickfeedback', 'grades'),
                                            get_string('configshowquickfeedback', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_fixedstudents', get_string('fixedstudents', 'grades'),
                                            get_string('configfixedstudents', 'grades'), 0));

$settings->add(new admin_setting_configselect('grade_report_meanselection', get_string('meanselection', 'grades'),
                                          get_string('configmeanselection', 'grades'), GRADE_REPORT_MEAN_GRADED,
                                          array(GRADE_REPORT_MEAN_ALL => get_string('meanall', 'grades'),
                                                GRADE_REPORT_MEAN_GRADED => get_string('meangraded', 'grades'))));

// $settings->add(new admin_setting_configcheckbox('grade_report_enableajax', get_string('enableajax', 'grades'),
//                                            get_string('configenableajax', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showcalculations', get_string('showcalculations', 'grades'),
                                            get_string('configshowcalculations', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showeyecons', get_string('showeyecons', 'grades'),
                                            get_string('configshoweyecons', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showaverages', get_string('showaverages', 'grades'),
                                            get_string('configshowaverages', 'grades'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_showlocks', get_string('showlocks', 'grades'),
                                            get_string('configshowlocks', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showranges', get_string('showranges', 'grades'),
                                            get_string('configshowranges', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showuserimage', get_string('showuserimage', 'grades'),
                                            get_string('configshowuserimage', 'grades'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_showuseridnumber', get_string('showuseridnumber', 'grades'),
                                            get_string('configshowuseridnumber', 'grades'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_showactivityicons', get_string('showactivityicons', 'grades'),
                                            get_string('configshowactivityicons', 'grades'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_shownumberofgrades', get_string('shownumberofgrades', 'grades'),
                                            get_string('configshownumberofgrades', 'grades'), 0));

$settings->add(new admin_setting_configselect('grade_report_averagesdisplaytype', get_string('averagesdisplaytype', 'grades'),
                                          get_string('configaveragesdisplaytype', 'grades'), GRADE_REPORT_PREFERENCE_INHERIT,
                                          array(GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                GRADE_DISPLAY_TYPE_REAL => $strreal,
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => $strpercentage,
                                                GRADE_DISPLAY_TYPE_LETTER => $strletter)));

$settings->add(new admin_setting_configselect('grade_report_rangesdisplaytype', get_string('rangesdisplaytype', 'grades'),
                                          get_string('configrangesdisplaytype', 'grades'), GRADE_REPORT_PREFERENCE_INHERIT,
                                          array(GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                GRADE_DISPLAY_TYPE_REAL => $strreal,
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => $strpercentage,
                                                GRADE_DISPLAY_TYPE_LETTER => $strletter)));

$settings->add(new admin_setting_configselect('grade_report_averagesdecimalpoints', get_string('averagesdecimalpoints', 'grades'),
                                          get_string('configaveragesdecimalpoints', 'grades'), GRADE_REPORT_PREFERENCE_INHERIT,
                                          array(GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                 '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));
$settings->add(new admin_setting_configselect('grade_report_rangesdecimalpoints', get_string('rangesdecimalpoints', 'grades'),
                                          get_string('configrangesdecimalpoints', 'grades'), GRADE_REPORT_PREFERENCE_INHERIT,
                                          array(GRADE_REPORT_PREFERENCE_INHERIT => $strinherit,
                                                 '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));


?>
