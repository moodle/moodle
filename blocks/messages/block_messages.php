<?PHP //$Id$

class block_messages extends block_base {
    function init() {
        $this->title = get_string('messages','message');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004122800;
    }

    function get_content() {
        global $USER, $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        
        if (empty($this->instance) or empty($USER->id) or isguest() or empty($CFG->messaging)) {
            return $this->content;
        }

        $this->content->footer = '<a target="message" href="'.$CFG->wwwroot.'/message/index.php" onclick="return openpopup(\'/message/index.php\', \'message\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);">'.get_string('messages', 'message').'</a>...';

        $users = get_records_sql("SELECT m.useridfrom as id, COUNT(m.useridfrom) as count,
                                         u.firstname, u.lastname, u.picture, u.lastaccess
                                       FROM {$CFG->prefix}user u, 
                                            {$CFG->prefix}message m 
                                       WHERE m.useridto = '$USER->id' 
                                         AND u.id = m.useridfrom
                                    GROUP BY m.useridfrom, u.firstname,u.lastname,u.picture,u.lastaccess");


        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            foreach ($users as $user) {
                $this->content->text .= '<div style="text-align: left; font-size: 0.75em; padding-top: 5px;">';
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
                $timeago = format_time(time() - $user->lastaccess);
                $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->instance->pageid.'" title="'.$timeago.'">'.fullname($user).'</a>';
                $this->content->text .= '&nbsp;<a target="message_'.$user->id.'" href="'.$CFG->wwwroot.'/message/user.php?id='.$user->id.'" onclick="return openpopup(\'/message/user.php?id='.$user->id.'\', \'message_'.$user->id.'\', \'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500\', 0);"><img height="11" width="11" src="'.$CFG->pixpath.'/t/message.gif" alt="" />&nbsp;'.$user->count.'</a>';
                $this->content->text .= '</div>';
            }
        } else {
            $this->content->text .= "<center><font size=\"-1\">".get_string('nomessages', 'message')."</font></center>";
        }

        return $this->content;
    }
}

?>
