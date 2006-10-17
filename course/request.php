<?php  // $Id$
 
    /// this allows a student to request a course be created for them.

    require_once('../config.php');
    include_once $CFG->libdir.'/formslib.php';
    
    require_login();
      
    require_once('request_form.php');   

    if (isguest()) {
        error("No guests here!");
    }

    if (empty($CFG->enablecourserequests)) {
        error(get_string('courserequestdisabled'));
    }
   

    $strtitle = get_string('courserequest');
    print_header($strtitle,$strtitle,$strtitle);

    print_simple_box_start("center");
    print_string('courserequestintro'); 
    print_simple_box_end();
    
    $requestform = new course_request_form('request.php');
    
    if (($data = $requestform->data_submitted())) {        
       
        $data->requester = $USER->id;

        if (insert_record('course_request',$data)) {
            notice(get_string('courserequestsuccess'));
        }
        else {
            notice(get_string('courserequestfailed'));
        }
        print_footer();
        exit;
        
        
    } 
    
    
    $requestform->display();

    print_footer();
    
    exit;



?>