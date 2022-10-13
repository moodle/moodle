<?php

/**
 * Version file for component 'local_gws_query_practice'
 *
 * @package    local_gws_query_practice
 * @copyright  2019 onwards GWS
 * @developer  Brian kremer (greatwallstudio.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//Includes
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $DB;

//Set page object
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/local/gws_query_practice/index.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
require_login();
$context = context_system::instance();

//Bread crumb trail
$previewnode = $PAGE->navigation->add(
    get_string('pluginname', 'local_gws_query_practice'), new moodle_url('index.php'), navigation_node::TYPE_CONTAINER
);
$previewnode->make_active();
require_capability('local/gws_query_practice:accessquerypractice', $context);

//Navigation and header 
$strpluginname = $SITE->fullname . ' query practice - index.php';
$PAGE->set_title($strpluginname);
$PAGE->set_heading($strpluginname);
echo $OUTPUT->header();
echo html_writer::nonempty_tag('h3', 'GWS query practice');

echo html_writer::nonempty_tag(
    'p', 'Page to practice writing queries... find moodle/local/gws_query_practice/index.php, and edit it! '
);

/*
// ********** ********** SECTION 1 ********** ********** 

//example of get_records_sql
echo '<hr><p>Example of get_records_sql() function</p>';

//put query in a variable
$sql = "SELECT * FROM mdl_role WHERE 1";

//display the query
echo '<p>Query: <b>' . $sql . '</b></p>';

//send the query to the get_records_sql() function
$results = $DB->get_records_sql($sql);

//display the results of the function call
echo 'Query results: ';
print_r($results);
*/

/*
echo html_writer::nonempty_tag('h4', 'Detective case #1: find out what the role is assigned to Albohtori');
$result = $DB->get_record_select('user', 'username = ?', array('albohtori@gmail.com'));
echo 'Albohtori id is ';
echo $result->id;
echo '<br>';

$assignmentresult = $DB->get_record_select('role_assignments', 'userid = ?', array($result->id));
echo 'Role is equals ';
echo $assignmentresult->roleid;
echo '<br>';
$roleresult = $DB->get_record_select('role', 'id = ?', array($assignmentresult->roleid));
echo 'Role short name is: ';
echo $roleresult->shortname;
echo '<br>';

echo html_writer::nonempty_tag('h4', 'Detective case #2: find all users assigned to coursecreator role.');
$getrole = $DB->get_record_select('role', 'shortname = ?', array('coursecreator'));
echo 'The id of coursecreator is ';
echo $getrole->id;
echo '<br>';

$getrole = $DB->get_record_select('role', 'shortname = ?', array('coursecreator'));
echo 'The id of coursecreator is ';
echo $getrole->id;
echo '<br>';

$getmembers = $DB->get_records_select('role_assignments', 'roleid = ?', array($getrole->id));
echo 'The id of coursecreator is ';
print_r($getmembers);
echo '<br>';

foreach ($getmembers as $getmember) {
    $getuserinfo = $DB->get_record_select('user', 'id = ?', array($getmember->userid));
    echo 'Member name: ';
    echo $getuserinfo->firstname . ' ' . $getuserinfo->lastname;
    echo '<br>';

}
*/

