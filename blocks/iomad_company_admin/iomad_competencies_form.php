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
$perpage      = optional_param('perpage', $CFG->iomad_max_list_competencies, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$frameworkid = optional_param('frameworkid', 0, PARAM_INTEGER);
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
if ($frameworkid) {
    $params['frameworkid'] = $frameworkid;
}

$systemcontext = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:manageframeworks', $systemcontext);

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/iomad_frameworks_form.php');
$linktext = get_string('iomad_frameworks_title', 'block_iomad_company_admin');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('iomad_company_frameworks_title', 'block_iomad_company_admin'));
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext, false);

// Is the users company set and no other company selected?
if (empty($company) && !empty($companyid)) {
    $company = $companyid;
    $params['company'] = $company;
}

if (!empty($update)) {
    // Need to change something.
    if (!$frameworkdetails = (array) $DB->get_record('iomad_frameworks', array('frameworkid' => $frameworkid))) {
        print_error(get_string('invaliddetails', 'block_iomad_company_admin'));
    } else {
        if ('shared' == $update) {
            $previousshared = $frameworkdetails['shared'];
            // Check if we are sharing a framework for the first time.
            if ($previousshared == 0 && $shared != 0) { // Turning sharing on.
                $frameworkinfo = $DB->get_record('framework', array('id' => $frameworkid));
                // Set the shared options on.
                $frameworkinfo->groupmode = 1;
                $frameworkinfo->groupmodeforce = 1;
                $DB->update_record('framework', $frameworkinfo);
                $frameworkdetails['shared'] = $shared;
                $DB->update_record('iomad_frameworks', $frameworkdetails);
                // Deal with any current enrolments.
                if ($companyframework = $DB->get_record('company_framework', array('frameworkid' => $frameworkid))) {
                    if ($shared == 2) {
                        $sharingrecord = new stdclass();
                        $sharingrecord->frameworkid = $frameworkid;
                        $sharingrecord->companyid = $companyframework->companyid;
                        $DB->insert_record('company_shared_frameworks', $sharingrecord);
                    }
                    company::company_users_to_company_framework_group($companyframework->companyid, $frameworkid);
                }
            } else if ($shared == 0 and $previousshared != 0) { // Turning sharing off.
                $frameworkinfo = $DB->get_record('framework', array('id' => $frameworkid));
                // Set the shared options on.
                $frameworkinfo->groupmode = 0;
                $frameworkinfo->groupmodeforce = 0;
                $DB->update_record('framework', $frameworkinfo);
                $frameworkdetails['shared'] = $shared;
                $DB->update_record('iomad_frameworks', $frameworkdetails);
                // Deal with enrolments.
                if ($companygroups = $DB->get_records('company_framework_groups', array('frameworkid' => $frameworkid))) {
                    // Got companies using it.
                    $count = 1;
                    // Skip the first company, it was the one who had it before anyone else so is
                    // assumed to be the owning company.
                    foreach ($companygroups as $companygroup) {
                        if ($count == 1) {
                            continue;
                        }
                        $count ++;
                        company::unenrol_company_from_framework($companygroup->companyid, $frameworkid);
                    }
                }
            } else {  // Changing from open sharing to closed sharing.
                $frameworkdetails['shared'] = $shared;
                $DB->update_record('iomad_frameworks', $frameworkdetails);
                if ($companygroups = $DB->get_records('company_framework_groups', array('frameworkid' => $frameworkid))) {
                    // Got companies using it.
                    foreach ($companygroups as $companygroup) {
                        $sharingrecord = new stdclass();
                        $sharingrecord->frameworkid = $frameworkid;
                        $sharingrecord->companyid = $companygroup->companyid;
                        $DB->insert_record('company_shared_frameworks', $sharingrecord);
                    }
                }
            }

        }
    }
}

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

echo $OUTPUT->header();

// Get the list of companies and display it as a drop down select..

$companyids = $DB->get_records_menu('company', array(), 'id, name');
$companyids['none'] = get_string('nocompany', 'block_iomad_company_admin');
$companyids['all'] = get_string('allframeworks', 'block_iomad_company_admin');
ksort($companyids);
$companyselect = new single_select($linkurl, 'company', $companyids, $company);
$companyselect->label = get_string('company', 'block_iomad_company_admin');
$companyselect->formid = 'choosecompany';
echo html_writer::tag('div', $OUTPUT->render($companyselect), array('id' => 'iomad_company_selector')).'</br>';

// Need a name search in here too.

// Set default frameworks.
$frameworks = array();

if (!empty($company)) {
    if ($company == 'none') {
        // Get all frameworks which are not assigned to any company.
        if (!empty($search)) {
            $select = "shortname like '%$search%' AND";
        } else {
            $select = "";
        }
        $sql = "SELECT * from {competency_framework} WHERE $select
                id not in (select frameworkid from {company_comp_framework})";
        $frameworks = $DB->get_records_sql($sql);
    } else  if ($company == 'all') {
        // Get every framework.
        if (!empty($search)) {
            $select = "shortname like '%$search%'";
        } else {
            $select = "";
        }
        $frameworks = $DB->get_records_select('competency_framework', $select);
    } else {
        // Get the frameworks belonging to that company only.
        if (!empty($search)) {
            $select = "AND cf.shortname like '%$search%'";
        } else {
            $select = "";
        }
        $sql = "SELECT cf.* from {competency_framework} cf, {company_comp_framework} ccf WHERE
                ccf.companyid=$company AND ccf.frameworkid = cf.id $select";
        $frameworks = $DB->get_records_sql($sql);
    }
}

// Display the table.
$table = new html_table();
$table->head = array (
    get_string('company', 'block_iomad_company_admin'),
    get_string('framework'),
    get_string('shared', 'block_iomad_company_admin')  . $OUTPUT->help_icon('shared', 'block_iomad_company_admin'),
);
$table->align = array ("left", "center", "center");
$table->width = "95%";
$selectbutton = array('0' => get_string('no'), '1' => get_string('yes'));
$sharedselectbutton = array('0' => get_string('no'),
                            '1' => get_string('open', 'block_iomad_company_admin'),
                            '2' => get_string('closed', 'block_iomad_company_admin'));


foreach ($frameworks as $framework) {
    if (!$iomaddetails = $DB->get_record('iomad_frameworks', array('frameworkid' => $framework->id))) {
        $iomadrecord = array('frameworkid' => $framework->id, 'licensed' => 0, 'shared' => 0);
        $iomadrecord['id'] = $DB->insert_record('iomad_frameworks', $iomadrecord);
        $iomaddetails = (object) $iomadrecord;
    }
    $linkparams = $params;
    $linkparams['frameworkid'] = $framework->id;
    $linkparams['update'] = 'shared';
    $sharedurl = new moodle_url($baseurl, $linkparams);
    $sharedselect = new single_select($sharedurl, 'shared', $sharedselectbutton, $iomaddetails->shared);
    $sharedselect->label = '';
    $sharedselect->formid = 'sharedselect'.$framework->id;
    $sharedselectoutput = html_writer::tag('div', $OUTPUT->render($sharedselect), array('id' => 'shared_selector'.$framework->id));
    if ($tablecompany = $DB->get_records_sql("select c.shortname from {company} c, {company_comp_framework} ccf WHERE
                                                      ccf.frameworkid = $framework->id and ccf.companyid = c.id")) {
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
    $frameworklink = new moodle_url('/framework/view.php', array('id'=>$framework->id));
    $table->data[] = array ($companyname,
                            "<a href='$frameworklink'>$framework->fullname</a>",
                            $sharedselectoutput);
}

if (!empty($table)) {
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
