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
 * Certificate expiry check
 *
 * @package    auth_iomadsaml2
 * @copyright  2021 Catalyst IT Australia
 * @author     Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2\check;

use core\check\check;
use core\check\result;

/**
 * Cert expiry check
 *
 * @package    auth_iomadsaml2
 * @copyright  2021 Catalyst IT Australia
 * @author     Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class certificateexpiry extends check {

    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        $this->id = 'certificateexpiry';
        $this->name = get_string('checkcertificateexpiry', 'auth_iomadsaml2');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/auth/iomadsaml2/cert.php'),
            get_string('certificatedetails', 'auth_iomadsaml2'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result() : result {
        global $CFG, $iomadsaml2auth;

        $path = $iomadsaml2auth->certcrt;
        $data = openssl_x509_parse(file_get_contents($path));

        $now = time();
        $expires = $data['validTo_time_t'];
        $delta = time() - $expires;
        $formatdelta = format_time($delta);

        // Critical when the certicate has expired.
        if ($now > $expires) {
            $summary = get_string('checkcertificateexpired', 'auth_iomadsaml2', $formatdelta);
            return new result(result::CRITICAL, $summary, '');
        }

        // Error if the certificate expiration is imminent the next week.
        if ($now > $expires - WEEKSECS) {
            $summary = get_string('checkcertificatewarn', 'auth_iomadsaml2', $formatdelta);
            return new result(result::ERROR, $summary, '');
        }

        // Warn if the certificate expiration is in the next 28 days.
        if ($now > $expires - 4 * WEEKSECS) {
            $summary = get_string('checkcertificatewarn', 'auth_iomadsaml2', $formatdelta);
            return new result(result::WARNING, $summary, '');
        }

        $summary = get_string('checkcertificateok', 'auth_iomadsaml2', $formatdelta);
        return new result(result::OK, $summary, '');
    }
}

