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
 * The main lib file.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\board;
use mod_board\local\note;

/**
 * Specify what the plugin supports.
 * @param string $feature
 * @return bool|null
 */
function board_supports($feature) {
    switch ($feature) {
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COLLABORATION;
        default:
            return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param array $data the data submitted from the reset course.
 * @return array status array
 */
function board_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return [];
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function board_get_view_actions() {
    return ['view', 'view all'];
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function board_get_post_actions() {
    return ['update', 'add'];
}

/**
 * Add board instance.
 * @param stdClass $data
 * @param mod_board_mod_form $mform
 * @return int new board instance id
 */
function board_add_instance($data, $mform = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    if (!isset($data->hideheaders)) {
        $data->hideheaders = 0;
    }
    if (empty($data->postbyenabled)) {
        $data->postby = 0;
    }

    // Add 3 columns by default.
    $columnheading = get_string('default_column_heading', 'mod_board');
    $columns = [$columnheading, $columnheading, $columnheading];

    // Apply template if selected.
    if (!empty($data->templateid)) {
        $template = $DB->get_record('board_templates', ['id' => $data->templateid], '*', MUST_EXIST);

        if ($template->columns === '') {
            $columns = [];
        } else {
            $columns = explode("\n", $template->columns);
        }

        $settings = \mod_board\local\template::get_settings($template->jsonsettings);
        foreach ($settings as $k => $v) {
            $data->$k = $v;
        }
    }

    // Add default columns.
    $boardid = $DB->insert_record('board', $data);
    $i = 0;
    foreach ($columns as $columname) {
        $i++;
        $DB->insert_record(
            'board_columns',
            ['boardid' => $boardid, 'name' => $columname, 'sortorder' => $i]
        );
    }

    // Save background image if set.
    $cmid = $data->coursemodule;
    $context = context_module::instance($cmid);
    if (!empty($data->background_image)) {
        file_save_draft_area_files(
            $data->background_image,
            $context->id,
            'mod_board',
            'background',
            0,
            board::get_background_picker_options()
        );
    }

    return $boardid;
}

/**
 * Update board instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function board_update_instance($data, $mform) {
    global $DB;

    if (!isset($data->hideheaders)) {
        $data->hideheaders = 0;
    }

    if (empty($data->postbyenabled)) {
        $data->postby = 0;
    }

    $data->id = $data->instance;
    $DB->update_record('board', $data);

    // Save background image if set.
    $cmid = $data->coursemodule;
    $context = context_module::instance($cmid);
    if (!empty($data->background_image)) {
        file_save_draft_area_files(
            $data->background_image,
            $context->id,
            'mod_board',
            'background',
            0,
            board::get_background_picker_options()
        );
    }

    return true;
}

/**
 * Delete board instance.
 * @param int $id
 * @return bool true
 */
function board_delete_instance($id) {
    global $DB;

    $board = board::get_board($id);
    if (!$board) {
        return false;
    }
    $context = board::context_for_board($board);

    // Remove notes.
    $columns = $DB->get_records('board_columns', ['boardid' => $board->id], '', 'id');
    foreach ($columns as $columnid => $column) {
        $rs = $DB->get_recordset('board_notes', ['columnid' => $columnid]);
        foreach ($rs as $note) {
            $DB->delete_records('board_note_ratings', ['noteid' => $note->id]);
            $DB->delete_records('board_comments', ['noteid' => $note->id]);
            note::delete_files($note, $context);
        }
        $rs->close();
        $DB->delete_records('board_notes', ['columnid' => $columnid]);
    }

    // Remove columns.
    $DB->delete_records('board_columns', ['boardid' => $board->id]);

    $DB->delete_records('board', ['id' => $board->id]);

    return true;
}

/**
 * Extend navigation.
 * @param settings_navigation $settings
 * @param navigation_node $boardnode
 */
function board_extend_settings_navigation(settings_navigation $settings, navigation_node $boardnode) {
    $cm = $settings->get_page()->cm;
    $context = context_module::instance($cm->id);

    if (
        has_capability('moodle/course:manageactivities', $context)
        && !board::board_has_notes($cm->instance)
        && \mod_board\local\template::get_applicable_templates($context)
    ) {
        $node = navigation_node::create(
            get_string('template_apply', 'board'),
            new moodle_url('/mod/board/template/apply.php', ['id' => $cm->id]),
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/settings', '')
        );
        $boardnode->add_node($node);
    }

    if (has_capability('mod/board:manageboard', $context)) {
        $node = navigation_node::create(
            get_string('export', 'board'),
            new moodle_url('/mod/board/export.php', ['id' => $cm->id]),
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/export', '')
        );
        $boardnode->add_node($node);
    }
}

/**
 * Handle plugin files.
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return false
 */
function mod_board_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;
    require_once($CFG->libdir . '/filelib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);

    if ($filearea === 'images') {
        $note = board::get_note($args[0]);
        if (!$note || !board::can_view_note($note)) {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = '/' . $context->id . '/mod_board/images/' . $relativepath;

        $fs = get_file_storage();
        if ((!$file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload);
    } else if ($filearea === 'files') {
        $note = board::get_note($args[0]);
        if (!$note || !board::can_view_note($note)) {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = '/' . $context->id . '/mod_board/files/' . $relativepath;

        $fs = get_file_storage();
        if ((!$file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, true);
    } else if ($filearea === 'background') {
        require_capability('mod/board:view', $context);
        $relativepath = implode('/', $args);
        $fullpath = '/' . $context->id . '/mod_board/background/' . $relativepath;

        $fs = get_file_storage();
        if ((!$file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload);
    }

    return false;
}

/**
 * Add a get_coursemodule_info function in case any forum type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function board_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, completionnotes';
    if (!$board = $DB->get_record('board', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $board->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('board', $board, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionnotes'] = $board->completionnotes;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_board_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (
        empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC
    ) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionnotes':
                if (!empty($val)) {
                    $descriptions[] = get_string('completionnotesdesc', 'mod_board', $val);
                }
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

/**
 * Obtains the automatic completion state for this board on any conditions
 * in board settings
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function board_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    $boardid = $cm->instance;

    if (!$board = $DB->get_record('board', ['id' => $boardid])) {
        throw new \moodle_exception('Unable to find board with id ' . $boardid);
    }

    $notescountparams = ['userid' => $userid, 'boardid' => $boardid];
    $notescountsql = "SELECT COUNT(*)
                        FROM {board_notes} bn
                        JOIN {board_columns} bc ON bn.columnid = bc.id
                        WHERE bn.userid = :userid
                        AND bc.boardid = :boardid";

    if ($board->completionnotes) {
        $numnotes = $DB->get_field_sql($notescountsql, $notescountparams);
        if ($numnotes) {
            return ($numnotes >= $board->completionnotes) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
        } else {
            return COMPLETION_INCOMPLETE;
        }
    }
    return $type;
}
/**
 * Dynamically change the activity to not show a link if we want to embed it.
 * This is called via a automatic callback if this method exists.
 * @param cm_info $cm
 * @return void
 */
function board_cm_info_dynamic(cm_info $cm) {

    // Look up the board based on the course module.
    $board = board::get_board($cm->instance);

    // Check if embedding feature is allowed.
    $embedallowed = get_config('mod_board', 'embed_allowed');

    // If we are embedding the board, turn off the view link.
    if ($embedallowed && $board->embed) {
        $cm->set_no_view_link();
    }
}

/**
 * Shows the board on the course page if the board is embedded.
 *
 * @param cm_info $cm course module info.
 */
function board_cm_info_view(cm_info $cm) {

    // Look up the board based on the course module.
    $board = board::get_board($cm->instance);

    // Check if embedding feature is allowed.
    $embedallowed = get_config('mod_board', 'embed_allowed');

    if ($embedallowed && $board->embed) {
        $width = get_config('mod_board', 'embed_width');
        $height = get_config('mod_board', 'embed_height');
        $output = html_writer::start_tag('div', ['class' => 'mod_board_embed_container']);
        if (empty($board->hidename)) {
            $output .= html_writer::tag('h3', $board->name);
        }
        $output .= html_writer::start_tag('iframe', [
            'src' => new moodle_url('/mod/board/view.php', ['id' => $cm->id, 'embed' => 1]),
            'width' => $width,
            'height' => $height,
            'frameborder' => 0,
            'allowfullscreen' => true,
        ]);
        $output .= html_writer::end_tag('iframe');
        $output .= html_writer::link(
            new moodle_url('/mod/board/view.php', ['id' => $cm->id]),
            get_string('viewboard', 'board')
        );
        $output .= html_writer::end_tag('div');
        $cm->set_content($output, true);
    }
}
