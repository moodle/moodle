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

use core_badges\reportbuilder\local\systemreports\recipients;
use core_reportbuilder\system_report_factory;

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid    = required_param('id', PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

require_capability('moodle/badges:viewawarded', $context);

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        throw new \moodle_exception('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $course = get_course($badge->courseid);
    $heading = format_string($course->fullname, true, ['context' => $context]);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    $heading = get_string('administrationsite');
    navigation_node::override_active_url($navurl, true);
}

$PAGE->set_context($context);
$PAGE->set_url('/badges/recipients.php', ['id' => $badgeid]);
$PAGE->set_heading($heading);
$PAGE->set_title($badge->name);
$PAGE->navbar->add($badge->name);

/** @var core_badges_renderer $output */
$output = $PAGE->get_renderer('core', 'badges');

echo $output->header();

$actionbar = new \core_badges\output\recipients_action_bar($badge, $PAGE);
echo $output->render_tertiary_navigation($actionbar);

echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);
echo $output->print_badge_status_box($badge);

$report = system_report_factory::create(recipients::class, $PAGE->context, '', '', 0, ['badgeid' => $badge->id]);
echo $report->output();

echo $output->footer();
