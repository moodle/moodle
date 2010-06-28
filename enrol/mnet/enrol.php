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
    //function cron() {
    //
    //} // end of cron()



    /***
     *** MNET functions
     ***
     ***/
    function mnet_publishes() {
        
        $enrol = array();
        $enrol['name']        = 'mnet_enrol'; // Name & Description go in lang file
        $enrol['apiversion']  = 1;
        $enrol['methods']     = array('available_courses','user_enrolments', 'enrol_user', 'unenrol_user', 'course_enrolments' );

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
        global $CFG;

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
                {$CFG->prefix}course_categories ca
            JOIN
                {$CFG->prefix}course co ON
                ca.id = co.category
            LEFT JOIN
                {$CFG->prefix}role r ON
                r.id = co.defaultrole
            WHERE
                co.visible = '1' AND
                co.enrollable = '1'
            ORDER BY
                sortorder ASC
                ";

            return get_records_sql($query);

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
                {$CFG->prefix}course_categories ca
            WHERE
                ca.id IN ({$CFG->enrol_mnet_allowed_categories})
                OR ( $cats )
            ORDER BY
                path ASC,
                depth ASC
                ";
            unset($cats);

            $rs = get_records_sql($query);

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
                r.name as defaultrolename
            FROM
                {$CFG->prefix}course_categories ca
            JOIN
                {$CFG->prefix}course co ON
                ca.id = co.category
            LEFT JOIN
                {$CFG->prefix}role r ON
                r.id = co.defaultrole
            WHERE
                co.visible = '1' AND
                co.enrollable = '1' $where
            ORDER BY
                sortorder ASC
                ";

            return get_records_sql($query);

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
                    r.name as defaultrolename
                FROM
                    {$CFG->prefix}course_categories ca
                JOIN
                    {$CFG->prefix}course co ON
                    ca.id = co.category
                LEFT JOIN
                    {$CFG->prefix}role r ON
                    r.id = co.defaultrole
                WHERE
                    co.visible = '1' AND
                    co.enrollable = '1' AND
                    co.id in ({$CFG->enrol_mnet_allowed_courses})
                ORDER BY
                    sortorder ASC
                    ";

            return get_records_sql($query);

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
        global $MNET_REMOTE_CLIENT, $CFG;

        if (! $course = get_record('course', 'id', $courseid) ) {
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
                    {$CFG->prefix}role_assignments a,
                    {$CFG->prefix}role r,
                    {$CFG->prefix}user u
                WHERE
                    a.contextid = '{$context->id}' AND
                    a.roleid = r.id AND
                    a.userid = u.id AND
                    u.mnethostid = '{$MNET_REMOTE_CLIENT->id}'
                    ";

        if(!empty($roles)) {
            // $default_role = get_default_course_role($course); ???
            $sql .= " AND
                    a.roleid in ('".str_replace(',',  "', '",  $roles)."')";
        } 

        $enrolments = get_records_sql($sql);

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
        global $MNET, $MNET_REMOTE_CLIENT;

        $userrecord = get_record('user','username',addslashes($user['username']), 'mnethostid',$MNET_REMOTE_CLIENT->id);

        if ($userrecord == false) {
            // We should at least be checking that we allow the remote
            // site to create users
            // TODO: more rigour here thanks!
            $userrecord = new stdClass();
            $userrecord->username   = addslashes($user['username']);
            $userrecord->email      = addslashes($user['email']);
            $userrecord->firstname  = addslashes($user['firstname']);
            $userrecord->lastname   = addslashes($user['lastname']);
            $userrecord->mnethostid = $MNET_REMOTE_CLIENT->id;

            if ($userrecord->id = insert_record('user', $userrecord)) {
                $userrecord = get_record('user','id', $userrecord->id);
            } else {
                // TODO: Error out
                return false;
            }
        }

        if (! $course = get_record('course', 'id', $courseid) ) {
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
        global $MNET_REMOTE_CLIENT;

        $userrecord = get_record('user', 'username', addslashes($username), 'mnethostid', $MNET_REMOTE_CLIENT->id);

        if ($userrecord == false) {
            return false;
            // TODO: Error out
        }

        if (! $course = get_record('course', 'id', $courseid) ) {
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
            error("An error occurred while trying to unenrol that person.");
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
        global $CFG;

        $sql = "
            SELECT DISTINCT 
                h.id, 
                h.name
            FROM 
                {$CFG->prefix}mnet_host h,
                {$CFG->prefix}mnet_host2service h2s,
                {$CFG->prefix}mnet_service s
            WHERE
                h.id          = h2s.hostid   AND
                h2s.serviceid = s.id         AND
                s.name        = 'mnet_enrol' AND
                h2s.subscribe = 1";

        $res = get_records_sql($sql);
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
        global $CFG;
        global $USER;
        global $MNET;
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
            $cachedcourses = get_records('mnet_enrol_course',
                                         'hostid', $mnethostid,
                                         'remoteid', 'remoteid, id' );

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
                $dbcourse->cat_name        = addslashes(substr($dbcourse->cat_name,0,255));
                $dbcourse->cat_description = addslashes($dbcourse->cat_description);
                $dbcourse->fullname        = addslashes(substr($dbcourse->fullname,0,254));
                $dbcourse->shortname       = addslashes(substr($dbcourse->shortname,0,15));
                $dbcourse->idnumber        = addslashes(substr($dbcourse->idnumber,0,100));
                $dbcourse->summary         = addslashes($dbcourse->summary);
                $dbcourse->currency        = addslashes(substr($dbcourse->currency,0,3));
                $dbcourse->defaultrolename = addslashes(substr($dbcourse->defaultrolename,0,255));

                // insert or update
                if (empty($cachedcourses[$course->remoteid])) {
                    $course->id = insert_record('mnet_enrol_course', $dbcourse);
                } else {
                    $course->id = $cachedcourses[$course->remoteid]->id;
                    $cachedcourses[$course->remoteid]->seen=true;
                    update_record('mnet_enrol_course', $dbcourse);
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
                    delete_records_select('mnet_enrol_course', 'id IN ('.join(',',$stale).')');
                }
            }

            return $courses;
        } else {
            foreach ($mnetrequest->error as $errormessage) {
                list($code, $errormessage) = array_map('trim',explode(':', $errormessage, 2));
                $message .= "ERROR $code:<br/>$errormessage<br/>";
            }
            error("RPC enrol/mnet/available_courses:<br/>$message");
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
        global $CFG;
        global $USER;
        global $MNET;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // Prepare a basic user record
        // in case the remote host doesn't have it
        $user = get_record('user', 'id', $userid, '','','','', 'username, email, firstname, lastname');
        $user = (array)$user;

        $course = get_record('mnet_enrol_course', 'id', $courseid);

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
                if (insert_record('mnet_enrol_assignments', $assignment)) {
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
        global $CFG;
        global $USER;
        global $MNET;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // in case the remote host doesn't have it
        $username = get_field('user', 'username', 'id', $userid);

        $course = get_record('mnet_enrol_course', 'id', $courseid);

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
                delete_records_select('mnet_enrol_assignments',
                                      "userid={$userid} AND courseid={$course->id}");

                return true;
            }
        }
        return false;
    }

} // end of class

?>
