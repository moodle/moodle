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
 * @package    enrol
 * @subpackage paypal
 * @copyright  2010 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Paypal enrolment plugin implementation.
 * @author  Eugene Venter - based on code by Martin Dougiamas and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_paypal_plugin extends enrol_plugin {

    public function get_currencies() {
        // See https://www.paypal.com/cgi-bin/webscr?cmd=p/sell/mc/mc_intro-outside,
        // 3-character ISO-4217: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_currency_codes
        $codes = array(
            'AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'JPY',
            'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'USD');
        $currencies = array();
        foreach ($codes as $c) {
            $currencies[$c] = new lang_string($c, 'core_currencies');
        }

        return $currencies;
    }

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
        return array(new pix_icon('icon', get_string('pluginname', 'enrol_paypal'), 'enrol_paypal'));
    }

    public function roles_protected() {
        // users with role assign cap may tweak the roles later
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // users with unenrol cap may unenrol other users manually - requires enrol/paypal:unenrol
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // users with manage cap may tweak period and status - requires enrol/paypal:manage
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
        if ($instance->enrol !== 'paypal') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/paypal:config', $context)) {
            $managelink = new moodle_url('/enrol/paypal/edit.php', array('courseid'=>$instance->courseid, 'id'=>$instance->id));
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

        if ($instance->enrol !== 'paypal') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/paypal:config', $context)) {
            $editlink = new moodle_url("/enrol/paypal/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/edit', get_string('edit'), 'core', array('class'=>'icon')));
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

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/paypal:config', $context)) {
            return NULL;
        }

        // multiple instances supported - different cost for different roles
        return new moodle_url('/enrol/paypal/edit.php', array('courseid'=>$courseid));
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
        $context = context_course::instance($course->id);

        $shortname = format_string($course->shortname, true, array('context' => $context));
        $strloginto = get_string("loginto", "", $shortname);
        $strcourses = get_string("courses");

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

            if (isguestuser()) { // force login only for guest user, not real users with guest role
                if (empty($CFG->loginhttps)) {
                    $wwwroot = $CFG->wwwroot;
                } else {
                    // This actually is not so secure ;-), 'cause we're
                    // in unencrypted connection...
                    $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
                }
                echo '<div class="mdl-align"><p>'.get_string('paymentrequired').'</p>';
                echo '<p><b>'.get_string('cost').": $instance->currency $cost".'</b></p>';
                echo '<p><a href="'.$wwwroot.'/login/">'.get_string('loginsite').'</a></p>';
                echo '</div>';
            } else {
                //Sanitise some fields before building the PayPal form
                $coursefullname  = format_string($course->fullname, true, array('context'=>$context));
                $courseshortname = $shortname;
                $userfullname    = fullname($USER);
                $userfirstname   = $USER->firstname;
                $userlastname    = $USER->lastname;
                $useraddress     = $USER->address;
                $usercity        = $USER->city;
                $instancename    = $this->get_instance_name($instance);

                include($CFG->dirroot.'/enrol/paypal/enrol.html');
            }

        }

        return $OUTPUT->box(ob_get_clean());
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
                'roleid'     => $data->roleid,
                'cost'       => $data->cost,
                'currency'   => $data->currency,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
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
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/paypal:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    public function cron() {
        $trace = new text_progress_trace();
        $this->process_expirations($trace);
    }

    /**
     * Execute synchronisation.
     * @param progress_trace $trace
     * @return int exit code, 0 means ok
     */
    public function sync(progress_trace $trace) {
        $this->process_expirations($trace);
        return 0;
    }

    /**
     * Do any enrolment expiration processing.
     *
     * @param progress_trace $trace
     * @return bool true if any data processed, false if not
     */
    protected function process_expirations(progress_trace $trace) {
        global $DB;

        //TODO: this method should be moved to parent class once we refactor all existing enrols, see MDL-36504.

        $processed = false;
        $name = $this->get_name();

        // Deal with expired accounts.
        $action = $this->get_config('expiredaction', ENROL_EXT_REMOVED_KEEP);

        if ($action == ENROL_EXT_REMOVED_UNENROL) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :enrol)
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now";
            $params = array('now'=>time(), 'courselevel'=>CONTEXT_COURSE, 'enrol'=>$name);

            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!$processed) {
                    $trace->output("Starting processing of enrol_$name expirations...");
                    $processed = true;
                }
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                if (!$this->roles_protected()) {
                    // Let's just guess what extra roles are supposed to be removed.
                    if ($instance->roleid) {
                        role_unassign($instance->roleid, $ue->userid, $ue->contextid);
                    }
                }
                // The unenrol cleans up all subcontexts if this is the only course enrolment for this user.
                $this->unenrol_user($instance, $ue->userid);
                $trace->output("Unenrolling expired user $ue->userid from course $instance->courseid", 1);
            }
            $rs->close();
            unset($instances);

        } else if ($action == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :enrol)
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now
                           AND ue.status = :useractive";
            $params = array('now'=>time(), 'courselevel'=>CONTEXT_COURSE, 'useractive'=>ENROL_USER_ACTIVE, 'enrol'=>$name);
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!$processed) {
                    $trace->output("Starting processing of enrol_$name expirations...");
                    $processed = true;
                }
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];

                if (!$this->roles_protected()) {
                    // Let's just guess what roles should be removed.
                    $count = $DB->count_records('role_assignments', array('userid'=>$ue->userid, 'contextid'=>$ue->contextid));
                    if ($count == 1) {
                        role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0));

                    } else if ($count > 1 and $instance->roleid) {
                        role_unassign($instance->roleid, $ue->userid, $ue->contextid, '', 0);
                    }
                }
                // In any case remove all roles that belong to this instance and user.
                role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'enrol_'.$name, 'itemid'=>$instance->id), true);
                // Final cleanup of subcontexts if there are no more course roles.
                if (0 == $DB->count_records('role_assignments', array('userid'=>$ue->userid, 'contextid'=>$ue->contextid))) {
                    role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0), true);
                }

                $this->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                $trace->output("Suspending expired user $ue->userid in course $instance->courseid", 1);
            }
            $rs->close();
            unset($instances);

        } else {
            // ENROL_EXT_REMOVED_KEEP means no changes.
        }

        if ($processed) {
            $trace->output("...finished processing of enrol_$name expirations");
        } else {
            $trace->output("No expired enrol_$name enrolments detected");
        }
        $trace->finished();

        return $processed;
    }
}
