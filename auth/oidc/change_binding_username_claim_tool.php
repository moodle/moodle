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
 * Change binding username claim tool page.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

use auth_oidc\form\change_binding_username_claim_tool_form1;
use auth_oidc\form\change_binding_username_claim_tool_form2;
use auth_oidc\preview;
use auth_oidc\process;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');

require_login();

$url = new moodle_url('/auth/oidc/change_binding_username_claim_tool.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('settings_page_change_binding_username_claim_tool', 'auth_oidc'));
$PAGE->set_title(get_string('settings_page_change_binding_username_claim_tool', 'auth_oidc'));

admin_externalpage_setup('auth_oidc_change_binding_username_claim_tool');

require_admin();

$iid = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

core_php_time_limit::raise(60 * 60); // 1 hour should be enough.
raise_memory_limit(MEMORY_HUGE);

if (empty($iid)) {
    $form1 = new change_binding_username_claim_tool_form1();
    if ($formdata = $form1->get_data()) {
        $iid = csv_import_reader::get_new_iid('changebindingusernameclaimtool');
        $cir = new csv_import_reader($iid, 'changebindingusernameclaimtool');

        $content = $form1->get_file_content('usernamefile');

        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        $csvloaderror = $cir->get_error();
        unset($content);

        if (!is_null($csvloaderror)) {
            throw new moodle_exception('csvloaderror', '', $url, $csvloaderror);
        }
    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading(get_string('change_binding_username_claim_tool', 'auth_oidc'));
        $bindingusernameclaimurl = new moodle_url('/auth/oidc/binding_username_claim.php');
        echo html_writer::tag('p', get_string('change_binding_username_claim_tool_description', 'auth_oidc',
            $bindingusernameclaimurl->out()));

        $form1->display();

        echo $OUTPUT->footer();
        exit;
    }
} else {
    $cir = new csv_import_reader($iid, 'changebindingusernameclaimtool');
}

// Test if columns ok.
$process = new process($cir);
$filecolumns = $process->get_file_columns();

$mform2 = new change_binding_username_claim_tool_form2(null,
    ['columns' => $filecolumns, 'data' => ['iid' => $iid, 'previewrows' => $previewrows]]);

// If a file has been uploaded, then process it.
if ($mform2->is_cancelled()) {
    $cir->cleanup(true);
    redirect($url);
} else if ($formdata = $mform2->get_data()) {
    // Print the header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('change_binding_username_claim_tool_result', 'auth_oidc'));

    $process->set_form_data($formdata);
    $process->process();

    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo html_writer::tag('p', join('<br />', $process->get_stats()));
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    exit;
}

// Print the header.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('change_binding_username_claim_tool', 'auth_oidc'));

$table = new preview($cir, $filecolumns, $previewrows);

echo html_writer::tag('div', html_writer::table($table), ['class' => 'flexible-wrap']);

if ($table->get_no_error()) {
    $mform2->display();
}

echo $OUTPUT->footer();

exit;
