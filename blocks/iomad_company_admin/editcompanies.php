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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once('lib.php');

$delete = optional_param('delete', 0, PARAM_INT);
$suspend = optional_param('suspend', 0, PARAM_INT);
$unsuspend = optional_param('unsuspend', 0, PARAM_INT);
$enableecommerce = optional_param('enableecommerce', 0, PARAM_INT);
$disableecommerce = optional_param('disableecommerce', 0, PARAM_INT);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$confirmcompany = optional_param('confirmcompany', 0, PARAM_INT);
$sort = optional_param('sort', 'name', PARAM_ALPHA);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', $CFG->iomad_max_list_companies, PARAM_INT);
$companyid = optional_param('companyid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_CLEAN);// Search string.
$name = optional_param('name', '', PARAM_CLEAN);
$city = optional_param('city', '', PARAM_CLEAN);
$country = optional_param('country', '', PARAM_CLEAN);
$postcode = optional_param('postcode', '', PARAM_CLEAN);
$region = optional_param('region', '', PARAM_CLEAN);
$code = optional_param('code', '', PARAM_CLEAN);
$custom1 = optional_param('ccustom1', '', PARAM_CLEAN);
$custom2 = optional_param('ccustom2', '', PARAM_CLEAN);
$custom3 = optional_param('ccustom3', '', PARAM_CLEAN);
$showchild = optional_param('showchild', 1, PARAM_INT);
$resetbutton = optional_param('resetbutton', '', PARAM_CLEAN);

$params = [
    'delete' => $delete,
    'suspend' => $suspend ? $suspend : $unsuspend,
    'showsuspended' => $showsuspended,
    'confirm' => $confirm,
    'confirmcompany' => $confirmcompany,
    'sort' => $sort,
    'dir' => $dir,
    'page' => $page,
    'perpage' => $perpage,
    'search' => $search,
    'name' => $name,
    'city' => $city,
    'region' => $region,
    'postcode' => $postcode,
    'code' => $code,
    'country' => $country,
    'showchild' => $showchild,
    'companyid' => $companyid,
    'custom1' => $custom1,
    'custom2' => $custom2,
    'custom3' => $custom3,
];

$context = context_system::instance();

require_login();
iomad::require_capability('block/iomad_company_admin:company_add_child', $context);

// Correct the navbar.
$linktext = get_string('managecompanies', 'block_iomad_company_admin');
$linkurl = new moodle_url('/blocks/iomad_company_admin/editcompanies.php', $params);

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading($linktext);

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

// Set up the filter form.
$mform = new block_iomad_company_admin\forms\iomad_company_filter_form();
$mform->set_data(array('companyid' => $companyid));
$mform->set_data($params);
$data = $mform->get_data();
if (empty($data->showchild)) {
    $showchild = 0;
    $params['showchild'] = 0;
}

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

// Reset form?
if ($resetbutton) {
    redirect(new moodle_url('/blocks/iomad_company_admin/editcompanies.php'));
}

if ($suspend and confirm_sesskey()) {

    // Suspend a company, after confirmation.
    $company = $DB->get_record('company', ['id' => $suspend], '*', MUST_EXIST);
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
    $company = $DB->get_record('company', ['id' => $unsuspend], '*', MUST_EXIST);
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
    $company = $DB->get_record('company', ['id' => $enableecommerce], '*', MUST_EXIST);
    $enableecommercecompany = new company($company->id);
    $enableecommercecompany->ecommerce(1);

} else if ($disableecommerce and confirm_sesskey()) {

    // Disables ecommerce for a selected company.
    $company = $DB->get_record('company', ['id' => $disableecommerce], '*', MUST_EXIST);
    $enableecommercecompany = new company($company->id);
    $enableecommercecompany->ecommerce(0);
}

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
} else {
    $sqlsearch .= " 1 = 1 ";
}

