<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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
 * Honorlock proctoring language strings.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['honorlockproctoring'] = 'Custom API functions required for the honorlock client';
$string['honorlock_url'] = 'Honorlock URL';
$string['honorlock_url_description'] = 'Honorlock Application URL';
$string['settings_header'] = 'Honorlock Proctoring Configuration';
$string['pluginname'] = "Honorlock Proctoring Service";
$string['honorlock_client_id'] = 'Honorlock Client ID';
$string['honorlock_client_id_description'] = 'The Organization Client ID generated for your organization in app.honorlock.com';
$string['honorlock_client_secret'] = 'Honorlock Client Secret';
$string['honorlock_client_secret_description'] = 'The Organization Client Secret generated for your organization in app.honorlock.com';
$string['cachedef_honorlock_api_token'] = 'Honorlock API Token Cache';

$string['privacy:metadata:local_honorlockproctoring'] = "In order to integrate with the 'Honorlock' proctoring service, user data needs to be exchanged with that service.";
$string['privacy:metadata:local_honorlockproctoring:user_id'] = "The exam taker's 'user ID' is sent from Moodle for identification on the remote system.";
$string['privacy:metadata:local_honorlockproctoring:email'] = "The exam taker's 'email' is sent to the remote system to allow for better user experience.";
$string['privacy:metadata:local_honorlockproctoring:first_name'] = "The exam taker's 'first name' is sent to the remote system to allow for a better user experience.";
$string['privacy:metadata:local_honorlockproctoring:last_name'] = "The exam taker's 'last name' is sent to the remote system to allow for a better user experience.";
$string['privacy:metadata:local_honorlockproctoring:quiz_id'] = "The 'quiz ID' is sent to the remote system to aggregate the session data.";
$string['privacy:metadata:local_honorlockproctoring:attempt_id'] = "The 'attempt ID' is sent to the remote system to aggregate the session data.";
