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
 * Serve Issuer JSON for related badge or default Issuer if no badge is defined.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true); // No need for a session here.

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');


$id = optional_param('id', null, PARAM_INT);

if (empty($id)) {
    // Get the default issuer for this site.
    $json = badges_get_default_issuer();
} else {
    // Get the issuer for this badge.
    $badge = new badge($id);
    if ($badge->status != BADGE_STATUS_INACTIVE) {
        $json = $badge->get_badge_issuer();
    } else {
        // The badge doen't exist or not accessible for the users.
        header("HTTP/1.0 410 Gone");
        $badgeurl = new moodle_url('/badges/issuer_json.php', array('id' => $id));
        $json = ['id' => $badgeurl->out()];
        $json['error'] = get_string('error:relatedbadgedoesntexist', 'badges');
    }
}

echo $OUTPUT->header();
echo json_encode($json);