// Deal with search strings.
$searchparams = [];
if (!empty($params['name'])) {
    $sqlsearch .= " AND " . $DB->sql_like('name', ':name', false);
    $searchparams['name'] = '%'.$params['name'].'%';
}
if (!empty($params['city'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('city', ':city', false);
    $searchparams['city'] = '%'.$params['city'].'%';
}
if (!empty($params['country'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('country', ':country', false);
    $searchparams['country'] = '%'.$params['country'].'%';
}
if (!empty($params['region'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('region', ':region', false);
    $searchparams['region'] = '%'.$params['region'].'%';
}
if (!empty($params['postcode'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('postcode', ':postcode', false);
    $searchparams['postcode'] = '%'.$params['postcode'].'%';
}
if (!empty($params['address'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('address', ':address', false);
    $searchparams['address'] = '%'.$params['address'].'%';
}
if (!empty($params['code'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('code', ':code', false);
    $searchparams['code'] = '%'.$params['code'].'%';
}
if (!empty($params['custom1'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('custom1', ':custom1', false);
    $searchparams['custom1'] = '%'.$params['custom1'].'%';
}
if (!empty($params['custom2'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('custom2', ':custom2', false);
    $searchparams['custom2'] = '%'.$params['custom2'].'%';
}
if (!empty($params['custom3'])) {
    $sqlsearch .=  " AND " . $DB->sql_like('custom3', ':custom3', false);
    $searchparams['custom3'] = '%'.$params['custom3'].'%';
}

$companyrecords = $DB->get_fieldset_select('company', 'id', $sqlsearch, $searchparams);

// Add in the parent companies if option is set.
if (!empty($params['showchild']) && !empty($params['name'])) {
    foreach ($companyrecords as $companyrecord) {
        $sqlsearch1 = " parentid  = $companyrecord";
        $companyrecords1 = $DB->get_fieldset_select('company', 'id', $sqlsearch1);
        foreach($companyrecords1 as $companyrecord1){
            array_push($companyrecords, $companyrecord1); 
        }
    }
    foreach ($companyrecords as $companyrecord) {
        $sqlsearch1 = " id  = $companyrecord AND parentid  != 0";
        $companyrecords1 = $DB->get_fieldset_select('company', 'parentid', $sqlsearch1);
        foreach($companyrecords1 as $companyrecord1){
            array_push($companyrecords, $companyrecord1);
        }
    }
}

// Sort out the resulting list so we only have the distinct values.
$companyrecords = array_unique($companyrecords);

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

    // Check to make sure if the first company is a child.
    if (!empty($showchild)) {
        foreach ($companies as $companycheck) {
            if ($companycheck->parentid != 0) {
                $parentcompany = $DB->get_records_sql("SELECT *, 0 as depth
                                                       FROM {company}
                                                       WHERE id = :parentid",
                                                       array('parentid' => $companycheck->parentid));
                $companies = $parentcompany + $companies;
            }
            break;
        }

        $companies = block_iomad_company_admin\iomad_company_admin::order_companies_by_parent($companies);
    }
    $allmycompanies = iomad::get_companies_listing($sort, $dir, 0, 0, '', '', '', $companylist);
    $companycount = count($allmycompanies);
} else {
    $companies = array();
    $companycount = 0;
}

$baseurl = new moodle_url('editcompanies.php', $params);

if ($companies) {

    // set up the table.
    $table = new html_table();
    $table->head = array ($name, $city, $country, "");
    $table->align = array ("left", "left", "left", "left");
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
                    $suspendbutton = "<a class='btn btn-sm btn-warning' href='$suspendurl'>$strunsuspend</a>";
                }
            }
        } else {
            if (!$primary) {
                $linkparams['suspend'] = $company->id;
                $suspendurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                            $linkparams);
                $suspendbutton = "<a class='btn btn-sm btn-warning' href='$suspendurl'>$strsuspend</a>";
            }
            $manageurl = new moodle_url('/my', array('company' => $company->id));
            $managebutton = "<a class='btn btn-sm btn-primary' href='$manageurl'>$strmanage</a>";

            if (iomad::has_capability('block/iomad_company_admin:company_add_child', $context)) {
                $childurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/company_edit_form.php",
                                           array('createnew' => 1, 'parentid' => $company->id));
                $childbutton = "<a class='btn btn-sm btn-primary' href='$childurl'>$strcreatechild</a>";
            }
        }

        unset($linkparams['suspend']);
        unset($linkparams['unsuspend']);

        if (empty($CFG->commerce_admin_enableall) && iomad::has_capability('block/iomad_company_admin:company_add', $context)) {
            if (!empty($company->ecommerce)) {
                unset($linkparams['suspend']);
                $linkparams['disableecommerce'] = $company->id;

                $ecommerceurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                            $linkparams);
                $ecommercebutton = "<a class='btn btn-sm btn-primary' href='$ecommerceurl'>$strdisableecommerce</a>";
            } else {
                $linkparams['enableecommerce'] = $company->id;
                $ecommerceurl = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/editcompanies.php",
                                           $linkparams);
                $ecommercebutton = "<a class='btn btn-sm btn-primary' href='$ecommerceurl'>$strenableecommerce</a>";
            }
        }

        $overviewurl = new moodle_url($CFG->wwwroot . "/local/report_companies/index.php",
                                    array('companyid' => $company->id));
        $overviewurl = "<a class='btn btn-sm btn-primary' href='$overviewurl'>$stroverview</a>";

        // Is the company suspended?
        if (!empty($company->suspended)) {
            $fullname = $company->name . ' (S)';
            $table->rowclasses[] = 'table-dark';
        } else {
            $fullname = $company->name;
            $table->rowclasses[] = '';
        }

        // Indent child companies
        if ($company->depth == 0) {
            $fullname = "<b>$fullname</b>";
        } else {
            $fullname = str_repeat('&emsp;&emsp;', $company->depth) . $fullname;
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
} else {
    $table = null;
    $match = [];
}

// Render template
$editcompanies = new block_iomad_company_admin\output\editcompanies([
    'form' => $mform->render(),
    'table' => empty($table) ? null : html_writer::table($table),
    'pagingbar' => $output->paging_bar($companycount, $page, $perpage, $linkurl),
    'companycount' => $companycount,
    'companycountplural' => $companycount != 1,
]);

echo $OUTPUT->header();
echo $output->render_editcompanies($editcompanies);
echo $OUTPUT->footer();
