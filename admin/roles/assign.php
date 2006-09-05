<?php // $Id$
      // Script to assign users to contexts

    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/forum/lib.php');

    define("MAX_USERS_PER_PAGE", 5000);

    $contextid      = required_param('contextid',PARAM_INT); // context id
    $roleid         = optional_param('roleid', 0, PARAM_INT); // required role id
    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $hidden         = optional_param('hidden', 0, PARAM_BOOL); // whether this assignment is hidden
    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;
    $timestart      = optional_param('timestart', 0, PARAM_INT);
    $timeend        = optional_param('timened', 0, PARAM_INT);
    $userid         = optional_param('userid', 0, PARAM_INT); // needed for user tabs
    $courseid       = optional_param('courseid', 0, PARAM_INT); // needed for user tabs

    if ($courseid) {
        $course = get_record('course', 'id', $courseid);  
    }
    
    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if (! $context = get_context_instance_by_id($contextid)) {
        error("Context ID was incorrect (can't find it)");
    }


    require_capability('moodle/role:assign', $context);

    
    $strassignusers = get_string('assignusers', 'role');
    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('assignroles', 'role');
    $strroletoassign = get_string('roletoassign', 'role');
    $strcurrentcontext = get_string('currentcontext', 'role');
    $strsearch = get_string('search');
    $strshowall = get_string('showall');

    $context = get_record('context', 'id', $contextid);
    $assignableroles = get_assignable_roles($context);
    

/// Make sure this user can assign that role

    if ($roleid) {
        if (!user_can_assign($context, $roleid)) {
            error ('you can not override this role in this context');
        }  
    }
    
    $participants = get_string("participants");
    $user = get_record('user', 'id', $userid);
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));


/// Print the header and tabs

    if ($context->aggregatelevel == CONTEXT_USERID) {
        /// course header
        if ($courseid!= SITEID) {
            print_header("$fullname", "$fullname",
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"".$CFG->wwwroot."/user/index.php?id=$course->id\">$participants</a> -> <a href=\"".$CFG->wwwroot."/user/view.php?id=".$userid."&course=".$courseid."\">$fullname</a> ->".$straction,
                      "", "", true, "&nbsp;", navmenu($course));      
        
        /// site header  
        } else {
            print_header("$course->fullname: $fullname", "$course->fullname",
                        "<a href=\"".$CFG->wwwroot."/user/view.php?id=".$userid."&course=".$courseid."\">$fullname</a> -> $straction", "", "", true, "&nbsp;", navmenu($course));     
        }
        
        $showroles = 1;
        $currenttab = 'assign';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else {
        $currenttab = '';
        $tabsmode = 'assign';
        include_once('tabs.php');
    }


/// Process incoming role assignment

    if ($frm = data_submitted()) {

        if ($add and !empty($frm->addselect) and confirm_sesskey()) {

            $timemodified = time();

            foreach ($frm->addselect as $adduser) {
                $adduser = clean_param($adduser, PARAM_INT);
                if (! role_assign($roleid, $adduser, 0, $context->id, $timestart, $timeend, $hidden)) {
                    error("Could not add user with id $adduser to this role!");
                }
            }

        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {

            foreach ($frm->removeselect as $removeuser) {
                $removeuser = clean_param($removeuser, PARAM_INT);
                if (! role_unassign($roleid, $removeuser, 0, $context->id)) {
                    error("Could not remove user with id $removeuser from this role!");
                }
            }

        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }


/// Get all existing participants in this course.

    $existinguserarray = array();

    $SQL = "select u.* from {$CFG->prefix}role_assignments r, {$CFG->prefix}user u where contextid = $context->id and roleid = $roleid and u.id = r.userid"; // join now so that we can just use fullname() later

    if (!$contextusers = get_records_sql($SQL)) {
        $contextusers = array();  
    }

    foreach ($contextusers as $contextuser) {
        $existinguserarray[] = $contextuser->id;
    }
    
    $existinguserlist = implode(',', $existinguserarray);
    unset($existinguserarray);

/// Get search results excluding any users already in this course
    if (($searchtext != '') and $previoussearch) {
        $searchusers = get_users(true, $searchtext, true, $existinguserlist, 'firstname ASC, lastname ASC',
                                      '', '', 0, 99999, 'id, firstname, lastname, email');
        $usercount = get_users(false, '', true, $existinguserlist);
    }

/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchusers)) {

        $usercount = get_users(false, '', true, $existinguserlist, 'firstname ASC, lastname ASC', '', '',
                              0, 99999, 'id, firstname, lastname, email') ;
        $users = array();

        if ($usercount <= MAX_USERS_PER_PAGE) {
            $users = get_users(true, '', true, $existinguserlist, 'firstname ASC, lastname ASC', '', '',
                               0, 99999, 'id, firstname, lastname, email');
        }

    }
    
    // prints a form to swap roles
    print ('<form name="rolesform" action="assign.php" method="post">');
    print ('<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>');
    if ($userid) {
        print ('<input type="hidden" name="userid" value="'.$userid.'" />');
    }
    if ($courseid) {
        print ('<input type="hidden" name="courseid" value="'.$courseid.'" />');
    }
    print ('<input type="hidden" name="contextid" value="'.$context->id.'" />'.$strroletoassign.': ');
    choose_from_menu ($assignableroles, 'roleid', $roleid, 'choose', $script='rolesform.submit()');
    print ('</div></form>');
    
    if ($roleid) {

        print_simple_box_start("center");
    
        include('assign.html');

        print_simple_box_end();

    }
    print_footer($course);

?>
