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
 * Intermediator for handling requests from the BigBlueButton server.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Darko Miletic  (darko.miletic [at] gmail [dt] com)
 */

// We should not have any require login or MOODLE_INTERNAL Check in this file.
// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState,moodle.Files.RequireLogin.Missing
require(__DIR__ . '/../../config.php');

use Firebase\JWT\Key;
use mod_bigbluebuttonbn\broker;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\meeting;

global $PAGE, $USER, $CFG, $SESSION, $DB;

$params = $_REQUEST;

$error = broker::validate_parameters($params);
if (!empty($error)) {
    header('HTTP/1.0 400 Bad Request. ' . $error);
    return;
}
$action = $params['action'];

$instance = instance::get_from_instanceid($params['bigbluebuttonbn']);
if (empty($instance)) {
    header('HTTP/1.0 410 Gone. The activity may have been deleted');
    return;
}

$PAGE->set_context($instance->get_context());

try {
    switch (strtolower($action)) {
        case 'recording_ready':
            broker::process_recording_ready($instance, $params);
            return;
        case 'meeting_events':
            // When meeting_events callback is implemented by BigBlueButton, Moodle receives a POST request
            // which is processed in the function using super globals.
            broker::process_meeting_events($instance);
            return;
    }
    header("HTTP/1.0 400 Bad request. The action '{$action}' does not exist");
} catch (Exception $e) {
    header('HTTP/1.0 500 Internal Server Error. ' . $e->getMessage());
}
