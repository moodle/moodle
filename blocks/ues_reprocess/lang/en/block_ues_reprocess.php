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
 *
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Reprocess Enrollment';
$string['ues_reprocess:canreprocess'] = 'Allow UES enrollment reprocessing for courses';
$string['ues_reprocess:addinstance'] = 'Add UES Repocess block';
$string['ues_reprocess:myaddinstance'] = 'Add UES Repocess block';
$string['not_supported'] = 'You have requested an unsupported reprocess type: {$a}';
$string['reprocess'] = 'Reprocess';
$string['reprocess_course'] = 'Reprocess Course';
$string['select'] = 'Select a course or section to be reprocessed.';
$string['none_found'] = 'No sections were found associated with this course. You can either wait for the section association to be restored tonight, or you can force reprocessing on section individually, by continuing.';
$string['cleanup'] = 'Initiating reprocessing cleanup ...';
$string['done'] = 'Done.';
$string['are_you_sure'] = 'Are you sure you want to reprocess the following sections?
    <ul>
    {$a}
    </ul>
';
$string['patience'] = 'Reprocessing can take a few minutes. Please be patient while the job finishes. Thank you.';

// Settings.
$string['settings'] = 'UES Reprocess All';
$string['reprocess_all_courses'] = 'Reprocess requested courses';
$string['semesters'] = 'Semester List';
$string['semesters_help'] = 'Select as many semesters as you\'d like to reprocess';
$string['categories'] = 'Course Categories';
$string['categories_help'] = 'Select as many categories as you\'d like to reprocess for the selected semester(s)';
$string['priorafter'] = 'Days prior / after';
$string['priorafter_help'] = 'The number of days you would like to use as a buffer when listing semesters.';
