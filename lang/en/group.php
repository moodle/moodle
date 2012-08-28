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
 * Strings for component 'group', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   group
 * @copyright 2006 The Open University
 * @author    J.White AT open.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addedby'] = 'Added by {$a}';
$string['addgroup'] = 'Add user into group';
$string['addgroupstogrouping'] = 'Add group to grouping';
$string['addgroupstogroupings'] = 'Add/remove groups';
$string['adduserstogroup'] = 'Add/remove users';
$string['allocateby'] = 'Allocate members';
$string['anygrouping'] = '[Any grouping]';
$string['autocreategroups'] = 'Auto-create groups';
$string['backtogroupings'] = 'Back to groupings';
$string['backtogroups'] = 'Back to groups';
$string['badnamingscheme'] = 'Must contain exactly one \'@\' or one \'#\'  character';
$string['byfirstname'] = 'Alphabetically by first name, last name';
$string['byidnumber'] = 'Alphabetically by ID number';
$string['bylastname'] = 'Alphabetically by last name, first name';
$string['createautomaticgrouping'] = 'Create automatic grouping';
$string['creategroup'] = 'Create group';
$string['creategrouping'] = 'Create grouping';
$string['creategroupinselectedgrouping'] = 'Create group in grouping';
$string['createingrouping'] = 'Create in grouping';
$string['createorphangroup'] = 'Create orphan group';
$string['databaseupgradegroups'] = 'Groups version is now {$a}';
$string['defaultgrouping'] = 'Default grouping';
$string['defaultgroupingname'] = 'Grouping';
$string['defaultgroupname'] = 'Group';
$string['deleteallgroupings'] = 'Delete all groupings';
$string['deleteallgroups'] = 'Delete all groups';
$string['deletegroupconfirm'] = 'Are you sure you want to delete group \'{$a}\'?';
$string['deletegrouping'] = 'Delete grouping';
$string['deletegroupingconfirm'] = 'Are you sure you want to delete grouping \'{$a}\'? (Groups in the grouping are not deleted.)';
$string['deletegroupsconfirm'] = 'Are you sure you want to delete the following groups?';
$string['deleteselectedgroup'] = 'Delete selected group';
$string['editgroupingsettings'] = 'Edit grouping settings';
$string['editgroupsettings'] = 'Edit group settings';
$string['enrolmentkey'] = 'Enrolment key';
$string['enrolmentkey_help'] = 'An enrolment key enables access to the course to be restricted to only those who know the key. If a group enrolment key is specified, then not only will entering that key let the user into the course, but it will also automatically make them a member of this group.';
$string['erroraddremoveuser'] = 'Error adding/removing user {$a} to group';
$string['erroreditgroup'] = 'Error creating/updating group {$a}';
$string['erroreditgrouping'] = 'Error creating/updating grouping {$a}';
$string['errorinvalidgroup'] = 'Error, invalid group {$a}';
$string['errorremovenotpermitted'] = 'You do not have permission to remove automatically-added group member {$a}';
$string['errorselectone'] = 'Please select a single group before choosing this option';
$string['errorselectsome'] = 'Please select one or more groups before choosing this option';
$string['evenallocation'] = 'Note: To keep group allocation even, the actual number of members per group differs from the number you specified.';
$string['existingmembers'] = 'Existing members: {$a}';
$string['filtergroups'] = 'Filter groups by:';
$string['group'] = 'Group';
$string['groupaddedsuccesfully'] = 'Group {$a} added successfully';
$string['groupby'] = 'Specify';
$string['groupdescription'] = 'Group description';
$string['groupinfo'] = 'Info about selected group';
$string['groupinfomembers'] = 'Info about selected members';
$string['groupinfopeople'] = 'Info about selected people';
$string['grouping'] = 'Grouping';
$string['grouping_help'] = 'A grouping is a collection of groups within a course. If a grouping is selected, students assigned to groups within the grouping will be able to work together.';
$string['groupingsection'] = 'Grouping access';
$string['groupingsection_help'] = 'A grouping is a collection of groups within a course. If a grouping is selected here, only students assigned to groups within this grouping will have access to the section.';
$string['groupingdescription'] = 'Grouping description';
$string['groupingname'] = 'Grouping name';
$string['groupingnameexists'] = 'The grouping name \'{$a}\' already exists in this course, please choose another one.';
$string['groupings'] = 'Groupings';
$string['groupingsonly'] = 'Groupings only';
$string['groupmember'] = 'Group member';
$string['groupmemberdesc'] = 'Standard role for a member of a group.';
$string['groupmembers'] = 'Group members';
$string['groupmembersonly'] = 'Available for group members only';
$string['groupmembersonly_help'] = 'If this checkbox is ticked, the activity (or resource) will only be available to students assigned to groups within the selected grouping.';
$string['groupmembersonlyerror'] = 'Sorry, you must be member of at least one group that is used in this activity.';
$string['groupmemberssee'] = 'See group members';
$string['groupmembersselected'] = 'Members of selected group';
$string['groupmode'] = 'Group mode';
$string['groupmode_help'] = 'This setting has 3 options:

