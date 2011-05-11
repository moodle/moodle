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

require_once($CFG->dirroot . '/user/selector/lib.php');

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
                           AND h.deleted = 0
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
                           AND h.deleted = 0
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
        global $CFG, $DB; // $CFG needed!

        $lastfetchcourses = get_config('mnetservice_enrol', 'lastfetchcourses');
        if (empty($lastfetchcourses) or (time()-$lastfetchcourses > DAYSECS)) {
            $usecache = false;
        }

        if ($usecache) {
            return $DB->get_records('mnetservice_enrol_courses', array('hostid' => $mnethostid), 'sortorder, shortname');
        }

        // do not use cache - fetch fresh list from remote MNet host
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';
        $peer = new mnet_peer();
        if (!$peer->set_id($mnethostid)) {
            return serialize(array('unknown mnet peer'));
        }

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
                $course->summaryformat  = empty($remote['summaryformat']) ? FORMAT_MOODLE : (int)$remote['summaryformat'];
                $course->startdate      = (int)$remote['startdate'];
                $course->roleid         = (int)$remote['defaultroleid'];
                $course->rolename       = substr($remote['defaultrolename'], 0, 255);
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
     * Updates local cache about enrolments of our users in remote courses
     *
     * The remote course must allow enrolments via our Remote enrolment service client.
     * Because of legacy design of data structure returned by XML-RPC code, only one
     * user enrolment per course is returned by 1.9 MNet servers. This may be an issue
     * if the user is enrolled multiple times by various enrolment plugins. MNet 2.0
     * servers do not use user name as array keys - they do not need to due to side
     * effect of MDL-19219.
     *
     * @param id $mnethostid MNet remote host id
     * @param int $remotecourseid ID of the course at the remote host
     * @param bool $usecache use cached data or invoke new XML-RPC?
     * @uses mnet_xmlrpc_client Invokes XML-RPC request
     * @return bool|string true if success or serialized array of mnet error messages
     */
    public function req_course_enrolments($mnethostid, $remotecourseid) {
        global $CFG, $DB; // $CFG needed!
        require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

        if (!$DB->record_exists('mnetservice_enrol_courses', array('hostid'=>$mnethostid, 'remoteid'=>$remotecourseid))) {
            return serialize(array('course not available for remote enrolments'));
        }

        $peer = new mnet_peer();
        if (!$peer->set_id($mnethostid)) {
            return serialize(array('unknown mnet peer'));
        }

        $request = new mnet_xmlrpc_client();
        $request->set_method('enrol/mnet/enrol.php/course_enrolments');
        $request->add_param($remotecourseid, 'int');

        if ($request->send($peer)) {
            $list = array();
            $response = $request->response;

            // prepare a table mapping usernames of our users to their ids
            $usernames = array();
            foreach ($response as $unused => $remote) {
                if (!isset($remote['username'])) {
                    // see MDL-19219
                    return serialize(array('remote host running old version of mnet server - does not return username attribute'));
                }
                if ($remote['username'] == 'guest') { // we can not use $CFG->siteguest here
                    // do not try nasty things you bastard!
                    continue;
                }
                $usernames[$remote['username']] = $remote['username'];
            }

            if (!empty($usernames)) {
                list($usql, $params) = $DB->get_in_or_equal($usernames, SQL_PARAMS_NAMED);
                $params['mnetlocalhostid'] = $CFG->mnet_localhost_id;
                $sql = "SELECT username,id
                          FROM {user}
                         WHERE mnethostid = :mnetlocalhostid
                               AND username $usql
                               AND deleted = 0
                               AND confirmed = 1
                      ORDER BY lastname,firstname,email";
                $usersbyusername = $DB->get_records_sql($sql, $params);
            } else {
                $usersbyusername = array();
            }

            // populate the returned list and update local cache of enrolment records
            foreach ($response as $remote) {
                if (empty($usersbyusername[$remote['username']])) {
                    // we do not know this user or she is deleted or not confirmed or is 'guest'
                    continue;
                }
                $enrolment                  = new stdclass();
                $enrolment->hostid          = $mnethostid;
                $enrolment->userid          = $usersbyusername[$remote['username']]->id;
                $enrolment->remotecourseid  = $remotecourseid;
                $enrolment->rolename        = $remote['name']; // $remote['shortname'] not used
                $enrolment->enroltime       = $remote['timemodified'];
                $enrolment->enroltype       = $remote['enrol'];

                $current = $DB->get_record('mnetservice_enrol_enrolments', array('hostid'=>$enrolment->hostid, 'userid'=>$enrolment->userid,
                                       'remotecourseid'=>$enrolment->remotecourseid, 'enroltype'=>$enrolment->enroltype), 'id, enroltime');
                if (empty($current)) {
                    $enrolment->id = $DB->insert_record('mnetservice_enrol_enrolments', $enrolment);
                } else {
                    $enrolment->id = $current->id;
                    if ($current->enroltime != $enrolment->enroltime) {
                        $DB->update_record('mnetservice_enrol_enrolments', $enrolment);
                    }
                }

                $list[$enrolment->id] = $enrolment;
            }

            // prune stale enrolment records
            if (empty($list)) {
                $DB->delete_records('mnetservice_enrol_enrolments', array('hostid'=>$mnethostid, 'remotecourseid'=>$remotecourseid));
            } else {
                list($isql, $params) = $DB->get_in_or_equal(array_keys($list), SQL_PARAMS_NAMED, 'param', false);
                $params['hostid'] = $mnethostid;
                $params['remotecourseid'] = $remotecourseid;
                $select = "hostid = :hostid AND remotecourseid = :remotecourseid AND id $isql";
                $DB->delete_records_select('mnetservice_enrol_enrolments', $select, $params);
            }

            // store the timestamp of the recent fetch, can be used for cache invalidate purposes
            set_config('lastfetchenrolments', time(), 'mnetservice_enrol');
            // local cache successfully updated
            return true;

        } else {
            return serialize($request->error);
        }
    }

    /**
     * Send request to enrol our user to the remote course
     *
     * Updates our remote enrolments cache if the enrolment was successful.
     *
     * @uses mnet_xmlrpc_client Invokes XML-RPC request
     * @param object $user our user
     * @param object $remotecourse record from mnetservice_enrol_courses table
     * @return true|string true if success, error message from the remote host otherwise
     */
    public function req_enrol_user(stdclass $user, stdclass $remotecourse) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mnet/xmlrpc/client.php');

        $peer = new mnet_peer();
        $peer->set_id($remotecourse->hostid);

        $request = new mnet_xmlrpc_client();
        $request->set_method('enrol/mnet/enrol.php/enrol_user');
        $request->add_param(mnet_strip_user((array)$user, mnet_fields_to_send($peer)));
        $request->add_param($remotecourse->remoteid);

        if ($request->send($peer) === true) {
            if ($request->response === true) {
                // cache the enrolment information in our table
                $enrolment                  = new stdclass();
                $enrolment->hostid          = $peer->id;
                $enrolment->userid          = $user->id;
                $enrolment->remotecourseid  = $remotecourse->remoteid;
                $enrolment->enroltype       = 'mnet';
                // $enrolment->rolename not known now, must be re-fetched
                // $enrolment->enroltime not known now, must be re-fetched
                $DB->insert_record('mnetservice_enrol_enrolments', $enrolment);
                return true;

            } else {
                return serialize(array('invalid response: '.print_r($request->response, true)));
            }

        } else {
            return serialize($request->error);
        }
    }

    /**
     * Send request to unenrol our user from the remote course
     *
     * Updates our remote enrolments cache if the unenrolment was successful.
     *
     * @uses mnet_xmlrpc_client Invokes XML-RPC request
     * @param object $user our user
     * @param object $remotecourse record from mnetservice_enrol_courses table
     * @return true|string true if success, error message from the remote host otherwise
     */
    public function req_unenrol_user(stdclass $user, stdclass $remotecourse) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mnet/xmlrpc/client.php');

        $peer = new mnet_peer();
        $peer->set_id($remotecourse->hostid);

        $request = new mnet_xmlrpc_client();
        $request->set_method('enrol/mnet/enrol.php/unenrol_user');
        $request->add_param($user->username);
        $request->add_param($remotecourse->remoteid);

        if ($request->send($peer) === true) {
            if ($request->response === true) {
                // clear the cached information
                $DB->delete_records('mnetservice_enrol_enrolments',
                    array('hostid'=>$peer->id, 'userid'=>$user->id, 'remotecourseid'=>$remotecourse->remoteid, 'enroltype'=>'mnet'));
                return true;

            } else {
                return serialize(array('invalid response: '.print_r($request->response, true)));
            }

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

/**
 * Selector of our users enrolled into remote course via enrol_mnet plugin
 */
class mnetservice_enrol_existing_users_selector extends user_selector_base {
    /** @var id of the MNet peer */
    protected $hostid;
    /** @var id of the course at the remote server */
    protected $remotecourseid;

    public function __construct($name, $options) {
        $this->hostid = $options['hostid'];
        $this->remotecourseid = $options['remotecourseid'];
        parent::__construct($name, $options);
    }

    /**
     * Find our users currently enrolled into the remote course
     *
     * @param string $search
     * @return array
     */
    public function find_users($search) {
        global $DB;

        list($wherecondition, $params)  = $this->search_sql($search, 'u');
        $params['hostid']               = $this->hostid;
        $params['remotecourseid']       = $this->remotecourseid;

        $fields      = "SELECT ".$this->required_fields_sql("u");
        $countfields = "SELECT COUNT(1)";

        $sql = "          FROM {user} u
                          JOIN {mnetservice_enrol_enrolments} e ON e.userid = u.id
                         WHERE e.hostid = :hostid AND e.remotecourseid = :remotecourseid
                               AND e.enroltype = 'mnet'
                               AND $wherecondition";
        $order = "    ORDER BY u.lastname ASC, u.firstname ASC";

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('enrolledusersmatching', 'enrol', $search);
        } else {
            $groupname = get_string('enrolledusers', 'enrol');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['hostid'] = $this->hostid;
        $options['remotecourseid'] = $this->remotecourseid;
        $options['file'] = 'mnet/service/enrol/locallib.php';
        return $options;
    }
}

/**
 * Selector of our users who could be enrolled into a remote course via their enrol_mnet
 */
class mnetservice_enrol_potential_users_selector extends user_selector_base {
    /** @var id of the MNet peer */
    protected $hostid;
    /** @var id of the course at the remote server */
    protected $remotecourseid;

    public function __construct($name, $options) {
        $this->hostid = $options['hostid'];
        $this->remotecourseid = $options['remotecourseid'];
        parent::__construct($name, $options);
    }

    /**
     * Find our users who could be enrolled into the remote course
     *
     * Our users must have 'moodle/site:mnetlogintoremote' capability assigned.
     * Remote users, guests, deleted and not confirmed users are not returned.
     *
     * @param string $search
     * @return array
     */
    public function find_users($search) {
        global $CFG, $DB;

        $systemcontext = get_system_context();
        $userids = get_users_by_capability($systemcontext, 'moodle/site:mnetlogintoremote', 'u.id');

        if (empty($userids)) {
            return array();
        }

        list($usql, $uparams) = $DB->get_in_or_equal(array_keys($userids), SQL_PARAMS_NAMED, 'uid');

        list($wherecondition, $params) = $this->search_sql($search, 'u');

        $params = array_merge($params, $uparams);
        $params['hostid'] = $this->hostid;
        $params['remotecourseid'] = $this->remotecourseid;
        $params['mnetlocalhostid'] = $CFG->mnet_localhost_id;

        $fields      = "SELECT ".$this->required_fields_sql("u");
        $countfields = "SELECT COUNT(1)";

        $sql = "          FROM {user} u
                         WHERE $wherecondition
                               AND u.mnethostid = :mnetlocalhostid
                               AND u.id $usql
                               AND u.id NOT IN (SELECT e.userid
                                                  FROM {mnetservice_enrol_enrolments} e
                                                 WHERE (e.hostid = :hostid AND e.remotecourseid = :remotecourseid))";

        $order = "    ORDER BY u.lastname ASC, u.firstname ASC";

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('enrolcandidatesmatching', 'enrol', $search);
        } else {
            $groupname = get_string('enrolcandidates', 'enrol');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['hostid'] = $this->hostid;
        $options['remotecourseid'] = $this->remotecourseid;
        $options['file'] = 'mnet/service/enrol/locallib.php';
        return $options;
    }
}
