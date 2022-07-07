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
 * Verifies if https enabled only secure cookies allowed
 *
 * This prevents redirections and sending of cookies to unsecure port.
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\http;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Verifies if https enabled only secure cookies allowed
 *
 * This prevents redirections and sending of cookies to unsecure port.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cookiesecure extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_cookiesecure_name', 'report_security');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/settings.php?section=httpsecurity#admin-cookiesecure'),
            get_string('httpsecurity', 'admin'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG;
        $details = get_string('check_cookiesecure_details', 'report_security');
        if (!is_https()) {
            $status = result::WARNING;
            $summary = get_string('check_cookiesecure_http', 'report_security');
            return new result($status, $summary, $details);
        }

        if (!is_moodle_cookie_secure()) {
            $status = result::ERROR;
            $summary = get_string('check_cookiesecure_error', 'report_security');
        } else {
            $status = result::OK;
            $summary = get_string('check_cookiesecure_ok', 'report_security');
        }
        return new result($status, $summary, $details);
    }
}

