<?php
/**
 * Language strings for Moodle Groups (cvs:/group/) 
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

$string['groupmember'] = 'Group Member';
$string['groupmemberdesc'] = 'Standard role for a member of a group.';
$string['notingrouping'] = '[Not in a grouping]';
$string['anygrouping'] = '[Any grouping]';

$string['errorinvalidgroup'] = 'Error, invalid group $a';
$string['erroreditgrouping'] = 'Error creating/updating grouping $a';
$string['erroreditgroup'] = 'Error creating/updating group $a';
$string['erroraddremoveuser'] = 'Error adding/removing user $a to group';
$string['errorselectone'] = 'Please select a single group before choosing this option';
$string['errorselectsome'] = 'Please select one or more groups before choosing this option';

$string['groupings'] = 'Groupings';
$string['grouping'] = 'Grouping';
$string['groups'] = 'Groups';
$string['group'] = 'Group';
$string['groupsinselectedgrouping'] = 'Groups in:'; //'selected grouping'
$string['membersofselectedgroup'] = 'Members of:';
$string['overview'] = 'Overview';

$string['showgroupsingrouping'] = 'Show groups in grouping';
$string['showmembersforgroup'] = 'Show members for group';
$string['databaseupgradegroups'] = 'Groups version is now $a';

$string['deletegroupingconfirm'] = 'Are you sure you want to delete grouping \'$a\'? (Groups in the grouping are not deleted.)';
$string['deletegroupconfirm'] = 'Are you sure you want to delete group \'$a\'?';
$string['deletegroupsconfirm'] = 'Are you sure you want to delete the following groups?';

$string['editgroupingsettings'] = 'Edit grouping settings';
$string['deletegrouping'] = 'Delete grouping';
$string['creategrouping'] = 'Create grouping';
$string['createautomaticgrouping'] = 'Create automatic grouping';
$string['printerfriendly'] = 'Printer-friendly display';  //'of grouping'

$string['editgroupsettings'] = 'Edit group settings';
$string['deleteselectedgroup'] = 'Delete selected group';
$string['removegroupfromselectedgrouping'] = 'Remove group from grouping';  //'selected'
$string['creategroupinselectedgrouping'] = 'Create group in grouping';
$string['addgroupstogrouping'] = 'Add group to grouping'; //'groupS'

$string['removeselectedusers'] = 'Remove selected users';
$string['adduserstogroup'] = 'Add/remove users';  //'from group'
$string['addgroupstogroupings'] = 'Add/remove groups';  //'from group'

$string['groupingname'] = 'Grouping name';
$string['defaultgroupingname'] = 'Grouping';
$string['groupingdescription'] = 'Grouping description';
$string['creategrouping'] = 'Create grouping';
$string['creategroup'] = 'Create group';
$string['createorphangroup'] = 'Create orphan group';

$string['groupname'] = 'Group name';
$string['groupnameexists'] = 'The group name \'$a\' already exists in this course, please choose another one.';
$string['groupingnameexists'] = 'The grouping name \'$a\' already exists in this course, please choose another one.';
$string['defaultgroupname'] = 'Group';
$string['groupdescription'] = 'Group description';
$string['enrolmentkey'] = 'Enrolment key';
$string['hidepicture'] = 'Hide picture';
$string['newpicture'] = 'New picture';
$string['newgrouping'] = 'New grouping';

$string['backtogroups'] = 'Back to groups';
$string['backtogroupings'] = 'Back to groupings';
$string['existingmembers'] = 'Existing members: $a'; 
$string['potentialmembs'] = 'Potential members';
$string['potentialmembers'] = 'Potential members: $a';
$string['groupinfo'] = 'Info about selected group';
$string['groupinfomembers'] = 'Info about selected members';
$string['groupinfopeople'] = 'Info about selected people';
$string['groupmembers'] = 'Group members';
$string['groupmemberssee'] = 'See group members';
$string['groupmembersselected'] = 'Members of selected group';

$string['javascriptrequired'] = 'This page requires Javascript to be enabled.';

$string['defaultgrouping'] = 'Default grouping';
$string['groupmode'] = 'Group mode';
$string['groupmodeforce'] = 'Force group mode';
$string['groupmy'] = 'My group';
$string['groupnotamember'] = 'Sorry, you are not a member of that group';
$string['groupsnone'] = 'No groups';
$string['groupsseparate'] = 'Separate groups';
$string['groupsvisible'] = 'Visible groups';
$string['groupmembersonly'] = 'Available for group members only';
$string['groupmembersonlyerror'] = 'Sorry, you must be member of at least one group that is used in this activity.';
$string['grouptemplate'] = 'Group @';

$string['groupaddedsuccesfully'] = 'Group $a added succesfully';
$string['nopermissionforcreation'] = 'Can\'t create group \"$a\" as you dont have the required permissions';

$string['usergroupmembership'] = 'Selected user\'s membership:';
$string['filtergroups'] = 'Filter groups by: ';
$string['nogroups'] = 'There are no groups setup in this course yet';

$string['autocreategroups'] = 'Auto-create groups';
$string['selectfromrole'] = 'Select members from role';
$string['groupby'] = 'Specify';
$string['numgroups'] = 'Number of groups';
$string['nummembers'] = 'Members per group';
$string['nosmallgroups'] = 'Prevent last small group';

$string['groupscount'] = 'Groups ($a)';
$string['usercounttotal'] = 'User count ($a)';
$string['usercount'] = 'User count';

$string['members'] = 'Members per group';
$string['number'] = 'Group/member count';
$string['allocateby'] = 'Allocate members';
$string['noallocation'] = 'No allocation';
$string['random'] = 'Randomly';
$string['byfirstname'] = 'Alphabetically by first name, last name';
$string['bylastname'] = 'Alphabetically by last name, first name';
$string['byidnumber'] = 'Alphabetically by ID number';
$string['createingrouping'] = 'Create in grouping';

$string['namingscheme'] = 'Naming scheme';
$string['namingschemehelp'] = 'Use @ character to represent the group letter (A-Z) or # to represent the group number.';
$string['toomanygroups'] = 'Insufficient users to populate this number of groups - there are only $a users in the selected role.';
$string['badnamingscheme'] = 'Must contain exactly one \'@\' or one \'#\'  character';
$string['groupspreview'] = 'Groups preview';
$string['nousersinrole'] = 'There are no suitable users in the selected role';
$string['nogroupsassigned'] = 'No groups assigned';
$string['evenallocation'] = 'Note: To keep group allocation even, the actual number of members per group differs from the number you specified.';

$string['removegroupsmembers'] = 'Remove all group members';
$string['removegroupingsmembers'] = 'Remove all groups from groupings';
$string['deleteallgroups'] = 'Delete all groups';
$string['deleteallgroupings'] = 'Delete all groupings';

$string['groupsgroupings'] = 'Groups &amp; groupings';
$string['groupingsonly'] = 'Groupings only';
$string['groupsonly'] = 'Groups only';

?>