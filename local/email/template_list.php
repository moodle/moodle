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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( 'local_lib.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once( 'config.php');
require_once( 'lib.php');
require_once($CFG->dirroot . '/local/iomad/lib/user.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$lang         = optional_param('lang', '', PARAM_LANG);
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

// Deal with the default language.
if (empty($lang)) {
    if (isset($SESSION->lang)) {
        $lang = $SESSION->lang;
    } else {
        $lang = $CFG->lang;
    }
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
$manageurl = new moodle_url('/local/email/template_list.php', ['manage' => 1]);
$finishedurl = new moodle_url('/local/email/template_list.php', ['manage' => 1, 'finished' => 1]);

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->requires->jquery();

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
$company = new company($companyid);

// Set the page heading.
if (empty($templatesetid)) {
    if (empty($manage)) {
        $linktext = get_string('email_templates_for', 'local_email', $company->get_name());
    } else {
            $linktext = get_string('emailtemplatesets', 'local_email');
    }
} else {
    if (empty($action)) {
        if ($templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            $linktext = get_string('email_templates_for', 'local_email', $templatesetinfo->templatesetname);
        } else {
            $linktext = get_string('email_templates_for', 'local_email', $company->get_name());
        }
    } else {
        if ($templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            $linktext = get_string('deletetemplateset', 'local_email'). " " . $templatesetinfo->templatesetname;
        }
    }
}
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir,
                                                    'perpage' => $perpage,
                                                    'lang' => $lang));
$returnurl = $baseurl;

// check if ajax callback
if ($ajaxtemplate) {
    $parts = explode('.', $ajaxtemplate);
    list($type, $id, $managertype, $templatename) = $parts;

    // Get the installed languages.
    $alllangs = get_string_manager()->get_list_of_translations(true);
    foreach ($alllangs as $installedlang => $drop) {
        if ($type == 'c') {
            if (!is_numeric($templatename)) {
                // dealing with a company email template.
                if (!$templateinfos = $DB->get_records('email_template',
                    array('name' => $templatename,
                          'companyid' => $id,
                          'lang' => $installedlang))) {
                    $newtemplate = new stdclass();
                    $newtemplate->companyid = $id;
                    $newtemplate->name = $templatename;
                    $newtemplate->subject = get_string($templatename.'_subject', 'local_email');
                    $newtemplate->body = get_string($templatename.'_body', 'local_email');
                    $newtemplate->disabled = 0;
                    $newtemplate->disabledmanager = 0;
                    $newtemplate->disabledsupervisor = 0;
                    $newtemplate->lang = $installedlang;

                    // What are we disabling?
                    if ($managertype == 'e') {
                        $newtemplate->disabled = 1;
                    }
                    if ($managertype == 'em') {
                        $newtemplate->disabledmanager = 1;
                    }
                    if ($managertype == 'es') {
                        $newtemplate->disabledsupervisor = 1;
                    }
                    $DB->insert_record('email_template', $newtemplate);
                } else {
                    $dbparams = array('companyid' => $id,
                                      'templatename' => $templatename,
                                      'ajaxvalue' => 0,
                                      'installedlang' => $installedlang);
                    if ($ajaxvalue == 'false') {
                        $dbparams['ajaxvalue'] = 1;
                    }
                    if ($managertype == 'e') {
                        $DB->execute("UPDATE {email_template}
                                      SET disabled = :ajaxvalue
                                      WHERE companyid = :companyid
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                    if ($managertype == 'em') {
                        $DB->execute("UPDATE {email_template}
                                      SET disabledmanager = :ajaxvalue
                                      WHERE companyid = :companyid
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                    if ($managertype == 'es') {
                        $DB->execute("UPDATE {email_template}
                                      SET disabledsupervisor = :ajaxvalue
                                      WHERE companyid = :companyid
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                }
            } else {
                // Sort the keys of the global $email object, the make sure we have that and the
                // recordset we'll get next in the same order.
                $configtemplates = array_keys($email);
                sort($configtemplates);
                $ntemplates = count($configtemplates);
                $start = $templatename * $perpage;
                $end = ($templatename + 1) * $perpage;
                $count = 0;
                foreach ($configtemplates as $configtemplatename) {
                    if ($count < $start) {
                        $count++;
                        continue;
                    }
                    if ($count == $end) {
                        break;
                    }
                    // dealing with a company email template.
                    if (!$templateinfos = $DB->get_records('email_template',
                        array('name' => $configtemplatename,
                              'companyid' => $id,
                              'lang' => $installedlang))) {
                        $newtemplate = new stdclass();
                        $newtemplate->companyid = $id;
                        $newtemplate->name = $configtemplatename;
                        $newtemplate->subject = get_string($configtemplatename.'_subject', 'local_email');
                        $newtemplate->body = get_string($configtemplatename.'_body', 'local_email');
                        $newtemplate->disabled = 0;
                        $newtemplate->disabledmanager = 0;
                        $newtemplate->disabledsupervisor = 0;
                        $newtemplate->lang = $installedlang;

                        // What are we disabling?
                        if ($managertype == 'e') {
                            $newtemplate->disabled = 1;
                        }
                        if ($managertype == 'em') {
                            $newtemplate->disabledmanager = 1;
                        }
                        if ($managertype == 'es') {
                            $newtemplate->disabledsupervisor = 1;
                        }
                        $DB->insert_record('email_template', $newtemplate);
                    } else {
                        $dbparams = array('companyid' => $id,
                                          'templatename' => $configtemplatename,
                                          'ajaxvalue' => 0,
                                          'installedlang' => $installedlang);
                        if ($ajaxvalue == 'false') {
                            $dbparams['ajaxvalue'] = 1;
                        }
                        if ($managertype == 'e') {
                            $DB->execute("UPDATE {email_template}
                                          SET disabled = :ajaxvalue
                                          WHERE companyid = :companyid
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                        if ($managertype == 'em') {
                            $DB->execute("UPDATE {email_template}
                                          SET disabledmanager = :ajaxvalue
                                          WHERE companyid = :companyid
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                        if ($managertype == 'es') {
                            $DB->execute("UPDATE {email_template}
                                          SET disabledsupervisor = :ajaxvalue
                                          WHERE companyid = :companyid
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                    }
                    $count++;
                }
            }
        } else if ($type == 't') {
            // dealing with a Template email template.
            if (!is_numeric($templatename)) {
                if (!$templateinfos = $DB->get_records('email_templateset_templates',
                    array('name' => $templatename,
                          'templateset' => $id,
                          'lang' => $installedlang))) {
                    $newtemplate = new stdclass();
                    $newtemplate->templateset = $id;
                    $newtemplate->name = $templatename;
                    $newtemplate->subject = get_string($templatename.'_subject', 'local_email');
                    $newtemplate->body = get_string($templatename.'_body', 'local_email');
                    $newtemplate->disabled = 0;
                    $newtemplate->disabledmanager = 0;
                    $newtemplate->disabledsupervisor = 0;
                    $newtemplate->lang = $installedlang;

                    // What are we disabling?
                    if ($managertype == 'e') {
                        $newtemplate->disabled = 1;
                    }
                    if ($managertype == 'em') {
                        $newtemplate->disabledmanager = 1;
                    }
                    if ($managertype == 'es') {
                        $newtemplate->disabledsupervisor = 1;
                    }
                    $DB->insert_record('email_templateset_templates', $newtemplate);
                } else {
                    $dbparams = array('templateset' => $id,
                                      'templatename' => $templatename,
                                      'ajaxvalue' => 0,
                                      'installedlang' => $installedlang);
                    if ($ajaxvalue == 'false') {
                        $dbparams['ajaxvalue'] = 1;
                    }
                    if ($managertype == 'e') {
                        $DB->execute("UPDATE {email_templateset_templates}
                                      SET disabled = :ajaxvalue
                                      WHERE templateset = :templateset
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                    if ($managertype == 'em') {
                        $DB->execute("UPDATE {email_templateset_templates}
                                      SET disabledmanager = :ajaxvalue
                                      WHERE templateset = :templateset
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                    if ($managertype == 'es') {
                        $DB->execute("UPDATE {email_templateset_templates}
                                      SET disabledsupervisor = :ajaxvalue
                                      WHERE templateset = :templateset
                                      AND name = :templatename
                                      AND lang = :installedlang",
                                      $dbparams);
                    }
                }
            } else {
                // Sort the keys of the global $email object, the make sure we have that and the
                // recordset we'll get next in the same order.
                $configtemplates = array_keys($email);
                sort($configtemplates);
                $ntemplates = count($configtemplates);
                $start = $templatename * $perpage;
                $end = ($templatename + 1) * $perpage;
                $count = 0;
                foreach ($configtemplates as $configtemplatename) {
                    if ($count < $start) {
                        $count++;
                        continue;
                    }
                    if ($count == $end) {
                        break;
                    }
                    // dealing with a company email template.
                    if (!$templateinfos = $DB->get_records('email_templateset_templates',
                        array('name' => $configtemplatename,
                          'templateset' => $id,
                          'lang' => $installedlang))) {
                        $newtemplate = new stdclass();
                        $newtemplate->templateset = $id;
                        $newtemplate->name = $configtemplatename;
                        $newtemplate->subject = get_string($configtemplatename.'_subject', 'local_email');
                        $newtemplate->body = get_string($configtemplatename.'_body', 'local_email');
                        $newtemplate->disabled = 0;
                        $newtemplate->disabledmanager = 0;
                        $newtemplate->disabledsupervisor = 0;
                        $newtemplate->lang = $installedlang;

                        // What are we disabling?
                        if ($managertype == 'e') {
                            $newtemplate->disabled = 1;
                        }
                        if ($managertype == 'em') {
                            $newtemplate->disabledmanager = 1;
                        }
                        if ($managertype == 'es') {
                            $newtemplate->disabledsupervisor = 1;
                        }
                        $DB->insert_record('email_templateset_templates', $newtemplate);
                    } else {
                        $dbparams = array('templateset' => $id,
                                          'templatename' => $configtemplatename,
                                          'ajaxvalue' => 0,
                                          'installedlang' => $installedlang);
                        if ($ajaxvalue == 'false') {
                            $dbparams['ajaxvalue'] = 1;
                        }
                        if ($managertype == 'e') {
                            $DB->execute("UPDATE {email_templateset_templates}
                                          SET disabled = :ajaxvalue
                                          WHERE templateset = :templateset
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                        if ($managertype == 'em') {
                            $DB->execute("UPDATE {email_templateset_templates}
                                          SET disabledmanager = :ajaxvalue
                                          WHERE templateset = :templateset
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                        if ($managertype == 'es') {
                            $DB->execute("UPDATE {email_templateset_templates}
                                          SET disabledsupervisor = :ajaxvalue
                                          WHERE templateset = :templateset
                                          AND name = :templatename
                                          AND lang = :installedlang",
                                          $dbparams);
                        }
                    }
                    $count++;
                }
            }
        }
    }

    // Don't process any more.
    die;
}

//  Deal with any deletes.
if ($action == 'delete' && confirm_sesskey()) {
    if ($confirm != md5($templatesetid)) {
        echo $output->header();

        if (!$templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            print_error('templatesetnotfound', 'local_email');
        }

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
        if ($SESSION->currenttemplatesetid == $templatesetid) {
            unset($SESSION->currenttemplatesetid);
        }
        redirect($manageurl,get_string('templatesetdeleted', 'local_email'), null, \core\output\notification::NOTIFY_SUCCESS);
        die;
    }
} else if ($action == 'setdefault' && confirm_sesskey()) {
    if ($confirm != md5($templatesetid)) {
        echo $output->header();

        if (!$templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            print_error('templatesetnotfound', 'local_email');
        }

        $optionsyes = array('templatesetid' => $templatesetid, 'confirm' => md5($templatesetid), 'sesskey' => sesskey(), 'action' => 'setdefault');
        echo $OUTPUT->confirm(get_string('setdefaulttemplatesetfull', 'local_email', "'" . $templatesetinfo->templatesetname ."'"),
                              new moodle_url('/local/email/template_list.php', $optionsyes),
                                             '/local/email/template_list.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Set the template set as default.
        $DB->set_field('email_templateset', 'isdefault', 0, []);
        $DB->set_field('email_templateset', 'isdefault', 1, ['id' => $templatesetid]);
        redirect($finishedurl, get_string('templatesetsetdefault', 'local_email'), null, \core\output\notification::NOTIFY_SUCCESS);
        die;
    }
} else if ($action == 'unsetdefault' && confirm_sesskey()) {
    if ($confirm != md5($templatesetid)) {
        echo $output->header();

        if (!$templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid))) {
            print_error('templatesetnotfound', 'local_email');
        }

        $optionsyes = array('templatesetid' => $templatesetid, 'confirm' => md5($templatesetid), 'sesskey' => sesskey(), 'action' => 'unsetdefault');
        echo $OUTPUT->confirm(get_string('unsetdefaulttemplatesetfull', 'local_email', "'" . $templatesetinfo->templatesetname ."'"),
                              new moodle_url('/local/email/template_list.php', $optionsyes),
                                             '/local/email/template_list.php');
        echo $OUTPUT->footer();
        die;
    } else {
        // Set the template set as default.
        $DB->set_field('email_templateset', 'isdefault', 0, []);
        redirect($finishedurl, get_string('templatesetsetdefault', 'local_email'), null, \core\output\notification::NOTIFY_SUCCESS);
        die;
    }
}

// Set up the form.
$mform = new \local_email\forms\company_templateset_save_form($linkurl, $companyid, $templatesetid);

if ($data = $mform->get_data()) {
    // Save the template.
    $templatesetid = $DB->insert_record('email_templateset', array('templatesetname' => $data->templatesetname));
    $emailtemplates = $DB->get_records('email_template', array('companyid' => $companyid));
    foreach ($emailtemplates as $emailtemplate) {
        $emailtemplate->templateset = $templatesetid;
        $DB->insert_record('email_templateset_templates', $emailtemplate);
    }
    redirect($linkurl, get_string('emailtemplatesetsaved', 'local_email'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $output->header();
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
if (empty($templatesetid)) {
    iomad::require_capability('local/email:list', $context);
} else {
    iomad::require_capability('local/email:templateset_list', $context);
}

if (empty($manage)) {
    // output the save button.
    $saveurl = new moodle_url('/local/email/template_list.php',
                                array('savetemplateset' => 1,
                                      'templatesetid' => $templatesetid));
    $manageurl = new moodle_url('/local/email/template_list.php',
                                  array('manage' => 1));
    if (!empty($templatesetid)) {
        if ($DB->get_record('email_templateset', array('id' => $templatesetid))) {
            $backurl = new moodle_url('/local/email/template_list.php', array('finished' => true, 'manage' => 1));
        } else {
            $backurl = '';
        }
    } else {
        $backurl = '';
    }
    if (empty($templatesetid)) {
        if (iomad::has_capability('local/email:templateset_list', $context)) {
            echo $output->templateset_buttons($saveurl, $manageurl, $backurl);
        }
    } else {
        echo $output->templateset_buttons($saveurl, $manageurl, $backurl);
    }
}

// Sort the keys of the global $email object, the make sure we have that and the
// recordset we'll get next in the same order.
$configtemplates = array_keys($email);
sort($configtemplates);
$ntemplates = count($configtemplates);

if ($manage) {
    if (empty($templatesetid)) {
        // Display the list of templates.
        $table = new \local_email\tables\templatesets_table('email_templatessets_table');
        $table->set_sql('*', '{email_templateset}', '1=1', []);
        $table->define_baseurl($baseurl);
        $table->define_columns(['templatesetname', 'actions']);
        $table->define_headers([get_string('name'), '']);
        $table->no_sorting('actions');

        echo '<a class="btn btn-primary" href="'.$linkurl.'">' .
                                           get_string('back') . '</a>';
        $table->out(30, true);

    }
} else {
    // Get the number of templates.
    if (empty($templatesetid)) {
        $templates = $DB->get_records('email_template',
                                      array('companyid' => $companyid, 'lang' => $lang),
                                      'name', '*');
    } else {
        $templates = $DB->get_records('email_templateset_templates',
                                      array('templateset' => $templatesetid, 'lang' => $lang),
                                      'name', '*');
    }
    // get heading
    if (empty($templatesetid)) {
        $prefix = "c." . $companyid;
    } else {
        $prefix = "t." . $templatesetid;
    }

    // Display the list.
    echo $output->paging_bar($ntemplates, $page, $perpage, $baseurl);
    echo $output->email_templates($templates, $configtemplates, $lang, $prefix, $templatesetid, $page, $perpage);
    echo $output->paging_bar($ntemplates, $page, $perpage, $baseurl);
}

?>
<script>
$(".checkbox").change(function() {
    var inputElems = document.getElementsByTagName("input")
    $.post("<?php echo $linkurl; ?>", {
        ajaxtemplate:this.value,
        ajaxvalue:this.checked
    });
    var matched = this.value;
    if(this.checked) {
        if(this.classList.contains("enableall")) {
            $(".enableallall").prop("checked", this.checked);
        }
        if(this.classList.contains("enablemanager")) {
            $(".enableallmanager").prop("checked", this.checked);
        }
        if(this.classList.contains("enablesupervisor")) {
            $(".enableallsupervisor").prop("checked", this.checked);
        }
    } else {
        if(this.classList.contains("enableall")) {
            var checked = 0;
            for (var i=0; i<inputElems.length; i++) {
                if (inputElems[i].type === "checkbox" && inputElems[i].classList.contains('enableall')) {
                    if (inputElems[i].checked) {
                        checked++;
                    }
                }
            }
            if (checked == 0) {
                 $(".enableallall").prop("checked", "");
            }
        }
        if(this.classList.contains("enablemanager")) {
            var checked = 0;
            for (var i=0; i<inputElems.length; i++) {
                if (inputElems[i].type === "checkbox" && inputElems[i].classList.contains('enablemanager')) {
                    if (inputElems[i].checked) {
                        checked++;
                    }
                }
            }
            if (checked == 0) {
                 $(".enableallmanager").prop("checked", "");
            }
        }
        if(this.classList.contains("enablesupervisor")) {
            var checked = 0;
            for (var i=0; i<inputElems.length; i++) {
                if (inputElems[i].type === "checkbox" && inputElems[i].classList.contains('enablesupervisor')) {
                    if (inputElems[i].checked) {
                        checked++;
                    }
                }
            }
            if (checked == 0) {
                 $(".enablesupervisorall").prop("checked", "");
            }
        }
    }
    if (matched.match(/\.e\.\d+$/) != null) {
        // Get all of the entries and change them.
        $(".enableall").prop("checked", this.checked);
    }
    if (matched.match(/\.em\.\d+$/) != null) {
        // Get all of the entries and change them.
        $(".enablemanager").prop("checked", this.checked);
    }
    if (matched.match(/\.es\.\d+$/) != null) {
        // Get all of the entries and change them.
        $(".enablesupervisor").prop("checked", this.checked);
    }
});
</script>
<?php

echo $output->footer();
