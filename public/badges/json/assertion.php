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
 * Assertion JSON by unique hash of issued badge
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true); // No need for a session here.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/badgeslib.php');

use core_badges\local\backpack\helper;
use core_badges\local\backpack\ob_factory;

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

// Issued badge unique hash for badge assertion.
$hash = required_param('b', PARAM_ALPHANUM);
// OB specification version. If it's not defined, the site will be used as default.
$defaultobversion = helper::convert_apiversion(badges_open_badges_backpack_api());
$obversion = optional_param('obversion', $defaultobversion, PARAM_ALPHANUM);

$assertion = ob_factory::create_assertion_exporter_from_hash($hash, $obversion);
if ($assertion->is_revoked()) {
    header("HTTP/1.0 410 Gone");
}

echo $OUTPUT->header();
echo $assertion->get_json();
