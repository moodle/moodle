<?PHP // $Id$
      // Produces a graph of log accesses

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/graphlib.php");

    require_variable($id);    // Course ID
    require_variable($type);  // Graph Type
    optional_variable($user);  // Student ID
    optional_variable($date);  // A time of a day (in GMT)

    if (! $course = get_record("course", "id", $id)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! (isteacher($course->id) or ($course->showreports and $USER->id == $user))) {
        error("Sorry, you aren't allowed to see this.");
    }

    if ($user) {
        if (! $user = get_record("user", "id", $user)) {
            error("Can not find that user");
        }
    }

    $logs = array();

    $timenow = time();

    switch ($type) {
     case "usercourse.png":

       $site = get_site();
        
       if ($course->id == $site->id) {
           $courseselect = 0;
       } else {
           $courseselect = $course->id;
       }

       $maxseconds = COURSE_MAX_LOG_DISPLAY * 3600 * 24;  // seconds
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

       $a->coursename = $course->shortname;
       $a->username = fullname($user, true);
       $graph->parameter['title'] = get_string("hitsoncourse", "", $a);

       $graph->x_data           = $days;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','line' => 'line');
       $graph->y_label_left     = get_string("hits");
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       error_reporting(5); // ignore most warnings such as font problems etc
       $graph->draw_stack();

       break;

     case "userday.png":

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

       for ($i=0; $i<=23; $i++) {
           $logs[$i] = 0;
           $hour = $daystart + $i * 3600;
           $hh        = (int)userdate($hour, "%I");
           $hours[$i] = userdate($hour, "$hh %p");
       }

       if ($rawlogs = get_logs_userday($user->id, $courseselect, $daystart)) {
           foreach ($rawlogs as $rawlog) {
               $logs[$rawlog->hour] = $rawlog->num;
           }
       }

       $graph = new graph(750, 400);

       $a->coursename = $course->shortname;
       $a->username = fullname($user, true);
       $graph->parameter['title'] = get_string("hitsoncoursetoday", "", $a);

       $graph->x_data           = $hours;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','legend' =>'actual','bar_size' => 0.9);
       $graph->y_label_left     = get_string("hits");
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       error_reporting(5); // ignore most warnings such as font problems etc
       $graph->draw_stack();

       break;

     default:
       break;
   }

?>
