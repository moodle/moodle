<?php  // $Id$

    /// this allows a student to request a course be created for them.

    require_once('../config.php');
    require_once('request_form.php');

    require_login();

    if (isguest()) {
        error("No guests here!");
    }

    if (empty($CFG->enablecourserequests)) {
        error(get_string('courserequestdisabled'));
    }

    $requestform = new course_request_form('request.php');

    $strtitle = get_string('courserequest');
    print_header($strtitle, $strtitle, $strtitle, $requestform->focus());

    print_simple_box_start('center');
    print_string('courserequestintro');
    print_simple_box_end();


    if (($data = $requestform->data_submitted())) {

        $data->requester = $USER->id;

        if (insert_record('course_request', $data)) {
            notice(get_string('courserequestsuccess'));
        } else {
            notice(get_string('courserequestfailed'));
        }
        print_footer();
        exit;

    }


    $requestform->display();

    print_footer();

    exit;


?>