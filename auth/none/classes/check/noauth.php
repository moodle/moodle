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
 * Verifies unsupported noauth setting
 *
 * @package    auth_none
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_none\check;

defined('MOODLE_INTERNAL') || die();

use core\check\result;

/**
 * Verifies unsupported noauth setting
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class noauth extends \core\check\check {

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'noauth';
        $this->name = get_string('check_noauth_name', 'auth_none');
        $this->actionlink = new \action_link(
            new \moodle_url('/admin/settings.php?section=manageauths'),
            get_string('authsettings', 'admin'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {

        if (is_enabled_auth('none')) {
            $status = result::ERROR;
            $summary = get_string('check_noauth_error', 'auth_none');
        } else {
            $status = result::OK;
            $summary = get_string('check_noauth_ok', 'auth_none');
        }
        $details = get_string('check_noauth_details', 'auth_none');

        return new result($status, $summary, $details);
    }
}

