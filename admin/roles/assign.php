<?php // $Id$
      // Script to assign users to contexts

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    define("MAX_USERS_PER_PAGE", 5000);
    define("MAX_USERS_TO_LIST_PER_ROLE", 10);

    $contextid      = required_param('contextid',PARAM_INT); // context id
    $roleid         = optional_param('roleid', 0, PARAM_INT); // required role id
    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $hidden         = optional_param('hidden', 0, PARAM_BOOL); // whether this assignment is hidden
    $extendperiod   = optional_param('extendperiod', 0, PARAM_INT);
    $extendbase     = optional_param('extendbase', 0, PARAM_INT);
    $userid         = optional_param('userid', 0, PARAM_INT); // needed for user tabs
    $courseid       = optional_param('courseid', 0, PARAM_INT); // needed for user tabs

    $errors = array();

    $baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/role/assign.php?contextid=' . $contextid;
    if (!empty($userid)) {
        $baseurl .= '&amp;userid='.$userid;
    }
    if (!empty($courseid)) {
        $baseurl .= '&amp;courseid='.$courseid;
    }

    if (! $context = get_context_instance_by_id($contextid)) {
        print_error('wrongcontextid', 'error');
    }
    $isfrontpage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID;
    $contextname = print_context_name($context);

    $inmeta = 0;
    if ($context->contextlevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        if ($course = $DB->get_record('course', array('id'=>$courseid))) {
            $inmeta = $course->metacourse;
        } else {
            print_error('invalidcourse', 'error');
        }

    } else if (!empty($courseid)){ // we need this for user tabs in user context
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourse', 'error');
        }

    } else {
        $courseid = SITEID;
        $course = clone($SITE);
    }

    require_login($course);

    require_capability('moodle/role:assign', $context);

/// needed for tabs.php

    $overridableroles = get_overridable_roles($context, ROLENAME_BOTH);
    list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);

/// Get some language strings

    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('assignroles', 'role');
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
    $today = time();
    $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

    // MDL-12420, preventing course start date showing up as an option at system context and front page roles.
    if ($course->startdate > 0) {
        $basemenu[0] = get_string('startdate') . ' (' . userdate($course->startdate, $timeformat) . ')';
    }
    if ($course->enrollable != 2 || ($course->enrolstartdate == 0 || $course->enrolstartdate <= $today) && ($course->enrolenddate == 0 || $course->enrolenddate > $today)) {
        $basemenu[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;
    }
    if($course->enrollable == 2) {
        if($course->enrolstartdate > 0) {
            $basemenu[4] = get_string('courseenrolstartdate') . ' (' . userdate($course->enrolstartdate, $timeformat) . ')';
        }
        if($course->enrolenddate > 0) {
            $basemenu[5] = get_string('courseenrolenddate') . ' (' . userdate($course->enrolenddate, $timeformat) . ')';
        }
    }

/// Make sure this user can assign this role

    if ($roleid) {
        if (!isset($assignableroles[$roleid])) {
            error ('you can not override this role in this context');
        }
    }

    if ($userid) {
        $user = $DB->get_record('user', array('id'=>$userid));
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
    }


/// Print the header and tabs

    if ($context->contextlevel == CONTEXT_USER) {
        /// course header
        $navlinks = array();
        if ($courseid != SITEID) {
            if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $course->id))) {
                $navlinks[] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$course->id", 'type' => 'misc');
            }
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);

            print_header("$fullname", "$fullname", $navigation, "", "", true, "&nbsp;", navmenu($course));

        /// site header
        } else {
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header("$course->fullname: $fullname", $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));
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
        $currenttab = 'assign';
        include_once('tabs.php');
    } else {
        $currenttab = 'assign';
        include_once('tabs.php');
    }


