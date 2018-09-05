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

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$lang         = optional_param('lang', 'en', PARAM_LANG);

$context = context_system::instance();
require_login();

$email = local_email_get_templates();

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

// Set the page heading.
$PAGE->set_heading($linktext);

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid to bypass the company select form if possible.
if (!empty($SESSION->currenteditingcompany)) {
    $companyid = $SESSION->currenteditingcompany;
} else if (!empty($USER->company)) {
    $companyid = company_user::companyid();
} else if (!iomad::has_capability('local/email:list', context_system::instance())) {
    print_error('There has been a configuration error, please contact the site administrator');
} else {
    redirect(new moodle_url('/my'),
                            'Please select a company from the dropdown first');
}

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir,
                                                    'perpage' => $perpage));
$returnurl = $baseurl;

if ($delete and confirm_sesskey()) {
    // Delete a selected override template, after confirmation.

    iomad::require_capability('local/email:delete', $context);

    $template = $DB->get_record('email_template', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = $template->name;
        echo $OUTPUT->heading(get_string('delete_template', $block), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('delete_template_checkfull', $block, "'$name'"),
              new moodle_url('template_list.php', $optionsyes), 'template_list.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $transaction = $DB->start_delegated_transaction();

        if ( $DB->delete_records('email_template', array('id' => $delete)) ) {
            $transaction->allow_commit();
            redirect($returnurl);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $template->name));
            die;
        }

        $transaction->rollback();
    }

}
echo $OUTPUT->header();

$company = new company($companyid);
echo '<h3>' . get_string('email_templates_for', $block, $company->get_name()) . '</h3>';

// Check we can actually do anything on this page.
iomad::require_capability('local/email:list', $context);

// Deal with the language selector.
$langs = get_string_manager()->get_list_of_translations();
$s = new single_select($PAGE->url, 'lang', $langs);
$s->label = get_string('language') . $OUTPUT->help_icon('language', 'local_email') . '&nbsp';
$s->class = 'langselector';
$s->selected = $lang;
echo $OUTPUT->box($OUTPUT->render($s), 'langselectorbox');

// Get the number of templates.
$objectcount = $DB->count_records('email_template');
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

// Sort the keys of the global $email object, the make sure we have that and the
// recordset we'll get next in the same order.
$configtemplates = array_keys($email);
sort($configtemplates);
$ntemplates = count($configtemplates);

// Returns true if user is allowed to send emails using a particular template.
function allow_sending_to_template($templatename) {
    return in_array($templatename, array('advertise_classroom_based_course'));
}

function create_default_template_row($templatename, $strdefault, $stradd, $strsend, $lang) {
    global $PAGE;

    $deletebutton = "";

    if ($stradd) {
        $editbutton = "<a class='btn' href='" . new moodle_url('template_edit_form.php',
                       array("templatename" => $templatename, 'lang' => $lang)) . "'>$stradd</a>";
    } else {
        $editbutton = "";
    }
    if ($strsend && allow_sending_to_template($templatename) ) {
        $sendbutton = "<a class='btn' href='" . new moodle_url('template_send_form.php',
                       array("templatename" => $templatename, 'lang' => $lang)) . "'>$strsend</a>";
    } else {
        $sendbutton = "";
    }

    $name = get_string($templatename . '_name', 'local_email') . "</br>(" . $templatename. ")";
    $description = get_string($templatename . '_description', 'local_email');
    return array ($name,
                  $description,
                  $editbutton . '&nbsp;' .
                  $deletebutton . '&nbsp;' .
                  $sendbutton);
}

if ($templates = $DB->get_recordset('email_template', array('companyid' => $companyid, 'lang' => $lang),
                                    'name', '*', $page, $perpage)) {
    if (iomad::has_capability('local/email:edit', $context)) {
        $stredit = get_string('edit');
    } else {
        $stredit = null;
    }
    if (iomad::has_capability('local/email:add', $PAGE->context)) {
        $stradd = get_string('add_template_button', $block);
    } else {
        $stradd = null;
    }
    if (iomad::has_capability('local/email:delete', $context)) {
        $strdelete = get_string('delete_template_button', $block);
    } else {
        $strdelete = null;
    }
    if (iomad::has_capability('local/email:send', $context)) {
        $strsend = get_string('send_button', $block);
    } else {
        $strsend = null;
    }
    $stroverride = get_string('custom', $block);
    $strdefault = get_string('default', $block);

    $table = new html_table();
    $table->id = 'ReportTable';
    $table->head = array (get_string('emailtemplatename', $block),
                          get_string('description'),
                          get_string('controls', $block));
    $table->align = array ("left", "left", "center");

    $i = 0;

    foreach ($templates as $template) {
        while ($i < $ntemplates && $configtemplates[$i] < $template->name) {
            $table->data[] = create_default_template_row($configtemplates[$i], $strdefault,
                                                         $stradd, $strsend, $lang);
            $i++;
        }

        if ($strdelete) {
            $deletebutton = "<a class='btn' href='" . new moodle_url('template_list.php',
                          array("delete" => $template->id, 'lang' => $lang, 'sesskey' => sesskey())) ."'>$strdelete</a>";
        } else {
            $deletebutton = "";
        }

        if ($stredit) {
            $editbutton = "<a class='btn' href='" . new moodle_url('template_edit_form.php',
                          array("templateid" => $template->id, 'lang' => $lang)) . "'>$stredit</a>";
        } else {
            $editbutton = "";
        }

        if ($strsend && allow_sending_to_template($templatename)) {
            $sendbutton = "<a class='btn' href='" . new moodle_url('template_send_form.php',
                          array("templateid" => $template->id, 'lang' => $lang)) . "'>$strsend</a>";
        } else {
            $sendbutton = "";
        }

        $table->data[] = array ("$template->name",
                            $stroverride,
                            $editbutton . '&nbsp;' .
                            $deletebutton . '&nbsp;' .
                            $sendbutton
                            );

        // Need to increase the counter to skip the default template.
        $i++;
    }

    while ($i < $ntemplates) {
        $table->data[] = create_default_template_row($configtemplates[$i],
                          $strdefault, $stradd, $strsend, $lang);
        $i++;
    }

    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
    }

    $templates->close();
}

echo $OUTPUT->footer();
