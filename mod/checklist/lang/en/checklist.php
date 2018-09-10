<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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

$string['addcomments'] = 'Add comments';

$string['additem'] = 'Add';
$string['additemalt'] = 'Add a new item to the list';
$string['additemhere'] = 'Insert new item after this one';
$string['addownitems'] = 'Add your own items';
$string['addownitems-stop'] = 'Stop adding your own items';

$string['allowmodulelinks'] = 'Allow module links';

$string['anygrade'] = 'Any';
$string['anygrouping'] = 'Any grouping';
$string['autopopulate'] = 'Show course modules in checklist';
$string['autopopulate_help'] = 'This will automatically add a list of all the resources and activities in the current course into the checklist.<br />
This list will be updated with any changes in the course, whenever you visit the \'Edit\' page for the checklist.<br />
Items can be hidden from the list, by clicking on the \'hide\' icon beside them.<br />
To remove the automatic items from the list, change this option back to \'No\', then click on \'Remove course module items\' on the \'Edit\' page.';
$string['autoupdate'] = 'Check-off when modules complete';
$string['autoupdate2'] = 'Check-off when courses or modules complete';
$string['autoupdate_help'] = 'This will automatically check-off items in your checklist when you complete the relevant activity in the course.<br />
If completion tracking is switched on for a particular activity, that will be used to tick-off the item in the list<br>
Otherwise, \'completing\' an activity varies from one activity to another - \'view\' a resource, \'submit\' a quiz or assignment, \'post\' to a forum or join in with a chat, etc. (for details of exactly what causes an activity to be marked as \'complete\', ask your site administrator to look in the file \'mod/checklist/classes/local/autoupdate.php\')<br>';
$string['autoupdate2_help'] = 'This will automatically check-off items in your checklist when you complete the relevant activity in the course.<br />
If completion tracking is switched on for a particular activity, that will be used to tick-off the item in the list<br>
Otherwise, \'completing\' an activity varies from one activity to another - \'view\' a resource, \'submit\' a quiz or assignment, \'post\' to a forum or join in with a chat, etc. (for details of exactly what causes an activity to be marked as \'complete\', ask your site administrator to look in the file \'mod/checklist/classes/local/autoupdate.php\')<br>
If an item is linked to a course and that course has completion enabled for it, then the item will be updated when that course is marked as complete.';
$string['autoupdatenote'] = 'It is the \'student\' mark that is automatically updated - no updates will be displayed for \'Teacher only\' checklists';

$string['autoupdatewarning_both'] = 'There are items on this list that will be automatically updated (as students complete the related activity). However, as this is a \'student and teacher\' checklist the progress bars will not update until a teacher agrees the marks given.';
$string['autoupdatewarning_student'] = 'There are items on this list that will be automatically updated (as students complete the related activity).';
$string['autoupdatewarning_teacher'] = 'There are items on this list that will be automatically updated (as students complete the related activity).';

$string['canceledititem'] = 'Cancel';

$string['calendardescription'] = 'This event was added by the checklist: {$a}';

$string['changetextcolour'] = 'Next text colour';

$string['checkeditemsdeleted'] = 'Checked items deleted';

$string['checklist'] = 'checklist';
$string['pluginadministration'] = 'Checklist administration';

$string['checklist:addinstance'] = 'Add a new checklist';
$string['checklist:edit'] = 'Create and edit checklists';
$string['checklist:emailoncomplete'] = 'Receive completion emails';
$string['checklist:preview'] = 'Preview a checklist';
$string['checklist:updatelocked'] = 'Update locked checklist marks';
$string['checklist:updateother'] = 'Update students\' checklist marks';
$string['checklist:updateown'] = 'Update your checklist marks';
$string['checklist:viewmenteereports'] = 'View mentee progress (only)';
$string['checklist:viewreports'] = 'View students\' progress';

$string['checklistautoupdate'] = 'Allow checklists to automatically update';

$string['checklistfor'] = 'Checklist for';

$string['checklistintro'] = 'Introduction';
$string['checklistsettings'] = 'Settings';

$string['checks'] = 'Check marks';
$string['choosecourse'] = 'Choose course...';
$string['comments'] = 'Comments';

$string['completionpercentgroup'] = 'Require checked-off';
$string['completionpercent'] = 'Percentage of items that should be checked-off:';

