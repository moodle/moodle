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

require_once('../../config.php');
require_once( dirname('__FILE__').'/lib.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);   // Md5 confirmation hash.
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
$email  = optional_param('email', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
// How many per page.
$perpage      = optional_param('perpage', 30, PARAM_INT);
// Id of user to tweak mnet ACL (requires $access).
$acl          = optional_param('acl', '0', PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);

$params = array();

if ($firstname) {
    $params['firstname'] = $firstname;
}
if ($lastname) {
    $params['lastname'] = $lastname;
}
if ($email) {
    $params['email'] = $email;
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
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}

$systemcontext = context_system::instance();
require_login(); // Adds to $PAGE, creates $OUTPUT.
iomad::require_capability('local/report_completion:view', $systemcontext);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('report_users_title', 'local_report_users');

// Set the url.
$linkurl = new moodle_url('/local/report_users/index.php');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);

echo $OUTPUT->header();

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Get the associated department id.
$company = new company($companyid);
$parentlevel = company::get_company_parentnode($company->id);
$companydepartment = $parentlevel->id;

// Get the company additional optional user parameter names.
$fieldnames = array();
if ($category = company::get_category($companyid)) {
    // Get field names from company category.
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id))) {
        foreach ($fields as $field) {
            $fieldnames[$field->id] = 'profile_field_'.$field->shortname;
            ${'profile_field_'.$field->shortname} = optional_param('profile_field_'.
                                                      $field->shortname, null, PARAM_RAW);
        }
    }
}

// Deal with the user optional profile search.
$urlparams = $params;
$idlist = array();
$foundfields = false;
if (!empty($fieldnames)) {
    $fieldids = array();
    foreach ($fieldnames as $id => $fieldname) {
        $paramarray = array();
        if ($fields[$id]->datatype == "menu" ) {
            $paramarray = explode("\n", $fields[$id]->param1);
            if (!empty($paramarray[${$fieldname}])) {
                ${$fieldname} = $paramarray[${$fieldname}];
            }
        }
        if (!empty(${$fieldname}) ) {
            $idlist[0] = "We found no one";
            $fieldsql = $DB->sql_compare_text('data')." LIKE '%".${$fieldname}."%'
                                                        AND fieldid = $id";
            if ($idfields = $DB->get_records_sql("SELECT userid FROM {user_info_data}
                                                  WHERE $fieldsql")) {
                $fieldids[] = $idfields;
            }
            if (!empty($paramarray)) {
                $params[$fieldname] = array_search(${$fieldname}, $paramarray);
                $urlparams[$fieldname] = array_search(${$fieldname}, $paramarray);
            } else {
                if (!is_array(${$fieldname})) {
                    $params[$fieldname] = ${$fieldname};
                    $urlparams[$fieldname] = ${$fieldname};
                } else {
                    $params[$fieldname] = ${$fieldname};
                    $urlparams[$fieldname] = serialize(${$fieldname});
                }
            }
        }
    }
    if (!empty($fieldids)) {
        $foundfields = true;
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

$baseurl = new moodle_url(basename(__FILE__), $urlparams);
$returnurl = $baseurl;

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $systemcontext) ||
    !empty($SESSION->currenteditingcompany)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = company::get_userlevel($USER);
    $userhierarchylevel = $userlevel->id;
}
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the appropriate list of departments.
$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
$select = new single_select($baseurl, 'departmentid', $subhierarchieslist, $departmentid);
$select->label = get_string('department', 'block_iomad_company_admin');
$select->formid = 'choosedepartment';
echo html_writer::tag('div', $OUTPUT->render($select), array('id' => 'iomad_department_selector'));
$fwselectoutput = html_writer::tag('div', $OUTPUT->render($select),
                                    array('id' => 'iomad_company_selector'));
//if (!(iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
//    or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
//    print_error('nopermissions', 'error', '', 'edit/delete users');
//}

// Set up the filter form.
$mform = new iomad_user_filter_form(null, array('companyid' => $companyid));
$mform->set_data(array('departmentid' => $departmentid));
$mform->set_data($params);


// Display the user filter form.
$mform->display();

$stredit   = get_string('edit');
$strdelete = get_string('delete');
$strdeletecheck = get_string('deletecheck');
$strshowallusers = get_string('showallusers');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

$returnurl = $CFG->wwwroot."/local/report_users/index.php";


// Carry on with the user listing.

$columns = array("firstname", "lastname", "department", "email", "city", "country", "timecreated", "lastaccess");

foreach ($columns as $column) {
    if ($column == 'timecreated') {
        $string[$column] = get_string("$column", 'local_report_completion');
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
        $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $$column = $string[$column].$columnicon;
}

if ($sort == "name") {
    $sort = "firstname";
}

// Get all or company users depending on capability.

$dbsort = "";
// Check if has capability edit all users.
//if (iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext)) {
    // Check we havent looked and discounted everyone.
    if ((empty($idlist) && !$foundfields) || (!empty($idlist) && $foundfields)) {
        // Make sure we dont display site admins.
        // Set default search to something which cant happen.
        $sqlsearch = "id!='-1'";
        $siteadmins = explode(" ", $CFG->siteadmins);
        foreach ($siteadmins as $siteadmin) {
            $sqlsearch .= " AND id!='$siteadmin'";
        }

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
            if (!empty($showsuspended)) {
                $sqlsearch .= " AND deleted <> 1 AND id in ($departmentids) ";
            } else {
                $sqlsearch .= " AND deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
            }
        } else {
            $sqlsearch = "1 = 0";
        }

        // Deal with search strings..
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

        switch($sort) {
            case "firstname":
                $sqlsearch .= " order by firstname $dir ";
            break;
            case "lastname":
                $sqlsearch .= " order by lastname $dir ";
            break;
            case "email":
                $sqlsearch .= " order by email $dir ";
            break;
            case "timecreated":
                $sqlsearch .= " order by timecreated $dir ";
            break;
            case "access":
                $sqlsearch .= " order by lastaccess $dir ";
                $sort = "lastaccess";
            break;
        }

        // Get the user records.
        $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);
    } else {
        $userrecords = array();
    }

