<?PHP //$Id$

class block_online_users extends block_base {
    function init() {
        $this->title = get_string('blockname','block_online_users');
        $this->version = 2006030100;
    }

    function has_config() {return true;}

    function get_content() {
        global $USER, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        if (empty($this->instance)) {
            return $this->content;
        }
    
        $timetoshowusers = 300; //Seconds default
        if (isset($CFG->block_online_users_timetosee)) {
            $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        }
        $timefrom = time()-$timetoshowusers;

        //Calculate if we are in separate groups
        $isseparategroups = ($COURSE->groupmode == SEPARATEGROUPS && $COURSE->groupmodeforce && !isteacheredit($COURSE->id));

        //Get the user current group
        $currentgroup = $isseparategroups ? get_current_group($COURSE->id) : NULL;

        $groupmembers = "";
        $groupselect = "";

        //Add this to the SQL to show only group users
        if ($currentgroup !== NULL) {
            $groupmembers = ", {$CFG->prefix}groups_members gm ";
            $groupselect .= " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
        }

        if ($COURSE->id == SITEID) {  // Site-level
            $courseselect = '';
            $timeselect = "AND (s.timeaccess > $timefrom OR u.lastaccess > $timefrom)";
        } else {
            $courseselect = "AND s.course = '".$COURSE->id."'";
            $timeselect = "AND s.timeaccess > $timefrom";
        }

        $users = array();

        if ($students = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess, s.timeaccess
                                     FROM {$CFG->prefix}user u,
                                          {$CFG->prefix}user_students s
                                          $groupmembers
                                     WHERE u.id = s.userid $courseselect $groupselect $timeselect 
                                  ORDER BY s.timeaccess DESC ".sql_paging_limit(0,20))) {

            foreach ($students as $student) {
                $student->fullname = fullname($student);
                $users[$student->id] = $student;
            }
        }

        if ($COURSE->id == SITEID && $CFG->allusersaresitestudents) {
            if ($siteusers = get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess
                                     FROM {$CFG->prefix}user u
                                     WHERE u.lastaccess > $timefrom AND u.username <> 'guest'
                                  ORDER BY u.lastaccess DESC ".sql_paging_limit(0,20))) {
                foreach ($siteusers as $siteuser) {
                    $siteuser->fullname = fullname($siteuser);
                    $siteuser->timeaccess = $siteuser->lastaccess;
                    $users[$siteuser->id] = $siteuser;
                }
            }
        }

        $findteacherssql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess, s.timeaccess
                                     FROM {$CFG->prefix}user u,
                                          {$CFG->prefix}user_teachers s
                                          $groupmembers
                                     WHERE u.id = s.userid $courseselect $groupselect $timeselect ";

        if (!isteacher($COURSE->id)) {
            // Hide hidden teachers from students.
            $findteacherssql .= 'AND s.authority > 0 ';
        }
        $findteacherssql .= 'ORDER BY s.timeaccess DESC';

        if ($teachers = get_records_sql($findteacherssql)) {
            foreach ($teachers as $teacher) {
                $teacher->fullname = '<strong>'.fullname($teacher).'</strong>';
                $users[$teacher->id] = $teacher;
            }
        }


        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);

        $this->content->text = "<div class=\"message\">(".get_string("periodnminutes","block_online_users",$minutes).")</div>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            //Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link (existing lang string).
            //Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time(time() - max($user->timeaccess, $user->lastaccess)); //bruno to calculate correctly on frontpage 
                $this->content->text .= print_user_picture($user->id, $COURSE->id, $user->picture, 16, true).' ';
                $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$COURSE->id.'" title="'.$timeago.'">'.$user->fullname.'</a>';
                if (!empty($USER->id) and ($USER->id != $user->id) and !empty($CFG->messaging) and !isguest()) {  // Only when logged in
                    $this->content->text .= "\n".' <a title="'.get_string('messageselectadd').'" target="message_'.$user->id.'" href="'.$CFG->wwwroot.'/message/discussion.php?id='.$user->id.'" onclick="return openpopup(\'/message/discussion.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'
                        .'<img class="messageicon" src="'.$CFG->pixpath.'/t/message.gif" alt="'. get_string('messageselectadd') .'" /></a>';
                }
                $this->content->text .= "</li>\n";
            }
            $this->content->text .= "</ul>\n";
        } else {
            $this->content->text .= "<div class=\"message\">".get_string("none")."</div>";
        }

        return $this->content;
    }
}

?>
