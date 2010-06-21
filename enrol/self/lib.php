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
 * Self enrolment plugin.
 *
 * @package   enrol_self
 * @copyright 2010 Petr Skoda  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Self enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_self_plugin extends enrol_plugin {

    /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = ' (' . role_get_name($role, get_context_instance(CONTEXT_COURSE, $instance->courseid)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    public function roles_protected() {
        // users may tweak the roles later
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // users with unenrol cap may unenrol other users manually manually
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // users with manage cap may tweak period and status
        return true;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_candidate_link($courseid) {
        if (!has_capability('moodle/course:enrolconfig', get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST))) {
            return NULL;
        }
        // multiple instances supported - different roles with different password
        return new moodle_url('/enrol/self/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $SESSION, $USER, $DB;

        if (isguestuser()) {
            // can not enrol guest!!
            return null;
        }
        if ($DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id))) {
            //TODO: maybe we should tell them they are already enrolled, but can not access the course
            return null;
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate < time) {
            //TODO: inform that we can not enrol yet
            return null;
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate > time) {
            //TODO: inform that enrolment is not possible any more
            return null;
        }

        require_once("$CFG->dirroot/enrol/self/locallib.php");
        $form = new enrol_self_enrol_form(NULL, $instance);
        $instanceid = optional_param('instance', 0, PARAM_INT);

        if ($instance->id == $instanceid) {
            if ($data = $form->get_data()) {
                $enrol = enrol_get_plugin('self');
                if ($instance->enrolperiod) {
                    $timestart = time();
                    $tineend   = $timestart + $instance->enrolperiod;
                } else {
                    $timestart = 0;
                    $tineend   = 0;
                }

                $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $tineend);
                add_to_log($instance->courseid, 'course', 'enrol', '../enrol/users.php?id='.$instance->courseid, $instance->courseid); //there should be userid somewhere!
                // send welcome
                if ($this->get_config('sendcoursewelcomemessage')) {
                    $this->email_welcome_message($instance, $USER);
                }
            }
        }

        ob_start();
        $form->display();
        $output = ob_get_clean();

        return $OUTPUT->box($output);
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
        $plugin = enrol_get_plugin('self');
        $header = $plugin->get_instance_name($instance);
        $config = has_capability('enrol/self:config', $context);

        $mform->addElement('header', 'enrol_self_header_'.$i, $header);


        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'enrol_self_status_'.$i, get_string('status', 'enrol_self'), $options);
        $mform->setDefault('enrol_self_status_'.$i, $this->get_config('status'));
        $mform->setAdvanced('enrol_self_status_'.$i, $this->get_config('status_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_self_status_'.$i);
        }


        $mform->addElement('passwordunmask', 'enrol_self_password_'.$i, get_string('password', 'enrol_self'));
        if (!$config) {
            $mform->hardFreeze('enrol_self_password_'.$i);
        } else {
            $mform->disabledIf('enrol_self_password_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $options = array(1 => get_string('yes'),
                         0 => get_string('no'));
        $mform->addElement('select', 'enrol_self_customint1_'.$i, get_string('groupkey', 'enrol_self'), $options);
        $mform->setDefault('enrol_self_customint1_'.$i, $this->get_config('groupkey'));
        $mform->setAdvanced('enrol_self_customint1_'.$i, $this->get_config('groupkey_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_self_customint1_'.$i);
        } else {
            $mform->disabledIf('enrol_self_customint1_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        if ($instance) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $this->get_config('roleid'));
        }
        $mform->addElement('select', 'enrol_self_roleid_'.$i, get_string('role', 'enrol_self'), $roles);
        $mform->setDefault('enrol_self_roleid_'.$i, $this->get_config('roleid'));
        $mform->setAdvanced('enrol_self_roleid_'.$i, $this->get_config('roleid_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_self_roleid_'.$i);
        } else {
            $mform->disabledIf('enrol_self_roleid_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('duration', 'enrol_self_enrolperiod_'.$i, get_string('enrolperiod', 'enrol_self'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrol_self_enrolperiod_'.$i, $this->get_config('enrolperiod'));
        $mform->setAdvanced('enrol_self_enrolperiod_'.$i, $this->get_config('enrolperiod_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_self_enrolperiod_'.$i);
        } else {
            $mform->disabledIf('enrol_self_enrolperiod_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('date_selector', 'enrol_self_enrolstartdate_'.$i, get_string('enrolstartdate', 'enrol_self'), array('optional' => true));
        $mform->setDefault('enrol_self_enrolstartdate_'.$i, 0);
        $mform->setAdvanced('enrol_self_enrolstartdate_'.$i, 1);
        if (!$config) {
            $mform->hardFreeze('enrol_self_enrolstartdate_'.$i);
        } else {
            $mform->disabledIf('enrol_self_enrolstartdate_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('date_selector', 'enrol_self_enrolenddate_'.$i, get_string('enrolenddate', 'enrol_self'), array('optional' => true));
        $mform->setDefault('enrol_self_enrolenddate_'.$i, 0);
        $mform->setAdvanced('enrol_self_enrolenddate_'.$i, 1);
        if (!$config) {
            $mform->hardFreeze('enrol_self_enrolenddate_'.$i);
        } else {
            $mform->disabledIf('enrol_self_enrolenddate_'.$i, 'enrol_self_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        // now add all values from enrol table
        if ($instance) {
            foreach($instance as $key=>$val) {
                $data->{'enrol_self_'.$key.'_'.$i} = $val;
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

        if (!has_capability('enrol/self:config', $context)) {
            // we are going to ignore the data later anyway, they would not be able to fix the form anyway
            return $errors;
        }

        $i = isset($instance->id) ? $instance->id : 0;

        $password = empty($data['enrol_self_password_'.$i]) ? '' : $data['enrol_self_password_'.$i];
        $checkpassword = false;

        if ($instance) {
            if ($data['enrol_self_status_'.$i] == ENROL_INSTANCE_ENABLED) {
                if ($instance->password !== $password) {
                    $checkpassword = true;
                }
            }
        } else {
            if ($data['enrol_self_status_'.$i] == ENROL_INSTANCE_ENABLED) {
                $checkpassword = true;
            }
        }

        if ($checkpassword) {
            $require = $this->get_config('requirepassword');
            $policy  = $this->get_config('usepasswordpolicy');
            if ($require and empty($password)) {
                $errors['enrol_self_password_'.$i] = get_string('required');
            } else if ($policy) {
                $errmsg = '';//prevent eclipse warning
                if (!check_password_policy($password, $errmsg)) {
                    $errors['enrol_self_password_'.$i] = $errmsg;
                }
            }
        }

        if ($data['enrol_self_status_'.$i] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrol_self_enrolenddate_'.$i]) and $data['enrol_self_enrolenddate_'.$i] < $data['enrol_self_enrolstartdate_'.$i]) {
                $errors['enrol_self_enrolenddate_'.$i] = get_string('enrolenddaterror', 'enrol_self');
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

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if (has_capability('enrol/self:config', $context)) {
            if ($inserted) {
                if (isset($data->enrol_self_status_0)) {
                    $fields = array('status'=>$data->enrol_self_status_0);
                    if ($fields['status'] == ENROL_INSTANCE_ENABLED) {
                        $fields['password']       = $data->enrol_self_password_0;
                        $fields['customint1']     = $data->enrol_self_customint1_0;
                        $fields['roleid']         = $data->enrol_self_roleid_0;
                        $fields['enrolperiod']    = $data->enrol_self_enrolperiod_0;
                        $fields['enrolstartdate'] = $data->enrol_self_enrolstartdate_0;
                        $fields['enrolenddate']   = $data->enrol_self_enrolenddate_0;
                    } else {
                        if ($this->get_config('requirepassword')) {
                            // make sure some password is set after enabling this plugin
                            $fields['password']   = generate_password(20);
                        }
                        $fields['customint1']     = $this->get_config('groupkey');
                        $fields['roleid']         = $this->get_config('roleid');
                        $fields['enrolperiod']    = $this->get_config('enrolperiod');
                        $fields['enrolstartdate'] = 0;
                        $fields['enrolenddate']   = 0;
                    }
                    $this->add_instance($course, $fields);
                }

            } else {
                $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'self'));
                foreach ($instances as $instance) {
                    $i = $instance->id;

                    if (isset($data->{'enrol_self_status_'.$i})) {
                        $instance->status       = $data->{'enrol_self_status_'.$i};
                        $instance->timemodified = time();
                        if ($instance->status == ENROL_INSTANCE_ENABLED) {
                            $instance->password       = $data->{'enrol_self_password_'.$i};
                            $instance->customint1     = $data->{'enrol_self_customint1_'.$i};
                            $instance->roleid         = $data->{'enrol_self_roleid_'.$i};
                            $instance->enrolperiod    = $data->{'enrol_self_enrolperiod_'.$i};
                            $instance->enrolstartdate = $data->{'enrol_self_enrolstartdate_'.$i};
                            $instance->enrolenddate   = $data->{'enrol_self_enrolenddate_'.$i};
                        }
                        $DB->update_record('enrol', $instance);
                    }
                }
            }

        } else {
            if ($inserted) {
                if ($this->get_config('defaultenrol')) {
                    $this->add_default_instance($course);
                }
            } else {
                // bad luck, user can not change anything
            }
        }
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        global $DB;

        $exists = $DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'self'));

        $fields = array('customint1'=>$this->get_config('groupkey'), 'enrolperiod'=>$this->get_config('enrolperiod', 0), 'roleid'=>$this->get_config('roleid', 0));

        $fields['status'] = $exists ? ENROL_INSTANCE_DISABLED : $this->get_config('status');

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }

        return $this->add_instance($course, $fields);
    }

    /**
     * Send welcome email to specified user
     *
     * @param object $instance
     * @param object $user user record
     * @return void
     */
    protected function email_welcome_message($instance, $user) {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);

        if (!empty($instance->customtext1)) {
            //note: there is no gui for this yet, do we really need it?
            $message = formaat_string($instance->customtext1);
        } else {
            $a = new object();
            $a->coursename = format_string($course->fullname);
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";
            $message = get_string("welcometocoursetext", 'enrol_self', $a);
        }

        $subject = get_string('welcometocourse', 'enrol_self', format_string($course->fullname));

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $rusers = null;
        if (!empty($CFG->coursecontact)) {
            $croles = explode(',', $CFG->coursecontact);
            $rusers = get_role_users($croles, $context, true, '', 'r.sortorder ASC, u.lastname ASC');
        }
        if ($rusers) {
            $contact = reset($rusers);
        } else {
            $contact = get_admin();
        }

        email_to_user($user, $contact, $subject, $message);
    }
}


