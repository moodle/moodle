<?php //$Id$

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
        $timefrom = 100 * floor((time()-$timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache

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
            $groupmembers = ', '.groups_members_from_sql(); //TODO: ", {$CFG->prefix}groups_members gm ";
            $groupselect .= groups_members_where_sql($currentgroup, 'u.id'); //" AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
        }

        if ($COURSE->id == SITEID) {  // Site-level
            $courseselect = '';
            $timeselect = "AND (ul.timeaccess > $timefrom OR u.lastaccess > $timefrom)";
        } else {
            $courseselect = "AND ul.courseid = '".$COURSE->id."'";
            $timeselect = "AND ul.timeaccess > $timefrom";
        }

        $users = array();

        $SQL = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess, ul.timeaccess
                FROM {$CFG->prefix}user_lastaccess ul,
                     {$CFG->prefix}user u
                     $groupmembers
                WHERE 
                      ul.userid = u.id
                      $courseselect
                      $timeselect
                      $groupselect
                GROUP BY u.id 
                ORDER BY ul.timeaccess DESC";
        
        
        
        $pcontext = get_related_contexts_string($context);
    
        if ($pusers = get_records_sql($SQL, 0, 50)) {   // We'll just take the most recent 50 maximum
            foreach ($pusers as $puser) {
                
                // if current user can't view hidden role assignment in this context and 
                // user has a hidden role assigned at this context or any parent contexts,
                // ignore this user
                
                $SQL = "SELECT id,id FROM {$CFG->prefix}role_assignments
                        WHERE userid = $puser->id
                        AND contextid $pcontext
                        AND hidden = 1";
                
                if (!has_capability('moodle/role:viewhiddenassigns', $context) && record_exists_sql($SQL)) {
                    // can't see this user as the current user has no capability
                    // and this user has a hidden assignment at this context or higher
                    continue;  
                }
              
                $puser->fullname = fullname($puser);
                $users[$puser->id] = $puser;  
            }
        }  
           
        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);

        $this->content->text = "<div class=\"info\">(".get_string("periodnminutes","block_online_users",$minutes).")</div>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            //Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link (existing lang string).
            //Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time(time() - max($user->timeaccess, $user->lastaccess)); //bruno to calculate correctly on frontpage 
                if ($user->username == 'guest') {
                    $this->content->text .= '<div class="user">'.print_user_picture($user->id, $COURSE->id, $user->picture, 16, true, false, '', false);
                    $this->content->text .= get_string('guestuser').'</div>';

                } else {
                    $this->content->text .= '<div class="user"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$COURSE->id.'" title="'.$timeago.'">';
                    $this->content->text .= print_user_picture($user->id, $COURSE->id, $user->picture, 16, true, false, '', false);
                    $this->content->text .= $user->fullname.'</a></div>';
                }
                if (!empty($USER->id) and ($USER->id != $user->id) and !empty($CFG->messaging) and 
                    !isguest() and $user->username != 'guest') {  // Only when logged in and messaging active etc
                    $this->content->text .= '<div class="message"><a title="'.get_string('messageselectadd').'" href="'.$CFG->wwwroot.'/message/discussion.php?id='.$user->id.'" onclick="this.target=\'message_'.$user->id.'\';return openpopup(\'/message/discussion.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'
                        .'<img class="iconsmall" src="'.$CFG->pixpath.'/t/message.gif" alt="'. get_string('messageselectadd') .'" /></a></div>';
                }
                $this->content->text .= "</li>\n";
            }
            $this->content->text .= '</ul><div class="clearer"><!-- --></div>';
        } else {
            $this->content->text .= "<div class=\"info\">".get_string("none")."</div>";
        }

        return $this->content;
    }
}

?>
