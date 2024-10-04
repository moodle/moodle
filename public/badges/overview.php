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
 * Badge overview page
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('id', PARAM_INT);
$awards = optional_param('awards', '', PARAM_ALPHANUM);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));
$title = [$badge->name];

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        throw new \moodle_exception('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $course = get_course($badge->courseid);
    $heading = format_string($course->fullname, true, ['context' => $context]);
    $title[] = $heading;

    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    $heading = get_string('administrationsite');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/badges/overview.php', array('id' => $badge->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($heading);
$PAGE->set_title(implode(\moodle_page::TITLE_SEPARATOR, $title));
$PAGE->navbar->add($badge->name);

require_capability('moodle/badges:viewbadges', $context);

echo $OUTPUT->header();
$output = $PAGE->get_renderer('core', 'badges');
$actionbar = new \core_badges\output\manage_badge_action_bar($badge, $PAGE);
echo $output->render_tertiary_navigation($actionbar);
echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);

if ($awards == 'cron') {
    echo $OUTPUT->notification(get_string('awardoncron', 'badges', ['badgename' => $badge->name]), 'info');
} else if ((int)$awards > 0) {
    echo $OUTPUT->notification(get_string('numawardstat', 'badges', ['badgename' => $badge->name, 'awards' => $awards]), 'info');
}
echo $output->print_badge_status_box($badge);
echo $output->print_badge_overview($badge, $context);

echo $OUTPUT->footer();
