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
 * @package    enrol
 * @subpackage license
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Self enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_license_plugin extends enrol_plugin {

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
            if ($instance->password or $instance->customint1) {
                $key = true;
            } else {
                $nokey = true;
            }
        }
        $icons = array();
        if ($nokey) {
            $icons[] = new pix_icon('withoutkey', get_string('pluginname', 'enrol_license'), 'enrol_license');
        }
        if ($key) {
            $icons[] = new pix_icon('withkey', get_string('pluginname', 'enrol_license'), 'enrol_license');
        }
        return $icons;
    }

    /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id' => $instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid)) . ')';
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
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    /**
     * Sets up navigation entries.
     *
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'license') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/license:config', $context)) {
            $managelink = new moodle_url('/enrol/license/edit.php', array('courseid' => $instance->courseid,
                                                                          'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'license') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/license:config', $context)) {
            $editlink = new moodle_url("/enrol/license/edit.php", array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink,
                                            new pix_icon('i/edit', get_string('edit'),
                                            'core',
                                            array('class' => 'icon')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/manual:config', $context)) {
            return null;
        }
        // Multiple instances supported - different roles with different password.
        return new moodle_url('/enrol/license/edit.php', array('courseid' => $courseid));
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
            // Can not enrol guest!!
            return null;
        }
        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            // TODO: maybe we should tell them they are already enrolled, but can not access the course.
            return null;
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            // TODO: inform that we can not enrol yet.
            return null;
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            // TODO: inform that enrolment is not possible any more.
            return null;
        }

        if ($instance->customint3 > 0) {
            // Max enrol limit specified.
            $count = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
            if ($count >= $instance->customint3) {
                // Bad luck, no more license enrolments here.
                return $OUTPUT->notification(get_string('maxenrolledreached', 'enrol_license'));
            }
        }

        // Get the license information.
        $sql = "SELECT * from {companylicense} cl, {companylicense_users} clu
                WHERE clu.userid = :userid
                AND clu.licenseid = cl.id
                AND clu.isusing = 0
                AND clu.licensecourseid = :courseid";
        if (!$license = $DB->get_record_sql($sql, array('userid' => $USER->id, 'courseid' => $instance->courseid))) {
            return $OUTPUT->notification(get_string('nolicenseinformationfound', 'enrol_license'));
        }

        if (time() > $license->expirydate) {
            return $OUTPUT->notification(get_string('licensenolongervalid', 'enrol_license'));
        }

        if (time() < $license->startdate) {
            return $OUTPUT->notification(get_string('licensenotyetvalid', 'enrol_license', date($CFG->iomad_date_format, $license->startdate)));
        }

        require_once("$CFG->dirroot/enrol/license/locallib.php");
        require_once("$CFG->dirroot/group/lib.php");

        $form = new enrol_license_enrol_form(null, $instance);
        $instanceid = optional_param('instance', 0, PARAM_INT);

        if ($instance->id == $instanceid || $license->type == 1 || $license->type == 3) {
            if ($data = $form->get_data() || $license->type == 1 || $license->type == 3) {
                $enrol = enrol_get_plugin('license');

                // Enrol the user in the course.
                $timestart = time();

                if ($license->type == 1 || $license->type == 3) {
                    // Set the timeend to be time start + the valid length for the license in days.
                    $timeend = $timestart + ($license->validlength * 24 * 60 * 60 );
                } else {
                    // Set the timeend to be when the license runs out.
                    $timeend = $license->expirydate;
                }

                if ($license->type < 2) {
                    $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);
                } else {
                    // Educator role.
                    if ($DB->get_record('iomad_courses', array('courseid' => $instance->courseid, 'shared' => 0))) {
                        // Not shared.
                        $role = $DB->get_record('role', array('shortname' => 'companycourseeditor'));
                    } else {
                        // Shared.
                        $role = $DB->get_record('role', array('shortname' => 'companycoursenoneditor'));
                    }
                    $this->enrol_user($instance, $USER->id, $role->id, $timestart, $timeend);
                }

                // Get the userlicense record.
                $userlicense = $DB->get_record('companylicense_users', array('id' => $license->id));

                // Add the user to the appropriate course group.
                if (!$DB->get_record('course', array('id' => $instance->courseid, 'groupmode' => 0))) {
                    company::add_user_to_shared_course($instance->courseid, $USER->id, $license->companyid, $userlicense->groupid);
                }

                // Update the userlicense record to mark it as in use.
                $DB->set_field('companylicense_users', 'isusing', 1, array('id' => $userlicense->id));

                // Send welcome.
                if ($instance->customint4) {
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
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = array('customint1'  => $this->get_config('groupkey'),
                        'customint2'  => $this->get_config('longtimenosee'),
                        'customint3'  => $this->get_config('maxenrolled'),
                        'customint4'  => $this->get_config('sendcoursewelcomemessage'),
                        'enrolperiod' => $this->get_config('enrolperiod', 0),
                        'status'      => $this->get_config('status'),
                        'roleid'      => $this->get_config('roleid', 0));

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }

        return $this->add_instance($course, $fields);
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

        if (trim($instance->customtext1) !== '') {
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
            $messagetext = get_string('welcometocoursetext', 'enrol_license', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        $subject = get_string('welcometocourse', 'enrol_self', format_string($course->fullname, true, array('context'=>$context)));

        $sendoption = $instance->customint4;
        $contact = $this->get_welcome_email_contact($sendoption, $context);

        // Directly emailing welcome message rather than using messaging.
        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

    /**
     * Enrol license cron support
     * @return void
     */
    public function cron() {
        global $DB;

        if (!enrol_is_enabled('license')) {
            return;
        }

        $plugin = enrol_get_plugin('license');

        $now = time();

        // Note: the logic of license enrolment guarantees that user logged in at least once (=== u.lastaccess set)
        //      and that user accessed course at least once too (=== user_lastaccess record exists).

        // First deal with users that did not log in for a really long time.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'license' AND e.customint2 > 0)
                  JOIN {user} u ON u.id = ue.userid
                 WHERE :now - u.lastaccess > e.customint2";
        $rs = $DB->get_recordset_sql($sql, array('now' => $now));
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $plugin->unenrol_user($instance, $userid);
            mtrace("unenrolling user $userid from course ".
                   $instance->courseid.
                   " as they have did not log in for ".
                   $instance->customint2." days");
        }
        $rs->close();

        // Now unenrol from course user did not visit for a long time.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'license' AND e.customint2 > 0)
                  JOIN {user_lastaccess} ul ON (ul.userid = ue.userid AND ul.courseid = e.courseid)
                 WHERE :now - ul.timeaccess > e.customint2";
        $rs = $DB->get_recordset_sql($sql, array('now' => $now));
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $plugin->unenrol_user($instance, $userid);
            mtrace("unenrolling user $userid from course ".
                   $instance->courseid.
                   " as they have did not access course for ".
                   $instance->customint2." days");
        }
        $rs->close();

        flush();

        // Deal with users who are past enrolment time/ completed the course.
        $runtime = time();
        if ($userids = $DB->get_records_sql("SELECT ue.id, ue.userid, ue.enrolid, e.courseid
                                             FROM {user_enrolments} ue, {enrol} e
                                             WHERE e.enrol='license'
                                             AND e.id = ue.enrolid
                                             AND ue.timeend < :time",
                                             array('time' => $runtime))) {
            foreach ($userids as $user) {
                mtrace("dealing with user $user->userid");
                // Get the license details.
                $license = (array) $DB->get_record_sql("SELECT lu.id, lu.licenseid, lu.userid, lu.isusing,
                                                        lu.timecompleted, lu.score, lu.result
                                                        FROM {companylicense_users} lu, {companylicense_courses} lc
                                                        WHERE lu.userid = :userid AND lc.courseid = :courseid
                                                        AND lu.licenseid = lc.licenseid AND lu.timecompleted IS NULL",
                                                        array('userid' => $user->userid, 'courseid' => $user->courseid));

                // Get the grade item details.
                if ($gradeitems = $DB->get_records_sql("SELECT gg.id, gg.finalgrade, gg.feedback, gi.itemtype
                                                        FROM {grade_grades} gg, {grade_items} gi
                                                        WHERE gg.userid = :userid AND gi.courseid = :courseid
                                                        AND gg.itemid = gi.id",
                                                        array('userid' => $user->userid,
                                                              'enrolid' => $user->enrolid,
                                                              'courseid' => $user->courseid))) {
                    // Delete the grade items.
                    mtrace("removing grade items from course $user->courseid");
                    foreach ($gradeitems as $gradeitem) {
                        if ($gradeitem->itemtype == 'course' ) {
                            $license['score'] = $gradeitem->finalgrade;
                            $license['result'] = $gradeitem->feedback;
                        }
                        $DB->delete_records('grade_grades', array('id' => $gradeitem->id));
                    }

                    // Delete any completion data.
                    $completion = $DB->get_record('course_completions', array('userid' => $user->userid,
                                                                              'course' => $user->courseid));
                    if (!empty($completion->timecompleted)) {
                        $license['timecompleted'] = $completion->timecompleted;
                    } else {
                        $license['timecompleted'] = $runtime;
                    }
                    // Update the user license information.
                    mtrace("updating license ".$license['id']." for user ".$user->userid);
                    $DB->update_record('companylicense_users', $license);
                    // Delete the completion information.
                    mtrace("removing course completion for user $user->userid on $user->courseid");
                    $DB->delete_records('course_completions', array('id' => $completion->id));
                    // Delete the enrolment.
                    mtrace("removing enrolment for user ".$user->userid." from ".$user->courseid);
                    $DB->delete_records('user_enrolments', array('id' => $user->id));
                }
            }
        }
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

        return true;
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
                    $rusers = get_role_users($croles[$i], $context, true, '',
                        'r.sortorder ASC, ' . $sort, null, '', '', '', '', $sortparams);
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
 * Indicates API features that the enrol plugin supports.
 *
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function enrol_license_supports($feature) {
    switch($feature) {
        case ENROL_RESTORE_TYPE: {
            return ENROL_RESTORE_EXACT;
        }

        default: {
            return null;
        }
    }
}