/// Process incoming role assignment

    if ($frm = data_submitted()) {

        if ($add and !empty($frm->addselect) and confirm_sesskey()) {

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
                            $erruser = $DB->get_record('user', array('id'=>$adduser), 'id, firstname, lastname');
                            $errors[] = get_string('metaassignerror', 'role', fullname($erruser));
                            $allow = false;
                        }
                    }
                }
                if ($allow) {
                    switch($extendbase) {
                        case 0:
                            $timestart = $course->startdate;
                            break;
                        case 3:
                            $timestart = $today;
                            break;
                        case 4:
                            $timestart = $course->enrolstartdate;
                            break;
                        case 5:
                            $timestart = $course->enrolenddate;
                            break;
                    }

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
            
            $rolename = $DB->get_field('role', 'name', array('id'=>$roleid));
            add_to_log($course->id, 'role', 'assign', 'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
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
                        $erruser = $DB->get_record('user', array('id'=>$removeuser), 'id, firstname, lastname');
                        $errors[] = get_string('metaunassignerror', 'role', fullname($erruser));
                        $allow = false;
                    }
                }
            }
            
            $rolename = $DB->get_field('role', 'name', array('id'=>$roleid));
            add_to_log($course->id, 'role', 'unassign', 'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
        } else if ($showall) {
            $searchtext = '';
        }
    }

    if ($isfrontpage) {
        print_heading_with_help(get_string('frontpageroles', 'admin'), 'assignroles');
    } else {
        print_heading_with_help(get_string('assignrolesin', 'role', $contextname), 'assignroles');
    }

    if ($context->contextlevel==CONTEXT_SYSTEM) {
        print_box(get_string('globalroleswarning', 'role'));
    }

    if ($roleid) {

    /// Get all existing participants in this context.
        // Why is this not done with get_users???

        if (!$contextusers = get_role_users($roleid, $context, false, 'u.id, u.firstname, u.lastname, u.email, ra.hidden')) {
            $contextusers = array();
        }

        $select  = "username <> 'guest' AND deleted = 0 AND confirmed = 1";
        $params = array();

        $usercount = $DB->count_records_select('user', $select, $params) - count($contextusers);

        $searchtext = trim($searchtext);

        if ($searchtext !== '') {   // Search for a subset of remaining users
            $LIKE      = $DB->sql_ilike();
            $FULLNAME  = $DB->sql_fullname();

            $select .= " AND ($FULLNAME $LIKE :search1 OR email $LIKE :search2) ";
            $params['search1'] = "%$searchtext%";
            $params['search2'] = "%$searchtext%";
        }

        if ($context->contextlevel > CONTEXT_COURSE) { // mod or block (or group?)

            /************************************************************************
             *                                                                      *
             * context level is above or equal course context level                 *
             * in this case we pull out all users matching search criteria (if any) *
             *                                                                      *
             * MDL-11324                                                            *
             * a mini get_users_by_capability() call here, this is done instead of  *
             * get_users_by_capability() because                                    *
             * 1) get_users_by_capability() does not deal with searching by name    *
             * 2) exceptions array can be potentially large for large courses       *
             * 3) $DB->get_recordset_sql() is more efficient                        *
             *                                                                      *
             ************************************************************************/

            if ($possibleroles = get_roles_with_capability('moodle/course:view', CAP_ALLOW, $context)) {

                $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, get_context_instance(CONTEXT_SYSTEM));

                $validroleids = array();
                foreach ($possibleroles as $possiblerole) {
                    if (isset($doanythingroles[$possiblerole->id])) {  // We don't want these included
                            continue;
                    }
                    if ($caps = role_context_capabilities($possiblerole->id, $context, 'moodle/course:view')) { // resolved list
                        if (isset($caps['moodle/course:view']) && $caps['moodle/course:view'] > 0) { // resolved capability > 0
                            $validroleids[] = $possiblerole->id;
                        }
                    }
                }

                if ($validroleids) {
                    $roleids =  '('.implode(',', $validroleids).')';

                    $fields      = "SELECT u.id, u.firstname, u.lastname, u.email";
                    $countfields = "SELECT COUNT('x')";

                    $sql   = " FROM {user} u
                               JOIN {role_assignments} ra ON ra.userid = u.id
                               JOIN {role} r ON r.id = ra.roleid
                              WHERE ra.contextid ".get_related_contexts_string($context)."
                                    AND $select AND ra.roleid in $roleids
                                    AND u.id NOT IN (
                                       SELECT u.id
                                         FROM {role_assignments} r, {user} u
                                        WHERE r.contextid = :contextid
                                              AND u.id = r.userid
                                              AND r.roleid = :roleid)";
                    $params['contextid'] = $contextid;
                    $params['roleid']    = $roleid;

                    $availableusers = $DB->get_recordset_sql("$fields $sql", $params);
                    $usercount      = $DB->count_records_sql("$countfields $sql", $params);

                } else {
                    $availableusers = array();
                    $usercount = 0;
                }
            }

        } else {

            /************************************************************************
             *                                                                      *
             * context level is above or equal course context level                 *
             * in this case we pull out all users matching search criteria (if any) *
             *                                                                      *
             ************************************************************************/

            /// MDL-11111 do not include user already assigned this role in this context as available users
            /// so that the number of available users is right and we save time looping later
            $fields      = "SELECT id, firstname, lastname, email";
            $countfields = "SELECT COUNT('x')";

            $sql = " FROM {user}
                    WHERE $select
                          AND id NOT IN (
                             SELECT u.id
                               FROM {role_assignments} r, {user} u
                              WHERE r.contextid = :contextid
                                    AND u.id = r.userid
                                    AND r.roleid = :roleid)";
            $order = "ORDER BY lastname ASC, firstname ASC";

            $params['contextid'] = $contextid;
            $params['roleid']    = $roleid;

            $availableusers = $DB->get_recordset_sql("$fields $sql $order", $params);
            $usercount      = $DB->count_records_sql("$countfields $sql", $params);
        }

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

    /// Print a form to swap roles, and a link back to the all roles list.
        echo '<div class="backlink">';
        popup_form($baseurl . '&amp;roleid=', $nameswithcounts, 'switchrole',
                $roleid, '', '', '', false, 'self', get_string('assignanotherrole', 'role'));
        echo '<p><a href="' . $baseurl . '">' . get_string('backtoallroles', 'role') . '</a></p>';
        echo '</div>';

    } else {   // Print overview table

        // Print instruction
        print_heading(get_string('chooseroletoassign', 'role'), 'center', 3);

        // sync metacourse enrolments if needed
        if ($inmeta) {
            sync_metacourse($course);
        }

        // Get the names of role holders for roles with between 1 and MAX_USERS_TO_LIST_PER_ROLE users,
        // and so determine whether to show the extra column. 
        $rolehodlernames = array();
        $strmorethanmax = get_string('morethan', 'role', MAX_USERS_TO_LIST_PER_ROLE);
        $showroleholders = false;
        foreach ($assignableroles as $roleid => $rolename) {
            $roleusers = '';
            if (0 < $assigncounts[$roleid] && $assigncounts[$roleid] <= MAX_USERS_TO_LIST_PER_ROLE) {
                $roleusers = get_role_users($roleid, $context, false, 'u.id, u.lastname, u.firstname');
                if (!empty($roleusers)) {
                    $strroleusers = array();
                    foreach ($roleusers as $user) {
                        $strroleusers[] = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '" >' . fullname($user) . '</a>';
                    }
                    $rolehodlernames[$roleid] = implode('<br />', $strroleusers);
                    $showroleholders = true;
                }
            } else if ($assigncounts[$roleid] > MAX_USERS_TO_LIST_PER_ROLE) {
                $rolehodlernames[$roleid] = '<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$strmorethanmax.'</a>';
            } else {
                $rolehodlernames[$roleid] = '';
            }
        }

        // Print overview table
        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '60%';
        $table->head = array(get_string('role'), get_string('description'), get_string('userswiththisrole', 'role'));
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->align = array('left', 'left', 'center');
        if ($showroleholders) {
            $table->headspan = array(1, 1, 2);
            $table->wrap[] = 'nowrap';
            $table->align[] = 'left';
        }

        foreach ($assignableroles as $roleid => $rolename) {
            $description = format_string($DB->get_field('role', 'description', array('id'=>$roleid)));
            $row = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>',$description, $assigncounts[$roleid]);
            if ($showroleholders) {
                $row[] = $rolehodlernames[$roleid];
            }
            $table->data[] = $row;
        }

        print_table($table);

        if (!$isfrontpage && ($url = get_context_url($context))) {
            echo '<div class="backlink"><a href="' . $url . '">' .
                get_string('backto', '', $contextname) . '</a></div>';
        }
    }

    print_footer($course);
?>
