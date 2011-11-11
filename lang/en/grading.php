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
$string['gradingformunavailable'] = 'Please note: the advanced grading form is not ready at the moment. Simple grading method will be used until the form has a valid status.';
$string['gradingmanagement'] = 'Advanced grading';
$string['gradingmanagementtitle'] = 'Advanced grading: {$a->component} ({$a->area})';
$string['gradingmethod'] = 'Grading method';
$string['gradingmethod_help'] = 'Choose the advanced grading method that should be used for calculating grades in the given context.

To disable advanced grading and switch back to the default grading mechanism, choose \'Simple direct grading\'.';
$string['gradingmethodnone'] = 'Simple direct grading';
$string['gradingmethods'] = 'Grading methods';
$string['manageactionclone'] = 'Create new grading form from a template';
$string['manageactiondelete'] = 'Delete the currently defined form';
$string['manageactiondeleteconfirm'] = 'You are going to delete the grading form \'{$a->formname}\' and all the associated information from \'{$a->component} ({$a->area})\'. Please make sure you understand the following consequences:

* There is no way to undo this operation.
* You can switch to another grading method including the \'Simple direct grading\' without deleting this form.
* All the information about how the grading forms are filled will be lost.
* The calculated result grades stored in the gradebook will not be affected. However the explanation of how they were calculated will not be available.
* This operation does not affect eventual copies of this form in other activities.';
$string['manageactiondeletedone'] = 'The form was successfully deleted';
$string['manageactionedit'] = 'Edit the current form definition';
$string['manageactionnew'] = 'Define new grading form from scratch';
$string['manageactionshare'] = 'Publish the form as a new template';
$string['manageactionshareconfirm'] = 'You are going to save a copy of the grading form \'{$a}\' as a new public template. Other users at your site will be able to create new grading forms in their activities from that template.';
$string['manageactionsharedone'] = 'The form was successfully saved as a template';
$string['noitemid'] = 'Grading not possible. The graded item does not exist.';
$string['nosharedformfound'] = 'No template found';
$string['searchtemplate'] = 'Grading forms search';
$string['searchtemplate_help'] = 'You can search for a grading form and use it as a template for the new grading form here. Simply type words that should appear somewhere in the form name, its description or the form body itself. To search for a phrase, wrap the whole query in double quotes.

By default, only the grading forms that have been saved as shared templates are included in the search results. You can also include all your own grading forms in the search results. This way, you can simply re-use your grading forms without sharing them. Only forms marked as \'Ready for usage\' can be re-used this way.';
$string['searchownforms'] = 'include my own forms';
$string['statusdraft'] = 'Draft';
$string['statusready'] = 'Ready for usage';
$string['templatedelete'] = 'Delete';
$string['templatedeleteconfirm'] = 'You are going to delete the shared template \'{$a}\'. Deleting a template does not affect existing forms that were created from it.';
$string['templateedit'] = 'Edit';
$string['templatepick'] = 'Use this template';
$string['templatepickconfirm'] = 'Do you want to use the grading form \'{$a->formname}\' as a template for the new grading form in \'{$a->component} ({$a->area})\'?';
$string['templatepickownform'] = 'Use this form as a template';
$string['templatetypeown'] = 'Own form';
$string['templatetypeshared'] = 'Shared template';
$string['templatesource'] = 'Location: {$a->component} ({$a->area})';
