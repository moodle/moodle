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
 * Display details of a badge.
 *
 * @package    core_badges
 * @copyright  2022 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('id', PARAM_ALPHANUM);
$badgeclass = new \core_badges\output\badgeclass($badgeid);

$context = !empty($badgeclass) ? $badgeclass->context : \context_system::instance();
$PAGE->set_context($context);
$output = $PAGE->get_renderer('core', 'badges');
$PAGE->set_url('/badges/badgeclass.php', ['id' => $badgeid]);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('badgedetails', 'badges'));

if (!empty($badgeclass->badge)) {
    $PAGE->navbar->add($badgeclass->badge->name);
    $url = new moodle_url($CFG->wwwroot);
    navigation_node::override_active_url($url);

    echo $OUTPUT->header();
    echo $output->render($badgeclass);
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('error:relatedbadgedoesntexist', 'badges'));
}

// Trigger event, badge viewed.
$other = ['badgeid' => $badgeclass->badgeid];
$eventparams = ['context' => $PAGE->context, 'other' => $other];

$event = \core\event\badge_viewed::create($eventparams);
$event->trigger();

echo $OUTPUT->footer();
