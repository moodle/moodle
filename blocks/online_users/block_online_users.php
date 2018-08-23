<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Online users block.
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_online_users\fetcher;

/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_online_users extends block_base {
    function init() {
        $this->title = get_string('pluginname','block_online_users');
    }

    function has_config() {
        return true;
    }

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
        $now = time();

        //Calculate if we are in separate groups
        $isseparategroups = ($this->page->course->groupmode == SEPARATEGROUPS
                             && $this->page->course->groupmodeforce
                             && !has_capability('moodle/site:accessallgroups', $this->page->context));

        //Get the user current group
        $currentgroup = $isseparategroups ? groups_get_course_group($this->page->course) : NULL;

        $sitelevel = $this->page->course->id == SITEID || $this->page->context->contextlevel < CONTEXT_COURSE;

        $onlineusers = new fetcher($currentgroup, $now, $timetoshowusers, $this->page->context,
                $sitelevel, $this->page->course->id);

        //Calculate minutes
        $minutes  = floor($timetoshowusers/60);
        $periodminutes = get_string('periodnminutes', 'block_online_users', $minutes);

        // Count users.
        $usercount = $onlineusers->count_users();
        if ($usercount === 0) {
            $usercount = get_string('nouser', 'block_online_users');
        } else if ($usercount === 1) {
            $usercount = get_string('numuser', 'block_online_users', $usercount);
        } else {
            $usercount = get_string('numusers', 'block_online_users', $usercount);
        }

        $this->content->text = '<div class="info">'.$usercount.' ('.$periodminutes.')</div>';

        // Verify if we can see the list of users, if not just print number of users
        if (!has_capability('block/online_users:viewlist', $this->page->context)) {
            return $this->content;
        }

        $userlimit = 50; // We'll just take the most recent 50 maximum.
        if ($users = $onlineusers->get_users($userlimit)) {
            foreach ($users as $user) {
                $users[$user->id]->fullname = fullname($user);
            }
        } else {
            $users = array();
        }

        //Now, we have in users, the list of users to show
        //Because they are online
        if (!empty($users)) {
            $this->page->requires->js_call_amd('block_online_users/change_user_visibility', 'init');
            //Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link (existing lang string).
            //Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            if (isloggedin() && has_capability('moodle/site:sendmessage', $this->page->context)
                           && !empty($CFG->messaging) && !isguestuser()) {
                $canshowicon = true;
            } else {
                $canshowicon = false;
            }
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time($now - $user->lastaccess); //bruno to calculate correctly on frontpage

                if (isguestuser($user)) {
                    $this->content->text .= '<div class="user">'.$OUTPUT->user_picture($user, array('size'=>16, 'alttext'=>false));
                    $this->content->text .= get_string('guestuser').'</div>';

                } else { // Not a guest user.
                    $this->content->text .= '<div class="user">';
                    $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->page->course->id.'" title="'.$timeago.'">';
                    $this->content->text .= $OUTPUT->user_picture($user, array('size'=>16, 'alttext'=>false, 'link'=>false)) .$user->fullname.'</a></div>';

                    if ($USER->id == $user->id) {
                        $action = ($user->uservisibility != null && $user->uservisibility == 0) ? 'show' : 'hide';
                        $anchortagcontents = $OUTPUT->pix_icon('t/' . $action,
                            get_string('online_status:' . $action, 'block_online_users'));
                        $anchortag = html_writer::link("", $anchortagcontents,
                            array('title' => get_string('online_status:' . $action, 'block_online_users'),
                                'data-action' => $action, 'data-userid' => $user->id, 'id' => 'change-user-visibility'));

                        $this->content->text .= '<div class="uservisibility">' . $anchortag . '</div>';
                    } else {
                        if ($canshowicon) {  // Only when logged in and messaging active etc.
                            $anchortagcontents = $OUTPUT->pix_icon('t/message', get_string('messageselectadd'));
                            $anchorurl = new moodle_url('/message/index.php', array('id' => $user->id));
                            $anchortag = html_writer::link($anchorurl, $anchortagcontents,
                                array('title' => get_string('messageselectadd')));

                            $this->content->text .= '<div class="message">'.$anchortag.'</div>';
                        }
                    }
                }
                $this->content->text .= "</li>\n";
            }
            $this->content->text .= '</ul><div class="clearer"><!-- --></div>';
        }

        return $this->content;
    }
}


