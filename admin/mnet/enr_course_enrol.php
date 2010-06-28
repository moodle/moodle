<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/xmlrpc/client.php');

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    admin_externalpage_setup('mnetenrol');
    $CFG->pagepath = 'admin/mnet';

    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class
    $enrolment = enrolment_factory::factory('mnet');

    $mnethostid = required_param('host', PARAM_INT);
    $courseid = required_param('courseid', PARAM_INT);

    $mnet_peer = new mnet_peer();
    if (!$mnet_peer->set_id($mnethostid)) {
        print_error('hostcoursenotfound','mnet');
    }

    $course = get_record('mnet_enrol_course', 'id', $courseid, 'hostid', $mnet_peer->id);

    if (empty($course)) {
        print_error('hostcoursenotfound','mnet');
    }

    define("MAX_USERS_PER_PAGE", 5000);

    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $userid         = optional_param('userid', 0, PARAM_INT); // needed for user tabs

    $errors = array();

    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;

    $baseurl = "remote_enrolment.php?courseid={$course->id}&amp;host={$mnet_peer->id}";
    if (!empty($userid)) {
        $baseurl .= '&amp;userid='.$userid;
    }

/// Process incoming role assignment

    if ($frm = data_submitted()) {
        if ($add and !empty($frm->addselect) and confirm_sesskey()) {
            $timemodified = time();

            foreach ($frm->addselect as $adduser) {
                if (!$adduser = clean_param($adduser, PARAM_INT)) {
                    continue;
                }
                if (! $enrolment->req_enrol_user($adduser, $course->id)) {
                    $errors[] = "Could not add user with id $adduser to course {$course->id}!";
                }
            }
        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removeuser) {
                $removeuser = clean_param($removeuser, PARAM_INT);
                if (! $enrolment->req_unenrol_user($removeuser, $course->id)) {
                    $errors[] = "Could not remove user with id $removeuser from course {$course->id}!";
                }
            }
        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }

/// Prepare data for users / enrolled users panes


/// Create a new request object
    $mnet_request = new mnet_xmlrpc_client();

/// Pass it the path to the method that we want to execute
    $mnet_request->set_method('enrol/mnet/enrol.php/course_enrolments');
    $mnet_request->add_param($course->remoteid, 'int');
    $mnet_request->send($mnet_peer);
    $raw_all_enrolled_users = $mnet_request->response;
    unset($mnet_request);

    $all_enrolled_users = array();
    if (!empty($raw_all_enrolled_users)) {
        // Try to repair keying of remote users array, numeric usernames get lost in the fracas
        foreach ($raw_all_enrolled_users as $username => $userdetails) {
            if (empty($userdetails['username']) || !is_numeric($username)) {
                //Not able to repair, or no need to repair
                $all_enrolled_users[$username] = $userdetails;
            } else {
                $all_enrolled_users[$userdetails['username']] = $userdetails;
            }
        }
    }
    
    $select = '';
    $all_enrolled_usernames = '';
    $timemodified = array();
/// List all the users (homed on this server) who are enrolled on the course
/// This will include mnet-enrolled users, and those who have enrolled 
/// themselves, etc.
    if (is_array($all_enrolled_users) && count($all_enrolled_users)) {
        foreach($all_enrolled_users as $username => $data) {
            $all_enrolled_usernames .= "'$username', ";
        }
        $select = ' u.username IN (' .substr($all_enrolled_usernames, 0, -2) .') AND ';
    } else {
        $all_enrolled_users = array();
    }

