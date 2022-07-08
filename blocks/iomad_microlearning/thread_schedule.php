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
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

$threadid = required_param('threadid', PARAM_INT);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:edit_threads', $context);

$threadlist = new moodle_url('/blocks/iomad_microlearning/threads.php');

$linktext = get_string('threadschedule', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/thread_schedule.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_microlearning');

// Set the page heading.
$PAGE->set_heading($linktext);

// Deal with the link back to the main microlearning page.
$buttoncaption = get_string('threads', 'block_iomad_microlearning');
$buttonlink = new moodle_url('/blocks/iomad_microlearning/threads.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Check the thread is valid.
if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
    print_error('invalidthread', 'block_iomad_microlearning');
}

if ($deleteid && confirm_sesskey() && $confirm == md5($deleteid)) {
    // Check the thread is valid.
    if (!$threadinfo = $DB->get_record('microlearning_thread', array('id' => $threadid))) {
        print_error('invalidthread', 'block_iomad_microlearning');
    }

    // Get the list of thread ids which are to be removed..
    if (!empty($deleteid)) {
        microlearning::reset_thread_schedule($threadinfo);
        //$redirectmessage = get_string('threadscheduleresetok', 'block_iomad_microlearning');
        //redirect($threadlist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
        //die;
    }
}

// Get the nuggets for this thread.
$nuggets = $DB->get_records('microlearning_nugget', array('threadid' => $threadid), 'nuggetorder ASC');

// Set up the form.
$editform = new block_iomad_microlearning\forms\thread_schedule_form($PAGE->url, $threadid, $nuggets);

$nuggetschedules = microlearning::get_schedules($threadinfo, $nuggets);

$editform->set_data($nuggetschedules);

// Process the form.
if ($editform->is_cancelled()) {
    redirect($threadlist);
    die;
}
if ($scheduledata = $editform->get_data()) {

    // Are we resetting the schedules to default?
    if (!empty($scheduledata->resetallbutton)) {

        // No so show the confirmation question.
        echo $output->header();
        echo $output->heading(get_string('resetschedule', 'block_iomad_microlearning'));
        $optionsyes = array('threadid' => $threadid, 'deleteid' => $threadid, 'confirm' => md5($threadid), 'sesskey' => sesskey());
        echo $output->confirm(get_string('resetschedulecheckfull', 'block_iomad_microlearning', "'$threadinfo->name'"),
                              new moodle_url('thread_schedule.php', $optionsyes), 'threads.php');
        echo $output->footer();
        die;
    }

    // Update the schedules.
    microlearning::update_thread_schedule($scheduledata);
    $redirectmessage = get_string('threadscheduleupdatedok', 'block_iomad_microlearning');
    redirect($threadlist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    die;
}

// Display the form.
echo $output->header();

$editform->display();

echo $output->footer();
