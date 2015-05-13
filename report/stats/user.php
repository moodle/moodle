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
 * Display user activity reports for a course (totals)
 *
 * @package    report
 * @subpackage stats
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/stats/locallib.php');

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);

$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

$pageheading = $course->fullname;
$userfullname = fullname($user);
if ($courseid == SITEID) {
    $PAGE->set_context($personalcontext);
    $pageheading = $userfullname;
}

if ($USER->id != $user->id and has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}

if (!report_stats_can_access_user_report($user, $course, true)) {
    // this should never happen
    error('Can not access user statistics report');
}

$stractivityreport = get_string('activityreport');

$PAGE->set_pagelayout('report');
$PAGE->set_url('/report/stats/user.php', array('id'=>$user->id, 'course'=>$course->id));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
// Breadcrumb stuff.
$navigationnode = array(
        'name' => get_string('stats'),
        'url' => new moodle_url('/report/stats/user.php', array('id' => $user->id, 'course' => $course->id))
    );
$PAGE->add_report_nodes($user->id, $navigationnode);

$PAGE->set_title("$course->shortname: $stractivityreport");
$PAGE->set_heading($pageheading);
echo $OUTPUT->header();
if ($courseid != SITEID) {
    echo $OUTPUT->context_header(
            array(
            'heading' => $userfullname,
            'user' => $user,
            'usercontext' => $personalcontext
        ), 2);
}

// Trigger a user report viewed event.
$event = \report_stats\event\user_report_viewed::create(array('context' => $coursecontext, 'relateduserid' => $user->id));
$event->trigger();

if (empty($CFG->enablestats)) {
    print_error('statsdisable', 'error');
}

$statsstatus = stats_check_uptodate($course->id);
if ($statsstatus !== NULL) {
    echo $OUTPUT->notification($statsstatus);
}

$earliestday   = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_daily}');
$earliestweek  = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_weekly}');
$earliestmonth = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_monthly}');

if (empty($earliestday)) {
    $earliestday = time();
}
if (empty($earliestweek)) {
    $earliestweek = time();
}
if (empty($earliestmonth)) {
    $earliestmonth = time();
}

$now = stats_get_base_daily();
$lastweekend = stats_get_base_weekly();
$lastmonthend = stats_get_base_monthly();

$timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

if (empty($timeoptions)) {
    print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/user.php?id='.$course->id.'&user='.$user->id.'&mode=outline');
}

// use the earliest.
$timekeys = array_keys($timeoptions);
$time = array_pop($timekeys);

$param = stats_get_parameters($time,STATS_REPORT_USER_VIEW,$course->id,STATS_MODE_DETAILED);
$params = $param->params;

$param->table = 'user_'.$param->table;

$sql = 'SELECT timeend,'.$param->fields.' FROM {stats_'.$param->table.'} WHERE '
.(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
    .' userid = '.$user->id.' AND timeend >= '.$param->timeafter .$param->extras
    .' ORDER BY timeend DESC';
$stats = $DB->get_records_sql($sql, $params); //TODO: improve these params!!

if (empty($stats)) {
    print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/user.php?id='.$course->id.'&user='.$user->id.'&mode=outline');
}

echo '<center><img src="'.$CFG->wwwroot.'/report/stats/graph.php?mode='.STATS_MODE_DETAILED.'&course='.$course->id.'&time='.$time.'&report='.STATS_REPORT_USER_VIEW.'&userid='.$user->id.'" alt="'.get_string('statisticsgraph').'" /></center>';

// What the heck is this about?   -- MD
$stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)),(!empty($param->line3)));

$table = new html_table();
$table->align = array('left','center','center','center');
$param->table = str_replace('user_','',$param->table);
switch ($param->table) {
    case 'daily'  : $period = get_string('day'); break;
    case 'weekly' : $period = get_string('week'); break;
    case 'monthly': $period = get_string('month', 'form'); break;
    default : $period = '';
}
$table->head = array(get_string('periodending','moodle',$period),$param->line1,$param->line2,$param->line3);
foreach ($stats as $stat) {
    if (!empty($stat->zerofixed)) {  // Don't know why this is necessary, see stats_fix_zeros above - MD
        continue;
    }
    $a = array(userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone),$stat->line1);
    $a[] = $stat->line2;
    $a[] = $stat->line3;
    $table->data[] = $a;
}
echo html_writer::table($table);


echo $OUTPUT->footer();
