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
 * Display details of an issued badge with criteria and evidence
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$id = required_param('hash', PARAM_ALPHANUM);
$bake = optional_param('bake', 0, PARAM_BOOL);

$PAGE->set_context(context_system::instance());
$output = $PAGE->get_renderer('core', 'badges');

$badge = new issued_badge($id);

if ($bake && ($badge->recipient->id == $USER->id)) {
    $name = str_replace(' ', '_', $badge->badgeclass['name']) . '.png';
    ob_start();
    $file = badges_bake($id, $badge->badgeid);
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="'. $name .'"');
    readfile($file);
    ob_flush();
}

$PAGE->set_url('/badges/badge.php', array('hash' => $id));
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('issuedbadge', 'badges'));

if (isloggedin()) {
    $PAGE->set_heading($badge->badgeclass['name']);
    $PAGE->navbar->add($badge->badgeclass['name']);
    $url = new moodle_url('/badges/mybadges.php');
    navigation_node::override_active_url($url);
}

// Include JS files for backpack support.
badges_setup_backpack_js();

echo $OUTPUT->header();

echo $output->render($badge);

echo $OUTPUT->footer();
