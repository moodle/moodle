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
           $days[$i] = userdate($timestart, "%a %e %b");
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
       $graph->parameter['title'] = "Hits on $course->shortname by $user->firstname $user->lastname";

       $graph->x_data           = $days;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','line' => 'line','point' => 'square');
       $graph->y_label_left     = "Hits";
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       $graph->draw_stack();

       break;

     case "userday.png":

       if ($date) {
           $timestart = usergetmidnight($date);
       } else {
           $timestart = usergetmidnight(time());
       }
       while ($timestart < $timenow) {
           $timefinish = $timestart + 3600;
           if (! $logcount = get_record_sql("SELECT COUNT(*) as count FROM log
                                             WHERE user = '$user->id' AND course = '$course->id'
                                               AND `time` > '$timestart' AND `time` < '$timefinish'")) {
               $logs[$i] = 0;
           }
           $logs[$i] = $logcount->count;
           $hh = (int)userdate($timestart, "%I");
           $hours[$i] = userdate($timestart, "$hh %p");
           $i++;
           $timestart = $timefinish;
       }

       $maxlogs = max($logs);

       $graph = new graph(750, 400);
       $graph->parameter['title'] = "Today's hits on $course->shortname by $user->firstname $user->lastname";

       $graph->x_data           = $hours;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','legend' =>'actual','bar_size' => 0.9);
       $graph->y_label_left     = "Hits";
       $graph->label_size       = "6";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       $graph->draw_stack();

       break;

     default:
       break;
   }

?>
