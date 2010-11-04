<?php

class block_messages extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_messages');
    }

    function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if (!$CFG->messaging) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabled', 'message');
            }
            return $this->content;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance) or !isloggedin() or isguestuser() or empty($CFG->messaging)) {
            return $this->content;
        }

        $link = '/message/index.php';
        $action = null; //this was using popup_action() but popping up a fullsize window seems wrong
        $this->content->footer = $OUTPUT->action_link($link, get_string('messages', 'message'), $action);

        $ufields = user_picture::fields('u', array('lastaccess'));
        $users = $DB->get_records_sql("SELECT $ufields, COUNT(m.useridfrom) AS count
                                         FROM {user} u, {message} m
                                        WHERE m.useridto = ? AND u.id = m.useridfrom AND m.notification = 0
                                     GROUP BY $ufields", array($USER->id));


        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            $this->content->text .= '<ul class="list">';
            foreach ($users as $user) {
                $timeago = format_time(time() - $user->lastaccess);
                $this->content->text .= '<li class="listentry"><div class="user"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'" title="'.$timeago.'">';
                $this->content->text .= $OUTPUT->user_picture($user, array('courseid'=>SITEID)); //TODO: user might not have capability to view frontpage profile :-(
                $this->content->text .= fullname($user).'</a></div>';

                $link = '/message/index.php?usergroup=unread&id='.$user->id;
                $anchortagcontents = '<img class="iconsmall" src="'.$OUTPUT->pix_url('t/message') . '" alt="" />&nbsp;'.$user->count;

                $action = null; // popup is gone now
                $anchortag = $OUTPUT->action_link($link, $anchortagcontents, $action);

                $this->content->text .= '<div class="message">'.$anchortag.'</div></li>';
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


