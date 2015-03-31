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
 * Save and load draft text while a user is still editing a form.
 *
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../config.php');

$contextid = required_param('contextid', PARAM_INT);
$elementid = required_param('elementid', PARAM_ALPHANUMEXT);
$pagehash = required_param('pagehash', PARAM_ALPHANUMEXT);
$pageinstance = required_param('pageinstance', PARAM_ALPHANUMEXT);
$now = time();
// This is the oldest time any autosave text will be recovered from.
// This is so that there is a good chance the draft files will still exist (there are many variables so
// this is impossible to guarantee).
$before = $now - 60*60*24*4;

list($context, $course, $cm) = get_context_info_array($contextid);
$PAGE->set_url('/lib/editor/atto/autosave-ajax.php');
$PAGE->set_context($context);

require_login($course, false, $cm);
require_sesskey();

$action = required_param('action', PARAM_ALPHA);

$response = array();

if ($action === 'save') {
    $drafttext = required_param('drafttext', PARAM_RAW);
    $params = array('elementid' => $elementid,
                    'userid' => $USER->id,
                    'pagehash' => $pagehash,
                    'contextid' => $contextid);

    $record = $DB->get_record('editor_atto_autosave', $params);
    if ($record && $record->pageinstance != $pageinstance) {
        print_error('concurrent access from the same user is not supported');
        die();
    }

    if (!$record) {
        $record = new stdClass();
        $record->elementid = $elementid;
        $record->userid = $USER->id;
        $record->pagehash = $pagehash;
        $record->contextid = $contextid;
        $record->drafttext = $drafttext;
        $record->pageinstance = $pageinstance;
        $record->timemodified = $now;

        $DB->insert_record('editor_atto_autosave', $record);

        // No response means no error.
        die();
    } else {
        $record->drafttext = $drafttext;
        $record->timemodified = time();
        $DB->update_record('editor_atto_autosave', $record);

        // No response means no error.
        die();
    }
} else if ($action == 'resume') {
    $params = array('elementid' => $elementid,
                    'userid' => $USER->id,
                    'pagehash' => $pagehash,
                    'contextid' => $contextid);

    $newdraftid = required_param('draftid', PARAM_INT);

    $record = $DB->get_record('editor_atto_autosave', $params);

    if (!$record) {
        $record = new stdClass();
        $record->elementid = $elementid;
        $record->userid = $USER->id;
        $record->pagehash = $pagehash;
        $record->contextid = $contextid;
        $record->pageinstance = $pageinstance;
        $record->pagehash = $pagehash;
        $record->draftid = $newdraftid;
        $record->timemodified = time();
        $record->drafttext = '';

        $DB->insert_record('editor_atto_autosave', $record);

        // No response means no error.
        die();
    } else {
        // Copy all draft files from the old draft area.
        $usercontext = context_user::instance($USER->id);
        $stale = $record->timemodified < $before;
        require_once($CFG->libdir . '/filelib.php');

        // This function copies all the files in one draft area, to another area (in this case it's
        // another draft area). It also rewrites the text to @@PLUGINFILE@@ links.
        $newdrafttext = file_save_draft_area_files($record->draftid,
                                                   $usercontext->id,
                                                   'user',
                                                   'draft',
                                                   $newdraftid,
                                                   array(),
                                                   $record->drafttext);

        // Final rewrite to the new draft area (convert the @@PLUGINFILES@@ again).
        $newdrafttext = file_rewrite_pluginfile_urls($newdrafttext,
                                                     'draftfile.php',
                                                     $usercontext->id,
                                                     'user',
                                                     'draft',
                                                     $newdraftid);
        $record->drafttext = $newdrafttext;

        $record->pageinstance = $pageinstance;
        $record->draftid = $newdraftid;
        $record->timemodified = time();
        $DB->update_record('editor_atto_autosave', $record);

        // A response means the draft has been restored and here is the auto-saved text.
        if (!$stale) {
            $response['result'] = $record->drafttext;
            echo json_encode($response);
        }
        die();
    }
} else if ($action == 'reset') {
    $params = array('elementid' => $elementid,
                    'userid' => $USER->id,
                    'pagehash' => $pagehash,
                    'contextid' => $contextid);

    $DB->delete_records('editor_atto_autosave', $params);
    die();
}

print_error('invalidarguments');
