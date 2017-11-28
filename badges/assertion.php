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

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true); // No need for a session here.

require_once(__DIR__ . '/../config.php');

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$hash = required_param('b', PARAM_ALPHANUM); // Issued badge unique hash for badge assertion.
$action = optional_param('action', null, PARAM_BOOL); // Generates badge class if true.

$assertion = new core_badges_assertion($hash);

if (!is_null($action)) {
    // Get badge class or issuer information depending on $action.
    $json = ($action) ? $assertion->get_badge_class() : $assertion->get_issuer();
} else {
    // Otherwise, get badge assertion.
    $column = $DB->sql_compare_text('uniquehash', 255);
    if ($DB->record_exists_sql(sprintf('SELECT * FROM {badge_issued} WHERE %s = ?', $column), array($hash))) {
        $json = $assertion->get_badge_assertion();
    } else { // Revoked badge.
        header("HTTP/1.0 410 Gone");
        echo json_encode(array("revoked" => true));
        die();
    }
}


echo $OUTPUT->header();
echo json_encode($json);
