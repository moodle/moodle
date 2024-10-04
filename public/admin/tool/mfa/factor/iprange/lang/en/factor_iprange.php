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
 * Language strings.
 *
 * @package     factor_iprange
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowedipsempty'] = 'Nobody will currently pass this factor! You can add your own IP address (<i>{$a->ip}</i>)';
$string['allowedipshasmyip'] = 'Your IP (<i>{$a->ip}</i>) is in the list and you will pass this factor.';
$string['allowedipshasntmyip'] = 'Your IP (<i>{$a->ip}</i>) is not in the list and you will not pass this factor.';
$string['pluginname'] = 'IP range';
$string['privacy:metadata'] = 'The IP range factor plugin does not store any personal data.';
$string['settings:description'] = 'Enable automatic user verification using IP addresses. This doesn\'t require user setup and can provide a secure, seamless login on trusted networks.';
$string['settings:safeips'] = 'Safe IP ranges';
$string['settings:safeips_help'] = 'Enter a list of IP addresses or subnets to be counted as a pass in factor. If empty nobody will pass this factor. {$a->info} {$a->syntax}';
$string['settings:shortdescription'] = 'Use IP addresses to automatically verify users\' identity.';
$string['summarycondition'] = 'is on a secured network';
