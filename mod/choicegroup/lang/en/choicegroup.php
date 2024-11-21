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
 * Strings for component 'choice', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   mod_choicegroup
 * @copyright 2013 Université de Lausanne
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activitydate:closingbeforeopening'] = 'Opening date must be earlier than closing date.';
$string['activitydate:exceeded'] = 'Due date exceeded.';
$string['activitydate:hasopened'] = 'Opened:';
$string['activitydate:notavailableyet'] = 'Not available yet.';
$string['activitydate:willclose'] = 'Closes:';
$string['activitydate:willopen'] = 'Opens:';
$string['add'] = "Add";
$string['add_group'] = "Add Group";
$string['add_grouping'] = "Add Grouping";
$string['add_groupings'] = "Add Groupings";
$string['add_groups'] = "Add Groups";
$string['addmorechoices'] = 'Add more choices';
$string['afterresultsviewable'] = 'The results will be visible after you have made your choice.';
$string['allowupdate'] = 'Allow choice to be updated';
$string['and'] = 'and';
$string['answered'] = 'Answered';
$string['applytoallgroups'] = 'Apply to all groups';
$string['available_groups'] = 'Available Groups';
$string['byparticipants'] = 'by {$a} participants';
$string['char_bullet_collapsed'] = '►';
$string['char_bullet_expanded'] = '▼';
$string['char_limitui_parenthesis_end'] = '⦘';
$string['char_limitui_parenthesis_start'] = '⦗';
$string['choice'] = 'Choice';
$string['choicegroup:addinstance'] = 'Add a new group choice activity';
$string['choicegroup:choose'] = 'Record a choice';
$string['choicegroup:deleteresponses'] = 'Delete responses';
$string['choicegroup:downloadresponses'] = 'Download responses';
$string['choicegroup:readresponses'] = 'Read responses';
$string['choicegroupclose'] = 'Until';
$string['choicegroupfull'] = 'This group choice is full and there are no available places.';
$string['choicegroupname'] = 'Group choice name';
$string['choicegroupopen'] = 'Open';
$string['choicegroupoptions'] = 'Choice options';
$string['choicegroupoptions_description'] = 'Define available group options for participants';
$string['choicegroupoptions_help'] = 'Here is where you specify which groups participants can choose from.

The list on the left displays all available groups and groupings. To add one or several groups, select these from the list and click "Add". To add all groups from a grouping, select the grouping and click "Add".

The selected groups appear on the list on the right.

To remove any groups from the selection, select them from the list on the right and click "Remove".';
$string['choicegroupsaved'] = 'Your choice has been saved';
$string['choicetext'] = 'Choice text';
$string['chooseaction'] = 'Choose an action ...';
$string['choosegroup'] = 'Choose a group';
$string['collapse_all_groupings'] = 'Collapse All Groupings';
$string['completiondetail:submit'] = 'Choose a group';
$string['completionsubmit'] = 'Show as complete when user makes a choice';
$string['createdate'] = 'Group creation date';
$string['defaultgroupdescriptionstate'] = 'Default group description display';
$string['defaultgroupdescriptionstate_desc'] = 'Should the group description be displayed by default or not.';
$string['defaultsettings'] = 'Default settings';
$string['del'] = "Remove";
$string['del_group'] = "Remove Group";
$string['del_groups'] = "Remove Groups";
$string['displayhorizontal'] = 'Display horizontally';
$string['displaymode'] = 'Display mode';
$string['displayvertical'] = 'Display vertically';
$string['double_click_group_legend'] = 'Double click on a group to add it.';
$string['double_click_grouping_legend'] = 'Double click on a grouping to expand/collapse individually.';
$string['event:answered'] = 'Choice made';
$string['event:answered_desc'] = 'The user with id \'{$a->userid}\' has chosen a group in the group choice with the course module id \'{$a->contextinstanceid}\'.';
$string['event:removed'] = 'Choice removed';
$string['event:removed_desc'] = 'The user with id \'{$a->userid}\' has removed his choice in the group choice with the course module id \'{$a->contextinstanceid}\'.';
$string['event:reportviewed'] = 'Report viewed';
$string['event:reportviewed_desc'] = 'The user with id \'{$a->userid}\' has viewed the report for the group choice activity with the course module id \'{$a->contextinstanceid}\'.';
$string['expand_all_groupings'] = 'Expand All Groupings';
$string['expired'] = 'Sorry, this activity closed on {$a} and is no longer available';
$string['fillinatleastoneoption'] = 'You need to provide at least one possible answer.';
$string['fillinatleasttwooptions'] = 'You need to provide at least two possible answers.';
$string['full'] = '(Full)';
$string['generallimitation'] = 'General limitation';
$string['groupdoesntexist'] = 'Some of the specified groups don\'t exist within this course. The teacher should create the necessary groups and/or modify this activity.';
$string['groupmembers'] = 'Group members';
$string['groupsheader'] = "Groups";
$string['havetologin'] = 'You have to log in before you can submit your choice';
$string['hidedescription'] = 'Hide descriptions';
$string['hidegroupmembers'] = 'Hide Group Members';
$string['limit'] = 'Limit';
$string['limitanswers'] = 'Limit the number of responses allowed';
$string['limitanswers_help'] = 'This option allows you to limit the number of participants that can select each choice option. When the limit is reached then no-one else can select that option.

