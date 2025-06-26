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
 * View for BigBlueButton interaction.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

use core\notification;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\exceptions\server_not_available_exception;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\plugin;
use mod_bigbluebuttonbn\recording;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

global $SESSION, $PAGE, $CFG, $DB, $USER, $OUTPUT;

$action = required_param('action', PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);
$bn = optional_param('bn', 0, PARAM_INT);
$rid = optional_param('rid', '', PARAM_TEXT);
$rtype = optional_param('rtype', 'presentation', PARAM_TEXT);
$errors = optional_param('errors', '', PARAM_TEXT);
$timeline = optional_param('timeline', 0, PARAM_INT);
$index = optional_param('index', 0, PARAM_INT);
$group = optional_param('group', -1, PARAM_INT);

// Get the bbb instance from either the cmid (id), or the instanceid (bn).
if ($id) {
    $instance = instance::get_from_cmid($id);
} else {
    if ($bn) {
        $instance = instance::get_from_instanceid($bn);
    }
}

if (!$instance) {
    $courseid = optional_param('courseid', 1, PARAM_INT);
    \core\notification::error(get_string('general_error_not_found', 'mod_bigbluebuttonbn', $id));
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

$cm = $instance->get_cm();
$course = $instance->get_course();
$bigbluebuttonbn = $instance->get_instance_data();
$context = $instance->get_context();

require_login($course, true, $cm);

// Note : this uses the group optional_param as a value to decide which groupid.
$groupid = groups_get_activity_group($cm, true) ?: null;
if ($groupid) {
    $instance->set_group_id($groupid);
}

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url('/mod/bigbluebuttonbn/bbb_view.php', ['id' => $cm->id, 'bigbluebuttonbn' => $bigbluebuttonbn->id]);
$PAGE->set_title(format_string($bigbluebuttonbn->name));
$PAGE->set_cacheable(false);
$PAGE->set_heading($course->fullname);
$PAGE->blocks->show_only_fake_blocks();

switch (strtolower($action)) {
    case 'logout':
        if (isset($errors) && $errors != '') {
            $errors = (array) json_decode(urldecode($errors));
            $msgerrors = '';
            foreach ($errors as $error) {
                $msgerrors .= html_writer::tag('p', $error->{'message'}, ['class' => 'alert alert-danger']) . "\n";
            }
            throw new moodle_exception('view_error_bigbluebutton', 'bigbluebuttonbn',
                $CFG->wwwroot . '/mod/bigbluebuttonbn/view.php?id=' . $id, $msgerrors, $errors);
        }

        if (empty($bigbluebuttonbn)) {
            echo get_string('view_message_tab_close', 'bigbluebuttonbn');
            die();
        }
        // Moodle event logger: Create an event for meeting left.
        logger::log_meeting_left_event($instance);

        // Update the cache.
        $meeting = new meeting($instance);
        $meeting->update_cache();

        // Check the origin page.
        $select = "userid = ? AND log = ?";
        $params = [
            'userid' => $USER->id,
            'log' => logger::EVENT_JOIN,
        ];
        $accesses = $DB->get_records_select('bigbluebuttonbn_logs', $select, $params, 'id ASC', 'id, meta', 1);
        $lastaccess = end($accesses);
        if (!empty($lastaccess->meta)) {
            $lastaccess = json_decode($lastaccess->meta);
            // If the user acceded from Timeline it should be redirected to the Dashboard.
            if (isset($lastaccess->origin) && $lastaccess->origin == logger::ORIGIN_TIMELINE) {
                redirect($CFG->wwwroot . '/my/');
            }
        }
        break;
    case 'join':
        if (empty($bigbluebuttonbn)) {
            throw new moodle_exception('view_error_unable_join', 'bigbluebuttonbn');
            break;
        }
        // Check the origin page.
        $origin = logger::ORIGIN_BASE;
        if ($timeline) {
            $origin = logger::ORIGIN_TIMELINE;
        } else if ($index) {
            $origin = logger::ORIGIN_INDEX;
        }

        try {
            $url = meeting::join_meeting($instance, $origin);
            redirect($url);
        } catch (server_not_available_exception $e) {
            bigbluebutton_proxy::handle_server_not_available($instance);
        }
        // We should never reach this point.
        break;

    case 'play':
        $recordings = $instance->get_recordings();
        if (!isset($recordings[$rid])) {
            notification::add(get_string('recordingnotfound', 'mod_bigbluebuttonbn'), notification::ERROR);
            redirect($instance->get_view_url());
        }
        $href = $recordings[$rid]->get_remote_playback_url($rtype);
        if (!$href) {
            notification::add(get_string('recordingurlnotfound', 'mod_bigbluebuttonbn'), notification::ERROR);
            redirect($instance->get_view_url());
        }
        logger::log_recording_played_event($instance, $rid);
        redirect(urldecode($href));
        // We should never reach this point.
        break;
}
// When we reach this point, we can close the tab or window where BBB was opened.
echo $OUTPUT->header();
// Behat does not like when we close the Windows as it is expecting to locate
// on click part of the pages (bug with selenium raising an exception). So this is a workaround.
if (!defined('BEHAT_SITE_RUNNING')) {
    $PAGE->requires->js_call_amd('mod_bigbluebuttonbn/rooms', 'setupWindowAutoClose');
}
echo $OUTPUT->footer();
