<?PHP // $Id$

    require("../config.php");
    require("lib.php");
    require("$CFG->libdir/graphlib.php");

    require_variable($id);    // Course ID
    require_variable($type);  // Graph Type
    optional_variable($user);  // Student ID

    if (! $course = get_record("course", "id", $id)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        if (! ($type == "student.png" and $user == $USER->id) ) {
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

     case "user.png":

       $timestart = $course->startdate;
       $i = 0;
       while ($timestart < $timenow) {
           $timefinish = $timestart + (3600 * 24);
           if (! $logcount = get_record_sql("SELECT COUNT(*) as count FROM log
                                             WHERE user = '$user->id' AND course = '$course->id'
                                               AND `time` > '$timestart' AND `time` < '$timefinish'")) {
               $logs[$i] = 0;
           }
           $logs[$i] = $logcount->count;
           $days[$i] = date("j M", $timestart);
           $i++;
           $timestart = $timefinish;
       }

       $maxlogs = max($logs);


       $graph = new graph(600, 300);
       $graph->parameter['title'] = "Rough usage of $course->shortname by $user->firstname $user->lastname";

       $graph->x_data           = $days;

       $graph->y_data['logs']   = $logs;
       $graph->y_format['logs'] = array('colour' => 'blue','bar' => 'fill','legend' =>'actual','bar_size' => 0.4);
       $graph->y_label_left     = "Hits";

       $graph->y_order = array('logs');

       
       $graph->parameter['shadow']          = 'none';

       $graph->draw_stack();

       break;

     default:
       break;
   }

?>
