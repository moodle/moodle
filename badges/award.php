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

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/lib/awardlib.php');

$badgeid = required_param('id', PARAM_INT);
$role = optional_param('role', 0, PARAM_INT);
$award = optional_param('award', false, PARAM_BOOL);
$revoke = optional_param('revoke', false, PARAM_BOOL);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$isadmin = is_siteadmin($USER);

$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

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

require_capability('moodle/badges:awardbadge', $context);

$url = new moodle_url('/badges/award.php', array('id' => $badgeid, 'role' => $role));
$PAGE->set_url($url);
$PAGE->set_context($context);

// Set up navigation and breadcrumbs.
$strrecipients = get_string('recipients', 'badges');
$PAGE->navbar->add($badge->name, new moodle_url('overview.php', array('id' => $badge->id)))
    ->add($strrecipients, new moodle_url('recipients.php', array('id' => $badge->id)))
    ->add(get_string('award', 'badges'));
$PAGE->set_title($strrecipients);
$PAGE->set_heading($heading);

if (!$badge->is_active()) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('donotaward', 'badges'));
    echo $OUTPUT->footer();
    die();
}

$returnurl = new moodle_url('recipients.php', array('id' => $badge->id));
$returnlink = html_writer::link($returnurl, $strrecipients);
$actionbar = new \core_badges\output\standard_action_bar($PAGE, $badge->type, false, false, $returnurl);
$output = $PAGE->get_renderer('core', 'badges');
$tertiarynav = $output->render_tertiary_navigation($actionbar);

// Roles that can award this badge.
$acceptedroles = array_keys($badge->criteria[BADGE_CRITERIA_TYPE_MANUAL]->params);

if (empty($acceptedroles)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('notacceptedrole', 'badges', $returnlink));
    echo $OUTPUT->footer();
    die();
}

// Get groupmode and currentgroup before going further.
$groupmode = groups_get_course_groupmode($COURSE);  // Groups are being used.
$currentgroup = groups_get_course_group($COURSE, true); // Get active group.

// Check groupmode (SEPARATEGROUPS), currentgroup and capability (or admin).
if ($groupmode == SEPARATEGROUPS && empty($currentgroup) &&
    !has_capability('moodle/site:accessallgroups', $context) && !is_siteadmin() ) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    die();
}

if (count($acceptedroles) > 1) {
    // If there is more than one role that can award a badge, prompt user to make a selection.
    // If it is an admin, include all accepted roles, otherwise only the ones that current user has in this context.
    if ($isadmin) {
        $selection = $acceptedroles;
    } else {
        // Get all the roles that user has and use the ones required by this badge.
        $roles = get_user_roles($context, $USER->id);
        $roleids = array_map(function($o) {
            return $o->roleid;
        }, $roles);
        $selection = array_intersect($acceptedroles, $roleids);
    }

    if (!empty($selection)) {
        list($usertest, $userparams) = $DB->get_in_or_equal($selection, SQL_PARAMS_NAMED, 'existing', true);
        $options = $DB->get_records_sql('SELECT * FROM {role} WHERE id ' . $usertest, $userparams);
        foreach ($options as $p) {
            $select[$p->id] = role_get_name($p);
        }
        if (!$role) {
            $pageurl = new moodle_url('/badges/award.php', array('id' => $badgeid));
            echo $OUTPUT->header();
            echo $tertiarynav;
            echo $OUTPUT->box($OUTPUT->single_select(new moodle_url($pageurl), 'role', $select, '', array('' => 'choosedots'),
                null, array('label' => get_string('selectaward', 'badges'))));
            echo $OUTPUT->footer();
            die();
        } else {
            $pageurl = new moodle_url('/badges/award.php', array('id' => $badgeid));
            $issuerrole = new stdClass();
            $issuerrole->roleid = $role;
            $roleselect = $OUTPUT->single_select(new moodle_url($pageurl), 'role', $select, $role, null, null,
                array('label' => get_string('selectaward', 'badges')));
        }
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('notacceptedrole', 'badges', $returnlink));
        echo $OUTPUT->footer();
        die();
    }
} else {
    // User has to be an admin or the one with the required role.
    $users = get_role_users($acceptedroles[0], $context, true, 'u.id', 'u.id ASC');
    $usersids = array_keys($users);
    if (!$isadmin && !in_array($USER->id, $usersids)) {
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('notacceptedrole', 'badges', $returnlink));
        echo $OUTPUT->footer();
        die();
    } else {
        $issuerrole = new stdClass();
        $issuerrole->roleid = $acceptedroles[0];
    }
}

$options = array(
        'badgeid' => $badge->id,
        'context' => $context,
        'issuerid' => $USER->id,
        'issuerrole' => $issuerrole->roleid,
        'currentgroup' => $currentgroup,
        'url' => $url,
    );
$existingselector = new badge_existing_users_selector('existingrecipients', $options);
$recipientselector = new badge_potential_users_selector('potentialrecipients', $options);
$recipientselector->set_existing_recipients($existingselector->find_users(''));

if ($award && data_submitted() && has_capability('moodle/badges:awardbadge', $context)) {
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
} else if ($revoke && data_submitted() && has_capability('moodle/badges:revokebadge', $context)) {
    require_sesskey();
    $users = $existingselector->get_selected_users();

    foreach ($users as $user) {
        if (!process_manual_revoke($user->id, $USER->id, $issuerrole->roleid, $badgeid)) {
            echo $OUTPUT->error_text(get_string('error:cannotrevokebadge', 'badges'));
        }
    }

    $recipientselector->invalidate_selected_users();
    $existingselector->invalidate_selected_users();
    $recipientselector->set_existing_recipients($existingselector->find_users(''));
}

echo $OUTPUT->header();
echo $tertiarynav;
echo $OUTPUT->heading($strrecipients);

// Print group selector/dropdown menu (find out current groups mode).
groups_print_course_menu($COURSE, $url);

if (count($acceptedroles) > 1) {
    echo $OUTPUT->box($roleselect);
}

echo $output->recipients_selection_form($existingselector, $recipientselector);
echo $OUTPUT->footer();
