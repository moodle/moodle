<?php  //$Id$

    require_once('../../config.php');

    $contextid = required_param('contextid', PARAM_INT);   // context id
    $roleid    = optional_param('roleid', 0, PARAM_INT);   // requested role id
    $userid    = optional_param('userid', 0, PARAM_INT);   // needed for user tabs
    $courseid  = optional_param('courseid', 0, PARAM_INT); // needed for user tabs

    $baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/override.php?contextid=' . $contextid;
    if (!empty($userid)) {
        $baseurl .= '&amp;userid=' . $userid;
    }
    if ($courseid && $courseid != SITEID) {
        $baseurl .= '&amp;courseid=' . $courseid;
    }

    if (!$context = $DB->get_record('context', array('id'=>$contextid))) {
        print_error('wrongcontextid', 'error');
    }
    $isfrontpage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID;
    $contextname = print_context_name($context);

    if ($context->contextlevel == CONTEXT_SYSTEM) {
        print_error('cannotoverridebaserole', 'error');
    }

    if ($context->contextlevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourse');
        }
    } if ($courseid) { // we need this for user tabs in user context
        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
            print_error('invalidcourse');
        }
    } else {
        $course = clone($SITE);
        $courseid = SITEID;
    }

    require_login($course);

    $safeoverridesonly = !has_capability('moodle/role:override', $context);
    if ($safeoverridesonly) {
        require_capability('moodle/role:safeoverride', $context);
    }

    if (optional_param('cancel', false, PARAM_BOOL)) {
        redirect($baseurl);
    }

/// These are needed early because of tabs.php
    $assignableroles  = get_assignable_roles($context, ROLENAME_BOTH);
    list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);

/// Make sure this user can override that role
    if ($roleid && !isset($overridableroles[$roleid])) {
        $a = stdClass;
        $a->role = $roleid;
        $a->context = $contextname;
        print_error('cannotoverriderolehere', '', get_context_url($context), $a);
    }

/// Get some language strings
    $straction = get_string('overrideroles', 'role'); // Used by tabs.php

/// Work out an appropriate page title.
    if ($roleid) {
        $a = new stdClass;
        $a->role = $overridableroles[$roleid];
        $a->context = $contextname;
        $title = get_string('overridepermissionsforrole', 'role', $a);
    } else {
        if ($isfrontpage) {
            $title = get_string('frontpageoverrides', 'admin');
        } else {
            $title = get_string('overridepermissionsin', 'role', $contextname);
        }
    }

