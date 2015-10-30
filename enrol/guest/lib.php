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
 * Guest access plugin.
 *
 * This plugin does not add any entries into the user_enrolments table,
 * the access control is granted on the fly via the tricks in require_login().
 *
 * @package    enrol_guest
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_guest_plugin extends enrol_plugin {

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        foreach ($instances as $instance) {
            if ($instance->password !== '') {
                return array(new pix_icon('withpassword', get_string('guestaccess_withpassword', 'enrol_guest'), 'enrol_guest'));
            } else {
                return array(new pix_icon('withoutpassword', get_string('guestaccess_withoutpassword', 'enrol_guest'), 'enrol_guest'));
            }
        }
    }

    public function enrol_user(stdClass $instance, $userid, $roleid = null, $timestart = 0, $timeend = 0, $status = null, $recovergrades = null) {
        // no real enrolments here!
        return;
    }

    public function unenrol_user(stdClass $instance, $userid) {
        // nothing to do, we never enrol here!
        return;
    }

    /**
     * Attempt to automatically gain temporary guest access to course,
     * calling code has to make sure the plugin and instance are active.
     *
     * @param stdClass $instance course enrol instance
     * @return bool|int false means no guest access, integer means end of cached time
     */
    public function try_guestaccess(stdClass $instance) {
        global $USER, $CFG;

        $allow = false;

        if ($instance->password === '') {
            $allow = true;

        } else if (isset($USER->enrol_guest_passwords[$instance->id])) { // this is a hack, ideally we should not add stuff to $USER...
            if ($USER->enrol_guest_passwords[$instance->id] === $instance->password) {
                $allow = true;
            }
        }

        if ($allow) {
            // Temporarily assign them some guest role for this context
            $context = context_course::instance($instance->courseid);
            load_temp_course_role($context, $CFG->guestroleid);
            return ENROL_MAX_TIMESTAMP;
        }

        return false;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/guest:config', $context)) {
            return NULL;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'guest'))) {
            return NULL;
        }

        return new moodle_url('/enrol/guest/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $SESSION, $USER;

        if ($instance->password === '') {
            return null;
        }

        if (isset($USER->enrol['tempguest'][$instance->courseid]) and $USER->enrol['tempguest'][$instance->courseid] > time()) {
            // no need to show the guest access when user can already enter course as guest
            return null;
        }

        require_once("$CFG->dirroot/enrol/guest/locallib.php");
        $form = new enrol_guest_enrol_form(NULL, $instance);
        $instanceid = optional_param('instance', 0, PARAM_INT);

        if ($instance->id == $instanceid) {
            if ($data = $form->get_data()) {
                // add guest role
                $context = context_course::instance($instance->courseid);
                $USER->enrol_guest_passwords[$instance->id] = $data->guestpassword; // this is a hack, ideally we should not add stuff to $USER...
                if (isset($USER->enrol['tempguest'][$instance->courseid])) {
                    remove_temp_course_roles($context);
                }
                load_temp_course_role($context, $CFG->guestroleid);
                $USER->enrol['tempguest'][$instance->courseid] = ENROL_MAX_TIMESTAMP;

                // go to the originally requested page
                if (!empty($SESSION->wantsurl)) {
                    $destination = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                } else {
                    $destination = "$CFG->wwwroot/course/view.php?id=$instance->courseid";
                }
                redirect($destination);
            }
        }

        ob_start();
        $form->display();
        $output = ob_get_clean();

        return $OUTPUT->box($output, 'generalbox');
    }

    /**
     * Adds enrol instance UI to course edit form
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param object $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return void
     */
    public function course_edit_form($instance, MoodleQuickForm $mform, $data, $context) {

        $i = isset($instance->id) ? $instance->id : 0;

        if (!$i and !$this->get_config('defaultenrol')) {
            return;
        }

        $header = $this->get_instance_name($instance);
        if (!$i) {
            $config = guess_if_creator_will_have_course_capability('enrol/guest:config', $context);
        } else {
            $config = has_capability('enrol/guest:config', $context);
        }

        $mform->addElement('header', 'enrol_guest_header_'.$i, $header);


        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'enrol_guest_status_'.$i, get_string('status', 'enrol_guest'), $options);
        $mform->addHelpButton('enrol_guest_status_'.$i, 'status', 'enrol_guest');
        $mform->setDefault('enrol_guest_status_'.$i, $this->get_config('status'));
        $mform->setAdvanced('enrol_guest_status_'.$i, $this->get_config('status_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_guest_status_'.$i);
            if (!$i) {
                $mform->setConstant('enrol_guest_status_'.$i, $this->get_config('status'));
            } else {
                $mform->setConstant('enrol_guest_status_'.$i, $instance->status);
            }
        }

        $mform->addElement('passwordunmask', 'enrol_guest_password_'.$i, get_string('password', 'enrol_guest'));
        $mform->addHelpButton('enrol_guest_password_'.$i, 'password', 'enrol_guest');
        if (!$config) {
            $mform->hardFreeze('enrol_guest_password_'.$i);
            if (!$i) {
                if ($this->get_config('requirepassword')) {
                    $password = generate_password(20);
                } else {
                    $password = '';
                }
                $mform->setConstant('enrol_guest_password_'.$i, $password);
            } else {
                $mform->setConstant('enrol_guest_password_'.$i, $instance->password);
            }
        } else {
            $mform->disabledIf('enrol_guest_password_'.$i, 'enrol_guest_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        // now add all values from enrol table
        if ($instance) {
            foreach($instance as $key=>$val) {
                $data->{'enrol_guest_'.$key.'_'.$i} = $val;
            }
        }
    }

    /**
     * Validates course edit form data
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param array $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return array errors array
     */
    public function course_edit_validation($instance, array $data, $context) {
        $errors = array();

        if (!has_capability('enrol/guest:config', $context)) {
            // we are going to ignore the data later anyway, they would nto be able to fix the form anyway
            return $errors;
        }

        $i = isset($instance->id) ? $instance->id : 0;

        if (!isset($data['enrol_guest_status_'.$i])) {
            return $errors;
        }

        $password = empty($data['enrol_guest_password_'.$i]) ? '' : $data['enrol_guest_password_'.$i];
        $checkpassword = false;

        if ($instance) {
            if ($data['enrol_guest_status_'.$i] == ENROL_INSTANCE_ENABLED) {
                if ($instance->password !== $password) {
                    $checkpassword = true;
                }
            }
        } else {
            if ($data['enrol_guest_status_'.$i] == ENROL_INSTANCE_ENABLED) {
                $checkpassword = true;
            }
        }

        if ($checkpassword) {
            $require = $this->get_config('requirepassword');
            $policy  = $this->get_config('usepasswordpolicy');
            if ($require and empty($password)) {
                $errors['enrol_guest_password_'.$i] = get_string('required');
            } else if ($policy) {
                $errmsg = '';//prevent eclipse warning
                if (!check_password_policy($password, $errmsg)) {
                    $errors['enrol_guest_password_'.$i] = $errmsg;
                }
            }
        }

        return $errors;
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param object $course
     * @param object $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        global $DB;

        if ($inserted) {
            if (isset($data->enrol_guest_status_0)) {
                $fields = array('status'=>$data->enrol_guest_status_0);
                if ($fields['status'] == ENROL_INSTANCE_ENABLED) {
                    $fields['password'] = $data->enrol_guest_password_0;
                } else {
                    if ($this->get_config('requirepassword')) {
                        $fields['password'] = generate_password(20);
                    }
                }
                $this->add_instance($course, $fields);
            } else {
                if ($this->get_config('defaultenrol')) {
                    $this->add_default_instance($course);
                }
            }

        } else {
            $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'guest'));
            foreach ($instances as $instance) {
                $i = $instance->id;

                if (isset($data->{'enrol_guest_status_'.$i})) {
                    $reset = ($instance->status != $data->{'enrol_guest_status_'.$i});

                    $instance->status       = $data->{'enrol_guest_status_'.$i};
                    $instance->timemodified = time();
                    if ($instance->status == ENROL_INSTANCE_ENABLED) {
                        if ($instance->password !== $data->{'enrol_guest_password_'.$i}) {
                            $reset = true;
                        }
                        $instance->password = $data->{'enrol_guest_password_'.$i};
                    }
                    $DB->update_record('enrol', $instance);

                    if ($reset) {
                        $context = context_course::instance($course->id);
                        $context->mark_dirty();
                    }
                }
            }
        }
    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = NULL) {
        $fields = (array)$fields;

        if (!isset($fields['password'])) {
            $fields['password'] = '';
        }

        return parent::add_instance($course, $fields);
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = array('status'=>$this->get_config('status'));

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }

        return $this->add_instance($course, $fields);
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;

        if (!$DB->record_exists('enrol', array('courseid' => $data->courseid, 'enrol' => $this->get_name()))) {
            $this->add_instance($course, (array)$data);
        }

        // No need to set mapping, we do not restore users or roles here.
        $step->set_mapping('enrol', $oldid, 0);
    }
}
