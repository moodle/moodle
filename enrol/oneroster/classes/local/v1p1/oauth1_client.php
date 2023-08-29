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

namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\rostering_client as rostering_client_interface;
use enrol_oneroster\local\oauth1_client as abstract_oauth_client;
use enrol_oneroster\local\oneroster_client as root_oneroster_client;
use enrol_oneroster\local\v1p1\oneroster_client as versioned_oneroster_client;

/**
 * One Roster v1p1 client utilising OAuth 1.0.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oauth1_client extends abstract_oauth_client implements client_interface, rostering_client_interface {
    use root_oneroster_client;
    use versioned_oneroster_client;
}
