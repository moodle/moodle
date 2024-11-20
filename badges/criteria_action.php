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
 * Processing actions with badge criteria.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = optional_param('badgeid', 0, PARAM_INT); // Badge ID.
$crit    = optional_param('crit', 0, PARAM_INT);
$type    = optional_param('type', 0, PARAM_INT); // Criteria type.
$delete  = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

$return = new moodle_url('/badges/criteria.php', array('id' => $badgeid));
$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

// Make sure that no actions available for locked or active badges.
if ($badge->is_active() || $badge->is_locked()) {
    redirect($return);
}

if ($badge->type == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$PAGE->set_context($context);
$PAGE->set_url('/badges/criteria_action.php');
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);

if ($delete && has_capability('moodle/badges:configurecriteria', $context)) {
    if ($type == BADGE_CRITERIA_TYPE_OVERALL) {
        redirect($return, get_string('error:cannotdeletecriterion', 'badges'));
    }
    if (!$confirm) {
        $optionsyes = array('confirm' => 1, 'sesskey' => sesskey(), 'badgeid' => $badgeid, 'delete' => true, 'type' => $type);

        $strdeletecheckfull = get_string('delcritconfirm', 'badges');

        echo $OUTPUT->header();
        $formcontinue = new single_button(new moodle_url('/badges/criteria_action.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
        echo $OUTPUT->footer();

        die();
    }

    require_sesskey();
    if (count($badge->criteria) == 2) {
        // Remove overall criterion as well.
        $badge->criteria[$type]->delete();
        $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->delete();
    } else {
        $badge->criteria[$type]->delete();
    }
    $return->param('msg', 'criteriadeleted');
    redirect($return);
}

redirect($return);