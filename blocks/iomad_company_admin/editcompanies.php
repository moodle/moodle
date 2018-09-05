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
$suspend      = optional_param('suspend', 0, PARAM_INT);
$unsuspend      = optional_param('unsuspend', 0, PARAM_INT);
$enableecommerce    = optional_param('enableecommerce', 0, PARAM_INT);
$disableecommerce    = optional_param('disableecommerce', 0, PARAM_INT);
$showsuspended  = optional_param('showsuspended', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$confirmcompany  = optional_param('confirmcompany', 0, PARAM_INT);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);
$companyid      = optional_param('companyid', 0, PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$name       = optional_param('name', 0, PARAM_CLEAN);
$city       = optional_param('city', 0, PARAM_CLEAN);
$country       = optional_param('country', 0, PARAM_CLEAN);

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
if ($confirmcompany) {
    $params['confirmcompany'] = $confirmcompany;
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
if ($name) {
    $params['name'] = $name;
}
if ($city) {
    $params['city'] = $city;
}
if ($country) {
    $params['country'] = $country;
}
if ($companyid) {
    $params['companyid'] = $companyid;
}

$context = context_system::instance();

require_login();
iomad::require_capability('block/iomad_company_admin:company_add_child', $context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('managecompanies', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/editcompanies.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, null);

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

// Set up the filter form.
$mform = new iomad_company_filter_form();
$mform->set_data(array('companyid' => $companyid));
$mform->set_data($params);

$strsuspend = get_string('suspendcompany', 'block_iomad_company_admin');
$strsuspendcheck = get_string('suspendcompanycheck', 'block_iomad_company_admin');
$strunsuspend = get_string('unsuspendcompany', 'block_iomad_company_admin');
$strunsuspendcheck = get_string('unsuspendcompanycheck', 'block_iomad_company_admin');
$strenableecommerce = get_string('ecommerceenabled', 'block_iomad_company_admin');
$strdisableecommerce = get_string('disableecommerce', 'block_iomad_company_admin');
$strshowallusers = get_string('showallcompanies', 'block_iomad_company_admin');
$strmanage = get_string('managecompany', 'block_iomad_company_admin');
$stroverview = get_string('overview', 'local_report_companies');
$strcreatechild = get_string('createchildcompany', 'block_iomad_company_admin');

if (empty($CFG->loginhttps)) {
    $securewwwroot = $CFG->wwwroot;
} else {
    $securewwwroot = str_replace('http:', 'https:', $CFG->wwwroot);
}

if ($suspend and confirm_sesskey()) {
    // Suspend a company, after confirmation.

    /* if (!iomad::has_capability('block/iomad_company_admin:suspendcompany', $context)) {
        print_error('nopermissions', 'error', '', 'delete a user');
    } */

    if (!$company = $DB->get_record('company', array('id' => $suspend))) {
        print_error('companynotfound', 'block_iomad_company_admin');
    }

    if ($confirm != md5($suspend)) {
        $fullname = $company->name;
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('suspendcompany', 'block_iomad_company_admin'). " " . $fullname);
        $optionsyes = array('suspend' => $suspend,
                            'confirm' => md5($suspend),
                            'showsuspended' => $showsuspended,
                            'sesskey' => sesskey());

        echo $OUTPUT->confirm(get_string('suspendcompanycheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editcompanies.php', $optionsyes), 'editcompanies.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Suspend the company
        // Create an event for this.  This handles the actual lifting.
        $eventother = array('companyid' => $company->id);
        $event = \block_iomad_company_admin\event\company_suspended::create(array('context' => context_system::instance(),
                                                                                      'objectid' => $company->id,
                                                                                      'userid' => $USER->id,
                                                                                      'other' => $eventother));
        $event->trigger();
        $returnurl->param('suspend', 0);
        $returnurl->param('unsuspend', 0);
        $returnurl->param('showsuspended', $showsuspended);
        redirect($returnurl);
        die;
    }
} else if ($unsuspend and confirm_sesskey()) {
    // Unsuspends a selected company, after confirmation.

   /* if (!iomad::has_capability('block/iomad_company_admin:suspendcompany', $context)) {
        print_error('nopermissions', 'error', '', 'delete a user');
    } */

    if (!$company = $DB->get_record('company', array('id' => $unsuspend))) {
        print_error('companynotfound', 'block_iomad_company_admin');
    }

    if (!empty($company->parentid) && $DB->get_record('company', array('id' => $company->parentid, 'suspended' => 1))) {
        print_error('parentcompanysuspended', 'block_iomad_company_admin');
    }

    if ($confirm != md5($unsuspend)) {
        $fullname = $company->name;
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('unsuspendcompany', 'block_iomad_company_admin'). " " . $fullname);
        $optionsno = array('unsuspend' => $unsuspend,
                            'confirm' => md5($unsuspend),
                            'showsuspended' => $showsuspended,
                            'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('unsuspendcompanycheckfull', 'block_iomad_company_admin', "'$fullname'"),
                              new moodle_url('editcompanies.php', $optionsno), 'editcompanies.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Unsuspend the company
        // Create an event for this.  This handles the actual lifting.
        $eventother = array('companyid' => $company->id);
        $event = \block_iomad_company_admin\event\company_unsuspended::create(array('context' => context_system::instance(),
                                                                                      'objectid' => $company->id,
                                                                                      'userid' => $USER->id,
                                                                                      'other' => $eventother));
        $event->trigger();
        $returnurl->param('unsuspend', 0);
        $returnurl->param('suspend', 0);
        $returnurl->param('showsuspended', $showsuspended);
        redirect($returnurl);
        die;
    }

} else if ($enableecommerce and confirm_sesskey()) {

    // Enables ecommerce for a selected company.
    if (!$company = $DB->get_record('company', array('id' => $enableecommerce))) {
        print_error('companynotfound', 'block_iomad_company_admin');
    }

    // Enable ecommerce for the company
    $enableecommercecompany = new company($company->id);
    $enableecommercecompany->ecommerce(1);

} else if ($disableecommerce and confirm_sesskey()) {

    // Disables ecommerce for a selected company.
    if (!$company = $DB->get_record('company', array('id' => $disableecommerce))) {
        print_error('companynotfound', 'block_iomad_company_admin');
    }

    // Disable ecommerce for the company
    $enableecommercecompany = new company($company->id);
    $enableecommercecompany->ecommerce(0);
}

echo $OUTPUT->header();

// Display the user filter form.
$mform->display();

// Carry on with the user listing.
$columns = array("name", "city", "country");

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
        $columnicon = $dir == "ASC" ? "down":"up";
        $columnicon = " <img src=\"" . $OUTPUT->image_url('t/' . $columnicon) . "\" alt=\"\" />";

    }
    $params['sort'] = $column;
    $params['dir'] = $columndir;
    $$column = "<a href= ". new moodle_url('editcompanies.php', $params).">".$string[$column]."</a>$columnicon";
}

// Get all companies.
$sqlsearch = "";
if (empty($showsuspended)) {
    $sqlsearch .= " suspended = 0 ";
}

// Deal with search strings.
$searchparams = array();
if (!empty($params['name'])) {
    if (!empty($sqlsearch)) {
        $sqlsearch .= " AND ";
    }
    $sqlsearch .= "name like :name ";
    $searchparams['name'] = '%'.$params['name'].'%';
}
if (!empty($params['city'])) {
    if (!empty($sqlsearch)) {
        $sqlsearch .= " AND ";
    }
    $sqlsearch .= "city like :city ";
    $searchparams['city'] = '%'.$params['city'].'%';
}
if (!empty($params['country'])) {
    if (!empty($sqlsearch)) {
        $sqlsearch .= " AND ";
    }
    $sqlsearch .= "country like :country ";
    $searchparams['country'] = '%'.$params['country'].'%';
}

$companyrecords = $DB->get_fieldset_select('company', 'id', $sqlsearch, $searchparams);

$companylist = "";
if (!empty($companyrecords)) {
    if (iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
        $companylist = "id IN (". implode(',', array_values($companyrecords)).")";
    } else {
        $mycompanylist = company::get_companies_select(true);
        $companylist = "id IN (". implode(',', array_values($companyrecords)).") AND
                        id IN (". implode(',', array_keys($mycompanylist)).")";
    }
} else {
    $companylist = "1=2";
}
if (!empty($companylist)) {
    $companies = iomad::get_companies_listing($sort, $dir, $page * $perpage, $perpage, '', '', '', $companylist);
    $allmycompanies = iomad::get_companies_listing($sort, $dir, 0, 0, '', '', '', $companylist);
    $companycount = count($allmycompanies);
} else {
    $companies = array();
    $companycount = 0;
}

if ($companycount == 1) {
    echo $OUTPUT->heading(get_string('companycount', 'block_iomad_company_admin', $companycount));
} else {
    echo $OUTPUT->heading(get_string('companycountplural', 'block_iomad_company_admin', $companycount));
}

$alphabet = explode(',', get_string('alphabet', 'block_iomad_company_admin'));
$strall = get_string('all');

$baseurl = new moodle_url('editcompanies.php', $params);
echo $OUTPUT->paging_bar($companycount, $page, $perpage, $baseurl);

flush();


if (!$companies) {
    $match = array();
    echo $OUTPUT->heading(get_string('nocompanies', 'block_iomad_company_admin'));

    $table = null;

} else {
    
    // set up the table.
    $table = new html_table();
    $table->head = array ($name, $city, $country, "");
    $table->align = array ("left", "left", "left", "center");
    $table->width = "95%";

    foreach ($companies as $company) {
        $primary = true;
        $suspendurl = '';
        $suspendbutton = '';
        $manageurl = '';
        $managebutton = '';
        $ecommerceurl = '';
        $ecommercebutton = '';
        $childurl = '';
        $childbutton = '';
        $linkparams = $params;
        $linkparams['sesskey'] = sesskey();
        if (iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            $primary = false;
        } else if ($DB->get_records_sql("SELECT * FROM {company} c
                                  JOIN {company_users} cu
                                  ON (c.id = cu.companyid)
                                  WHERE c.id = :companyid
                                  AND c.parentid IN (". implode(',', array_keys($companies)) . ")
                                  AND cu.userid = :userid",
                                  array('companyid' => $company->id, 'userid' => $USER->id))) {
            // primary company is either only company you are in or its any company in the list
            // which doesn't have a parent in the list.
            $primary = false; 
        }
        if (!empty($company->suspended)) {
            if (!$primary) {
                // is the parent suspended?
                if (empty($company->parentid) || $DB->get_record('company', array('id' => $company->parentid, 'suspended' => 0))) {
                    $linkparams['unsuspend'] = $company->id;
                    $suspendurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                                $linkparams);
                    $suspendbutton = "<a class='btn btn-primary' href='$suspendurl'>$strunsuspend</a>";
                }
            }
        } else {
            if (!$primary) {
                $linkparams['suspend'] = $company->id;
                $suspendurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                            $linkparams);
                $suspendbutton = "<a class='btn btn-primary' href='$suspendurl'>$strsuspend</a>";
            }
            $manageurl = new moodle_url('/my', array('company' => $company->id));
            $managebutton = "<a class='btn btn-primary' href='$manageurl'>$strmanage</a>";

            if (iomad::has_capability('block/iomad_company_admin:company_add_child', $context)) {
                $childurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/company_edit_form.php",
                                           array('createnew' => 1, 'parentid' => $company->id));
                $childbutton = "<a class='btn btn-primary' href='$childurl'>$strcreatechild</a>";
            }
        }

        if (empty($CFG->commerce_admin_enableall) && iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            if (!empty($company->ecommerce)) {
                $linkparams['disableecommerce'] = $company->id;

                $ecommerceurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                            $linkparams);
                $ecommercebutton = "<a class='btn btn-primary' href='$ecommerceurl'>$strdisableecommerce</a>";
            } else {
                $linkparams['enableecommerce'] = $company->id;
                $ecommerceurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                           $linkparams);
                $ecommercebutton = "<a class='btn btn-primary' href='$ecommerceurl'>$strenableecommerce</a>";
            }
        }

        $overviewurl = new moodle_url($CFG->wwwroot . "/local/report_companies/index.php",
                                    array('companyid' => $company->id));
        $overviewurl = "<a class='btn btn-primary' href='$overviewurl'>$stroverview</a>";

        // Is the company suspended?
        if (!empty($company->suspended)) {
            $fullname = $company->name . ' (S)';
        } else {
            $fullname = $company->name;
        }
        $table->data[] = array ("$fullname",
                            "$company->city",
                            "$company->country",
                            $overviewurl . ' ' .
                            $managebutton . ' ' .
                            $childbutton . ' ' .
                            $suspendbutton . ' ' .
                            $ecommercebutton);
    }
}

if (!empty($table)) {
    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($companycount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
