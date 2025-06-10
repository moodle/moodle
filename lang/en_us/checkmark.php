<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'checkmark', language 'en_us', version '4.1'.
 *
 * @package     checkmark
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autograde_confirm_continue'] = 'Are you sure you want to continue?';
$string['autograde_str_help'] = 'Auto-grading calculates users grades according to points per example and checked examples. It adds the points for each checked example and uses this as the users grade. <ul><li>Grade selected users - grades just these users, which are checked in the list. If a user hasn\'t submitted anything, a empty submission gets added.</li><li>Grade who needs grading - grades every submission which is more up to date than the corresponding grading</li><li>Grade all submissions - grades all present submissions (for this instance). Does NOT add empty submissions.</li></ul><br />The grade gets calculated based on chosen example grades and checked examples:<ul><li>Standard-grading: here each example is equally weighted (integral grade per example). The grade is calculated by multiplication of the sum of checked examples with the quotient of checkmark-grade and checkmark-count.</li><li>Individual example-weights: the grade is the sum of example grades for each checked example (according to instance-settings).</li></ul>';
$string['availabledate_help'] = 'Beginning of the submission period. After this date students are able to submit.';
$string['count_individuals_mismatch'] = 'The number of individual names({$a->namecount}) doesn\'t match the number of individual grades({$a->gradecount})!';
$string['coursemisconf'] = 'Course is misconfigured';
$string['cutoffdate_help'] = 'If activated, this indicates the end of the submission period. After this date, no student will be able to submit. If disabled, this indicates students are allowed to submit even after the due date.';
$string['data_preview_help'] = 'Click on [+] or [-] for showing or hiding columns in the print preview.';
$string['gradingdue_help'] = 'The expected date that marking of the submissions should be completed by. This date is used to prioritize dashboard notifications for teachers.';
$string['pdfprintheader'] = 'Print header/footer';
