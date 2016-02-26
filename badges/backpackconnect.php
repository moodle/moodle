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
 * AJAX script for validating backpack connection.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Simon Coggins <simon.coggins@totaralms.com>
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/badges/lib/backpacklib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/badgeslib.php');

require_sesskey();
require_login();
$PAGE->set_url('/badges/backpackconnect.php');
$PAGE->set_context(context_system::instance());
echo $OUTPUT->header();

// Use PHP input filtering as there is no PARAM type for
// the type of cleaning that is required (ASCII chars 32-127 only).
$assertion = filter_input(
    INPUT_POST,
    'assertion',
    FILTER_UNSAFE_RAW,
    FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
);

// Audience is the site url scheme + host + port only.
$wwwparts = parse_url($CFG->wwwroot);
$audience = $wwwparts['scheme'] . '://' . $wwwparts['host'];
$audience .= isset($wwwparts['port']) ? ':' . $wwwparts['port'] : '';
$params = 'assertion=' . urlencode($assertion) . '&audience=' .
           urlencode($audience);

$curl = new curl();
$url = 'https://verifier.login.persona.org/verify';
$options = array(
    'FRESH_CONNECT'  => true,
    'RETURNTRANSFER' => true,
    'FORBID_REUSE'   => true,
    'SSL_VERIFYPEER' => true,
    'SSL_VERIFYHOST' => 2,
    'HEADER'         => 0,
    'HTTPHEADER'     => array('Content-type: application/x-www-form-urlencoded'),
    'CONNECTTIMEOUT' => 0,
    'TIMEOUT' => 10, // Fail if data not returned within 10 seconds.
);
$result = $curl->post($url, $params, $options);

// Handle time-out and failed request.
if ($curl->errno != 0) {
    if ($curl->errno == CURLE_OPERATION_TIMEOUTED) {
        $reason = get_string('error:requesttimeout', 'badges');
    } else {
        $reason = get_string('error:requesterror', 'badges', $curl->errno);
    }
    badges_send_response('failure', $reason);
}

$data = json_decode($result);

if (!isset($data->status) || $data->status != 'okay') {
    $reason = isset($data->reason) ? $data->reason : get_string('error:connectionunknownreason', 'badges');
    badges_send_response('failure', $reason);
}

// Make sure email matches a backpack.
$check = new stdClass();
$check->backpackurl = BADGE_BACKPACKURL;
$check->email = $data->email;

$bp = new OpenBadgesBackpackHandler($check);
$request = $bp->curl_request('user');
if (isset($request->status) && $request->status == 'missing') {
    $reason = get_string('error:backpackemailnotfound', 'badges', $data->email);
    badges_send_response('failure', $reason);
} else if (empty($request->userId)) {
    $reason = get_string('error:backpackdatainvalid', 'badges');
    badges_send_response('failure', $reason);
} else {
    $backpackuid = $request->userId;
}

// Insert record.
$obj = new stdClass();
$obj->userid = $USER->id;
$obj->email = $data->email;
$obj->backpackurl = BADGE_BACKPACKURL;
$obj->backpackuid = $backpackuid;
$obj->autosync = 0;
$obj->password = '';
$DB->insert_record('badge_backpack', $obj);

// Return success indicator and email address.
badges_send_response('success', $data->email);


/**
 * Return a JSON response containing the response provided.
 *
 * @param string $status Status of the response, typically 'success' or 'failure'.
 * @param string $responsetext On success, the email address of the user,
 *                             otherwise a reason for the failure.
 * @return void Outputs the JSON and terminates the script.
 */
function badges_send_response($status, $responsetext) {
    $out = new stdClass();
    $out->status = $status;
    if ($status == 'success') {
        $out->email = $responsetext;
    } else {
        $out->reason = $responsetext;
        send_header_404();
    }
    echo json_encode($out);
    exit;
}