If limits are disabled then any number of participants can select each of the options.';
$string['maxenrollments'] = 'Max. enrollments';
$string['maxenrollments_help'] = 'This option allows to limit the number of group enrollments for a participant. Use default value **0** if there is no limit.';
$string['members/'] = 'Members';
$string['members/max'] = 'Members / Capacity';
$string['modulename'] = 'Group choice';
$string['modulename_help'] = 'The Group Choice module allows students to enrol themselves in a group within a course. The teacher can select which groups students can choose from and the maximum number of students allowed in each group.';
$string['modulename_link'] = 'mod/choicegroup/view';
$string['modulenameplural'] = 'Group choices';
$string['moveselectedusersto'] = 'Move selected users to...';
$string['multipleenrollmentspossible'] = 'Allow enrollment to multiple groups';
$string['mustchoosemax'] = 'You must choose a maximum of {$a} groups. Nothing was saved.';
$string['mustchooseone'] = 'You must choose an answer before saving.  Nothing was saved.';
$string['name'] = 'Name';
$string['neverresultsviewable'] = 'The results are not viewable.';
$string['nogroupincourse'] = 'No groups defined in course.';
$string['noguestchoose'] = 'Sorry, guests are not allowed to make choices.';
$string['noresultsviewable'] = 'The results are not currently viewable.';
$string['notanswered'] = 'Not answered yet';
$string['notenrolledchoose'] = 'Sorry, only enrolled users are allowed to make choices.';
$string['notopenyet'] = 'Sorry, this activity is not available until {$a}';
$string['notyetresultsviewable'] = 'The results will be visible after this activity has closed.';
$string['numberofuser'] = 'The number of users';
$string['onlyactive'] = 'Filter out response data for users with expired or suspended enrolments';
$string['option'] = 'Group';
$string['page-mod-choice-x'] = 'Any Group choice module page';
$string['pleaseselectonegroup'] = 'Please select at least one group to choose from.';
$string['pleasesetgroups'] = 'Please create at least one group in this course.';
$string['pleasesetonegroupor'] = 'Please create at least one group in this course.<br /><br />
<ul>
<li><a href="{$a->linkgroups}">manage course groups</a></li>
<li><a href="{$a->linkcourse}">get back to the course</a></li>
</ul>';
$string['pluginadministration'] = 'Choice administration';
$string['pluginname'] = 'Group choice';
$string['privacy'] = 'Privacy of results';
$string['privacy:metadata'] = 'The Group Choice plugin does not store any personal data. All user data is stored by the group component of Moodle core (core_group).';
$string['publish'] = 'Publish results';
$string['publishafteranswer'] = 'Show results to students after they answer';
$string['publishafterclose'] = 'Show results to students only after the choice is closed';
$string['publishalways'] = 'Always show results to students';
$string['publishanonymous'] = 'Publish anonymous results, do not show student names';
$string['publishnames'] = 'Publish full results, showing names and their choices';
$string['publishnot'] = 'Do not publish results to students';
$string['removemychoicegroup'] = 'Remove my choice';
$string['removeresponses'] = 'Remove all responses';
$string['responses'] = 'Responses';
$string['responsesto'] = 'Responses to {$a}';
$string['samegroupused'] = 'The same group can not be used several times.';
$string['savemychoicegroup'] = 'Save my choice';
$string['selected_groups'] = 'Selected Groups';
$string['set_limit_for_group'] = "Limit For ";
$string['showdescription'] = 'Show descriptions';
$string['showgroupmembers'] = 'Show Group Members';
$string['showunanswered'] = 'Show column for unanswered';
$string['skipresultgraph'] = 'Skip result graph';
$string['sortgroupsby'] = 'Sort groups by';
$string['spaceleft'] = 'space available';
$string['spacesleft'] = 'spaces available';
$string['systemdefault_date'] = 'System Default (currently Group creation date)';
$string['systemdefault_name'] = 'System Default (currently Name)';
$string['taken'] = 'Taken';
$string['the_value_you_entered_is_not_a_number'] = "The value you entered is not a number.";
$string['timerestrict'] = 'Restrict answering to this time period';
$string['viewallresponses'] = 'View {$a} responses';
$string['withselected'] = 'With selected';
$string['yourselection'] = 'Your selection';
