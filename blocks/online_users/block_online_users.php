<?PHP //$Id$

class CourseBlock_online_users extends MoodleBlock {
    function CourseBlock_online_users ($course) {
        $this->title = get_string('blockname','block_online_users');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052700;
    }

    function has_config() {return true;}

    function print_config() {
        global $CFG, $USER, $THEME;
        print_simple_box_start('center', '', $THEME->cellheading);
        include($CFG->dirroot.'/blocks/'.$this->name().'/config.html');
        print_simple_box_end();
        return true;
    }
    function handle_config($config) {
        foreach ($config as $name => $value) {
            set_config($name, $value);
        }
        return true;
    }

    function get_content() {
        global $USER, $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->course)) {
            $this->content = '';
            return $this->content;
        }
    
        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';
        
        $timetoshowusers = 300; //Seconds default
        if (isset($CFG->block_online_users_timetosee)) {
            $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        }
        $timefrom = time()-$timetoshowusers;

        //Calculate if we are in separate groups
        $isseparategroups = ($this->course->groupmode == SEPARATEGROUPS and $this->course->groupmodeforce and
                             !isteacheredit($this->course->id));

        //Get the user current group
        $currentgroup = $isseparategroups ? get_current_group($this->course->id) : NULL;

        $groupmembers = "";
        $groupselect = "";

        //Add this to the SQL to show only group users
        if ($currentgroup !== NULL) {
            $groupmembers = ", {$CFG->prefix}groups_members gm ";
            $groupselect .= " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
        }

        if (empty($this->course->category)) {  // Site-level
            $courseselect = '';
            $timeselect = "AND (s.timeaccess > $timefrom OR u.lastaccess > $timefrom)";
        } else {
            $courseselect = "AND s.course = '".$this->course->id."'";
            $timeselect = "AND s.timeaccess > $timefrom";
        }

        $users = array();

        if ($students = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess, s.timeaccess
                                     FROM {$CFG->prefix}user u,
                                          {$CFG->prefix}user_students s
                                          $groupmembers
                                     WHERE u.id = s.userid $courseselect $groupselect $timeselect 
                                  ORDER BY s.timeaccess DESC")) {
            foreach ($students as $student) {
                $student->fullname = fullname($student);
                $users[$student->id] = $student;
            }
        }

        if (!$this->course->category and $CFG->allusersaresitestudents) {
            if ($siteusers = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess
                                     FROM {$CFG->prefix}user u
                                     WHERE u.lastaccess > $timefrom AND u.username <> 'guest'
                                  ORDER BY u.lastaccess DESC")) {
                foreach ($siteusers as $siteuser) {
                    $siteuser->fullname = fullname($siteuser);
                    $siteuser->timeaccess = $siteuser->lastaccess;
                    $users[$siteuser->id] = $siteuser;
                }
            }
        }

        if ($teachers = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess, s.timeaccess
                                     FROM {$CFG->prefix}user u,
                                          {$CFG->prefix}user_teachers s
                                          $groupmembers
                                     WHERE u.id = s.userid $courseselect $groupselect $timeselect
                                  ORDER BY s.timeaccess DESC")) {
            foreach ($teachers as $teacher) {
                $teacher->fullname = '<b>'.fullname($teacher).'</b>';
                $users[$teacher->id] = $teacher;
            }
        }


        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);

        $this->content->text = "<font size=\"-2\"><div align=center>(".get_string("periodnminutes","block_online_users",$minutes).")</div></font>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            foreach ($users as $user) {
                $this->content->text .= '<div style="text-align: left; font-size: 0.75em; padding-top: 5px;">';
                $timeago = format_time(time() - max($user->timeaccess, $user->lastaccess)); //bruno to calculate correctly on frontpage 
                if ($user->picture==0) {
                    $this->content->text .= '<img src="'.$CFG->pixpath.'/i/user.gif" style="height: 16px; width=16px; vertical-align: middle;" alt=""> ';
                } else {
                    if ($CFG->slasharguments) {
                        $imgtag = '<img src="'.$CFG->wwwroot.'/user/pix.php/'.$user->id.'/f2.jpg" style="height: 16px; width=16px; vertical-align: middle;" alt=""> ';
                    } else {
                        $imgtag = '<img src="'.$CFG->wwwroot.'/user/pix.php?file=/'.$user->id.'/f2.jpg" style="height: 16px; width=16px; vertical-align: middle;" alt=""> ';
                    }
                    $this->content->text .= $imgtag;
                }
                $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->course->id.'" title="'.$timeago.'">'.$user->fullname.'</a></div>';
            }
/*
                $table->align = array("right","left");
                $table->cellpadding = 1;
                $table->cellspacing = 1;
                $table->data[] = array("<img src=\"$CFG->pixpath/i/user.gif\" height=16 width=16 alt=\"\">",$user->fullname);
            }
            // Slightly hacky way to do it but...
            ob_start();
            print_table($table);
            //$this->content->text .= "<br>".ob_get_contents();
            ob_end_clean();
*/
        } else {
            $this->content->text .= "<font size=\"-1\"><p align=center>".get_string("none")."</p></font>";
        }

        return $this->content;
    }
}

?>