/// get all cababilities
    $safeoverridenotice = false;
    if ($roleid) {
        $capabilities = fetch_context_capabilities($context);
        if (!$capabilities) {
            $capabilities = array();
        }
        // Determine which capabilities should be locked.
        foreach ($capabilities as $capname=>$capability) {
            $capabilities[$capname]->locked = false;
            if ($safeoverridesonly && !is_safe_capability($capability)) {
                $capabilities[$capname]->locked = true;
                $safeoverridenotice = true;
            }
        }
    }

    if ($roleid && optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    /// Process incoming role override
        $localoverrides = $DB->get_records('role_capabilities', array('roleid' => $roleid,
                'contextid' => $context->id), '', 'capability,permission,id');

        foreach ($capabilities as $cap) {
            if ($cap->locked || is_legacy($cap->name)) {
                //user not allowed to change this cap
                continue;
            }

            $capname = $cap->name;
            $value = optional_param($capname, null, PARAM_PERMISSION);
            if (is_null($value)) {
                //cap not specified in form
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
        $rolename = $overridableroles[$roleid];
        add_to_log($course->id, 'role', 'override', 'admin/roles/override.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
        redirect($baseurl);
    }

/// Print the header and tabs
    require_js(array('yui_yahoo', 'yui_dom', 'yui_event'));
    require_js($CFG->admin . '/roles/roles.js');
    if ($context->contextlevel == CONTEXT_USER) {
        $user = $DB->get_record('user', array('id'=>$userid));
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

        /// course header
        $navlinks = array();
        if ($course->id != SITEID) {
            if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $course->id))) {
                $navlinks[] = array('name' => get_string('participants'), 'link' => "$CFG->wwwroot/user/index.php?id=$course->id", 'type' => 'misc');
            }
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header($title, "$fullname", $navigation, "", "", true, "&nbsp;", navmenu($course));

        /// site header
        } else {
            $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header($title, $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));
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

    print_heading_with_help($title, 'overrides');

    if ($roleid) {
    /// Show UI for overriding roles.

    /// Get the capabiltites from the parent context, so that can be shown in the interface.
        $parentcontext = get_context_instance_by_id(get_parent_contextid($context));
        $r_caps = role_context_capabilities($roleid, $parentcontext);

    /// And get the current overrides in this context.
        $localoverrides = $DB->get_records('role_capabilities', array('roleid' => $roleid,
                'contextid' => $context->id), '', 'capability,permission,id');

        if (!empty($capabilities)) {
            // Print the capabilities overrideable in this context
            print_box_start('generalbox boxwidthwide boxaligncenter');

            $allrisks = get_all_risks();
            $allpermissions = array(
                CAP_INHERIT => 'inherit',
                CAP_ALLOW => 'allow',
                CAP_PREVENT => 'prevent' ,
                CAP_PROHIBIT => 'prohibit',
            );
            $strperms = array();
            foreach ($allpermissions as $permname) {
                $strperms[$permname] =  get_string($permname, 'role');
            }
?>
<form id="overrideform" action=""<?php echo $baseurl; ?>" method="post"><div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()) ?>" />

    <table class="rolecap" id="overriderolestable">
        <tr>
            <th class="name" align="left" scope="col"><?php print_string('capability','role') ?></th>
<?php
            foreach ($strperms as $permname => $strpermname) {
                echo '<th class="' . $permname . '" scope="col">' . $strpermname . '</th>';
            }
            echo '<th class="risk" colspan="' . count($allrisks) . '" scope="col">' . get_string('risks','role') . '</th>';
            echo "</tr>\n";

        /// Loop over capabilities.
            $contextlevel = 0;
            $component = '';
            foreach ($capabilities as $capability) {

        /// Legacy caps and doanything should not be overriden - we must use proper capabilities if needed
            if (is_legacy($capability->name)) {
                continue;
            }

        /// Prints a breaker if component or name or context level has changed
            if (component_level_changed($capability, $component, $contextlevel)) {
                echo '<tr class="rolecapheading header"><td colspan="' . (2 + count($allpermissions) + count($allrisks)) . '" class="header"><strong>' .
                        get_component_string($capability->component, $capability->contextlevel) .
                        '</strong></td></tr>';
            }
            $contextlevel = $capability->contextlevel;
            $component = $capability->component;

        /// Check the capability override for this cap, this role in this context
            if (isset($localoverrides[$capability->name])) {
                $localpermission = $localoverrides[$capability->name]->permission;
            } else {
                $localpermission = 0;  // Just inherit
            }

            if (!isset($r_caps[$capability->name])) {
                $r_caps[$capability->name] = CAP_INHERIT;
            }

            $disabled = '';
            if ($capability->locked || $r_caps[$capability->name] == CAP_PROHIBIT) {
                $disabled = ' disabled="disabled"';
            }

        /// Start the table row.
            $rowclasses = array('rolecap');
            foreach ($allrisks as $riskname => $risk) {
                if ($risk & (int)$capability->riskbitmask) {
                    $rowclasses[] = $riskname;
                }
            }
            echo '<tr class="' . implode(' ', $rowclasses) . '">';

        /// Table cell for the capability name.
            echo '<td class="name"><span class="cap-desc">' . get_capability_docs_link($capability) .
                    '<span class="cap-name">' . $capability->name . '</span></span></td>';

        /// One cell for each possible permission.
            foreach ($allpermissions as $perm => $permname) {
                $extraclass = '';
                if ($perm != CAP_INHERIT && $perm == $r_caps[$capability->name]) {
                    $extraclass = ' capcurrent';
                }
                $checked = '';
                if ($localpermission == $perm) {
                    $checked = ' checked="checked"';
                }
                echo '<td class="' . $permname . $extraclass . '">';
                echo '<input type="radio" title="' . $strperms[$permname] . '" name="' . $capability->name .
                        '" value="' . $perm . '"' . $checked . $disabled . ' />';
                echo '</td>';
            }

        /// One cell for each possible risk.
            foreach ($allrisks as $riskname => $risk) {
                echo '<td class="risk ' . str_replace('risk', '', $riskname) . '">';
                if ($risk & (int)$capability->riskbitmask) {
                    print_risk_icon($riskname);
                }
                echo '</td>';
            }

        /// End of the row.
            echo "</tr>\n";
        }
?>
    </table>
    <div class="submit buttons">
        <input type="submit" name="savechanges" value="<?php print_string('savechanges') ?>" />
        <input type="submit" name="cancel" value="<?php print_string('cancel') ?>" />
    </div>

    <?php
            if ($safeoverridenotice) {
                echo '<div class="sefeoverridenotice">' . get_string('safeoverridenotice', 'role') . "</div>\n";
            }

            if (count($capabilities) > 12) {
                print_js_call('cap_table_filter.init',
                        array('overriderolestable', get_string('search'), get_string('clear')));
            }

            echo "</div></form>\n";
            print_box_end();

        } else {
            print_box(get_string('nocapabilitiesincontext', 'role'), 'generalbox boxaligncenter');
        }

    /// Print a form to swap roles, and a link back to the all roles list.
        echo '<div class="backlink">';
        popup_form($baseurl . '&amp;roleid=', $nameswithcounts, 'switchrole',
                $roleid, '', '', '', false, 'self',  get_string('overrideanotherrole', 'role'));
        echo '<p><a href="' . $baseurl . '">' . get_string('backtoallroles', 'role') . '</a></p>';
        echo '</div>';

    } else {
    /// Show UI for choosing a role to assign.

        $table->tablealign = 'center';
        $table->width = '60%';
        $table->head = array(get_string('role'), get_string('description'), get_string('overrides', 'role'));
        $table->wrap = array('nowrap', '', 'nowrap');
        $table->align = array('right', 'left', 'center');

        foreach ($overridableroles as $roleid => $rolename) {
            $countusers = 0;
            $description = format_string($DB->get_field('role', 'description', array('id'=>$roleid)));
            $table->data[] = array('<a href="'.$baseurl.'&amp;roleid='.$roleid.'">'.$rolename.'</a>',
                    $description, $overridecounts[$roleid]);
        }

        print_table($table);

        if (!$isfrontpage && ($url = get_context_url($context))) {
            echo '<div class="backlink"><a href="' . $url . '">' .
                get_string('backto', '', $contextname) . '</a></div>';
        }
    }

    print_footer($course);

?>