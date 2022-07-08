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
 * Control company capabilities.
*/

use block_iomad_company_admin\iomad_company_admin;

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// parameters
$roleid = optional_param('roleid', 0, PARAM_INT);
$templateid = optional_param('templateid', 0, PARAM_INT);
$manage = optional_param('manage', 0, PARAM_INT);
$templatesaved = optional_param('templatesaved', 0, PARAM_INT);

// Set the companyid
// (before output in case it redirects)
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

// access stuff
require_login();
iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

// Set the name for the page.
if (empty($templateid)) {
    $linktext = get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $company->get_name());
} else {
    $template = $DB->get_record('company_role_templates', ['id' => $templateid], '*', MUST_EXIST);
    $linktext = get_string('roletemplate', 'block_iomad_company_admin') . ' ' . $template->name;
}

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', array('templateid' => $templateid));

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Require javascript
$PAGE->requires->js_call_amd('block_iomad_company_admin/company_capabilities', 'init', [$companyid, $templateid, $roleid]);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

echo $OUTPUT->header();

if ($roleid) {

    // Display the list of capabilities (template or company).
    if (empty($templateid)) {
        $caps = iomad_company_admin::get_iomad_capabilities($roleid, $companyid);
    } else {
        $caps = iomad_company_admin::get_iomad_template_capabilities($roleid, $templateid);
    }

    $capabilities = new \block_iomad_company_admin\output\capabilities($caps, $roleid, $companyid, $templateid, $linkurl);
    echo $output->render_capabilities($capabilities);
} else if ($manage) {

    // Display the list of templates.
    $templates = $DB->get_records('company_role_templates', array(), 'name');
    $roletemplates = new \block_iomad_company_admin\output\roletemplates($templates, $linkurl);
    echo $output->render_roletemplates($roletemplates);
} else {

    // get the list of roles to choose from
    $roles = iomad_company_admin::get_roles();
    $saveurl = new moodle_url('/blocks/iomad_company_admin/save_template.php', ['templateid' => $templateid]);
    $manageurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', ['manage' => 1]);
    $backurl = empty($templateid) ? '' : $backurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php');
    $capabilitiesroles = new \block_iomad_company_admin\output\capabilitiesroles($roles, $companyid, $templateid, $linkurl, $saveurl, $manageurl, $backurl, $templatesaved);
    echo $output->render_capabilitiesroles($capabilitiesroles);
}

echo $OUTPUT->footer();
