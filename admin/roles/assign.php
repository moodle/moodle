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
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $hidden         = optional_param('hidden', 0, PARAM_BOOL); // whether this assignment is hidden
    $extendperiod   = optional_param('extendperiod', 0, PARAM_INT);
    $extendbase     = optional_param('extendbase', 0, PARAM_INT);
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

    require_capability('moodle/role:assign', $context);

/// needed for tabs.php

    $overridableroles = get_overridable_roles($context, 'name', ROLENAME_BOTH);
    $assignableroles  = get_assignable_roles($context, 'name', ROLENAME_BOTH);

/// Get some language strings

    $strpotentialusers = get_string('potentialusers', 'role');
    $strexistingusers = get_string('existingusers', 'role');
    $straction = get_string('assignroles', 'role');
    $strroletoassign = get_string('roletoassign', 'role');
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

/// Make sure this user can assign that role

    if ($roleid) {
        if (!isset($assignableroles[$roleid])) {
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
        admin_externalpage_setup('assignroles', '', array('contextid' => $contextid, 'roleid' => $roleid));
        admin_externalpage_print_header('');
    } else if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        admin_externalpage_setup('frontpageroles', '', array('contextid' => $contextid, 'roleid' => $roleid));
        admin_externalpage_print_header('');
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
                            $erruser = get_record('user', 'id', $adduser, '','','','', 'id, firstname, lastname');
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
            
            $rolename = get_field('role', 'name', 'id', $roleid);
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
                        $erruser = get_record('user', 'id', $removeuser, '','','','', 'id, firstname, lastname');
                        $errors[] = get_string('metaunassignerror', 'role', fullname($erruser));
                        $allow = false;
                    }
                }
            }
            
            $rolename = get_field('role', 'name', 'id', $roleid);
            add_to_log($course->id, 'role', 'unassign', 'admin/roles/assign.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
        } else if ($showall) {
            $searchtext = '';
            $previoussearch = 0;
        }
        
        
    
    }

    if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        print_heading_with_help(get_string('frontpageroles', 'admin'), 'assignroles');
    } else {
        print_heading_with_help(get_string('assignrolesin', 'role', print_context_name($context)), 'assignroles');
    }

    if ($context->contextlevel==CONTEXT_SYSTEM) {
        print_box(get_string('globalroleswarning', 'role'));
    }

    if ($roleid) {        /// prints a form to swap roles

    /// Get all existing participants in this context.
        // Why is this not done with get_users???

        if (!$contextusers = get_role_users($roleid, $context, false, 'u.id, u.firstname, u.lastname, u.email, ra.hidden')) {
            $contextusers = array();
        }

        $select  = "username <> 'guest' AND deleted = 0 AND confirmed = 1";

        $usercount = count_records_select('user', $select) - count($contextusers);

        $searchtext = trim($searchtext);

        if ($searchtext !== '') {   // Search for a subset of remaining users
            $LIKE      = sql_ilike();
            $FULLNAME  = sql_fullname();

            $selectsql = " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') ";
            $select  .= $selectsql;
        } else {
            $selectsql = "";
        }

        if ($context->contextlevel > CONTEXT_COURSE && !is_inside_frontpage($context)) { // mod or block (or group?)

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
             * 3) get_recordset_sql() is more efficient                             *
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

                    $select = " SELECT DISTINCT u.id, u.firstname, u.lastname, u.email";
                    $countselect = "SELECT COUNT(u.id)";
                    $from   = " FROM {$CFG->prefix}user u
                                INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                                INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid";
                    $where  = " WHERE ra.contextid ".get_related_contexts_string($context)."
                                AND u.deleted = 0
                                AND ra.roleid in $roleids";
                    $excsql = " AND u.id NOT IN (
                                    SELECT u.id
                                    FROM {$CFG->prefix}role_assignments r,
                                    {$CFG->prefix}user u
                                    WHERE r.contextid = $contextid
                                    AND u.id = r.userid
                                    AND r.roleid = $roleid
                                    $selectsql)";

                    $availableusers = get_recordset_sql($select . $from . $where . $selectsql . $excsql);
                }

                $usercount =  $availableusers->_numOfRows;
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
            $availableusers = get_recordset_sql('SELECT id, firstname, lastname, email
                                                FROM '.$CFG->prefix.'user
                                                WHERE '.$select.'
                                                AND id NOT IN (
                                                    SELECT u.id
                                                    FROM '.$CFG->prefix.'role_assignments r,
                                                    '.$CFG->prefix.'user u
                                                    WHERE r.contextid = '.$contextid.'
                                                    AND u.id = r.userid
                                                    AND r.roleid = '.$roleid.'
                                                    '.$selectsql.')
                                                ORDER BY lastname ASC, firstname ASC');

            $usercount = $availableusers->_numOfRows;         
        }

        echo '<div class="selector">';
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
		
		//Back to Assign Roles button
		echo "<br/>";
		echo "<div class='continuebutton'>";
		print_single_button('assign.php', array('contextid' => $contextid), get_string('assignrolesin', 'role', print_context_name($context)));
		echo "</div>";

    } else {   // Print overview table

        // sync metacourse enrolments if needed
        if ($inmeta) {
            sync_metacourse($course);
        }

        // Get the names of role holders for roles with between 1 and MAX_USERS_TO_LIST_PER_ROLE users,
        // and so determine whether to show the extra column. 
        $rolehodlercount = array();
        $rolehodlernames = array();
        $strmorethanten = get_string('morethan', 'role', MAX_USERS_TO_LIST_PER_ROLE);
        $showroleholders = false;
        foreach ($assignableroles as $roleid => $rolename) {
            $countusers = count_role_users($roleid, $context);
            $rolehodlercount[$roleid] = $countusers;
            $roleusers = '';
            if (0 < $countusers && $countusers <= MAX_USERS_TO_LIST_PER_ROLE) {
                $roleusers = get_role_users($roleid, $context, false, 'u.id, u.lastname, u.firstname');
                if (!empty($roleusers)) {
                    $strroleusers = array();
                    foreach ($roleusers as $user) {
                        $strroleusers[] = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '" >' . fullname($user) . '</a>';
                    }
                    $rolehodlernames[$roleid] = implode('<br />', $strroleusers);
                    $showroleholders = true;
                }
            } else if ($countusers > MAX_USERS_TO_LIST_PER_ROLE) {
                $rolehodlernames[$roleid] = '<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$strmorethanten.'</a>';
            } else {
                $rolehodlernames[$roleid] = '';
            }
        }
		
		
        // Print overview table
        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '60%';
        $table->head = array(get_string('roles', 'role'), get_string('description'), get_string('users'));
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->align = array('right', 'left', 'center');
        if ($showroleholders) {
            $table->head[] = '';
            $table->wrap[] = 'nowrap';
            $table->align[] = 'left';
        }

        foreach ($assignableroles as $roleid => $rolename) {
            $description = format_string(get_field('role', 'description', 'id', $roleid));
            $row = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>',$description, $rolehodlercount[$roleid]);
            if ($showroleholders) {
                $row[] = $rolehodlernames[$roleid];
            }
            $table->data[] = $row;
        }
        print_table($table);
		
	   //Continue to Course Button
	   echo "<br/>";
	   echo "<div class='continuebutton'>";
	   print_single_button($CFG->wwwroot.'/course/view.php', array('id' => $courseid), get_string('continuetocourse'));
	   echo "</div>";
    }
	
    print_footer($course);
?>
