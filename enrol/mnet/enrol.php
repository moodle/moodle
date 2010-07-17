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
defined('MNET_SERVER') || die();

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
                $context = get_context_instance(CONTEXT_COURSE, $course->remoteid);
                // Rewrite file URLs so that they are correct
                $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary');
                $courses[$course->remoteid] = $course;
            }
        }
        $rs->close();

        return array_values($courses); // can not use keys for backward compatibility
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function user_enrolments() {
        return array();
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function enrol_user() {
        return false;
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function unenrol_user() {
        return false;
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
     * @return array
     */
    public function course_enrolments($courseid) {
        global $DB;

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        // todo 'guest' nevracet

        return array(
            0 => array(
                'username' => 'admina',
                'shortname' => 'student',
                'name' => 'Student',
                'enrol' => 'mnet',
                'timemodified' => time(),
                ),
            1 => array(
                'username' => 'admina',
                'shortname' => 'teacher',
                'name' => 'Teacher',
                'enrol' => 'manual',
                'timemodified' => time(),
                ),
            2 => array(
                'username' => 'guest',
                'shortname' => 'admin',
                'name' => 'Admin',
                'enrol' => 'crack',
                'timemodified' => time(),
                ),
            3 => array(
                'username' => 'usera',
                'shortname' => 'student',
                'name' => 'Student',
                'enrol' => 'mnet',
                'timemodified' => time(),
                ),
            4 => array(
                'username' => 'doesnotexist',
                'shortname' => 'student',
                'name' => 'Student',
                'enrol' => 'mnet',
                'timemodified' => time(),
                ),
        );
    }
}
