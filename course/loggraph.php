<?PHP // $Id$
      // Produces a graph of log accesses

    require("../config.php");
    require("lib.php");
    require("$CFG->libdir/graphlib.php");

    require_variable($id);    // Course ID
    require_variable($type);  // Graph Type
    optional_variable($user);  // Student ID
    optional_variable($date);  // A time of a day (in GMT)

    if (! $course = get_record("course", "id", $id)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        if (! ($type == "usercourse.png" and $user == $USER->id) ) {
            error("Sorry, you aren't allowed to see this.");
        }
    }

    if ($user) {
        if (! $user = get_record("user", "id", $user)) {
            error("Can not find that user");
        }
    }


    $timenow = time();

    switch ($type) {
     case "usercourse.png":

       $COURSE_MAX_LOG_DISPLAY = $COURSE_MAX_LOG_DISPLAY * 3600 * 24;  // seconds
       if ($timenow - $course->startdate > $COURSE_MAX_LOG_DISPLAY) {
           $course->startdate = $timenow - $COURSE_MAX_LOG_DISPLAY;
       }
       $timestart = $coursestart = usergetmidnight($course->startdate);

       $i = 0;
       while ($timestart < $timenow) {
           $timefinish = $timestart + 86400;
           $days[$i] = userdate($timestart, "%a %d %b");
           $logs[$i] = 0;
           $i++;
           $timestart = $timefinish;
       }

       if ($rawlogs = get_records_sql("SELECT floor((`time` - $coursestart)/86400) as day, 
                                              count(*) as num FROM log 
                                       WHERE user = '$user->id' 
                                         AND course = '$course->id'
                                         AND `time` > '$coursestart'
                                       GROUP BY day ")) {
           foreach ($rawlogs as $rawlog) {
               $logs[$rawlog->day] = $rawlog->num;
           }
       }

       $maxlogs = max($logs);


       $graph = new graph(750, 400);

       $a->coursename = $course->shortname;
       $a->username = "$user->firstname $user->lastname";
       $graph->parameter['title'] = get_string("hitsoncourse", "", $a);

       $graph->x_data           = $days;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','line' => 'line');
       $graph->y_label_left     = get_string("hits");
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       error_reporting(5);
       $graph->draw_stack();

       break;

     case "userday.png":

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

       if ($rawlogs = get_records_sql("SELECT floor((`time` - $daystart)/3600) as hour, 
                                              count(*) as num FROM log 
                                       WHERE user = '$user->id' 
                                         AND course = '$course->id'
                                         AND `time` > '$daystart'
                                       GROUP BY hour ")) {
           foreach ($rawlogs as $rawlog) {
               $logs[$rawlog->hour] = $rawlog->num;
           }
       }

       $maxlogs = max($logs);

       $graph = new graph(750, 400);

       $a->coursename = $course->shortname;
       $a->username = "$user->firstname $user->lastname";
       $graph->parameter['title'] = get_string("hitsoncoursetoday", "", $a);

       $graph->x_data           = $hours;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','legend' =>'actual','bar_size' => 0.9);
       $graph->y_label_left     = get_string("hits");
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       error_reporting(5);
       $graph->draw_stack();

       break;

     default:
       break;
   }

?>
