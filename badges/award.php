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
 * Handle manual badge award.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/lib/awardlib.php');

$badgeid = required_param('id', PARAM_INT);
$role = optional_param('role', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$isadmin = is_siteadmin($USER);

$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

if ($badge->type == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
}

require_capability('moodle/badges:awardbadge', $context);

$url = new moodle_url('/badges/award.php', array('id' => $badgeid));
$PAGE->set_url($url);
$PAGE->set_context($context);

// Set up navigation and breadcrumbs.
$strrecipients = get_string('recipients', 'badges');
navigation_node::override_active_url($navurl);
$PAGE->navbar->add($badge->name, new moodle_url('overview.php', array('id' => $badge->id)))->add($strrecipients);
$PAGE->set_title($strrecipients);
$PAGE->set_heading($badge->name);
$PAGE->set_pagelayout('standard');

if (!$badge->is_active()) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('donotaward', 'badges'));
    echo $OUTPUT->footer();
    die();
}

$output = $PAGE->get_renderer('core', 'badges');

// Roles that can award this badge.
$accepted_roles = array_keys($badge->criteria[BADGE_CRITERIA_TYPE_MANUAL]->params);

// If site admin, select a role to award a badge.
if ($isadmin) {
    list($usertest, $userparams) = $DB->get_in_or_equal($accepted_roles, SQL_PARAMS_NAMED, 'existing', true);
    $options = $DB->get_records_sql('SELECT * FROM {role} WHERE id ' . $usertest, $userparams);
    foreach ($options as $p) {
        $select[$p->id] = role_get_name($p);
    }
    if (!$role) {
        echo $OUTPUT->header();
        echo $OUTPUT->box(get_string('adminaward', 'badges') . $OUTPUT->single_select(new moodle_url($PAGE->url), 'role', $select));
        echo $OUTPUT->footer();
        die();
    } else {
        $issuerrole = new stdClass();
        $issuerrole->roleid = $role;
        $roleselect = get_string('adminaward', 'badges') . $OUTPUT->single_select(new moodle_url($PAGE->url), 'role', $select, $role);
    }
} else {
    // Current user's role.
    $roles = get_user_roles($context, $USER->id);
    $issuerrole = array_shift($roles);
    if (!isset($issuerrole->roleid) || !in_array($issuerrole->roleid, $accepted_roles)) {
        echo $OUTPUT->header();
        $rlink = html_writer::link(new moodle_url('recipients.php', array('id' => $badge->id)), get_string('recipients', 'badges'));
        echo $OUTPUT->notification(get_string('notacceptedrole', 'badges', $rlink));
        echo $OUTPUT->footer();
        die();
    }
}
$options = array(
        'badgeid' => $badge->id,
        'context' => $context,
        'issuerid' => $USER->id,
        'issuerrole' => $issuerrole->roleid
        );
$existingselector = new badge_existing_users_selector('existingrecipients', $options);
$recipientselector = new badge_potential_users_selector('potentialrecipients', $options);
$recipientselector->set_existing_recipients($existingselector->find_users(''));

if (optional_param('award', false, PARAM_BOOL) && data_submitted() && has_capability('moodle/badges:awardbadge', $context)) {
    require_sesskey();
    $users = $recipientselector->get_selected_users();
    foreach ($users as $user) {
        if (process_manual_award($user->id, $USER->id, $issuerrole->roleid, $badgeid)) {
            // If badge was successfully awarded, review manual badge criteria.
            $data = new stdClass();
            $data->crit = $badge->criteria[BADGE_CRITERIA_TYPE_MANUAL];
            $data->userid = $user->id;
            badges_award_handle_manual_criteria_review($data);
        } else {
            echo $OUTPUT->error_text(get_string('error:cannotawardbadge', 'badges'));
        }
    }

    $recipientselector->invalidate_selected_users();
    $existingselector->invalidate_selected_users();
    $recipientselector->set_existing_recipients($existingselector->find_users(''));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strrecipients);

if ($isadmin) {
    echo $OUTPUT->box($roleselect);
}

echo $output->recipients_selection_form($existingselector, $recipientselector);
echo $OUTPUT->footer();
