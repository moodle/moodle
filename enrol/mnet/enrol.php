<?php
// The following flags are set in the configuration
// $config->allow_allcourses:       expose all courses to external enrolment
// $config->allowed_categories:     serialised array of courses allowed
// $config->allowed_courses:        serialised array of courses allowed

class enrolment_plugin_mnet {

    /// Override the base config_form() function
    function config_form($frm) {
        global $CFG;

       $vars = array('enrol_mnet_allow_allcourses',
                     'enrol_mnet_allowed_categories',
                     'enrol_mnet_allowed_courses');

        foreach ($vars as $var) {
            if (!isset($frm->$var)) {
                $frm->$var = '';
            }
        }

        $mnethosts = $this->list_remote_servers();

        include ("$CFG->dirroot/enrol/mnet/config.html");
    }


    /// Override the base process_config() function
    function process_config($config) {

        if (!isset($config->enrol_mnet_allow_allcourses)) {
            $config->enrol_mnet_allow_allcourses = false;
        }
        set_config('enrol_mnet_allow_allcourses', $config->enrol_mnet_allow_allcourses);

        if (!isset($config->enrol_mnet_allowed_categories)) {
            $config->enrol_mnet_allowed_categories = '';
        }
        set_config('enrol_mnet_allowed_categories', $config->enrol_mnet_allowed_categories);

        if (!isset($config->enrol_mnet_allowed_courses)) {
            $config->enrol_mnet_allowed_courses = '';
        }
        set_config('enrol_mnet_allowed_courses', $config->enrol_mnet_allowed_courses);

        return true;

    }

    /// Override the get_access_icons() function
    function get_access_icons($course) {
    }

    /**
     * Override the base cron() function
     */
    function cron() {
        $this->process_role_updates();
    } // end of cron()



    /***
     *** MNET functions
     ***
     ***/
    function mnet_publishes() {
        
        $enrol = array();
        $enrol['name']        = 'mnet_enrol'; // Name & Description go in lang file
        $enrol['apiversion']  = 1;
        $enrol['methods'] = array('available_courses','user_enrolments', 'enrol_user',
                'unenrol_user', 'course_enrolments', 'abridge_course_enrolments',
                'assign_role_user', 'get_default_role', 'get_allocatable_roles');
        return array($enrol);
    }

    /**
    * Does Foo
    *
    * @param string $username   The username
    * @param int    $mnethostid The id of the remote mnethost
    * @return bool              Whether the user can login from the remote host
    */
    function available_courses() {
        global $CFG, $DB;

        if (!empty($CFG->enrol_mnet_allow_allcourses)) {

            $query =
            "SELECT
                co.id          AS remoteid,
                ca.id          AS cat_id,
                ca.name        AS cat_name,
                ca.description AS cat_description,
                co.sortorder,
                co.fullname,
                co.shortname,
                co.idnumber,
                co.summary,
                co.startdate,
                co.cost,
                co.currency,
                co.defaultrole AS defaultroleid,
                r.name         AS defaultrolename 
            FROM
                {course_categories} ca
            JOIN
                {course} co ON ca.id = co.category
            LEFT JOIN
                {role} r ON r.id = co.defaultrole
            WHERE
                co.visible = 1 AND
                co.enrollable = 1 AND
                coalesce(co.mnetpeer, 0) = 0 AND
                coalesce(co.remotecourseid, 0) = 0
            ORDER BY
                sortorder ASC
                ";

            return $DB->get_records_sql($query);

        } elseif (!empty($CFG->enrol_mnet_allowed_categories)) {

            $cats = preg_split('/\s*,\s*/', $CFG->enrol_mnet_allowed_categories);
            for ($n=0;$n < count($cats); $n++) {
                $cats[$n] = " ca.path LIKE '%/" . (int)$cats[$n] . "/%' ";
            }
            $cats = join(' OR ', $cats);

            $query =
            "SELECT
                id, name
            FROM
                {course_categories} ca
            WHERE
                ca.id IN ({$CFG->enrol_mnet_allowed_categories})
                OR ( $cats )
            ORDER BY
                path ASC,
                depth ASC
                ";
            unset($cats);

            $rs = $DB->get_records_sql($query);

            if (!empty($rs)) {
                $cats = array_keys($rs);
            }
            $where = ' AND ( ca.id IN (' . join(',', $cats) . ') ';


            if (!empty($CFG->enrol_mnet_allowed_courses)) {
                $where .=  " OR co.id in ({$CFG->enrol_mnet_allowed_courses}) ";
            }

            $where .= ')';

            $query =
            "SELECT
                co.id as remoteid,
                ca.id as cat_id,
                ca.name as cat_name,
                ca.description as cat_description,
                co.sortorder,
                co.fullname,
                co.shortname,
                co.idnumber,
                co.summary,
                co.startdate,
                co.cost,
                co.currency,
                co.defaultrole as defaultroleid,
                r.name
            FROM
                {course_categories} ca
            JOIN
                {course} co ON ca.id = co.category
            LEFT JOIN
                {role} r ON r.id = co.defaultrole
            WHERE
                co.visible = 1 AND
                co.enrollable = 1 $where AND
                coalesce(co.mnetpeer, 0) = 0 AND
                coalesce(co.remotecourseid, 0) = 0
            ORDER BY
                sortorder ASC
                ";

            return $DB->get_records_sql($query);

        } elseif (!empty($CFG->enrol_mnet_allowed_courses)) {

            $query =
                "SELECT
                    co.id as remoteid,
                    ca.id as cat_id,
                    ca.name as cat_name,
                    ca.description as cat_description,
                    co.sortorder,
                    co.fullname,
                    co.shortname,
                    co.idnumber,
                    co.summary,
                    co.startdate,
                    co.cost,
                    co.currency,
                    co.defaultrole as defaultroleid,
                    r.name
                FROM
                    {course_categories} ca
                JOIN
                    {course} co ON ca.id = co.category
                LEFT JOIN
                    {role} r ON r.id = co.defaultrole
                WHERE
                    co.visible = 1 AND
                    co.enrollable = 1 AND
                    co.id IN ({$CFG->enrol_mnet_allowed_courses}) AND
                    coalesce(co.mnetpeer, 0) = 0 AND
                    coalesce(co.remotecourseid, 0) = 0
                ORDER BY
                    sortorder ASC
                    ";

            return $DB->get_records_sql($query);

        }

        return array();
    }

