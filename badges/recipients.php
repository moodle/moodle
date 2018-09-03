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
 * Badge awards information
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid    = required_param('id', PARAM_INT);
$sortby     = optional_param('sort', 'dateissued', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'DESC', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

if (!in_array($sortby, array('firstname', 'lastname', 'dateissued'))) {
    $sortby = 'dateissued';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'DESC';
}

if ($page < 0) {
    $page = 0;
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        print_error('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$PAGE->set_context($context);
$PAGE->set_url('/badges/recipients.php', array('id' => $badgeid, 'sort' => $sortby, 'dir' => $sorthow));
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);
$PAGE->navbar->add($badge->name);

$output = $PAGE->get_renderer('core', 'badges');

echo $output->header();
echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);

echo $output->print_badge_status_box($badge);
$output->print_badge_tabs($badgeid, $context, 'awards');

// Add button for badge manual award.
if ($badge->has_manual_award_criteria() && has_capability('moodle/badges:awardbadge', $context) && $badge->is_active()) {
    $url = new moodle_url('/badges/award.php', array('id' => $badge->id));
    echo $OUTPUT->box($OUTPUT->single_button($url, get_string('award', 'badges')), 'clearfix mdl-align');
}

$namefields = get_all_user_name_fields(true, 'u');
$sql = "SELECT b.userid, b.dateissued, b.uniquehash, $namefields
    FROM {badge_issued} b INNER JOIN {user} u
        ON b.userid = u.id
    WHERE b.badgeid = :badgeid AND u.deleted = 0
    ORDER BY $sortby $sorthow";

$totalcount = $DB->count_records('badge_issued', array('badgeid' => $badge->id));

if ($badge->has_awards()) {
    $users = $DB->get_records_sql($sql, array('badgeid' => $badge->id), $page * BADGE_PERPAGE, BADGE_PERPAGE);
    $recipients             = new badge_recipients($users);
    $recipients->sort       = $sortby;
    $recipients->dir        = $sorthow;
    $recipients->page       = $page;
    $recipients->perpage    = BADGE_PERPAGE;
    $recipients->totalcount = $totalcount;

    echo $output->render($recipients);
} else {
    echo $output->notification(get_string('noawards', 'badges'));
}

echo $output->footer();