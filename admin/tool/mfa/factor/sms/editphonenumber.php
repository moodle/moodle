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
 * Edit phonenumber redirect
 *
 * @package     factor_sms
 * @copyright   2023 Raquel Ortega <raquel.ortega@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '../../../../../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('error:isguestuser', 'tool_mfa');
}

$sesskey = optional_param('sesskey', false, PARAM_TEXT);
require_sesskey();

// Remove session phone number.
unset($SESSION->tool_mfa_sms_number);
// Clean temp secrets code.
$secretmanager = new \tool_mfa\local\secret_manager('sms');
$secretmanager->cleanup_temp_secrets();

redirect(new \moodle_url('/admin/tool/mfa/action.php', [
    'action' => 'setup',
    'factor' => 'sms',
]));
