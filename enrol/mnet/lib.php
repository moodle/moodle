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
 * MNet enrolment plugin
 *
 * @package    enrol
 * @subpackage mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * MNet enrolment plugin implementation for Moodle 2.x enrolment framework
 */
class enrol_mnet_plugin extends enrol_plugin {

    /** @var caches the result of {@link get_remote_subscribers()} */
    protected $cachesubscribers = null;

    ////////////////////////////////////////////////////////////////////////////
    // Enrolment plugin API - methods required by Moodle core
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns localised name of enrol instance
     *
     * @param object|null $instance enrol_mnet instance
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);

        } else if (empty($instance->name)) {
            $enrol = $this->get_name();
            if ($role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = role_get_name($role, get_context_instance(CONTEXT_COURSE, $instance->courseid));
            } else {
                $role = get_string('error');
            }
            if (empty($instance->customint1)) {
                $host = get_string('remotesubscribersall', 'enrol_mnet');
            } else {
                $host = $DB->get_field('mnet_host', 'name', array('id'=>$instance->customint1));
            }
            return get_string('pluginname', 'enrol_'.$enrol) . ' (' . format_string($host) . ' - ' . $role .')';

        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin into the course
     *
     * The link is returned only if there are some MNet peers that we publish enrolment service to.
     *
     * @param int $courseid id of the course to add the instance to
     * @return moodle_url|null page url or null if instance can not be created
     */
    public function get_candidate_link($courseid) {
        global $DB;

        if (!$this->mnet_available()) {
            return null;
        }
        $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
        if (!has_capability('moodle/course:enrolconfig', $coursecontext)) {
            return null;
        }
        $subscribers = $this->get_remote_subscribers();
        if (empty($subscribers)) {
            return null;
        }

        return new moodle_url('/enrol/mnet/addinstance.php', array('id'=>$courseid));
    }

    ////////////////////////////////////////////////////////////////////////////
    // Internal methods used by the plugin itself only
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns a list of remote servers that can enrol their users into our courses
     *
     * We must publish MNet service 'mnet_enrol' for the peers to allow them to enrol
     * their users into our courses.
     *
     * @return array
     */
    public function get_remote_subscribers() {
        global $DB;

        if (is_null($this->cachesubscribers)) {
            $sql = "SELECT DISTINCT h.id, h.name AS hostname, h.wwwroot AS hosturl,
                           a.display_name AS appname
                      FROM {mnet_host} h
                      JOIN {mnet_host2service} hs ON h.id = hs.hostid
                      JOIN {mnet_service} s ON hs.serviceid = s.id
                      JOIN {mnet_application} a ON h.applicationid = a.id
                     WHERE s.name = 'mnet_enrol'
                           AND hs.publish = 1";
            $this->cachesubscribers = $DB->get_records_sql($sql);
        }

        return $this->cachesubscribers;
    }

    /**
     * Is MNet networking enabled at this server?
     *
     * @return bool
     */
    protected function mnet_available() {
        global $CFG;

        if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
            return false;
        }
        return true;
    }

}
