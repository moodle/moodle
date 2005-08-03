<?PHP //$Id$

class block_online_users extends block_base {
    function init() {
        $this->title = get_string('blockname','block_online_users');
        $this->version = 2004111600;
    }

    function has_config() {return true;}

    function get_content() {
        global $USER, $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        if (empty($this->instance)) {
            return $this->content;
        }
    
        $course = get_record('course', 'id', $this->instance->pageid);

        $timetoshowusers = 300; //Seconds default
        if (isset($CFG->block_online_users_timetosee)) {
            $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        }
        $timefrom = time()-$timetoshowusers;

        //Calculate if we are in separate groups
        $isseparategroups = ($course->groupmode == SEPARATEGROUPS && $course->groupmodeforce && !isteacheredit($this->instance->pageid));

        //Get the user current group
        $currentgroup = $isseparategroups ? get_current_group($this->instance->pageid) : NULL;

        $groupmembers = "";
        $groupselect = "";

        //Add this to the SQL to show only group users
        if ($currentgroup !== NULL) {
            $groupmembers = ", {$CFG->prefix}groups_members gm ";
            $groupselect .= " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
        }

        if ($this->instance->pageid == SITEID) {  // Site-level
            $courseselect = '';
            $timeselect = "AND (s.timeaccess > $timefrom OR u.lastaccess > $timefrom)";
        } else {
            $courseselect = "AND s.course = '".$this->instance->pageid."'";
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

        if ($this->instance->pageid == SITEID && $CFG->allusersaresitestudents) {
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

        $this->content->text = "<div class=\"message\">(".get_string("periodnminutes","block_online_users",$minutes).")</div>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            foreach ($users as $user) {
                $this->content->text .= '<div class="listentry">';
                $timeago = format_time(time() - max($user->timeaccess, $user->lastaccess)); //bruno to calculate correctly on frontpage 
                if ($user->picture) {
                    if ($CFG->slasharguments) {
                        $imgtag = '<img src="'.$CFG->wwwroot.'/user/pix.php/'.$user->id.'/f2.jpg" style="height: 16px; width:16px; vertical-align: middle;" alt="" /> ';
                    } else {
                        $imgtag = '<img src="'.$CFG->wwwroot.'/user/pix.php?file=/'.$user->id.'/f2.jpg" style="height: 16px; width:16px; vertical-align: middle;" alt="" /> ';
                    }
                    $this->content->text .= $imgtag;
                } else {
                    $this->content->text .= '<img src="'.$CFG->pixpath.'/i/user.gif" style="height: 16px; width:16px; vertical-align: middle;" alt="" /> ';
                }
                $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->instance->pageid.'" title="'.$timeago.'">'.$user->fullname.'</a>';
                if (!empty($USER->id) and ($USER->id != $user->id) and !empty($CFG->messaging) and !isguest()) {  // Only when logged in
                    $this->content->text .= '&nbsp;<a target="message_'.$user->id.'" href="'.$CFG->wwwroot.'/message/discussion.php?id='.$user->id.'" onclick="return openpopup(\'/message/discussion.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);"><img height="11" width="11" src="'.$CFG->pixpath.'/t/message.gif" alt="" /></a>';
                }
                $this->content->text .= '</div>';
            }
        } else {
            $this->content->text .= "<div class=\"message\">".get_string("none")."</div>";
        }

        return $this->content;
    }
}

?>
