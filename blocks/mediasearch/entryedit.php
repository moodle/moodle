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
 * Version details
 *
 * @package    block_mediasearch
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/mediasearch/locallib.php');
require_once($CFG->dirroot.'/blocks/mediasearch/entryedit_form.php');

$id = optional_param('id', 0, PARAM_INT);
$new = optional_param('new', 0, PARAM_BOOL);

$context = context_system::instance();

require_login();
require_capability('block/mediasearch:manageentries',$context);

$url = '/blocks/mediasearch/entryedit.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('entry_edit', 'block_mediasearch'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);


// Set up the local renderer.
$renderer = $PAGE->get_renderer('block_mediasearch');

if (!empty($id)) {
echo "HERE</br>";
    if (!$entry = $DB->get_record('block_mediasearch_data', array('id' => $id))) {
        print_error('invalidrecordid');
    }
}

// Set up the form.
$mform = new block_mediasearch_entry_edit_form();
if (!empty($id)) {
    $mform->set_data($entry);
echo "entry = <pre>";
print_r($entry);
echo "</pre></br>";
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/mediasearch/manage.php'));
} else if ($data = $mform->get_data()) {
    if (!empty($data->id)) {
        // We are updating a record.
        $DB->update_record('block_mediasearch_data', (array) $data);
    } else {
        $data->id = $DB->insert_record('block_mediasearch_data', (array) $data);
    }
    redirect(new moodle_url('/blocks/mediasearch/manage.php'));
}

echo $renderer->header();

$mform->display();

echo $renderer->footer();