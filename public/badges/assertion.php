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
 * Serve assertion JSON by unique hash of issued badge
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

use core_badges\local\backpack\helper;

$hash = required_param('b', PARAM_ALPHANUM); // Issued badge unique hash for badge assertion.
$action = optional_param('action', null, PARAM_BOOL); // Generates badge class if true.
// OB specification version. If it's not defined, the site will be used as default.
$obversion = helper::convert_apiversion(
    optional_param('obversion', badges_open_badges_backpack_api(), PARAM_FLOAT)
);

if (!is_null($action)) {
    $badgeid = helper::get_badgeid_from_hash($hash);
    if ($action) {
        // Display only the BadgeClass.
        redirect(new moodle_url('/badges/json/badge.php', ['id' => $badgeid, 'obversion' => $obversion]));
    } else {
        // Display only the Issuer.
        redirect(new moodle_url('/badges/json/issuer.php', ['id' => $badgeid, 'obversion' => $obversion]));
    }
}

// Display badge assertion.
redirect(new moodle_url(
    '/badges/json/assertion.php',
    [
        'b' => $hash,
        'obversion' => $obversion,
    ],
));
