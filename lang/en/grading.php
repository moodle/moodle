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
$string['clicktoclose'] = 'click to close';
$string['exc_gradingformelement'] = 'Unable to instantiate grading form element';
$string['formnotavailable'] = 'Advanced grading method was selected to use but the grading form is not available yet. You may need to define it first via a link in the Settings block.';
$string['gradingmanagement'] = 'Advanced grading';
$string['gradingmanagementtitle'] = 'Advanced grading: {$a->component} ({$a->area})';
$string['gradingmethod'] = 'Grading method';
$string['gradingmethod_help'] = 'Choose the advanced grading method that should be used for calculating grades in the given context.

To disable advance grading and switch back to the default grading mechanism, choose \'Simple direct grading\'.';
$string['gradingmethodnone'] = 'Simple direct grading';
$string['gradingmethods'] = 'Grading methods';
$string['manageactionclone'] = 'Create new grading form from a template';
$string['manageactiondelete'] = 'Remove the currently defined form';
$string['manageactiondeleteconfirm'] = 'You are going to remove the grading form \'{$a->formname}\' and all the associated information from \'{$a->component} ({$a->area})\'. Please make sure you understand the following consequences:

* There is no way to undo this operation.
* You can switch to another grading method including the \'Simple direct grading\' without removing this form.
* All the information about how the grading forms are filled will be lost.
* The calculated result grades stored in the gradebook will not be affected. However the explanation of how they were calculated will not be available.
* This operation does not affect eventual copies of this form in other activities.';
$string['manageactiondeletedone'] = 'The form was successfully deleted';
$string['manageactionedit'] = 'Edit the current form definition';
$string['manageactionnew'] = 'Define new grading form from scratch';
$string['manageactionshare'] = 'Publish the form as a new template';
$string['manageactionshareconfirm'] = 'You are going to save a copy of the grading form \'{$a}\' as a new public template. Other users at your site will be able to create new grading forms in their activities from that template. Note that users are able to reuse their own grading forms in other activities even if the forms were not saved as template.';
$string['manageactionsharedone'] = 'The form was successfully saved as a template';
$string['noitemid'] = 'Grading not possible. The graded item does not exist.';
