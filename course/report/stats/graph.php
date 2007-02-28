<?php  //$Id$

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->dirroot.'/lib/graphlib.php');

    $courseid = required_param('course', PARAM_INT);
    $report   = required_param('report', PARAM_INT);
    $time     = required_param('time', PARAM_INT);
    $mode     = required_param('mode', PARAM_INT);
    $userid   = optional_param('userid', 0, PARAM_INT);
    $roleid   = optional_param('roleid',0,PARAM_INT);

    if (!$course = get_record("course","id",$courseid)) {
        error("That's an invalid course id");
    }

    if (!empty($userid)) {
        if (!$user = get_record('user','id',$userid)) {
            error("That's an invalid user id");
        }
    }

    require_login();
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    
    if (!has_capability('moodle/site:viewreports', $context)) {
        error('You need do not have the required permission to view reports for this course');
    }

    stats_check_uptodate($course->id);

    $param = stats_get_parameters($time,$report,$course->id,$mode);

    if (!empty($userid)) {
        $param->table = 'user_'.$param->table;
    }

    $sql = 'SELECT '.((empty($param->fieldscomplete)) ? 'id,roleid,timeend,' : '').$param->fields
    .' FROM '.$CFG->prefix.'stats_'.$param->table.' WHERE '
    .(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
     .((!empty($userid)) ? ' userid = '.$userid.' AND ' : '')
     .((!empty($roleid)) ? ' roleid = '.$roleid.' AND ' : '')
     . ((!empty($param->stattype)) ? ' stattype = \''.$param->stattype.'\' AND ' : '')
     .' timeend >= '.$param->timeafter
    .' '.$param->extras
    .' ORDER BY timeend DESC';

    $stats = get_records_sql($sql);

    $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)),(!empty($param->line3)));

    $stats = array_reverse($stats);

    $graph = new graph(750,400);

    $graph->parameter['legend'] = 'outside-right';
    $graph->parameter['legend_size'] = 10;
    $graph->parameter['x_axis_angle'] = 90;
    $graph->parameter['title'] = false; // moodle will do a nicer job.
    $graph->y_tick_labels = null;

    $c = array_keys($graph->colour);

    if (empty($param->crosstab)) {
        foreach ($stats as $stat) {
            $graph->x_data[] = userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone);
            $graph->y_data['line1'][] = $stat->line1;
            if (isset($stat->line2)) {
                $graph->y_data['line2'][] = $stat->line2;
            }
            if (isset($stat->line3)) {
                $graph->y_data['line3'][] = $stat->line3;
            }
        }
        $graph->y_order = array('line1');
        $graph->y_format['line1'] = array('colour' => $c[1],'line' => 'line','legend' => $param->line1);
        if (!empty($param->line2)) {
            $graph->y_order[] = 'line2';
            $graph->y_format['line2'] = array('colour' => $c[2],'line' => 'line','legend' => $param->line2); 
        }
        if (!empty($param->line3)) {
            $graph->y_order[] = 'line3';
            $graph->y_format['line3'] = array('colour' => $c[3],'line' => 'line','legend' => $param->line3); 
        }
        $graph->y_tick_labels = false;
    } else {
        $data = array();
        $times = array();
        $roles = array();
        $missedlines = array();
        foreach ($stats as $stat) {
            $data[$stat->roleid][$stat->timeend] = $stat->line1;
            if (!empty($stat->zerofixed)) {
                $missedlines[] = $stat->timeend;
            }
            if ($stat->roleid != 0) {
                if (!array_key_exists($stat->roleid,$roles)) {
                    $roles[$stat->roleid] = get_field('role','name','id',$stat->roleid);
                }
            }
            if (!array_key_exists($stat->timeend,$times)) {
                $times[$stat->timeend] = userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone);
            }
        }
        $nonzeroroleid = 0;
        foreach (array_keys($data) as $roleid) {
            if ($roleid == 0) {
                continue;
            }
            $graph->y_order[] = $roleid;
            $graph->y_format[$roleid] = array('colour' => $c[$roleid], 'line' => 'line','legend' => $roles[$roleid]);
            $nonzeroroleid = $roleid;
        }
        foreach (array_keys($data[$nonzeroroleid]) as $time) {
            $graph->x_data[] = $times[$time];
        }
        foreach ($data as $roleid => $t) {
            if ($roleid == 0) {
                continue;
            }
            foreach ($t as $time => $data) {
                $graph->y_data[$roleid][] = $data;
            }
        }
    }

    $graph->draw_stack();



?>