/// Synch our mnet_enrol_assignments with remote server
    $sql = "
            SELECT
                u.id,
                u.firstname,
                u.lastname,
                u.username,
                u.email,
                a.enroltype,
                a.id as enrolid,
                COALESCE(a.hostid, 0) as wehaverecord,
                a.courseid
            FROM
                {$CFG->prefix}user u
            JOIN
                {$CFG->prefix}mnet_enrol_assignments a
            ON
                a.userid = u.id AND a.courseid={$courseid}
            WHERE
                $select 
                u.deleted = 0 AND
                u.confirmed = 1 AND
                u.mnethostid = {$CFG->mnet_localhost_id}
            ORDER BY
                u.firstname ASC,
                u.lastname ASC";

    if (!$enrolledusers = get_records_sql($sql)) {
        $enrolledusers = array();
    }

    foreach($enrolledusers as $user) {

        $dataobj = new stdClass();
        $dataobj->userid    = $user->id;
        $dataobj->hostid    = $mnet_peer->id;
        $dataobj->courseid  = $courseid;
        $dataobj->rolename  = $all_enrolled_users[$user->username]['name'];
        $dataobj->enroltype = $all_enrolled_users[$user->username]['enrol'];

        if ($user->wehaverecord == 0) {
            $dataobj->enroltime = $all_enrolled_users[$user->username]['timemodified'];
            $dataobj->id = insert_record('mnet_enrol_assignments', $dataobj);
        } elseif (array_key_exists($user->username, $all_enrolled_users)) {
            $dataobj->id    = $user->enrolid;
            update_record('mnet_enrol_assignments', $dataobj);
        } elseif (is_array($all_enrolled_users) && count($all_enrolled_users)) {
            delete_record('mnet_enrol_assignments', 'id', $user->enrolid);
        }
    }
    unset($enrolledusers);

    // Read about our remote enrolments in 2 sets
    // first, get the remote enrolments done via enrol/mnet      $mnetenrolledusers
    // second, get the remote enrolments done with other plugins $remtenrolledusers
    // NOTE: both arrays are keyed on the userid! 
    $sql = "
            SELECT
                u.id,
                u.firstname,
                u.lastname,
                a.rolename,
                a.enroltype,
                a.courseid
            FROM
                {$CFG->prefix}user u,
                {$CFG->prefix}mnet_enrol_assignments a
            WHERE
                a.userid = u.id AND 
                a.courseid={$courseid} AND
                a.enroltype = 'mnet'   AND
                u.deleted = 0 AND
                u.confirmed = 1 AND
                u.mnethostid = {$CFG->mnet_localhost_id}
            ORDER BY
                u.firstname ASC,
                u.lastname ASC";

    if (!$mnetenrolledusers = get_records_sql($sql)) {
        $mnetenrolledusers = array();
    }
    $sql = "
            SELECT
                u.id,
                u.firstname,
                u.lastname,
                a.rolename,
                a.enroltype,
                a.courseid
            FROM
                {$CFG->prefix}user u,
                {$CFG->prefix}mnet_enrol_assignments a
            WHERE
                a.userid = u.id AND 
                a.courseid={$courseid} AND
                a.enroltype != 'mnet'  AND
                u.deleted = 0 AND
                u.confirmed = 1 AND
                u.mnethostid = {$CFG->mnet_localhost_id}
            ORDER BY
                u.firstname ASC,
                u.lastname ASC";

    if (!$remtenrolledusers = get_records_sql($sql)) {
        $remtenrolledusers = array();
    }

    $select = '';
    $exclude = array_merge(array_keys($mnetenrolledusers), array_keys($remtenrolledusers));
    $exclude[] = 0;
    $select = 'AND u.username!=\'guest\' AND u.id NOT IN ('. join(',',$exclude) .') ';
    unset($exclude);

    $searchtext = trim($searchtext);

    if ($searchtext !== '') {   // Search for a subset of remaining users
        $LIKE      = sql_ilike();
        $FULLNAME  = sql_fullname();

        $select  .= " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') ";
    }

    $sql = ('SELECT id, firstname, lastname, email 
            FROM '.$CFG->prefix.'user u
            WHERE deleted = 0 AND confirmed = 1 
                  AND mnethostid = '.$CFG->mnet_localhost_id.' '
            .$select
            .'ORDER BY lastname ASC, firstname ASC');

    $availableusers = get_recordset_sql($sql, 0, MAX_USERS_PER_PAGE);



/// Print the page

/// get language strings
$str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));
/// Get some language strings

$strpotentialusers = get_string('potentialusers', 'role');
$strexistingusers = get_string('existingusers', 'role');
$straction = get_string('assignroles', 'role');
$strroletoassign = get_string('roletoassign', 'role');
$strcurrentcontext = get_string('currentcontext', 'role');
$strsearch = get_string('search');
$strshowall = get_string('showall');
$strparticipants = get_string('participants');
$strsearchresults = get_string('searchresults');

admin_externalpage_print_header();

print_box('<strong>' . s($mnet_peer->name) . ' : ' 
          . format_string($course->shortname) .' '. format_string($course->fullname) 
          . '</strong><br />'
          . get_string("enrolcourseenrol_desc", "mnet"));

echo "<hr />";

include(dirname(__FILE__).'/enr_course_enrol.html');

if (!empty($errors)) {
    $msg = '<p>';
    foreach ($errors as $e) {
        $msg .= $e.'<br />';
    }
    $msg .= '</p>';
    notify($msg);
}


admin_externalpage_print_footer();

?>
