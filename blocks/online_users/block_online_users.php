<?php

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
        global $USER, $CFG, $DB, $OUTPUT;

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

        //Calculate if we are in separate groups
        $isseparategroups = ($this->page->course->groupmode == SEPARATEGROUPS
                             && $this->page->course->groupmodeforce
                             && !has_capability('moodle/site:accessallgroups', $this->page->context));

        //Get the user current group
        $currentgroup = $isseparategroups ? groups_get_course_group($this->page->course) : NULL;

        $groupmembers = "";
        $groupselect  = "";
        $rafrom       = "";
        $rawhere      = "";
        $params = array();

        //Add this to the SQL to show only group users
        if ($currentgroup !== NULL) {
            $groupmembers = ", {groups_members} gm";
            $groupselect = "AND u.id = gm.userid AND gm.groupid = :currentgroup";
            $params['currentgroup'] = $currentgroup;
        }

        if ($this->page->course->id == SITEID) {  // Site-level
            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, MAX(u.lastaccess) AS lastaccess
                      FROM {user} u $groupmembers
                     WHERE u.lastaccess > $timefrom
                           $groupselect
                  GROUP BY u.id, u.username, u.firstname, u.lastname, u.picture
                  ORDER BY lastaccess DESC ";

           $csql = "SELECT COUNT(u.id), u.id
                      FROM {user} u $groupmembers
                     WHERE u.lastaccess > $timefrom
                           $groupselect
                  GROUP BY u.id";

        } else { // Course-level
            if (!has_capability('moodle/role:viewhiddenassigns', $this->page->context)) {
                $pcontext = get_related_contexts_string($this->page->context);
                $rafrom  = ", {role_assignments} ra";
                $rawhere = " AND ra.userid = u.id AND ra.contextid $pcontext AND ra.hidden = 0";
            }

            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, MAX(ul.timeaccess) AS lastaccess
                      FROM {user_lastaccess} ul, {user} u $groupmembers $rafrom
                     WHERE ul.timeaccess > $timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           $groupselect $rawhere
                  GROUP BY u.id, u.username, u.firstname, u.lastname, u.picture
                  ORDER BY lastaccess DESC";

           $csql = "SELECT u.id
                      FROM {user_lastaccess} ul, {user} u $groupmembers $rafrom
                     WHERE ul.timeaccess > $timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           $groupselect $rawhere
                  GROUP BY u.id";

            $params['courseid'] = $this->page->course->id;
        }

        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);

        // Verify if we can see the list of users, if not just print number of users
        if (!has_capability('block/online_users:viewlist', $this->page->context)) {
            if (!$usercount = $DB->count_records_sql($csql, $params)) {
                $usercount = get_string("none");
            }
            $this->content->text = "<div class=\"info\">".get_string("periodnminutes","block_online_users",$minutes).": $usercount</div>";
            return $this->content;
        }

        if ($users = $DB->get_records_sql($sql, $params, 0, 50)) {   // We'll just take the most recent 50 maximum
            foreach ($users as $user) {
                $users[$user->id]->fullname = fullname($user);
            }
        } else {
            $users = array();
        }

        if (count($users) < 50) {
            $usercount = "";
        } else {
            $usercount = $DB->count_records_sql($csql, $params);
            $usercount = ": $usercount";
        }

        $this->content->text = "<div class=\"info\">(".get_string("periodnminutes","block_online_users",$minutes)."$usercount)</div>";

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            //Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link (existing lang string).
            //Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            if (!empty($USER->id) && has_capability('moodle/site:sendmessage', $this->page->context)
                           && !empty($CFG->messaging) && !isguestuser()) {
                $canshowicon = true;
            } else {
                $canshowicon = false;
            }
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time(time() - $user->lastaccess); //bruno to calculate correctly on frontpage
                $userpic = moodle_user_picture::make($user, $this->page->course->id);
                $userpic->size = 16;

                if ($user->username == 'guest') {
                    $this->content->text .= '<div class="user">'.$OUTPUT->user_picture($userpic);
                    $this->content->text .= get_string('guestuser').'</div>';

                } else {
                    $this->content->text .= '<div class="user"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->page->course->id.'" title="'.$timeago.'">';
                    $this->content->text .= '<div class="user">'.$OUTPUT->user_picture($userpic);
                    $this->content->text .= $user->fullname.'</a></div>';
                }
                if ($canshowicon and ($USER->id != $user->id) and  $user->username != 'guest') {  // Only when logged in and messaging active etc
                    $this->content->text .= '<div class="message"><a title="'.get_string('messageselectadd').'" href="'.$CFG->wwwroot.'/message/discussion.php?id='.$user->id.'" onclick="this.target=\'message_'.$user->id.'\';return openpopup(\'/message/discussion.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'
                        .'<img class="iconsmall" src="'.$OUTPUT->pix_url('t/message') . '" alt="'. get_string('messageselectadd') .'" /></a></div>';
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


