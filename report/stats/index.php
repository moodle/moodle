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
 * stats report
 *
 * @package    report
 * @subpackage stats
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;

require_once('../../config.php');
require_once($CFG->dirroot.'/report/stats/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

$courseid = optional_param('course', SITEID, PARAM_INT);
$report   = optional_param('report', 0, PARAM_INT);
$time     = optional_param('time', 0, PARAM_INT);
$mode     = optional_param('mode', STATS_MODE_GENERAL, PARAM_INT);
$userid   = optional_param('userid', 0, PARAM_INT);
$roleid   = 0;

if ($report > 50) {
    $roleid = substr($report,1);
    $report = 5;
}

if ($report == STATS_REPORT_USER_LOGINS) {
    $courseid = SITEID; //override
}

if ($mode == STATS_MODE_RANKED) {
    redirect($CFG->wwwroot.'/report/stats/index.php?time='.$time);
}

if (!$course = $DB->get_record("course", array("id"=>$courseid))) {
    throw new \moodle_exception("invalidcourseid");
}

if (!empty($userid)) {
    $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
} else {
    $user = null;
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/stats:view', $context);

$PAGE->set_url(new moodle_url('/report/stats/index.php', array('course' => $course->id,
                                                               'report' => $report,
                                                               'time'   => $time,
                                                               'mode'   => $mode,
                                                               'userid' => $userid)));
navigation_node::override_active_url(new moodle_url('/report/stats/index.php', array('course' => $course->id)));

// Trigger a content view event.
$event = \report_stats\event\report_viewed::create(array('context' => $context, 'relateduserid' => $userid,
        'other' => array('report' => $report, 'time' => $time, 'mode' => $mode)));
$event->trigger();
stats_check_uptodate($course->id);

if ($course->id == SITEID) {
    admin_externalpage_setup('reportstats', '', null, '', array('pagelayout'=>'report'));
    echo $OUTPUT->header();
} else {
    $strreports = get_string("reports");
    $strstats = get_string('stats');

    $PAGE->set_title("$course->shortname: $strstats");
    $PAGE->set_heading($course->fullname);
    $PAGE->set_pagelayout('report');

    echo $OUTPUT->header();

    // Print the selected dropdown.
    report_helper::print_report_selector(
        get_string('pluginname', 'report_stats'),
        report_stats_mode_menu($course, $mode, $time, $PAGE->url->out_omit_querystring())
    );

}

report_stats_report($course, $report, $mode, $user, $roleid, $time);

if (empty($CFG->enablestats)) {
    if (has_capability('moodle/site:config', context_system::instance())) {
        redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=stats", get_string('mustenablestats', 'admin'), 3);
    } else {
        throw new \moodle_exception('statsdisable');
    }
}

echo $OUTPUT->footer();


