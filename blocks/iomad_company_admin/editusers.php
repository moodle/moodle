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
$password      = optional_param('password', 0, PARAM_INT);
$suspend      = optional_param('suspend', 0, PARAM_INT);
$unsuspend      = optional_param('unsuspend', 0, PARAM_INT);
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
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
$showall = optional_param('showall', false, PARAM_BOOL);
$usertype = optional_param('usertype', 'a', PARAM_ALPHANUM);

$params = array();

if ($delete) {
    $params['delete'] = $delete;
}
if ($suspend) {
    $params['suspend'] = $suspend;
}
if ($unsuspend) {
    $params['suspend'] = $unsuspend;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
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
$params['usertype'] = $usertype;

$systemcontext = context_system::instance();

require_login();

if (!iomad::has_capability('block/iomad_company_admin:company_add', $systemcontext)) {
    $showall = false;
}
if ($showall) {
    $params['showall'] = $showall;
}

// Set the name for the page.
$linktext = get_string('edit_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/editusers.php');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Get output renderer.                                                                                                                                                                                         
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('departmentid', 1, optional_param('departmentid', 0, PARAM_INT)));

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

require_login(null, false); // Adds to $PAGE, creates $output.

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

echo $output->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}   

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}
if ($departmentid == 0) {
    $departmentid = $userhierarchylevel;
}

