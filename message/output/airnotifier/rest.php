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
 * Provide interface for AJAX device actions
 *
 * @copyright 2012 Jerome Mouneyrac
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message_airnotifier
 */

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot.'/message/output/airnotifier/lib.php');

// Initialise ALL the incoming parameters here, up front.
$field      = optional_param('field', '', PARAM_ALPHA);
$id         = required_param('id', PARAM_INT);
$pageaction = optional_param('action', '', PARAM_ALPHA); // Used to simulate a DELETE command

$usercontext = context_user::instance($USER->id);

$PAGE->set_url('/message/output/airnotifier/rest.php');
$PAGE->set_context($usercontext);
require_login();
require_sesskey();

echo $OUTPUT->header(); // send headers

// OK, now let's process the parameters and do stuff
// MDL-10221 the DELETE method is not allowed on some web servers, so we simulate it with the action URL param
$requestmethod = $_SERVER['REQUEST_METHOD'];
if ($pageaction == 'DELETE') {
    $requestmethod = 'DELETE';
}

$device = $DB->get_record('airnotifier_user_devices', array('id' => $id), '*', MUST_EXIST);

$airnotifiermanager = new airnotifier_manager();

switch($requestmethod) {
    case 'POST':
                switch ($field) {
                    case 'enable':
                        require_capability('message/airnotifier:managedevice', $usercontext);
                        $device->enable = required_param('enable', PARAM_BOOL);
                        $DB->update_record('airnotifier_user_devices', $device);
                        break;
                }
        break;

    case 'DELETE':
                require_capability('message/airnotifier:managedevice', $usercontext);
                $DB->delete_records('airnotifier_user_devices', array('id' => $id));
                break;
}
