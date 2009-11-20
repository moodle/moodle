<?php //$Id$

/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_online_users extends block_base {
    function init() {
        $this->title = get_string('blockname','block_online_users');
        $this->version = 2007101510;
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

        if (empty($this->instance->pinned)) {
            $blockcontext = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        } else {
            $blockcontext = get_context_instance(CONTEXT_SYSTEM); // pinned blocks do not have own context
        }

        //Calculate if we are in separate groups
        $isseparategroups = ($COURSE->groupmode == SEPARATEGROUPS
                             && $COURSE->groupmodeforce
                             && !has_capability('moodle/site:accessallgroups', $context));

        //Get the user current group
        $currentgroup = $isseparategroups ? groups_get_course_group($COURSE) : NULL;

        $groupmembers = "";
        $groupselect = "";
        $rafrom  = "";
        $rawhere = "";

        //Add this to the SQL to show only group users
        if ($currentgroup !== NULL) {
            $groupmembers = ",  {$CFG->prefix}groups_members gm ";
            $groupselect = " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
        }

        if ($COURSE->id == SITEID) {  // Site-level
            $select = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, max(u.lastaccess) as lastaccess ";
            $from = "FROM {$CFG->prefix}user u
                          $groupmembers ";
            $where = "WHERE u.lastaccess > $timefrom
                      $groupselect ";
            $order = "ORDER BY lastaccess DESC ";

        } else { // Course-level
            if (!has_capability('moodle/role:viewhiddenassigns', $context)) {
                $pcontext = get_related_contexts_string($context);
                $rafrom  = ", {$CFG->prefix}role_assignments ra";
                $rawhere = " AND ra.userid = u.id AND ra.contextid $pcontext AND ra.hidden = 0";
            }

            $courseselect = "AND ul.courseid = '".$COURSE->id."'";
            $select = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, max(ul.timeaccess) as lastaccess ";
            $from = "FROM {$CFG->prefix}user_lastaccess ul,
                          {$CFG->prefix}user u
                          $groupmembers $rafrom ";
            $where =  "WHERE ul.timeaccess > $timefrom
                       AND u.id = ul.userid
                       AND ul.courseid = $COURSE->id
                       $groupselect $rawhere ";
            $order = "ORDER BY lastaccess DESC ";
        }

        $groupby = "GROUP BY u.id, u.username, u.firstname, u.lastname, u.picture ";

        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);

        // Verify if we can see the list of users, if not just print number of users
        if (!has_capability('block/online_users:viewlist', $blockcontext)) {
            if (!$usercount = count_records_sql("SELECT COUNT(DISTINCT(u.id)) $from $where")) {
                $usercount = get_string("none");
            }
            $this->content->text = "<div class=\"info\">".get_string("periodnminutes","block_online_users",$minutes).": $usercount</div>";
            return $this->content;
        }

        $SQL = $select . $from . $where . $groupby . $order;

        if ($users = get_records_sql($SQL, 0, 50)) {   // We'll just take the most recent 50 maximum
            foreach ($users as $user) {
                $users[$user->id]->fullname = fullname($user);
            }
        } else {
            $users = array();
        }

        if (count($users) < 50) {
            $usercount = "";
        } else {
            $usercount = count_records_sql("SELECT COUNT(DISTINCT(u.id)) $from $where");
            $usercount = ": $usercount";
        }

        $this->content->text = "<div class=\"info\">(".get_string("periodnminutes","block_online_users",$minutes)."$usercount)</div>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            //Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link (existing lang string).
            //Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            if (!empty($USER->id) && has_capability('moodle/site:sendmessage', $context)
                           && !empty($CFG->messaging) && !isguest()) {
                $canshowicon = true;
            } else {
                $canshowicon = false;
            }
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time(time() - $user->lastaccess); //bruno to calculate correctly on frontpage
                if ($user->username == 'guest') {
                    $this->content->text .= '<div class="user">'.print_user_picture($user->id, $COURSE->id, $user->picture, 16, true, false, '', false);
                    $this->content->text .= get_string('guestuser').'</div>';

                } else {
                    $this->content->text .= '<div class="user"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$COURSE->id.'" title="'.$timeago.'">';
                    $this->content->text .= print_user_picture($user->id, $COURSE->id, $user->picture, 16, true, false, '', false);
                    $this->content->text .= $user->fullname.'</a></div>';
                }
                if ($canshowicon and ($USER->id != $user->id) and  $user->username != 'guest') {  // Only when logged in and messaging active etc
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