if (!(iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
    or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
    print_error('nopermissions', 'error', '', 'edit/delete users');
}

// If we are showing all users we can't use the departments.
if (!$showall) {
// Get the appropriate list of departments.
    $userdepartment = $company->get_userlevel($USER);
    $departmenttree = company::get_all_subdepartments_raw($userdepartment->id);
    $treehtml = $output->department_tree($departmenttree, optional_param('departmentid', 0, PARAM_INT));
    echo $treehtml;

    $subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
    $select = new single_select($baseurl, 'departmentid', $subhierarchieslist, $departmentid);
    $select->label = get_string('department', 'block_iomad_company_admin');
    $select->formid = 'choosedepartment';
    $departmentselect = html_writer::tag('div', $output->render($select), array('id' => 'iomad_department_selector', 'style' => 'display: none;'));
}

// Set up the filter form.
if (iomad::has_capability('block/iomad_company_admin:company_add', $systemcontext)) {
    $mform = new iomad_user_filter_form(null, array('companyid' => $companyid, 'useshowall' => true, 'addusertype' => true));
} else {
    $mform = new iomad_user_filter_form(null, array('companyid' => $companyid, 'addusertype' => true));
}
$mform->set_data(array('departmentid' => $departmentid, 'usertype' => $usertype));
$mform->set_data($params);
$mform->get_data();

// Get the company additional optional user parameter names.
$fieldnames = array();
$foundfields = false;

if (!$showall && $category = $DB->get_record_sql('select uic.id, uic.name from {user_info_category} uic, {company} c where c.id = '.$companyid.'
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
$strsuspend = get_string('suspend', 'block_iomad_company_admin');
$strsuspendcheck = get_string('suspendcheck', 'block_iomad_company_admin');
$strpassword = get_string('resetpassword', 'block_iomad_company_admin');
$strpasswordcheck = get_string('resetpasswordcheck', 'block_iomad_company_admin');
$strunsuspend = get_string('unsuspend', 'block_iomad_company_admin');
$strunsuspendcheck = get_string('unsuspendcheck', 'block_iomad_company_admin');
$strshowallusers = get_string('showallusers');
$strenrolment = get_string('userenrolments', 'block_iomad_company_admin');
$struserlicense = get_string('userlicenses', 'block_iomad_company_admin');
$strshowall = get_string('showallcompanies', 'block_iomad_company_admin');

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

} else if ($password and confirm_sesskey()) {
    if (!$user = $DB->get_record('user', array('id' => $password))) {
        print_error('nousers');
    }

    if ($confirm != md5($password)) {
        $fullname = fullname($user, true);
        echo $output->heading(get_string('resetpassword', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('password' => $password, 'confirm' => md5($password), 'sesskey' => sesskey());
        echo $output->confirm(get_string('resetpasswordcheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editusers.php', $optionsyes), 'editusers.php');
        echo $output->footer();
        die;
    } else {
        // Actually delete the user.
        company_user::generate_temporary_password($user, true, true);
    }
} else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation.

    if (!iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
        print_error('nopermissions', 'error', '', 'delete a user');
    }

    if (!$user = $DB->get_record('user', array('id' => $delete))) {
        print_error('nousers', 'error');
    }

    if (!company::check_canedit_user($companyid, $user->id)) {
        print_error('invaliduserid');
    }

    if (is_primary_admin($user->id)) {
        print_error('nopermissions', 'error', '', 'delete the primary admin user');
    }

    if ($confirm != md5($delete)) {
        $fullname = fullname($user, true);
        echo $output->heading(get_string('deleteuser', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $output->confirm(get_string('deletecheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editusers.php', $optionsyes), 'editusers.php');
        echo $output->footer();
        die;
    } else {
        // Actually delete the user.
        company_user::delete($user->id);

        // Create an event for this.
        $eventother = array('userid' => $user->id, 'companyname' => $company->get_name(), 'companyid' => $companyid);
        $event = \block_iomad_company_admin\event\company_user_deleted::create(array('context' => context_system::instance(),
                                                                                     'objectid' => $user->id,
                                                                                     'userid' => $USER->id,
                                                                                     'other' => $eventother));
        $event->trigger();

    }

} else if ($suspend and confirm_sesskey()) {              // Delete a selected user, after confirmation.

    if (!iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
        print_error('nopermissions', 'error', '', 'suspend a user');
    }

    if (!$user = $DB->get_record('user', array('id' => $suspend))) {
        print_error('nousers', 'error');
    }

    if (!company::check_canedit_user($companyid, $user->id)) {
        print_error('invaliduserid');
    }
    if (is_primary_admin($user->id)) {
        print_error('nopermissions', 'error', '', 'delete the primary admin user');
    }

    if ($confirm != md5($suspend)) {
        $fullname = fullname($user, true);
        echo $output->heading(get_string('suspenduser', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('suspend' => $suspend, 'confirm' => md5($suspend), 'sesskey' => sesskey());
        echo $output->confirm(get_string('suspendcheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editusers.php', $optionsyes), 'editusers.php');
        echo $output->footer();
        die;
    } else {
        // Actually suspend the user.
        company_user::suspend($user->id);

        // Create an event for this.
        $eventother = array('userid' => $user->id, 'companyname' => $company->get_name(), 'companyid' => $companyid);
        $event = \block_iomad_company_admin\event\company_user_suspended::create(array('context' => context_system::instance(),
                                                                                       'objectid' => $user->id,
                                                                                       'userid' => $USER->id,
                                                                                       'other' => $eventother));
        $event->trigger();

        $params['suspend'] = 0;
    }

} else if ($unsuspend and confirm_sesskey()) {
    // Unsuspends a selected user, after confirmation.

    if (!iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
        print_error('nopermissions', 'error', '', 'suspend a user');
    }

    if (!$user = $DB->get_record('user', array('id' => $unsuspend))) {
        print_error('nousers', 'error');
    }

    if (!company::check_canedit_user($companyid, $user->id)) {
        print_error('invaliduserid');
    }

    if (is_primary_admin($user->id)) {
        print_error('nopermissions', 'error', '', 'delete the primary admin user');
    }

    if ($confirm != md5($unsuspend)) {
        $fullname = fullname($user, true);
        echo $output->heading(get_string('unsuspenduser', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('unsuspend' => $unsuspend, 'confirm' => md5($unsuspend), 'sesskey' => sesskey());
        echo $output->confirm(get_string('unsuspendcheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editusers.php', $optionsyes), 'editusers.php');
        echo $output->footer();
        die;
    } else {
        // Actually unsuspend the user.
        company_user::unsuspend($user->id);

        // Create an event for this.
        $eventother = array('userid' => $user->id, 'companyname' => $company->get_name(), 'companyid' => $companyid);
        $event = \block_iomad_company_admin\event\company_user_unsuspended::create(array('context' => context_system::instance(),
                                                                                         'objectid' => $user->id,
                                                                                         'userid' => $USER->id,
                                                                                         'other' => $eventother));
        $event->trigger();

        $params['suspend'] = 0;
    }

} else if ($acl and confirm_sesskey()) {
    if (!iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
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
        $aclrecord = new stdclass();
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
if (!$showall) {
    echo $departmentselect;
    $columns = array("firstname", "lastname", "email", "department", "lastaccess");
} else {
    $columns = array('company', "firstname", "lastname", "email", "department", "lastaccess");
}

// Display the user filter form.
$mform->display();

foreach ($columns as $column) {
    if ($column == 'company') {
        $string[$column] = get_string('company', 'block_iomad_company_admin');
    } else {
        $string[$column] = get_string("$column");
    }
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
        $columnicon = " <img src=\"" . $output->image_url('t/' . $columnicon) . "\" alt=\"\" />";

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
if (iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    // Make sure we dont display site admins.
    // Set default search to something which cant happen.
    $sqlsearch = "id!='-1' AND id NOT IN (" . $CFG->siteadmins . ")";

    // Get department users.
    $departmentusers = company::get_recursive_department_users($departmentid);
    if (count($departmentusers) > 0 || $showall) {
        $departmentids = "";
        foreach ($departmentusers as $departmentuser) {
            if (!empty($departmentids)) {
                $departmentids .= ",".$departmentuser->userid;
            } else {
                $departmentids .= $departmentuser->userid;
            }
        }
        if (!empty($showsuspended)) {
            $sqlsearch .= " AND deleted <> 1 ";
        } else {
            $sqlsearch .= " AND deleted <> 1 AND suspended = 0 ";
        }
        if (!$showall) {
            $sqlsearch .= " AND id in ($departmentids) ";
        }
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

    if ($usertype != 'a' ) {
        $sqlsearch .= " AND id IN (SELECT userid FROM {company_users}
                         WHERE managertype = :managertype) ";
        $searchparams['managertype'] = $usertype;
    }

    $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);

} else if (iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {   // Check if has role edit company users.

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
        if (!empty($showsuspended)) {
            $sqlsearch = " deleted <> 1 AND id in ($departmentids) ";
        } else {
            $sqlsearch = " deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
        }
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

    if ($usertype != 'a' ) {
        $sqlsearch .= " AND id IN (SELECT userid FROM {company_users}
                         WHERE managertype = :managertype) ";
        $searchparams['managertype'] = $usertype;
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
    $users = iomad_get_users_listing($sort, $dir, $page * $perpage, $perpage, '', '', '', $userlist, array('companyid' => $companyid, 'showall' => $showall, 'usertype' => $usertype));
    $totalusers = iomad_get_users_listing($sort, $dir, 0, 0, '', '', '', $userlist, array('companyid' => $companyid, 'showall' => $showall, 'usertype' => $usertype));

} else {
    $users = array();
}
$usercount = count($totalusers);

echo $output->heading("$usercount ".get_string('users'));

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

// Fix sort for paging.
$params['sort'] = $sort;
$params['dir'] = $dir;

// We don't want to be deleting when we are using the paging bar.
$params['delete'] = '';
$params['confirm'] = '';

$baseurl = new moodle_url('editusers.php', $params);
//echo "usercount = $usercount - page = $page - perpage = $perpage</br>";
echo $output->paging_bar($usercount, $page, $perpage, $baseurl);

flush();


if (!$users) {
    $match = array();
    echo $output->heading(get_string('nousersfound'));

    $table = null;

} else {

    $mainadmin = get_admin();

    $override = new stdclass();
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

    // set up the table.
    $table = new html_table();
    $table->id = 'ReportTable';
    if (!$showall) {
        $table->head = array ($fullnamedisplay, $email, $department, $lastaccess, '');
        $table->align = array ("left", "center", "center", "center", "center");
    } else {
        $table->head = array ($company, $fullnamedisplay, $email, $department, $lastaccess, '');
        $table->align = array ("left", 'center', "center", "center", "center", "center");
    }

    foreach ($users as $user) {

        // User actions
        $actions = array();

        if ($user->username == 'guest') {
            continue; // Do not dispaly dummy new user and guest here.
        }

        if ((iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($user->id == $USER->id or $user->id != $mainadmin->id) and !is_mnet_remote_user($user)) {
            if ($user->id != $USER->id && $DB->get_record_select('company_users', 'companyid =:company AND managertype != 0 AND userid = :userid', array('company' => $companyid, 'userid' => $user->id))
                && !iomad::has_capability('block/iomad_company_admin:editmanagers', $systemcontext)) {
               // This manager can't edit manager users.
            } else {
                $url = new moodle_url('/blocks/iomad_company_admin/editadvanced.php', array(
                    'id' => $user->id,   
                ));
                $actions['edit'] = new action_menu_link_secondary(
                    $url,
                    null,
                    $stredit
                );
                if (iomad::has_capability('block/iomad_company_admin:edituserpassword', $systemcontext)) {
                    $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                        'password' => $user->id,
                        'sesskey' => sesskey(),   
                    ));
                    $actions['password'] = new action_menu_link_secondary(
                        $url,
                        null,
                        $strpassword
                    );
                }
            }
        }

        if ($user->id != $USER->id) {
            if ((iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
                 or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
                if ($DB->get_record_select('company_users', 'companyid =:company AND managertype != 0 AND userid = :userid', array('company' => $companyid, 'userid' => $user->id))
                && !iomad::has_capability('block/iomad_company_admin:editmanagers', $systemcontext)) {
                    // Do nothing.
                } else {
                    if (iomad::has_capability('block/iomad_company_admin:deleteuser', $systemcontext)) {
                        $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                            'delete' => $user->id,
                            'sesskey' => sesskey(),
                        ));
                        $actions['delete'] = new action_menu_link_secondary(
                            $url,
                            null,
                            $strdelete
                        );
                    }
                    if (iomad::has_capability('block/iomad_company_admin:suspenduser', $systemcontext)) {
                        if (!empty($user->suspended)) {
                            $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                                'unsuspend' => $user->id,
                                'sesskey' => sesskey(),
                            ));
                            $actions['unsuspend'] = new action_menu_link_secondary(
                                $url,
                                null,
                                $strunsuspend
                            );
                        } else {
                            $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                                'suspend' => $user->id,
                                'sesskey' => sesskey(),
                            ));
                            $actions['suspend'] = new action_menu_link_secondary(
                                $url,
                                null,
                                $strsuspend
                            );
                        }
                    }
                }
            }
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_course_users', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($user->id == $USER->id or $user->id != $mainadmin->id)
             and !is_mnet_remote_user($user)) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_course_form.php', array(
                'userid' => $user->id,
            ));
            $actions['enrolment'] = new action_menu_link_secondary(
                $url,
                null,
                $strenrolment
            );
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_license_users', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($user->id == $USER->id or $user->id != $mainadmin->id)
             and !is_mnet_remote_user($user)) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php', array(
                'userid' => $user->id,
            ));
            $actions['userlicense'] = new action_menu_link_secondary(
                $url,
                null,
                $struserlicense
            );
        }

        if ($user->lastaccess) {
            $strlastaccess = format_time(time() - $user->lastaccess);
        } else {
            $strlastaccess = get_string('never');
        }
        $fullname = fullname($user, true);

        // Is this a suspended user?
        if (!empty($user->suspended)) {
            $fullname .= " (S)";
        }

        $user->department = $user->departmentname;

        // Edit menu
        $menu = new action_menu();
        $menu->set_owner_selector('.iomad_editusers-actionmenu');
        $menu->set_alignment(action_menu::TL, action_menu::BL);
        $menu->set_menu_trigger(get_string('usercontrols', 'block_iomad_company_admin'));
        foreach ($actions as $action) {
            $menu->add($action);
        }
        

        if (!$showall) {
            $table->data[] = array("$fullname",
                                "$user->email",
                                "$user->department",
                                $strlastaccess,
                                $output->render($menu),
                                );
        } else {
            $user->company = $user->companyname;

            $table->data[] = array($user->company,
                                    $fullname,
                                    $user->email,
                                    $user->department,
                                    $strlastaccess,
                                    $output->render($menu),
                                    );
        }
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $output->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $output->footer();

function iomad_get_users_listing($sort='lastaccess', $dir='ASC', $page=0, $recordsperpage=0,
                       $search='', $firstinitial='', $lastinitial='', $extraselect='', array $extraparams = null) {
    global $DB, $USER;

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
        } else if ($sort == "company") {
            $sort = " ORDER by c.name $dir";
        } else {
            $sort = " ORDER BY u.$sort $dir";
        }
    }

    // return the right type of user.
    if ($extraparams['usertype'] != 'a' ) {
        $managertypesql = " AND cu.managertype = :managertype ";
        $params['managertype'] = $extraparams['usertype'];
    } else {
        $managertypesql = "";
    }

    // all companies?
    if (!empty($extraparams['showall'])) {
        $companysql = "";
    } else {
        $company = new company($extraparams['companyid']);

        if ($parentslist = $company->get_parent_companies_recursive()) {
            $companysql = " AND c.id = :companyid AND u.id NOT IN (
                            SELECT userid FROM {company_users}
                            WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
        } else {
            $companysql = " AND c.id = :companyid";
        }
           $params['companyid'] = $extraparams['companyid'];
    }
    return $DB->get_records_sql("SELECT concat(c.id, '-', u.id), u.*, d.name as departmentname, c.name as companyname
                                 FROM {user} u, {department} d, {company_users} cu, {company} c
                                 WHERE $select and cu.userid = u.id and d.id = cu.departmentid AND c.id = cu.companyid
                                 $companysql
                                 $managertypesql
                                 $sort", $params, $page, $recordsperpage);

}

