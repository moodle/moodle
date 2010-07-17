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
 * Provides various useful functionality to plugins that offer or use this MNet service
 *
 * Remote enrolment service is used by enrol_mnet plugin which publishes the server side
 * methods. The client side is accessible from the admin tree.
 *
 * @package    mnetservice
 * @subpackage enrol
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Singleton providing various functionality usable by plugin(s) implementing this MNet service
 */
class mnetservice_enrol {

    /** @var mnetservice_enrol holds the singleton instance. */
    protected static $singleton;

    /** @var caches the result of {@link self::get_remote_subscribers()} */
    protected $cachesubscribers = null;

    /** @var caches the result of {@link self::get_remote_publishers()} */
    protected $cachepublishers = null;

    /**
     * This is singleton, use {@link mnetservice_enrol::get_instance()}
     */
    protected function __construct() {
    }

    /**
     * @return mnetservice_enrol singleton instance
     */
    public static function get_instance() {
        if (is_null(self::$singleton)) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * Is this service enabled?
     *
     * Currently, this checks if whole MNet is available. In the future, additional
     * checks can be done. Probably the field 'offer' should be checked but it does
     * not seem to be used so far.
     *
     * @todo move this to some parent class once we have such
     * @return bool
     */
    public function is_available() {
        global $CFG;

        if (empty($CFG->mnet_dispatcher_mode) || $CFG->mnet_dispatcher_mode !== 'strict') {
            return false;
        }
        return true;
    }

    /**
     * Returns a list of remote servers that can enrol their users into our courses
     *
     * We must publish MNet service 'mnet_enrol' for the peers to allow them to enrol
     * their users into our courses.
     *
     * @todo once the MNet core is refactored this may be part of a parent class
     * @todo the name of the service should be changed to the name of this plugin
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
     * Returns a list of remote servers that offer their courses for our users
     *
     * We must subscribe MNet service 'mnet_enrol' for the peers to allow our users to enrol
     * into their courses.
     *
     * @todo once the MNet core is refactored this may be part of a parent class
     * @todo the name of the service should be changed to the name of this plugin
     * @return array
     */
    public function get_remote_publishers() {
        global $DB;

        if (is_null($this->cachepublishers)) {
            $sql = "SELECT DISTINCT h.id, h.name AS hostname, h.wwwroot AS hosturl,
                           a.display_name AS appname
                      FROM {mnet_host} h
                      JOIN {mnet_host2service} hs ON h.id = hs.hostid
                      JOIN {mnet_service} s ON hs.serviceid = s.id
                      JOIN {mnet_application} a ON h.applicationid = a.id
                     WHERE s.name = 'mnet_enrol'
                           AND hs.subscribe = 1";
            $this->cachepublishers = $DB->get_records_sql($sql);
        }

        return $this->cachepublishers;
    }

}
