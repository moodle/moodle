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

    $errors = array();

    if (! $context = get_context_instance_by_id($contextid)) {
        error("Context ID was incorrect (can't find it)");
    }


    $inmeta = 0;
    if ($context->aggregatelevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        if ($course = get_record('course', 'id', $courseid)) {
            $inmeta = $course->metacourse;
        } else {
            error('Invalid course id');
        }
    } else if (!empty($courseid)){ // we need this for user tabs in user context
        if (!$course = get_record('course', 'id', $courseid)) {
            error('Invalid course id');
        }
    } else {
        $courseid = SITEID;
        $course = get_site();
    }

    require_login($courseid);
    require_capability('moodle/role:assign', $context);

    $assignableroles = get_assignable_roles($context);

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



/// Make sure this user can assign that role

    if ($roleid) {
        if (!user_can_assign($context, $roleid)) {
            error ('you can not override this role in this context');
        }
    }

    if ($userid) {
        $user = get_record('user', 'id', $userid);
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
    }


/// Print the header and tabs

    if ($context->aggregatelevel == CONTEXT_USER) {
        /// course header
        if ($courseid!= SITEID) {
            print_header("$fullname", "$fullname",
                     "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"".$CFG->wwwroot."/user/index.php?id=$course->id\">$strparticipants</a> -> <a href=\"".$CFG->wwwroot."/user/view.php?id=".$userid."&course=".$courseid."\">$fullname</a> ->".$straction,
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
                $allow = true;
                if ($inmeta) {
                    if (has_capability('moodle/course:managemetacourse', $context, $adduser)) {
                        //ok
                    } else {
                        $managerroles = get_roles_with_capability('moodle/course:managemetacourse', CAP_ALLOW, $context);
                        if (!empty($managerroles) and !array_key_exists($roleid, $managerroles)) {
                            $erruser = get_record('user', 'id', $adduser, '','','','', 'id, firstname, lastname');
                            $errors[] = get_string('metaassignerror', 'role', fullname($erruser));
                            $allow = false;
                        }
                    }
                }
                if ($allow) {
                    if (! role_assign($roleid, $adduser, 0, $context->id, $timestart, $timeend, $hidden)) {
                        $errors[] = "Could not add user with id $adduser to this role!";
                    }
                }
            }

        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {

            foreach ($frm->removeselect as $removeuser) {
                $removeuser = clean_param($removeuser, PARAM_INT);
                if (! role_unassign($roleid, $removeuser, 0, $context->id)) {
                    $errors[] = "Could not remove user with id $removeuser from this role!";
                } else if ($inmeta) {
                    sync_metacourse($courseid);
                    $newroles = get_user_roles($context, $removeuser, false);
                    if (!empty($newroles) and !array_key_exists($roleid, $newroles)) {
                        $erruser = get_record('user', 'id', $removeuser, '','','','', 'id, firstname, lastname');
                        $errors[] = get_string('metaunassignerror', 'role', fullname($erruser));
                        $allow = false;
                    }
                }
            }

        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }


/// Get all existing participants in this course.

    $existinguserarray = array();

    if (!$contextusers = get_role_users($roleid, $context)) {
        $contextusers = array();
    }

    foreach ($contextusers as $contextuser) {
        $existinguserarray[] = $contextuser->id;
    }

    $existinguserlist = implode(',', $existinguserarray);
    unset($existinguserarray);

    $usercount = get_users(false, '', true, $existinguserlist);

/// Get search results excluding any users already in this course
    if (($searchtext != '') and $previoussearch) {
        $searchusers = get_users(true, $searchtext, true, $existinguserlist, 'firstname ASC, lastname ASC',
                                      '', '', 0, MAX_USERS_PER_PAGE, 'id, firstname, lastname, email');
    }

/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchusers)) {
        $users = array();
        if ($usercount <= MAX_USERS_PER_PAGE) {
            if (!$users = get_users(true, '', true, $existinguserlist, 'firstname ASC, lastname ASC', '', '',
                               0, MAX_USERS_PER_PAGE, 'id, firstname, lastname, email')) {
                $users = array();
            }
        }

    }

    if ($roleid) {
    /// prints a form to swap roles
        echo '<form name="rolesform" action="assign.php" method="post">';
        echo '<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>';
        if ($userid) {
            echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        }
        echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
        echo '<input type="hidden" name="contextid" value="'.$context->id.'" />'.$strroletoassign.': ';
        choose_from_menu ($assignableroles, 'roleid', $roleid, get_string('listallroles', 'role').'...', $script='rolesform.submit()');
        echo '</div></form>';

        print_simple_box_start("center");
        include('assign.html');
        print_simple_box_end();

        if (!empty($errors)) {
            print_simple_box_start("center");
            foreach ($errors as $error) {
                notify($error);
            }
            print_simple_box_end();
        }

    } else {   // Print overview table

        // sync metacourse enrolments if needed
        if ($inmeta) {
            sync_metacourse($course);
        }
        $userparam = (!empty($userid)) ? '&amp;userid='.$userid : '';

        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '20%';
        $table->head = array(get_string('roles', 'role'), get_string('users'));
        $table->wrap = array('nowrap', 'nowrap');
        $table->align = array('right', 'center');

        foreach ($assignableroles as $roleid => $rolename) {
            $countusers = 0;
            if ($contextusers = get_role_users($roleid, $context)) {
                $countusers = count($contextusers);
            }
            $table->data[] = array('<a href="assign.php?contextid='.$context->id.'&amp;roleid='.$roleid.$userparam.'">'.$rolename.'</a>', $countusers);
        }

        print_table($table);
    }

    print_footer($course);

?>
