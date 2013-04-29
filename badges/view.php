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
 * Displays available badges to a user
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
$sortby     = optional_param('sort', 'name', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'DESC', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

if (empty($CFG->badges_allowcoursebadges) && $courseid != 0) {
    print_error('coursebadgesdisabled', 'badges');
}

if (!in_array($sortby, array('name', 'dateissued'))) {
    $sortby = 'name';
}

if ($sorthow != 'ASC' && $sorthow != 'DESC') {
    $sorthow = 'ACS';
}

if ($page < 0) {
    $page = 0;
}

if ($course = $DB->get_record('course', array('id' => $courseid))) {
    $PAGE->set_url('/badges/view.php', array('type' => $type, 'id' => $course->id, 'sort' => $sortby, 'dir' => $sorthow));
} else {
    $PAGE->set_url('/badges/view.php', array('type' => $type, 'sort' => $sortby, 'dir' => $sorthow));
}

if ($type == BADGE_TYPE_SITE) {
    $title = get_string('sitebadges', 'badges');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading($title);
} else {
    require_login($course);
    $title = $course->fullname . ': ' . get_string('coursebadges', 'badges');
    $PAGE->set_context(context_course::instance($course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($title);

    // Fix course navigation.
    $PAGE->navbar->ignore_active();
    $PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', array('id' => $course->id)));
    $PAGE->navbar->add(get_string('coursebadges', 'badges'));
}

$PAGE->set_title($title);
$output = $PAGE->get_renderer('core', 'badges');

echo $output->header();
echo $OUTPUT->heading($title);

$totalcount = count(badges_get_badges($type, $courseid, '', '', '', '', $USER->id));
$records = badges_get_badges($type, $courseid, $sortby, $sorthow, $page, BADGE_PERPAGE, $USER->id);

if ($totalcount) {
    echo $output->heading(get_string('badgestoearn', 'badges', $totalcount), 4);

    if ($course && $course->startdate > time()) {
        echo $OUTPUT->box(get_string('error:notifycoursedate', 'badges'), 'generalbox notifyproblem');
    }

    $badges             = new badge_collection($records);
    $badges->sort       = $sortby;
    $badges->dir        = $sorthow;
    $badges->page       = $page;
    $badges->perpage    = BADGE_PERPAGE;
    $badges->totalcount = $totalcount;

    echo $output->render($badges);
} else {
    echo $output->notification(get_string('nobadges', 'badges'));
}

echo $output->footer();