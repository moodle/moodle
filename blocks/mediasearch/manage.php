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

$search = optional_param('search', '', PARAM_CLEAN);
$sort = optional_param('sort', 'coursefullname', PARAM_CLEAN);
$dir = optional_param('dir', 'DESC', PARAM_CLEAN);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();

require_login();
require_capability('block/mediasearch:manageentries',$context);

$url = '/blocks/mediasearch/manage.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('manage', 'block_mediasearch'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);


// Set up the local renderer.
$renderer = $PAGE->get_renderer('block_mediasearch');

// Do we need to do anything?
if ($action == 'delete' && confirm_sesskey() && !empty($id)) {
    if ($confirm = optional_param('confirm',0,PARAM_INT)) {
        if ($delrecord = $DB->get_record('block_mediasearch_data', array('id' => $id))) {
            $DB->delete_record('block_mediasearch_data', array('id' => $id));
            echo $renderer->notification(get_string('recorddeleted', 'block_mediasearch'), 'notifysuccess');
            $action = '';
        } else {
            echo $renderer->notification(get_string('invalidrecord', 'block_mediasearch'), 'notifyfailure');
        }
    } else {   // Print a confirmation page
        if ($delrecord = $DB->get_record('block_mediasearch_data', array('id' => $id), MUST_EXIST)) { // Need to check this is valid.
            $deletebutton = new single_button(new moodle_url('/blocks/mediasearch/manage.php',
                                                             array('id' => $id,
                                                                   'search' => $search,
                                                                   'confirm' => 1,
                                                                   'action' => 'delete',
                                                                   'sesskey' => sesskey(),
                                                                   'sort' => $sort,
                                                                   'dir' => $dir,
                                                                   'page' => $page,
                                                                   'perpage' => $perpage)), get_string('delete'), 'get');
            echo $renderer->confirm(get_string('confirmdeleterecord', 'block_mediasearch'),
                                    $deletebutton, 'manage.php?id='.$id);

            $records[] = $deleterecord;

            echo $renderer->footer();
            exit;
        }
    }
}

if ($action == 'download') {
    // Get all the entries.
    $entries = mediasearch::get_media_entries('', $sort, $dir, 0, 0);

    // Output to CSV.
    $renderer->do_entrydownload($entries->entries);
    exit;
}


// Get the entries to be displayed.
$entries = mediasearch::get_media_entries($search, $sort, $dir, $page, $perpage);

echo $renderer->header();

// Display the search form and so on.
$renderer->display_managetop();

// Display the table of entries.
$renderer->display_entries($entries, $sort, $dir, $page, $perpage, $search);

echo $renderer->footer();