    /**
     * 
     */
    function user_enrolments($userid) {
        return array();
    }

    /**
     * Get a list of users from the client server who are enrolled in a course
     *
     * @param   int     $courseid   The Course ID
     * @param   string  $roles      Comma-separated list of role shortnames
     * @return  array               Array of usernames who are homed on the 
     *                              client machine
     */
    function course_enrolments($courseid, $roles = '') {
        global $MNET_REMOTE_CLIENT, $CFG, $DB;

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            return 'no course';
            //error("That's an invalid course id");
        }

        $context = get_context_instance(CONTEXT_COURSE, $courseid);

        $sql = "
                SELECT
                    u.id,
                    u.username,
                    a.enrol,
                    a.timemodified,
                    r.name,
                    r.shortname
                FROM
                    {role_assignments} a,
                    {role} r,
                    {user} u
                WHERE
                    a.contextid = {$context->id} AND
                    a.roleid = r.id AND
                    a.userid = u.id AND
                    u.mnethostid = '{$MNET_REMOTE_CLIENT->id}'
                    ";

        if(!empty($roles)) {
            // $default_role = get_default_course_role($course); ???
            $sql .= " AND
                    a.roleid in ('".str_replace(',',  "', '",  $roles)."')";
        } 

        $enrolments = $DB->get_records_sql($sql);

