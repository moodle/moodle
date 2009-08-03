<?php // $Id$
    require_once('../config.php');
    require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
    require_once $CFG->dirroot . '/course/lib.php';

    require_login();

    $courseid = required_param('id', PARAM_INT); // course id
    $mnetpeer = optional_param('mnetpeer', 0, PARAM_INT); // mnet peer id
    $remotecourseid = optional_param('remotecourseid', 0, PARAM_INT); //Course id on remote peer

    // True if user has submitted a form (by js or otherwise)
    $saveform = optional_param('saveform', 0, PARAM_INT);

    // "reload/update" if user clicked to submit the form
    $reloadupdate = optional_param('reloadupdate', '', PARAM_TEXT);
    $clicked = !empty($reloadupdate);

    if($courseid == SITEID){
        // don't allow editing of 'site course' using this from
        error('You cannot edit the site course using this form');
    }

    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        error('Course ID was incorrect');
    }

    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    $streditcoursemnetsettings = get_string("editcoursemnetsettings");
    $strmnetsettings = get_string("mnetsettings");
    $navlinks[] = array('name' => $course->shortname,
                        'link' => 'view.php?id=' . $courseid,
                        'type' => 'misc');
    $navlinks[] = array('name' => $strmnetsettings,
                        'link' => 'mnet.php?id=' . $courseid,
                        'type' => 'misc');
    $title = $streditcoursemnetsettings;
    $fullname = $course->fullname;

    $PAGE->set_generaltype('form');
    $PAGE->set_url('course/mnet.php', array('id' => $courseid));
    $navigation = build_navigation($navlinks);
    print_header($title, $fullname, $navigation);

    print_heading(get_string('editcoursemnetsettings'));
    if (!has_capability('moodle/course:update', $coursecontext)) {
        print_error("notpermittedtoview");
    }


    //Select those peers to whome we subscribe to for enrolment services
    $contentpeerssql = 'SELECT h.id, h.name, h.wwwroot '.
                    'FROM {mnet_service} s'.
                    ' INNER JOIN {mnet_host2service} h2s on h2s.serviceid=s.id'.
                    ' INNER JOIN {mnet_host} h on h.id = h2s.hostid '.
                    'WHERE s.name = \'mnet_enrol\''.
                    ' AND h2s.subscribe <> 0 '.
                    ' AND h.deleted = 0 '.
                    'ORDER BY h.name ASC, h.wwwroot ASC, h.id ASC';
    $contentpeers = $DB->get_records_sql($contentpeerssql);
    if (!$contentpeers) {
        print_error('mnetpeersnoenrolment');
    }

    // If the user has made a selection check if that selection is valid
    // Valid selections are a) 0, or b) rows of $contentpeers
    // If it's not valid override it with no-change, or no-mnetpeer
    if ($saveform && (!empty($contentpeers[$mnetpeer]) || $mnetpeer == 0)) {
        $selectedpeer = $mnetpeer;
        if (!$clicked && $selectedpeer != $course->mnetpeer) {
            $remotecourseid = 0;
        }
    } elseif (!empty($course->mnetpeer) && !empty($contentpeers[$course->mnetpeer])) {
        $selectedpeer = $course->mnetpeer;
    } else {
        $selectedpeer = 0;
    }

    if (!has_capability('moodle/course:linkmnetcourse', $coursecontext)) {
        print_error('mnetnotpermittedtolink');
    }
    $peercourses = array();
    if ($selectedpeer) {
        $peercourses = mnet_get_available_courses($selectedpeer, $courseid);
    }

    // Ensure any course the user has selected is a valid option
    // Valid options are a) 0 (no mnet content provider after all), or
    // b) a course that is provided by the selected mnetpeer that isn't a content provider for one of our other courses;
    if ($saveform && ($remotecourseid == 0 || (!empty($peercourses[$remotecourseid]) && empty($peercourses[$remotecourseid]->unavailable)))) {
        $selectedcourse = $remotecourseid;
    } else {
        $selectedcourse = 0;
    }

    $updateenrolments = false;

    // Assuming the user has made peer/course selections and we haven't had to override either
    // We can update course information & process enrolments/unenrolments.
    if ($clicked && ($mnetpeer == $selectedpeer) && ($remotecourseid == $selectedcourse)) {
        $updateenrolments = true;
    }

    // If user has selected to not use remote peer/course, or has supplied invalid details
    // we also need to update enrolments
    if ($saveform && (empty($selectedpeer) || empty($selectedcourse))) {
        $updateenrolments = true;
    }

    if (!$updateenrolments) {
        // User hasn't made selections - preset form with current values:
        $selectedpeer = $course->mnetpeer;
        $selectedcourse = $course->remotecourseid;
    } else {
        //                                     &course
        $updateresult = mnet_update_enrolments($course, $selectedpeer, $selectedcourse);
    }
    include('mnet.html');
    print_footer();
?>
