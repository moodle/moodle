<?php  // $Id$

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
 * this page defines what roles can override (override roles in different context. For example,
 * we can say that Admin can override teacher roles in a course
 * To be able to override roles. If a user has moodle/role:override at context level
 * and be in the roles_allow_override table.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    require_login();
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/role:manage', $systemcontext);

/// Get all roles
    $roles = get_all_roles();
    role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);

/// Process form submission
    if (optional_param('submit', false, PARAM_BOOL) && data_submitted() && confirm_sesskey()) {
    /// Delete all records, then add back the ones that should be allowed.
        $DB->delete_records('role_allow_override');
        foreach ($roles as $fromroleid => $notused) {
            foreach ($roles as $targetroleid => $alsonotused) {
                if (optional_param('s_' . $fromroleid . '_' . $targetroleid, false, PARAM_BOOL)) {
                    allow_override($fromroleid, $targetroleid);
                }
            }
        }

    /// Updated allowoverrides sitewide, so force a premissions refresh, and redirect.
        mark_context_dirty($systemcontext->path);
        add_to_log(SITEID, 'role', 'edit allow override', 'admin/roles/allowoverride.php', '', '', $USER->id);
        redirect($CFG->wwwroot . '/' . $CFG->admin . '/roles/allowoverride.php');
    }

/// Load the current settings
    $allowed = array();
    foreach ($roles as $role) {
        // Make an array $role->id => false. This is probalby too clever for its own good.1
        $allowed[$role->id] = array_combine(array_keys($roles), array_fill(0, count($roles), false));
        }
    $raas = $DB->get_recordset('role_allow_override');
    foreach ($raas as $raa) {
        $allowed[$raa->roleid][$raa->allowoverride] = true;
    }

/// Display the editing form.
    admin_externalpage_setup('defineroles', '', array(), $CFG->wwwroot . '/' . $CFG->admin . '/roles/allowoverride.php');
    admin_externalpage_print_header();

    $currenttab='allowoverride';
    require_once('managetabs.php');

    $table->tablealign = 'center';
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = '90%';
    $table->align[] = 'left';
    $table->rotateheaders = true;
    $table->head = array('&#xa0;');

/// Add role name headers.
    foreach ($roles as $targetrole) {
        $table->head[] = $targetrole->localname;
        $table->align[] = 'left';
    }

/// Now the rest of the table.
    foreach ($roles as $fromrole) {
        $row = array($fromrole->localname);
        $a = new stdClass;
        $a->fromrole = $fromrole->localname;
        foreach ($roles as $targetrole) {
            if ($allowed[$fromrole->id][$targetrole->id]) {
                $checked = ' checked="checked"';
            } else {
                $checked = '';
            }
            $a->targetrole = $targetrole->localname;
            $name = 's_' . $fromrole->id . '_' . $targetrole->id;
            $tooltip = get_string('allowroletooverride', 'role', $a);
            $row[] = '<input type="checkbox" name="' . $name . '" id="' . $name . '" title="' . $tooltip . '" value="1"' . $checked . ' />' .
                    '<label for="' . $name . '" class="accesshide">' . $tooltip . '</label>';
        }
        $table->data[] = $row;
    }

    print_simple_box(get_string('configallowoverride2', 'admin'), 'center');

    echo '<form action="allowoverride.php" method="post">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    print_table($table);
    echo '<div class="buttons"><input type="submit" name="submit" value="'.get_string('savechanges').'"/>';
    echo '</div></form>';

    admin_externalpage_print_footer();
?>
