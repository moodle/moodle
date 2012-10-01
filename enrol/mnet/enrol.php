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
 * Implements the XML-RPC methods this plugin publishes to MNet peers
 *
 * This file must be named enrol.php because current MNet framework has the
 * filename hardcoded in XML-RPC path and we want to be compatible with
 * Moodle 1.x MNet clients. There is a proposal in MDL-21993 to allow
 * map XMP-RPC calls to whatever file, function, class or methods. Once this
 * is fixed, this file will be probably renamed to mnetlib.php (which could
 * be a common name of a plugin library containing functions/methods callable
 * via MNet framework.
 *
 * @package    enrol
 * @subpackage mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * MNet server-side methods that are part of mnetservice_enrol
 *
 * The weird name of the class tries to follow a pattern
 * {plugintype}_{pluginnname}_mnetservice_{servicename}
 *
 * Class methods are compatible with API 1 of the service used by Moodle 1.x
 * and 2.0 peers. The API version might become a part of class name but it is
 * not neccessary due to how xml-rcp methods are/will be mapped to php methods.
 */
class enrol_mnet_mnetservice_enrol {

    /**
     * Returns list of courses that we offer to the caller for remote enrolment of their users
     *
     * Since Moodle 2.0, courses are made available for MNet peers by creating an instance
     * of enrol_mnet plugin for the course. Hidden courses are not returned. If there are two
     * instances - one specific for the host and one for 'All hosts', the setting of the specific
     * one is used. The id of the peer is kept in customint1, no other custom fields are used.
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @return array
     */
    public function available_courses() {
        global $CFG, $DB;
        require_once($CFG->libdir.'/filelib.php');

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        // we call our id as 'remoteid' because it will be sent to the peer
        // the column aliases are required by MNet protocol API for clients 1.x and 2.0
        $sql = "SELECT c.id AS remoteid, c.fullname, c.shortname, c.idnumber, c.summary, c.summaryformat,
                       c.sortorder, c.startdate, cat.id AS cat_id, cat.name AS cat_name,
                       cat.description AS cat_description, cat.descriptionformat AS cat_descriptionformat,
                       e.cost, e.currency, e.roleid AS defaultroleid, r.name AS defaultrolename,
                       e.customint1
                  FROM {enrol} e
            INNER JOIN {course} c ON c.id = e.courseid
            INNER JOIN {course_categories} cat ON cat.id = c.category
            INNER JOIN {role} r ON r.id = e.roleid
                 WHERE e.enrol = 'mnet'
                       AND (e.customint1 = 0 OR e.customint1 = ?)
                       AND c.visible = 1
              ORDER BY cat.sortorder, c.sortorder, c.shortname";

        $rs = $DB->get_recordset_sql($sql, array($client->id));

        $courses = array();
        foreach ($rs as $course) {
            // use the record if it does not exist yet or is host-specific
            if (empty($courses[$course->remoteid]) or ($course->customint1 > 0)) {
                unset($course->customint1); // the client does not need to know this
                $context = context_course::instance($course->remoteid);
                // Rewrite file URLs so that they are correct
                $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', false);
                $courses[$course->remoteid] = $course;
            }
        }
        $rs->close();

        return array_values($courses); // can not use keys for backward compatibility
    }

    /**
     * This method has never been implemented in Moodle MNet API
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @return array empty array
     */
    public function user_enrolments() {
        global $CFG, $DB;

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }
        return array();
    }

    /**
     * Enrol remote user to our course
     *
     * If we do not have local record for the remote user in our database,
     * it gets created here.
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @param array $userdata user details {@see mnet_fields_to_import()}
     * @param int $courseid our local course id
     * @return bool true if the enrolment has been successful, throws exception otherwise
     */
    public function enrol_user(array $userdata, $courseid) {
        global $CFG, $DB;
        require_once(dirname(__FILE__).'/lib.php');

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        if (empty($userdata['username'])) {
            throw new mnet_server_exception(5021, 'emptyusername', 'enrol_mnet');
        }

        // do we know the remote user?
        $user = $DB->get_record('user', array('username'=>$userdata['username'], 'mnethostid'=>$client->id));

        if ($user === false) {
            // here we could check the setting if the enrol_mnet is allowed to auto-register
            // users {@link http://tracker.moodle.org/browse/MDL-21327}
            $user = mnet_strip_user((object)$userdata, mnet_fields_to_import($client));
            $user->mnethostid = $client->id;
            $user->auth = 'mnet';
            $user->confirmed = 1;
            try {
                $user->id = $DB->insert_record('user', $user);
            } catch (Exception $e) {
                throw new mnet_server_exception(5011, 'couldnotcreateuser', 'enrol_mnet');
            }
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            throw new mnet_server_exception(5012, 'coursenotfound', 'enrol_mnet');
        }

        $courses = $this->available_courses();
        $isavailable = false;
        foreach ($courses as $available) {
            if ($available->remoteid == $course->id) {
                $isavailable = true;
                break;
            }
        }
        if (!$isavailable) {
            throw new mnet_server_exception(5013, 'courseunavailable', 'enrol_mnet');
        }

        // try to load host specific enrol_mnet instance first
        $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'mnet', 'customint1'=>$client->id), '*', IGNORE_MISSING);

        if ($instance === false) {
            // if not found, try to load instance for all hosts
            $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'mnet', 'customint1'=>0), '*', IGNORE_MISSING);
        }

        if ($instance === false) {
            // this should not happen as the course was returned by {@see self::available_courses()}
            throw new mnet_server_exception(5017, 'noenrolinstance', 'enrol_mnet');
        }

        if (!$enrol = enrol_get_plugin('mnet')) {
            throw new mnet_server_exception(5018, 'couldnotinstantiate', 'enrol_mnet');
        }

        try {
            $enrol->enrol_user($instance, $user->id, $instance->roleid, time());

        } catch (Exception $e) {
            throw new mnet_server_exception(5019, 'couldnotenrol', 'enrol_mnet', $e->getMessage());
        }

        return true;
    }

    /**
     * Unenrol remote user from our course
     *
     * Only users enrolled via enrol_mnet plugin can be unenrolled remotely. If the
     * remote user is enrolled into the local course via some other enrol plugin
     * (enrol_manual for example), the remote host can't touch such enrolment. Please
     * do not report this behaviour as bug, it is a feature ;-)
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @param string $username of the remote user
     * @param int $courseid of our local course
     * @return bool true if the unenrolment has been successful, throws exception otherwise
     */
    public function unenrol_user($username, $courseid) {
        global $CFG, $DB;

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$client->id));

        if ($user === false) {
            throw new mnet_server_exception(5014, 'usernotfound', 'enrol_mnet');
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            throw new mnet_server_exception(5012, 'coursenotfound', 'enrol_mnet');
        }

        $courses = $this->available_courses();
        $isavailable = false;
        foreach ($courses as $available) {
            if ($available->remoteid == $course->id) {
                $isavailable = true;
                break;
            }
        }
        if (!$isavailable) {
            // if they can not enrol, they can not unenrol
            throw new mnet_server_exception(5013, 'courseunavailable', 'enrol_mnet');
        }

        // try to load host specific enrol_mnet instance first
        $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'mnet', 'customint1'=>$client->id), '*', IGNORE_MISSING);

        if ($instance === false) {
            // if not found, try to load instance for all hosts
            $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'mnet', 'customint1'=>0), '*', IGNORE_MISSING);
            $instanceforall = true;
        }

        if ($instance === false) {
            // this should not happen as the course was returned by {@see self::available_courses()}
            throw new mnet_server_exception(5017, 'noenrolinstance', 'enrol_mnet');
        }

        if (!$enrol = enrol_get_plugin('mnet')) {
            throw new mnet_server_exception(5018, 'couldnotinstantiate', 'enrol_mnet');
        }

        if ($DB->record_exists('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$user->id))) {
            try {
                $enrol->unenrol_user($instance, $user->id);

            } catch (Exception $e) {
                throw new mnet_server_exception(5020, 'couldnotunenrol', 'enrol_mnet', $e->getMessage());
            }
        }

        if (empty($instanceforall)) {
            // if the user was enrolled via 'All hosts' instance and the specific one
            // was created after that, the first enrolment would be kept.
            $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'mnet', 'customint1'=>0), '*', IGNORE_MISSING);

            if ($instance) {
                // repeat the same procedure for 'All hosts' instance, too. Note that as the host specific
                // instance exists, it will be used for the future enrolments

                if ($DB->record_exists('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$user->id))) {
                    try {
                        $enrol->unenrol_user($instance, $user->id);

                    } catch (Exception $e) {
                        throw new mnet_server_exception(5020, 'couldnotunenrol', 'enrol_mnet', $e->getMessage());
                    }
                }
            }
        }

        return true;
    }

    /**
     * Returns a list of users from the client server who are enrolled in our course
     *
     * Suitable instance of enrol_mnet must be created in the course. This method will not
     * return any information about the enrolments in courses that are not available for
     * remote enrolment, even if their users are enrolled into them via other plugin
     * (note the difference from {@link self::user_enrolments()}).
     *
     * This method will return enrolment information for users from hosts regardless
     * the enrolment plugin. It does not matter if the user was enrolled remotely by
     * their admin or locally. Once the course is available for remote enrolments, we
     * will tell them everything about their users.
     *
     * In Moodle 1.x the returned array used to be indexed by username. The side effect
     * of MDL-19219 fix is that we do not need to use such index and therefore we can
     * return all enrolment records. MNet clients 1.x will only use the last record for
     * the student, if she is enrolled via multiple plugins.
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @param int $courseid ID of our course
     * @param string|array $roles comma separated list of role shortnames (or array of them)
     * @return array
     */
    public function course_enrolments($courseid, $roles=null) {
        global $DB, $CFG;

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        $sql = "SELECT u.username, r.shortname, r.name, e.enrol, ue.timemodified
                  FROM {user_enrolments} ue
                  JOIN {user} u ON ue.userid = u.id
                  JOIN {enrol} e ON ue.enrolid = e.id
                  JOIN {role} r ON e.roleid = r.id
                 WHERE u.mnethostid = :mnethostid
                       AND e.courseid = :courseid
                       AND u.id <> :guestid
                       AND u.confirmed = 1
                       AND u.deleted = 0";
        $params['mnethostid'] = $client->id;
        $params['courseid'] = $courseid;
        $params['guestid'] = $CFG->siteguest;

        if (!is_null($roles)) {
            if (!is_array($roles)) {
                $roles = explode(',', $roles);
            }
            $roles = array_map('trim', $roles);
            list($rsql, $rparams) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED);
            $sql .= " AND r.shortname $rsql";
            $params = array_merge($params, $rparams);
        }

        list($sort, $sortparams) = users_order_by_sql('u');
        $sql .= " ORDER BY $sort";

        $rs = $DB->get_recordset_sql($sql, array_merge($params, $sortparams));
        $list = array();
        foreach ($rs as $record) {
            $list[] = $record;
        }
        $rs->close();

        return $list;
    }
}
