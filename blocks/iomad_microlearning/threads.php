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

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
require_once($CFG->dirroot."/lib/tablelib.php");
require_once('thread_table.php');


$threadid = optional_param('threadid', 0, PARAM_INT);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$cloneid = optional_param('cloneid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$search = optional_param('search', '', PARAM_ALPHANUM);
$page = optional_param('page', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:edit_threads', $context);

$urlparams = array();
$urlparams['search'] = $search;
$companylist = new moodle_url('/blocks/iomad_company_admin/index.php', $urlparams);

$linktext = get_string('threads', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/threads.php', $urlparams);

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_microlearning');

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Delete any valid threads.
if ($deleteid) {
    // Check the thread is valid.
    if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $deleteid))) {
        print_error('invalidthread', 'block_iomad_microlearning');
    }

    // Have we confirmed it?
    if(confirm_sesskey() && $confirm == md5($deleteid)) {
        // Get the list of thread ids which are to be removed..
        if (!empty($deleteid)) {
            // Check if thread is valid.
            if (microlearning::check_valid_thread($companyid, $deleteid)) {
                // If it is then delete it.
                microlearning::delete_thread($deleteid, $deleteid);
                redirect($linkurl);
            }
        }
    } else {
        // No so show the confirmation question.
        echo $output->header();
        echo $output->heading(get_string('deletethread', 'block_iomad_microlearning'));
        $optionsyes = array('deleteid' => $deleteid, 'confirm' => md5($deleteid), 'sesskey' => sesskey());
        echo $output->confirm(get_string('deletethreadcheckfull', 'block_iomad_microlearning', "'$threadinfo->name'"),
                              new moodle_url('threads.php', $optionsyes), 'threads.php');
    }
    echo $output->footer();
    die;
}

// clone any valid threads.
if ($cloneid) {
    // Check the thread is valid.
    if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $cloneid))) {
        print_error('invalidthread', 'block_iomad_microlearning');
    }

    // Have we confirmed it?
    if(confirm_sesskey() && $confirm == md5($cloneid)) {
        // Get the list of thread ids which are to be removed..
        if (!empty($cloneid)) {
            // Check if thread is valid.
            if (microlearning::check_valid_thread($companyid, $cloneid)) {
                // If it is then delete it.
                microlearning::clone_thread($cloneid, $cloneid);
                redirect($linkurl);
            }
        }
    } else {
        // No so show the confirmation question.
        echo $output->header();
        echo $output->heading(get_string('clonethread', 'block_iomad_microlearning'));
        $optionsyes = array('cloneid' => $cloneid, 'confirm' => md5($cloneid), 'sesskey' => sesskey());
        echo $output->confirm(get_string('clonethreadcheckfull', 'block_iomad_microlearning', "'$threadinfo->name'"),
                              new moodle_url('threads.php', $optionsyes), 'threads.php');
    }
    echo $output->footer();
    die;
}

// Create the thread table.
$threadtable = new block_iomad_microlearning_thread_table('block_microlearning_threads');
$sqlparams = array('companyid' => $companyid);
$selectsql = "*";
$fromsql = "{microlearning_thread}";
$wheresql = "companyid = :companyid";
if (!empty($search)) {
    $wheresql .= " AND name like :search ";
    $sqlparams['search'] = "%search%";
}

$headers = array(get_string('threadname', 'block_iomad_microlearning'),
                 get_string('active', 'block_iomad_microlearning'),
                 get_string('startdate', 'block_iomad_microlearning'),
                 get_string('timecreated', 'block_iomad_microlearning'),
                 get_string('actions'));
$columns = array('name',
                 'active',
                 'startdate',
                 'timecreated',
                 'actions');

$threadtable->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$threadtable->define_baseurl($linkurl);
$threadtable->define_columns($columns);
$threadtable->define_headers($headers);
$threadtable->no_sorting('actions');
$threadtable->sort_default_column='name';

echo $output->header();

echo $output->threads_list_buttons(new moodle_url('thread_edit.php'), new moodle_url('thread_import.php'), new moodle_url('groups.php'), new moodle_url('group_import.php'));

$threadtable->out(30, true);

echo $output->footer();
