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
 * Displays the user's profile information.
 *
 * @package    blocks
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Displays the user's profile information.
 *
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myprofile extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title   = get_string('pluginname', 'block_myprofile');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin()){
            return '';      // Never useful unless you are logged in
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $course = $this->page->course;
        if ($PAGE->context->contextlevel == CONTEXT_USER) {
            $user = $DB->get_record('user', array('id' => $PAGE->context->instanceid));
        } else {
            $user = $USER;
        }

        if ($course->id == SITEID) {
            $coursecontext = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
            // Make sure they can view the course
            if (!is_viewing($coursecontext)) {
                return '';
            }
        }


        // TODO: clean up the following even more

        if (!isset($this->config->display_picture) || $this->config->display_picture == 1) {
            $this->content->text .= '<div class="myprofileitem picture">';
            $this->content->text .= $OUTPUT->user_picture($user, array('courseid'=>$course->id, 'size'=>'100', 'class'=>'profilepicture'));  // The new class makes CSS easier
            $this->content->text .= '</div>';
        }

        $this->content->text .= '<div class="myprofileitem fullname">'.fullname($user).'</div>';

        if(!isset($this->config->display_country) || $this->config->display_country == 1) {
            $countries = get_string_manager()->get_list_of_countries();
            if (isset($countries[$user->country])) {
                $this->content->text .= '<div class="myprofileitem country">';
                $this->content->text .= get_string('country') . ': ' . $countries[$user->country];
                $this->content->text .= '</div>';
            }
        }

        if(!isset($this->config->display_city) || $this->config->display_city == 1) {
            $this->content->text .= '<div class="myprofileitem city">';
            $this->content->text .= get_string('city') . ': ' . $user->city;
            $this->content->text .= '</div>';
        }

        if(!isset($this->config->display_email) || $this->config->display_email == 1) {
            $this->content->text .= '<div class="myprofileitem email">';
            $this->content->text .= obfuscate_mailto($user->email, '', $user->emailstop);
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_icq) && !empty($user->icq)) {
            $this->content->text .= '<div class="myprofileitem icq">';
            $this->content->text .= 'ICQ: ' . $user->icq;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_skype) && !empty($user->skype)) {
            $this->content->text .= '<div class="myprofileitem skype">';
            $this->content->text .= 'Skype: ' . $user->skype;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_yahoo) && !empty($user->yahoo)) {
            $this->content->text .= '<div class="myprofileitem yahoo">';
            $this->content->text .= 'Yahoo: ' . $user->yahoo;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_aim) && !empty($user->aim)) {
            $this->content->text .= '<div class="myprofileitem aim">';
            $this->content->text .= 'AIM: ' . $user->aim;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_msn) && !empty($user->msn)) {
            $this->content->text .= '<div class="myprofileitem msn">';
            $this->content->text .= 'MSN: ' . $user->msn;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_phone1) && !empty($user->phone1)) {
            $this->content->text .= '<div class="myprofileitem phone1">';
            $this->content->text .= get_string('phone').': ' . $user->phone1;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_phone2) && !empty($user->phone2)) {
            $this->content->text .= '<div class="myprofileitem phone2">';
            $this->content->text .= get_string('phone').': ' . $user->phone2;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_institution) && !empty($user->institution)) {
            $this->content->text .= '<div class="myprofileitem institution">';
            $this->content->text .= $user->institution;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_address) && !empty($user->address)) {
            $this->content->text .= '<div class="myprofileitem address">';
            $this->content->text .= $user->address;
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_firstaccess) && !empty($user->firstaccess)) {
            $this->content->text .= '<div class="myprofileitem firstaccess">';
            $this->content->text .= get_string('firstaccess').': ' . userdate($user->firstaccess);
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_lastaccess) && !empty($user->lastaccess)) {
            $this->content->text .= '<div class="myprofileitem lastaccess">';
            $this->content->text .= get_string('lastaccess').': ' . userdate($user->lastaccess);
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_currentlogin) && !empty($user->currentlogin)) {
            $this->content->text .= '<div class="myprofileitem currentlogin">';
            $this->content->text .= get_string('login').': ' . userdate($user->currentlogin);
            $this->content->text .= '</div>';
        }

        if(!empty($this->config->display_lastip) && !empty($user->lastip)) {
            $this->content->text .= '<div class="myprofileitem lastip">';
            $this->content->text .= 'IP: ' . $user->lastip;
            $this->content->text .= '</div>';
        }

        $editscript = NULL;
        if (isguestuser($user)) {
            // guest account can not be edited

        } else if (is_mnet_remote_user($user)) {
            // cannot edit remote users

        } else if (isguestuser() or !isloggedin()) {
            // guests and not logged in can not edit own profile

        } else if ($USER->id == $user->id) {
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            if (has_capability('moodle/user:update', $systemcontext)) {
                $editscript = '/user/editadvanced.php';
            } else if (has_capability('moodle/user:editownprofile', $systemcontext)) {
                $editscript = '/user/edit.php';
            }

        } else {
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            $personalcontext = get_context_instance(CONTEXT_USER, $user->id);
            if (has_capability('moodle/user:update', $systemcontext) and !is_primary_admin($user->id)){
                $editscript = '/user/editadvanced.php';
            } else if (has_capability('moodle/user:editprofile', $personalcontext) and !is_primary_admin($user->id)){
                //teachers, parents, etc.
                $editscript = '/user/edit.php';
            }
        }


        if ($editscript) {
            $this->content->text .= '<div class="myprofileitem edit">';
            $this->content->text .= '<a href="'.$CFG->wwwroot.$editscript.'?id='.$user->id.'&amp;course='.$course->id.'">'.get_string('editmyprofile').'</a>';
            $this->content->text .= '</div>';
        }


        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
    }

    /**
     * allow instances to have their own configuration
     *
     * @return boolean
     */
    function instance_allow_config() {
        //allow instances to have their own configuration
        return false;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
    }

    /**
     * displays instance configuration form
     *
     * @return boolean
     */
    function instance_config_print() {
        if (!$this->instance_allow_config()) {
            return false;
        }

        global $CFG;

        $form = new block_myprofile.phpConfigForm(null, array($this->config));
        $form->display();

        return true;
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all'=>true);
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
    }

}
?>
