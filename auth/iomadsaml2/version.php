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
 * Version information
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2022111702;    // The current plugin version (Date: YYYYMMDDXX).
$plugin->release   = 2022111701;    // Match release exactly to version.
$plugin->requires  = 2017051509;    // Requires PHP 7, 2017051509 = T12. M3.3
                                    // Strictly we require either Moodle 3.5 OR
                                    // we require Totara 3.3, but the version number
                                    // for Totara 3.3 is the same as Moodle 3.3.
$plugin->component = 'auth_iomadsaml2';  // Full name of the plugin (used for diagnostics).
$plugin->maturity  = MATURITY_STABLE;
$plugin->supported = [39, 401];     // A range of branch numbers of supported moodle versions.
