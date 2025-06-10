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
 * iseek search lti launch class
 *
 * @package   block_iseek
 * @copyright iseek.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Main iseek search block class.
 *
 * @copyright iseek.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_iseek extends block_base {

    /**
     * {@inheritdoc}
     */
    public function init() {
        $this->title = get_string('iseek', 'block_iseek');
    }
    
    /**
     * {@inheritdoc}
     */
    public function specialization() {
        $allowHTML = get_config('iseek', 'Allow_HTML');
        
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_iseek');            
            } else {
                $this->title = $this->config->title;
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function get_content() {
        global $CFG, $USER, $COURSE;
        
        require_once($CFG->dirroot.'/message/lib.php');
        
        if ($this->content !== null) {
            return $this->content;
        }

        if (!isloggedin()) {
            return $this->content;
        }

        $catlimits = get_config('iseek', 'categorylimit');
        $catsettings = get_config('iseek', 'cats');
        $cats = explode(',', $catsettings);

        if ($catlimits == 1 && (empty($cats) || in_array($COURSE->category, $cats))) {
            $iscat = true;
        } else {
            return $this->content;
        }

        // Create empty content.
        $this->content = new stdClass();
        
        // Configure LTI params from Settings
        $key = get_config('iseek', 'LTI_KEY');
        $secret = get_config('iseek', 'LTI_SECRET');
        $moodle_url = new moodle_url('/');
        $launch_url = get_config('iseek', 'LTI_URL');
        $fullname = $USER->firstname . ' ' . $USER->lastname;
        $blockid = $this->context->instanceid;
        $user = $USER->id;
         
        if (!empty($COURSE)) {
            $coursecontext = context_course::instance($COURSE->id);
            
            if (is_enrolled($coursecontext, $USER->id, '', true)) {
                $courseid = $COURSE->id;
            } else {
                $courseid = 'n/a';
            }
         } else {
            $courseid = 'n/a';
         }
         
        // Create string of roles from module or block context. Grabs all roles for user
        $roles = array();
        
        if (empty($cmid)) {
            // If no cmid is passed, check if the user is a teacher in the course
            $context = context_block::instance($blockid);

            if (has_capability('moodle/course:viewhiddenactivities', $context, $user) && has_capability('moodle/course:viewhiddensections', $context, $user)) {
                array_push($roles, 'Instructor');
            } else {
                array_push($roles, 'Learner');
            }
        } else {
            $context = context_module::instance($cmid);

            if (has_capability('moodle/course:viewhiddenactivities', $context, $user) && has_capability('moodle/course:viewhiddensections', $context, $user)) {
                array_push($roles, 'Instructor');
            } else {
                array_push($roles, 'Learner');
            }
        }

        if (is_siteadmin($user) || has_capability('mod/lti:admin', $context)) {
            // Make sure admins do not have the Learner role, then set admin role.
            $roles = array();
            array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator', 'urn:lti:instrole:ims/lis/Administrator');
        }

        // Join role array into string
        $role = join(',', $roles);
        
        // Create LTI params array
        $launch_data = array(
            'roles' => $role,
            'lis_person_name_full' => $fullname,
            'lis_person_name_family' => $USER->lastname,
            'lis_person_name_given' => $USER->firstname,
            'lis_person_contact_email_primary' => $USER->email,
            'launch_presentation_return_url' => $moodle_url,
            'user_id' => $USER->id,
            'context_id' => $courseid,
            'custom_iseek_plugin_version' => '1.3'
        );

        $now = new DateTime();
        
        $launch_data["lti_version"] = "LTI-1p0";
        $launch_data["lti_message_type"] = "basic-lti-launch-request";
        
        # Basic LTI uses OAuth to sign requests
        # OAuth Core 1.0 spec: http://oauth.net/core/1.0/
        $launch_data["oauth_callback"] = "about:blank";
        $launch_data["oauth_consumer_key"] = $key;
        $launch_data["oauth_version"] = "1.0";
        $launch_data["oauth_nonce"] = uniqid(mt_rand(1, 1000));
        $launch_data["oauth_timestamp"] = $now->getTimestamp();
        $launch_data["oauth_signature_method"] = "HMAC-SHA1";
        
        # In OAuth, request parameters must be sorted by name
        ksort($launch_data);
        
        $launch_params = array();
        foreach($launch_data as $key => $value) {
            array_push($launch_params, $key . "=" . rawurlencode($value));
        }
        
        //Create oauth signature
        $base_string = "POST&" . urlencode($launch_url) . "&" . rawurlencode(implode("&", $launch_params));
        $secret = urlencode($secret) . "&";
        $signature = base64_encode(hash_hmac("sha1", $base_string, $secret, true));
        
        $launch_data["oauth_signature"] = $signature;
        
        ksort($launch_data);
        
        // Form display.
        $this->content->text = null;
        $this->content->text .= html_writer::start_tag('div', array('id' => 'iseek'));
            $this->content->text .= html_writer::tag('img', null, array('id' => 'iseek_brand', 'src' => '//iseek.ai/images/logos/logo.iseek-iseek.default.png'));
            $this->content->text .= html_writer::start_tag('form',array('id' => 'iseek_form', 'method' => 'post', 'target' => '_blank', 'action' => $launch_url));
                $this->content->text .= html_writer::tag('input', null, array('id' => 'iseek_text', 'name' => 'unsigned_q', 'type' => 'text'));
                foreach($launch_data as $key => $value) {
                    $this->content->text .= html_writer::tag('input', null, array('name' => $key, 'value' => $value, 'type' => 'hidden'));
                }
                $this->content->text .= html_writer::tag('input', '', array('id' => 'iseek_button', 'type' => 'submit', 'value' => '&#10132;'));
            $this->content->text .= html_writer::end_tag('form');
        $this->content->text .= html_writer::end_tag('div');
 
        return $this->content;
    }
    
    /**
     * {@inheritdoc}
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function instance_allow_config() {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    function has_config() {return true;}
}
?>
