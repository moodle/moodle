<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * The main zoom configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/zoom/lib.php');
require_once($CFG->dirroot.'/mod/zoom/locallib.php');

/**
 * Module instance settings form
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_zoom_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $PAGE, $USER;
        $config = get_config('mod_zoom');
        $PAGE->requires->js_call_amd("mod_zoom/form", 'init');

        $isnew = empty($this->_cm);

        $service = new mod_zoom_webservice();
        $zoomuser = $service->get_user($USER->email);

        // If creating a new instance, but the Zoom user does not exist.
        if ($isnew && $zoomuser === false) {
            // Assume user is using Zoom for the first time.
            $errstring = 'zoomerr_usernotfound';
            // After they set up their account, the user should continue to the page they were on.
            $nexturl = $PAGE->url;
            zoom_fatal_error($errstring, 'mod_zoom', $nexturl, $config->zoomurl);
        }

        // Array of emails and proper names of Moodle users in this course that
        // can add Zoom meetings, and the user can schedule.
        $scheduleusers = [];

        $canschedule = false;
        if ($zoomuser !== false) {
            // Get the array of users they can schedule.
            $canschedule = $service->get_schedule_for_users($USER->email);
        }

        if (!empty($canschedule)) {
            // Add the current user.
            $canschedule[$zoomuser->id] = new stdClass();
            $canschedule[$zoomuser->id]->email = $USER->email;

            // If the activity exists and the current user is not the current host.
            if (!$isnew && $zoomuser->id !== $this->current->host_id) {
                // Get intersection of current host's schedulers and $USER's schedulers to prevent zoom errors.
                $currenthostschedulers = $service->get_schedule_for_users($this->current->host_id);
                if (!empty($currenthostschedulers)) {
                    // Since this is the second argument to array_intersect_key,
                    // the entry from $canschedule will be used, so we can just
                    // use true to avoid a service call.
                    $currenthostschedulers[$this->current->host_id] = true;
                }
                $canschedule = array_intersect_key($canschedule, $currenthostschedulers);
            }

            // Get list of users who can add Zoom activities in this context.
            $moodleusers = get_enrolled_users($this->context, 'mod/zoom:addinstance', 0, 'u.*', 'lastname');

            // Check each potential host to see if they are a valid host.
            foreach ($canschedule as $zoomuserinfo) {
                $zoomemail = strtolower($zoomuserinfo->email);
                if (isset($scheduleusers[$zoomemail])) {
                    continue;
                }
                if ($zoomemail === strtolower($USER->email)) {
                    $scheduleusers[$zoomemail] = get_string('scheduleforself', 'zoom');
                    continue;
                }
                foreach ($moodleusers as $muser) {
                    if ($zoomemail === strtolower($muser->email)) {
                        $scheduleusers[$zoomemail] = fullname($muser);
                        break;
                    }
                }
            }
        }

        $meetinginfo = new stdClass();
        if (!$isnew) {
            try {
                $meetinginfo = $service->get_meeting_webinar_info($this->current->meeting_id, $this->current->webinar);
            } catch (moodle_exception $error) {
                // If the meeting can't be found, offer to recreate the meeting on Zoom.
                if (zoom_is_meeting_gone_error($error)) {
                    $errstring = 'zoomerr_meetingnotfound';
                    $param = zoom_meetingnotfound_param($this->_cm->id);
                    $nexturl = "/mod/zoom/view.php?id=" . $this->_cm->id;
                    zoom_fatal_error($errstring, 'mod_zoom', $nexturl, $param, "meeting/get : $error");
                } else {
                    throw $error;
                }
            }
        }

        // If the current editing user has the host saved in the db for this meeting on their list
        // of people that they can schedule for, allow them to change the host, otherwise don't.
        $allowschedule = false;
        if (!$isnew) {
            try {
                $founduser = $service->get_user($meetinginfo->host_id);
                if ($founduser && array_key_exists($founduser->email, $scheduleusers)) {
                    $allowschedule = true;
                }
            } catch (moodle_exception $error) {
                // Don't need to throw an error, just leave allowschedule as false.
                $allowschedule = false;
            }
        } else {
            $allowschedule = true;
        }

        // Start of form definition.
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Add topic (stored in database as 'name').
        $mform->addElement('text', 'name', get_string('topic', 'zoom'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 300), 'maxlength', 300, 'client');

        // Add description ('intro' and 'introformat').
        $this->standard_intro_elements();

        // Add date/time. Validation in validation().
        $mform->addElement('date_time_selector', 'start_time', get_string('start_time', 'zoom'));
        // Disable for recurring meetings.
        $mform->disabledIf('start_time', 'recurring', 'checked');

        // Add duration.
        $mform->addElement('duration', 'duration', get_string('duration', 'zoom'), array('optional' => false));
        // Validation in validation(). Default to one hour.
        $mform->setDefault('duration', array('number' => 1, 'timeunit' => 3600));
        // Disable for recurring meetings.
        $mform->disabledIf('duration', 'recurring', 'checked');

        // Add recurring.
        $mform->addElement('advcheckbox', 'recurring', get_string('recurringmeeting', 'zoom'));
        $mform->setDefault('recurring', $config->defaultrecurring);
        $mform->addHelpButton('recurring', 'recurringmeeting', 'zoom');

        if ($isnew) {
            // Add webinar, disabled if the user cannot create webinars.
            $webinarattr = null;
            if (!$service->_get_user_settings($zoomuser->id)->feature->webinar) {
                $webinarattr = array('disabled' => true, 'group' => null);
            }
            $mform->addElement('advcheckbox', 'webinar', get_string('webinar', 'zoom'), '', $webinarattr);
            $mform->setDefault('webinar', 0);
            $mform->addHelpButton('webinar', 'webinar', 'zoom');
        } else if ($this->current->webinar) {
            $mform->addElement('html', get_string('webinar_already_true', 'zoom'));
        } else {
            $mform->addElement('html', get_string('webinar_already_false', 'zoom'));
        }

        // Deals with password manager issues.
        if (isset($this->current->password)) {
            $this->current->meetingcode = $this->current->password;
            unset($this->current->password);
        }
        // Add password.
        $mform->addElement('text', 'meetingcode', get_string('password', 'zoom'), array('maxlength' => '10'));
        $mform->setType('meetingcode', PARAM_TEXT);
        // Check password uses valid characters.
        $regex = '/^[a-zA-Z0-9@_*-]{1,10}$/';
        $mform->addRule('meetingcode', get_string('err_invalid_password', 'mod_zoom'), 'regex', $regex, 'client');
        $mform->setDefault('meetingcode', strval(rand(100000, 999999)));
        $mform->disabledIf('meetingcode', 'requirepasscode', 'notchecked');
        $mform->addElement('static', 'passwordrequirements', '', get_string('err_password', 'mod_zoom'));

        // Add password requirement prompt.
        $mform->addElement('advcheckbox', 'requirepasscode', get_string('requirepasscode', 'zoom'));

        if (isset($this->current->meetingcode) && strval($this->current->meetingcode) === "") {
            $mform->setDefault('requirepasscode', 0);
        } else {
            $mform->setDefault('requirepasscode', 1);
        }

        // Add host/participants video (checked by default).
        $mform->addGroup(array(
            $mform->createElement('radio', 'option_host_video', '', get_string('on', 'zoom'), true),
            $mform->createElement('radio', 'option_host_video', '', get_string('off', 'zoom'), false)
        ), null, get_string('option_host_video', 'zoom'));
        $mform->setDefault('option_host_video', $config->defaulthostvideo);
        $mform->disabledIf('option_host_video', 'webinar', 'checked');

        $mform->addGroup(array(
            $mform->createElement('radio', 'option_participants_video', '', get_string('on', 'zoom'), true),
            $mform->createElement('radio', 'option_participants_video', '', get_string('off', 'zoom'), false)
        ), null, get_string('option_participants_video', 'zoom'));
        $mform->setDefault('option_participants_video', $config->defaultparticipantsvideo);
        $mform->disabledIf('option_participants_video', 'webinar', 'checked');

        // Add audio options.
        $mform->addGroup(array(
            $mform->createElement('radio', 'option_audio', '', get_string('audio_telephony', 'zoom'), ZOOM_AUDIO_TELEPHONY),
            $mform->createElement('radio', 'option_audio', '', get_string('audio_voip', 'zoom'), ZOOM_AUDIO_VOIP),
            $mform->createElement('radio', 'option_audio', '', get_string('audio_both', 'zoom'), ZOOM_AUDIO_BOTH)
        ), null, get_string('option_audio', 'zoom'));
        $mform->setDefault('option_audio', $config->defaultaudiooption);

        $mform->addElement('advcheckbox', 'option_mute_upon_entry', get_string('option_mute_upon_entry', 'mod_zoom'));
        $mform->setDefault('option_mute_upon_entry', $config->defaultmuteuponentryoption);
        $mform->addHelpButton('option_mute_upon_entry', 'option_mute_upon_entry', 'mod_zoom');

        // Add meeting options. Make sure we pass $appendName as false
        // so the options aren't nested in a 'meetingoptions' array.
        $mform->addGroup(array(
            // Join before host.
            $mform->createElement('advcheckbox', 'option_jbh', '', get_string('option_jbh', 'zoom'))
        ), 'meetingoptions', get_string('meetingoptions', 'zoom'), null, false);
        $mform->setDefault('option_jbh', $config->defaultjoinbeforehost);

        $mform->addHelpButton('meetingoptions', 'meetingoptions', 'zoom');
        $mform->disabledIf('meetingoptions', 'webinar', 'checked');

        $mform->addElement('advcheckbox', 'option_waiting_room', get_string('option_waiting_room', 'mod_zoom'));
        $mform->setDefault('option_waiting_room', $config->defaultwaitingroomoption);

        $mform->addElement('advcheckbox', 'option_authenticated_users', get_string('option_authenticated_users', 'mod_zoom'));
        $mform->setDefault('option_authenticated_users', $config->defaultauthusersoption);

        // Add Schedule for if current user is able to.
        // Check if the size is greater than 1 because we add the editing/creating user by default.
        if (count($scheduleusers) > 1 && $allowschedule) {
            $mform->addElement('select', 'schedule_for', get_string('schedulefor', 'zoom'), $scheduleusers);
            $mform->setType('schedule_for', PARAM_EMAIL);
            if (!$isnew) {
                $mform->disabledIf('schedule_for', 'change_schedule_for');
                $mform->addElement('checkbox', 'change_schedule_for', get_string('changehost', 'zoom'));
                $mform->setDefault('schedule_for', strtolower($service->get_user($this->current->host_id)->email));
            } else {
                $mform->setDefault('schedule_for', strtolower($USER->email));
            }
        }

        // Add alternative hosts.
        $mform->addElement('text', 'alternative_hosts', get_string('alternative_hosts', 'zoom'), array('size' => '64'));
        $mform->setType('alternative_hosts', PARAM_TEXT);
        // Set the maximum field length to 255 because that's the limit on Zoom's end.
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('alternative_hosts', 'alternative_hosts', 'zoom');

        // Add meeting id.
        $mform->addElement('hidden', 'meeting_id', -1);
        $mform->setType('meeting_id', PARAM_ALPHANUMEXT);

        // Add host id (will error if user does not have an account on Zoom).
        $mform->addElement('hidden', 'host_id', zoom_get_user_id());
        $mform->setType('host_id', PARAM_ALPHANUMEXT);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();
        $mform->setDefault('grade', false);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * More validation on form data.
     * See documentation in lib/formslib.php.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $CFG, $USER;
        $errors = array();

        // Only check for scheduled meetings.
        if (empty($data['recurring'])) {
            // Make sure start date is in the future.
            if ($data['start_time'] < strtotime('today')) {
                $errors['start_time'] = get_string('err_start_time_past', 'zoom');
            }

            // Make sure duration is positive and no more than 150 hours.
            if ($data['duration'] <= 0) {
                $errors['duration'] = get_string('err_duration_nonpositive', 'zoom');
            } else if ($data['duration'] > 150 * 60 * 60) {
                $errors['duration'] = get_string('err_duration_too_long', 'zoom');
            }
        }

        require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');
        $service = new mod_zoom_webservice();

        if (!empty($data['requirepasscode']) && empty($data['meetingcode'])) {
            $errors['meetingcode'] = get_string('err_password_required', 'mod_zoom');
        }
        if (isset($data['schedule_for']) &&  $data['schedule_for'] !== $USER->email) {
            $scheduleusers = $service->get_schedule_for_users($USER->email);
            $scheduleok = false;
            foreach ($scheduleusers as $zuser) {
                if (strtolower($zuser->email) === strtolower($data['schedule_for'])) {
                    // Found a matching email address in the Zoom users list.
                    $scheduleok = true;
                    break;
                }
            }
            if (!$scheduleok) {
                $errors['schedule_for'] = get_string('invalidscheduleuser', 'mod_zoom');
            }
        }
        // Check if the listed alternative hosts are valid users on Zoom.
        $alternativehosts = explode(',', str_replace(';', ',', $data['alternative_hosts']));
        foreach ($alternativehosts as $alternativehost) {
            if (!($service->get_user($alternativehost))) {
                $errors['alternative_hosts'] = 'User ' . $alternativehost . ' was not found on Zoom.';
                break;
            }
        }

        return $errors;
    }
}

/**
 * Form to search for meeting reports.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_zoom_report_form extends moodleform {
    /**
     * Define form elements.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('date_selector', 'from', get_string('from'));

        $mform->addElement('date_selector', 'to', get_string('to'));

        $mform->addElement('submit', 'submit', get_string('go'));
    }
}
