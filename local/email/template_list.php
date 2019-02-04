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

require_once( 'local_lib.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once( 'config.php');
require_once( 'lib.php');
require_once($CFG->dirroot . '/local/iomad/lib/user.php');

// Set up the save form.
class company_templateset_save_form extends company_moodleform {

    public function __construct($actionurl,
                                $companyid,
                                $templatesetid) {

        $this->companyid = $companyid;
        $this->templatesetid = $templatesetid;

        parent::__construct($actionurl);
    }


    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->companyid);
        $this->_form->setType('companyid', PARAM_INT);
    }


    public function definition_after_data() {

        $mform =& $this->_form;

        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->setType('templatesetid', PARAM_INT);

        $mform->addElement('text',  'templatesetname', get_string('templatesetname', 'local_email'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('templatesetname', 'templatesetname', 'local_email');
        $mform->addRule('templatesetname', get_string('missingtemplatesetname', 'local_email'), 'required', null, 'client');
        $mform->setType('templatesetname', PARAM_MULTILANG);

        $this->add_action_buttons(true, get_string('savetemplateset', 'local_email'));
    }

    public function validation($data, $files) {
        global $DB;
        $errors = array();

        if ($DB->get_record_sql("SELECT id FROM {email_templateset}
                                 where " . $DB->sql_compare_text('templatesetname') ." = :templatesetname",
                                 array('templatesetname' => $data['templatesetname']))) {
            $errors['templatesetname'] = get_string('templatesetnamealreadyinuse', 'local_email');
        }

        return $errors;
    }
}

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$lang         = optional_param('lang', 'en', PARAM_LANG);
$ajaxtemplate = optional_param('ajaxtemplate', '', PARAM_CLEAN);
$ajaxvalue = optional_param('ajaxvalue', '', PARAM_CLEAN);
$save = optional_param('savetemplateset', 0, PARAM_CLEAN);
$manage = optional_param('manage', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUM);
$finished = optional_param('finished', 0, PARAM_BOOL);
$templatesetid = optional_param('templatesetid', 0, PARAM_INT);
$templateid = optional_param('templateid', 0, PARAM_INT);

if (!empty($templatesetid)) {
    $SESSION->currenttemplatesetid = $templatesetid;
}
if (!empty($SESSION->currenttemplatesetid) && !$finished) {
     $templatesetid = $SESSION->currenttemplatesetid;
}
if ($finished) {
    unset($SESSION->currenttemplatesetid);
    $templatesetid = 0;
}

$context = context_system::instance();
require_login();

$email = local_email::get_templates();

$block = 'local_email';

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('template_list_title', $block);
// Set the url.
$linkurl = new moodle_url('/local/email/template_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->requires->jquery();

// Set the page heading.
$PAGE->set_heading($linktext);
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// get output renderer
$output = $PAGE->get_renderer('local_email');


// Set the companyid to bypass the company select form if possible.
if (!empty($SESSION->currenteditingcompany)) {
    $companyid = $SESSION->currenteditingcompany;
} else if (!empty($USER->company)) {
    $companyid = company_user::companyid();
} else if (!iomad::has_capability('local/email:list', context_system::instance())) {
    print_error('There has been a configuration error, please contact the site administrator');
} else {
    redirect(new moodle_url('/local/iomad_dashboard/index.php'),
                            'Please select a company from the dropdown first');
}

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir,
                                                    'perpage' => $perpage));
$returnurl = $baseurl;

