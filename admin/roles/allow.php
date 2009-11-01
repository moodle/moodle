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
 * this page defines what roles can do things with other roles. For example
 * which roles can assign which other roles, or which roles can switch to
 * which other roles.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir . '/adminlib.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

    $mode = required_param('mode', PARAM_ACTION);
    $classformode = array(
        'assign' => 'role_allow_assign_page',
        'override' => 'role_allow_override_page',
        'switch' => 'role_allow_switch_page'
    );
    if (!isset($classformode[$mode])) {
        print_error('invalidmode', '', '', $mode);
    }

    $baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/allow.php?mode=' . $mode;
    admin_externalpage_setup('defineroles', '', array(), $baseurl);
    require_login();

    $syscontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/role:manage', $syscontext);

    $controller = new $classformode[$mode]();

    if (optional_param('submit', false, PARAM_BOOL) && data_submitted() && confirm_sesskey()) {
        $controller->process_submission();
        mark_context_dirty($syscontext->path);
        add_to_log(SITEID, 'role', 'edit allow ' . $mode, str_replace($CFG->wwwroot . '/', '', $baseurl), '', '', $USER->id);
        redirect($baseurl);
    }

    $controller->load_current_settings();

/// Display the editing form.
    admin_externalpage_print_header();

    $currenttab = $mode;
    require_once('managetabs.php');

    $table = $controller->get_table();

    echo $OUTPUT->box($controller->get_intro_text());

    echo '<form action="' . $baseurl . '" method="post">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    echo $OUTPUT->table($table);
    echo '<div class="buttons"><input type="submit" name="submit" value="'.get_string('savechanges').'"/>';
    echo '</div></form>';

    echo $OUTPUT->footer();
