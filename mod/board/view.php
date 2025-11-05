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
 * Main view file.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\board;

require('../../config.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID.
$b       = optional_param('b', 0, PARAM_INT);  // Board instance ID.
$ownerid = optional_param('ownerid', 0, PARAM_INT);  // Board owner ID.
$embed   = optional_param('embed', 0, PARAM_INT); // Value 1 means page is embedded into the course page.

if ($b) {
    if (!$board = board::get_board($b)) {
        throw new \moodle_exception('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('board', $board->id, $board->course, false, MUST_EXIST);
} else {
    if (!$cm = get_coursemodule_from_id('board', $id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }
    $board = board::get_board($cm->instance, MUST_EXIST);
}

// Make sure the board history ID is set.
$board->historyid = $board->historyid ?? 0;

$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/board:view', $context);

$groupid = groups_get_activity_group($cm, true) ?: 0;

if ($board->singleusermode == board::SINGLEUSER_DISABLED) {
    $ownerid = 0;
} else if (!$ownerid) {
    if (is_enrolled(context_course::instance($course->id), $USER->id, 'mod/board:view', true)) {
        $ownerid = $USER->id;
    } else {
        $ownerid = 0;
    }
}

$pageurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id, 'ownerid' => $ownerid]);
$baseurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);
if ($embed) {
    $pageurl->param('embed', $embed);
    $baseurl->param('embed', $embed);
}
$PAGE->set_url($pageurl);

$PAGE->set_title(format_string($board->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($board);

// Logic to limit view when board is in singleuser mode.
if ($board->singleusermode != board::SINGLEUSER_DISABLED && !board::can_view_owner($board, $ownerid)) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('nopermission', 'mod_board'));
    echo $OUTPUT->footer();
    die();
}

// Update 'viewed' state if required by completion system.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_title(format_string($board->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($board);

// If we are embedding the board on the course, set the layout to embedded to remove all other content.
if ($embed) {
    $PAGE->set_pagelayout('embedded');
}

echo $OUTPUT->header();

if ($board->enableblanktarget) {
    echo html_writer::tag('div', get_string('blanktargetenabled', 'mod_board'), ['class' => 'small']);
}

if (get_config('mod_board', 'enableprivacystatement')) {
    echo html_writer::tag('div', get_string('privacystatement', 'mod_board'), ['class' => 'normal']);
}

if ($board->singleusermode != board::SINGLEUSER_PRIVATE || has_capability('mod/board:manageboard', $context)) {
    echo html_writer::tag('div', groups_print_activity_menu($cm, $baseurl, true));
}

if (
    $board->singleusermode == board::SINGLEUSER_PUBLIC ||
    ($board->singleusermode == board::SINGLEUSER_PRIVATE && has_capability('mod/board:manageboard', $context))
) {
    $users = board::get_users_for_board($board, $groupid);
    if ($ownerid && !isset($users[$ownerid])) {
        $ownerid = 0;
    }
    if (count($users) == 0) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo $OUTPUT->notification(get_string('nousers', 'mod_board'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
    } else {
        $nothing = $ownerid ? null : ['' => 'choosedots'];
        $select = new single_select($baseurl, 'ownerid', $users, $ownerid, $nothing);
        $select->label = get_string('selectuser', 'mod_board');
        echo html_writer::tag('div', $OUTPUT->render($select));
    }
}

$extrabackground = '';
if (!empty($board->background_color)) {
    $color = '#' . str_replace('#', '', $board->background_color);
    $extrabackground = "background-color: {$color};";
}

if (!$ownerid && $board->singleusermode != board::SINGLEUSER_DISABLED) {
    echo $OUTPUT->box_start('mod_introbox', 'pageintro');
    echo $OUTPUT->notification(get_string('selectuserplease', 'mod_board'));
    echo $OUTPUT->box_end();
} else {
    $PAGE->requires->js_call_amd(
        'mod_board/main',
        'initialize',
        [
            'boardid' => $board->id,
            'ownerid' => $ownerid,
            'groupid' => ($board->singleusermode == board::SINGLEUSER_DISABLED) ? $groupid : 0,
        ]
    );

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_board', 'background', 0, '', false);
    if (count($files)) {
        $file = reset($files);
        $url = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        )->get_path();
        $extrabackground = "background:url({$url}) no-repeat center center; -webkit-background-size: cover;
        -moz-background-size: cover; -o-background-size: cover; background-size: cover;";
    }
    echo '<div class="mod_board_wrapper">';
    echo '<div class="mod_board flex-fill" style="' . $extrabackground . '"></div>';
    if (has_capability('mod/board:manageboard', $context)) {
        $img = html_writer::img(
            $OUTPUT->image_url('brickfield-logo-poweredby', 'mod_board'),
            get_string('brickfieldlogo', 'mod_board'),
            ['style' => 'display: block !important; width: 140px;']
        );
        if (get_config('core', 'version') > 2024100799) {
            $visuallyhidden = 'visually-hidden';
        } else {
            $visuallyhidden = 'sr-only';
        }
        $img .= html_writer::tag('span', get_string('opensinnewwindow', 'mod_board'), ['class' => $visuallyhidden]);
        echo html_writer::link('https://www.brickfield.ie/docs/mod_board/', $img, ['target' => '_blank',
            'style' => 'margin-left: auto; margin-right: 90px; display: block !important; width: 140px;']);
    }
    echo '</div>';
}
echo $OUTPUT->footer();
