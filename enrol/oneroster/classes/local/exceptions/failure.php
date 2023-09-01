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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\exceptions;

use stdClass;

/**
 * IMS Failure Exception.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class failure extends exception {
    /**
     * Constructor for a new IMSx Exception.
     *
     * @param   stdClass $failure
     * @param   array $curlinfo
     * @param   moodle_url $url
     */
    public function __construct(stdClass $failure, array $curlinfo, moodle_url $url) {
        parent::__construct(
            sprintf(
                "IMSx Exception (%s/%s): %s - %s (%d)",
                $failure->imsx_codeMajor,
                $failure->imsx_severity,
                $failure->imsx_description,
                $url->out(false),
                $curlinfo['http_code']
            ),
            $curlinfo['http_code']
        );

        $this->set_url($url);
    }

}
