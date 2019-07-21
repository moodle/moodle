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
 * Script to let a user create a department for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
require_once($CFG->dirroot."/lib/tablelib.php");
require_once('threads_table.php');


$threadid = required_param('threadid', 0, PARAM_INT);
$nuggetid = optional_param('nuggetid', 0, PARAM_INT);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$action = optional_param('action', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:edit_nuggets', $context);

// Deal with any actions.
if (!empty($action) && !empty($nuggetid)) {
    if ($action == 'up') {
        microlearning::up_nugget($nuggetid);
    } else if ($action == 'down') {
        microlearning::down_nugget($nuggetid);
    }
}

$urlparams = array('threadid' => $threadid, 'nuggetid' => $nuggetid, 'page' => $page);
$companylist = new moodle_url('/my', $urlparams);

$linktext = get_string('nuggets', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/nuggets.php', $urlparams);

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_microlearning');

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Delete any valid nuggets.
if ($deleteid) {
    // Check the thread is valid.
    if (!$nuggetinfo = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
        print_error('invalidnugget', 'block_iomad_microlearning');
    }

    // Have we confirmed it?
    if(confirm_sesskey() && $confirm == md5($deleteid)) {
        // Get the list of thread ids which are to be removed..
        if (!empty($deleteid)) {
            microlearning::delete_nugget($deleteid);
            redirect($linkurl);
        }
    } else {
        // No so show the confirmation question.
        echo $output->header();
        echo $output->heading(get_string('deletenugget', 'block_iomad_microlearning'));
        $optionsyes = array('deleteid' => $nuggetid, 'confirm' => md5($nuggetid), 'sesskey' => sesskey());
        echo $output->confirm(get_string('deletenuggetcheckfull', 'block_iomad_microlearning', "'$nuggetinfo->name'"),
                              new moodle_url('nuggets.php', $optionsyes), 'threads.php');
    }
    echo $output->footer();
    die;
}

// Create the thread table.
$nuggettable = new block_microlearning_nugget_table('block_microlearning_nuggets');
$sqlparams = array('threadid' => $threadid);
$selectsql = "*";
$fromsql = "{microlearning_nugget}";
$wheresql = "threadid = :threadid";

$headers = array();
$headers['name'] = get_string('threadname', 'block_iomad_microlearning');
$headers['order'] = get_string('active', 'block_iomad_microlearning');
$headers['timecreated'] = get_strng('timecreated', 'block_iomad_microlearning');
$headers['updown'] = get_string('updown');
$headers['actions'] = get_string('actions', 'block_iomad_microlearning');

$threadtable->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$threadtable->define_base_url($linkurl);
$threadtable->define_columns(array('name', 'order', 'timecreated', 'updown', 'actions'));
$threadtable->define_headers($headers);
$threadtable->no_sorting(array('name', 'order', 'updown', 'actions'));
$threadtable->sort_default_column='order';

echo $output->header();

$threadtable->out(30);

echo $output->footer();
