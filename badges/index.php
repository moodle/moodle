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
 * Page for badges management
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

$type       = required_param('type', PARAM_INT);
$courseid   = optional_param('id', 0, PARAM_INT);
$page       = optional_param('page', 0, PARAM_INT);
$activate   = optional_param('activate', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$sortby     = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'ASC', PARAM_ALPHA);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$delete     = optional_param('delete', 0, PARAM_INT);
$msg        = optional_param('msg', '', PARAM_TEXT);

if (!in_array($sortby, array('name', 'status'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

if (empty($CFG->badges_allowcoursebadges) && ($type == BADGE_TYPE_COURSE)) {
    print_error('coursebadgesdisabled', 'badges');
}

$err = '';
$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'page' => $page);

if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $urlparams['type'] = $type;
    $urlparams['id'] = $course->id;
} else {
    $urlparams['type'] = $type;
}

$hdr = get_string('managebadges', 'badges');
$returnurl = new moodle_url('/badges/index.php', $urlparams);
$PAGE->set_url($returnurl);

if ($type == BADGE_TYPE_SITE) {
    $title = get_string('sitebadges', 'badges');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title . ': ' . $hdr);
    navigation_node::override_active_url(new moodle_url('/badges/index.php', array('type' => BADGE_TYPE_SITE)));
} else {
    require_login($course);
    $title = get_string('coursebadges', 'badges');
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($course->fullname . ': ' . $hdr);
    navigation_node::override_active_url(
        new moodle_url('/badges/index.php', array('type' => BADGE_TYPE_COURSE, 'id' => $course->id))
    );
}

if (!has_any_capability(array(
        'moodle/badges:viewawarded',
        'moodle/badges:createbadge',
        'moodle/badges:awardbadge',
        'moodle/badges:configuremessages',
        'moodle/badges:configuredetails',
        'moodle/badges:deletebadge'), $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($hdr);
$PAGE->requires->js('/badges/backpack.js');
$PAGE->requires->js_init_call('check_site_access', null, false);
$output = $PAGE->get_renderer('core', 'badges');

if ($delete && has_capability('moodle/badges:deletebadge', $PAGE->context)) {
    $badge = new badge($delete);
    if (!$confirm) {
        echo $output->header();
        echo $output->confirm(
                    get_string('delconfirm', 'badges', $badge->name),
                    new moodle_url($PAGE->url, array('delete' => $badge->id, 'confirm' => 1)),
                    $returnurl
                );
        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $badge->delete();
        redirect($returnurl);
    }
}

if ($activate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    $badge = new badge($activate);

    if (!$badge->has_criteria()) {
        $err = get_string('error:cannotact', 'badges') . get_string('nocriteria', 'badges');
    } else {
        if ($badge->is_locked()) {
            $badge->set_status(BADGE_STATUS_ACTIVE_LOCKED);
            $msg = get_string('activatesuccess', 'badges');
        } else {
            require_sesskey();
            $badge->set_status(BADGE_STATUS_ACTIVE);
            $msg = get_string('activatesuccess', 'badges');
        }
        $returnurl->param('msg', $msg);
        redirect($returnurl);
    }
} else if ($deactivate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    $badge = new badge($deactivate);
    if ($badge->is_locked()) {
        $badge->set_status(BADGE_STATUS_INACTIVE_LOCKED);
        $msg = get_string('deactivatesuccess', 'badges');
    } else {
        require_sesskey();
        $badge->set_status(BADGE_STATUS_INACTIVE);
        $msg = get_string('deactivatesuccess', 'badges');
    }
    $returnurl->param('msg', $msg);
    redirect($returnurl);
}

echo $OUTPUT->header();
if ($type == BADGE_TYPE_SITE) {
    echo $OUTPUT->heading_with_help($PAGE->heading, 'sitebadges', 'badges');
} else {
    echo $OUTPUT->heading($PAGE->heading);
}
echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');

$totalcount = count(badges_get_badges($type, $courseid, '', '' , '', ''));
$records = badges_get_badges($type, $courseid, $sortby, $sorthow, $page, BADGE_PERPAGE);

if ($totalcount) {
    echo $output->heading(get_string('badgestoearn', 'badges', $totalcount), 4);

    if ($course && $course->startdate > time()) {
        echo $OUTPUT->box(get_string('error:notifycoursedate', 'badges'), 'generalbox notifyproblem');
    }

    if ($err !== '') {
        echo $OUTPUT->notification($err, 'notifyproblem');
    }

    if ($msg !== '') {
        echo $OUTPUT->notification($msg, 'notifysuccess');
    }

    $badges             = new badge_management($records);
    $badges->sort       = $sortby;
    $badges->dir        = $sorthow;
    $badges->page       = $page;
    $badges->perpage    = BADGE_PERPAGE;
    $badges->totalcount = $totalcount;

    echo $output->render($badges);
} else {
    echo $output->notification(get_string('nobadges', 'badges'));

    if (has_capability('moodle/badges:createbadge', $PAGE->context)) {
        echo $OUTPUT->single_button(new moodle_url('newbadge.php', array('type' => $type, 'id' => $courseid)),
            get_string('newbadge', 'badges'));
    }
}

echo $OUTPUT->footer();
