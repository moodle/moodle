<?php // $Id$

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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


/**
 * Definitions of constants for gradebook
 *
 * @author Moodle HQ developers
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

// category aggregation types
define('GRADE_AGGREGATE_MEAN', 0);
define('GRADE_AGGREGATE_MEDIAN', 2);
define('GRADE_AGGREGATE_MIN', 4);
define('GRADE_AGGREGATE_MAX', 6);
define('GRADE_AGGREGATE_MODE', 8);
define('GRADE_AGGREGATE_WEIGHTED_MEAN', 10);
define('GRADE_AGGREGATE_WEIGHTED_MEAN2', 11);
define('GRADE_AGGREGATE_EXTRACREDIT_MEAN', 12);
define('GRADE_AGGREGATE_SUM', 13);

// grade types
define('GRADE_TYPE_NONE', 0);
define('GRADE_TYPE_VALUE', 1);
define('GRADE_TYPE_SCALE', 2);
define('GRADE_TYPE_TEXT', 3);

// grade_update() return status
define('GRADE_UPDATE_OK', 0);
define('GRADE_UPDATE_FAILED', 1);
define('GRADE_UPDATE_MULTIPLE', 2);
define('GRADE_UPDATE_ITEM_DELETED', 3);
define('GRADE_UPDATE_ITEM_LOCKED', 4);

// Grate teables history tracking actions
define('GRADE_HISTORY_INSERT', 1);
define('GRADE_HISTORY_UPDATE', 2);
define('GRADE_HISTORY_DELETE', 3);

// Display style constants
define('GRADE_DISPLAY_TYPE_DEFAULT', 0);
define('GRADE_DISPLAY_TYPE_REAL', 1);
define('GRADE_DISPLAY_TYPE_PERCENTAGE', 2);
define('GRADE_DISPLAY_TYPE_LETTER', 3);
define('GRADE_DISPLAY_TYPE_REAL_PERCENTAGE', 12);
define('GRADE_DISPLAY_TYPE_REAL_LETTER', 13);
define('GRADE_DISPLAY_TYPE_LETTER_REAL', 31);
define('GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE', 32);
define('GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER', 23);
define('GRADE_DISPLAY_TYPE_PERCENTAGE_REAL', 21);

define('GRADE_REPORT_AGGREGATION_POSITION_FIRST', 0);
define('GRADE_REPORT_AGGREGATION_POSITION_LAST', 1);
define('GRADE_REPORT_AGGREGATION_VIEW_FULL', 0);
define('GRADE_REPORT_AGGREGATION_VIEW_AGGREGATES_ONLY', 1);
define('GRADE_REPORT_AGGREGATION_VIEW_GRADES_ONLY', 2);

define('GRADE_REPORT_PREFERENCE_DEFAULT', 'default'); // means use setting from site preferences
define('GRADE_REPORT_PREFERENCE_INHERIT', 'inherit'); // means inherit from parent
define('GRADE_REPORT_PREFERENCE_UNUSED', -1);

define('GRADE_REPORT_MEAN_ALL', 0);
define('GRADE_REPORT_MEAN_GRADED', 1);

define('GRADE_NAVMETHOD_DROPDOWN', 0);
define('GRADE_NAVMETHOD_TABS', 1);
define('GRADE_NAVMETHOD_COMBO', 2);
?>
