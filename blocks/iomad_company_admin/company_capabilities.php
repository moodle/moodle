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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');

// parameters
$roleid = optional_param('roleid', 0, PARAM_INT);
$ajaxcap = optional_param('ajaxcap', '', PARAM_CLEAN);
$ajaxvalue = optional_param('ajaxvalue', '', PARAM_CLEAN);
$save = optional_param('savetemplate', 0, PARAM_CLEAN);
$manage = optional_param('manage', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUM);
$templateid = optional_param('templateid', 0, PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);

// Set the companyid
// (before output in case it redirects)
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);

// Set up the save form.
class company_role_save_form extends company_moodleform {

    public function __construct($actionurl,
                                $companyid,
                                $templateid) {
        
        $this->companyid = $companyid;
        $this->templateid = $templateid;

        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->companyid);
        $this->_form->setType('companyid', PARAM_INT);
    }


    public function definition_after_data() {

        $mform =& $this->_form;

        $mform->addElement('hidden', 'templateid', $this->templateid);
        $mform->setType('templateid', PARAM_INT);

        $mform->addElement('text',  'name', get_string('roletemplatename', 'block_iomad_company_admin'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('name', 'roletemplatename', 'block_iomad_company_admin');
        $mform->addRule('name', get_string('missingroletemplatename', 'block_iomad_company_admin'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $this->add_action_buttons(true, get_string('saveroletemplate', 'block_iomad_company_admin'));
    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($DB->get_record('company_role_templates', array('name' => $data['name']))) {
            $errors['name'] = get_string('templatenamealreadyinuse', 'block_iomad_company_admin');
        }

        return $errors;
    }
}

// access stuff
require_login();
iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

// check if ajax callback
if ($ajaxcap) {
    error_log('Got it '.$ajaxcap.' '.$ajaxvalue);
    $parts = explode('.', $ajaxcap);
    list($type, $id, $roleid, $capability) = $parts;
    
    if ($type == 'c') {
        // dealing with a company restriction.
        // if box is unticked (false) an entry is created (or kept)
        // if box is ticked (true) any entry is deleted
        $restriction = $DB->get_record('company_role_restriction', array(
                'roleid' => $roleid,
                'companyid' => $id,
                'capability' => $capability,
        ));
        if ($ajaxvalue=='false') {
            if (!$restriction) {
                $restriction = new stdClass();
                $restriction->companyid = $id;
                $restriction->roleid = $roleid;
                $restriction->capability = $capability;
                $DB->insert_record('company_role_restriction', $restriction);
            }
        } else {
            if ($restriction) {
                $DB->delete_records('company_role_restriction', array('id' => $restriction->id));
            }
        }
    } else if ($type == 't') {
        // Deling with a template restriction.
        // if box is unticked (false) an entry is created (or kept)
        // if box is ticked (true) any entry is deleted
        $restriction = $DB->get_record('company_role_templates_caps', array(
                'roleid' => $roleid,
                'templateid' => $id,
                'capability' => $capability,
        ));
        if ($ajaxvalue=='false') {
            if (!$restriction) {
                $restriction = new stdClass();
                $restriction->templateid = $id;
                $restriction->roleid = $roleid;
                $restriction->capability = $capability;
                $DB->insert_record('company_role_templates_caps', $restriction);
            }
        } else {
            if ($restriction) {
                $DB->delete_records('company_role_templates_caps', array('id' => $restriction->id));
            }
        }
    }
    reload_all_capabilities();
    die;
}

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

// Correct the navbar.
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);
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

$mform = new company_role_save_form($linkurl, $companyid, $templateid);

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

if (!empty($save)) {
    if (!empty($templateid)) {
        $template = $DB->get_record('company_role_templates', array('id' => $templateid));
        $mform->set_data($template);
    }

    // Display the form.
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

if ($roleid) {
    if (empty($templateid)) {
        $capabilities = iomad_company_admin::get_iomad_capabilities($roleid, $companyid);
    } else {
        $capabilities = iomad_company_admin::get_iomad_template_capabilities($roleid, $templateid);
    }

    echo $output->capabilities($capabilities, $roleid, $companyid, $templateid);
    echo $output->roles_button($linkurl);

} else if ($manage) {
    // Display the list of templates.
    $templates = $DB->get_records('company_role_templates', array(), 'name');
    echo $output->role_templates($templates, $linkurl);
    
} else {

    // get the list of roles to choose from
    $roles = iomad_company_admin::get_roles();
    echo $output->role_select($roles, $linkurl, $companyid, $templateid);

    // output the save button.
    
    $saveurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php',
                                array('savetemplate' => 1,
                                      'templateid' => $templateid));
    $manageurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php',
                                  array('manage' => 1));
    if (!empty($templateid)) {
        $backurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php');
    } else {
        $backurl = '';
    }
    echo $output->templates_buttons($saveurl, $manageurl, $backurl);
}
?>
<script>
$(".checkbox").change(function() {
	$.post("<?php echo $linkurl; ?>", {
		ajaxcap:this.value,
		ajaxvalue:this.checked
	});
});
</script>
<?php

echo $OUTPUT->footer();