/*
echo html_writer::nonempty_tag('h4', 'Detective case #1: Show all capabilities for student role.');
$getrole = $DB->get_record_select('role', 'shortname = ?', array('student'));

echo '<b>The id for the student role is </b>';
echo $getrole->id;
echo '<br>';

$getcapabilities = $DB->get_records_select('role_capabilities', 'roleid = ?', array($getrole->id));

foreach ($getcapabilities as $showcapabilities) {

    echo '<b>Context ID: </b>';
    echo $showcapabilities->contextid;
    echo ': ';

    echo '<b>Capability: </b>';
    echo $showcapabilities->capability;
    echo ' - ';

    echo '<b>Time Modified: </b>';
    echo date('r', $showcapabilities->timemodified);
    echo '<br>';

}

echo html_writer::nonempty_tag('h4', 'Detective case #2: Show all capabilities for Albohtori');

$getuser = $DB->get_record_select('user', 'username = ?', array('albohtori@gmail.com'));

echo '<b>Albohtori user record id is </b>';
echo $getuser->id;
echo '<br>';

$getusersrole = $DB->get_record_select('role_assignments', 'userid = ?', array($getuser->id));

echo '<b>Albohtori is assigned to the role with this id: </b>';
echo $getusersrole->roleid;
echo '<br>';

$getrolename = $DB->get_record_select('role', 'id = ?', array($getusersrole->roleid));

echo '<b>The name of Albohtori role is: </b>';
echo $getrolename->shortname;
echo '<br>';

$getuserscapabilities = $DB->get_records_select('role_capabilities', 'roleid = ?', array($getusersrole->roleid));

foreach ($getuserscapabilities as $showuserscapabilities) {

    echo '<b>Context ID: </b>';
    echo $showuserscapabilities->contextid;
    echo ': ';

    echo '<b>Capability: </b>';
    echo $showuserscapabilities->capability;
    echo ' - ';

    echo '<b>Time Modified: </b>';
    echo date('d-m-Y H:i', $showuserscapabilities->timemodified);
    echo '<br>';

}
*/

/*

echo html_writer::nonempty_tag('h4', 'Detective case #1: Show the context types where the manager role may be assigned.');

$getmanagerroleid = $DB->get_field_select('role', 'id', 'shortname = ?', array('manager'));

echo 'The id for the manager role is: ';
echo $getmanagerroleid;
echo '<br>';
echo '<p>These are the context levels where you can assign the manager role:</p>';

$getcontextlevels = $DB->get_records_select('role_context_levels', 'roleid = ?', array($getmanagerroleid));

if ( ! empty($getcontextlevels)) {

    foreach ($getcontextlevels as $showcontextlevels) {

        echo $showcontextlevels->contextlevel;
        echo '<br>';

    }
}
echo html_writer::nonempty_tag(
    'h4',
    'Detective case #2: List all roles. If someone is assigned to the role, then show their name and show the context levels for the assignment.'
);

$getroles = $DB->get_records_select('role', '1 = ?', array(1));

foreach ($getroles as $showroles) {

    echo '<b>';
    echo $showroles->shortname;
    echo '</b><br>';

    $getrolemembership = $DB->get_records_select('role_assignments', 'roleid = ?', array($showroles->id));

    if ( ! empty($getrolemembership)) {

        foreach ($getrolemembership as $showrolemembership) {

            $getmemberinfo = $DB->get_record_select('user', 'id = ?', array($showrolemembership->userid));

            echo $getmemberinfo->username;
            echo ' was assigned at a context level of ';

            $getcontextlevel = $DB->get_record_select('context', 'id = ?', array($showrolemembership->contextid));

            echo $getcontextlevel->contextlevel;
            echo '<br>';

        }

    } else {

        echo 'No users are assigned to this role.';
        echo '<br>';

    }

    echo '<br>';
}
*/

/*

$getallusers = $DB->get_records_select('user', 'id != ?', array(0));

foreach ($getallusers as $showallusers) {

    echo '<b>' . $showallusers->username . ' </b>';

    if (has_capability('local/gws_query_practice:accessquerypractice', $context, $showallusers->id)) {

        echo ' has the local/gws_query_practice:accessquerypractice capability.';

    } else {

        echo ' does not have the local/gws_query_practice:accessquerypractice capability.';

    }

    echo '<br>';

}
*/

