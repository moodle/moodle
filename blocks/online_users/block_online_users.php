<?PHP //$Id$

/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
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

        // Get context so we can check capabilities.
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        //Calculate if we are in separate groups
        $isseparategroups = ($COURSE->groupmode == SEPARATEGROUPS 
                             && $COURSE->groupmodeforce
                             && !has_capability('moodle/site:accessallgroups', $context));

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
            $timeselect = "AND timeaccess > $timefrom OR u.lastaccess > $timefrom)";
        } else {
            $courseselect = "AND s.course = '".$COURSE->id."'";
            $timeselect = "AND s.timeaccess > $timefrom";
        }

        $users = array();

        $SQL1 = "SELECT DISTINCT userid, userid FROM {$CFG->prefix}log WHERE course=$COURSE->id AND time>$timefrom";
        if ($records = get_records_sql($SQL1)) {
            $possibleusers = '(';
            foreach ($records as $record) {
                $possibleusers .= $record->userid.',';
            }
            $possibleusers = rtrim($possibleusers, ',').')';
            $SQL2 = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess
                    FROM {$CFG->prefix}user u
                    $groupmembers
                    WHERE u.id IN $possibleusers $groupselect ".sql_paging_limit(0,20);
        
            if ($pusers = get_records_sql($SQL2)) {
                foreach ($pusers as $puser) {
                    $puser->fullname = fullname($puser);
                    $users[$puser->id] = $puser;  
                }
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
                        .'<img class="icon message" src="'.$CFG->pixpath.'/t/message.gif" alt="'. get_string('messageselectadd') .'" /></a>';
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