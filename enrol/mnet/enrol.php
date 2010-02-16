<?php
// The following flags are set in the configuration
// $config->allow_allcourses:       expose all courses to external enrolment
// $config->allowed_categories:     serialised array of courses allowed
// $config->allowed_courses:        serialised array of courses allowed

class enrolment_plugin_mnet {

    /** * mnet environment - constructor makes sure its set up */
    private $mnet;

    function __construct() {
        $this->mnet = get_mnet_environment();
    }

    /// Override the base config_form() function
    function config_form($frm) {
        global $CFG, $OUTPUT, $PAGE;

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

    /**
    * Returns a list of all courses available for remote login
    *
    * @return array Array of courses
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
                co.enrollable = 1
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
                co.enrollable = 1 $where
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
                    co.id IN ({$CFG->enrol_mnet_allowed_courses})
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
        global $CFG, $DB;

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            return 'no course';
            //error("That's an invalid course id");
        }

        $remoteclient = get_mnet_remote_client();

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
                    u.mnethostid = '{$remoteclient->id}'
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
        global $DB;
        $remoteclient = get_mnet_remote_client();

        $userrecord = $DB->get_record('user',array('username'=>$user['username'], 'mnethostid'=>$remoteclient->id));

        if ($userrecord == false) {
            $userrecord = mnet_strip_user((object)$user, mnet_fields_to_import($remoteclient));
            /* there used to be a setting in auth/mnet called auto_create_users
             * which we should have been checking here (but weren't).
             * this setting has now been removed. See MDL-21327
             */
            $userrecord->mnethostid = $remoteclient->id;

            //TODO - username required to use PARAM_USERNAME before inserting into user table (MDL-16919)
            if ($userrecord->id = $DB->insert_record('user', $userrecord)) {
                $userrecord = $DB->get_record('user', array('id'=>$userrecord->id));
            } else {
                throw new mnet_server_exception(5011, get_string('couldnotcreateuser', 'enrol_mnet'));
            }
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            throw new mnet_server_exception(5012, get_string('coursenotfound', 'enrol_mnet'));
        }

        $courses = $this->available_courses();

        if (!empty($courses[$courseid])) {
            if (enrol_into_course($course, $userrecord, 'mnet')) {
                return true;
            }
            throw new mnet_server_exception(5016, get_string('couldnotenrol', 'enrol_mnet'));
        }
        throw new mnet_server_exception(5013, get_string('courseunavailable', 'enrol_mnet'));
    }

    /**
    * Unenrol a user from a course
    *
    * @param string $username   The username
    * @param int    $courseid   The id of the local course
    * @return bool              Whether the user can login from the remote host
    */
    function unenrol_user($username, $courseid) {
        global $DB;
        $remoteclient = get_mnet_remote_client();

        if (!$userrecord = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$remoteclient->id))) {
            throw new mnet_exception(5014, get_string('usernotfound', 'enrol_mnet'));
        }

        if (! $course = $DB->get_record('course', array('id'=>$courseid))) {
            throw new mnet_server_exception(5012, get_string('coursenotfound', 'enrol_mnet'));
        }

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        // Are we a *real* user or the shady MNET Daemon?
        // require_capability('moodle/role:assign', $context, NULL, false);

        if (!role_unassign(0, $userrecord->id, 0, $context->id)) {
            throw new mnet_exception(5015, get_string('couldnotunenrol', 'enrol_mnet'));
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
                {mnet_host} h,
                {mnet_host2service} h2s,
                {mnet_service} s
            WHERE
                h.id          = h2s.hostid   AND
                h2s.serviceid = s.id         AND
                s.name        = 'mnet_enrol' AND
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
        global $CFG, $USER, $DB;
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
                    $course->id = $dbcourse->id = $cachedcourses[$course->remoteid]->id;
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
        global $CFG, $USER, $DB;
        require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

        // Prepare a user record
        // in case the remote host doesn't have it
        $user = $DB->get_record('user', array('id'=>$userid));
        $user = (array)$user;

        $course = $DB->get_record('mnet_enrol_course', array('id'=>$courseid));

        // get the Service Provider info
        $mnet_sp = new mnet_peer();
        $mnet_sp->set_id($course->hostid);

        // set up the RPC request
        $mnetrequest = new mnet_xmlrpc_client();
        $mnetrequest->set_method('enrol/mnet/enrol.php/enrol_user');
        $mnetrequest->add_param(mnet_strip_user($user, mnet_fields_to_send($mnet_sp)));
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
        global $CFG, $USER, $DB;
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

} // end of class


