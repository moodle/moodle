<?php

    $courses = get_courses('all','c.shortname','c.id,c.shortname,c.fullname');
    $courseoptions = array();

    foreach ($courses as $c) {
        $context = get_context_instance(CONTEXT_COURSE, $c->id);

        if (has_capability('moodle/site:viewreports', $context)) {
            $courseoptions[$c->id] = $c->shortname;
        }
    }
    

    echo '<form action="index.php" method="post">'."\n"
        .'<input type="hidden" name="mode" value="'.$mode.'" />'."\n";

    $reportoptions = stats_get_report_options($course->id, $mode);
    $timeoptions = report_stats_timeoptions($mode);
    if (empty($timeoptions)) {
        error(get_string('nostatstodisplay'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    $table->width = '*';

    if ($mode == STATS_MODE_DETAILED) {
        if (!empty($time)) {
            $param = stats_get_parameters($time,null,$course->id,$mode); // we only care about the table and the time string.
            $sql =  'SELECT DISTINCT s.userid,s.roleid,u.firstname,u.lastname,u.idnumber  FROM '.$CFG->prefix.'stats_user_'.$param->table.' s JOIN '.$CFG->prefix.'user u ON u.id = s.userid '
                .'WHERE courseid = '.$course->id.' AND timeend >= '.$param->timeafter  . ((!empty($param->stattype)) ? ' AND stattype = \''.$param->stattype.'\'' : '');
            if (!has_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                $sql .= ' AND (s.roleid = 1 OR s.userid = '.$USER->id .")";
            }
            $sql .= " ORDER BY s.roleid ";
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            
            $sql = 'SELECT ra.userid,u.firstname,u.lastname,u.idnumber,1 AS roleid 
                    FROM '.$CFG->prefix.'role_assignments ra, 
                         '.$CFG->prefix.'user u
                    WHERE u.id = ra.userid 
                          AND ra.contextid '.get_related_contexts_string($context);
        }
        if (!$us = get_records_sql($sql)) {
            error('Cannot enter detailed view: No users found for this course.');
        }
        $admins = get_admins();
        foreach ($us as $u) {
            $role = $course->student;
            if ($u->roleid == 2) {
                $role = $course->teacher;
            }
            if (array_key_exists($u->userid,$admins)) {
                $role = get_string('admin');
            }
            $users[$u->userid] = $role.' - '.fullname($u,true);
        }
        if (empty($time)) {
            if (has_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                $sql = 'SELECT t.userid,u.firstname,u.lastname,u.idnumber,1 AS roleid FROM '.$CFG->prefix.'user_teachers t JOIN '.$CFG->prefix.'user u ON u.id = t.userid WHERE course = '.$course->id;
                $moreusers = get_records_sql($sql);
                foreach ($moreusers as $u) {
                    $users[$u->userid] = $course->teacher .' - '.fullname($u,true);
                }
            } else {
                $users[$USER->id] = $course->teacher.' - '.fullname($USER,true);
            }
        }

        // make sure we sort so teachers are at the top.
        if (strcmp($course->student,$course->teacher) < 0) {
            arsort($users);
        } else {
            asort($users);
        }
        
        $table->align = array('left','left','left','left','left','left','left','left');
        $table->data[] = array(get_string('course'),choose_from_menu($courseoptions,'course',$course->id,'','','',true),
                               get_string('users'),choose_from_menu($users,'userid',$userid,'','','',true),
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',($report == 5) ? $report.$roleid : $report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else {
        $table->align = array('left','left','left','left','left','left','left');
        $table->data[] = array(get_string('course'),choose_from_menu($courseoptions,'course',$course->id,'','','',true),
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',($report == 5) ? $report.$roleid : $report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    }


    print_table($table);
    echo '</form>';

    if (!empty($report) && !empty($time)) {
        if ($report == STATS_REPORT_LOGINS && $course->id != SITEID) {
            error("This type of report is only available for the site course");
        }
        $param = stats_get_parameters($time,$report,$course->id,$mode);

        if ($mode == STATS_MODE_DETAILED) {
            $param->table = 'user_'.$param->table;
        }
        $sql = 'SELECT '.((empty($param->fieldscomplete)) ? 'id,roleid,timeend,' : '').$param->fields
            .' FROM '.$CFG->prefix.'stats_'.$param->table.'_tmp WHERE '
            .(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
            .((!empty($userid)) ? ' userid = '.$userid.' AND ' : '')
            .((!empty($roleid)) ? ' roleid = '.$roleid.' AND ' : '')
            . ((!empty($param->stattype)) ? ' stattype = \''.$param->stattype.'\' AND ' : '')
            .' timeend >= '.$param->timeafter
            .' '.$param->extras
            .' ORDER BY timeend DESC';

        $stats = get_records_sql($sql);

        if (empty($stats)) {
            error(get_string('statsnodata'.((!empty($user)) ? 'user' : '')),$CFG->wwwroot.'/stats/index.php?course='.$course->id.'&mode='.$mode.'&time='.$time);
        }
            
        $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)));

        print_heading($course->shortname.' - '.get_string('statsreport'.$report)
                      .((!empty($user)) ? ' '.get_string('statsreportforuser').' ' .fullname($user,true) : '')
                      .((!empty($roleid)) ? ' '.get_field('role','name','id',$roleid) : ''));
            

        if (empty($CFG->gdversion)) {
            echo "(".get_string("gdneed").")";
        } else {
            if ($mode == STATS_MODE_DETAILED) {
                echo '<center><img src="'.$CFG->wwwroot.'/course/report/stats/graph.php?mode='.$mode.'&course='.$course->id.'&time='.$time.'&report='.$report.'&userid='.$userid.'" /></center>';
            } else {
                echo '<center><img src="'.$CFG->wwwroot.'/course/report/stats/graph.php?mode='.$mode.'&course='.$course->id.'&time='.$time.'&report='.$report.'&roleid='.$roleid.'" /></center>';
            }
        }

        $table = new StdClass;
        $table->align = array('left','center','center','center');
        $param->table = str_replace('user_','',$param->table);
        $table->head = array(get_string('periodending','moodle',$param->table));
        if (empty($param->crosstab)) {
            $table->head[] = $param->line1;
            if (!empty($param->line2)) {
                $table->head[] = $param->line2; 
            }
        }
        if (empty($param->crosstab)) {
            foreach  ($stats as $stat) {
                $a = array(userdate($stat->timeend-(60*60*24),get_string('strftimedate'),$CFG->timezone),$stat->line1);
                if (isset($stat->line2)) {
                    $a[] = $stat->line2;
                }
                if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                    $a[] = '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?id='.
                        $course->id.'&chooselog=1&showusers=1&showcourses=1&user='
                        .$userid.'&date='.usergetmidnight($stat->timeend-(60*60*24)).'">'
                        .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                }
                $table->data[] = $a;
            }
        } else {
            $data = array();
            $roles = array();
            $times = array();
            $missedlines = array();
            foreach ($stats as $stat) {
                if (!empty($stat->zerofixed)) {
                    $missedlines[] = $stat->timeend;
                }
                $data[$stat->timeend][$stat->roleid] = $stat->line1;
                if ($stat->roleid != 0) {
                    if (!array_key_exists($stat->roleid,$roles)) {
                        $roles[$stat->roleid] = get_field('role','name','id',$stat->roleid);
                    }
                }
                if (!array_key_exists($stat->timeend,$times)) {
                    $times[$stat->timeend] = userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone);
                }
            }
            foreach ($data as $time => $rolesdata) {
                if (in_array($time,$missedlines)) {
                    $rolesdata = array();
                    foreach ($roles as $roleid => $guff) {
                        if ($roleid == 0 ) {
                            continue;
                        }
                        $rolesdata[$roleid] = 0;
                    }
                }
                krsort($rolesdata); 
                $row = array_merge(array($times[$time]),$rolesdata);
                if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                    $row[] = '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?id='
                        .$course->id.'&chooselog=1&showusers=1&showcourses=1&user='.$userid
                        .'&date='.usergetmidnight($time-(60*60*24)).'">'
                        .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                }
                $table->data[] = $row;
            }
            krsort($roles); 
            $table->head = array_merge($table->head,$roles);
        }
        $table->head[] = get_string('logs');
        if (!empty($lastrecord)) {
            $lastrecord[] = $lastlink;
            $table->data[] = $lastrecord;
        }
        print_table($table);
    }

?>