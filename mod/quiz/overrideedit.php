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
 * This page handles editing and creation of quiz overrides
 *
 * @package   mod_quiz
 * @copyright 2010 Matt Petro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\form\edit_override_form;
use mod_quiz\quiz_settings;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

$cmid = optional_param('cmid', 0, PARAM_INT);
$overrideid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);
$reset = optional_param('reset', false, PARAM_BOOL);

$override = null;
if ($overrideid) {
    $override = $DB->get_record('quiz_overrides', ['id' => $overrideid], '*', MUST_EXIST);
    $quizobj = quiz_settings::create($override->quiz);
} else {
    $quizobj = quiz_settings::create_for_cmid($cmid);
}

$quiz = $quizobj->get_quiz();
$cm = $quizobj->get_cm();
$course = $quizobj->get_course();
$context = $quizobj->get_context();

$url = new moodle_url('/mod/quiz/overrideedit.php');
if ($action) {
    $url->param('action', $action);
}
if ($overrideid) {
    $url->param('id', $overrideid);
} else {
    $url->param('cmid', $cmid);
}

$PAGE->set_url($url);

// Activate the secondary nav tab.
$PAGE->set_secondary_active_tab("mod_quiz_useroverrides");

require_login($course, false, $cm);

// Add or edit an override.
require_capability('mod/quiz:manageoverrides', $context);

if ($overrideid) {
    // Editing an override.
    $data = clone $override;

    if ($override->groupid) {
        if (!groups_group_visible($override->groupid, $course, $cm)) {
            throw new \moodle_exception('invalidoverrideid', 'quiz');
        }
    } else {
        if (!groups_user_groups_visible($course, $override->userid, $cm)) {
            throw new \moodle_exception('invalidoverrideid', 'quiz');
        }
    }
} else {
    // Creating a new override.
    $data = new stdClass();
}

// Merge quiz defaults with data.
$keys = ['timeopen', 'timeclose', 'timelimit', 'attempts', 'password'];
foreach ($keys as $key) {
    if (!isset($data->{$key}) || $reset) {
        $data->{$key} = $quiz->{$key};
    }
}

// If we are duplicating an override, then clear the user/group and override id
// since they will change.
if ($action === 'duplicate') {
    $override->id = null;
    $override->userid = null;
    $override->groupid = null;
}

// True if group-based override.
$groupmode = !empty($data->groupid) || ($action === 'addgroup' && empty($overrideid));

$overridelisturl = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $cm->id]);
if (!$groupmode) {
    $overridelisturl->param('mode', 'user');
}

// Setup the form.
$mform = new edit_override_form($url, $cm, $quiz, $context, $groupmode, $override);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($overridelisturl);

} else if (optional_param('resetbutton', 0, PARAM_ALPHA)) {
    $url->param('reset', true);
    redirect($url);

} else if ($fromform = $mform->get_data()) {
    // Process the data.
    $fromform->quiz = $quiz->id;

    // Replace unchanged values with null.
    foreach ($keys as $key) {
        if ($fromform->{$key} == $quiz->{$key}) {
            $fromform->{$key} = null;
        }
    }

    // See if we are replacing an existing override.
    $userorgroupchanged = false;
    if (empty($override->id)) {
        $userorgroupchanged = true;
    } else if (!empty($fromform->userid)) {
        $userorgroupchanged = $fromform->userid !== $override->userid;
    } else {
        $userorgroupchanged = $fromform->groupid !== $override->groupid;
    }

    if ($userorgroupchanged) {
        $conditions = [
                'quiz' => $quiz->id,
                'userid' => empty($fromform->userid) ? null : $fromform->userid,
                'groupid' => empty($fromform->groupid) ? null : $fromform->groupid];
        if ($oldoverride = $DB->get_record('quiz_overrides', $conditions)) {
            // There is an old override, so we merge any new settings on top of
            // the older override.
            foreach ($keys as $key) {
                if (is_null($fromform->{$key})) {
                    $fromform->{$key} = $oldoverride->{$key};
                }
            }
            // Set the course module id before calling quiz_delete_override().
            $quiz->cmid = $cm->id;
            quiz_delete_override($quiz, $oldoverride->id);
        }
    }

    // Set the common parameters for one of the events we may be triggering.
    $params = [
        'context' => $context,
        'other' => [
            'quizid' => $quiz->id
        ]
    ];
    if (!empty($override->id)) {
        $fromform->id = $override->id;
        $DB->update_record('quiz_overrides', $fromform);
        $cachekey = $groupmode ? "{$fromform->quiz}_g_{$fromform->groupid}" : "{$fromform->quiz}_u_{$fromform->userid}";
        cache::make('mod_quiz', 'overrides')->delete($cachekey);

        // Determine which override updated event to fire.
        $params['objectid'] = $override->id;
        if (!$groupmode) {
            $params['relateduserid'] = $fromform->userid;
            $event = \mod_quiz\event\user_override_updated::create($params);
        } else {
            $params['other']['groupid'] = $fromform->groupid;
            $event = \mod_quiz\event\group_override_updated::create($params);
        }

        // Trigger the override updated event.
        $event->trigger();
    } else {
        unset($fromform->id);
        $fromform->id = $DB->insert_record('quiz_overrides', $fromform);
        $cachekey = $groupmode ? "{$fromform->quiz}_g_{$fromform->groupid}" : "{$fromform->quiz}_u_{$fromform->userid}";
        cache::make('mod_quiz', 'overrides')->delete($cachekey);

        // Determine which override created event to fire.
        $params['objectid'] = $fromform->id;
        if (!$groupmode) {
            $params['relateduserid'] = $fromform->userid;
            $event = \mod_quiz\event\user_override_created::create($params);
        } else {
            $params['other']['groupid'] = $fromform->groupid;
            $event = \mod_quiz\event\group_override_created::create($params);
        }

        // Trigger the override created event.
        $event->trigger();
    }

    quiz_update_open_attempts(['quizid' => $quiz->id]);
    if ($groupmode) {
        // Priorities may have shifted, so we need to update all of the calendar events for group overrides.
        quiz_update_events($quiz);
    } else {
        // User override. We only need to update the calendar event for this user override.
        quiz_update_events($quiz, $fromform);
    }

    if (!empty($fromform->submitbutton)) {
        redirect($overridelisturl);
    }

    // The user pressed the 'again' button, so redirect back to this page.
    $url->remove_params('cmid');
    $url->param('action', 'duplicate');
    $url->param('id', $fromform->id);
    redirect($url);

}

// Print the form.
$pagetitle = get_string('editoverride', 'quiz');
$PAGE->navbar->add($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->set_attrs([
    "title" => format_string($quiz->name, true, ['context' => $context]),
    "description" => "",
    "hidecompletion" => true
]);
echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
