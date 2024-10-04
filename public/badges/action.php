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
 * Page to handle actions associated with badges management.
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
$copy = optional_param('copy', 0, PARAM_BOOL);
$return = optional_param('return', 0, PARAM_LOCALURL);

require_login();

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

if ($badge->type == BADGE_TYPE_COURSE) {
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
$PAGE->set_url('/badges/action.php', array('id' => $badge->id));

if ($return !== 0) {
    $returnurl = new moodle_url($return);
} else {
    $returnurl = new moodle_url('/badges/overview.php', array('id' => $badge->id));
}
$returnurl->remove_params('awards');

if ($copy) {
    require_sesskey();
    require_capability('moodle/badges:createbadge', $context);

    $cloneid = $badge->make_clone();
    // If a user can edit badge details, they will be redirected to the edit page.
    if (has_capability('moodle/badges:configuredetails', $context)) {
        redirect(new moodle_url('/badges/edit.php', array('id' => $cloneid, 'action' => 'badge')));
    }
    redirect(new moodle_url('/badges/overview.php', array('id' => $cloneid)));
}
