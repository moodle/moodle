<?PHP //$Id$

class block_messages extends block_base {
    function init() {
        $this->title = get_string('messages','message');
        $this->version = 2007101509;
    }

    function get_content() {
        global $USER, $CFG;

        if (!$CFG->messaging) {
            return ''; 
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        if (empty($this->instance) or empty($USER->id) or isguest() or empty($CFG->messaging)) {
            return $this->content;
        }

        $this->content->footer = '<a href="'.$CFG->wwwroot.'/message/index.php" onclick="this.target=\'message\'; return openpopup(\'/message/index.php\', \'message\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'.get_string('messages', 'message').'</a>...';

        $users = get_records_sql("SELECT m.useridfrom as id, COUNT(m.useridfrom) as count,
                                         u.firstname, u.lastname, u.picture, u.imagealt, u.lastaccess
                                       FROM {$CFG->prefix}user u, 
                                            {$CFG->prefix}message m 
                                       WHERE m.useridto = '$USER->id' 
                                         AND u.id = m.useridfrom
                                    GROUP BY m.useridfrom, u.firstname,u.lastname,u.picture,u.lastaccess,u.imagealt");


        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            $this->content->text .= '<ul class="list">';
            foreach ($users as $user) {
                $timeago = format_time(time() - $user->lastaccess);
                $this->content->text .= '<li class="listentry"><div class="user"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'" title="'.$timeago.'">';
                $this->content->text .= print_user_picture($user, SITEID, $user->picture, 0, true, false, '', false);
                $this->content->text .= fullname($user).'</a></div>';
                $this->content->text .= '<div class="message"><a href="'.$CFG->wwwroot.'/message/discussion.php?id='.$user->id.'" onclick="this.target=\'message_'.$user->id.'\'; return openpopup(\'/message/discussion.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);"><img class="iconsmall" src="'.$CFG->pixpath.'/t/message.gif" alt="" />&nbsp;'.$user->count.'</a>';
                $this->content->text .= '</div></li>';
            }
            $this->content->text .= '</ul>'; 
        } else {
            $this->content->text .= '<div class="info">';
            $this->content->text .= get_string('nomessages', 'message');
            $this->content->text .= '</div>'; 
        }

        return $this->content;
    }
}

?>
