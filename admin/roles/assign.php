<?php // $Id$
      // Script to assign students to courses

    require_once("../../config.php");

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

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if (! $context = get_context_instance_by_id($contextid)) {
        error("Context ID was incorrect (can't find it)");
    }

    require_login();

    require_capability('moodle/role:assign', $context);
    
    /**
     * TODO XXX:
     * Permission check to see whether this user can assign people to this role
     * needs to be:    
     * 1) has the capability to assign
     * 2) not in role_deny_grant
     * end of permission checking  
     */
    

    $strassignusers = get_string('assignusers', 'role');
    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('assignroles', 'role');
    $strcurrentrole = get_string('currentrole', 'role');
    $strcurrentcontext = get_string('currentcontext', 'role');
    $strsearch = get_string('search');
    $strshowall = get_string('showall');

    $currenttab = '';
    $tabsmode = 'assign';
    include_once('tabs.php');
    
/// Print a help notice about the need to use this page

    if (!$frm = data_submitted()) {

/// A form was submitted so process the input

    } else {
        if ($add and !empty($frm->addselect) and confirm_sesskey()) {
              //$timestart = ????
              // time end = ????
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


/// Get all existing students and teachers for this course.
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
        
    // this needs to check capability too
    $role = get_records('role');
    foreach ($role as $rolex) {
        $options[$rolex->id] = $rolex->name;
    }
    
    // prints a form to swap roles
    print ('<form name="rolesform" action="assign.php" method="post">');
    print ('<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>');
    print ('<input type="hidden" name="contextid" value="'.$context->id.'">'.$strcurrentrole.': ');
    choose_from_menu ($options, 'roleid', $roleid, 'choose', $script='rolesform.submit()');
    print ('</div></form>');
    
    if ($roleid) {

        print_simple_box_start("center");
    
        include('assign.html');

        print_simple_box_end();

    }
    print_footer($course);

?>
