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
 * Strings for component 'gradingform_guide', language 'en_us', version '4.1'.
 *
 * @package     gradingform_guide
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['definemarkingguide'] = 'Define grading guide';
$string['descriptionmarkers'] = 'Description for Graders';
$string['err_nodescriptionmarkers'] = 'Grader description can not be empty';
$string['guidemappingexplained'] = 'WARNING: Your grading guide has a maximum grade of <b>{$a->maxscore} points</b> but the maximum grade set in your activity is {$a->modulegrade}  The maximum score set in your grading guide will be scaled to the maximum grade in the module.<br />
    Intermediate scores will be converted respectively and rounded to the nearest available grade.';
$string['guideoptions'] = 'Grading guide options';
$string['guidestatus'] = 'Current grading guide status';
$string['hidemarkerdesc'] = 'Hide grader criterion descriptions';
$string['needregrademessage'] = 'The grading guide definition was changed after this student had been graded. The student can not see this marking guide until you check the grading guide and update the grade.';
$string['pluginname'] = 'Grading guide';
$string['previewmarkingguide'] = 'Preview grading guide';
$string['regrademessage1'] = 'You are about to save changes to a grading guide that has already been used for grading. Please indicate if existing grades need to be reviewed. If you set this then the grading guide will be hidden from students until their item is regraded.';
$string['regrademessage5'] = 'You are about to save significant changes to a grading guide that has already been used for grading. The gradebook value will be unchanged, but the grading guide will be hidden from students until their item is regraded.';
$string['saveguide'] = 'Save grading guide and make it ready';
$string['showmarkerdesc'] = 'Show grader criterion descriptions';
$string['showmarkspercriterionstudents'] = 'Show grades per criterion to students';
