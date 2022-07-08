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

require_once('../../config.php');
require_once(dirname('__FILE__').'/lib.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$company       = optional_param('company', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_templates, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$templateid = optional_param('templateid', 0, PARAM_INTEGER);
$update = optional_param('update', null, PARAM_ALPHA);
$shared = optional_param('shared', 0, PARAM_INTEGER);

$params = array();

if ($company) {
    $params['company'] = $company;
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
if ($templateid) {
    $params['templateid'] = $templateid;
}

$systemcontext = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:managetemplates', $systemcontext);
$PAGE->set_context($systemcontext);

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/iomad_templates_form.php');
$linktext = get_string('iomad_templates_title', 'block_iomad_company_admin');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext, false);

// Is the users company set and no other company selected?
if (empty($company) && !empty($companyid)) {
    $company = $companyid;
    $params['company'] = $company;
}

if (!empty($update)) {
    // Need to change something.
    if (!$templatedetails = (array) $DB->get_record('iomad_templates', array('templateid' => $templateid))) {
        print_error(get_string('invaliddetails', 'block_iomad_company_admin'));
    } else {
        if ('shared' == $update) {
            $previousshared = $templatedetails['shared'];
            // Check if we are sharing a template for the first time.
            if ($previousshared == 0 && $shared != 0) { // Turning sharing on.
                // Set the shared options on.
                $templatedetails['shared'] = $shared;
                $DB->update_record('iomad_templates', $templatedetails);
                // Deal with any current assignments.
                if ($companytemplate = $DB->get_record('company_comp_templates', array('templateid' => $templateid))) {
                    if ($shared == 2) {
                        $sharingrecord = new stdclass();
                        $sharingrecord->templateid = $templateid;
                        $sharingrecord->companyid = $companytemplate->companyid;
                        $DB->insert_record('company_shared_templates', $sharingrecord);
                    }
                }
            } else if ($shared == 0 and $previousshared != 0) { // Turning sharing off.
                // Set the shared options on.
                $templatedetails['shared'] = $shared;
                $DB->update_record('iomad_templates', $templatedetails);
                // Deal with assignments.
                if ($companygroups = $DB->get_records('company_shared_templates', array('templateid' => $templateid))) {
                    // Got companies using it.
                    $count = 1;
                    // Skip the first company, it was the one who had it before anyone else so is
                    // assumed to be the owning company.
                    foreach ($companygroups as $companygroup) {
                        if ($count == 1) {
                            continue;
                        }
                        $count ++;
                        $DB->delete_records('company_shared_templates', (array) $companygroup);
                    }
                }
            } else {  // Changing from open sharing to closed sharing.
                $templatedetails['shared'] = $shared;
                $DB->update_record('iomad_templates', $templatedetails);
            }

        }
    }
}

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

echo $OUTPUT->header();

// Get the list of companies and display it as a drop down select..

$companyids = $DB->get_records_menu('company', array(), 'id, name');
$companyids['none'] = get_string('nocompanytemplates', 'block_iomad_company_admin');
$companyids['all'] = get_string('alltemplates', 'block_iomad_company_admin');
ksort($companyids);
$companyselect = new single_select($linkurl, 'company', $companyids, $company);
$companyselect->label = get_string('company', 'block_iomad_company_admin');
$companyselect->formid = 'choosecompany';
echo html_writer::tag('div', $OUTPUT->render($companyselect), array('id' => 'iomad_company_selector')).'</br>';

// Need a name search in here too.

// Set default templates.
$templates = array();

if (!empty($company)) {
    if ($company == 'none') {
        // Get all templates which are not assigned to any company.
        if (!empty($search)) {
            $select = "shortname like '%$search%' AND";
        } else {
            $select = "";
        }
        $sql = "SELECT * from {competency_template} WHERE $select
                id not in (select templateid from {company_comp_templates})";
        $templates = $DB->get_records_sql($sql);
    } else  if ($company == 'all') {
        // Get every template.
        if (!empty($search)) {
            $select = "shortname like '%$search%'";
        } else {
            $select = "";
        }
        $templates = $DB->get_records_select('competency_template', $select);
    } else {
        // Get the templates belonging to that company only.
        if (!empty($search)) {
            $select = "AND cf.shortname like '%$search%'";
        } else {
            $select = "";
        }
        $sql = "SELECT cf.* from {competency_template} cf, {company_comp_templates} ccf WHERE
                ccf.companyid=$company AND ccf.templateid = cf.id $select";
        $templates = $DB->get_records_sql($sql);
    }
}

// Display the table.
$table = new html_table();
$table->head = array (
    get_string('company', 'block_iomad_company_admin'),
    get_string('template', 'block_iomad_company_admin'),
    get_string('shared', 'block_iomad_company_admin')  . $OUTPUT->help_icon('shared_template', 'block_iomad_company_admin'),
);
$table->align = array ("left", "center", "center");
$table->width = "95%";
$selectbutton = array('0' => get_string('no'), '1' => get_string('yes'));
$sharedselectbutton = array('0' => get_string('no'),
                            '1' => get_string('open', 'block_iomad_company_admin'),
                            '2' => get_string('closed', 'block_iomad_company_admin'));


foreach ($templates as $template) {
    if (!$iomaddetails = $DB->get_record('iomad_templates', array('templateid' => $template->id))) {
        $iomadrecord = array('templateid' => $template->id, 'licensed' => 0, 'shared' => 0);
        $iomadrecord['id'] = $DB->insert_record('iomad_templates', $iomadrecord);
        $iomaddetails = (object) $iomadrecord;
    }
    $linkparams = $params;
    $linkparams['templateid'] = $template->id;
    $linkparams['update'] = 'shared';
    $sharedurl = new moodle_url($baseurl, $linkparams);
    $sharedselect = new single_select($sharedurl, 'shared', $sharedselectbutton, $iomaddetails->shared);
    $sharedselect->label = '';
    $sharedselect->formid = 'sharedselect'.$template->id;
    $sharedselectoutput = html_writer::tag('div', $OUTPUT->render($sharedselect), array('id' => 'shared_selector'.$template->id));
    if ($tablecompany = $DB->get_records_sql("select c.shortname from {company} c, {company_comp_templates} ccf WHERE
                                                      ccf.templateid = $template->id and ccf.companyid = c.id")) {
        $companyname = "";
        foreach ($tablecompany as $tcompany) {
            if ($companyname == "") {
                $companyname = $tcompany->shortname;
            } else {
                $companyname .= ", " . $tcompany->shortname;
            }
        }
    } else {
        $companyname = "";
    }
    $templatelink = new moodle_url('/admin/tool/lp/templatecompetencies.php', array('templateid'=>$template->id,
                                                                                    'pagecontextid' => 1));
    $table->data[] = array ($companyname,
                            "<a href='$templatelink'>$template->shortname</a>",
                            $sharedselectoutput);
}

if (!empty($table)) {
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
