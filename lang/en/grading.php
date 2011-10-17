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
 * Strings for the advanced grading methods subsystem
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activemethodinfo'] = '\'{$a->method}\' is selected as the active grading method for the \'{$a->area}\' area';
$string['activemethodinfonone'] = 'There is no advanced grading method selected for the \'{$a->area}\' area. Simple direct grading will be used.';
$string['changeactivemethod'] = 'Change active grading method to';
$string['exc_gradingformelement'] = 'Unable to instantiate grading form element';
$string['formnotavailable'] = 'Advanced grading method was selected to use but the grading form is not available yet. You may need to define it first via a link in the Settings block.';
$string['gradingmanagement'] = 'Advanced grading';
$string['gradingmanagementtitle'] = 'Advanced grading: {$a->component} ({$a->area})';
$string['gradingmethod'] = 'Grading method';
$string['gradingmethod_help'] = 'Choose the advanced grading method that should be used for calculating grades in the given context.

To disable advance grading and switch back to the default grading mechanism, choose \'Simple direct grading\'.';
$string['gradingmethods'] = 'Grading methods';
$string['gradingmethodnone'] = 'Simple direct grading';
$string['manageactionclone'] = 'Create new grading form from template';
$string['manageactiondelete'] = 'Remove the currently defined form';
$string['manageactionedit'] = 'Edit the current form definition';
$string['manageactionnew'] = 'Define new grading form from scratch';
$string['noitemid'] = 'Grading not possible. The graded item does not exist.';
