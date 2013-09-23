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

/*
 * URL of backpack. Currently only the Open Badges backpack
 * is supported.
 */
define('BADGE_BACKPACKURL', 'http://backpack.openbadges.org');

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

        $options = array(
            'FRESH_CONNECT'  => true,
            'RETURNTRANSFER' => true,
            'FORBID_REUSE'   => true,
            'HEADER'         => 0,
            'HTTPHEADER'     => array('Expect:'),
            'CONNECTTIMEOUT' => 3,
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
