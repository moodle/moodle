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
require_once($CFG->dirroot.'/blocks/iomad_company_admin/editusers_table.php');
require_once('lib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$password      = optional_param('password', 0, PARAM_INT);
$suspend      = optional_param('suspend', 0, PARAM_INT);
$unsuspend      = optional_param('unsuspend', 0, PARAM_INT);
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
$sort         = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);   // Md5 confirmation hash.
$email  = optional_param('email', 0, PARAM_CLEAN);
$showall = optional_param('showall', false, PARAM_BOOL);
$usertype = optional_param('usertype', 'a', PARAM_ALPHANUM);

$params = array();

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
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

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
    if ($categories = $DB->get_records_sql("SELECT id FROM {user_info_category}
                                                    WHERE id NOT IN (
                                                     SELECT profileid FROM {company})")) {
        foreach ($categories as $category) {
            if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
                foreach ($fields as $field) {
                    $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
                    ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.
                                                              $field->shortname, null, PARAM_RAW);
                }
            }
        }
    }
}

// Deal with the user optional profile search.
$idlist = array();
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        if (!empty($fields[$id]->datatype) && $fields[$id]->datatype == "menu") {
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
$struserreport = get_string('report_users_title', 'local_report_users');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

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
        $returnmessage = get_string('userdeletedok', 'block_iomad_company_admin');
        redirect($returnurl, $returnmessage, null, \core\output\notification::NOTIFY_SUCCESS);

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

        $returnmessage = get_string('usersuspendedok', 'block_iomad_company_admin');
        redirect($returnurl, $returnmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    }

} else if ($unsuspend and confirm_sesskey()) {
    // Check if the company has gone over the user quota.
    if (!$company->check_usercount(1)) {
        $maxusers = $company->get('maxusers');
        print_error('maxuserswarning', 'block_iomad_company_admin', $returnurl, $maxusers);
    }

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

        $returnmessage = get_string('userunsuspendedok', 'block_iomad_company_admin');
        redirect($returnurl, $returnmessage, null, \core\output\notification::NOTIFY_SUCCESS);
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

// Display the user filter form.
$mform->display();







// Build the table.
// Do we have any additional reporting fields?
$extrafields = array();
if (!empty($CFG->iomad_report_fields)) {
    $companyrec = $DB->get_record('company', array('id' => $companyid));
    foreach (explode(',', $CFG->iomad_report_fields) as $extrafield) {
        $extrafields[$extrafield] = new stdclass();
        $extrafields[$extrafield]->name = $extrafield;
        if (strpos($extrafield, 'profile_field') !== false) {
            // Its an optional profile field.
            $profilefield = $DB->get_record('user_info_field', array('shortname' => str_replace('profile_field_', '', $extrafield)));
            if ($profilefield->categoryid == $companyrec->profileid ||
                !$DB->get_record('company', array('profileid' => $profilefield->categoryid))) {
                $extrafields[$extrafield]->title = $profilefield->name;
                $extrafields[$extrafield]->fieldid = $profilefield->id;
            } else {
                unset($extrafields[$extrafield]);
            }
        } else {
            $extrafields[$extrafield]->title = get_string($extrafield);
        }
    }
}

// Deal with the form searching.
$searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);

// Get all or company users depending on capability.
if (iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    // Make sure we dont display site admins.
    // Set default search to something which cant happen.
    $sqlsearch = " AND u.id NOT IN (" . $CFG->siteadmins . ")";

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
            $sqlsearch .= " AND u.deleted = 0 ";
        } else {
            $sqlsearch .= " AND u.deleted = 0 AND u.suspended = 0 ";
        }
        if (!$showall) {
            $sqlsearch .= " AND u.id IN ($departmentids) ";
        }
    } else {
        $sqlsearch = " AND 1 = 0";
    }

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
            $sqlsearch = " AND u.deleted = 0 AND u.id IN ($departmentids) ";
        } else {
            $sqlsearch = " AND u.deleted = 0 AND u.suspended = 0 AND u.id in ($departmentids) ";
        }
    } else {
        $sqlsearch = "AND 1 = 0";
    }
} else {
    // Can't edit any users.
    $sqlsearch = " AND 1 = 0";
}

// return the right type of user.
if ($usertype != 'a' ) {
    $managertypesql = " AND cu.managertype = :usertype ";
} else {
    $managertypesql = "";
}

// all companies?
if (!empty($showall)) {
    $companysql = "";
} else {
    $company = new company($companyid);

    if ($parentslist = $company->get_parent_companies_recursive()) {
        $companysql = " AND c.id = :companyid AND u.id NOT IN (
                        SELECT userid FROM {company_users}
                        WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
    } else {
        $companysql = " AND c.id = :companyid";
    }
}

$selectsql = "DISTINCT " . $DB->sql_concat("u.id", $DB->sql_concat("'-'", "c.id")) . " AS cindex, u.*, c.id AS companyid, c.name AS companyname";
$fromsql = "{user} u JOIN {company_users} cu ON (u.id = cu.userid) JOIN {department} d ON (cu.departmentid = d.id AND cu.companyid = d.company) JOIN {company} c ON (cu.companyid = c.id AND d.company = c.id)";
$wheresql = $searchinfo->sqlsearch . " $sqlsearch $companysql $managertypesql";
$sqlparams = $params + array('companyid' => $companyid) + $searchinfo->searchparams;
$countsql = "SELECT COUNT(DISTINCT u.id, c.id) FROM $fromsql WHERE $wheresql";

// Carry on with the user listing.
if (!$showall) {
    echo $departmentselect;
    $headers = array(get_string('fullname'),
                     get_string('email'),
                     get_string('department'));
    $columns = array("fullname",
                     "email",
                     "department");
} else {
    $headers = array(get_string('company', 'block_iomad_company_admin'),
                     get_string('fullname'),
                     get_string('email'),
                     get_string('department'));
    $columns = array('companyname',
                     "fullname",
                     "email",
                     "department");
}


// Deal with optional report fields.
if (!empty($extrafields)) {
    foreach ($extrafields as $extrafield) {
        $headers[] = $extrafield->title;
        $columns[] = $extrafield->name;
        if (!empty($extrafield->fieldid)) {
            // Its a profile field.
            // Skip it this time as these may not have data.
        } else {
            $selectsql .= ", u." . $extrafield->name;
        }
    }
    foreach ($extrafields as $extrafield) {
        if (!empty($extrafield->fieldid)) {
            // Its a profile field.
            $selectsql .= ", P" . $extrafield->fieldid . ".data AS " . $extrafield->name;
            $fromsql .= " LEFT JOIN {user_info_data} P" . $extrafield->fieldid . " ON (u.id = P" . $extrafield->fieldid . ".userid AND P".$extrafield->fieldid . ".fieldid = :p" . $extrafield->fieldid . "fieldid )";
            $sqlparams["p".$extrafield->fieldid."fieldid"] = $extrafield->fieldid;
        }
    }
}

// Deal with final columns.
$headers[] = get_string('lastaccess');
$columns[] = "lastaccess";

// Can we see the controls?
if (iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
             || iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    $headers[] = '';
    $columns[] = 'actions';

}

// Actually create and display the table.
$table = new block_iomad_company_admin_editusers_table('block_iomad_company_admin_editusers_table');
$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->set_count_sql($countsql, $sqlparams);
$table->define_baseurl($baseurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('actions');
$table->sort_default_column = 'fullname DESC';

$table->out($CFG->iomad_max_list_users, true);

// Finish the display
echo $output->footer();