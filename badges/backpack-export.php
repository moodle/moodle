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
 * Export badges to the backpack site.
 *
 * @package    core_badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$hash = optional_param('hash', null, PARAM_RAW);

$PAGE->set_pagelayout('admin');
$url = new moodle_url('/badges/backpack-export.php');

require_login();
if (empty($CFG->badges_allowexternalbackpack) || empty($CFG->enablebadges)) {
    redirect($CFG->wwwroot);
}

$backpack = badges_get_user_backpack();
if (badges_open_badges_backpack_api($backpack->id) != OPEN_BADGES_V2P1) {
    throw new coding_exception('backpacks only support Open Badges V2.1');
}

$userbadges = badges_get_user_badges($USER->id);
$context = context_user::instance($USER->id);

$PAGE->set_context($context);
$PAGE->set_url($url);
$title = get_string('badges', 'badges');
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('standard');

$redirecturl = new moodle_url('/badges/mybadges.php');
if ($hash) {
    $api = new core_badges\backpack_api2p1($backpack);
    $notify = $api->put_assertions($hash);
    if (!empty($notify['status']) && $notify['status'] == \core\output\notification::NOTIFY_SUCCESS) {
        redirect($redirecturl, $notify['message'], null, \core\output\notification::NOTIFY_SUCCESS);
    } else if (!empty($notify['status']) && $notify['status'] == \core\output\notification::NOTIFY_ERROR) {
        redirect($redirecturl, $notify['message'], null, \core\output\notification::NOTIFY_ERROR);
    }
}
redirect($redirecturl);