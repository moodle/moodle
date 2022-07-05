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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('lib.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$search       = optional_param('search', '', PARAM_CLEAN);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_companies, PARAM_INT);        // How many per page.

$context = context_system::instance();
require_login();

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Set the name for the page.
$linktext = get_string('blocktitle', 'block_iomad_microlearning');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/company_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

// Are we performing any actions?
if ($delete and confirm_sesskey()) {

    iomad::require_capability('block/iomad_microlearning:thread_delete', $context);

    $thread = $DB->get_record('microlearning_thread', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = $thread->name;
        echo $OUTPUT->heading(get_string('deletethread', 'block_iomad_microlearning'), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('deletethreadcheckfull', 'block_iomad_microlearning', "'$name'"),
                              new moodle_url('microlearning.php', $optionsyes), 'microlearning.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $transaction = $DB->start_delegated_transaction();

        if ($DB->delete_records('microlearning_thread', array('id' => $delete))) {

            // Delete associated nuggets.
            $DB->delete_records('microlearning_nugget', array('threadid' => $delete));

            // Delete associated nugget schedules.
            $DB->delete_records('microlearning_nugget_sched', array('threadid' => $delete));

            // Delete associated nuggets.
            $DB->delete_records('microlearning_nugget_user', array('threadid' => $delete));

            $transaction->allow_commit();
            redirect($returnurl);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $thread->name));
            die;
        }

        $transaction->rollback();
    }
}

echo $OUTPUT->header();

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_microlearning:thread_view', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Get the micro learning threads
$microthreads = microthreads::get_threads($companyid, $search);
$objectcount = count($microthreads);

if (!empty($microthreads)) {
    $table = new html_table();
    $table->head = array(get_string('name'), '');
    $table->align('center', 'center');
    $table->width = '95%';

    foreach ($microthreads as $microthread) {
        $deleteurl = '';
        $assignurl = '';
        $editurl = '';
        $deletebutton = '';
        $assignbutton = '';
        $editbutton = '';
        $linkparams = $params;
    }
}
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

echo $OUTPUT->footer();
