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
 * Related badges information
 *
 * @package    core
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/related_form.php');

$badgeid = required_param('id', PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);
$lang = current_language();

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));
require_capability('moodle/badges:configuredetails', $context);

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

$currenturl = new moodle_url('/badges/related.php', array('id' => $badge->id));
$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);
$PAGE->navbar->add($badge->name);
$output = $PAGE->get_renderer('core', 'badges');
$msg = optional_param('msg', '', PARAM_TEXT);
$emsg = optional_param('emsg', '', PARAM_TEXT);
$url = new moodle_url('/badges/related.php', array('id' => $badge->id, 'action' => 'add'));

$mform = new edit_relatedbadge_form($url, array('badge' => $badge));
if ($mform->is_cancelled()) {
    redirect($currenturl);
} else if ($mform->is_submitted() && $mform->is_validated() && ($data = $mform->get_data())) {

    if (isset($data->relatedbadgeids)) {
        $badge->add_related_badges($data->relatedbadgeids);
    }
    redirect($currenturl);
}
echo $OUTPUT->header();
echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);
echo $output->print_badge_status_box($badge);

$output->print_badge_tabs($badgeid, $context, 'brelated');
if ($emsg !== '') {
    echo $OUTPUT->notification($emsg);
} else if ($msg !== '') {
    echo $OUTPUT->notification(get_string($msg, 'badges'), 'notifysuccess');
}

echo $output->notification(get_string('noterelated', 'badges'), 'info');
if (is_null($action)) {
    if (!$badge->is_active() && !$badge->is_locked()) {
        echo $OUTPUT->box($OUTPUT->single_button($url, get_string('addrelated', 'badges')), 'clearfix mdl-align');
    }
    if ($badge->has_related()) {
        $badgerelated = $badge->get_related_badges();
        $renderrelated = new \core_badges\output\badge_related($badgerelated, $badgeid);
        echo $output->render($renderrelated);
    } else {
        echo $output->notification(get_string('norelated', 'badges'));
    }
} else if ($action == 'add') {
    $mform->display();
}
echo $OUTPUT->footer();
