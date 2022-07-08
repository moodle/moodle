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

$nuggetid = optional_param('nuggetid', 0, PARAM_INT);
$threadid = required_param('threadid', PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:edit_nuggets', $context);

$nuggetlist = new moodle_url('/blocks/iomad_microlearning/nuggets.php', array('threadid' => $threadid));

$linktext = get_string('editnugget', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/nugget_edit.php');

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
$editform = new block_iomad_microlearning\forms\nugget_edit_form($PAGE->url, $threadid, $nuggetid);

// Set up the initial forms.
if (!empty($nuggetid)) {
    $nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid));
} else {
    $nugget = new stdclass();
    $nugget->threadid = $threadid;
    $threadrec = $DB->get_record('microlearning_thread', array('id' => $threadid));
    $nugget->halt_until_fulfilled = $threadrec->halt_until_fulfilled;
}
$editform->set_data($nugget);

// Process the form.
if ($editform->is_cancelled()) {
    redirect($nuggetlist);
    die;
} else if ($createdata = $editform->get_data()) {

    // Deal with leading/trailing spaces.
    $createdata->name = trim($createdata->name);

    // Create or update the department.
    if (empty($createdata->id)) {
        // We are creating a new nugget.
        $createdata->timecreated = time();
        $createdata->threadid = $threadid;

        // Set the order;
        $nuggetcount = $DB->count_records('microlearning_nugget', array('threadid' => $threadid));
        $createdata->nuggetorder = $nuggetcount;


        $nuggetid = $DB->insert_record('microlearning_nugget', $createdata);
        $redirectmessage = get_string('nuggetcreatedok', 'block_iomad_microlearning');

        // Fire an Event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\nugget_created::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $nuggetid,
                                                                               'other' => $eventother));
        $event->trigger();
    } else {
        // We are editing a current nugget.
        $createdata->message_time = $createdata->message_time['hour'] * 3600 + $createdata->message_time['minute'] * 60;

        $nuggetid = $DB->update_record('microlearning_nugget', $createdata);
        $redirectmessage = get_string('nuggetcupdatedok', 'block_iomad_microlearning');

        // Fire an Event for this.
        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_microlearning\event\nugget_updated::create(array('context' => context_system::instance(),
                                                                               'userid' => $USER->id,
                                                                               'objectid' => $nuggetid,
                                                                               'other' => $eventother));
        $event->trigger();
    }

    redirect($nuggetlist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    die;
}

// Display the form.
echo $output->header();

$editform->display();

echo $output->footer();
