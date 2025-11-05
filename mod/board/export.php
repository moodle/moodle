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
 * Downloads a user's board submissions.
 * @package     mod_board
 * @author      Bas Brands <bas@sonsbeekmedia.nl>
 * @copyright   2023 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

use mod_board\board;

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$ownerid = optional_param('ownerid', 0, PARAM_INT); // The ID of the board owner.
$includedeleted = optional_param('includedeleted', 0, PARAM_INT); // Whether to include deleted comments.
$download = optional_param('download', '', PARAM_ALPHA);
$tabletype = optional_param('tabletype', 'board', PARAM_ALPHA);
$group = optional_param('group', 0, PARAM_INT);

if (!$cm = get_coursemodule_from_id('board', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}
$board = board::get_board($cm->instance, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/board:manageboard', $context);

$classname = 'mod_board\\tables\\' . $tabletype . '_table';

$table = new $classname($cm->id, $board->id, $group, $ownerid, $includedeleted);
$filename = clean_param($board->name, PARAM_FILE) . '_' . $tabletype . '_report_' .
    userdate(time(), '%Y-%m-%d-%H%M%S');
$table->is_downloading($download, $filename);

$pageurl = new moodle_url('/mod/board/export.php', ['id' => $id, 'ownerid' => $ownerid, 'tabletype' => $tabletype,
    'group' => $group, 'includedeleted' => $includedeleted]);
$baseurl = new moodle_url(
    '/mod/board/export.php',
    ['id' => $id, 'tabletype' => $tabletype, 'includedeleted' => $includedeleted]
);

// Create tabs for the 3 table types.
$tabs = [];
$tabs[] = new tabobject(
    'board',
    new moodle_url($pageurl, ['tabletype' => 'board']),
    get_string('export_board', 'mod_board')
);
$tabs[] = new tabobject(
    'notes',
    new moodle_url($pageurl, ['tabletype' => 'notes']),
    get_string('export_submissions', 'mod_board')
);
$tabs[] = new tabobject(
    'comments',
    new moodle_url($pageurl, ['tabletype' => 'comments']),
    get_string('export_comments', 'mod_board')
);

if (!$table->is_downloading()) {
    // Only print headers if not asked to download data.
    $PAGE->set_url($pageurl);
    $PAGE->set_title(get_string('export', 'mod_board'));
    $PAGE->set_heading(get_string('export', 'mod_board'));
    $PAGE->activityheader->disable();

    echo $OUTPUT->header();

    // Print the navigation tabs.
    echo $OUTPUT->tabtree($tabs, $tabletype);

    // Print the activity menu.
    echo html_writer::tag('div', groups_print_activity_menu($cm, $baseurl, true));

    // Print the user selector.
    if ($board->singleusermode == board::SINGLEUSER_PUBLIC || $board->singleusermode == board::SINGLEUSER_PRIVATE) {
        $users = board::get_existing_owners_for_board($board, $group, ($tabletype === 'comments'));
        // Include board download user selection to have default all users option if required.
        $users = [0 => get_string('all')] + $users;
        $select = new single_select($pageurl, 'ownerid', $users, $ownerid, null);
        $select->label = get_string('selectuser', 'mod_board');
        echo html_writer::tag('div', $OUTPUT->render($select));
    }

    // Print the include deleted checkbox.
    $includedeletedurl = new moodle_url($pageurl, ['includedeleted' => !$includedeleted]);
    $onchangelocation = "window.location.href = '" . $includedeletedurl->out(false) . "';";
    $includedeletedlabel = get_string('include_deleted', 'mod_board');
    $includedeletedcheckbox = html_writer::checkbox(
        'includedeleted',
        1,
        $includedeleted,
        $includedeletedlabel,
        ['id' => 'includedeleted', 'class' => 'form-check-input', 'onChange' => $onchangelocation],
        ['class' => 'form-check-label']
    );
    echo html_writer::div($includedeletedcheckbox, 'form-check mb-1');
}

$table->display();

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
