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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once('lib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);   // Md5 confirmation hash.
$email  = optional_param('email', 0, PARAM_CLEAN);

$params = array();

if ($delete) {
    $params['delete'] = $delete;
}
if ($confirm) {
    $params['confirm'] = $confirm;
}
if ($confirmuser) {
    $params['confirmuser'] = $confirmuser;
}
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
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}

$systemcontext = context_system::instance();

require_login();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('edit_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Print the page header.
$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block', 'company_edit_users_title');
$blockpage->setup();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

$blockpage->display_header();

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

if (has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = company::get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}
if ($departmentid == 0) {
    $departmentid = $userhierarchylevel;
}

// Get the appropriate list of departments.
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($baseurl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin');
$select->formid = 'choosedepartment';
echo html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_department_selector'));
$fwselectoutput = html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_company_selector'));
if (!(has_capability('block/iomad_company_admin:editusers', $systemcontext)
    or has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
    print_error('nopermissions', 'error', '', 'edit/delete users');
}
// Set up the filter form.
$mform = new iomad_user_filter_form(null, array('companyid' => $companyid));
$mform->set_data(array('departmentid' => $departmentid));
$mform->set_data($params);

// Display the user filter form.
$mform->display();

// Get the company additional optional user parameter names.
$fieldnames = array();
$foundfields = false;

if ($category = $DB->get_record_sql('select uic.id, uic.name from {user_info_category} uic, {company} c where c.id = '.$companyid.'
                                     and c.profileid=uic.id')) {
    // Get field names from company category.
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
        foreach ($fields as $field) {
            $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
            ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.$field->shortname, null, PARAM_RAW);
        }
    }
}

// Deal with the user optional profile search.
$idlist = array();
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        if ($fields[$id]->datatype == "menu") {
            $paramarray = explode("\n", $fields[$id]->param1);
            if (!empty($paramarray[${$fieldname}])) {
                ${$fieldname} = $paramarray[${$fieldname}];
            }
        }
        if (!empty(${$fieldname})) {
            $idlist[0] = "We found no one";
            $fieldsql = $DB->sql_compare_text('data')." LIKE '%".${$fieldname}."%' AND fieldid = $id";
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

$stredit   = get_string('edit');
$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');
$strshowallusers = get_string('showallusers');
$strenrolment = get_string('userenrolments', 'block_iomad_company_admin');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$returnurl = "$CFG->wwwroot/blocks/iomad_company_admin/editusers.php";

if ($confirmuser and confirm_sesskey()) {
    if (!$user = $DB->get_record('user', array('id' => $confirmuser))) {
        print_error('nousers');
    }

    $auth = get_auth_plugin($user->auth);

    $result = $auth->user_confirm($user->username, $user->secret);

    if ($result == AUTH_CONFIRM_OK or $result == AUTH_CONFIRM_ALREADY) {
        redirect($returnurl);
    } else {
        redirect($returnurl, get_string('usernotconfirmed', '', fullname($user, true)));
    }

} else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation.

    if (!has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
        print_error('nopermissions', 'error', '', 'delete a user');
    }

    if (!$user = $DB->get_record('user', array('id' => $delete))) {
        print_error('nousers', 'error');
    }

    if (is_primary_admin($user->id)) {
        print_error('nopermissions', 'error', '', 'delete the primary admin user');
    }

    if ($confirm != md5($delete)) {
        $fullname = fullname($user, true);
        echo $OUTPUT->heading(get_string('deleteuser', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('deletecheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editusers.php', $optionsyes), 'editusers.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Actually delete the user.
        if (!$DB->delete_records('user', array('id' => $delete))) {
            print_error('error while deleting user');
        }
        // Remove them from the department lists.
        $DB->delete_records('company_users', array('userid' => $delete));
    }

} else if ($acl and confirm_sesskey()) {
    if (!has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
        // TODO: this should be under a separate capability.
        print_error('nopermissions', 'error', '', 'modify the NMET access control list');
    }
    if (!$user = $DB->get_record('user', array('id' => $acl))) {
        print_error('nousers', 'error');
    }
    if (!is_mnet_remote_user($user)) {
        print_error('usermustbemnet', 'error');
    }
    $accessctrl = strtolower(required_param('accessctrl', PARAM_ALPHA));
    if ($accessctrl != 'allow' and $accessctrl != 'deny') {
        print_error('invalidaccessparameter', 'error');
    }
    $aclrecord = $DB->get_record('mnet_sso_access_control', array('username' => $user->username, 'mnet_host_id'
                                  => $user->mnethostid));
    if (empty($aclrecord)) {
        $aclrecord = new object();
        $aclrecord->mnet_host_id = $user->mnethostid;
        $aclrecord->username = $user->username;
        $aclrecord->accessctrl = $accessctrl;
        $DB->insert_record('mnet_sso_access_control', $aclrecord);
    } else {
        $aclrecord->accessctrl = $accessctrl;
        $DB->update_record('mnet_sso_access_control', $aclrecord);
    }
    $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id, wwwroot, name');
    redirect($returnurl);
}

// Carry on with the user listing.

$columns = array("firstname", "lastname", "email", "department", "lastaccess");

foreach ($columns as $column) {
    $string[$column] = get_string("$column");
    if ($sort != $column) {
        $columnicon = "";
        if ($column == "lastaccess") {
            $columndir = "DESC";
        } else {
            $columndir = "ASC";
        }
    } else {
        $columndir = $dir == "ASC" ? "DESC":"ASC";
        if ($column == "lastaccess") {
            $columnicon = $dir == "ASC" ? "up":"down";
        } else {
            $columnicon = $dir == "ASC" ? "down":"up";
        }
        $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $params['sort'] = $column;
    $params['dir'] = $columndir;
    $$column = "<a href= ". new moodle_url('editusers.php', $params).">".$string[$column]."</a>$columnicon";
}

if ($sort == "name") {
    $sort = "firstname";
}

// Get all or company users depending on capability.

//  Check if has capability edit all users.
if (has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    // Make sure we dont display site admins.
    // Set default search to something which cant happen.
    $sqlsearch = "userid!='-1'";
    $siteadmins = explode(" ", $CFG->siteadmins);
    foreach ($siteadmins as $siteadmin) {
        $sqlsearch .= " AND userid!='$siteadmin'";
    }

    // Get department users.
    $departmentusers = company::get_recursive_department_users($departmentid);
    if (count($departmentusers) > 0) {
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

    // Deal with search strings.
    $searchparams = array();
    if (!empty($idlist)) {
        $sqlsearch .= " AND id in (".implode(',', array_keys($idlist)).") ";
    }
    if (!empty($params['firstname'])) {
        $sqlsearch .= " AND firstname like :firstname ";
        $searchparams['firstname'] = '%'.$params['firstname'].'%';
    }

    if (!empty($params['lastname'])) {
        $sqlsearch .= " AND lastname like :lastname ";
        $searchparams['lastname'] = '%'.$params['lastname'].'%';
    }

    if (!empty($params['email'])) {
        $sqlsearch .= " AND email like :email ";
        $searchparams['email'] = '%'.$params['email'].'%';
    }

    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);

} else if (has_capability('block/iomad_company_admin:editusers', $systemcontext)) {   // Check if has role edit company users.

    // Get users company association.
    $departmentusers = company::get_recursive_department_users($departmentid);
    if (count($departmentusers) > 0) {
        $departmentids = "";
        foreach ($departmentusers as $departmentuser) {
            if (!empty($departmentids)) {
                $departmentids .= ",".$departmentuser->userid;
            } else {
                $departmentids .= $departmentuser->userid;
            }
        }
        $sqlsearch = " deleted <> 1 AND id in ($departmentids) ";
    } else {
        $sqlsearch = "1 = 0";
    }
    // Deal with search strings.
    $searchparams = array();
    if (!empty($idlist)) {
        $sqlsearch .= " AND id in (".implode(',', array_keys($idlist)).") ";
    }
    if (!empty($params['firstname'])) {
        $sqlsearch .= " AND firstname like :firstname ";
        $searchparams['firstname'] = '%'.$params['firstname'].'%';
    }

    if (!empty($params['lastname'])) {
        $sqlsearch .= " AND lastname like :lastname ";
        $searchparams['lastname'] = '%'.$params['lastname'].'%';
    }

    if (!empty($params['email'])) {
        $sqlsearch .= " AND email like :email ";
        $searchparams['email'] = '%'.$params['email'].'%';
    }

    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);
}
$userlist = "";

if (!empty($userrecords)) {
    $userlist = "u.id in (". implode(',', array_values($userrecords)).")";
} else {
    $userlist = "1=2";
}
if (!empty($userlist)) {
    $users = iomad_get_users_listing($sort, $dir, $page * $perpage, $perpage, '', '', '', $userlist);
} else {
    $users = array();
}
$usercount = count($userrecords);

echo $OUTPUT->heading("$usercount ".get_string('users'));

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

$baseurl = new moodle_url('editusers.php', $params);
echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

flush();


if (!$users) {
    $match = array();
    echo $OUTPUT->heading(get_string('nousersfound'));

    $table = null;

} else {

    $mainadmin = get_admin();

    $override = new object();
    $override->firstname = 'firstname';
    $override->lastname = 'lastname';
    $fullnamelanguage = get_string('fullnamedisplay', '', $override);
    if (($CFG->fullnamedisplay == 'firstname lastname') or
        ($CFG->fullnamedisplay == 'firstname') or
        ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname')) {
        $fullnamedisplay = "$firstname / $lastname";
    } else {
        $fullnamedisplay = "$lastname / $firstname";
    }

    $table = new html_table();
    $table->head = array ($fullnamedisplay, $email, $department, $lastaccess, "", "", "");
    $table->align = array ("left", "left", "left", "left", "center", "center", "center");
    $table->width = "95%";
    foreach ($users as $user) {
        if ($user->username == 'guest') {
            continue; // Do not dispaly dummy new user and guest here.
        }
        if ($user->id == $USER->id) {
            $deletebutton = "";
        } else {
            if ((has_capability('block/iomad_company_admin:editusers', $systemcontext)
                 or has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
                $deletebutton = "<a href=\"editusers.php?delete=$user->id&amp;sesskey=".sesskey()."\">$strdelete</a>";
            } else {
                $deletebutton = "";
            }
        }
        if ((has_capability('block/iomad_company_admin:editusers', $systemcontext)
             or has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($user->id == $USER->id or $user->id != $mainadmin->id) and !is_mnet_remote_user($user)) {
            $editbutton = "<a href=\"$securewwwroot/blocks/iomad_company_admin/editadvanced.php?id=$user->id\">$stredit</a>";
        } else {
            $editbutton = "";
        }

        if ((has_capability('block/iomad_company_admin:company_course_users', $systemcontext)
             or has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($user->id == $USER->id or $user->id != $mainadmin->id)
             and !is_mnet_remote_user($user)) {
            $enrolmentbutton = "<a href=\"company_users_course_form.php?userid=$user->id\">$strenrolment</a>";
        } else {
            $enrolmentbutton = "";
        }

        if ($user->lastaccess) {
            $strlastaccess = format_time(time() - $user->lastaccess);
        } else {
            $strlastaccess = get_string('never');
        }
        $fullname = fullname($user, true);

        // Get the users department.
        $userdepartment = $DB->get_record_sql("SELECT d.name
                                               FROM {department} d, {company_users} du
                                               WHERE du.userid = " . $user->id ."
                                               AND d.id = du.departmentid");
        $user->department = $userdepartment->name;

        $table->data[] = array ("$fullname",
                            "$user->email",
                            "$user->department",
                            $strlastaccess,
                            $editbutton,
                            $deletebutton,
                            $enrolmentbutton);
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();

function iomad_get_users_listing($sort='lastaccess', $dir='ASC', $page=0, $recordsperpage=0,
                       $search='', $firstinitial='', $lastinitial='', $extraselect='', array $extraparams =null) {
    global $DB;

    $fullname  = $DB->sql_fullname();

    $select = "u.deleted <> 1";
    $params = array();

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND (". $DB->sql_like("u.$fullname", ':search1', false, false).
                   " OR ". $DB->sql_like('u.email', ':search2', false, false).
                   " OR u.username = :search3)";
        $params['search1'] = "%$search%";
        $params['search2'] = "%$search%";
        $params['search3'] = "$search";
    }

    if ($firstinitial) {
        $select .= " AND ". $DB->sql_like('u.firstname', ':fni', false, false);
        $params['fni'] = "$firstinitial%";
    }
    if ($lastinitial) {
        $select .= " AND ". $DB->sql_like('u.lastname', ':lni', false, false);
        $params['lni'] = "$lastinitial%";
    }

    if ($extraselect) {
        $select .= " AND $extraselect";
        $params = $params + (array)$extraparams;
    }

    if ($sort) {
        if ($sort == "department") {
            $sort = " ORDER by d.name $dir";
        } else {
            $sort = " ORDER BY u.$sort $dir";
        }
    }

    // Warning: will return UNCONFIRMED USERS!
    return $DB->get_records_sql("SELECT u.*, d.name
                                 FROM {user} u, {department} d, {company_users} cu
                                 WHERE $select and cu.userid = u.id and d.id = cu.departmentid
                                 $sort", $params, $page, $recordsperpage);

}

