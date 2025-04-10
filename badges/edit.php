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
 * Editing badge details, criteria, messages.
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

// Used only for creating new badge.
$courseid = optional_param('courseid', 0, PARAM_INT);
if ($courseid === 0 ) {
    $courseid = null;
}

// Used for editing existing badge.
$badgeid = optional_param('id', null, PARAM_INT);
$action = optional_param('action', 'badge', PARAM_TEXT);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

if (!empty($badgeid)) {
    // Existing badge.
    $badge = new badge($badgeid);

    if ($badge->courseid) {
        $course = get_course($badge->courseid);
    }
    $params = ['id' => $badgeid, 'action' => $action];
    $badgename = $badge->name;

    // Check capabilities.
    $context = $badge->get_context();
    if ($action == 'message') {
        $title = [get_string('configuremessage', 'badges'), $badge->name];
        require_capability('moodle/badges:configuremessages', $context);
    } else {
        $title = [get_string('badgedetails', 'badges'), $badge->name];
        require_capability('moodle/badges:configuredetails', $context);
    }

    $cancelurl = new moodle_url('/badges/overview.php', ['id' => $badgeid]);
} else {
    // New badge.
    if ($courseid) {
        $course = get_course($courseid);
        $context = context_course::instance($course->id);
    } else {
        $context = context_system::instance();
    }

    $badge = new stdClass();
    $badge->id = null;
    $badge->type = $courseid ? BADGE_TYPE_COURSE : BADGE_TYPE_SITE;
    $badge->courseid = $courseid;

    $params = ['courseid' => $courseid];
    $badgename = get_string('create', 'badges');
    $title = [$badgename];

    // Check capabilities.
    require_capability('moodle/badges:createbadge', $context);

    $cancelurl = new moodle_url('/badges/index.php', ['type' => $badge->type, 'id' => $courseid]);
}

// Check if course badges are enabled.
if (empty($CFG->badges_allowcoursebadges) && ($badge->type == BADGE_TYPE_COURSE)) {
    throw new \moodle_exception('coursebadgesdisabled', 'badges');
}

$navurl = new moodle_url('/badges/index.php', ['type' => $badge->type]);
if ($badge->type == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $heading = format_string($course->fullname, true, ['context' => $context]);
    $title[] = $heading;
    $navurl = new moodle_url('/badges/index.php', ['type' => $badge->type, 'id' => $badge->courseid]);
    $PAGE->set_pagelayout('incourse');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    $heading = get_string('administrationsite');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/badges/edit.php', $params);

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($heading);
$PAGE->set_title(implode(\moodle_page::TITLE_SEPARATOR, $title));
$PAGE->add_body_class('limitedwidth');
$PAGE->navbar->add($badgename);

/** @var \core_badges_renderer $output*/
$output = $PAGE->get_renderer('core', 'badges');
$statusmsg = '';
$errormsg  = '';

$editoroptions = [];
if ($badge->id && $action == 'message') {
    $badge->message = clean_text($badge->message, FORMAT_HTML);
    $editoroptions = [
        'subdirs' => 0,
        'maxbytes' => 0,
        'maxfiles' => 0,
        'changeformat' => 0,
        'context' => $context,
        'noclean' => false,
        'trusttext' => false,
    ];
    $badge = file_prepare_standard_editor($badge, 'message', $editoroptions, $context);
}

$formclass = '\core_badges\form' . '\\' . ($action == 'new' ? 'badge' : $action);
$params = [
    'action' => $action,
];
if ($badge->id) {
    $params['badge'] = $badge;
    $params['editoroptions'] = $editoroptions;
} else {
    $params['courseid'] = $courseid;
}

$form = new $formclass($currenturl, $params);
if ($form->is_cancelled()) {
    redirect($cancelurl);
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    switch ($action) {
        case 'new':
            // Create new badge.
            $badge = badge::create_badge($data, $courseid);
            $badgeid = $badge->id;
            badges_process_badge_image($badge, $form->save_temp_file('image'));

            // If a user can configure badge criteria, they will be redirected to the criteria page.
            if (has_capability('moodle/badges:configurecriteria', $context)) {
                redirect(new moodle_url('/badges/criteria.php', ['id' => $badgeid]));
            }
            redirect(new moodle_url('/badges/overview.php', ['id' => $badgeid]));
            break;

        case 'badge':
            // Edit existing badge.
            if ($badge->update($data)) {
                badges_process_badge_image($badge, $form->save_temp_file('image'));
                $form->set_data($badge);
                $statusmsg = get_string('changessaved');
            } else {
                $errormsg = get_string('error:save', 'badges');
            }
            break;

        case 'message':
            // Update badge message.
            if ($badge->update_message($data)) {
                $statusmsg = get_string('changessaved');
            } else {
                $errormsg = get_string('error:save', 'badges');
            }
            break;
    }
}

echo $output->header();

if ($badge->id) {
    $actionbar = new \core_badges\output\manage_badge_action_bar($badge, $PAGE);
    echo $output->render_tertiary_navigation($actionbar);
    echo $output->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);

    if ($errormsg !== '') {
        echo $output->notification($errormsg);

    } else if ($statusmsg !== '') {
        echo $output->notification($statusmsg, 'notifysuccess');
    }
    echo $output->print_badge_status_box($badge);
} else {
    echo $output->heading($badgename);
}

$form->display();

echo $output->footer();
