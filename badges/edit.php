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

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/edit_form.php');

$badgeid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$badge = new badge($badgeid);
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

if ($action == 'message') {
    require_capability('moodle/badges:configuremessages', $context);
} else {
    require_capability('moodle/badges:configuredetails', $context);
}

if ($badge->type == BADGE_TYPE_COURSE) {
    if (empty($CFG->badges_allowcoursebadges)) {
        print_error('coursebadgesdisabled', 'badges');
    }
    require_login($badge->courseid);
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('incourse');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    navigation_node::override_active_url($navurl, true);
}

$currenturl = new moodle_url('/badges/edit.php', array('id' => $badge->id, 'action' => $action));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($badge->name);
$PAGE->set_title($badge->name);
$PAGE->navbar->add($badge->name);

$output = $PAGE->get_renderer('core', 'badges');
$statusmsg = '';
$errormsg  = '';

$badge->message = clean_text($badge->message, FORMAT_HTML);
$editoroptions = array(
        'subdirs' => 0,
        'maxbytes' => 0,
        'maxfiles' => 0,
        'changeformat' => 0,
        'context' => $context,
        'noclean' => false,
        'trusttext' => false
        );
$badge = file_prepare_standard_editor($badge, 'message', $editoroptions, $context);

$form_class = 'edit_' . $action . '_form';
$form = new $form_class($currenturl, array('badge' => $badge, 'action' => $action, 'editoroptions' => $editoroptions));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/badges/overview.php', array('id' => $badgeid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    if ($action == 'details') {
        $badge->name = $data->name;
        $badge->description = $data->description;
        $badge->usermodified = $USER->id;
        $badge->issuername = $data->issuername;
        $badge->issuerurl = $data->issuerurl;
        $badge->issuercontact = $data->issuercontact;
        $badge->expiredate = ($data->expiry == 1) ? $data->expiredate : null;
        $badge->expireperiod = ($data->expiry == 2) ? $data->expireperiod : null;

        // Need to unset message_editor options to avoid errors on form edit.
        unset($badge->messageformat);
        unset($badge->message_editor);

        if ($badge->save()) {
            badges_process_badge_image($badge, $form->save_temp_file('image'));
            $form->set_data($badge);
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'badges');
        }
    } else if ($action == 'message') {
        // Calculate next message cron if form data is different from original badge data.
        if ($data->notification != $badge->notification) {
            if ($data->notification > BADGE_MESSAGE_ALWAYS) {
                $badge->nextcron = badges_calculate_message_schedule($data->notification);
            } else {
                $badge->nextcron = null;
            }
        }

        $badge->message = clean_text($data->message_editor['text'], FORMAT_HTML);
        $badge->messagesubject = $data->messagesubject;
        $badge->notification = $data->notification;
        $badge->attachment = $data->attachment;

        unset($badge->messageformat);
        unset($badge->message_editor);
        if ($badge->save()) {
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'badges');
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(print_badge_image($badge, $context, 'small') . ' ' . $badge->name);

if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

echo $output->print_badge_status_box($badge);
$output->print_badge_tabs($badgeid, $context, $action);

$form->display();

echo $OUTPUT->footer();