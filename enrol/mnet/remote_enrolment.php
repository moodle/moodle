<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('enrolment', $adminroot);

    $CFG->pagepath = 'enrol/mnet';
    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class
    $enrolment = enrolment_factory::factory('mnet');

    $mnethost = required_param('host', PARAM_INT);
    $courseid = required_param('courseid', PARAM_INT);

    $mnethost = get_record('mnet_host', 'id', $mnethost);
     $course = get_record('mnet_enrol_course', 'id', $courseid, 'hostid', $mnethost->id);

    if (empty($mnethost) || empty($course)) {
        error("Host or course not found");
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

    $baseurl = "remote_enrolment.php?courseid={$course->id}&amp;host={$mnethost->id}";
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
                    $errors[] = "Could not add user with id $adduser to this role!";
                }
            }
        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removeuser) {
                $removeuser = clean_param($removeuser, PARAM_INT);
                if (! $enrolment->req_unenrol_user($removeuser, $course->id)) {
                    $errors[] = "Could not remove user with id $removeuser from this role!";
                }
            }
        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }

/// Prepare data for users / enrolled users panes
    $sql = "SELECT u.id, u.firstname, u.lastname, u.email
            FROM {$CFG->prefix}mnet_enrol_assignments a
            JOIN {$CFG->prefix}user u ON a.userid=u.id
            WHERE a.courseid={$courseid}
            ORDER BY u.id";
    if (!$enrolledusers = get_records_sql($sql)) {
        $enrolledusers = array();
    }

    $select  = "username != 'guest' AND username != 'changeme' AND deleted = 0 AND confirmed = 1 AND mnethostid = {$CFG->mnet_localhost_id}";
    
    $usercount = count_records_select('user', $select) - count($enrolledusers);

    $searchtext = trim($searchtext);

    if ($searchtext !== '') {   // Search for a subset of remaining users
        $LIKE      = sql_ilike();
        $FULLNAME  = sql_fullname();

        $select  .= " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') ";
    }
    $availableusers = get_recordset_sql('SELECT id, firstname, lastname, email 
                                         FROM '.$CFG->prefix.'user 
                                         WHERE '.$select.'
                                         ORDER BY lastname ASC, firstname ASC');



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
print "Enrolling in course " . s($course->shortname) . ' on host ' . s($mnethost->name) ."</p>";
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



admin_externalpage_print_footer($adminroot);

?>
