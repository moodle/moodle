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

/**
 * Class enrol_guest_plugin
 *
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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

    /**
     * Enrol a user using a given enrolment instance.
     *
     * @param stdClass $instance
     * @param int $userid
     * @param null $roleid
     * @param int $timestart
     * @param int $timeend
     * @param null $status
     * @param null $recovergrades
     */
    public function enrol_user(stdClass $instance, $userid, $roleid = null, $timestart = 0, $timeend = 0, $status = null, $recovergrades = null) {
        // no real enrolments here!
        return;
    }

    /**
     * Enrol a user from a given enrolment instance.
     *
     * @param stdClass $instance
     * @param int $userid
     */
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
        } else if (WS_SERVER) { // Mobile app mostly.
            $storedpass = get_user_preferences('enrol_guest_ws_password_'. $instance->id);
            // We check first if there is a supplied password.
            if (!is_null($storedpass)) {
                $allow = $storedpass === $instance->password;

                if (!$allow) {
                    // Reset, probably the course password was changed.
                    unset_user_preference('enrol_guest_ws_password_' . $instance->id);
                }
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
     * Returns true if the current user can add a new instance of enrolment plugin in course.
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/guest:config', $context)) {
            return false;
        }

        if ($DB->record_exists('enrol', array('courseid'=>$courseid, 'enrol'=>'guest'))) {
            return false;
        }

        return true;
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
                    \core\event\enrol_instance_updated::create_from_record($instance)->trigger();

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

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/guest:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        if (!has_capability('enrol/guest:config', $context)) {
            return false;
        }

        // If the instance is currently disabled, before it can be enabled, we must check whether the password meets the
        // password policies.
        if ($instance->status == ENROL_INSTANCE_DISABLED) {
            if ($this->get_config('requirepassword')) {
                if (empty($instance->password)) {
                    return false;
                }
            }

            // Only check the password if it is set.
            if (!empty($instance->password) && $this->get_config('usepasswordpolicy')) {
                if (!check_password_policy($instance->password, $errmsg)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get default settings for enrol_guest.
     *
     * @return array
     */
    public function get_instance_defaults() {
        $fields = array();
        $fields['status']          = $this->get_config('status');
        return $fields;
    }

    /**
     * Return information for enrolment instance containing list of parameters required
     * for enrolment, name of enrolment plugin etc.
     *
     * @param stdClass $instance enrolment instance
     * @return stdClass instance info.
     * @since Moodle 3.1
     */
    public function get_enrol_info(stdClass $instance) {

        $instanceinfo = new stdClass();
        $instanceinfo->id = $instance->id;
        $instanceinfo->courseid = $instance->courseid;
        $instanceinfo->type = $this->get_name();
        $instanceinfo->name = $this->get_instance_name($instance);
        $instanceinfo->status = $instance->status == ENROL_INSTANCE_ENABLED;

        // Specifics enrolment method parameters.
        $instanceinfo->requiredparam = new stdClass();
        $instanceinfo->requiredparam->passwordrequired = !empty($instance->password);

        // If the plugin is enabled, return the URL for obtaining more information.
        if ($instanceinfo->status) {
            $instanceinfo->wsfunction = 'enrol_guest_get_instance_info';
        }
        return $instanceinfo;
    }

    /**
     * Return an array of valid options for the status.
     *
     * @return array
     */
    protected function get_status_options() {
        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        return $options;
    }

    /**
     * Add elements to the edit instance form.
     *
     * @param stdClass $instance
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return bool
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        global $CFG;

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_guest'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_guest');
        $mform->setDefault('status', $this->get_config('status'));
        $mform->setAdvanced('status', $this->get_config('status_adv'));

        $mform->addElement('passwordunmask', 'password', get_string('password', 'enrol_guest'));
        $mform->addHelpButton('password', 'password', 'enrol_guest');

        // If we have a new instance and the password is required - make sure it is set. For existing
        // instances we do not force the password to be required as it may have been set to empty before
        // the password was required. We check in the validation function whether this check is required
        // for existing instances.
        if (empty($instance->id) && $this->get_config('requirepassword')) {
            $mform->addRule('password', get_string('required'), 'required', null);
        }
    }

    /**
     * We are a good plugin and don't invent our own UI/validation code path.
     *
     * @return boolean
     */
    public function use_standard_editing_ui() {
        return true;
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        $errors = array();

        $checkpassword = false;

        if ($data['id']) {
            // Check the password if we are enabling the plugin again.
            if (($instance->status == ENROL_INSTANCE_DISABLED) && ($data['status'] == ENROL_INSTANCE_ENABLED)) {
                $checkpassword = true;
            }

            // Check the password if the instance is enabled and the password has changed.
            if (($data['status'] == ENROL_INSTANCE_ENABLED) && ($instance->password !== $data['password'])) {
                $checkpassword = true;
            }
        } else {
            $checkpassword = true;
        }

        if ($checkpassword) {
            $require = $this->get_config('requirepassword');
            $policy  = $this->get_config('usepasswordpolicy');
            if ($require && trim($data['password']) === '') {
                $errors['password'] = get_string('required');
            } else if (!empty($data['password']) && $policy) {
                $errmsg = '';
                if (!check_password_policy($data['password'], $errmsg)) {
                    $errors['password'] = $errmsg;
                }
            }
        }

        $validstatus = array_keys($this->get_status_options());
        $tovalidate = array(
            'status' => $validstatus
        );
        $typeerrors = $this->validate_param_types($data, $tovalidate);
        $errors = array_merge($errors, $typeerrors);

        return $errors;
    }

    /**
     * Check if enrolment plugin is supported in csv course upload.
     *
     * @return bool
     */
    public function is_csv_upload_supported(): bool {
        return true;
    }

    /**
     * Finds matching instances for a given course.
     *
     * @param array $enrolmentdata enrolment data.
     * @param int $courseid Course ID.
     * @return stdClass|null Matching instance
     */
    public function find_instance(array $enrolmentdata, int $courseid): ?stdClass {

        $instances = enrol_get_instances($courseid, false);
        $instance = null;
        foreach ($instances as $i) {
            if ($i->enrol == 'guest') {
                // There can be only one guest enrol instance so find first available.
                $instance = $i;
                break;
            }
        }
        return $instance;
    }

    /**
     * Fill custom fields data for a given enrolment plugin.
     *
     * @param array $enrolmentdata enrolment data.
     * @param int $courseid Course ID.
     * @return array Updated enrolment data with custom fields info.
     */
    public function fill_enrol_custom_fields(array $enrolmentdata, int $courseid): array {
        return $enrolmentdata + ['password' => ''];
    }
}

/**
 * Get icon mapping for font-awesome.
 */
function enrol_guest_get_fontawesome_icon_map() {
    return [
        'enrol_guest:withpassword' => 'fa-key',
        'enrol_guest:withoutpassword' => 'fa-unlock-alt',
    ];
}