/*
$getuser = $DB->get_record_select('user', 'username = ?', array('joe'));
echo $getuser->firstname . ' ' . $getuser->lastname . ' ';
$getroleassignments = $DB->get_records_select('role_assignments', 'userid = ?', array($getuser->id));
foreach ($getroleassignments as $showroleassignments) {
    $getassignusername = $DB->get_record_select('role', 'id = ?', array($showroleassignments->roleid));
    echo 'is assigned to the ' . $getassignusername->shortname . ' role. <br>';
    $countassignmentperms = $DB->count_records_select('role_allow_assign', "roleid = {$getassignusername->id}");
    echo $getuser->firstname . ' can assign ' . $countassignmentperms . ' roles: <br>';
    echo $getuser->firstname . ' can assign these roles: <br>';
    $getassignmentperms = $DB->get_records_select('role_allow_assign', 'roleid = ?', array($showroleassignments->roleid));
    foreach ($getassignmentperms as $showassignmentperms) {
        $getassignmentrolenames = $DB->get_record_select('role', 'id = ?', array($showassignmentperms->allowassign));
        echo $getassignmentrolenames->shortname . '<br>';
    }
    echo '<br>';
    $countoverrideperms = $DB->count_records_select('role_allow_override', "roleid = {$getassignusername->id}");
    echo $getuser->firstname . ' can override ' . $countoverrideperms . ' roles: <br>';
    $getoverrideperms = $DB->get_records_select('role_allow_override', 'roleid = ?', array($showroleassignments->roleid));
    foreach ($getoverrideperms as $showoverrideperms) {
        $getoverriderolenames = $DB->get_record_select('role', 'id = ?', array($showoverrideperms->allowoverride));
        echo $getoverriderolenames->shortname . '<br>';
    }
    echo '<br>';
    $countswitchperms = $DB->count_records_select('role_allow_switch', "roleid = {$getassignusername->id}");
    echo $getuser->firstname . ' can switch ' . $countswitchperms . ' roles: <br>';
    $getswitchperms = $DB->get_records_select('role_allow_switch', 'roleid = ?', array($showroleassignments->roleid));
    foreach ($getswitchperms as $showswitchperms) {
        $getswitchrolenames = $DB->get_record_select('role', 'id = ?', array($showswitchperms->allowswitch));
        echo $getswitchrolenames->shortname . '<br>';
    }
    echo '<br>';
    $countviewperms = $DB->count_records_select('role_allow_view', "roleid = {$getassignusername->id}");
    echo $getuser->firstname . ' can view ' . $countviewperms . ' roles: <br>';
    $getviewperms = $DB->get_records_select('role_allow_view', 'roleid = ?', array($showroleassignments->roleid));
    foreach ($getviewperms as $showviewperms) {
        $getviewrolenames = $DB->get_record_select('role', 'id = ?', array($showviewperms->allowview));
        echo $getviewrolenames->shortname . '<br>';
    }
    echo '<br>';
}
*/

//Show all role assignments: who made the assignment, what role they assigned, who they assigned the role to, and the date

$getrolelogrecords = $DB->get_recordset_select('logstore_standard_log', 'target = ?', array('role'));
print_r($getrolelogrecords) . '<br>';
foreach ($getrolelogrecords as $showrolelogrecords) {

    $getassignor = $DB->get_record_select('user', 'id = ?', array($showrolelogrecords->userid));
    $getassignee = $DB->get_record_select('user', 'id = ?', array($showrolelogrecords->relateduserid));
    $getrole     = $DB->get_record_select('role', 'id = ?', array($showrolelogrecords->objectid));

    echo 'On ' . date('m-d-Y', $showrolelogrecords->timecreated) . ', ' . $getassignor->username . ' assigned the role of '
        . $getrole->shortname . ' to ' . $getassignee->username . '.<br>';
}

$getrolelogrecords->close();
/*
// ********** ********** SECTION 2 ********** **********  

//Query the role table
$roletablerecords = $DB->get_records_select('role', 'id != ?', array('0'), 'id');

echo '<p></p>';

//Make a heading 
echo '<b>id     -     shortname </b><br>';

//Loop through the query results
foreach ($roletablerecords as $showroletablerecords) {

    //Show role id and shortname
    echo $showroletablerecords->id . '     -     ' . $showroletablerecords->shortname . '<br>';
}

// ********** ********** ********** ********** ********** 
*/
//Show page footer
echo $OUTPUT->footer();