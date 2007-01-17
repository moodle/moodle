<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');
    include_once($CFG->dirroot.'/mnet/xmlrpc/client.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('enrolment', $adminroot);

    $CFG->pagepath = 'enrol/mnet';
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
    $all_enrolled_users = $mnet_request->response;

    unset($mnet_request);
    
    $select = '';
    $all_enrolled_usernames = '';
/// List all the users (homed on this server) who are enrolled on the course
/// This will include mnet-enrolled users, and those who have enrolled 
/// themselves, etc.
    if (is_array($all_enrolled_users) && count($all_enrolled_users)) {
        foreach($all_enrolled_users as $user) {
            $all_enrolled_usernames .= "'{$user['username']}', ";
        }
        $select = ' u.username IN (' .substr($all_enrolled_usernames, 0, -2) .') AND';
    }

/// Pseudocode for query - get records for all users that are enrolled in the 
/// course, and if they were enrolled via mnet, ismnetenrolment will be > 0
    $sql = "
            SELECT
                u.id,
                u.firstname,
                u.lastname,
                u.email,
                coalesce ( a.hostid , 0) as ismnetenrolment,
                a.courseid
            FROM
                {$CFG->prefix}user u
            LEFT JOIN
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

    $searchtext = trim($searchtext);
    $select = '';

    if ($searchtext !== '') {   // Search for a subset of remaining users
        $LIKE      = sql_ilike();
        $FULLNAME  = sql_fullname();

        $select  .= " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') ";
    }

    /**** start of NOT IN block ****/

    $select .= " AND username NOT IN ('guest', 'changeme', ";
    $select .= $all_enrolled_usernames;
    $select = substr($select, 0, -2) .') ';

    /**** end of NOT IN block ****/

    $availableusers = get_recordset_sql('SELECT id, firstname, lastname, email 
                                         FROM '.$CFG->prefix.'user 
                                         WHERE deleted = 0 AND confirmed = 1 AND mnethostid = '.$CFG->mnet_localhost_id.' '.$select.'
                                         ORDER BY lastname ASC, firstname ASC', 0, MAX_USERS_PER_PAGE);



/// Print the page

/// get language strings
$str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));
/// Get some language strings

$strassignusers = get_string('assignusers', 'role');
$strpotentialusers = get_string('potentialusers', 'role');
$strexistingusers = get_string('existingusers', 'role');
$straction = get_string('assignroles', 'role');
$strroletoassign = get_string('roletoassign', 'role');
$strcurrentcontext = get_string('currentcontext', 'role');
$strsearch = get_string('search');
$strshowall = get_string('showall');
$strparticipants = get_string('participants');
$strsearchresults = get_string('searchresults');

admin_externalpage_print_header($adminroot);

print_simple_box_start("center", "80%");

print_simple_box_start("center", "60%", '', 5, 'informationbox');
print_string('enrollingincourse', 'mnet', array(s($course->shortname), s($mnet_peer->name)));
print_string("description", "enrol_mnet");
print_simple_box_end();

echo "<hr />";

        print_simple_box_start('center');
        include('remote_enrolment.html');
        print_simple_box_end();

        if (!empty($errors)) {
            $msg = '<p>';
            foreach ($errors as $e) {
                $msg .= $e.'<br />';
            }
            $msg .= '</p>';
            print_simple_box_start('center');
            notify($msg);
            print_simple_box_end();
        }


print_simple_box_end();
admin_externalpage_print_footer($adminroot);

?>
