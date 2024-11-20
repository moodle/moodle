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

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filestorage/file_storage.php');

// Clean up actions.
$actions = array_map(function($actionparams) {
    $action = isset($actionparams['action']) ? $actionparams['action'] : null;
    $params = [];
    $keys = [
        'action' => PARAM_ALPHA,
        'contextid' => PARAM_INT,
        'elementid' => PARAM_ALPHANUMEXT,
        'pagehash' => PARAM_ALPHANUMEXT,
        'pageinstance' => PARAM_ALPHANUMEXT
    ];

    if ($action == 'save') {
        $keys['drafttext'] = PARAM_RAW;
    } else if ($action == 'resume') {
        $keys['draftid'] = PARAM_INT;
    }

    foreach ($keys as $key => $type) {
        // Replicate required_param().
        if (!isset($actionparams[$key])) {
            throw new \moodle_exception('missingparam', '', '', $key);
        }
        $params[$key] = clean_param($actionparams[$key], $type);
    }

    return $params;
}, isset($_REQUEST['actions']) ? $_REQUEST['actions'] : []);

$now = time();
// This is the oldest time any autosave text will be recovered from.
// This is so that there is a good chance the draft files will still exist (there are many variables so
// this is impossible to guarantee).
$before = $now - 60*60*24*4;

$context = context_system::instance();
$PAGE->set_url('/lib/editor/atto/autosave-ajax.php');
$PAGE->set_context($context);

require_login();
if (isguestuser()) {
    throw new \moodle_exception('accessdenied', 'admin');
}
require_sesskey();

if (!in_array('atto', explode(',', get_config('core', 'texteditors')))) {
    throw new \moodle_exception('accessdenied', 'admin');
}

$responses = array();
foreach ($actions as $actionparams) {

    $action = $actionparams['action'];
    $contextid = $actionparams['contextid'];
    $elementid = $actionparams['elementid'];
    $pagehash = $actionparams['pagehash'];
    $pageinstance = $actionparams['pageinstance'];

    if ($action === 'save') {
        $drafttext = $actionparams['drafttext'];
        $params = array('elementid' => $elementid,
                        'userid' => $USER->id,
                        'pagehash' => $pagehash,
                        'contextid' => $contextid);

        $record = $DB->get_record('editor_atto_autosave', $params);
        if ($record && $record->pageinstance != $pageinstance) {
            throw new \moodle_exception('concurrent access from the same user is not supported');
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
            $responses[] = null;
            continue;
        } else {
            $record->drafttext = $drafttext;
            $record->timemodified = time();
            $DB->update_record('editor_atto_autosave', $record);

            // No response means no error.
            $responses[] = null;
            continue;
        }

    } else if ($action == 'resume') {
        $params = array('elementid' => $elementid,
                        'userid' => $USER->id,
                        'pagehash' => $pagehash,
                        'contextid' => $contextid);

        $newdraftid = $actionparams['draftid'];

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
            $responses[] = null;
            continue;

        } else {
            // Copy all draft files from the old draft area.
            $usercontext = context_user::instance($USER->id);
            $stale = $record->timemodified < $before;
            require_once($CFG->libdir . '/filelib.php');

            $fs = get_file_storage();
            $files = $fs->get_directory_files($usercontext->id, 'user', 'draft', $newdraftid, '/', true, true);

            $lastfilemodified = 0;
            foreach ($files as $file) {
                $lastfilemodified = max($lastfilemodified, $file->get_timemodified());
            }
            if ($record->timemodified < $lastfilemodified) {
                $stale = true;
            }

            if (!$stale) {
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
                $response = ['result' => $record->drafttext];
                $responses[] = $response;

            } else {
                $DB->delete_records('editor_atto_autosave', array('id' => $record->id));

                // No response means no error.
                $responses[] = null;
            }
            continue;
        }

    } else if ($action == 'reset') {
        $params = array('elementid' => $elementid,
                        'userid' => $USER->id,
                        'pagehash' => $pagehash,
                        'contextid' => $contextid);

        $DB->delete_records('editor_atto_autosave', $params);
        $responses[] = null;
        continue;
    }
}

echo json_encode($responses);
