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
 * Produces a graph of log accesses for a user
 *
 * Generates an image representing the log data in a graphical manner for a user.
 *
 * @package    report_log
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require("../../config.php");
require_once("$CFG->libdir/graphlib.php");
require_once($CFG->dirroot.'/report/log/locallib.php');

$id   = required_param('id', PARAM_INT);       // Course ID
$type = required_param('type', PARAM_FILE);    // Graph Type
$user = required_param('user', PARAM_INT);     // Student ID
$date = optional_param('date', 0, PARAM_INT);  // A time of a day (in GMT)

$url = new moodle_url('/report/log/graph.php', array('id'=>$id,'type'=>$type,'user'=>$user,'date'=>$date));
$PAGE->set_url($url);

if ($type !== "usercourse.png" and $type !== "userday.png") {
    $type = 'userday.png';
}

$course = $DB->get_record("course", array("id"=>$id), '*', MUST_EXIST);
$user = $DB->get_record("user", array("id"=>$user, 'deleted'=>0), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

if ($USER->id != $user->id and has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}

list($all, $today) = report_log_can_access_user_report($user, $course);

if ($type === "userday.png") {
    if (!$today) {
        require_capability('report/log:viewtoday', $coursecontext);
    }
} else {
    if (!$all) {
        require_capability('report/log:view', $coursecontext);
    }
}

add_to_log($course->id, 'course', 'report log', "report/log/graph.php?user=$user->id&id=$course->id&type=$type&date=$date", $course->id);

$logs = array();

$timenow = time();

if ($type === "usercourse.png") {

   $site = get_site();

   if ($course->id == $site->id) {
       $courseselect = 0;
   } else {
       $courseselect = $course->id;
   }

   $maxseconds = REPORT_LOG_MAX_DISPLAY * 3600 * 24;  // seconds
   //$maxseconds = 60 * 3600 * 24;  // seconds
   if ($timenow - $course->startdate > $maxseconds) {
       $course->startdate = $timenow - $maxseconds;
   }

   if (!empty($CFG->loglifetime)) {
       $maxseconds = $CFG->loglifetime * 3600 * 24;  // seconds
       if ($timenow - $course->startdate > $maxseconds) {
           $course->startdate = $timenow - $maxseconds;
       }
   }

   $timestart = $coursestart = usergetmidnight($course->startdate);

   if ((($timenow - $timestart)/86400.0) > 40) {
       $reducedays = 7;
   } else {
       $reducedays = 0;
   }

   $days = array();
   $i = 0;
   while ($timestart < $timenow) {
       $timefinish = $timestart + 86400;
       if ($reducedays) {
           if ($i % $reducedays) {
               $days[$i] = "";
           } else {
               $days[$i] = userdate($timestart, "%a %d %b");
           }
       } else {
           $days[$i] = userdate($timestart, "%a %d %b");
       }
       $logs[$i] = 0;
       $i++;
       $timestart = $timefinish;
   }

   if ($rawlogs = get_logs_usercourse($user->id, $courseselect, $coursestart)) {
       foreach ($rawlogs as $rawlog) {
           $logs[$rawlog->day] = $rawlog->num;
       }
   }

   $graph = new graph(750, 400);

   $a = new stdClass();
   $a->coursename = format_string($course->shortname, true, array('context' => $coursecontext));
   $a->username = fullname($user, true);
   $graph->parameter['title'] = get_string("hitsoncourse", "", $a);

   $graph->x_data           = $days;

   $graph->y_data['logs']   = $logs;
   $graph->y_order = array('logs');

   if (!empty($CFG->preferlinegraphs)) {
       $graph->y_format['logs'] = array('colour' => 'blue','line' => 'line');
   } else {
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','bar_size' => 0.6);
       $graph->parameter['bar_spacing'] = 0;
   }


   $graph->parameter['y_label_left']     = get_string("hits");
   $graph->parameter['label_size']       = "12";
   $graph->parameter['x_axis_angle']     = 90;
   $graph->parameter['x_label_angle']    = 0;
   $graph->parameter['tick_length'] = 0;


   $graph->parameter['shadow']          = 'none';

   error_reporting(5); // ignore most warnings such as font problems etc
   $graph->draw_stack();

} else {

   $site = get_site();

   if ($course->id == $site->id) {
       $courseselect = 0;
   } else {
       $courseselect = $course->id;
   }

   if ($date) {
       $daystart = usergetmidnight($date);
   } else {
       $daystart = usergetmidnight(time());
   }
   $dayfinish = $daystart + 86400;

   $hours = array();
   for ($i=0; $i<=23; $i++) {
       $logs[$i] = 0;
       $hour = $daystart + $i * 3600;
       $hours[$i] = $i;
   }

   if ($rawlogs = get_logs_userday($user->id, $courseselect, $daystart)) {
       foreach ($rawlogs as $rawlog) {
           $logs[$rawlog->hour] = $rawlog->num;
       }
   }

   $graph = new graph(750, 400);

   $a = new stdClass();
   $a->coursename = format_string($course->shortname, true, array('context' => $coursecontext));
   $a->username = fullname($user, true);
   $graph->parameter['title'] = get_string("hitsoncoursetoday", "", $a);

   $graph->x_data           = $hours;

   $graph->y_data['logs']   = $logs;
   $graph->y_order = array('logs');

   if (!empty($CFG->preferlinegraphs)) {
       $graph->y_format['logs'] = array('colour' => 'blue','line' => 'line');
   } else {
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','bar_size' => 0.9);
   }

   $graph->parameter['y_label_left']     = get_string("hits");
   $graph->parameter['label_size']       = "12";
   $graph->parameter['x_axis_angle']     = 0;
   $graph->parameter['x_label_angle']    = 0;

   $graph->parameter['shadow']          = 'none';

   error_reporting(5); // ignore most warnings such as font problems etc
   $graph->draw_stack();
}

