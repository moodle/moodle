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
 * @package    enrol_self
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Self enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_self_plugin extends enrol_plugin {

    protected $lasternoller = null;
    protected $lasternollerinstanceid = 0;

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
        $key = false;
        $nokey = false;
        foreach ($instances as $instance) {
            if ($this->can_self_enrol($instance, false) !== true) {
                // User can not enrol himself.
                // Note that we do not check here if user is already enrolled for performance reasons -
                // such check would execute extra queries for each course in the list of courses and
                // would hide self-enrolment icons from guests.
                continue;
            }
            if ($instance->password or $instance->customint1) {
                $key = true;
            } else {
                $nokey = true;
            }
        }
        $icons = array();
        if ($nokey) {
            $icons[] = new pix_icon('withoutkey', get_string('pluginname', 'enrol_self'), 'enrol_self');
        }
        if ($key) {
            $icons[] = new pix_icon('withkey', get_string('pluginname', 'enrol_self'), 'enrol_self');
        }
        return $icons;
    }

    /**
     * Returns localised name of enrol instance
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING)) . ')';
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
        // Users may tweak the roles later.
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually manually.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak period and status.
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {

        if (true !== $this->can_self_enrol($instance, false)) {
            return false;
        }

        return true;
    }

    /**
     * Return true if we can add a new instance to this course.
     *
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/self:config', $context)) {
            return false;
        }

        return true;
    }

    /**
     * Self enrol user to course
     *
     * @param stdClass $instance enrolment instance
     * @param stdClass $data data needed for enrolment.
     * @return bool|array true if enroled else eddor code and messege
     */
    public function enrol_self(stdClass $instance, $data = null) {
        global $DB, $USER, $CFG;

        // Don't enrol user if password is not passed when required.
        if ($instance->password && !isset($data->enrolpassword)) {
            return;
        }

        $timestart = time();
        if ($instance->enrolperiod) {
            $timeend = $timestart + $instance->enrolperiod;
        } else {
            $timeend = 0;
        }

        $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);

        \core\notification::success(get_string('youenrolledincourse', 'enrol'));

        // Test whether the password is also used as a group key.
        if ($instance->password && $instance->customint1) {
            $groups = $DB->get_records('groups', array('courseid'=>$instance->courseid), 'id', 'id, enrolmentkey');
            foreach ($groups as $group) {
                if (empty($group->enrolmentkey)) {
                    continue;
                }
                if ($group->enrolmentkey === $data->enrolpassword) {
                    // Add user to group.
                    require_once($CFG->dirroot.'/group/lib.php');
                    groups_add_member($group->id, $USER->id);
                    break;
                }
            }
        }
        // Send welcome message.
        if ($instance->customint4 != ENROL_DO_NOT_SEND_EMAIL) {
            $this->email_welcome_message($instance, $USER);
        }
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $USER;

        require_once("$CFG->dirroot/enrol/self/locallib.php");

        $enrolstatus = $this->can_self_enrol($instance);

        if (true === $enrolstatus) {
            // This user can self enrol using this instance.
            $form = new enrol_self_enrol_form(null, $instance);
            $instanceid = optional_param('instance', 0, PARAM_INT);
            if ($instance->id == $instanceid) {
                if ($data = $form->get_data()) {
                    $this->enrol_self($instance, $data);
                }
            }
        } else {
            // This user can not self enrol using this instance. Using an empty form to keep
            // the UI consistent with other enrolment plugins that returns a form.
            $data = new stdClass();
            $data->header = $this->get_instance_name($instance);
            $data->info = $enrolstatus;

            // The can_self_enrol call returns a button to the login page if the user is a
            // guest, setting the login url to the form if that is the case.
            $url = isguestuser() ? get_login_url() : null;
            $form = new enrol_self_empty_form($url, $data);
        }

        ob_start();
        $form->display();
        $output = ob_get_clean();
        return $OUTPUT->box($output);
    }

    /**
     * Checks if user can self enrol.
     *
     * @param stdClass $instance enrolment instance
     * @param bool $checkuserenrolment if true will check if user enrolment is inactive.
     *             used by navigation to improve performance.
     * @return bool|string true if successful, else error message or false.
     */
    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true) {
        global $DB, $OUTPUT, $USER;

        if ($checkuserenrolment) {
            if (isguestuser()) {
                // Can not enrol guest.
                return get_string('noguestaccess', 'enrol') . $OUTPUT->continue_button(get_login_url());
            }
            // Check if user is already enroled.
            if ($DB->get_record('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
                return get_string('canntenrol', 'enrol_self');
            }
        }

        // Check if self enrolment is available right now for users.
        $result = $this->is_self_enrol_available($instance);
        if ($result !== true) {
            return $result;
        }

        // Check if user has the capability to enrol in this context.
        if (!has_capability('enrol/self:enrolself', context_course::instance($instance->courseid))) {
            return get_string('canntenrol', 'enrol_self');
        }

        return true;
    }

    /**
     * Does this plugin support some way to self enrol?
     * This function doesn't check user capabilities. Use can_self_enrol to check capabilities.
     *
     * @param stdClass $instance enrolment instance
     * @return bool - true means "Enrol me in this course" link could be available
     */
    public function is_self_enrol_available(stdClass $instance) {
        global $CFG, $DB, $USER;

        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return get_string('canntenrol', 'enrol_self');
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            return get_string('canntenrolearly', 'enrol_self', userdate($instance->enrolstartdate));
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            return get_string('canntenrollate', 'enrol_self', userdate($instance->enrolenddate));
        }

        if (!$instance->customint6) {
            // New enrols not allowed.
            return get_string('canntenrol', 'enrol_self');
        }

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            return get_string('canntenrol', 'enrol_self');
        }

        if ($instance->customint3 > 0) {
            // Max enrol limit specified.
            $count = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
            if ($count >= $instance->customint3) {
                // Bad luck, no more self enrolments here.
                return get_string('maxenrolledreached', 'enrol_self');
            }
        }

        if ($instance->customint5) {
            require_once("$CFG->dirroot/cohort/lib.php");
            if (!cohort_is_member($instance->customint5, $USER->id)) {
                $cohort = $DB->get_record('cohort', array('id' => $instance->customint5));
                if (!$cohort) {
                    return null;
                }
                $a = format_string($cohort->name, true, array('context' => context::instance_by_id($cohort->contextid)));
                return markdown_to_html(get_string('cohortnonmemberinfo', 'enrol_self', $a));
            }
        }

        return true;
    }

    /**
     * Return information for enrolment instance containing list of parameters required
     * for enrolment, name of enrolment plugin etc.
     *
     * @param stdClass $instance enrolment instance
     * @return stdClass instance info.
     */
    public function get_enrol_info(stdClass $instance) {

        $instanceinfo = new stdClass();
        $instanceinfo->id = $instance->id;
        $instanceinfo->courseid = $instance->courseid;
        $instanceinfo->type = $this->get_name();
        $instanceinfo->name = $this->get_instance_name($instance);
        $instanceinfo->status = $this->can_self_enrol($instance);

        if ($instance->password) {
            $instanceinfo->requiredparam = new stdClass();
            $instanceinfo->requiredparam->enrolpassword = get_string('password', 'enrol_self');
        }

        // If enrolment is possible and password is required then return ws function name to get more information.
        if ((true === $instanceinfo->status) && $instance->password) {
            $instanceinfo->wsfunction = 'enrol_self_get_instance_info';
        }
        return $instanceinfo;
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     * @return array
     */
    public function get_instance_defaults() {
        $expirynotify = $this->get_config('expirynotify');
        if ($expirynotify == 2) {
            $expirynotify = 1;
            $notifyall = 1;
        } else {
            $notifyall = 0;
        }

        $fields = array();
        $fields['status']          = $this->get_config('status');
        $fields['roleid']          = $this->get_config('roleid');
        $fields['enrolperiod']     = $this->get_config('enrolperiod');
        $fields['expirynotify']    = $expirynotify;
        $fields['notifyall']       = $notifyall;
        $fields['expirythreshold'] = $this->get_config('expirythreshold');
        $fields['customint1']      = $this->get_config('groupkey');
        $fields['customint2']      = $this->get_config('longtimenosee');
        $fields['customint3']      = $this->get_config('maxenrolled');
        $fields['customint4']      = $this->get_config('sendcoursewelcomemessage');
        $fields['customint5']      = 0;
        $fields['customint6']      = $this->get_config('newenrols');

        return $fields;
    }

    /**
     * Send welcome email to specified user.
     *
     * @param stdClass $instance
     * @param stdClass $user user record
     * @return void
     */
    protected function email_welcome_message($instance, $user) {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context'=>$context));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";

        if (!is_null($instance->customtext1) && trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $key = array('{$a->coursename}', '{$a->profileurl}', '{$a->fullname}', '{$a->email}');
            $value = array($a->coursename, $a->profileurl, fullname($user), $user->email);
            $message = str_replace($key, $value, $message);
            if (strpos($message, '<') === false) {
                // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else {
                // This is most probably the tag/newline soup known as FORMAT_MOODLE.
                $messagehtml = format_text($message, FORMAT_MOODLE, array('context'=>$context, 'para'=>false, 'newlines'=>true, 'filter'=>true));
                $messagetext = html_to_text($messagehtml);
            }
        } else {
            $messagetext = get_string('welcometocoursetext', 'enrol_self', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        $subject = get_string('welcometocourse', 'enrol_self', format_string($course->fullname, true, array('context'=>$context)));

        $sendoption = $instance->customint4;
        $contact = $this->get_welcome_email_contact($sendoption, $context);

        // Directly emailing welcome message rather than using messaging.
        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

    /**
     * Sync all meta course links.
     *
     * @param progress_trace $trace
     * @param int $courseid one course, empty mean all
     * @return int 0 means ok, 1 means error, 2 means plugin disabled
     */
    public function sync(progress_trace $trace, $courseid = null) {
        global $DB;

        if (!enrol_is_enabled('self')) {
            $trace->finished();
            return 2;
        }

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Verifying self-enrolments...');

        $params = array('now'=>time(), 'useractive'=>ENROL_USER_ACTIVE, 'courselevel'=>CONTEXT_COURSE);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        // Note: the logic of self enrolment guarantees that user logged in at least once (=== u.lastaccess set)
        //       and that user accessed course at least once too (=== user_lastaccess record exists).

        // First deal with users that did not log in for a really long time - they do not have user_lastaccess records.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'self' AND e.customint2 > 0)
                  JOIN {user} u ON u.id = ue.userid
                 WHERE :now - u.lastaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
            $days = $instance->customint2 / DAYSECS;
            $trace->output("unenrolling user $userid from course $instance->courseid " .
                "as they did not log in for at least $days days", 1);
        }
        $rs->close();

        // Now unenrol from course user did not visit for a long time.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'self' AND e.customint2 > 0)
                  JOIN {user_lastaccess} ul ON (ul.userid = ue.userid AND ul.courseid = e.courseid)
                 WHERE :now - ul.timeaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
            $days = $instance->customint2 / DAYSECS;
            $trace->output("unenrolling user $userid from course $instance->courseid " .
                "as they did not access the course for at least $days days", 1);
        }
        $rs->close();

        $trace->output('...user self-enrolment updates finished.');
        $trace->finished();

        $this->process_expirations($trace, $courseid);

        return 0;
    }

    /**
     * Returns the user who is responsible for self enrolments in given instance.
     *
     * Usually it is the first editing teacher - the person with "highest authority"
     * as defined by sort_by_roleassignment_authority() having 'enrol/self:manage'
     * capability.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        global $DB;

        if ($this->lasternollerinstanceid == $instanceid and $this->lasternoller) {
            return $this->lasternoller;
        }

        $instance = $DB->get_record('enrol', array('id'=>$instanceid, 'enrol'=>$this->get_name()), '*', MUST_EXIST);
        $context = context_course::instance($instance->courseid);

        if ($users = get_enrolled_users($context, 'enrol/self:manage')) {
            $users = sort_by_roleassignment_authority($users, $context);
            $this->lasternoller = reset($users);
            unset($users);
        } else {
            $this->lasternoller = parent::get_enroller($instanceid);
        }

        $this->lasternollerinstanceid = $instanceid;

        return $this->lasternoller;
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
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = array(
                'courseid'   => $data->courseid,
                'enrol'      => $this->get_name(),
                'status'     => $data->status,
                'roleid'     => $data->roleid,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            if (!empty($data->customint5)) {
                if ($step->get_task()->is_samesite()) {
                    // Keep cohort restriction unchanged - we are on the same site.
                } else {
                    // Use some id that can not exist in order to prevent self enrolment,
                    // because we do not know what cohort it is in this site.
                    $data->customint5 = -1;
                }
            }
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        // This is necessary only because we may migrate other types to this instance,
        // we do not use component in manual or self enrol.
        role_assign($roleid, $userid, $contextid, '', 0);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/self:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);

        if (!has_capability('enrol/self:config', $context)) {
            return false;
        }

        // If the instance is currently disabled, before it can be enabled,
        // we must check whether the password meets the password policies.
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
     * Return an array of valid options for the newenrols property.
     *
     * @return array
     */
    protected function get_newenrols_options() {
        $options = array(1 => get_string('yes'), 0 => get_string('no'));
        return $options;
    }

    /**
     * Return an array of valid options for the groupkey property.
     *
     * @return array
     */
    protected function get_groupkey_options() {
        $options = array(1 => get_string('yes'), 0 => get_string('no'));
        return $options;
    }

    /**
     * Return an array of valid options for the expirynotify property.
     *
     * @return array
     */
    protected function get_expirynotify_options() {
        $options = array(0 => get_string('no'),
                         1 => get_string('expirynotifyenroller', 'enrol_self'),
                         2 => get_string('expirynotifyall', 'enrol_self'));
        return $options;
    }

    /**
     * Return an array of valid options for the longtimenosee property.
     *
     * @return array
     */
    protected function get_longtimenosee_options() {
        $options = array(0 => get_string('never'),
                         1800 * 3600 * 24 => get_string('numdays', '', 1800),
                         1000 * 3600 * 24 => get_string('numdays', '', 1000),
                         365 * 3600 * 24 => get_string('numdays', '', 365),
                         180 * 3600 * 24 => get_string('numdays', '', 180),
                         150 * 3600 * 24 => get_string('numdays', '', 150),
                         120 * 3600 * 24 => get_string('numdays', '', 120),
                         90 * 3600 * 24 => get_string('numdays', '', 90),
                         60 * 3600 * 24 => get_string('numdays', '', 60),
                         30 * 3600 * 24 => get_string('numdays', '', 30),
                         21 * 3600 * 24 => get_string('numdays', '', 21),
                         14 * 3600 * 24 => get_string('numdays', '', 14),
                         7 * 3600 * 24 => get_string('numdays', '', 7));
        return $options;
    }

    /**
     * The self enrollment plugin has several bulk operations that can be performed.
     * @param course_enrolment_manager $manager
     * @return array
     */
    public function get_bulk_operations(course_enrolment_manager $manager) {
        global $CFG;
        require_once($CFG->dirroot.'/enrol/self/locallib.php');
        $context = $manager->get_context();
        $bulkoperations = array();
        if (has_capability("enrol/self:manage", $context)) {
            $bulkoperations['editselectedusers'] = new enrol_self_editselectedusers_operation($manager, $this);
        }
        if (has_capability("enrol/self:unenrol", $context)) {
            $bulkoperations['deleteselectedusers'] = new enrol_self_deleteselectedusers_operation($manager, $this);
        }
        return $bulkoperations;
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
        global $CFG, $DB;

        // Merge these two settings to one value for the single selection element.
        if ($instance->notifyall and $instance->expirynotify) {
            $instance->expirynotify = 2;
        }
        unset($instance->notifyall);

        $nameattribs = array('size' => '20', 'maxlength' => '255');
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'), $nameattribs);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_self'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_self');

        $options = $this->get_newenrols_options();
        $mform->addElement('select', 'customint6', get_string('newenrols', 'enrol_self'), $options);
        $mform->addHelpButton('customint6', 'newenrols', 'enrol_self');
        $mform->disabledIf('customint6', 'status', 'eq', ENROL_INSTANCE_DISABLED);

        $passattribs = array('size' => '20', 'maxlength' => '50');
        $mform->addElement('passwordunmask', 'password', get_string('password', 'enrol_self'), $passattribs);
        $mform->addHelpButton('password', 'password', 'enrol_self');
        if (empty($instance->id) and $this->get_config('requirepassword')) {
            $mform->addRule('password', get_string('required'), 'required', null, 'client');
        }
        $mform->addRule('password', get_string('maximumchars', '', 50), 'maxlength', 50, 'server');

        $options = $this->get_groupkey_options();
        $mform->addElement('select', 'customint1', get_string('groupkey', 'enrol_self'), $options);
        $mform->addHelpButton('customint1', 'groupkey', 'enrol_self');

        $roles = $this->extend_assignable_roles($context, $instance->roleid);
        $mform->addElement('select', 'roleid', get_string('role', 'enrol_self'), $roles);

        $options = array('optional' => true, 'defaultunit' => 86400);
        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_self'), $options);
        $mform->addHelpButton('enrolperiod', 'enrolperiod', 'enrol_self');

        $options = $this->get_expirynotify_options();
        $mform->addElement('select', 'expirynotify', get_string('expirynotify', 'core_enrol'), $options);
        $mform->addHelpButton('expirynotify', 'expirynotify', 'core_enrol');

        $options = array('optional' => false, 'defaultunit' => 86400);
        $mform->addElement('duration', 'expirythreshold', get_string('expirythreshold', 'core_enrol'), $options);
        $mform->addHelpButton('expirythreshold', 'expirythreshold', 'core_enrol');
        $mform->disabledIf('expirythreshold', 'expirynotify', 'eq', 0);

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_self'), $options);
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_self');

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_self'), $options);
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_self');

        $options = $this->get_longtimenosee_options();
        $mform->addElement('select', 'customint2', get_string('longtimenosee', 'enrol_self'), $options);
        $mform->addHelpButton('customint2', 'longtimenosee', 'enrol_self');

        $mform->addElement('text', 'customint3', get_string('maxenrolled', 'enrol_self'));
        $mform->addHelpButton('customint3', 'maxenrolled', 'enrol_self');
        $mform->setType('customint3', PARAM_INT);

        require_once($CFG->dirroot.'/cohort/lib.php');

        $cohorts = array(0 => get_string('no'));
        $allcohorts = cohort_get_available_cohorts($context, 0, 0, 0);
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5])) {
            $c = $DB->get_record('cohort',
                                 array('id' => $instance->customint5),
                                 'id, name, idnumber, contextid, visible',
                                 IGNORE_MISSING);
            if ($c) {
                // Current cohort was not found because current user can not see it. Still keep it.
                $allcohorts[$instance->customint5] = $c;
            }
        }
        foreach ($allcohorts as $c) {
            $cohorts[$c->id] = format_string($c->name, true, array('context' => context::instance_by_id($c->contextid)));
            if ($c->idnumber) {
                $cohorts[$c->id] .= ' ['.s($c->idnumber).']';
            }
        }
        if ($instance->customint5 && !isset($allcohorts[$instance->customint5])) {
            // Somebody deleted a cohort, better keep the wrong value so that random ppl can not enrol.
            $cohorts[$instance->customint5] = get_string('unknowncohort', 'cohort', $instance->customint5);
        }
        if (count($cohorts) > 1) {
            $mform->addElement('select', 'customint5', get_string('cohortonly', 'enrol_self'), $cohorts);
            $mform->addHelpButton('customint5', 'cohortonly', 'enrol_self');
        } else {
            $mform->addElement('hidden', 'customint5');
            $mform->setType('customint5', PARAM_INT);
            $mform->setConstant('customint5', 0);
        }

        $mform->addElement('select', 'customint4', get_string('sendcoursewelcomemessage', 'enrol_self'),
                enrol_send_welcome_email_options());
        $mform->addHelpButton('customint4', 'sendcoursewelcomemessage', 'enrol_self');

        $options = array('cols' => '60', 'rows' => '8');
        $mform->addElement('textarea', 'customtext1', get_string('customwelcomemessage', 'enrol_self'), $options);
        $mform->addHelpButton('customtext1', 'customwelcomemessage', 'enrol_self');

        if (enrol_accessing_via_instance($instance)) {
            $warntext = get_string('instanceeditselfwarningtext', 'core_enrol');
            $mform->addElement('static', 'selfwarn', get_string('instanceeditselfwarning', 'core_enrol'), $warntext);
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
        global $CFG;
        require_once("{$CFG->dirroot}/enrol/self/locallib.php");

        $errors = array();

        $checkpassword = false;

        if ($instance->id) {
            // Check the password if we are enabling the plugin again.
            if (($instance->status == ENROL_INSTANCE_DISABLED) && ($data['status'] == ENROL_INSTANCE_ENABLED)) {
                $checkpassword = true;
            }

            // Check the password if the instance is enabled and the password has changed.
            if (($data['status'] == ENROL_INSTANCE_ENABLED) && ($instance->password !== $data['password'])) {
                $checkpassword = true;
            }

            // Check the password if we are enabling group enrolment keys.
            if (!$instance->customint1 && $data['customint1']) {
                $checkpassword = true;
            }
        } else {
            $checkpassword = true;
        }

        if ($checkpassword) {
            $require = $this->get_config('requirepassword');
            $policy  = $this->get_config('usepasswordpolicy');
            if ($require and trim($data['password']) === '') {
                $errors['password'] = get_string('required');
            } else if (!empty($data['password']) && $policy) {
                $errmsg = '';
                if (!check_password_policy($data['password'], $errmsg)) {
                    $errors['password'] = $errmsg;
                }
            } else if (!empty($data['password']) && $data['customint1'] &&
                    enrol_self_check_group_enrolment_key($data['courseid'], $data['password'])) {

                $errors['password'] = get_string('passwordmatchesgroupkey', 'enrol_self');
            }
        }

        if ($data['status'] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrolenddate']) and $data['enrolenddate'] < $data['enrolstartdate']) {
                $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_self');
            }
        }

        if ($data['expirynotify'] > 0 and $data['expirythreshold'] < 86400) {
            $errors['expirythreshold'] = get_string('errorthresholdlow', 'core_enrol');
        }

        // Now these ones are checked by quickforms, but we may be called by the upload enrolments tool, or a webservive.
        if (core_text::strlen($data['name']) > 255) {
            $errors['name'] = get_string('err_maxlength', 'form', 255);
        }
        $validstatus = array_keys($this->get_status_options());
        $validnewenrols = array_keys($this->get_newenrols_options());
        if (core_text::strlen($data['password']) > 50) {
            $errors['name'] = get_string('err_maxlength', 'form', 50);
        }
        $validgroupkey = array_keys($this->get_groupkey_options());
        $context = context_course::instance($instance->courseid);
        $validroles = array_keys($this->extend_assignable_roles($context, $instance->roleid));
        $validexpirynotify = array_keys($this->get_expirynotify_options());
        $validlongtimenosee = array_keys($this->get_longtimenosee_options());
        $tovalidate = array(
            'enrolstartdate' => PARAM_INT,
            'enrolenddate' => PARAM_INT,
            'name' => PARAM_TEXT,
            'customint1' => $validgroupkey,
            'customint2' => $validlongtimenosee,
            'customint3' => PARAM_INT,
            'customint4' => PARAM_INT,
            'customint5' => PARAM_INT,
            'customint6' => $validnewenrols,
            'status' => $validstatus,
            'enrolperiod' => PARAM_INT,
            'expirynotify' => $validexpirynotify,
            'roleid' => $validroles
        );
        if ($data['expirynotify'] != 0) {
            $tovalidate['expirythreshold'] = PARAM_INT;
        }
        $typeerrors = $this->validate_param_types($data, $tovalidate);
        $errors = array_merge($errors, $typeerrors);

        return $errors;
    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = null) {
        // In the form we are representing 2 db columns with one field.
        if (!empty($fields) && !empty($fields['expirynotify'])) {
            if ($fields['expirynotify'] == 2) {
                $fields['expirynotify'] = 1;
                $fields['notifyall'] = 1;
            } else {
                $fields['notifyall'] = 0;
            }
        }

        return parent::add_instance($course, $fields);
    }

    /**
     * Update instance of enrol plugin.
     * @param stdClass $instance
     * @param stdClass $data modified instance fields
     * @return boolean
     */
    public function update_instance($instance, $data) {
        // In the form we are representing 2 db columns with one field.
        if ($data->expirynotify == 2) {
            $data->expirynotify = 1;
            $data->notifyall = 1;
        } else {
            $data->notifyall = 0;
        }
        // Keep previous/default value of disabled expirythreshold option.
        if (!$data->expirynotify) {
            $data->expirythreshold = $instance->expirythreshold;
        }
        // Add previous value of newenrols if disabled.
        if (!isset($data->customint6)) {
            $data->customint6 = $instance->customint6;
        }

        return parent::update_instance($instance, $data);
    }

    /**
     * Gets a list of roles that this user can assign for the course as the default for self-enrolment.
     *
     * @param context $context the context.
     * @param integer $defaultrole the id of the role that is set as the default for self-enrolment
     * @return array index is the role id, value is the role name
     */
    public function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id' => $defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }

    /**
     * Get the "from" contact which the email will be sent from.
     *
     * @param int $sendoption send email from constant ENROL_SEND_EMAIL_FROM_*
     * @param $context context where the user will be fetched
     * @return mixed|stdClass the contact user object.
     */
    public function get_welcome_email_contact($sendoption, $context) {
        global $CFG;

        $contact = null;
        // Send as the first user assigned as the course contact.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_COURSE_CONTACT) {
            $rusers = array();
            if (!empty($CFG->coursecontact)) {
                $croles = explode(',', $CFG->coursecontact);
                list($sort, $sortparams) = users_order_by_sql('u');
                // We only use the first user.
                $i = 0;
                do {
                    $userfieldsapi = \core_user\fields::for_name();
                    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
                    $rusers = get_role_users($croles[$i], $context, true, 'u.id,  u.confirmed, u.username, '. $allnames . ',
                    u.email, r.sortorder, ra.id', 'r.sortorder, ra.id ASC, ' . $sort, null, '', '', '', '', $sortparams);
                    $i++;
                } while (empty($rusers) && !empty($croles[$i]));
            }
            if ($rusers) {
                $contact = array_values($rusers)[0];
            }
        } else if ($sendoption == ENROL_SEND_EMAIL_FROM_KEY_HOLDER) {
            // Send as the first user with enrol/self:holdkey capability assigned in the course.
            list($sort) = users_order_by_sql('u');
            $keyholders = get_users_by_capability($context, 'enrol/self:holdkey', 'u.*', $sort);
            if (!empty($keyholders)) {
                $contact = array_values($keyholders)[0];
            }
        }

        // If send welcome email option is set to no reply or if none of the previous options have
        // returned a contact send welcome message as noreplyuser.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_NOREPLY || empty($contact)) {
            $contact = core_user::get_noreply_user();
        }

        return $contact;
    }
}

/**
 * Get icon mapping for font-awesome.
 */
function enrol_self_get_fontawesome_icon_map() {
    return [
        'enrol_self:withkey' => 'fa-key',
        'enrol_self:withoutkey' => 'fa-sign-in',
    ];
}
