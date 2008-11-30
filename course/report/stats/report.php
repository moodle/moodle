<?php // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $courses = get_courses('all','c.shortname','c.id,c.shortname,c.fullname');
    $courseoptions = array();

    foreach ($courses as $c) {
        $context = get_context_instance(CONTEXT_COURSE, $c->id);

        if (has_capability('coursereport/stats:view', $context)) {
            $courseoptions[$c->id] = $c->shortname;
        }
    }

    $reportoptions = stats_get_report_options($course->id, $mode);
    $timeoptions = report_stats_timeoptions($mode);
    if (empty($timeoptions)) {
        print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    $table->width = 'auto';

    if ($mode == STATS_MODE_DETAILED) {
        $param = stats_get_parameters($time,null,$course->id,$mode); // we only care about the table and the time string (if we have time)

        $sql = 'SELECT DISTINCT s.userid, u.firstname, u.lastname, u.idnumber
                     FROM '.$CFG->prefix.'stats_user_'.$param->table.' s
                     JOIN '.$CFG->prefix.'user u ON u.id = s.userid
                     WHERE courseid = '.$course->id
            . ((!empty($param->stattype)) ? ' AND stattype = \''.$param->stattype.'\'' : '')
            . ((!empty($time)) ? ' AND timeend >= '.$param->timeafter : '')
            .' ORDER BY u.lastname, u.firstname ASC';

        if (!$us = get_records_sql($sql)) {
            error('Cannot enter detailed view: No users found for this course.');
        }

        foreach ($us as $u) {
            $users[$u->userid] = fullname($u, true);
        }

        $table->align = array('left','left','left','left','left','left','left','left');
        $table->data[] = array(get_string('course'),choose_from_menu($courseoptions,'course',$course->id,'','','',true),
                               get_string('users'),choose_from_menu($users,'userid',$userid,'','','',true),
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',($report == 5) ? $report.$roleid : $report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else if ($mode == STATS_MODE_RANKED) {
        $table->align = array('left','left','left','left','left','left');
        $table->data[] = array(get_string('statsreporttype'),choose_from_menu($reportoptions,'report',($report == 5) ? $report.$roleid : $report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else if ($mode == STATS_MODE_GENERAL) {
        $table->align = array('left','left','left','left','left','left','left');
        $table->data[] = array(get_string('course'),choose_from_menu($courseoptions,'course',$course->id,'','','',true),
                               get_string('statsreporttype'),choose_from_menu($reportoptions,'report',($report == 5) ? $report.$roleid : $report,'','','',true),
                               get_string('statstimeperiod'),choose_from_menu($timeoptions,'time',$time,'','','',true),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    }

    echo '<form action="index.php" method="post">'."\n"
        .'<div>'."\n"
        .'<input type="hidden" name="mode" value="'.$mode.'" />'."\n";

    print_table($table);

    echo '</div>';
    echo '</form>';

    if (!empty($report) && !empty($time)) {
        if ($report == STATS_REPORT_LOGINS && $course->id != SITEID) {
            error('This type of report is only available for the site course');
        }

        $param = stats_get_parameters($time,$report,$course->id,$mode);

        if ($mode == STATS_MODE_DETAILED) {
            $param->table = 'user_'.$param->table;
        }

        if (!empty($param->sql)) {
            $sql = $param->sql;
        } else {
            $sql = 'SELECT '.((empty($param->fieldscomplete)) ? 'id,roleid,timeend,' : '').$param->fields
                .' FROM '.$CFG->prefix.'stats_'.$param->table.' WHERE '
                .(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
                .((!empty($userid)) ? ' userid = '.$userid.' AND ' : '')
                .((!empty($roleid)) ? ' roleid = '.$roleid.' AND ' : '')
                . ((!empty($param->stattype)) ? ' stattype = \''.$param->stattype.'\' AND ' : '')
                .' timeend >= '.$param->timeafter
                .' '.$param->extras
                .' ORDER BY timeend DESC';
        }

        $stats = get_records_sql($sql);

        if (empty($stats)) {
            notify(get_string('statsnodata'));

        } else {

            $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)));

            print_heading(format_string($course->shortname).' - '.get_string('statsreport'.$report)
                    .((!empty($user)) ? ' '.get_string('statsreportforuser').' ' .fullname($user,true) : '')
                    .((!empty($roleid)) ? ' '.get_field('role','name','id',$roleid) : ''));


            if (empty($CFG->gdversion)) {
                echo "(".get_string("gdneed").")";
            } else {
                if ($mode == STATS_MODE_DETAILED) {
                    echo '<div class="graph"><img src="'.$CFG->wwwroot.'/course/report/stats/graph.php?mode='.$mode.'&amp;course='.$course->id.'&amp;time='.$time.'&amp;report='.$report.'&amp;userid='.$userid.'" alt="'.get_string('statisticsgraph').'" /></div';
                } else {
                    echo '<div class="graph"><img src="'.$CFG->wwwroot.'/course/report/stats/graph.php?mode='.$mode.'&amp;course='.$course->id.'&amp;time='.$time.'&amp;report='.$report.'&amp;roleid='.$roleid.'" alt="'.get_string('statisticsgraph').'" /></div>';
                }
            }

            $table = new StdClass;
            $table->align = array('left','center','center','center');
            $param->table = str_replace('user_','',$param->table);
            switch ($param->table) {
                case 'daily'  : $period = get_string('day'); break;
                case 'weekly' : $period = get_string('week'); break;
                case 'monthly': $period = get_string('month', 'form'); break;
                default : $period = '';
            }
            $table->head = array(get_string('periodending','moodle',$period));
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
                        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_COURSE, $course->id))) {
                            $a[] = '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?id='.
                                $course->id.'&amp;chooselog=1&amp;showusers=1&amp;showcourses=1&amp;user='
                                .$userid.'&amp;date='.usergetmidnight($stat->timeend-(60*60*24)).'">'
                                .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                        } else {
                            $a[] = '';
                        }
                    }
                    $table->data[] = $a;
                }
            } else {
                $data = array();
                $roles = array();
                $times = array();
                $missedlines = array();
                $rolenames = get_all_roles();
                foreach ($rolenames as $r) {
                    $rolenames[$r->id] = $r->name;
                }
                $rolenames = role_fix_names($rolenames, get_context_instance(CONTEXT_COURSE, $course->id));
                foreach ($stats as $stat) {
                    if (!empty($stat->zerofixed)) {
                        $missedlines[] = $stat->timeend;
                    }
                    $data[$stat->timeend][$stat->roleid] = $stat->line1;
                    if ($stat->roleid != 0) {
                        if (!array_key_exists($stat->roleid,$roles)) {
                            $roles[$stat->roleid] = $rolenames[$stat->roleid];
                        }
                    } else {
                        if (!array_key_exists($stat->roleid,$roles)) {
                            $roles[$stat->roleid] = get_string('all');
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
                            $rolesdata[$roleid] = 0;
                        }
                    }
                    else {
                        foreach (array_keys($roles) as $r) {
                            if (!array_key_exists($r, $rolesdata)) {
                                $rolesdata[$r] = 0;
                            }
                        }
                    }
                    krsort($rolesdata);
                    $row = array_merge(array($times[$time]),$rolesdata);
                    if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_COURSE, $course->id))) {
                            $row[] = '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?id='
                                .$course->id.'&amp;chooselog=1&amp;showusers=1&amp;showcourses=1&amp;user='.$userid
                                .'&amp;date='.usergetmidnight($time-(60*60*24)).'">'
                                .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                        } else {
                            $row[] = '';
                        }
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
    }

?>
