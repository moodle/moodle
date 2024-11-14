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
 * Matrix communication plugin settings.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    // Home server URL.
    $name = new lang_string('matrixhomeserverurl', 'communication_matrix');
    $desc = new lang_string('matrixhomeserverurl_desc', 'communication_matrix');
    $settings->add(new admin_setting_configtext('communication_matrix/matrixhomeserverurl', $name, $desc, ''));

    // Home server name.
    $name = new lang_string('matrixhomeservername', 'communication_matrix');
    $desc = new lang_string('matrixhomeservername_desc', 'communication_matrix');
    $settings->add(new admin_setting_configtext('communication_matrix/matrixhomeservername', $name, $desc, ''));

    // Access token.
    $name = new lang_string('matrixaccesstoken', 'communication_matrix');
    $desc = new lang_string('matrixaccesstoken_desc', 'communication_matrix');
    $settings->add(new admin_setting_configpasswordunmask('communication_matrix/matrixaccesstoken', $name, $desc, ''));

    // Element web URL.
    $name = new lang_string('matrixelementurl', 'communication_matrix');
    $settings->add(new admin_setting_configtext('communication_matrix/matrixelementurl', $name, '', ''));
}
