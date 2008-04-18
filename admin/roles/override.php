<?php  //$Id$

    require_once('../../config.php');

    $contextid = required_param('contextid', PARAM_INT);   // context id
    $roleid    = optional_param('roleid', 0, PARAM_INT);   // requested role id
    $userid    = optional_param('userid', 0, PARAM_INT);   // needed for user tabs
    $courseid  = optional_param('courseid', 0, PARAM_INT); // needed for user tabs
    $cancel    = optional_param('cancel', 0, PARAM_BOOL);

    if (!$context = get_record('context', 'id', $contextid)) {
        error('Bad context ID');
    }

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
        error('No site ID');
    }

    if ($context->id == $sitecontext->id) {
        error('Can not override base role capabilities');
    }

    if (!has_capability('moodle/role:override', $context)) {
        error('You do not have permission to change overrides in this context!');
    }

    if ($courseid) {
        if (!$course = get_record('course', 'id', $courseid)) {
            error('Bad course ID');
        }
    } else {
        $course = clone($SITE);
        $courseid = SITEID;
    }

    require_login($course);

    $baseurl = 'override.php?contextid='.$context->id;
    if (!empty($userid)) {
        $baseurl .= '&amp;userid='.$userid;
    }
    if ($courseid != SITEID) {
        $baseurl .= '&amp;courseid='.$courseid;
    }

    if ($cancel) {
        redirect($baseurl);
    }

/// needed for tabs.php
    $overridableroles = get_overridable_roles($context, 'name', ROLENAME_BOTH);
    $assignableroles  = get_assignable_roles($context, 'name', ROLENAME_BOTH);

/// Get some language strings

    $strroletooverride = get_string('roletooverride', 'role');
    $straction         = get_string('overrideroles', 'role');
    $strcurrentrole    = get_string('currentrole', 'role');
    $strparticipants   = get_string('participants');

/// Make sure this user can override that role

    if ($roleid) {
        if (!user_can_override($context, $roleid)) {
            error ('you can not override this role in this context');
        }
    }

    if ($userid) {
        $user = get_record('user', 'id', $userid);
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
    }

/// get all cababilities
    $capabilities = fetch_context_capabilities($context);

/// Process incoming role override
    if ($data = data_submitted() and $roleid and confirm_sesskey()) {
        $allowed_values = array(CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT);

        $localoverrides = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id",
                                             '', 'capability, permission, id');

        foreach ($capabilities as $cap) {

            if (!isset($data->{$cap->name})) {
                //cap not specified in form
                continue;
            }

            if (islegacy($data->{$cap->name})) {
                continue;
            }

            $capname = $cap->name;
            $value = clean_param($data->{$cap->name}, PARAM_INT);
            if (!in_array($value, $allowed_values)) {
                 continue;
            }

            if (isset($localoverrides[$capname])) {
                // Something exists, so update it
                assign_capability($capname, $value, $roleid, $context->id, true);
            } else { // insert a record
                if ($value != CAP_INHERIT) {    // Ignore inherits
                    assign_capability($capname, $value, $roleid, $context->id);
                }
            }
        }

        // force accessinfo refresh for users visiting this context...
        mark_context_dirty($context->path);
        $rolename = get_field('role', 'name', 'id', $roleid);
        add_to_log($course->id, 'role', 'override', 'admin/roles/override.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
        redirect($baseurl);
    }


/// Print the header and tabs

    if ($context->contextlevel == CONTEXT_USER) {
        $navlinks = array();
        /// course header
        if ($course->id != SITEID) {
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
        $currenttab = 'override';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('frontpageroles');
        admin_externalpage_print_header();
        $currenttab = 'override';
        include_once('tabs.php');
    } else {
        $currenttab = 'override';
        include_once('tabs.php');
    }

    print_heading_with_help(get_string('overridepermissionsin', 'role', print_context_name($context)), 'overrides');

    if ($roleid) {
    /// prints a form to swap roles
        echo '<div class="selector">';
        $overridableroles = array('0'=>get_string('listallroles', 'role').'...') + $overridableroles;
        popup_form("$CFG->wwwroot/$CFG->admin/roles/override.php?userid=$userid&amp;courseid=$courseid&amp;contextid=$contextid&amp;roleid=",
            $overridableroles, 'switchrole', $roleid, '', '', '', false, 'self', $strroletooverride);
        echo '</div>';

        $parentcontexts = get_parent_contexts($context);
        if (!empty($parentcontexts)) {
            $parentcontext = array_shift($parentcontexts);
            $parentcontext = get_context_instance_by_id($parentcontext);
        } else {
            $parentcontext = $context; // site level in override??
        }

        $r_caps = role_context_capabilities($roleid, $parentcontext);

        $localoverrides = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id",
                                             '', 'capability, permission, id');

        $lang = str_replace('_utf8', '', current_language());

        if (!empty($capabilities)) {
            // Print the capabilities overrideable in this context
            print_simple_box_start('center');
            include_once('override.html');
            print_simple_box_end();

        } else {
            notice(get_string('nocapabilitiesincontext', 'role'),
                    $CFG->wwwroot.'/'.$CFG->admin.'/roles/'.$baseurl);
        }

    } else {   // Print overview table

        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '60%';
        $table->head = array(get_string('roles', 'role'), get_string('description'), get_string('overrides', 'role'));
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->align = array('right', 'left', 'center');

        foreach ($overridableroles as $roleid => $rolename) {
            $countusers = 0;
            $overridecount = count_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id");
            $description = format_string(get_field('role', 'description', 'id', $roleid));
            $table->data[] = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>', $description, $overridecount);
        }

        print_table($table);
    }

    print_footer($course);

?>
