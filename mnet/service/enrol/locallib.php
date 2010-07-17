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

    /**
     * Fetches the information about the courses available on remote host for our students
     *
     * The information about remote courses available for us is cached in {mnetservice_enrol_courses}.
     * This method either returns the cached information (typically when displaying the list to
     * students) or fetch fresh data via new XML-RPC request (which updates the local cache, too).
     * The lifetime of the cache is 1 day, so even if $usecache is set to true, the cache will be
     * re-populated if we did not fetch from any server (not only the currently requested one)
     * for some time.
     *
     * @param id $mnethostid MNet remote host id
     * @param bool $usecache use cached data or invoke new XML-RPC?
     * @uses mnet_xmlrpc_client Invokes XML-RPC request if the cache is not used
     * @return array|string returned list or serialized array of mnet error messages
     */
    public function get_remote_courses($mnethostid, $usecache=true) {
        global $CFG, $DB;
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

        $lastfetchcourses = get_config('mnetservice_enrol', 'lastfetchcourses');
        if (empty($lastfetchcourses) or (time()-$lastfetchcourses > DAYSECS)) {
            $usecache = false;
        }

        if ($usecache) {
            return $DB->get_records('mnetservice_enrol_courses', array('hostid' => $mnethostid), 'sortorder, shortname');
        }

        // do not use cache - fetch fresh list from remote MNet host
        $peer = new mnet_peer();
        $peer->set_id($mnethostid);

        $request = new mnet_xmlrpc_client();
        $request->set_method('enrol/mnet/enrol.php/available_courses');

        if ($request->send($peer)) {
            $list = array();
            $response = $request->response;

            // get the currently cached courses key'd on remote id - only need remoteid and id fields
            $cachedcourses = $DB->get_records('mnetservice_enrol_courses', array('hostid' => $mnethostid), 'remoteid', 'remoteid, id');

            foreach ($response as &$remote) {
                $course                 = new stdclass(); // record in our local cache
                $course->hostid         = $mnethostid;
                $course->remoteid       = (int)$remote['remoteid'];
                $course->categoryid     = (int)$remote['cat_id'];
                $course->categoryname   = substr($remote['cat_name'], 0, 255);
                $course->sortorder      = (int)$remote['sortorder'];
                $course->fullname       = substr($remote['fullname'], 0, 254);
                $course->shortname      = substr($remote['shortname'], 0, 100);
                $course->idnumber       = substr($remote['idnumber'], 0, 100);
                $course->summary        = $remote['summary'];
                $course->summaryformat  = (int)$remote['summaryformat'];
                $course->startdate      = (int)$remote['startdate'];
                $course->roleid         = (int)$remote['defaultroleid'];
                $course->rolename       = substr($remote['name'], 0, 255);
                // We do not cache the following fields returned from peer in 2.0 any more
                // not cached: cat_description
                // not cached: cat_descriptionformat
                // not cached: cost
                // not cached: currency

                if (empty($cachedcourses[$course->remoteid])) {
                    $course->id = $DB->insert_record('mnetservice_enrol_courses', $course);
                } else {
                    $course->id = $cachedcourses[$course->remoteid]->id;
                    $DB->update_record('mnetservice_enrol_courses', $course);
                }

                $list[$course->remoteid] = $course;
            }

            // prune stale data from cache
            if (!empty($cachedcourses)) {
                foreach ($cachedcourses as $cachedcourse) {
                    if (!empty($list[$cachedcourse->remoteid])) {
                        unset($cachedcourses[$cachedcourse->remoteid]);
                    }
                }
                $staleremoteids = array_keys($cachedcourses);
                if (!empty($staleremoteids)) {
                    list($sql, $params) = $DB->get_in_or_equal($staleremoteids, SQL_PARAMS_NAMED);
                    $select = "hostid=:hostid AND remoteid $sql";
                    $params['hostid'] = $mnethostid;
                    $DB->delete_records_select('mnetservice_enrol_courses', $select, $params);
                }
            }

            // and return the fresh data
            set_config('lastfetchcourses', time(), 'mnetservice_enrol');
            return $list;

        } else {
            return serialize($request->error);
        }
    }

    /**
     * Prepares error messages returned by our XML-RPC requests to be send as debug info to {@link print_error()}
     *
     * MNet client-side methods in this class return request error as serialized array.
     *
     * @param string $error serialized array
     * @return string
     */
    public function format_error_message($errormsg) {
        $errors = unserialize($errormsg);
        $output = 'mnet_xmlrpc_client request returned errors:'."\n";
        foreach ($errors as $error) {
            $output .= "$error\n";
        }
        return $output;
    }

}