$string['configchecklistautoupdate'] = 'Before allowing this you must make a few changes to the core Moodle code, please see mod/checklist/README.txt for details';
$string['configshowupdateablemymoodle'] = 'If this is checked then only updatable Checklists will be shown from the \'My Moodle\' page';
$string['configshowcompletemymoodle'] = 'If this is unchecked then completed Checklists will be hidden from the \'My Moodle\' page';
$string['configshowmymoodle'] = 'If this is unchecked then Checklist activities (with progress bars) will no longer appear on the \'My Moodle\' page';

$string['confirmdeleteitem'] = 'Are you sure you want to permanently delete this checklist item?';

$string['deleteitem'] = 'Delete this item';

$string['duedatesoncalendar'] = 'Add due dates to calendar';

$string['edit'] = 'Edit checklist';
$string['editchecks'] = 'Edit checks';
$string['editdatesstart'] = 'Edit dates';
$string['editdatesstop'] = 'Stop editing dates';
$string['edititem'] = 'Edit this item';

$string['emailoncomplete'] = 'Email when checklist is complete:';
$string['emailoncomplete_help'] = 'When a checklist is complete, a notification email can be sent: to the student who completed it, to all the teachers on the course or to both.<br />
An administrator can control who receives this email using the capability \'mod:checklist/emailoncomplete\' - by default all teachers and non-editing teachers have this capability.';
$string['emailoncompletesubject'] = 'User {$a->user} has completed checklist \'{$a->checklist}\'';
$string['emailoncompletesubjectown'] = 'You have completed checklist \'{$a->checklist}\'';
$string['emailoncompletebody'] = 'User {$a->user} has completed checklist \'{$a->checklist}\' in the course \'{$a->coursename}\'
View the checklist here:';
$string['emailoncompletebodyown'] = 'You have completed checklist \'{$a->checklist}\' in the course \'{$a->coursename}\'
View the checklist here:';
$string['enterurl'] = 'Enter url ...';
$string['eventchecklistcomplete'] = 'Checklist complete';
$string['eventeditpageviewed'] = 'Edit page viewed';
$string['eventreportviewed'] = 'Report viewed';
$string['eventstudentchecksupdated'] = 'Student checks updated';
$string['eventteacherchecksupdated'] = 'Teacher checks updated';

$string['export'] = 'Export items';

$string['forceupdate'] = 'Update checks for all automatic items';

$string['gradetocomplete'] = 'Grade to complete:';
$string['grouping'] = 'Visible to grouping';
$string['guestsno'] = 'You do not have permission to view this checklist';

$string['headingitem'] = 'This item is a heading - it will not have a checkbox beside it';

$string['import'] = 'Import items';
$string['importfile'] = 'Choose file to import';
$string['importfromsection'] = 'Current section';
$string['importfromcourse'] = 'Whole course';
$string['indentitem'] = 'Indent item';
$string['itemcomplete'] = 'Completed';
$string['items'] = 'Checklist items';

$string['linkcourses'] = 'Allow linking items to courses';
$string['linkcourses_desc'] = 'When enabled, checklist items can be linked to courses within Moodle - being marked as complete when the associated course is completed. Enabling this may have some performance implications when editing checkist items on sites with a large number of courses.';
$string['linkto'] = 'Link to';
$string['linktocourse'] = 'Course associated with this item';
$string['linktomodule'] = 'Activity associated with this item';
$string['linktourl'] = 'Link associated with this item';

$string['lockteachermarks'] = 'Lock teacher marks';
$string['lockteachermarks_help'] = 'When this setting is enabled, once a teacher has saved a \'Yes\' mark, they will be unable to change it. Users with the capability \'mod/checklist:updatelocked\' will still be able to change the mark.';
$string['lockteachermarkswarning'] = 'Note: Once you have saved these marks, you will be unable to change any \'Yes\' marks';

$string['modulename'] = 'Checklist';
$string['modulename_help'] = 'The checklist module allows a teacher to create a checklist / todo list / task list for their students to work through.';
$string['modulenameplural'] = 'Checklists';

$string['moveitemdown'] = 'Move item down';
$string['moveitemup'] = 'Move item up';

$string['noitems'] = 'No items in the checklist';

$string['onlyenrolled'] = 'Only active users';
$string['onlyenrolleddesc'] = 'When selected, only users with active enrolments are shown in checklists (deselect to return to the old behaviour)';
$string['optionalitem'] = 'This item is optional';
$string['optionalhide'] = 'Hide optional items';
$string['optionalshow'] = 'Show optional items';
$string['or'] = 'OR';

