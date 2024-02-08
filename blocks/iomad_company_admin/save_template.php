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

/**
 * Save a company role template
 */

use block_iomad_company_admin\iomad_company_admin;

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// parameters
$templateid = required_param('templateid', PARAM_INT);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $companycontext);

// Set the name for the page.
$linktext = get_string('savetemplate', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/save_template.php', array('templateid' => $templateid));

$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading and nav.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
    $PAGE->navbar->add($linktext, $linkurl);

$mform = new \block_iomad_company_admin\forms\company_role_save_form($linkurl, $companyid, $templateid);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/iomad_company_admin/company_capabilities.php'));
} else if ($data = $mform->get_data()) {

    // Save the template.
    $templateid = $DB->insert_record('company_role_templates', ['name' => $data->name]);
    $restrictions = $DB->get_records('company_role_restriction', ['companyid' => $companyid], null, 'id, roleid, capability');
    foreach ($restrictions as $restriction) {
        $DB->insert_record('company_role_templates_caps', [
            'templateid' => $templateid,
            'roleid' => $restriction->roleid,
            'capability' => $restriction->capability
        ]);
    }
    redirect(new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', ['templatesaved' => 1]));
}

if (!empty($templateid)) {
    $template = $DB->get_record('company_role_templates', ['id' => $templateid]);
    $mform->set_data($template);
}

// Display the form.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();