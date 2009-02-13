<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////


$strshow = get_string('statsshow', 'gradereport_stats') . ' ';

/// Add settings for this module to the $settings object (it's already defined)
$settings->add(new admin_setting_configselect('grade_report_stats_aggregationposition', 
    get_string('aggregationposition', 'grades'), 
    get_string('configaggregationposition', 'grades'), 
    GRADE_REPORT_AGGREGATION_POSITION_LAST, 
    array(GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
          GRADE_REPORT_AGGREGATION_POSITION_LAST => get_string('positionlast', 'grades'))));

$settings->add(new admin_setting_configcheckbox('grade_report_statshighest', 
    $strshow . get_string('highest', 'gradereport_stats'),
    get_string('stats:stat:highest', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statslowest', 
    $strshow . get_string('lowest', 'gradereport_stats'),
    get_string('stats:stat:lowest', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsmean', 
    $strshow . get_string('mean', 'gradereport_stats'),
    get_string('stats:stat:mean', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsmedian', 
    $strshow . get_string('median', 'gradereport_stats'),
    get_string('stats:stat:median', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsmode', 
    $strshow . get_string('mode', 'gradereport_stats'),
    get_string('stats:stat:mode', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statspass_percent', 
    $strshow . get_string('pass_percent', 'gradereport_stats'),
    get_string('stats:stat:passpercent', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsstandard_deviation', 
    $strshow . get_string('standarddeviation', 'gradereport_stats'),
    get_string('stats:stat:standarddeviation', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshowgroups', 
    $strshow . get_string('showgroups', 'gradereport_stats'),
    get_string('showgroups', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshowranges', 
    $strshow . get_string('showranges', 'gradereport_stats'),
    get_string('showranges', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshownumgrades', 
    $strshow . get_string('shownumgrades', 'gradereport_stats'),
    get_string('shownumgrades', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshowscaleitems', 
    $strshow . get_string('showscaleitems', 'gradereport_stats'),
    get_string('showscaleitems', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshowvalueitems', 
    $strshow . get_string('showvalueitems', 'gradereport_stats'),
    get_string('showvalueitems', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsshowinverted', 
    $strshow . get_string('showinverted', 'gradereport_stats'),
    get_string('showinverted', 'gradereport_stats'), 1))
;
$settings->add(new admin_setting_configcheckbox('grade_report_statsincompleasmin', 
    $strshow . get_string('incompleasmin', 'gradereport_stats'),
    get_string('incompleasmin', 'gradereport_stats'), 0));

$settings->add(new admin_setting_configcheckbox('grade_report_statsusehidden', 
    $strshow . get_string('usehidden', 'gradereport_stats'),
    get_string('usehidden', 'gradereport_stats'), 1));

$settings->add(new admin_setting_configcheckbox('grade_report_statsuselocked', 
    $strshow . get_string('uselocked', 'gradereport_stats'),
    get_string('uselocked', 'gradereport_stats'), 1));

?>
