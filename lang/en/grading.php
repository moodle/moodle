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
 * @package    core_grading
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
$string['formnotavailable'] = 'An advanced grading method was selected to use but the grading form is not available yet. You may need to define it first via a link in the actions menu or administration block.';
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
$string['privacy:metadata:gradingformpluginsummary'] = 'Data for the grading method.';
$string['privacy:metadata:grading_definitions'] = 'Basic information about an advanced grading form defined in a gradable area.';
$string['privacy:metadata:grading_definitions:areaid'] = 'The area ID where the advanced grading form is defined.';
$string['privacy:metadata:grading_definitions:copiedfromid'] = 'The grading definition ID from where this was copied.';
$string['privacy:metadata:grading_definitions:description'] = 'The description of the advanced grading method.';
$string['privacy:metadata:grading_definitions:method'] = 'The grading method which is responsible for the definition.';
$string['privacy:metadata:grading_definitions:name'] = 'The name of the advanced grading definition.';
$string['privacy:metadata:grading_definitions:options'] = 'Some settings of this grading definition.';
$string['privacy:metadata:grading_definitions:status'] = 'The status of this advanced grading definition.';
$string['privacy:metadata:grading_definitions:timecopied'] = 'The time when the grading definition was copied.';
$string['privacy:metadata:grading_definitions:timecreated'] = 'The time when the grading definition was created.';
$string['privacy:metadata:grading_definitions:timemodified'] = 'The time when the grading definition was last modified.';
$string['privacy:metadata:grading_definitions:usercreated'] = 'The ID of the user who created the grading definition.';
$string['privacy:metadata:grading_definitions:usermodified'] = 'The ID of the user who last modified the grading definition.';
$string['privacy:metadata:grading_instances'] = 'Assessment record for one gradable item assessed by one rater.';
$string['privacy:metadata:grading_instances:feedback'] = 'The feedback given by the user.';
$string['privacy:metadata:grading_instances:feedbackformat'] = 'The text format of the feedback given by the user.';
$string['privacy:metadata:grading_instances:raterid'] = 'The ID of the user who rated the grading instance.';
$string['privacy:metadata:grading_instances:rawgrade'] = 'The grade for the grading instance.';
$string['privacy:metadata:grading_instances:status'] = 'The status of this grading instance.';
$string['privacy:metadata:grading_instances:timemodified'] = 'The time when the grading instance was last modified.';
$string['searchtemplate'] = 'Grading forms search';
$string['searchtemplate_help'] = 'You can search for a grading form and use it as a template for the new grading form here. Simply type words that should appear somewhere in the form name, its description or the form body itself. To search for a phrase, wrap the whole query in double quotes.

By default, only the grading forms that have been saved as shared templates are included in the search results. You can also include all your own grading forms in the search results. This way, you can simply re-use your grading forms without sharing them. Only forms marked as \'Ready for use\' can be re-used this way.';
$string['searchownforms'] = 'include my own forms';
$string['statusdraft'] = 'Draft';
$string['statusready'] = 'Ready for use';
$string['templatedelete'] = 'Delete';
$string['templatedeleteconfirm'] = 'You are going to delete the shared template \'{$a}\'. Deleting a template does not affect existing forms that were created from it.';
$string['templateedit'] = 'Edit';
$string['templatepick'] = 'Use this template';
$string['templatepickconfirm'] = 'Do you want to use the grading form \'{$a->formname}\' as a template for the new grading form in \'{$a->component} ({$a->area})\'?';
$string['templatepickownform'] = 'Use this form as a template';
$string['templatetypeown'] = 'Own form';
$string['templatetypeshared'] = 'Shared template';
$string['templatesource'] = 'Location: {$a->component} ({$a->area})';
$string['error:notinrange'] = 'Invalid grade \'{$a->grade}\' provided. Grades must be between 0 and {$a->maxgrade}.';