* No groups - There are no sub groups, everyone is part of one big community
* Separate groups - Each group member can only see their own group, others are invisible
* Visible groups - Each group member works in their own group, but can also see other groups

The group mode defined at course level is the default mode for all activities within the course. Each activity that supports groups can also define its own group mode, though if the group mode is forced at course level, the group mode setting for each activity is ignored.';
$string['groupmodeforce'] = 'Force group mode';
$string['groupmodeforce_help'] = 'If group mode is forced, then the course group mode is applied to every activity in the course. Group mode settings in each activity are then ignored.';
$string['groupmy'] = 'My group';
$string['groupname'] = 'Group name';
$string['groupnameexists'] = 'The group name \'{$a}\' already exists in this course, please choose another one.';
$string['groupnotamember'] = 'Sorry, you are not a member of that group';
$string['groups'] = 'Groups';
$string['groupscount'] = 'Groups ({$a})';
$string['groupsgroupings'] = 'Groups &amp; groupings';
$string['groupsinselectedgrouping'] = 'Groups in:';
$string['groupsnone'] = 'No groups';
$string['groupsonly'] = 'Groups only';
$string['groupspreview'] = 'Groups preview';
$string['groupsseparate'] = 'Separate groups';
$string['groupsvisible'] = 'Visible groups';
$string['grouptemplate'] = 'Group @';
$string['hidepicture'] = 'Hide picture';
$string['importgroups'] = 'Import groups';
$string['importgroups_help'] = 'Groups may be imported via text file. The format of the file should be as follows:

* Each line of the file contains one record
* Each record is a series of data separated by commas
* The first record contains a list of fieldnames defining the format of the rest of the file
* Required fieldname is groupname
* Optional fieldnames are description, enrolmentkey, picture, hidepicture';
$string['importgroups_link'] = 'group/import';
$string['javascriptrequired'] = 'This page requires JavaScript to be enabled.';
$string['members'] = 'Members per group';
$string['membersofselectedgroup'] = 'Members of:';
$string['namingscheme'] = 'Naming scheme';
$string['namingscheme_help'] = 'The at symbol (@) may be used to create groups with names containing letters. For example Group @ will generate groups named Group A, Group B, Group C, ...

The hash symbol (#) may be used to create groups with names containing numbers. For example Group # will generate groups named Group 1, Group 2, Group 3, ...';
$string['newgrouping'] = 'New grouping';
$string['newpicture'] = 'New picture';
$string['newpicture_help'] = 'Select an image in JPG or PNG format. The image will be cropped to a square and resized to 100x100 pixels.';
$string['noallocation'] = 'No allocation';
$string['nogroups'] = 'There are no groups set up in this course yet';
$string['nogroupsassigned'] = 'No groups assigned';
$string['nopermissionforcreation'] = 'Can\'t create group "{$a}" as you don\'t have the required permissions';
$string['nosmallgroups'] = 'Prevent last small group';
$string['notingrouping'] = '[Not in a grouping]';
$string['nousersinrole'] = 'There are no suitable users in the selected role';
$string['number'] = 'Group/member count';
$string['numgroups'] = 'Number of groups';
$string['nummembers'] = 'Members per group';
$string['overview'] = 'Overview';
$string['potentialmembers'] = 'Potential members: {$a}';
$string['potentialmembs'] = 'Potential members';
$string['printerfriendly'] = 'Printer-friendly display';
$string['random'] = 'Randomly';
$string['removegroupfromselectedgrouping'] = 'Remove group from grouping';
$string['removefromgroup'] = 'Remove user from group {$a}';
$string['removefromgroupconfirm'] = 'Do you really want to remove user "{$a->user}" from group "{$a->group}"?';
$string['removegroupingsmembers'] = 'Remove all groups from groupings';
$string['removegroupsmembers'] = 'Remove all group members';
$string['removeselectedusers'] = 'Remove selected users';
$string['selectfromrole'] = 'Select members from role';
$string['showgroupsingrouping'] = 'Show groups in grouping';
$string['showmembersforgroup'] = 'Show members for group';
$string['toomanygroups'] = 'Insufficient users to populate this number of groups - there are only {$a} users in the selected role.';
$string['usercount'] = 'User count';
$string['usercounttotal'] = 'User count ({$a})';
$string['usergroupmembership'] = 'Selected user\'s membership:';
