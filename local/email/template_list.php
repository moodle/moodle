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
$search = optional_param('search', '', PARAM_CLEAN);

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
        $lang = $USER->lang;
    }
}

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

// Check we can actually do anything on this page.
if (empty($templatesetid)) {
    iomad::require_capability('local/email:list', $companycontext);
} else {
    iomad::require_capability('local/email:templateset_list', $companycontext);
}

$email = local_email::get_templates();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('template_list_title', 'local_email');
// Set the url.
$linkurl = new moodle_url('/local/email/template_list.php');
$manageurl = new moodle_url('/local/email/template_list.php', ['manage' => 1]);
$finishedurl = new moodle_url('/local/email/template_list.php', ['manage' => 1, 'finished' => 1]);

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->requires->jquery();

// get output renderer
$output = $PAGE->get_renderer('local_email');

// Set the companyid
$companyid = iomad::get_my_companyid($companycontext);
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
            if ($action == 'edit') {
                $linktext = format_string(get_string('edittemplateset', 'local_email'). " " . $templatesetinfo->templatesetname);
            } else {
                $linktext = format_string(get_string('deletetemplateset', 'local_email'). " " . $templatesetinfo->templatesetname);
            }
        }
    }
}
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir,
                                                    'perpage' => $perpage,
                                                    'lang' => $lang));
$returnurl = $baseurl;

// Are the templates being migrated?
if (!empty($CFG->local_email_templates_migrating)) {
    notice(get_string('templatesnoaccessigble', 'local_email'), new moodle_url('/blocks/iomad_company_admin/index.php'));
    die;
}