$string['percentcomplete'] = 'Required items';
$string['percentcompleteall'] = 'All items';
$string['pluginname'] = 'Checklist';
$string['preview'] = 'Preview';

$string['privacy:metadata:checklist_check'] = 'Information about which checklist items have been checked off by the user (or checked off by a teacher for the user) on a given checklist';
$string['privacy:metadata:checklist_check:item'] = 'The ID of the checklist item that has been checked-off';
$string['privacy:metadata:checklist_check:teacherid'] = 'The ID of the teacher who last updated the status of this item';
$string['privacy:metadata:checklist_check:teachermark'] = 'The status allocated to this item, for the user, by a teacher';
$string['privacy:metadata:checklist_check:teachertimestamp'] = 'The time when the teacher last allocated a status to this item';
$string['privacy:metadata:checklist_check:userid'] = 'The ID of the user who has checked-off the item (or, if marked by a teacher, the user it was checked-off for)';
$string['privacy:metadata:checklist_check:usertimestamp'] = 'The time when the user last checked-off the item (0 if the item is currently unchecked)';
$string['privacy:metadata:checklist_comment'] = 'Information about comments that have been made by a teacher about the user\'s progress on a given checklist';
$string['privacy:metadata:checklist_comment:commentby'] = 'The ID of the user (teacher) that made this comment';
$string['privacy:metadata:checklist_comment:itemid'] = 'The ID of the checklist item that the comment relates to';
$string['privacy:metadata:checklist_comment:text'] = 'The text of the comment';
$string['privacy:metadata:checklist_comment:userid'] = 'The ID of the user (student) that this comment relates to';
$string['privacy:metadata:checklist_item'] = 'Information about private items the user has added to the given checklist';
$string['privacy:metadata:checklist_item:checklist'] = 'The ID of the checklist this private item was added to';
$string['privacy:metadata:checklist_item:displaytext'] = 'The text of the private checklist item';
$string['privacy:metadata:checklist_item:userid'] = 'The ID of the user who created this private item (0 for items that are created by a teacher and shown to all users)';

$string['progress'] = 'Progress';

$string['removeauto'] = 'Remove course module items';

$string['report'] = 'View progress';
$string['reporttablesummary'] = 'Table showing the items on the checklist that each student has completed';

$string['requireditem'] = 'This item is required - it must be completed';

$string['resetchecklistprogress'] = 'Reset checklist progress and user items';

$string['savechecks'] = 'Save';

$string['showcompletemymoodle'] = 'Show completed Checklists on \'My Moodle\' page';
$string['showfulldetails'] = 'Show full details';
$string['showhidechecked'] = 'Show/hide selected items';
$string['showupdateablemymoodle'] = 'Show only updatable Checklists on \'My Moodle\' page';
$string['showmymoodle'] = 'Show Checklists on \'My Moodle\' page';
$string['showprogressbars'] = 'Show progress bars';

$string['teachercomments'] = 'Teachers can add comments';
$string['teacherdate'] = 'Date a teacher last updated this item';

$string['teacheredit'] = 'Updates by';
$string['teacherid'] = 'The teacher who last updated this mark';

$string['teachermarkundecided'] = 'Teacher has not yet marked this';
$string['teachermarkyes'] = 'Teacher states that you have completed this';
$string['teachermarkno'] = 'Teacher states that you have NOT completed this';

$string['teachernoteditcheck'] = 'Student only';
$string['teacheroverwritecheck'] = 'Teacher only';
$string['teacheralongsidecheck'] = 'Student and teacher';

$string['togglecolumn'] = 'Toggle Column';
$string['toggledates'] = 'Toggle names & dates';
$string['togglerow'] = 'Toggle Row';
$string['theme'] = 'Checklist display theme';

$string['updatecompletescore'] = 'Save completion grades';
$string['unindentitem'] = 'Unindent item';
$string['updateitem'] = 'Update';
$string['userdate'] = 'Date the user last updated this item';
$string['useritemsallowed'] = 'User can add their own items';
$string['useritemsdeleted'] = 'User items deleted';

$string['view'] = 'View checklist';
$string['viewall'] = 'View all students';
$string['viewallcancel'] = 'Cancel';
$string['viewallsave'] = 'Save';

$string['viewsinglereport'] = 'View progress for this user';
$string['viewsingleupdate'] = 'Update progress for this user';

$string['yesnooverride'] = 'Yes, cannot override';
$string['yesoverride'] = 'Yes, can override';
