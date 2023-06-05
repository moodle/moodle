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
 * Library of functions and constants for module trainingevent
 *
 * @package    mod
 * @subpackage trainingevent
 * @copyright  2013 onwards E-Learn Design Ltd.  {@link http://www.e-learndesign.co.uk}
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');

$sort         = optional_param('sort', 'firstname', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page?
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);   // Md5 confirmation hash.
$email  = optional_param('email', 0, PARAM_CLEAN);
$eventid = required_param('eventid', PARAM_INTEGER);

$params = array();

if ($sort) {
    $params['sort'] = $sort;
}
if ($dir) {
    $params['dir'] = $dir;
}
if ($page) {
    $params['page'] = $page;
}
if ($perpage) {
    $params['perpage'] = $perpage;
}
if ($search) {
    $params['search'] = $search;
}
if ($firstname) {
    $params['firstname'] = $firstname;
}
if ($lastname) {
    $params['lastname'] = $lastname;
}
if ($email) {
    $params['email'] = $email;
}
$params['deptid'] = $departmentid;
$params['eventid'] = $eventid;

if (!$event = $DB->get_record('trainingevent', array('id' => $eventid))) {
    print_error('invalid event ID');
}

if (!$cm = get_coursemodule_from_instance('trainingevent', $event->id, $event->course)) {
    print_error('invalid coursemodule ID');
}
// Page stuff.
$url = new moodle_url('/course/view.php', array('id' => $event->course));
$context = context_course::instance($event->course);
require_login($event->course); // Adds to $PAGE, creates $output.
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($event->name);
$PAGE->set_heading($SITE->fullname);
$baseurl  = new moodle_url('searchusers.php', array('eventid' => $eventid));

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

echo $output->header();

// Get the location information.
$location = $DB->get_record('classroom', array('id' => $event->classroomid));

// How many are already attending?
$attending = $DB->count_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0));

// Get the associated department id.
$company = new company($location->companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($company->id, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = key($userlevel);
}
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Set up the filter form..
$mform = new iomad_user_filter_form(null, array('companyid' => $company->id));
$mform->set_data(array('departmentid' => $departmentid, 'eventid' => $eventid));
$mform->set_data($params);
$mform->get_data();

// Display the tree selector thing.
echo $output->display_tree_selector($company, $parentlevel, $baseurl, $params, $departmentid);
echo html_writer::start_tag('div', array('class' => 'iomadclear', 'style' => 'padding-top: 5px;'));

// Display the user filter form.
$mform->display();

// Deal with the user optional profile search.
$fieldnames= array();
$allfields = array();
if ($category = $DB->get_record_sql("SELECT uic.id, uic.name FROM {user_info_category} uic, {company} c
                                     WHERE c.id = :companyid
                                     AND c.profileid=uic.id", array('companyid' => $location->companyid))) {
    // Get field names from company category.
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
        foreach ($fields as $field) {
            $allfields[$field->id] = $field;
            $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.$field->shortname, null, PARAM_ALPHANUMEXT);
        }
    }
}
if ($categories = $DB->get_records_sql("SELECT id FROM {user_info_category}
                                                WHERE id NOT IN (
                                                 SELECT profileid FROM {company})")) {
    foreach ($categories as $category) {
        if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
            foreach ($fields as $field) {
                $allfields[$field->id] = $field;
                $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
                require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                $newfield = 'profile_field_'.$field->datatype;
                ${'profile_field_'.$field->shortname} = optional_param('profile_field_'. $field->shortname,
                                                                       null,
                                                                       PARAM_ALPHANUMEXT);
            }
        }
    }
}

// Process this.
$idlist = array();
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        if (!empty($allfields[$id]->datatype) && $allfields[$id]->datatype == "menu" ) {
            $paramarray = explode("\n", $allfields[$id]->param1);
            ${$fieldname} = $paramarray[${$fieldname}];
        }
        if (!empty(${$fieldname}) ) {
            $idlist[0] = "We found no one";
            $fieldsql = $DB->sql_compare_text('data')."='".${$fieldname}."' AND fieldid = $id";
            if ($idfields = $DB->get_records_sql("SELECT userid from {user_info_data} WHERE $fieldsql")) {
                $fieldids[] = $idfields;
            }
        }
    }

    if (!empty($fieldids)) {
        $idlist = array_pop($fieldids);
        if (!empty($fieldids)) {
            foreach ($fieldids as $fieldid) {
                $idlist = array_intersect_key($idlist, $fieldid);
                if (empty($idlist)) {
                    break;
                }
            }
        }
    }
}


$returnurl = "view.php?eventid=$eventid";

// Carry on with the user listing.

$columns = array("firstname", "lastname", "email", "city", "country");

foreach ($columns as $column) {
    $string[$column] = get_string("$column");
}

// Get all or company users depending on capability.