//}
//  if (iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)) {
    // Check if has role edit company users.

    // Check we havent looked and discounted everyone.
    if ((empty($idlist) && !$foundfields) || (!empty($idlist) && $foundfields)) {
        // Get users company association.
        $departmentusers = company::get_recursive_department_users($departmentid);
        $sqlsearch = "id!='-1'";
        if ( count($departmentusers) > 0 ) {
            $departmentids = "";
            foreach ($departmentusers as $departmentuser) {
                if (!empty($departmentids)) {
                    $departmentids .= ",".$departmentuser->userid;
                } else {
                    $departmentids .= $departmentuser->userid;
                }
            }
            if (!empty($showsuspended)) {
                $sqlsearch .= " AND deleted <> 1 AND id in ($departmentids) ";
            } else {
                $sqlsearch .= " AND deleted <> 1 AND suspended = 0 AND id in ($departmentids) ";
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

        switch($sort) {
            case "name":
                $sqlsearch .= " order by firstname $dir ";
                $dbsort = " order by u.firstname $dir ";
            break;
            case "lastname":
                $sqlsearch .= " order by lastname $dir ";
                $dbsort = " order by u.lastname $dir ";
            break;
            case "email":
                $sqlsearch .= " order by email $dir ";
                $dbsort = " order by u.email $dir ";
            break;
            case "timecreated":
                $sqlsearch .= " order by timecreated $dir ";
                $dbsort = " order by u.timecreated $dir ";
            break;
            case "lastaccess":
                $sqlsearch .= " order by currentlogin $dir ";
                $dbsort = " order by u.currentlogin $dir ";
            break;
            case "department":
                $dbsort = " order by d.name $dir ";
            break;
        }

        $userrecords = $DB->get_fieldset_select('user', 'id', $sqlsearch, $searchparams);
    } else {
        $userrecords = array();
    }
//}
$userlist = "";
if (!empty($userrecords)) {
    $userlist = " u.id in (". implode(',', array_values($userrecords)).") ";
}

if (!empty($userlist)) {
    $users = $DB->get_records_sql("SELECT u.id as id,
                                          u.username as username,
                                          u.email as email,
                                          u.firstname as firstname,
                                          u.lastname as lastname,
                                          u.alternatename as alternatename,
                                          u.firstnamephonetic as firstnamephonetic,
                                          u.lastnamephonetic as lastnamephonetic,
                                          u.middlename as middlename,
                                          u.city as city,
                                          u.country as country,
                                          u.timecreated as timecreated,
                                          u.currentlogin as lastaccess,
                                          u.suspended as suspended,
                                          d.name as departmentname
                                   FROM {user} u, {department} d, {company_users} cu
                                   WHERE u.deleted <> 1 AND $userlist
                                   AND cu.userid = u.id AND cu.departmentid = d.id
                                   AND cu.companyid = :companyid
                                   GROUP BY u.id, d.name $dbsort ", array('companyid' => $company->id), $page * $perpage, $perpage);
} else {
    $users = array();
}
$usercount = count($userrecords);

echo $OUTPUT->heading("$usercount ".get_string('users'));

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

flush();


if (!$users) {
    $match = array();
    echo $OUTPUT->heading(get_string('nousersfound'));

    echo "<p><a class='btn' href='" . new moodle_url('/blocks/iomad_company_admin/company_user_create_form.php') . "'>" .
         get_string('createuser', 'block_iomad_company_admin') . "</a></p>";

    $table = null;

} else {

    $countries = get_string_manager()->get_list_of_countries();

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
    // Set the initial parameters for the table header links.
    $linkparams = $urlparams;

    $override = new stdclass();
    $override->firstname = 'firstname';
    $override->lastname = 'lastname';
    $fullnamelanguage = get_string('fullnamedisplay', '', $override);
    if (($CFG->fullnamedisplay == 'firstname lastname') or
        ($CFG->fullnamedisplay == 'firstname') or
        ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
        // Work out for name sorting/direction and links.
        // Set the defaults.
        $linkparams['dir'] = 'ASC';
        $linkparams['sort'] = 'firstname';
        $firstnameurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'lastname';
        $lastnameurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'department';
        $departmenturl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'email';
        $emailurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'timecreated';
        $timecreatedurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'lastaccess';
        $accessurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'city';
        $cityurl = new moodle_url('index.php', $linkparams);
        $linkparams['sort'] = 'country';
        $countryurl = new moodle_url('index.php', $linkparams);

        // Set the options if there is alread a sort.
        if (!empty($params['sort'])) {
            if ($params['sort'] == 'firstname') {
                $linkparams['sort'] = 'firstname';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $firstnameurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $firstnameurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'lastname') {
                $linkparams['sort'] = 'lastname';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $lastnameurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $lastnameurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'department') {
                $linkparams['sort'] = 'department';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $departmenturl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'email') {
                $linkparams['sort'] = 'email';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $emailurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'lastaccess') {
                $linkparams['sort'] = 'lastaccess';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $accessurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $accessurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'timecreated') {
                $linkparams['sort'] = 'timecreated';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $timecreatedurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $timecreatedurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'country') {
                $linkparams['sort'] = 'country';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $countryurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $countryurl = new moodle_url('index.php', $linkparams);
                }
            } else if ($params['sort'] == 'city') {
                $linkparams['sort'] = 'city';
                if ($params['dir'] == 'ASC') {
                    $linkparams['dir'] = 'DESC';
                    $cityurl = new moodle_url('index.php', $linkparams);
                } else {
                    $linkparams['dir'] = 'ASC';
                    $cityurl = new moodle_url('index.php', $linkparams);
                }
            }
        }
    }
    $fullnamedisplay = $OUTPUT->action_link($firstnameurl, $firstname)." / ".
                               $OUTPUT->action_link($lastnameurl, $lastname);

    $table = new html_table();
    $table->head = array ($fullnamedisplay,
                          $OUTPUT->action_link($emailurl, $email),
                          $OUTPUT->action_link($departmenturl,
                          $department),
                          $OUTPUT->action_link($cityurl, $city),
                          $OUTPUT->action_link($countryurl,
                          $country),
                          $OUTPUT->action_link($timecreatedurl, $timecreated),
                          $OUTPUT->action_link($accessurl, $lastaccess));
    $table->align = array ("left", "left", "left", "left", "left", "left", "center", "center", "center");
    $table->width = "95%";
    foreach ($users as $user) {
        if ($user->username == 'guest') {
            continue; // Do not dispaly dummy new user and guest here.
        }


        if ($user->timecreated) {
            $strtimecreated = date('Y-m-d', $user->timecreated);
        } else {
            $strtimecreated = get_string('never');
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

        $table->data[] = array ("<a href='".$CFG->wwwroot.
                                "/local/report_users/userdisplay.php?userid=".
                                $user->id."'>$fullname</a>",
                            "$user->email",
                            "$user->departmentname",
                            "$user->city",
                            "$user->country",
                            $strtimecreated,
                            $strlastaccess);
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
