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
 * AWS helper class. Contains useful functions when interacting with the SDK.
 * This class was based on the local_aws helper developed by Catalyst IT.
 *
 * @package    local_aws
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_aws_sdk\local;

/**
 * This class contains functions that help plugins to interact with the AWS SDK.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_helper {
    /**
     * This creates a proxy string suitable for use with the AWS SDK.
     *
     * @return string the string to use for proxy settings.
     */
    public static function get_proxy_string() {
        global $CFG;
        $proxy = '';
        if (empty($CFG->proxytype)) {
            return $proxy;
        }
        if ($CFG->proxytype === 'SOCKS5') {
            // If it is a SOCKS proxy, append the protocol info.
            $protocol = 'socks5://';
        } else {
            $protocol = '';
        }
        if (!empty($CFG->proxyhost)) {
            $proxy = $CFG->proxyhost;
            if (!empty($CFG->proxyport)) {
                $proxy .= ':'. $CFG->proxyport;
            }
            if (!empty($CFG->proxyuser) && !empty($CFG->proxypassword)) {
                $proxy = $protocol . $CFG->proxyuser . ':' . $CFG->proxypassword . '@' . $proxy;
            }
        }
        return $proxy;
    }
}
