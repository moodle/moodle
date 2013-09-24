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
 * Editing badge details, criteria, messages
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$badgeid = required_param('id', PARAM_INT);
$update = optional_param('update', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

require_capability('moodle/badges:configurecriteria', $context);

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        print_error('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
}

$currenturl = new moodle_url('/badges/criteria.php', array('id' => $badge->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);

// Set up navigation and breadcrumbs.
navigation_node::override_active_url($navurl);
$PAGE->navbar->add($badge->name);

$output = $PAGE->get_renderer('core', 'badges');
$msg = optional_param('msg', '', PARAM_TEXT);
$emsg = optional_param('emsg', '', PARAM_TEXT);

if ((($update == BADGE_CRITERIA_AGGREGATION_ALL) || ($update == BADGE_CRITERIA_AGGREGATION_ANY))) {
    require_sesskey();
    $obj = new stdClass();
    $obj->id = $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->id;
    $obj->method = $update;
    if ($DB->update_record('badge_criteria', $obj)) {
        $msg = 'criteriaupdated';
    } else {
        $emsg = get_string('error:save', 'badges');
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);

if ($emsg !== '') {
    echo $OUTPUT->notification($emsg);
} else if ($msg !== '') {
    echo $OUTPUT->notification(get_string($msg, 'badges'), 'notifysuccess');
}

echo $output->print_badge_status_box($badge);
$output->print_badge_tabs($badgeid, $context, 'criteria');

if (!$badge->is_locked() && !$badge->is_active()) {
    echo $output->print_criteria_actions($badge);
}

if ($badge->has_criteria()) {
    ksort($badge->criteria);

    foreach ($badge->criteria as $crit) {
        $crit->config_form_criteria($badge);
    }
} else {
    echo $OUTPUT->box(get_string('addcriteriatext', 'badges'));
}

echo $OUTPUT->footer();