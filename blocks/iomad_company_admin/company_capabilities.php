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
 * Control company capabilities.
*/

use block_iomad_company_admin\iomad_company_admin;

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// parameters
$roleid = optional_param('roleid', 0, PARAM_INT);
$savetemplate = optional_param('savetemplate', 0, PARAM_CLEAN);
$manage = optional_param('manage', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUM);
$templateid = optional_param('templateid', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);

// Set the companyid
// (before output in case it redirects)
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);

// access stuff
require_login();
iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

// Set the name for the page.
$linktext = get_string('restrictcapabilities', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', array('templateid' => $templateid));

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

$PAGE->requires->jquery();
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// Require javascript
$PAGE->requires->js_call_amd('block_iomad_company_admin/company_capabilities', 'init', [$companyid, $templateid, $roleid]);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

echo $OUTPUT->header();

//  Deal with any deletes.
if ($action == 'delete' && confirm_sesskey()) {
    if ($confirm != md5($templateid)) {
        if (!$templateinfo = $DB->get_record('company_role_templates', array('id' => $templateid))) {
            print_error('roletemplatenotfound', 'block_iomad_company_admin');
        }

        echo $OUTPUT->heading(get_string('deleteroletemplate', 'block_iomad_company_admin'). " " . $templateinfo->name);
        $optionsyes = array('templateid' => $templateid, 'confirm' => md5($templateid), 'sesskey' => sesskey(), 'action' => 'delete');
        echo $OUTPUT->confirm(get_string('deleteroletemplatefull', 'block_iomad_company_admin', "'" . $templateinfo->name ."'"),
                              new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', $optionsyes),
                                             '/blocks/iomad_company_admin/company_capabilities.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Delete the template.
        $DB->delete_records('company_role_templates_caps', array('templateid' => $templateid));
        $DB->delete_records('company_role_templates', array('id' => $templateid));
        notice(get_string('roletemplatedeleted', 'block_iomad_company_admin'));
    }
}

$mform = new \block_iomad_company_admin\forms\company_role_save_form($linkurl, $companyid, $templateid);

if ($data = $mform->get_data()) {

    // Save the template.
    $templateid = $DB->insert_record('company_role_templates', array('name' => $data->name));
    $restrictions = $DB->get_records('company_role_restriction', array('companyid' => $companyid), null, 'id,roleid,capability');
    foreach ($restrictions as $restriction) {
        $DB->insert_record('company_role_templates_caps', array ('templateid' => $templateid,
                                                                'roleid' => $restriction->roleid,
                                                                'capability' => $restriction->capability));
    }
    notice(get_string('roletemplatesaved', 'block_iomad_company_admin'));
}

if (!empty($savetemplate)) {
    if (!empty($templateid)) {
        $template = $DB->get_record('company_role_templates', array('id' => $templateid));
        $mform->set_data($template);
    }

    // Display the form.
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

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
    $saveurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', ['savetemplate' => 1, 'templateid' => $templateid]);
    $manageurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php', ['manage' => 1]);
    $backurl = empty($templateid) ? '' : $backurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php');
    $capabilitiesroles = new \block_iomad_company_admin\output\capabilitiesroles($roles, $companyid, $templateid, $linkurl, $saveurl, $manageurl, $backurl);
    echo $output->render_capabilitiesroles($capabilitiesroles);

}

echo $OUTPUT->footer();
