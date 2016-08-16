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
 * Definitions of constants for gradebook
 *
 * @package   core_grades
 * @category  grade
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Category aggregation types

/**
 * GRADE_AGGREGATE_MEAN - Use the category mean for grade aggregation.
 */
define('GRADE_AGGREGATE_MEAN', 0);

/**
 * GRADE_AGGREGATE_MEDIAN - Use the category median for grade aggregation.
 */
define('GRADE_AGGREGATE_MEDIAN', 2);

/**
 * GRADE_AGGREGATE_MIN - Use the category minimum grade for grade aggregation.
 */
define('GRADE_AGGREGATE_MIN', 4);

/**
 * GRADE_AGGREGATE_MAX - Use the category maximum grade for grade aggregation.
 */
define('GRADE_AGGREGATE_MAX', 6);

/**
 * GRADE_AGGREGATE_MEDIAN - Use the category mode for grade aggregation.
 */
define('GRADE_AGGREGATE_MODE', 8);

/**
 * GRADE_AGGREGATE_WEIGHTED_MEAN - Use a weighted mean of grades in the category for grade aggregation. Weights can be manually set.
 */
define('GRADE_AGGREGATE_WEIGHTED_MEAN', 10);

/**
 * GRADE_AGGREGATE_WEIGHTED_MEAN2 - Use a simple weighted mean of grades in the category for grade aggregation.
 */
define('GRADE_AGGREGATE_WEIGHTED_MEAN2', 11);

/**
 * GRADE_AGGREGATE_EXTRACREDIT_MEAN - Use the category mean for grade aggregation and include support for extra credit.
 */
define('GRADE_AGGREGATE_EXTRACREDIT_MEAN', 12);

/**
 * GRADE_AGGREGATE_WEIGHTED_MEAN2 - Use Natural in the category for grade aggregation.
 */
define('GRADE_AGGREGATE_SUM', 13);

// Grade types

/**
 * GRADE_TYPE_NONE - Ungraded.
 */
define('GRADE_TYPE_NONE', 0);

/**
 * GRADE_TYPE_NONE - The grade is a numeric value
 */
define('GRADE_TYPE_VALUE', 1);

/**
 * GRADE_TYPE_NONE - The grade is a value from the set of values available in a grade scale.
 */
define('GRADE_TYPE_SCALE', 2);

/**
 * GRADE_TYPE_NONE - Feedback only.
 */
define('GRADE_TYPE_TEXT', 3);


// grade_update() return status

/**
 * GRADE_UPDATE_OK - Grade updated completed successfully.
 */
define('GRADE_UPDATE_OK', 0);

/**
 * GRADE_UPDATE_FAILED - Grade updated failed.
 */
define('GRADE_UPDATE_FAILED', 1);

/**
 * GRADE_UPDATE_MULTIPLE - Grade update failed because there are multiple grade items with the same itemnumber for this activity.
 */
define('GRADE_UPDATE_MULTIPLE', 2);

/**
 * GRADE_UPDATE_DELETED - Grade item cannot be updated as it is locked
 */
define('GRADE_UPDATE_ITEM_LOCKED', 4);


// Grade tables history tracking actions

/**
 * GRADE_HISTORY_INSERT - A grade item was inserted
 */
define('GRADE_HISTORY_INSERT', 1);

/**
 * GRADE_HISTORY_UPDATE - A grade item was updated
 */
define('GRADE_HISTORY_UPDATE', 2);

/**
 * GRADE_HISTORY_INSERT - A grade item was deleted
 */
define('GRADE_HISTORY_DELETE', 3);

// Display style constants

/**
 * GRADE_DISPLAY_TYPE_DEFAULT - Grade display type can be set at 3 levels: grade_item, course setting and site. Use the display type from the higher level.
 */
define('GRADE_DISPLAY_TYPE_DEFAULT', 0);

/**
 * GRADE_DISPLAY_TYPE_REAL - Display the grade as a decimal number.
 */
define('GRADE_DISPLAY_TYPE_REAL', 1);

/**
 * GRADE_DISPLAY_TYPE_PERCENTAGE - Display the grade as a percentage.
 */
