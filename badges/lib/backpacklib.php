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
 * External backpack library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

// Adopted from https://github.com/jbkc85/openbadges-class-php.
// Author Jason Cameron <jbkc85@gmail.com>.

class OpenBadgesBackpackHandler {
    private $backpack;
    private $email;
    private $backpackuid = 0;

    public function __construct($record) {
        $this->backpack = $record->backpackurl;
        $this->email = $record->email;
        $this->backpackuid = isset($record->backpackuid) ? $record->backpackuid : 0;
    }

    public function curl_request($action, $collection = null) {
        $curl = new curl();

        switch($action) {
            case 'user':
                $url = $this->backpack . "/displayer/convert/email";
                $param = array('email' => $this->email);
                break;
            case 'groups':
                $url = $this->backpack . '/displayer/' . $this->backpackuid . '/groups.json';
                break;
            case 'badges':
                $url = $this->backpack . '/displayer/' . $this->backpackuid . '/group/' . $collection . '.json';
                break;
        }

        $curl->setHeader(array('Accept: application/json', 'Expect:'));
        $options = array(
            'FRESH_CONNECT'     => true,
            'RETURNTRANSFER'    => true,
            'FORBID_REUSE'      => true,
            'HEADER'            => 0,
            'CONNECTTIMEOUT'    => 3,
            // Follow redirects with the same type of request when sent 301, or 302 redirects.
            'CURLOPT_POSTREDIR' => 3
        );

        if ($action == 'user') {
            $out = $curl->post($url, $param, $options);
        } else {
            $out = $curl->get($url, array(), $options);
        }

        return json_decode($out);
    }

    private function check_status($status) {
        switch($status) {
            case "missing":
                $response = array(
                    'status'  => $status,
                    'message' => get_string('error:nosuchuser', 'badges')
                );
                return $response;
        }
    }

    public function get_collections() {
        $json = $this->curl_request('user', $this->email);
        if (isset($json->status)) {
            if ($json->status != 'okay') {
                return $this->check_status($json->status);
            } else {
                $this->backpackuid = $json->userId;
                return $this->curl_request('groups');
            }
        }
    }

    public function get_badges($collection) {
        $json = $this->curl_request('user', $this->email);
        if (isset($json->status)) {
            if ($json->status != 'okay') {
                return $this->check_status($json->status);
            } else {
                return $this->curl_request('badges', $collection);
            }
        }
    }

    public function get_url() {
        return $this->backpack;
    }
}

/**
 * Create and send a verification email to the email address supplied.
 *
 * Since we're not sending this email to a user, email_to_user can't be used
 * but this function borrows largely the code from that process.
 *
 * @param string $email the email address to send the verification email to.
 * @return true if the email was sent successfully, false otherwise.
 */
function send_verification_email($email) {
    global $DB, $USER;

    // Store a user secret (badges_email_verify_secret) and the address (badges_email_verify_address) as users prefs.
    // The address will be used by edit_backpack_form for display during verification and to facilitate the resending
    // of verification emails to said address.
    $secret = random_string(15);
    set_user_preference('badges_email_verify_secret', $secret);
    set_user_preference('badges_email_verify_address', $email);

    // To, from.
    $tempuser = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
    $tempuser->email = $email;
    $noreplyuser = core_user::get_noreply_user();

    // Generate the verification email body.
    $verificationurl = '/badges/backpackemailverify.php';
    $verificationurl = new moodle_url($verificationurl);
    $verificationpath = $verificationurl->out(false);

    $site = get_site();
    $args = new stdClass();
    $args->link = $verificationpath . '?data='. $secret;
    $args->sitename = $site->fullname;
    $args->admin = generate_email_signoff();

    $messagesubject = get_string('backpackemailverifyemailsubject', 'badges', $site->fullname);
    $messagetext = get_string('backpackemailverifyemailbody', 'badges', $args);
    $messagehtml = text_to_html($messagetext, false, false, true);

    return email_to_user($tempuser, $noreplyuser, $messagesubject, $messagetext, $messagehtml);
}
