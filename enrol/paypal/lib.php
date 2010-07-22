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
 * Paypal enrolment plugin.
 *
 * This plugin allows you to set up paid courses.
 *
 * @package   enrol_paypal
 * @copyright 2010 Eugene Venter
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Paypal enrolment plugin implementation.
 * @author  Eugene Venter - based on code by Martin Dougiamas and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class enrol_paypal_plugin extends enrol_plugin {

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        global $DB;

        $exists = $DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'paypal'));

        $fields = array('enrolperiod'=>$this->get_config('enrolperiod', 0),
                        'roleid'=>$this->get_config('roleid', 0),
                        'cost'=>$this->get_config('cost', 0),
                        'currency'=>$this->get_config('currency', 0)
                       );

        $fields['status'] = $exists ? ENROL_INSTANCE_DISABLED : $this->get_config('status');

        return $this->add_instance($course, $fields);
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
        return new moodle_url('/enrol/paypal/addinstance.php', array('sesskey'=>sesskey(), 'id'=>$courseid));
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
        $plugin = enrol_get_plugin('paypal');
        $header = $plugin->get_instance_name($instance);
        $config = has_capability('moodle/course:enrolconfig', $context);

        $mform->addElement('header', 'enrol_paypal_header_'.$i, $header);


        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'enrol_paypal_status_'.$i, get_string('status', 'enrol_paypal'), $options);
        $mform->setDefault('enrol_paypal_status_'.$i, $this->get_config('status'));
        $mform->setAdvanced('enrol_paypal_status_'.$i, $this->get_config('status_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_status_'.$i);
        }

        $mform->addElement('text', 'enrol_paypal_cost_'.$i, get_string('cost', 'enrol_paypal'), array('size'=>4));
        $mform->setDefault('enrol_paypal_cost_'.$i, $this->get_config('cost'));
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_cost_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_cost_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }

        $paypalcurrencies = array(  'USD' => 'US Dollars',
                                    'EUR' => 'Euros',
                                    'JPY' => 'Japanese Yen',
                                    'GBP' => 'British Pounds',
                                    'CAD' => 'Canadian Dollars',
                                    'AUD' => 'Australian Dollars'
                                    );
        $mform->addElement('select', 'enrol_paypal_currency_'.$i, get_string('currency', 'enrol_paypal'), $paypalcurrencies);
        $mform->setDefault('enrol_paypal_currency_'.$i, $this->get_config('currency'));
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_currency_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_currency_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        if ($instance) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $this->get_config('roleid'));
        }
        $mform->addElement('select', 'enrol_paypal_roleid_'.$i, get_string('assignrole', 'enrol_paypal'), $roles);
        $mform->setDefault('enrol_paypal_roleid_'.$i, $this->get_config('roleid'));
        $mform->setAdvanced('enrol_paypal_roleid_'.$i, $this->get_config('roleid_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_roleid_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_roleid_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('duration', 'enrol_paypal_enrolperiod_'.$i, get_string('enrolperiod', 'enrol_paypal'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrol_paypal_enrolperiod_'.$i, $this->get_config('enrolperiod'));
        $mform->setAdvanced('enrol_paypal_enrolperiod_'.$i, $this->get_config('enrolperiod_adv'));
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_enrolperiod_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_enrolperiod_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('date_selector', 'enrol_paypal_enrolstartdate_'.$i, get_string('enrolstartdate', 'enrol_paypal'), array('optional' => true));
        $mform->setDefault('enrol_paypal_enrolstartdate_'.$i, 0);
        $mform->setAdvanced('enrol_paypal_enrolstartdate_'.$i, 1);
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_enrolstartdate_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_enrolstartdate_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        $mform->addElement('date_selector', 'enrol_paypal_enrolenddate_'.$i, get_string('enrolenddate', 'enrol_paypal'), array('optional' => true));
        $mform->setDefault('enrol_paypal_enrolenddate_'.$i, 0);
        $mform->setAdvanced('enrol_paypal_enrolenddate_'.$i, 1);
        if (!$config) {
            $mform->hardFreeze('enrol_paypal_enrolenddate_'.$i);
        } else {
            $mform->disabledIf('enrol_paypal_enrolenddate_'.$i, 'enrol_paypal_status_'.$i, 'noteq', ENROL_INSTANCE_ENABLED);
        }


        // now add all values from enrol table
        if ($instance) {
            foreach($instance as $key=>$val) {
                $data->{'enrol_paypal_'.$key.'_'.$i} = $val;
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

        if (!has_capability('moodle/course:enrolconfig', $context)) {
            // we are going to ignore the data later anyway, they would not be able to fix the form anyway
            return $errors;
        }

        $i = isset($instance->id) ? $instance->id : 0;

        if ($data['enrol_paypal_status_'.$i] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrol_paypal_enrolenddate_'.$i]) and $data['enrol_paypal_enrolenddate_'.$i] < $data['enrol_paypal_enrolstartdate_'.$i]) {
                $errors['enrol_paypal_enrolenddate_'.$i] = get_string('enrolenddaterror', 'enrol_paypal');
            }

            if (!is_numeric($data['enrol_paypal_cost_'.$i])) {
                $errors['enrol_paypal_cost_'.$i] = get_string('costerror', 'enrol_paypal');

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

        if (has_capability('moodle/course:enrolconfig', $context)) {
            if ($inserted) {
                if (isset($data->enrol_paypal_status_0)) {
                    $fields = array('status'=>$data->enrol_paypal_status_0);
                    if ($fields['status'] == ENROL_INSTANCE_ENABLED) {
                        $fields['cost']           = $data->enrol_paypal_cost_0;
                        $fields['currency']       = $data->enrol_paypal_currency_0;
                        $fields['roleid']         = $data->enrol_paypal_roleid_0;
                        $fields['enrolperiod']    = $data->enrol_paypal_enrolperiod_0;
                        $fields['enrolstartdate'] = $data->enrol_paypal_enrolstartdate_0;
                        $fields['enrolenddate']   = $data->enrol_paypal_enrolenddate_0;
                    } else {
                        $fields['roleid']         = $this->get_config('roleid');
                        $fields['cost']           = $this->get_config('cost');
                        $fields['currency']       = $this->get_config('currency');
                        $fields['enrolperiod']    = $this->get_config('enrolperiod');
                        $fields['enrolstartdate'] = 0;
                        $fields['enrolenddate']   = 0;
                    }
                    $this->add_instance($course, $fields);
                }

            } else {
                $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'paypal'));
                foreach ($instances as $instance) {
                    $i = $instance->id;

                    if (isset($data->{'enrol_paypal_status_'.$i})) {
                        $instance->status       = $data->{'enrol_paypal_status_'.$i};
                        $instance->timemodified = time();
                        if ($instance->status == ENROL_INSTANCE_ENABLED) {
                            $instance->roleid         = $data->{'enrol_paypal_roleid_'.$i};
                            $instance->cost           = $data->{'enrol_paypal_cost_'.$i};
                            $instance->currency       = $data->{'enrol_paypal_currency_'.$i};
                            $instance->enrolperiod    = $data->{'enrol_paypal_enrolperiod_'.$i};
                            $instance->enrolstartdate = $data->{'enrol_paypal_enrolstartdate_'.$i};
                            $instance->enrolenddate   = $data->{'enrol_paypal_enrolenddate_'.$i};
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
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    function enrol_page_hook(stdClass $instance) {
        global $CFG, $USER, $OUTPUT, $PAGE, $DB;

        ob_start();

        if ($DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id))) {
            return ob_get_clean();
        }

        if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
            return ob_get_clean();
        }

        if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
            return ob_get_clean();
        }

        $course = $DB->get_record('course', array('id'=>$instance->courseid));

        $strloginto = get_string("loginto", "", $course->shortname);
        $strcourses = get_string("courses");

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                             '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }

        if ( (float) $instance->cost <= 0 ) {
            $cost = (float) $this->get_config('cost');
        } else {
            $cost = (float) $instance->cost;
        }

        if (abs($cost) < 0.01) { // no cost, other enrolment methods (instances) should be used
            echo '<p>'.get_string('nocost', 'enrol_paypal').'</p>';
        } else {

            if ($USER->username == 'guest') { // force login only for guest user, not real users with guest role
                if (empty($CFG->loginhttps)) {
                    $wwwroot = $CFG->wwwroot;
                } else {
                    // This actually is not so secure ;-), 'cause we're
                    // in unencrypted connection...
                    $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
                }
                echo '<div class="mdl-align"><p>'.get_string('paymentrequired').'</p>';
                echo '<p><b>'.get_string('cost').": $CFG->enrol_currency $cost".'</b></p>';
                echo '<p><a href="'.$wwwroot.'/login/">'.get_string('loginsite').'</a></p>';
                echo '</div>';
            } else {
                //Sanitise some fields before building the PayPal form
                $coursefullname  = $course->fullname;
                $courseshortname = $course->shortname;
                $userfullname    = fullname($USER);
                $userfirstname   = $USER->firstname;
                $userlastname    = $USER->lastname;
                $useraddress     = $USER->address;
                $usercity        = $USER->city;

                include($CFG->dirroot.'/enrol/paypal/enrol.html');
            }

        }

        return $OUTPUT->box(ob_get_clean());
    } // enrol_page_hook

} // class