// check if ajax callback
if ($ajaxtemplate) {
    $parts = explode('.', $ajaxtemplate);
    list($type, $id, $managertype, $templatename) = $parts;

    if ($type == 'c') {
        // dealing with a company email template.
        if (!$templateinfos = $DB->get_records('email_template',
            array('name' => $templatename,
                  'companyid' => $id))) {
            $newtemplate = new stdclass();
            $newtemplate->companyid = $id;
            $newtemplate->name = $templatename;
            $newtemplate->subject = get_string($templatename.'_subject', 'local_email');
            $newtemplate->body = get_string($templatename.'_body', 'local_email');
            $newtemplate->disabled = 0;
            $newtemplate->disabledmanager = 0;
            $newtemplate->disabledsupervisor = 0;

            if (isset($CFG->lang)) {
                $newtemplate->lang = $CFG->lang;
            } else {
                $newtemplate->lang = 'en';
            }

            // What are we disabling?
            if ($managertype == 'e') {
                $newtemplate->disabled = 1;
            }
            if ($managertype == 'em') {
                $newtemplate->managerdisabled = 1;
            }
            if ($managertype == 'es') {
                $newtemplate->supervisordisabled = 1;
            }
            $DB->insert_record('email_template', $newtemplate);
        }
        if ($ajaxvalue=='false') {
            if ($managertype == 'e') {
                $DB->execute("UPDATE {email_template}
                              SET disabled = 1
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'em') {
                $DB->execute("UPDATE {email_template}
                              SET disabledmanager = 1
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'es') {
                $DB->execute("UPDATE {email_template}
                              SET disabledsupervisor = 1
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
        } else {
            if ($managertype == 'e') {
                $DB->execute("UPDATE {email_template}
                              SET disabled = 0
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'em') {
                $DB->execute("UPDATE {email_template}
                              SET disabledmanager = 0
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'es') {
                $DB->execute("UPDATE {email_template}
                              SET disabledsupervisor = 0
                              WHERE companyid = :companyid
                              AND name = :templatename",
                              array('companyid' => $id,
                                    'templatename' => $templatename));
            }
        }
    } else if ($type == 't') {
        // dealing with a company email template.
        if (!$templateinfos = $DB->get_records('email_templateset_templates',
            array('name' => $templatename,
                  'templateset' => $id))) {
            $newtemplate = new stdclass();
            $newtemplate->templateset = $id;
            $newtemplate->name = $templatename;
            $newtemplate->subject = get_string($templatename.'_subject', 'local_email');
            $newtemplate->body = get_string($templatename.'_body', 'local_email');
            $newtemplate->disabled = 0;
            $newtemplate->disabledmanager = 0;
            $newtemplate->disabledsupervisor = 0;

            if (isset($CFG->lang)) {
                $newtemplate->lang = $CFG->lang;
            } else {
                $newtemplate->lang = 'en';
            }

            // What are we disabling?
            if ($managertype == 'e') {
                $newtemplate->disabled = 1;
            }
            if ($managertype == 'em') {
                $newtemplate->managerdisabled = 1;
            }
            if ($managertype == 'es') {
                $newtemplate->supervisordisabled = 1;
            }
            $DB->insert_record('email_templateset_templates', $newtemplate);
        }
        if ($ajaxvalue=='false') {
            if ($managertype == 'e') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabled = 1
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'em') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabledmanager = 1
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'es') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabledsupervisor = 1
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
        } else {
            if ($managertype == 'e') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabled = 0
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'em') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabledmanager = 0
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
            if ($managertype == 'es') {
                $DB->execute("UPDATE {email_templateset_templates}
                              SET disabledsupervisor = 0
                              WHERE templateset = :templateset
                              AND name = :templatename",
                              array('templateset' => $id,
                                    'templatename' => $templatename));
            }
        }
    }

    // Don't process any more.
    die;
}

echo $output->header();

//  Deal with any deletes.
if ($action == 'delete' && confirm_sesskey()) {
    if ($confirm != md5($templatesetid)) {
        if (!$templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            print_error('templatesetnotfound', 'local_email');
        }

        echo $OUTPUT->heading(get_string('deletetemplateset', 'local_email'). " " . $templatesetinfo->templatesetname);
        $optionsyes = array('templatesetid' => $templatesetid, 'confirm' => md5($templatesetid), 'sesskey' => sesskey(), 'action' => 'delete');
        echo $OUTPUT->confirm(get_string('deletetemplatesetfull', 'local_email', "'" . $templatesetinfo->templatesetname ."'"),
                              new moodle_url('/local/email/template_list.php', $optionsyes),
                                             '/local/email/template_list.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Delete the template.
        $DB->delete_records('email_templateset_templates', array('templateset' => $templatesetid));
        $DB->delete_records('email_templateset', array('id' => $templatesetid));
        notice(get_string('templatesetdeleted', 'local_email'));
    }
}

$mform = new company_templateset_save_form($linkurl, $companyid, $templatesetid);

if ($data = $mform->get_data()) {
    // Save the template.
    $templatesetid = $DB->insert_record('email_templateset', array('templatesetname' => $data->templatesetname));
    $emailtemplates = $DB->get_records('email_template', array('companyid' => $companyid));
    foreach ($emailtemplates as $emailtemplate) {
        $emailtemplate->templateset = $templatesetid;
        $DB->insert_record('email_templateset_templates', $emailtemplate);
    }
    notice(get_string('emailtemplatesetsaved', 'local_email'));
}

if (!empty($save)) {
    if (!empty($templatesetid)) {
        $templateset = $DB->get_record('email_templateset', array('id' => $templatesetid));
        $mform->set_data($templateset);
    }

    // Display the form.
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

$company = new company($companyid);
// Check we can actually do anything on this page.
iomad::require_capability('local/email:list', $context);

if (empty($manage)) {
    if (empty($templatesetid)) {
        echo '<h3>' . get_string('email_templates_for', $block, $company->get_name()) . '</h3>';
    } else {
        $templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid));
        echo '<h3>' . get_string('email_templates_for', $block, $templatesetinfo->templatesetname) . '</h3>';
    }

    // output the save button.
    $saveurl = new moodle_url('/local/email/template_list.php',
                                array('savetemplateset' => 1,
                                      'templatesetid' => $templatesetid));
    $manageurl = new moodle_url('/local/email/template_list.php',
                                  array('manage' => 1));
    if (!empty($templatesetid)) {
        $backurl = new moodle_url('/local/email/template_list.php', array('finished' => true));
    } else {
        $backurl = '';
    }
    echo $output->templateset_buttons($saveurl, $manageurl, $backurl);
}

// Sort the keys of the global $email object, the make sure we have that and the
// recordset we'll get next in the same order.
$configtemplates = array_keys($email);
sort($configtemplates);
$ntemplates = count($configtemplates);

// Get the number of templates.
echo $output->paging_bar($ntemplates, $page, $perpage, $baseurl);

flush();

if ($manage) {
echo "A</br>";
    if (empty($templatesetid)) {
        // Display the list of templates.
        $templates = $DB->get_records('email_templateset', array(), 'templatesetname');
        echo $output->email_templatesets($templates, $linkurl);
    } else {
    }

} else {
    $templates = $DB->get_records('email_template', array('companyid' => $companyid, 'lang' => $lang),
                                    'name', '*', $page * $perpage, $perpage);
    // get heading
    if (empty($templatesetid)) {
        $prefix = "c." . $companyid;
    } else {
        $prefix = "t." . $templatesetid;
    }

    // Display the list.
    echo $output->email_templates($templates, $configtemplates, $lang, $prefix, $templatesetid, $page, $perpage);
    echo $output->paging_bar($ntemplates, $page, $perpage, $baseurl);
}

?>
<script>
$(".checkbox").change(function() {
	$.post("<?php echo $linkurl; ?>", {
		ajaxtemplate:this.value,
		ajaxvalue:this.checked
	});
});
</script>
<?php

echo $output->footer();
