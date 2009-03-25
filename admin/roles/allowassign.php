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
 * this page defines what roles can access (grant user that role and override that roles'
 * capabilities in different context. For example, we can say that Teachers can only grant
 * student role or modify student role's capabilities. Note that you need both the right
 * capability moodle/role:assign or moodle/role:manage and this database table roles_deny_grant
 * to be able to grant roles. If a user has moodle/role:manage at site level assignment
 * then he can modify the roles_allow_assign table via this interface.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir . '/adminlib.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

    admin_externalpage_setup('defineroles', '', array(), $CFG->wwwroot . '/' . $CFG->admin . '/roles/allowassign.php');
    require_login();

    $controller = new role_allow_assign_page();
    require_capability('moodle/role:manage', $controller->get_context());

    if (optional_param('submit', false, PARAM_BOOL) && data_submitted() && confirm_sesskey()) {
        $controller->process_submission();
        mark_context_dirty($this->systemcontext->path);
        add_to_log(SITEID, 'role', 'edit allow assign', 'admin/roles/allowassign.php', '', '', $USER->id);
        redirect($CFG->wwwroot . '/' . $CFG->admin . '/roles/allowassign.php');
    }

    $controller->load_current_settings();

/// Display the editing form.
    admin_externalpage_print_header();

    $currenttab='allowassign';
    require_once('managetabs.php');

    $table = $controller->get_table();

    print_simple_box(get_string('configallowassign', 'admin'), 'center');

    echo '<form action="allowassign.php" method="post">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    print_table($table);
    echo '<div class="buttons"><input type="submit" name="submit" value="'.get_string('savechanges').'"/>';
    echo '</div></form>';

    admin_externalpage_print_footer();
?>