define('GRADE_DISPLAY_TYPE_PERCENTAGE', 2);

/**
 * GRADE_DISPLAY_TYPE_LETTER - Display the grade as a letter grade. For example, A, B, C, D or F.
 */
define('GRADE_DISPLAY_TYPE_LETTER', 3);

/**
 * GRADE_DISPLAY_TYPE_REAL_PERCENTAGE - Display the grade as a decimal number and a percentage.
 */
define('GRADE_DISPLAY_TYPE_REAL_PERCENTAGE', 12);

/**
 * GRADE_DISPLAY_TYPE_REAL_LETTER - Display the grade as a decimal number and a letter grade.
 */
define('GRADE_DISPLAY_TYPE_REAL_LETTER', 13);

/**
 * GRADE_DISPLAY_TYPE_LETTER_REAL - Display the grade as a letter grade and a decimal number.
 */
define('GRADE_DISPLAY_TYPE_LETTER_REAL', 31);

/**
 * GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE - Display the grade as a letter grade and a percentage.
 */
define('GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE', 32);

/**
 * GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER - Display the grade as a percentage and a letter grade.
 */
define('GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER', 23);

/**
 * GRADE_DISPLAY_TYPE_PERCENTAGE_REAL - Display the grade as a percentage and a decimal number.
 */
define('GRADE_DISPLAY_TYPE_PERCENTAGE_REAL', 21);

/**
 * GRADE_REPORT_AGGREGATION_POSITION_FIRST - Display the course totals before the individual activity grades
 */
define('GRADE_REPORT_AGGREGATION_POSITION_FIRST', 0);

/**
 * GRADE_REPORT_AGGREGATION_POSITION_LAST - Display the course totals after the individual activity grades
 */
define('GRADE_REPORT_AGGREGATION_POSITION_LAST', 1);

// What to do if category or course total contains a hidden item

/**
 * GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN - If the category or course total contains a hidden item hide the total from students.
 */
define('GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN', 0);

/**
 * GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN - If the category or course total contains a hidden item show the total to students minus grades from the hidden items.
 */
define('GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN', 1);

/**
 * GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN - If the category or course total contains a hidden item show students the real total including marks from hidden items.
 */
define('GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN', 2);

/**
 * GRADE_REPORT_PREFERENCE_DEFAULT - Use the setting from site preferences.
 */
define('GRADE_REPORT_PREFERENCE_DEFAULT', 'default');

/**
 * GRADE_REPORT_PREFERENCE_INHERIT - Inherit the setting value from the parent.
 */
define('GRADE_REPORT_PREFERENCE_INHERIT', 'inherit'); // means inherit from parent

/**
 * GRADE_REPORT_PREFERENCE_UNUSED - Unused constant.
 */
define('GRADE_REPORT_PREFERENCE_UNUSED', -1);

/**
 * GRADE_REPORT_MEAN_ALL - Include all grade items including those where the student hasn't received a grade when calculating the mean.
 */
define('GRADE_REPORT_MEAN_ALL', 0);

/**
 * GRADE_REPORT_MEAN_GRADED - Only include grade items where the student has a grade when calculating the mean.
 */
define('GRADE_REPORT_MEAN_GRADED', 1);

/**
 * GRADE_NAVMETHOD_DROPDOWN - Display a drop down box to allow navigation within the gradebook
 */
define('GRADE_NAVMETHOD_DROPDOWN', 0);

/**
 * GRADE_NAVMETHOD_TABS - Display tabs to allow navigation within the gradebook
 */
define('GRADE_NAVMETHOD_TABS', 1);

/**
 * GRADE_NAVMETHOD_TABS - Display both a drop down and tabs to allow navigation within the gradebook
 */
define('GRADE_NAVMETHOD_COMBO', 2);

/**
 * GRADE_MIN_MAX_FROM_GRADE_ITEM - Get the grade min/max from the grade item.
 */
define('GRADE_MIN_MAX_FROM_GRADE_ITEM', 1);

/**
 * GRADE_MIN_MAX_FROM_GRADE_GRADE - Get the grade min/max from the grade grade.
 */
define('GRADE_MIN_MAX_FROM_GRADE_GRADE', 2);
