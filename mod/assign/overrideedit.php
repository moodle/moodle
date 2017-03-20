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
 * This page handles editing and creation of assign overrides
 *
 * @package   mod_assign
 * @copyright 2016 Ilya Tregubov
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/mod/assign/lib.php');
require_once($CFG->dirroot.'/mod/assign/locallib.php');
require_once($CFG->dirroot.'/mod/assign/override_form.php');


$cmid = optional_param('cmid', 0, PARAM_INT);
$overrideid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);
$reset = optional_param('reset', false, PARAM_BOOL);

$pagetitle = get_string('editoverride', 'assign');

$override = null;
if ($overrideid) {

    if (! $override = $DB->get_record('assign_overrides', array('id' => $overrideid))) {
        print_error('invalidoverrideid', 'assign');
    }

    list($course, $cm) = get_course_and_cm_from_instance($override->assignid, 'assign');

} else if ($cmid) {
    list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'assign');

} else {
    print_error('invalidcoursemodule');
}

$url = new moodle_url('/mod/assign/overrideedit.php');
if ($action) {
    $url->param('action', $action);
}
if ($overrideid) {
    $url->param('id', $overrideid);
} else {
    $url->param('cmid', $cmid);
}

$PAGE->set_url($url);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
$assign = new assign($context, $cm, $course);
$assigninstance = $assign->get_instance();

// Add or edit an override.
require_capability('mod/assign:manageoverrides', $context);

if ($overrideid) {
    // Editing an override.
    $data = clone $override;
} else {
    // Creating a new override.
    $data = new stdClass();
}

// Merge assign defaults with data.
$keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate');
foreach ($keys as $key) {
    if (!isset($data->{$key}) || $reset) {
        $data->{$key} = $assigninstance->{$key};
    }
}

// True if group-based override.
$groupmode = !empty($data->groupid) || ($action === 'addgroup' && empty($overrideid));

// If we are duplicating an override, then clear the user/group and override id
// since they will change.
if ($action === 'duplicate') {
    $override->id = $data->id = null;
    $override->userid = $data->userid = null;
    $override->groupid = $data->groupid = null;
    $pagetitle = get_string('duplicateoverride', 'assign');
}

$overridelisturl = new moodle_url('/mod/assign/overrides.php', array('cmid' => $cm->id));
if (!$groupmode) {
    $overridelisturl->param('mode', 'user');
}

// Setup the form.
$mform = new assign_override_form($url, $cm, $assign, $context, $groupmode, $override);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($overridelisturl);

} else if (optional_param('resetbutton', 0, PARAM_ALPHA)) {
    $url->param('reset', true);
    redirect($url);

} else if ($fromform = $mform->get_data()) {
    // Process the data.
    $fromform->assignid = $assigninstance->id;

    // Replace unchanged values with null.
    foreach ($keys as $key) {
        if (($fromform->{$key} == $assigninstance->{$key})) {
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
        $conditions = array(
                'assignid' => $assigninstance->id,
                'userid' => empty($fromform->userid) ? null : $fromform->userid,
                'groupid' => empty($fromform->groupid) ? null : $fromform->groupid);
        if ($oldoverride = $DB->get_record('assign_overrides', $conditions)) {
            // There is an old override, so we merge any new settings on top of
            // the older override.
            foreach ($keys as $key) {
                if (is_null($fromform->{$key})) {
                    $fromform->{$key} = $oldoverride->{$key};
                }
            }

            $assign->delete_override($oldoverride->id);
        }
    }

    // Set the common parameters for one of the events we may be triggering.
    $params = array(
        'context' => $context,
        'other' => array(
            'assignid' => $assigninstance->id
        )
    );
    if (!empty($override->id)) {
        $fromform->id = $override->id;
        $DB->update_record('assign_overrides', $fromform);

        // Determine which override updated event to fire.
        $params['objectid'] = $override->id;
        if (!$groupmode) {
            $params['relateduserid'] = $fromform->userid;
            $event = \mod_assign\event\user_override_updated::create($params);
        } else {
            $params['other']['groupid'] = $fromform->groupid;
            $event = \mod_assign\event\group_override_updated::create($params);
        }

        // Trigger the override updated event.
        $event->trigger();
    } else {
        unset($fromform->id);
        $fromform->id = $DB->insert_record('assign_overrides', $fromform);
        if ($groupmode) {
            $fromform->sortorder = $fromform->id;

            $overridecountgroup = $DB->count_records('assign_overrides',
                array('userid' => null, 'assignid' => $assigninstance->id));
            $overridecountall = $DB->count_records('assign_overrides', array('assignid' => $assigninstance->id));
            if ((!$overridecountgroup) && ($overridecountall)) { // No group overrides and there are user overrides.
                $fromform->sortorder = 1;
            } else {
                $fromform->sortorder = $overridecountgroup;

            }

            $DB->update_record('assign_overrides', $fromform);

        }

        // Determine which override created event to fire.
        $params['objectid'] = $fromform->id;
        if (!$groupmode) {
            $params['relateduserid'] = $fromform->userid;
            $event = \mod_assign\event\user_override_created::create($params);
        } else {
            $params['other']['groupid'] = $fromform->groupid;
            $event = \mod_assign\event\group_override_created::create($params);
        }

        // Trigger the override created event.
        $event->trigger();
    }

    assign_update_events($assign, $fromform);

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
$PAGE->navbar->add($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($assigninstance->name, true, array('context' => $context)));

$mform->display();

echo $OUTPUT->footer();
