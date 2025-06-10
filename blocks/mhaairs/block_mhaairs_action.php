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

define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);
require_once('../../config.php');
global $CFG;
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->dirroot.'/blocks/mhaairs/block_mhaairs_util.php');

$PAGE->set_context(context_system::instance());

$secure = '';
if (isset($_SERVER['HTTPS'])) {
    $secure   = filter_var($_SERVER['HTTPS'], FILTER_SANITIZE_STRING);
}
$token = optional_param('token', 'test', PARAM_ALPHANUM);
$action = optional_param('action', null, PARAM_ALPHA);
$username = optional_param('username', null, PARAM_USERNAME);
$userid = optional_param('userid', null, PARAM_INT);
$password = optional_param('password', null, PARAM_TEXT);
$identitytype = optional_param('identitytype', null, PARAM_TEXT);

if (empty($secure) and !empty($CFG->block_mhaairs_sslonly)) {
    echo 'Connection must be secured with SSL';
    return;
}

$secret = !empty($CFG->block_mhaairs_shared_secret) ? $CFG->block_mhaairs_shared_secret : '';

$result = null;

switch ($action) {
    case "test":
        $result = "OK";
        break;
    case "ValidateLogin":
        $result = MHUtil::validate_login($token, $secret, $username, $password);
        break;
    case "GetUserInfo":
        $result = MHUtil::get_user_info($token, $secret, $identitytype);
        break;
    case "GetServerTime":
        $result = MHUtil::get_time_stamp();
        break;
    default:
        break;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
