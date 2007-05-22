<?php // $Id$
      // Script to assign users to contexts

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    define("MAX_USERS_PER_PAGE", 5000);

    $contextid      = required_param('contextid',PARAM_INT); // context id
    $roleid         = optional_param('roleid', 0, PARAM_INT); // required role id
    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $hidden         = optional_param('hidden', 0, PARAM_BOOL); // whether this assignment is hidden
    $timestart      = optional_param('timestart', 0, PARAM_INT);
    $timeend        = optional_param('timened', 0, PARAM_INT);
    $extendperiod   = optional_param('extendperiod', 0, PARAM_INT);
    $userid         = optional_param('userid', 0, PARAM_INT); // needed for user tabs
    $courseid       = optional_param('courseid', 0, PARAM_INT); // needed for user tabs

    $errors = array();

    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;

    $baseurl = 'assign.php?contextid='.$contextid;
    if (!empty($userid)) {
        $baseurl .= '&amp;userid='.$userid;
    }
    if (!empty($courseid)) {
        $baseurl .= '&amp;courseid='.$courseid;
    }

    if (! $context = get_context_instance_by_id($contextid)) {
        error("Context ID was incorrect (can't find it)");
    }

    $inmeta = 0;
    if ($context->contextlevel == CONTEXT_COURSE) {
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
        $course = clone($SITE);
    }

    require_login($course);

    if ($context->contextlevel == CONTEXT_COURSE) {
        require_login($context->instanceid);
    } else {
        require_login();
    }

    require_capability('moodle/role:assign', $context);

/// needed for tabs.php
    $overridableroles = get_overridable_roles($context);
    $assignableroles  = get_assignable_roles($context);

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

    $unlimitedperiod = get_string('unlimited');
    $defaultperiod = $course->enrolperiod;
    for ($i=1; $i<=365; $i++) {
        $seconds = $i * 86400;
        $periodmenu[$seconds] = get_string('numdays', '', $i);
    }

    $timeformat = get_string('strftimedate');
    $basemenu[0] = get_string('startdate') . ' (' . userdate($course->startdate, $timeformat) . ')';
    if(time() > $course->startdate) {
        $basemenu[3] = get_string('today') . ' (' . userdate(time(), $timeformat) . ')' ;
    }
    if($course->enrollable == 2) {
        if($course->enrolstartdate > 0) {
            $basemenu[4] = get_string('courseenrolstartdate') . ' (' . userdate($course->enrolstartdate, $timeformat) . ')';
        }
        if($course->enrolenddate > 0) {
            $basemenu[5] = get_string('courseenrolenddate') . ' (' . userdate($course->enrolenddate, $timeformat) . ')';
        }
    }
    
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

    if ($context->contextlevel == CONTEXT_USER) {
        /// course header
        if ($courseid != SITEID) {
            print_header("$fullname", "$fullname",
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">$strparticipants</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid\">$fullname</a> ->".$straction,
                      "", "", true, "&nbsp;", navmenu($course));

        /// site header
        } else {
            print_header("$course->fullname: $fullname", $course->fullname,
                        "<a href=\"$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid\">$fullname</a> -> $straction", "", "", true, "&nbsp;", navmenu($course));
        }

        $showroles = 1;
        $currenttab = 'assign';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else if ($context->contextlevel == CONTEXT_SYSTEM) {
        admin_externalpage_setup('assignroles');
        admin_externalpage_print_header();
    } else if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        admin_externalpage_setup('frontpageroles');
        admin_externalpage_print_header();
        $currenttab = '';
        $tabsmode = 'assign';
        include_once('tabs.php');
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
                if (!$adduser = clean_param($adduser, PARAM_INT)) {
                    continue;
                }
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
                    $timestart = $timemodified;
                    if($extendperiod > 0) {
                        $timeend = $timestart + $extendperiod;
                    } else {
                        $timeend = 0;
                    }
                    if (! role_assign($roleid, $adduser, 0, $context->id, $timestart, $timeend, $hidden)) {
                        $errors[] = "Could not add user with id $adduser to this role!";
                    }
                }
            }

        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {

            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            $topleveladmin = false;

            // we only worry about this if the role has doanything capability at site level
            if ($context->id == $sitecontext->id && $adminroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext)) {
                foreach ($adminroles as $adminrole) {
                    if ($adminrole->id == $roleid) {
                        $topleveladmin = true;
                    }
                }
            }

            foreach ($frm->removeselect as $removeuser) {
                $removeuser = clean_param($removeuser, PARAM_INT);

                if ($topleveladmin && ($removeuser == $USER->id)) {   // Prevent unassigning oneself from being admin
                    continue;
                }

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

    if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        print_heading_with_help(get_string('frontpageroles', 'admin'), 'assignroles');
    } else {
        print_heading_with_help(get_string('assignroles', 'role'), 'assignroles');
    }

    if ($context->contextlevel==CONTEXT_SYSTEM) {
        print_box(get_string('globalroleswarning', 'role'));
    }
    
    if ($roleid) {        /// prints a form to swap roles

    /// Get all existing participants in this context.
        // Why is this not done with get_users???

        if (!$contextusers = get_role_users($roleid, $context, false, 'u.id, u.firstname, u.lastname, u.email, r.hidden')) {
            $contextusers = array();
        }

        $select  = "username <> 'guest' AND deleted = 0 AND confirmed = 1";

        $usercount = count_records_select('user', $select) - count($contextusers);

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

        /// In the .html file below we loop through these results and exclude any in $contextusers

        echo '<div style="text-align:center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>';
        $assignableroles = array('0'=>get_string('listallroles', 'role').'...') + $assignableroles;
        popup_form("$CFG->wwwroot/$CFG->admin/roles/assign.php?userid=$userid&amp;courseid=$courseid&amp;contextid=$contextid&amp;roleid=",
            $assignableroles, 'switchrole', $roleid, '', '', '', false, 'self', $strroletoassign);
        echo '</div>';

        print_simple_box_start('center');
        include('assign.html');
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

    } else {   // Print overview table

        // sync metacourse enrolments if needed
        if ($inmeta) {
            sync_metacourse($course);
        }

        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '60%';
        $table->head = array(get_string('roles', 'role'), get_string('description'), get_string('users'));
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->align = array('right', 'left', 'center');

        foreach ($assignableroles as $roleid => $rolename) {
            $countusers = count_role_users($roleid, $context);
            $description = format_string(get_field('role', 'description', 'id', $roleid));
            $table->data[] = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>',$description, $countusers);
        }

        print_table($table);
    }

    print_footer($course);

?>
