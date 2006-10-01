<?php  //$Id$

    require_once('../../config.php');

    $contextid = required_param('contextid',PARAM_INT);    // context id
    $roleid    = optional_param('roleid', 0, PARAM_INT);   // requested role id
    $userid    = optional_param('userid', 0, PARAM_INT);   // needed for user tabs
    $courseid  = optional_param('courseid', 0, PARAM_INT); // needed for user tabs
    $cancel    = optional_param('cancel', 0, PARAM_BOOL);

    if (!$context = get_record('context', 'id', $contextid)) {
        error('Bad context ID');
    }

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID)) {
        error('No site ID');
    }

    if ($context->id == $sitecontext->id) {
        error ('Can not override base role capabilities');
    }

    require_capability('moodle/role:override', $context);   // Just to make sure

    if ($courseid) {
        if (!$course = get_record('course', 'id', $courseid)) {
            error('Bad course ID');
        }
    } else {
        $course = $SITE;
    }

    $baseurl = 'override.php?contextid='.$context->id;
    if (!empty($userid)) {
        $baseurl .= '&amp;userid='.$userid;
    }
    if (!empty($courseid)) {
        $baseurl .= '&amp;courseid='.$courseid;
    }

    if ($cancel) {
        redirect($baseurl);
    }


/// Get some language strings

    $strroletooverride = get_string('roletooverride', 'role');
    $stroverrideusers  = get_string('overrideusers', 'role');
    $straction         = get_string('overrideroles', 'role');
    $strcurrentrole    = get_string('currentrole', 'role');
    $strcurrentcontext = get_string('currentcontext', 'role');
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

/// Process incoming role override
    if ($data = data_submitted() and $roleid and confirm_sesskey()) {
        $allowed_values = array(CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT);
        $capabilities = fetch_context_capabilities($context); // capabilities applicable in this context

        $localoverrides = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id",
                                             '', 'capability, permission, id');

        foreach ($capabilities as $cap) {
            if (!isset($data->{$cap->name})) {
                continue;
            }
            $capname = $cap->name;
            $value = clean_param($data->{$cap->name}, PARAM_INT);
            if (!in_array($value, $allowed_values)) {
                 continue;
            }

            if (isset($localoverrides[$capname])) {    // Something exists, so update it
                if ($value == CAP_INHERIT) {       // inherit = delete
                    delete_records('role_capabilities', 'roleid', $roleid, 'contextid', $context->id,
                                                        'capability', $capname);
                } else {
                    $localoverride = new object();
                    $localoverride->id = $localoverrides[$capname]->id;
                    $localoverride->permission = $value;
                    $localoverride->timemodified = time();
                    $localoverride->modifierid = $USER->id;
                    if (!update_record('role_capabilities', $localoverride)) {
                        error('Could not update a capability!');
                    }
                }

            } else { // insert a record
                if ($value != CAP_INHERIT) {    // Ignore inherits
                    $localoverride = new object();
                    $localoverride->capability = $capname;
                    $localoverride->contextid = $context->id;
                    $localoverride->roleid = $roleid;
                    $localoverride->permission = $value;
                    $localoverride->timemodified = time();
                    $localoverride->modifierid = $USER->id;
                    if (!insert_record('role_capabilities', $localoverride)) {
                        error('Could not insert a capability!');
                    }
                }
            }
        }
        redirect($baseurl);
    }


/// Print the header and tabs

    if ($context->contextlevel == CONTEXT_USER) {

        /// course header
        if ($course->id != SITEID) {
            print_header("$fullname", "$fullname",
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                      <a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">$strparticipants</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$userid&amp;course=$course->id\">$fullname</a> -> $straction",
                      "", "", true, "&nbsp;", navmenu($course));

        /// site header
        } else {
            print_header("$course->fullname: $fullname", "$course->fullname",
                        "<a href=\"$CFG->wwwroot/user/view.php?id=$userid&amp;course=$course->id\">$fullname</a> -> $straction", "", "", true, "&nbsp;", navmenu($course));
        }
        $showroles = 1;
        $currenttab = 'override';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else {
        $currenttab = '';
        $tabsmode = 'override';
        include_once('tabs.php');
    }


    $overridableroles = get_overridable_roles($context);

    if ($roleid) {
    /// prints a form to swap roles
        echo '<form name="rolesform" action="override.php" method="get">';
        echo '<div align="center">'.$strcurrentcontext.': '.print_context_name($context).'<br/>';
        if ($userid) {
            echo '<input type="hidden" name="userid" value="'.$userid.'" />';
        }
        if ($courseid) {
            echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
        }
        echo '<input type="hidden" name="contextid" value="'.$context->id.'" />'.$strroletooverride.': ';
        choose_from_menu ($overridableroles, 'roleid', $roleid, get_string('listallroles', 'role').'...', $script='rolesform.submit()');
        echo '</div></form>';

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

        // Get the capabilities overrideable in this context
        if ($capabilities = fetch_context_capabilities($context)) {
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
        $table->width = '20%';
        $table->head = array(get_string('roles', 'role'), get_string('overrides', 'role'));
        $table->wrap = array('nowrap', 'nowrap');
        $table->align = array('right', 'center');

        foreach ($overridableroles as $roleid => $rolename) {
            $countusers = 0;
            $overridecount = count_records_select('role_capabilities', "roleid = $roleid AND contextid = $context->id");
            $table->data[] = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>', $overridecount);
        }

        print_table($table);
    }

    print_footer($course);

?>
