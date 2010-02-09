<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Lets you override role definitions in contexts.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

    $contextid = required_param('contextid', PARAM_INT);   // context id
    $roleid = optional_param('roleid', 0, PARAM_INT);   // requested role id
    $userid = optional_param('userid', 0, PARAM_INT);   // needed for user tabs
    $courseid = optional_param('courseid', 0, PARAM_INT); // needed for user tabs
    $returnurl      = optional_param('returnurl', null, PARAM_LOCALURL);

/// Get the base URL for this and related pages into a convenient variable.
    $urlparams = array('contextid' => $contextid);
    if (!empty($userid)) {
        $urlparams['userid'] = $userid;
    }
    if ($courseid && $courseid != SITEID) {
        $urlparams['courseid'] = $courseid;
    }
    if ($returnurl) {
        $urlparams['returnurl'] = $returnurl;
    }
    $PAGE->set_url('/admin/roles/override.php', $urlparams);
    $baseurl = $PAGE->url->out();

/// Validate the contextid parameter.
    if (!$context = $DB->get_record('context', array('id'=>$contextid))) {
        print_error('wrongcontextid', 'error');
    }
    $isfrontpage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID;
    $contextname = print_context_name($context);

    if ($context->contextlevel == CONTEXT_SYSTEM) {
        print_error('cannotoverridebaserole', 'error');
    }

/// Validate the courseid parameter.
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

/// Check access permissions.
    require_login($course);
    $safeoverridesonly = !has_capability('moodle/role:override', $context);
    if ($safeoverridesonly) {
        require_capability('moodle/role:safeoverride', $context);
    }

/// Handle the cancel button.
    if (optional_param('cancel', false, PARAM_BOOL)) {
        redirect($baseurl);
    }

/// Handle the toggle advanced mode button.
    $showadvanced = get_user_preferences('overridepermissions_showadvanced', false);
    if (optional_param('toggleadvanced', false, PARAM_BOOL)) {
        $showadvanced = !$showadvanced;
        set_user_preference('overridepermissions_showadvanced', $showadvanced);
    }

/// These are needed early because of tabs.php
    $assignableroles  = get_assignable_roles($context, ROLENAME_BOTH);
    list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);

/// Make sure this user can override that role
    if ($roleid && !isset($overridableroles[$roleid])) {
        $a = new stdClass;
        $a->roleid = $roleid;
        $a->context = $contextname;
        print_error('cannotoverriderolehere', '', get_context_url($context), $a);
    }


/// If we are actually overriding a role, create the table object, and save changes if appropriate.
    if ($roleid) {
        if ($showadvanced) {
            $overridestable = new override_permissions_table_advanced($context, $roleid, $safeoverridesonly);
        } else {
            $overridestable = new override_permissions_table_basic($context, $roleid, $safeoverridesonly);
        }
        $overridestable->read_submitted_permissions();

        if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
            $overridestable->save_changes();
            $rolename = $overridableroles[$roleid];
            add_to_log($course->id, 'role', 'override', 'admin/roles/override.php?contextid='.$context->id.'&roleid='.$roleid, $rolename, '', $USER->id);
            redirect($baseurl);
        }
    }

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

    /// Print the header and tabs
    $straction = get_string('overrideroles', 'role'); // Used by tabs.php
    if ($context->contextlevel == CONTEXT_USER) {
        $user = $DB->get_record('user', array('id'=>$userid));
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

        $PAGE->set_title($title);
        if ($courseid != SITEID) {
            if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $course->id))) {
                $PAGE->navbar->add(get_string('participants'), new moodle_url('/user/index.php', array('id'=>$course->id)));
            }
            $PAGE->set_heading($fullname);
        } else {
            $PAGE->set_heading($course->fullname);
        }
        $PAGE->navbar->add($fullname, new moodle_url("$CFG->wwwroot/user/view.php", array('id'=>$userid,'course'=>$courseid)));
        $PAGE->navbar->add($straction);
        echo $OUTPUT->header();

        $showroles = 1;
        $currenttab = 'override';
        include_once($CFG->dirroot.'/user/tabs.php');
    } else if ($context->contextlevel==CONTEXT_COURSE and $context->instanceid == SITEID) {
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('frontpageroles', '', array('contextid' => $contextid, 'roleid' => $roleid), $CFG->wwwroot . '/' . $CFG->admin . '/roles/override.php');
        admin_externalpage_print_header();
        $currenttab = 'override';
        include_once('tabs.php');
    } else {
        $currenttab = 'override';
        include_once('tabs.php');
    }

    echo $OUTPUT->heading_with_help($title, 'overrides');

    if ($roleid) {
    /// Show UI for overriding roles.

        if (!empty($capabilities)) {
            echo $OUTPUT->box(get_string('nocapabilitiesincontext', 'role'), 'generalbox boxaligncenter');

        } else {
            // Print the capabilities overrideable in this context
            echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');

            if ($showadvanced) {
                $showadvancedlabel = get_string('hideadvanced', 'form');
            } else {
                $showadvancedlabel = get_string('showadvanced', 'form');
            }
            ?>
<form id="overrideform" action="<?php echo $baseurl . '&amp;roleid=' . $roleid; ?>" method="post"><div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <div class="advancedbutton">
        <input type="submit" name="toggleadvanced" value="<?php echo $showadvancedlabel ?>" />
    </div>
    <div class="submit buttons">
        <input type="submit" name="savechanges" value="<?php print_string('savechanges') ?>" />
        <input type="submit" name="cancel" value="<?php print_string('cancel') ?>" />
    </div>
            <?php

            echo '<p class="overridenotice">' . get_string('highlightedcellsshowinherit', 'role') . ' </p>';
            $overridestable->display();

            if ($overridestable->has_locked_capabiltites()) {
                echo '<p class="overridenotice">' . get_string('safeoverridenotice', 'role') . "</p>\n";
            }

            ?>
    <div class="submit buttons">
        <input type="submit" name="savechanges" value="<?php print_string('savechanges') ?>" />
        <input type="submit" name="cancel" value="<?php print_string('cancel') ?>" />
    </div>
</div></form>

            <?php
            echo $OUTPUT->box_end();

        }

    /// Print a form to swap roles, and a link back to the all roles list.
        echo '<div class="backlink">';
        $select = new single_select(new moodle_url($baseurl), 'roleid', $nameswithcounts, $roleid, null);
        $select->label = get_string('overrideanotherrole', 'role');
        echo $OUTPUT->render($select);
        echo '<p><a href="' . $baseurl . '">' . get_string('backtoallroles', 'role') . '</a></p>';
        echo '</div>';

    } else if (empty($overridableroles)) {
    /// Print a message that there are no roles that can me assigned here.
        echo $OUTPUT->heading(get_string('notabletooverrideroleshere', 'role'), 3, 'mdl-align');

    } else {
    /// Show UI for choosing a role to assign.

        $table = new html_table();
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

        echo $OUTPUT->table($table);

        if (!$isfrontpage && ($url = get_context_url($context))) {
            echo '<div class="backlink"><a href="' . $url . '">' .
                get_string('backto', '', $contextname) . '</a></div>';
        } else if ($returnurl) {
            echo '<div class="backlink"><a href="' . $CFG->wwwroot . '/' . $returnurl . '">' .
                get_string('backtopageyouwereon') . '</a></div>';
        }
    }

    echo $OUTPUT->footer();
