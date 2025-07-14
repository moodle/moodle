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
 * Endorsement information
 *
 * @package    core_badges
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

use core_badges\form\endorsement;

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('id', PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$title = [get_string('bendorsement', 'badges'), $badge->name];
$navurl = new moodle_url('/badges/index.php', ['type' => $badge->type]);
require_capability('moodle/badges:configuredetails', $context);

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        throw new \moodle_exception('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $course = get_course($badge->courseid);
    $heading = format_string($course->fullname, true, ['context' => $context]);
    $title[] = $heading;
    $navurl = new moodle_url('/badges/index.php', ['type' => $badge->type, 'id' => $badge->courseid]);
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    $heading = get_string('administrationsite');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/badges/endorsement.php', ['id' => $badgeid]);
$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($heading);
$PAGE->set_title(implode(\moodle_page::TITLE_SEPARATOR, $title));
$PAGE->navbar->add($badge->name);

$output = $PAGE->get_renderer('core', 'badges');
$msg = optional_param('msg', '', PARAM_TEXT);
$emsg = optional_param('emsg', '', PARAM_TEXT);

if ($msg !== '') {
    $msg = get_string($msg, 'badges');
}

echo $OUTPUT->header();

$actionbar = new \core_badges\output\manage_badge_action_bar($badge, $PAGE);
echo $output->render_tertiary_navigation($actionbar);

echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);
echo $output->print_badge_status_box($badge);

$form = new endorsement($currenturl, ['badge' => $badge]);
if ($form->is_cancelled()) {
    redirect(new moodle_url('/badges/overview.php', ['id' => $badgeid]));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    $endorsement = new stdClass();
    $endorsement->badgeid = $badgeid;
    $endorsement->issuername = $data->issuername;
    $endorsement->issueremail = $data->issueremail;
    $endorsement->issuerurl = $data->issuerurl;
    $endorsement->claimid = $data->claimid;
    $endorsement->claimcomment = strip_tags($data->claimcomment);
    $endorsement->dateissued = $data->dateissued;

    if ($badge->save_endorsement($endorsement)) {
        $msg = get_string('changessaved');
    } else {
        $emsg = get_string('error:save', 'badges');
    }
}

if ($emsg !== '') {
    echo $OUTPUT->notification($emsg);

} else if ($msg !== '') {
    echo $OUTPUT->notification($msg, 'notifysuccess');
}
echo $output->notification(get_string('noteendorsement', 'badges'), 'info');
$form->display();
echo $OUTPUT->footer();
