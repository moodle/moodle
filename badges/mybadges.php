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
 * Displays user badges for badges management in own profile
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

$page        = optional_param('page', 0, PARAM_INT);
$search      = optional_param('search', '', PARAM_CLEAN);
$clearsearch = optional_param('clearsearch', '', PARAM_TEXT);
$download    = optional_param('download', 0, PARAM_INT);
$hash        = optional_param('hash', '', PARAM_ALPHANUM);
$downloadall = optional_param('downloadall', false, PARAM_BOOL);
$hide        = optional_param('hide', 0, PARAM_INT);
$show        = optional_param('show', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$url = new moodle_url('/badges/mybadges.php');
$PAGE->set_url($url);

if (isguestuser()) {
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('error:guestuseraccess', 'badges'), 'notifyproblem');
    echo $OUTPUT->footer();
    die();
}

if ($page < 0) {
    $page = 0;
}

if ($clearsearch) {
    $search = '';
}

if ($hide) {
    require_sesskey();
    $DB->set_field('badge_issued', 'visible', 0, array('id' => $hide, 'userid' => $USER->id));
} else if ($show) {
    require_sesskey();
    $DB->set_field('badge_issued', 'visible', 1, array('id' => $show, 'userid' => $USER->id));
} else if ($download && $hash) {
    require_sesskey();
    $badge = new badge($download);
    $name = str_replace(' ', '_', $badge->name) . '.png';
    $filehash = badges_bake($hash, $download, $USER->id, true);
    $fs = get_file_storage();
    $file = $fs->get_file_by_hash($filehash);
    send_stored_file($file, 0, 0, true, array('filename' => $name));
} else if ($downloadall) {
    require_sesskey();
    badges_download($USER->id);
}

$context = context_user::instance($USER->id);
require_capability('moodle/badges:manageownbadges', $context);

$PAGE->set_context($context);

$title = get_string('badges', 'badges');
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('standard');

// Include JS files for backpack support.
badges_setup_backpack_js();

$output = $PAGE->get_renderer('core', 'badges');
$badges = badges_get_user_badges($USER->id);

echo $OUTPUT->header();
$totalcount = count($badges);
$records = badges_get_user_badges($USER->id, null, $page, BADGE_PERPAGE, $search);

$userbadges             = new badge_user_collection($records, $USER->id);
$userbadges->sort       = 'dateissued';
$userbadges->dir        = 'DESC';
$userbadges->page       = $page;
$userbadges->perpage    = BADGE_PERPAGE;
$userbadges->totalcount = $totalcount;
$userbadges->search     = $search;

echo $output->render($userbadges);

echo $OUTPUT->footer();
