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
 * Serve profile image author JSON for assertion.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true); // No need for a session here.
require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$id = required_param('id', PARAM_INT); // Unique hash of badge assertion.
$badge = new badge($id);

$json = array();
$authorimage = new moodle_url('/badges/image_author_json.php', array('id' => $badge->id));
$json['id'] = $authorimage->out(false);
$json['type'] = OPEN_BADGES_V2_TYPE_AUTHOR;
if (!empty($badge->imageauthorname)) {
    $json['name'] = $badge->imageauthorname;
}
if (!empty($badge->imageauthoremail)) {
    $json['email'] = $badge->imageauthoremail;
}
if (!empty($badge->imageauthorurl)) {
    $json['url'] = $badge->imageauthorurl;
}
echo $OUTPUT->header();
echo json_encode($json);