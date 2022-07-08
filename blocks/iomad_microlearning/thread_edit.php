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

$threadid = optional_param('threadid', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:edit_threads', $context);

$threadlist = new moodle_url('/blocks/iomad_microlearning/threads.php');

$linktext = get_string('editthread', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/thread_edit.php');

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


// Set up the form.
$editform = new block_iomad_microlearning\forms\thread_edit_form();

// Set up the initial forms.
if (!empty($threadid)) {
    $thread = $DB->get_record('microlearning_thread', array('id' => $threadid));

    // Sort the hour stuff out.
    $hours = $thread->message_time;
    $h = floor($hours / 3600);
    $m = floor(($hours / 60) % 60);
    $thread->hour = $h;
    $thread->minute = $m;
    $editform->set_data($thread);
} else {
    $editform->set_data(array('companyid' => $companyid));
}

// Process the form.
if ($editform->is_cancelled()) {
    redirect($threadlist);
    die;
} else if ($createdata = $editform->get_data()) {

    // Deal with leading/trailing spaces.
    $createdata->name = trim($createdata->name);

    // Create or update the department.
    if (empty($createdata->id)) {
        // We are creating a new thread.
        // Make sure defaults are OK.
        if (empty($createdata->send_message)) {
            $createdata->send_message = 0;
        }
        if (empty($createdata->send_reminder)) {
            $createdata->send_reminder = 0;
        }
        if (empty($createdata->halt_until_fulfilled)) {
            $createdata->halt_until_fulfilled = 0;
        }
        if (empty($createdata->active)) {
            $createdata->active = 0;
        }
        $createdata->timecreated = time();
        $createdata->message_time = $createdata->hour * 3600 + $createdata->minute * 60;

        $threadid = $DB->insert_record('microlearning_thread', $createdata);
        $redirectmessage = get_string('threadcreatedok', 'block_iomad_microlearning');

        // Fire an Event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\thread_created::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $threadid,
                                                                               'other' => $eventother));
        $event->trigger();
    } else {
        // We are editing a current thread.
        $createdata->message_time = $createdata->hour * 3600 + $createdata->minute * 60;

        $threadid = $DB->update_record('microlearning_thread', $createdata);
        $redirectmessage = get_string('threadcupdatedok', 'block_iomad_microlearning');

        // Fire an Event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\thread_updated::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $threadid,
                                                                               'other' => $eventother));
        $event->trigger();
    }

    redirect($threadlist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    die;
}

// Display the form.
echo $output->header();

$editform->display();

echo $output->footer();
