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
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 * @codingStandardsIgnoreFile
 */

namespace local_intellidata\lti;

class OAuthConsumer {
    public $key;
    public $secret;
    public $callback_url;

    public function __construct($key, $secret, $callbackurl = null) {
        $this->key = $key;
        $this->secret = $secret;
        $this->callback_url = $callbackurl;
    }

    /**
     * @return string
     */
    public function __toString() {
        return "OAuthConsumer[key=$this->key,secret=$this->secret]";
    }
}
