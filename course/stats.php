<?php

    require_once(dirname(dirname(__FILE__)).'/config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');

    if (empty($CFG->enablestats)) {
        error("Stats is not enabled.");
    }

    $courseid = required_param('course',PARAM_INT);
    $report = optional_param('report',0,PARAM_INT);
    $time = optional_param('time',0,PARAM_INT);
    $mode = optional_param('mode',STATS_MODE_GENERAL,PARAM_INT);
    $userid = optional_param('userid',0,PARAM_INT);

    if ($report == STATS_REPORT_USER_LOGINS) {
        $courseid = SITEID; //override
    }

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

    $strheader = get_string('stats');
    $strnav = (($course->id != SITEID) ?  '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'">'.$course->shortname.'</a> -> ' : '').$strheader;
    
    $reportoptions = stats_get_report_options($courseid,$mode);

    $courses = get_courses('all','c.shortname','c.id,c.shortname,c.fullname');
    $courseoptions = array();

    foreach ($courses as $c) {
        if (isteacher($c->id)) {
            $courseoptions[$c->id] = $c->shortname;
        }
    }

    $tableprefix = $CFG->prefix.'stats_';

    if ($mode == STATS_MODE_DETAILED) {
        $tableprefix = $CFG->prefix.'stats_user_';
    }

    $earliestday = get_field_sql('SELECT timeend FROM '.$tableprefix.'daily ORDER BY timeend LIMIT 1');
    $earliestweek = get_field_sql('SELECT timeend FROM '.$tableprefix.'weekly ORDER BY timeend LIMIT 1');
    $earliestmonth = get_field_sql('SELECT timeend FROM '.$tableprefix.'monthly ORDER BY timeend LIMIT 1');
    
    if (empty($earliestday)) $earliestday = time();
    if (empty($earliestweek)) $earliestweek = time();
    if (empty($earliestmonth)) $earliestmonth = time();

    $now = stats_get_base_daily();
    $lastweekend = stats_get_base_weekly();
    $lastmonthend = stats_get_base_monthly();

    $timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

    if (empty($timeoptions)) {
        error(get_string('nostatstodisplay'), $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    $options = array();
    $options[STATS_MODE_GENERAL] = get_string('statsmodegeneral');
    $options[STATS_MODE_DETAILED] = get_string('statsmodedetailed');

    $menu = choose_from_menu($options,'mode',$mode,'','this.form.submit();',0,true);

    $menu = '<form action="stats.php" method="post">'."\n"
        .'<input type="hidden" name="course" value="'.$course->id.'" />'."\n"
        .'<input type="hidden" name="time" value="'.$time.'" />'."\n"
        .$menu."\n".'</form>';
    print_header($strheader,$strheader,$strnav,'','',true,'&nbsp',$menu);

    echo '<form action="stats.php" method="post">'."\n"
        .'<input type="hidden" name="mode" value="'.$mode.'" />'."\n";

    $table->width = '*';

    if ($mode == STATS_MODE_DETAILED) {
        if (!empty($time)) {
            $param = stats_get_parameters($time,null); // we only care about the table and the time string.
            $sql =  'SELECT DISTINCT s.userid,s.roleid,u.firstname,u.lastname,u.idnumber,u.nickname FROM '.$CFG->prefix.'stats_user_'.$param->table.' s JOIN '.$CFG->prefix.'user u ON u.id = s.userid '
                .'WHERE courseid = '.$course->id.' AND timeend > '.$param->timeafter  . ((!empty($param->stattype)) ? ' AND stattype = \''.$param->stattype.'\'' : '');
            if (!isadmin()) {
                $sql .= ' AND s.roleid = 1 OR s.userid = '.$USER->id;
            }
        } else {
            $sql = 'SELECT s.userid,u.firstname,u.lastname,u.idnumber,u.nickname,1 AS roleid FROM '.$CFG->prefix.'user_students s JOIN '.$CFG->prefix.'user u ON u.id = s.userid WHERE course = '.$course->id;
        }
        $us = get_records_sql($sql);
        foreach ($us as $u) {
            $role = $course->student;
            if ($u->roleid == 2) {
                $role = $course->teacher;
            }
            if (isadmin($u->userid)) {
                $role = get_string('admin');
            }
            $users[$u->userid] = $role.' - '.fullname($u,isteacher($course->id));
        }
        if (empty($time)) {
            if (isadmin()) {
                $sql = 'SELECT t.userid,u.firstname,u.lastname,u.idnumber,u.nickname,1 AS roleid FROM '.$CFG->prefix.'user_teachers t JOIN '.$CFG->prefix.'user u ON u.id = t.userid WHERE course = '.$course->id;
                $moreusers = get_records_sql($sql);
                foreach ($moreusers as $u) {
                    $users[$u->userid] = $course->teacher .' - '.fullname($u,true);
                }
            } else {
                $users[$USER->id] = $course->teacher.' - '.fullname($USER);
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
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',$report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else {
        $table->align = array('left','left','left','left','left','left','left');
        $table->data[] = array(get_string('course'),choose_from_menu($courseoptions,'course',$course->id,'','','',true),
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',$report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    }


    print_table($table);
    echo '</form>';

    if (!empty($report) && !empty($time)) {
        if ($report == STATS_REPORT_LOGINS && $course->id != SITEID) {
            error("This type of report is only available for the site course");
        }
        $param = stats_get_parameters($time,$report);
        if ($mode == STATS_MODE_DETAILED) {
            $param->table = 'user_'.$param->table;
        }
        $sql = 'SELECT timeend,'.$param->fields.',id FROM '.$CFG->prefix.'stats_'.$param->table.' WHERE courseid = '.$course->id.((!empty($userid)) ? ' AND userid = '.$userid : '')
            . ((!empty($param->stattype)) ? ' AND stattype = \''.$param->stattype.'\'' : '')
            .' AND timeend > '.$param->timeafter
            .' ORDER BY timeend DESC';
        $stats = get_records_sql($sql);

        if (empty($stats)) {
            error(get_string('statsnodata'.((!empty($user)) ? 'user' : '')),$CFG->wwwroot.'/stats/index.php?course='.$course->id.'&mode='.$mode.'&time='.$time);
        }

        $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)));

        print_heading($course->shortname.' - '.get_string('statsreport'.$report).((!empty($user)) ? ' '.get_string('statsreportforuser').' ' .fullname($user,isteacher($course->id)) : ''));

        if (empty($CFG->gdversion)) {
            echo "(".get_string("gdneed").")";
        } else {
            if ($mode == STATS_MODE_DETAILED) {
                echo '<center><img src="'.$CFG->wwwroot.'/course/statsgraph.php?course='.$course->id.'&time='.$time.'&report='.$report.'&userid='.$userid.'" /></center>';
            } else {
                echo '<center><img src="'.$CFG->wwwroot.'/course/statsgraph.php?course='.$course->id.'&time='.$time.'&report='.$report.'" /></center>';
            }
        }

        $table = new object();
        $table->align = array('left','center','center','center');
        $param->table = str_replace('user_','',$param->table);
        $table->head = array(get_string('periodending','moodle',$param->table),$param->line1);
        if (!empty($param->line2)) {
            $table->head[] = $param->line2; 
        }
        $table->head[] = get_string('logs');
        
        foreach  ($stats as $stat) {
            $a = array(userdate($stat->timeend),$stat->line1);
            if (isset($stat->line2)) {
                $a[] = $stat->line2;
            }
            if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                $a[] = '<a href="'.$CFG->wwwroot.'/course/log.php?id='.$course->id.'&chooselog=1&showusers=1&showcourses=1&user='.$userid.'&date='.usergetmidnight($stat->timeend-(60*60*24)).'">'.get_string('course').' ' .get_string('logs').'</a>&nbsp;';
            }
            $table->data[] = $a;
        }
        print_table($table);
    }
    
    print_footer();


?>