        $returnarray = array();
        foreach($enrolments as $user) {
            $returnarray[$user->username] = array('enrol' => $user->enrol, 
                                                  'timemodified' => $user->timemodified, 
                                                  'shortname' => $user->shortname, 
                                                  'username' => $user->username,
                                                  'name' => $user->name);
        }
        return $returnarray;
    }

    /**
    * Enrols user to course with the default role
    *
    * @param string $username   The username of the remote use
    * @param int    $courseid   The id of the local course
    * @return bool              Whether the enrolment has been successful
    */
    function enrol_user($user, $courseid) {
        global $MNET, $MNET_REMOTE_CLIENT, $DB;

        $userrecord = $DB->get_record('user',array('username'=>$user['username'], 'mnethostid'=>$MNET_REMOTE_CLIENT->id));

        if ($userrecord == false) {
            // We should at least be checking that we allow the remote
            // site to create users
            // TODO: more rigour here thanks!
            $userrecord = new stdClass();
            $userrecord->username   = $user['username'];
            $userrecord->email      = $user['email'];
            $userrecord->firstname  = $user['firstname'];
            $userrecord->lastname   = $user['lastname'];
            $userrecord->mnethostid = $MNET_REMOTE_CLIENT->id;

            if ($userrecord->id = $DB->insert_record('user', $userrecord)) {
                $userrecord = $DB->get_record('user', array('id'=>$userrecord->id));
            } else {
                // TODO: Error out
                return false;
            }
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            // TODO: Error out
            return false;
        }

        $courses = $this->available_courses();

        if (!empty($courses[$courseid])) {
            if (enrol_into_course($course, $userrecord, 'mnet')) {
                return true;
            }
        }
        return false;
    }

    /**
    * Unenrol a user from a course
    *
    * @param string $username   The username
    * @param int    $courseid   The id of the local course
    * @return bool              Whether the user can login from the remote host
    */
    function unenrol_user($username, $courseid) {
        global $MNET_REMOTE_CLIENT, $DB;

        $userrecord = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$MNET_REMOTE_CLIENT->id));

        if ($userrecord == false) {
            return false;
            // TODO: Error out
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            return false;
            // TODO: Error out
        }

        if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
            return false;
            // TODO: Error out (Invalid context)
        }

        // Are we a *real* user or the shady MNET Daemon?
        // require_capability('moodle/role:assign', $context, NULL, false);

        if (!role_unassign(0, $userrecord->id, 0, $context->id)) {
            print_error("unenrolerror");
        }

        return true;
    }

    /***
     *** Client RPC behaviour
     ***
     ***
     ***/

    /**
    * Lists remote servers we use 'enrol' services from.
    *
    * @return array
    */
    function list_remote_servers() {
        global $CFG, $DB;

        $sql = "
            SELECT DISTINCT 
                h.id, 
                h.name
            FROM 
                {mnet_service} s
                INNER JOIN {mnet_host2service} h2s on h2s.serviceid = s.id
                INNER JOIN {mnet_host} h on h.id = h2s.hostid
            WHERE
                s.name = 'mnet_enrol' AND
                h2s.subscribe = 1";

        $res = $DB->get_records_sql($sql);
        if (is_array($res)) {
            return $res;
        } else {
            return array();
        }
    }

    /**
    * Does Foo
    *
    * @param int    $mnethostid The id of the remote mnethost
    * @return array              Whether the user can login from the remote host
    */
    function fetch_remote_courses($mnethostid) {
        global $CFG, $USER, $MNET, $DB;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // get the Service Provider info
        $mnet_sp = new mnet_peer();
        $mnet_sp->set_id($mnethostid);

        // set up the RPC request
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/available_courses');

        // Initialise $message
        $message = '';

        // TODO: cache for a while (10 minutes?)

        // Thunderbirds are go! Do RPC call and store response
        if ($mnetrequest->send($mnet_sp) === true) {
            $courses = $mnetrequest->response;

            // get the cached courses key'd on remote id - only need remoteid and id fields
            $cachedcourses = $DB->get_records('mnet_enrol_course', array('hostid'=>$mnethostid), 'remoteid', 'remoteid, id' );

            // Update cache and transform $courses into objects
            // in-place for the benefit of our caller...
            for ($n=0;$n<count($courses);$n++) {

                $course = &$courses[$n];

                // add/update cached data in mnet_enrol_courses
                // sanitise data 
                $course = (object)$course;
                $course->remoteid        = (int)$course->remoteid;
                $course->hostid          = $mnethostid;
                $course->cat_id          = (int)$course->cat_id;
                $course->sortorder       = (int)$course->sortorder ;
                $course->startdate       = (int)$course->startdate;
                $course->cost            = (int)$course->cost;
                $course->defaultroleid   = (int)$course->defaultroleid;

                // sanitise strings for DB NOTE - these are not sane
                // for printing, so we'll use a different object
                $dbcourse = clone($course);
                $dbcourse->cat_name        = substr($dbcourse->cat_name,0,255);
                $dbcourse->cat_description = $dbcourse->cat_description;
                $dbcourse->fullname        = substr($dbcourse->fullname,0,254);
                $dbcourse->shortname       = substr($dbcourse->shortname,0,15);
                $dbcourse->idnumber        = substr($dbcourse->idnumber,0,100);
                $dbcourse->summary         = $dbcourse->summary;
                $dbcourse->currency        = substr($dbcourse->currency,0,3);
                $dbcourse->defaultrolename = substr($dbcourse->defaultrolename,0,255);

                // insert or update
                if (empty($cachedcourses[$course->remoteid])) {
                    $course->id = $DB->insert_record('mnet_enrol_course', $dbcourse);
                } else {
                    $course->id = $cachedcourses[$course->remoteid]->id;
                    $cachedcourses[$course->remoteid]->seen=true;
                    $DB->update_record('mnet_enrol_course', $dbcourse);
                }
                // free tmp obj
                unset($dbcourse);
            }

            // prune stale data from cache
            if (!empty($cachedcourses)) {
                $stale = array();
                foreach ($cachedcourses as $id => $cc) {
                    // TODO: maybe set a deleted flag instead
                    if (empty($cc->seen)) {
                        $stale[] = $cc->id;
                    }
                }
                if (!empty($stale)) {
                    $DB->delete_records_select('mnet_enrol_course', 'id IN ('.join(',',$stale).')');
                }
            }

            return $courses;
        } else {
            foreach ($mnetrequest->error as $errormessage) {
                list($code, $errormessage) = array_map('trim',explode(':', $errormessage, 2));
                $message .= "ERROR $code:<br/>$errormessage<br/>";
            }
            print_error("rpcerror", '', '', $message);
        }
        return false;
    }

    /**
    * Does Foo
    *
    * @param int    $mnethostid The id of the remote mnethost
    * @return array              Whether the user can login from the remote host
    */
    function req_enrol_user($userid, $courseid) {
        global $CFG, $USER, $MNET, $DB;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // Prepare a basic user record
        // in case the remote host doesn't have it
        $user = $DB->get_record('user', array('id'=>$userid), 'username, email, firstname, lastname');
        $user = (array)$user;

        $course = $DB->get_record('mnet_enrol_course', array('id'=>$courseid));

        // get the Service Provider info
        $mnet_sp = new mnet_peer();
        $mnet_sp->set_id($course->hostid);

        // set up the RPC request
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/enrol_user');
        $mnetrequest->add_param($user);
        $mnetrequest->add_param($course->remoteid);

        // Thunderbirds are go! Do RPC call and store response
        if ($mnetrequest->send($mnet_sp) === true) {
            if ($mnetrequest->response == true) {
                // now store it in the mnet_enrol_assignments table
                $assignment = new StdClass;
                $assignment->userid = $userid;
                $assignment->hostid = $course->hostid;
                $assignment->courseid = $course->id;
                $assignment->enroltype = 'mnet';
                // TODO: other fields
                if ($DB->insert_record('mnet_enrol_assignments', $assignment)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
    * Does Foo
    *
    * @param int    $mnethostid The id of the remote mnethost
    * @return array              Whether the user can login from the remote host
    */
    function req_unenrol_user($userid, $courseid) {
        global $CFG, $USER, $MNET, $DB;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // in case the remote host doesn't have it
        $username = $DB->get_field('user', 'username', array('id'=>$userid));

        $course = $DB->get_record('mnet_enrol_course', array('id'=>$courseid));

        // get the Service Provider info
        $mnet_sp = new mnet_peer();
        $mnet_sp->set_id($course->hostid);

        // set up the RPC request
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/unenrol_user');
        $mnetrequest->add_param($username);
        $mnetrequest->add_param($course->remoteid);

        // TODO - prevent removal of enrolments that are not of
        // type mnet...


        // Thunderbirds are go! Do RPC call and store response
        if ($mnetrequest->send($mnet_sp) === true) {
            if ($mnetrequest->response == true) {
                // remove enrolment cached in mnet_enrol_assignments
                $DB->delete_records_select('mnet_enrol_assignments',
                                      "userid=? AND courseid=?", array($userid, $course->id));

                return true;
            }
        }
        return false;
    }

    /**
    * Unassign all role assignments (relating to calling mnet peer) in a course context - except specified list
    *
    * @param array keepusers users who should not be unenrolled from specified course
    * @param int $courseid the course we should be unenrolling users from.
    */
    function abridge_course_enrolments($keepusersarray, $courseid) {
        global $MNET_REMOTE_CLIENT, $CFG, $DB;

        // Check that detail about the remote mnet peer is available:
        if (empty($MNET_REMOTE_CLIENT->id)) {
            return false;
        }

        // Get a list of (mnet) role assignments in the course where the user is from the selected mnet peer
        // (and not in keepusers)
        $params = array();
        $delenrolmentssql = 'SELECT ' .
                            ' ra.id, ra.userid, ra.contextid ' .
                            'FROM ' .
                            "{context} c " .
                            " INNER JOIN {role_assignments} ra on ra.contextid = c.id " .
                            " INNER JOIN {user} u on u.id = ra.userid " .
                            'WHERE c.contextlevel = 50 ' .
                            ' AND c.instanceid = ? ' .
                            ' AND ra.enrol = \'mnet\' ' .
                            ' AND u.mnethostid = ' . $MNET_REMOTE_CLIENT->id;
        if (!empty($keepusersarray)) {
            list($sqlfragment, $params) = $DB->get_in_or_equal($keepusersarray, SQL_PARAMS_QM, 'param0000', FALSE);
            $delenrolmentssql .= " AND u.username $sqlfragment ";
        }
        array_unshift($params, $courseid);
        $delenrolments = $DB->get_records_sql($delenrolmentssql, $params);

        if ($delenrolments === false OR !is_array($delenrolments)) {
            return false;
        }

        foreach ($delenrolments as $enrolment) {
            if (!role_unassign(0, $enrolment->userid, 0, $enrolment->contextid)) {
                return false;
            }
        }
        return true;
    }

    /*
    * Step through the mnet_role_management_queue sending role updates to mnet peers
    * @return bool true if we had some success
    */
    function process_role_updates() {
        global $CFG, $MNET, $DB;
        echo "Processing mnet role management queue\n";
        $course2contexts = $this->get_queue_relevant_contexts();
        if (empty($course2contexts)) {
            //no courses of interest, nothing for us to do
            return true;
        }

        //Get current mnet cp course (if any) for courses of interest
        $cpdetails = $this->map_courses_to_cp(array_keys($course2contexts));
        //Get current local roles that are relevant in relation to each mnet peer
        $peersrolemappings = $this->get_mnet_role_mappings($cpdetails);
        foreach ($peersrolemappings as $peerid => $peerrolemappings) {
            if (is_array($peerrolemappings)) {
                $rolesmapped[$peerid] = array_keys($peerrolemappings);
            }
        }

        //Allow the role update process to run for an arbitary amount of time.
        $starttime = time();
        $timelimit = $starttime + 100;
        $queueitemssql = 'SELECT * FROM {mnet_role_management_queue} ORDER BY id ASC ';
        $queueitems = $DB->get_records_sql($queueitemssql);
        echo count($queueitems) . " items found ";
        $updatecount = 0;
        $rowcount = 0;
        while (time() < $timelimit) {
            if (empty($queueitems)) {
                break;
            }
            $queueitem = array_shift($queueitems);
            if (empty($queueitem->mnetpeer) || empty($queueitem->remotecourseid)) {
                // If peer & remote course are not defined, this is a basic create/update request
                // determine mnetpeer and remotecourseid based on record in course table
                $queueitem->mnetpeer = $cpdetails[$queueitem->localcourse]->mnetpeer;
                $queueitem->remotecourseid = $cpdetails[$queueitem->localcourse]->remotecourseid;
            }
            if (!empty($rolesmapped[$queueitem->mnetpeer])) {
                $remoteroleid = $this->map_user_role($queueitem->userid, $rolesmapped[$queueitem->mnetpeer],
                        $course2contexts[$queueitem->localcourse], $peersrolemappings[$queueitem->mnetpeer]);

                // Enrol the user on the remote peer
                $updateresult = $this->update_role_remote_course($queueitem->userid, $queueitem->mnetpeer, $queueitem->remotecourseid, $remoteroleid);
            } else {
                // No local roles map to anything on this peer - nothing to do:
                $updateresult = true;
            }
            if ($updateresult) {
                $deleteresult = $DB->delete_records('mnet_role_management_queue', array('id' => $queueitem->id));
                if (!empty($deleteresult)) {
                    ++$updatecount;
                    if (++$rowcount > 40) {
                        echo "\n";
                        $rowcount = 1;
                    }
                    echo ".";
                } else {
                    echo "\nWarning - unable to remove role update from queue this is an unsustainable problem";
                }
            } else {
                echo "Problem encountered - if this problem persists contact your mnet administrator\n";
                return false;
            }
        }
        echo "\n";
        return true;
    }

    /**
    * Update a local user's role in a remote course
    * @param userid integer id of the user who we want the role assigned to
    * @param mnethostid integer id of the mnet host the course is hosted on
    * @param remotecourseid the id number of the course on the remote moodle
    * @return bool true on success, false on failure
    */
    function update_role_remote_course($userid, $mnethostid, $remotecourseid, $remoteroleid) {
        global $DB, $CFG, $MNET;

        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
        // Create content-provider object
        $mnet_cp = new mnet_peer();
        $mnet_cp->set_id($mnethostid);

        // Set up a minimilist user record for passing to remote function
        $user = $DB->get_record('user',array('id' => $userid));
        if ($user->mnethostid != $CFG->mnet_localhost_id) {
            // If user is not local, force unenrolment
            $remoteroleid = FALSE;
        }

        $minimalistuser = new stdclass;
        $minimalistuser->id = $user->id;
        $minimalistuser->username = $user->username;
        $minimalistuser->firstname = $user->firstname;
        $minimalistuser->lastname = $user->lastname;
        $minimalistuser->email = $user->email;

        if ($remoteroleid === FALSE) {
            return $this->unenrol_user_remote($minimalistuser, $remotecourseid, $mnet_cp);
        } elseif (empty($remoteroleid)) {
            //Talking to an older system - can only enrol course's default role.
            return $this->enrol_user_remote($minimalistuser, $remotecourseid, $mnet_cp);
        } else {
            return $this->full_enrol_user_remote($user, $remotecourseid, $mnet_cp, $remoteroleid);
        }
    }

    /*
    * Retreive information about contexts relevant to courses listed in the mnet_role_management_queue
    * @return array keyed by localcourseid, containging array of contexts (course and its parents)
    */
    function get_queue_relevant_contexts() {
        global $DB;
        $coursecontextssql =
                'SELECT ' .
                ' DISTINCT localcourse, ' .
                ' c.path ' .
                'FROM {mnet_role_management_queue} que ' .
                ' INNER JOIN {context} c on c.instanceid=que.localcourse and c.contextlevel=50';
        $courses = $DB->get_records_sql($coursecontextssql);
        if (empty($courses)) {
            return false;
        }
        foreach ($courses as $course) {
            $course2contexts[$course->localcourse] = explode('/',substr($course->path,1));
        }
        return $course2contexts;
    }

    /*
    * Retreive information about the mnet content provider for specified courses
    * @param array courses id number of local courses
    * @return array of objects, keyed by localcourseid
    */
    function map_courses_to_cp($courses) {
        global $DB;
        //Get current mapping of localcourses to remote cp:
        list($coursefragment, $params) = $DB->get_in_or_equal($courses);
        $cpdetailsql =
                'SELECT ' .
                ' id, mnetpeer, remotecourseid ' .
                'FROM {course} WHERE id ' . $coursefragment;
        $cpdetails = $DB->get_records_sql($cpdetailsql, $params);
        return $cpdetails;
    }

    /**
    * form an array linking local and remote roles for each mnet peer
    *
    * @param array details of localcourses, mnet peer, and mnetcourseid
    * @return array
    */
    function get_mnet_role_mappings($cpdetails) {
        global $DB;
        foreach ($cpdetails as $cpdetail) {
            if (!isset($rolemappings[$cpdetail->mnetpeer])) {
                //Get current all rolemappings for specified mnet peer
                $rolemapsql = 'SELECT localrole, remoterole '.
                        'FROM {mnet_role_mapping} '.
                        'WHERE mnethost = ?';
                $params = array($cpdetail->mnetpeer);
                $rolemaps = $DB->get_records_sql($rolemapsql, $params);
                if (!empty($rolemaps)) {
                    foreach ($rolemaps as $rolemap) {
                        $rolemappings[$cpdetail->mnetpeer][$rolemap->localrole] = $rolemap->remoterole;
                    }
                } else {
                    $rolemappings[$cpdetail->mnetpeer] = null;
                }
            }
        }
        return $rolemappings;
    }

    /**
    * Assign role to remote mnet user on specified local course
    *
    * @param array $user basic identity information about user getting role assigned
    * @param int $courseid the id of the course the user is being assigned to
    * @param int $roleid the role to be assigned
    * @return bool Result of the role assignment
    */
    function assign_role_user($user, $courseid, $roleid) {
        global $MNET, $MNET_REMOTE_CLIENT, $DB;
        $context = $DB->get_record('context', array('contextlevel' => 50, 'instanceid' => $courseid));
        $user = $DB->get_record('user', array('username' => $user['username'], 'mnethostid' => $MNET_REMOTE_CLIENT->id));
        if (empty($context) || empty($user)) {
            return false;
        }

        // Check that we allow this mnet client to assign this role
        $published = $DB->get_record('mnet_role_published', array('mnethost' => $MNET_REMOTE_CLIENT->id, 'localrole' => $roleid));
        if (empty($published)) {
            return false;
        }
        // Check that this course is shared over mnet
        if (!$this->course_published($courseid)) {
            return false;
        }

        // Mnet peers can only assign a user to one role at a time
        $existingrolessql =
                'SELECT * ' .
                'FROM {role_assignments} ' .
                'WHERE userid = ? AND contextid = ? AND enrol = \'mnet\'';
        $params = array($user->id, $context->id);
        $existingroles = $DB->get_records_sql($existingrolessql, $params);
        if (!empty($existingroles)) {
            foreach ($existingroles as $existingrole) {
                if ($existingrole->roleid != $roleid) {
                    role_unassign($existingrole->roleid, $user->id, 0, $context->id, 'mnet');
                }
            }
        }
        return role_assign($roleid, $user->id, 0, $context->id, 0, 0, 0, 'mnet');
    }

    /**
    * Determine if specified courseid is published over mnet
    *
    * @param integer $courseid
    * @return bool true if the course is available over mnet, false otherwise
    */
    function course_published ($courseid) {
        global $CFG, $DB;
        //If the course in question is itself a shell course, it can not be published
        $course = $DB->get_record('course', array('id' => $courseid));
        if (!empty($course->mnetpeer) && !empty($course->remotecourseid)) {
            return false;
        }
        //First check to see if all courses are allowed to be shared:
        if ($CFG->enrol_mnet_allow_allcourses) {
            return true;
        }

        //Check to see if the specified course is named in the list of shared courses
        if (!empty($CFG->enrol_mnet_allowed_courses)) {
            $sharedcourses = explode(',', $CFG->enrol_mnet_allowed_courses);
            if (in_array($courseid,$sharedcourses)) {
                return true;
            }
        }

        //Check to see if any of the course's parent categories are shared
        if (!empty($CFG->enrol_mnet_allowed_categories)) {
            $categorysql =
                    'SELECT ca.id, ca.path ' .
                    'FROM {course} co ' .
                    ' INNER JOIN {course_categories} ca ON ca.id=co.category '.
                    'WHERE co.id = ?';
            $params = array($courseid);
            $category = $DB->get_record_sql($categorysql,$params);
            if (empty($category)) {
                return false;
            }

            $sharedcategories = explode(',',$CFG->enrol_mnet_allowed_categories);
            $categories = explode('/',substr($category->path,1));
            foreach ($categories as $category) {
                if (in_array($category, $sharedcategories)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * Map roles that a user has assigned to them relevant to course context (and parent contexts)
    * to a role we can allocate on the course's content provider
    * @param userid integer the local user's identifier
    * @param relevantroles array list of local roleids that we may want to translate to remote roleid
    * @param relevantcontexts array list of contexts where relevant roles might be assigned
    * @param peerrolemappings array keyed on local roleid, value is remote role id.
   */
    function map_user_role ($userid, $relevantroles, $relevantcontexts, $peerrolemappings) {
        global $DB;
        if (!empty($relevantroles)) {
            list($contextfragment, $contextids) = $DB->get_in_or_equal($relevantcontexts);
            list($rolefragment, $roleids) = $DB->get_in_or_equal($relevantroles);
            $roletoassignsql =
                    'SELECT ra.roleid, ra.roleid as roleid2 ' .
                    'FROM {role_assignments} ra ' .
                    ' INNER JOIN {context} con ON con.id=ra.contextid ' .
                    'WHERE con.id ' . $contextfragment .
                    '  AND ra.roleid ' . $rolefragment .
                    '  AND ra.userid = ? ' .
                    'ORDER BY con.contextlevel desc, ra.timemodified desc ' .
                    'LIMIT 1';
            $params = array_merge($contextids, $roleids);
            $params[] = $userid;
            $localrolestoassign = $DB->get_records_sql($roletoassignsql, $params);
            if (!empty($localrolestoassign)) {
                $localroletoassign = array_shift($localrolestoassign);
            } else {
                $localroletoassign = NULL;
            }
        } else {
            $localroletoassign = NULL;
        }

        // Determine the role to be assigned on the remote peer based on the user's role in
        // the local course, and the role mappings for the relevant peer
        if (empty($localroletoassign)) {
            // No local role means that this is an unenrolment
            return FALSE;
        } elseif (!isset($peerrolemappings[$localroletoassign->roleid])) {
            // User has local role, but this is not mapped to anything on the remote peer
            // - this is also an unenrolment
            return FALSE;
        } else {
            return $peerrolemappings[$localroletoassign->roleid];
        }
    }

    /**
    * Unenrol user from course on mnet peer
    * @param minimalistuser object user object with only basic properties
    * @param remotecourseid integer courseid on the remote peer
    * @param mnetcp object basic peer info
    * @return bool true on successful unenrolment
   */
    function unenrol_user_remote($minimalistuser, $remotecourseid, $mnet_cp) {
        global $CFG, $MNET;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
        //Unenrol user from remote course
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/unenrol_user');
        $mnetrequest->add_param($minimalistuser->username);
        $mnetrequest->add_param($remotecourseid);
        if ($mnetrequest->send($mnet_cp) === true) {
            if($mnetrequest->response) {
                return true;
            }
            // Legacy mnet considers request to unenrol 'nonexistant' user an error
            // Check for this situ by creating usr by enroling in dummy course,
            // Then repeating the unenrol request.
            $mnetrequest = new mnet_xmlrpc_client();
            $mnetrequest->set_method('enrol/mnet/enrol.php/enrol_user');
            $mnetrequest->add_param($minimalistuser);
            $mnetrequest->add_param(0); //dummy remote course id
            // Ignore result, we expect it to fail due to dummy remote course id
            $mnetrequest->send($mnet_cp);

            $mnetrequest = new mnet_xmlrpc_client();
            $mnetrequest->set_method('enrol/mnet/enrol.php/unenrol_user');
            $mnetrequest->add_param($minimalistuser->username);
            $mnetrequest->add_param($remotecourseid);
            if ($mnetrequest->send($mnet_cp) === true) {
                return $mnetrequest->response;
            }
        }
    }

    /**
    * Enrol user into course on mnet peer
    * @param minimalistuser object user object with only basic properties
    * @param remotecourseid integer courseid on the remote peer
    * @param mnetcp object basic peer info
    * @return bool true on successful enrolment
   */
    function enrol_user_remote($minimalistuser, $remotecourseid, $mnet_cp) {
        global $CFG, $MNET;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
        //Unenrol user from remote course
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/enrol_user');
        $mnetrequest->add_param($minimalistuser);
        $mnetrequest->add_param($remotecourseid);
        if ($mnetrequest->send($mnet_cp) === true) {
            return $mnetrequest->response;
        }
    }

    /**
    * Create user on remote mnet peer and assign remote role againnst specified course
    * @param user object properties about the user to create/assign role
    * @param remotecourseid integer courseid on the remote peer
    * @param mnetcp object basic peer info
    * @return bool true on successful enrolment
   */
    function full_enrol_user_remote($user, $remotecourseid, $mnet_cp, $remoteroleid) {
        global $CFG, $MNET;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
        //Make sure the user exists and is up to date on the remote peer:
        $userupdaterequest = new mnet_xmlrpc_client();
        $userupdaterequest->set_method('auth/mnet/auth.php/createupdate_user_bool');
        $userupdaterequest->add_param($user);
        $userupdaterequest->send($mnet_cp);
        $remoteuserexists = $userupdaterequest->response;
        if (empty($remoteuserexists)) {
            // Couldn't verify that remote user exists:
            return false;
        }
        //Then call full enrol function
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/assign_role_user');
        $mnetrequest->add_param((array)$user,'array');
        $mnetrequest->add_param($remotecourseid);
        $mnetrequest->add_param($remoteroleid);
        if ($mnetrequest->send($mnet_cp) === true) {
            return $mnetrequest->response;
        }
        return false;
    }

    /**
     * Get the default course role
     * @return mixed array record from our role table, or false on failure;
     */
    function get_default_role() {
        global $CFG, $DB;
        $roles = $DB->get_records('role', array('id' => $CFG->defaultcourseroleid));
        if (empty($roles)) {
            return false;
        }
        return (array) array_shift($roles);
    }

    /**
     * Get array of roles that an mnethost is allowed to allocate
     * @return mixed array records from our role table, or false on failure;
     */
    function get_allocatable_roles() {
        global $MNET_REMOTE_CLIENT, $DB;
        $rolessql =
                'SELECT r.id, r.shortname, r.name ' .
                'FROM {mnet_role_published} mrp ' .
                ' INNER JOIN {role} r ON r.id = mrp.localrole ' .
                'WHERE mrp.mnethost = ? ';
        $rolesparams = array( $MNET_REMOTE_CLIENT->id );
        $roles = $DB->get_records_sql($rolessql, $rolesparams);
        if (empty($roles)) {
            return false;
        }
        return $roles;
    }
} // end of class

?>