// Check if has capability edit all users.
// Get department users.
$departmentusers = company::get_recursive_department_users($departmentid);
if ( count($departmentusers) > 0 ) {
    $departmentids = "";
    foreach ($departmentusers as $departmentuser) {
        if (!empty($departmentids)) {
            $departmentids .= ",".$departmentuser->userid;
        } else {
            $departmentids .= $departmentuser->userid;
        }
    }
    $sqlsearch = " id in ($departmentids) ";
} else {
    $sqlsearch = "1 = 0";
}

// Deal with search strings..
if (!empty($idlist)) {
    $sqlsearch .= "AND id in (".implode(',', array_keys($idlist)).") ";
}
if (!empty($params['firstname'])) {
    $sqlsearch .= " AND firstname like '%".$params['firstname']."%' ";
}

if (!empty($params['lastname'])) {
    $sqlsearch .= " AND lastname like '%".$params['lastname']."%' ";
}

if (!empty($params['email'])) {
    $sqlsearch .= " AND email like '%".$params['email']."%' ";
}
// Deal with users already assigned..
if ($assignedusers = $DB->get_records('trainingevent_users', array('trainingeventid' => $event->id, 'waitlisted' => 0), null, 'userid')) {
    $sqlsearch .= " AND id not in (".implode(',', array_keys($assignedusers)).") ";
}

// Strip out no course users.
$sqlsearch .= " AND id IN (SELECT u.id FROM {user} u
                           JOIN (SELECT DISTINCT eu2_u.id FROM {user} eu2_u
                                 JOIN {user_enrolments} eu2_ue ON eu2_ue.userid = eu2_u.id
                                 JOIN {enrol} eu2_e ON (eu2_e.id = eu2_ue.enrolid AND eu2_e.courseid = " . $event->course . ")
                                 WHERE eu2_u.deleted = 0
                                 AND eu2_ue.status = 0
                                 AND eu2_e.status = 0
                                 AND eu2_ue.timestart < " . time() . "
                                 AND (eu2_ue.timeend = 0 OR eu2_ue.timeend > " . time() . ")) e
                           ON e.id = u.id
                           LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = " . $event->course . ")
                           LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = " . $context->id ."))";

// Get the user records.
$userlist = "";
$userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch);
foreach ($userrecords as $userrecord) {
    if ( !empty($userlist)) {
        $userlist .= " OR id=$userrecord ";
    } else {
        $userlist .= " id=$userrecord ";
    }
}
if (!empty($userlist)) {
    $users = get_users_listing($sort, $dir, $page * $perpage, $perpage, '', '', '', $userlist);
} else {
    $users = array();
}
$usercount = count($userrecords);

echo $output->heading("$usercount ".get_string('users'));

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

$baseurl = new moodle_url('searchusers.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'eventid' => $eventid));
echo $output->paging_bar($usercount, $page, $perpage, $baseurl);

flush();


if (!$users) {
    $match = array();
    echo $output->heading(get_string('nousersfound'));

    $table = null;

} else {

    $countries = get_string_manager()->get_list_of_countries();
    if (empty($mnethosts)) {
        $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
    }

    foreach ($users as $key => $user) {
        if (!empty($user->country)) {
            $users[$key]->country = $countries[$user->country];
        }
    }
    if ($sort == "country") {  // Need to resort by full country name, not code.
        foreach ($users as $user) {
            $susers[$user->id] = $user->country;
        }
        asort($susers);
        foreach ($susers as $key => $value) {
            $nusers[] = $users[$key];
        }
        $users = $nusers;
    }

    $mainadmin = get_admin();

    $override = new stdclass();
    $override->firstname = 'firstname';
    $override->lastname = 'lastname';
    $fullnamelanguage = get_string('fullnamedisplay', '', $override);
    if (($CFG->fullnamedisplay == 'firstname lastname') or
        ($CFG->fullnamedisplay == 'firstname') or
        ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
        $fullnamedisplay = "$firstname / $lastname";
    } else {
        $fullnamedisplay = "$lastname / $firstname";
    }

    $table = new html_table();
    $table->head = array (get_string('fullname'), get_string('email'), get_string('city'), get_string('country'), "");
    $table->align = array ("left", "left", "left", "left", "center");
    $table->width = "95%";

    foreach ($users as $user) {
        if ($user->username == 'guest') {
            continue; // Do not dispaly dummy new user and guest here.
        }

        if (has_capability('mod/trainingevent:add', $context) && ($location->isvirtual || $attending < $location->capacity)) {
            $enrolmentbutton = $output->single_button(new moodle_url("/mod/trainingevent/view.php",
                                                                      array('id' => $cm->id,
                                                                            'chosenevent' => $event->id,
                                                                            'userid' => $user->id,
                                                                            'view' => 1,
                                                                            'action' => 'add')),
                                                                      get_string('bookuser',
                                                                      'trainingevent'));
        } else {
            $enrolmentbutton = "";
        }
        $fullname = fullname($user, true);

        $table->data[] = array ("$fullname",
                            "$user->email",
                            "$user->city",
                            "$user->country",
                            $enrolmentbutton);
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $output->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $output->footer();
