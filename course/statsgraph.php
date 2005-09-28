<?php

    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->dirroot.'/lib/graphlib.php');

    $courseid = required_param('course',PARAM_INT);
    $report = required_param('report',PARAM_INT);
    $time = required_param('time',PARAM_INT);
    $userid = optional_param('userid',0,PARAM_INT);

    if (!$course = get_record("course","id",$courseid)) {
        error("That's an invalid course id");
    }

    if (!empty($userid)) {
        if (!$user = get_record('user','id',$userid)) {
            error("That's an invalid user id");
        }
    }

    require_login();
    if (!isteacher($course->id)) {
        error("You need to be a teacher to use this page");
    }

    stats_check_uptodate($course->id);

    $param = stats_get_parameters($time,$report);

    if (!empty($userid)) {
        $param->table = 'user_'.$param->table;
    }

    $sql = 'SELECT timeend,'.$param->fields.',id FROM '.$CFG->prefix.'stats_'.$param->table.' WHERE courseid = '.$course->id.((!empty($userid)) ? ' AND userid = '.$userid : '')
     . ((!empty($param->stattype)) ? ' AND stattype = \''.$param->stattype.'\'' : '')
     .' AND timeend > '.$param->timeafter
     .' ORDER BY timeend DESC';
    $stats = get_records_sql($sql);

    $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)),(!empty($param->line3)));

     $stats = array_reverse($stats);

    $graph = new graph(750,400);

    $graph->parameter['legend'] = 'outside-right';
    $graph->parameter['legend_size'] = 10;
    $graph->parameter['x_axis_angle'] = 90;
    $graph->parameter['title'] = false; // moodle will do a nicer job.

    foreach ($stats as $stat) {
        $graph->x_data[] = userdate($stat->timeend, "%a %d %b %y");
        $graph->y_data['line1'][] = $stat->line1;
        if (isset($stat->line2)) {
            $graph->y_data['line2'][] = $stat->line2;
        }
        if (isset($stat->line3)) {
            $graph->y_data['line3'][] = $stat->line3;
        }
    }
    $graph->y_order = array('line1','line2','line3');
    $graph->y_format['line1'] = array('colour' => 'blue','line' => 'line','legend' => $param->line1);
    if (!empty($param->line2)) {
        $graph->y_format['line2'] = array('colour' => 'red','line' => 'line','legend' => $param->line2); 
    }
    if (!empty($param->line3)) {
        $graph->y_format['line3'] = array('colour' => 'black','line' => 'line','legend' => $param->line3); 
    }

    $graph->draw_stack();



?>