// check if ajax callback
if ($ajaxtemplate) {
    $parts = explode('.', $ajaxtemplate);
    list($type, $id, $managertype, $senttemplatename) = $parts;

    // Get the new value;
    $newvalue = 0;
    if ($ajaxvalue == 'false') {
        $newvalue = 1;
    }

    // What are we dealing with?
    if ($type == 'c') {
        $tablename = "email_template";
        $tablekey = "companyid";
    } else if ($type == 't') {
        $tablename = "email_templateset_templates";
        $tablekey = "templateset";
    }
    // What are we disabling?
    if ($managertype == 'e') {
        $tablefield = "disabled";
    }
    if ($managertype == 'em') {
        $tablefield = "disabledmanager";
    }
    if ($managertype == 'es') {
        $tablefield = "disabledsupervisor";
    }

    if (!is_numeric($senttemplatename)) {
        // Do the work.
        $DB->set_field($tablename, $tablefield, $newvalue, ['name' => $senttemplatename, $tablekey => $id]);
    } else {
        // Get all the records.
        $findsql = "SELECT et.id, et.name
                    FROM {" . $tablename . "} et
                    JOIN {tool_customlang} cl
                     ON (et.lang=cl.lang
                         AND cl.stringid = CONCAT(et.name, '_name'))
                    WHERE et.$tablekey = :id AND et.lang = :lang
                    ORDER BY cl.master";
        $sqlparams = ['id' => $id, 'lang' => $lang];

        // Set up the headings
        $templatenames = $DB->get_records_sql_menu($findsql,
                                                   $sqlparams,
                                                   $senttemplatename * $perpage,
                                                   $perpage);

        foreach ($templatenames as $templatename) {
            $DB->set_field($tablename, $tablefield, $newvalue, ['name' => $templatename, $tablekey => $id]);
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

$buttons = "";

// Deal with the page buttons.
if (empty($manage)) {
    $saveurl = new moodle_url('/local/email/template_list.php',
                                array('savetemplateset' => 1,
                                      'templatesetid' => $templatesetid));
    $manageurl = new moodle_url('/local/email/template_list.php',
                                  array('manage' => 1));
    $backbutton = '';
    if (!empty($templatesetid)) {
        if ($DB->get_record('email_templateset', array('id' => $templatesetid))) {
            $backurl = new moodle_url('/local/email/template_list.php', array('finished' => true, 'manage' => 1));
            $backbutton = $output->single_button($backurl, get_string('backtocompanytemplates', 'local_email'), 'get');
        }
    }
    if (empty($templatesetid)) {
        if (iomad::has_capability('local/email:templateset_list', $companycontext)) {
            $buttons .= $output->single_button($saveurl, get_string('savetemplateset', 'local_email'), 'get');
            $buttons .= $output->single_button($manageurl, get_string('managetemplatesets', 'local_email'), 'get');
            $buttons .= $backbutton;
        }
    } else {
            $buttons .= $output->single_button($saveurl, get_string('savetemplateset', 'local_email'), 'get');
            $buttons .= $output->single_button($manageurl, get_string('managetemplatesets', 'local_email'), 'get');
            $buttons .= $backbutton;
    }
} else {
    $buttons .= $output->single_button($linkurl, get_string('back'), 'get');
}
$PAGE->set_button($buttons);

// Start the page.
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
// Output the search form.
$searchform = new \local_email\forms\template_search_form();
$searchform->set_data(['search' => $search, 'manage' => $manage]);
$searchform->display();

// Sort the keys of the global $email object, the make sure we have that and the
// recordset we'll get next in the same order.
$configtemplates = array_keys($email);
sort($configtemplates);
$ntemplates = count($configtemplates);

if ($manage) {
    if (empty($templatesetid)) {
        if (!empty($search)) {
            $searchsql = $DB->sql_like('templatesetname', ':templatesetname', false);
            $sqlparams = ['templatesetname' => "%" . $search . "%"];
        } else {
            $searchsql = "1=1";
            $sqlparams = [];
        }

        // Display the list of templates.
        $table = new \local_email\tables\templatesets_table('email_templatessets_table');
        $table->set_sql('*', '{email_templateset}', $searchsql, $sqlparams);
        $table->define_baseurl($baseurl);
        $table->define_columns(['templatesetname', 'actions']);
        $table->define_headers([get_string('name'), '']);
        $table->no_sorting('actions');

        $table->out(30, true);

    }
} else {
    // Set up the prefix value for controls.
    if (empty($templatesetid)) {
        $prefix = "c." . $companyid;
    } else {
        $prefix = "t." . $templatesetid;
    }

    if (!empty($search)) {
        $searchsql = " AND " . $DB->sql_like('cl.master', ':templatename', false);
    } else {
        $searchsql = "";
    }

    // Set up the table.
    $selectsql = "et.*, cl.master AS templatename, :prefix AS prefix";
    if (empty($templatesetid)) {
        $fromsql = "{email_template} et
                    JOIN {tool_customlang} cl
                     ON (et.lang=cl.lang
                         AND cl.stringid = CONCAT(et.name, '_name'))";
        $wheresql = "et.companyid = :companyid AND et.lang = :lang $searchsql";
        $sqlparams = ['companyid' => $companyid, 'lang' => $lang, 'prefix' => $prefix, 'templatename' => "%" . $search . "%"];
    } else {
        $fromsql = "{email_templateset_templates} et
                    JOIN {tool_customlang} cl
                     ON (et.lang=cl.lang
                         AND cl.stringid = CONCAT(et.name, '_name'))";
        $wheresql = "et.templateset = :templatesetid AND et.lang = :lang $searchsql";
        $sqlparams = ['templatesetid' => $templatesetid, 'lang' => $lang, 'prefix' => $prefix, 'templatename' => "%" . $search . "%"];
    }

    // Set up the headings -- All this for the checkbox.
    $enabledrecs = $DB->get_records_sql_menu("SELECT et.id, et.disabled
                                              FROM $fromsql
                                              WHERE $wheresql
                                              ORDER BY cl.master",
                                              $sqlparams,
                                              $page * $perpage,
                                              $perpage);
    $manenabledrecs = $DB->get_records_sql_menu("SELECT et.id, et.disabledmanager
                                                 FROM $fromsql
                                                 WHERE $wheresql
                                                 ORDER BY cl.master",
                                                 $sqlparams,
                                                 $page * $perpage,
                                                 $perpage);
    $supenabledrecs = $DB->get_records_sql_menu("SELECT et.id, et.disabledsupervisor
                                                 FROM $fromsql
                                                 WHERE $wheresql
                                                 ORDER BY cl.master",
                                                 $sqlparams,
                                                 $page * $perpage,
                                                 $perpage);

    // We have to process these as $array[0] and $array["0"] are not being handled properly.
    foreach ($enabledrecs as $i => $enabledvalue ) {
        if ($enabledvalue) {
            $enabledrecs[$i] = "e";
        } else {
            $enabledrecs[$i] = "d";
        }
    }
    foreach ($manenabledrecs as $i => $enabledvalue ) {
        if ($enabledvalue) {
            $manenabledrecs[$i] = "e";
        } else {
            $manenabledrecs[$i] = "d";
        }
    }
    foreach ($supenabledrecs as $i => $enabledvalue ) {
        if ($enabledvalue) {
            $supenabledrecs[$i] = "e";
        } else {
            $supenabledrecs[$i] = "d";
        }
    }
    $enabledcounts = array_count_values($enabledrecs);  
    if (empty($enabledcounts["d"])) {
        $enabledcounts["d"] = 0;
    }
    if (empty($enabledcounts["e"])) {
        $enabledcounts["e"] = 0;
    }
    $manenabledcounts = array_count_values($manenabledrecs);    
    if (empty($manenabledcounts["d"])) {
        $manenabledcounts["d"] = 0;
    }
    if (empty($manenabledcounts["e"])) {
        $manenabledcounts["e"] = 0;
    }
    $supenabledcounts = array_count_values($supenabledrecs);
    if (empty($supenabledcounts["d"])) {
        $supenabledcounts["d"] = 0;
    }
    if (empty($supenabledcounts["e"])) {
        $supenabledcounts["e"] = 0;
    }
    if ($enabledcounts["d"] < $enabledcounts["d"] + $enabledcounts["e"]) {    
        $echecked = "";
    } else {
        $echecked = " checked ";
    }
    if ($manenabledcounts["d"] < $manenabledcounts["d"] + $manenabledcounts["e"]) {    
        $emchecked = "";
    } else {
        $emchecked = " checked ";
    }
    if ($supenabledcounts["d"] < $supenabledcounts["d"] + $supenabledcounts["e"]) {    
        $eschecked = "";
    } else {
        $eschecked = " checked ";
    }

    $headers = [get_string('emailtemplatename', 'local_email'),
                get_string('enable') . '&nbsp<label class="switch"><input class="checkbox enableallall" type="checkbox" ' . $echecked. ' value="' . "{$prefix}.e.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>",
                get_string('enable_manager', 'local_email') . '&nbsp<label class="switch"><input class="checkbox enableallmanager" type="checkbox" ' . $emchecked. ' value="' . "{$prefix}.em.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>",
                get_string('enable_supervisor', 'local_email') . '&nbsp<label class="switch"><input class="checkbox enableallsupervisor" type="checkbox" ' . $eschecked. ' value="' . "{$prefix}.es.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>",
                ''];
                //get_string('controls', 'local_email')];

    $columns = ['templatename',
                'enableuser',
                'enablemanager',
                'enablesupervisor',
                'actions'];

    // Display the list of templates.
    $usertemplates = local_email::get_user_templates(false);
    $table = new \local_email\tables\templates_table('email_templatess_table');
    $table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $table->define_baseurl($baseurl);
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->no_sorting('actions');
    $table->no_sorting('templatename');
    $table->no_sorting('enableuser');
    $table->no_sorting('enablemanager');
    $table->no_sorting('enablesupervisor');
    $table->sort_default_column = 'templatename';
    $table->column_style('enableuser', 'text-align', 'right');
    $table->column_style('enablemanager', 'text-align', 'right');
    $table->column_style('enablesupervisor', 'text-align', 'right');
    $table->out($perpage, true);

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