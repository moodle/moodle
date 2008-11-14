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
 * Lets the user edit role definitions.
 *
 * Responds to actions:
 *   add  - add a new role
 *   edit - edit the definition of a role
 *   view - view the definition of a role
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

    $action = required_param('action', PARAM_ALPHA);
    if ($action != 'add') {
        $roleid = required_param('roleid', PARAM_INTEGER);
    } else {
        $roleid = 0;
    }

/// Get the base URL for this and related pages into a convenient variable.
    $manageurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/manage.php';
    $baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/define.php';

/// Check access permissions.
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    require_login();
    require_capability('moodle/role:manage', $systemcontext);
    admin_externalpage_setup('defineroles');

/// Handle the cancel button.
    if (optional_param('cancel', false, PARAM_BOOL)) {
        redirect($manageurl);
    }

/// Handle the toggle advanced mode button.
    $showadvanced = get_user_preferences('definerole_showadvanced', false);
    if (optional_param('toggleadvanced', false, PARAM_BOOL)) {
        $showadvanced = !$showadvanced;
        set_user_preference('definerole_showadvanced', $showadvanced);
    }

/// Get some basic data we are going to need.
    $roles = get_all_roles();
    $rolescount = count($roles);

    $allcontextlevels = array(
        CONTEXT_SYSTEM => get_string('coresystem'),
        CONTEXT_USER => get_string('user'),
        CONTEXT_COURSECAT => get_string('category'),
        CONTEXT_COURSE => get_string('course'),
        CONTEXT_MODULE => get_string('activitymodule'),
        CONTEXT_BLOCK => get_string('block')
    );

/// Create the table object.
    if ($action == 'view') {
        $definitiontable = new view_role_definition_table($systemcontext, $roleid);
    } else if ($showadvanced) {
        $definitiontable = new define_role_table_advanced($systemcontext, $roleid);
    } else {
        $definitiontable = new define_role_table_basic($systemcontext, $roleid);
    }
    $definitiontable->read_submitted_permissions();

/// form processing, editing a role, adding a role, deleting a role etc.
    $errors = array();
    $newrole = false;

    $name        = optional_param('name', '', PARAM_MULTILANG);        // new role name
    $shortname   = optional_param('shortname', '', PARAM_RAW);         // new role shortname, special cleaning before storage
    $description = optional_param('description', '', PARAM_CLEAN);     // new role desc

    if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
        switch ($action) {
        case 'add':

            $shortname = textlib_get_instance()->specialtoascii($shortname);
            
            $shortname = moodle_strtolower(clean_param($shortname, PARAM_ALPHANUMEXT)); // only lowercase safe ASCII characters
            $legacytype = required_param('legacytype', PARAM_RAW);

            $legacyroles = get_legacy_roles();
            if (!array_key_exists($legacytype, $legacyroles)) {
                $legacytype = '';
            }

            if (empty($name)) {
                $errors['name'] = get_string('errorbadrolename', 'role');
            } else if ($DB->count_records('role', array('name'=>$name))) {
                $errors['name'] = get_string('errorexistsrolename', 'role');
            }

            if (empty($shortname)) {
                $errors['shortname'] = get_string('errorbadroleshortname', 'role');
            } else if ($DB->count_records('role', array('shortname'=>$shortname))) {
                $errors['shortname'] = get_string('errorexistsroleshortname', 'role');
            }

            if (empty($errors)) {
                $newroleid = create_role($name, $shortname, $description);

                // set proper legacy type
                if (!empty($legacytype)) {
                    assign_capability($legacyroles[$legacytype], CAP_ALLOW, $newroleid, $systemcontext->id);
                }

            } else {
                $newrole = new object();
                $newrole->name        = $name;
                $newrole->shortname   = $shortname;
                $newrole->description = $description;
                $newrole->legacytype  = $legacytype;
            }

            $newcontextlevels = array();
            foreach (array_keys($allcontextlevels) as $cl) {
                if (optional_param('contextlevel' . $cl, false, PARAM_BOOL)) {
                    $newcontextlevels[$cl] = $cl;
                }
            }
            if (empty($errors)) {
                set_role_contextlevels($newroleid, $newcontextlevels);
            }

            $allowed_values = array(CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT);
            $capabilities = fetch_context_capabilities($systemcontext); // capabilities applicable in this context

            foreach ($capabilities as $cap) {
                if (!isset($data->{$cap->name})) {
                    continue;
                }

                // legacy caps have their own selector
                if (is_legacy($data->{$cap->name})) {
                    continue;
                }

                $capname = $cap->name;
                $value = clean_param($data->{$cap->name}, PARAM_INT);
                if (!in_array($value, $allowed_values)) {
                    continue;
                }

                if (empty($errors)) {
                    assign_capability($capname, $value, $newroleid, $systemcontext->id);
                } else {
                    $newrole->$capname = $value;
                }
            }

            // added a role sitewide...
            mark_context_dirty($systemcontext->path);

            if (empty($errors)) {
                $rolename = $DB->get_field('role', 'name', array('id'=>$newroleid));
                add_to_log(SITEID, 'role', 'add', 'admin/roles/manage.php?action=add', $rolename, '', $USER->id);
                redirect('manage.php');
            }

            break;

        case 'edit':
            $shortname = moodle_strtolower(clean_param(clean_filename($shortname), PARAM_SAFEDIR)); // only lowercase safe ASCII characters
            $legacytype = required_param('legacytype', PARAM_RAW);

            $legacyroles = get_legacy_roles();
            if (!array_key_exists($legacytype, $legacyroles)) {
                $legacytype = '';
            }

            if (empty($name)) {
                $errors['name'] = get_string('errorbadrolename', 'role');
            } else if ($rs = $DB->get_records('role', array('name'=>$name))) {
                unset($rs[$roleid]);
                if (!empty($rs)) {
                    $errors['name'] = get_string('errorexistsrolename', 'role');
                }
            }

            if (empty($shortname)) {
                $errors['shortname'] = get_string('errorbadroleshortname', 'role');
            } else if ($rs = $DB->get_records('role', array('shortname'=>$shortname))) {
                unset($rs[$roleid]);
                if (!empty($rs)) {
                    $errors['shortname'] = get_string('errorexistsroleshortname', 'role');
                }
            }
            if (!empty($errors)) {
                $newrole = new object();
                $newrole->name        = $name;
                $newrole->shortname   = $shortname;
                $newrole->description = $description;
                $newrole->legacytype  = $legacytype;
            }

            $newcontextlevels = array();
            foreach (array_keys($allcontextlevels) as $cl) {
                if (optional_param('contextlevel' . $cl, false, PARAM_BOOL)) {
                    $newcontextlevels[$cl] = $cl;
                }
            }
            if (empty($errors)) {
                set_role_contextlevels($roleid, $newcontextlevels);
            }

            $allowed_values = array(CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT);
            $capabilities = fetch_context_capabilities($systemcontext); // capabilities applicable in this context

            foreach ($capabilities as $cap) {
                if (!isset($data->{$cap->name})) {
                    continue;
                }

                // legacy caps have their own selector
                if (is_legacy($data->{$cap->name}) === 0 ) {
                    continue;
                }

                $capname = $cap->name;
                $value = clean_param($data->{$cap->name}, PARAM_INT);
                if (!in_array($value, $allowed_values)) {
                    continue;
                }

                if (!empty($errors)) {
                    $newrole->$capname = $value;
                    continue;
                }

                // edit default caps
                $SQL = "SELECT *
                          FROM {role_capabilities}
                         WHERE roleid = ? AND capability = ?
                               AND contextid = ?";
                $params = array($roleid, $capname, $systemcontext->id); 

                $localoverride = $DB->get_record_sql($SQL, $params);

                if ($localoverride) { // update current overrides
                    if ($value == CAP_INHERIT) { // inherit = delete
                        unassign_capability($capname, $roleid, $systemcontext->id);

                    } else {
                        $localoverride->permission = $value;
                        $localoverride->timemodified = time();
                        $localoverride->modifierid = $USER->id;
                        $DB->update_record('role_capabilities', $localoverride);
                    }
                } else { // insert a record
                    if ($value != CAP_INHERIT) {
                        assign_capability($capname, $value, $roleid, $systemcontext->id);
                    }
                }

            }

            if (empty($errors)) {
                // update normal role settings
                $role->id = $roleid;
                $role->name = $name;
                $role->shortname = $shortname;
                $role->description = $description;

                if (!$DB->update_record('role', $role)) {
                    print_error('cannotupdaterole', 'error');
                }

                // set proper legacy type
                foreach($legacyroles as $ltype=>$lcap) {
                    if ($ltype == $legacytype) {
                        assign_capability($lcap, CAP_ALLOW, $roleid, $systemcontext->id);
                    } else {
                        unassign_capability($lcap, $roleid);
                    } 
                }                    

                // edited a role sitewide...
                mark_context_dirty($systemcontext->path);
                add_to_log(SITEID, 'role', 'edit', 'admin/roles/manage.php?action=edit&roleid='.$role->id, $role->name, '', $USER->id);

                redirect('manage.php');
            }

            // edited a role sitewide - with errors, but still...
            mark_context_dirty($systemcontext->path);
        }
    }

    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);

/// Print the page header and tabs.
    admin_externalpage_print_header();

    $currenttab = 'manage';
    include_once('managetabs.php');

    if ($action == 'add') {
        $title = get_string('addinganewrole', 'role');
    } else if ($action == 'view') {
        $title = get_string('viewingdefinitionofrolex', 'role', $rolenames[$roleid]->localname);
    } else if ($action == 'edit') {
        $title = get_string('editingrolex', 'role', $rolenames[$roleid]->localname);
    }
    print_heading_with_help($title, 'roles');

/// Display the role definition, either read-only, or for editing.
    if ($action == 'add') {
        $roleid = 0;
        if (empty($errors) or empty($newrole)) {
            $role = new object();
            $role->name        = '';
            $role->shortname   = '';
            $role->description = '';
            $role->legacytype  = '';
            $rolecontextlevels = array();
        } else {
            $role = $newrole;
            $rolecontextlevels = $newcontextlevels;
        }
    } else if ($action == 'edit' and !empty($errors) and !empty($newrole)) {
        $role = $newrole;
        $rolecontextlevels = $newcontextlevels;
    } else {
        if(!$role = $DB->get_record('role', array('id'=>$roleid))) {
            print_error('wrongroleid', 'error');
        }
        $role->legacytype = get_legacy_type($role->id);
        $rolecontextlevels = get_role_contextlevels($roleid);
    }


    if ($action == 'view') {
        echo '<div class="selector">';
        popup_form('manage.php?action=view&amp;roleid=', $roleoptions, 'switchrole', $roleid, '', '', '',
                   false, 'self', get_string('selectrole', 'role'));

        echo '<div class="buttons">';

        $legacytype = get_legacy_type($roleid); 
        $options = array();
        $options['roleid'] = $roleid;
        $options['action'] = 'edit';
        print_single_button('manage.php', $options, get_string('edit'));
        $options['action'] = 'reset';
        if (empty($legacytype)) {
            print_single_button('manage.php', $options, get_string('resetrolenolegacy', 'role'));
        } else {
            print_single_button('manage.php', $options, get_string('resetrole', 'role'));
        }
        $options['action'] = 'duplicate';
        print_single_button('manage.php', $options, get_string('duplicaterole', 'role'));
        print_single_button('manage.php', null, get_string('listallroles', 'role'));
        echo '</div>';
        echo '</div>';
    }

    print_box_start('generalbox boxwidthwide boxaligncenter');
    $definitiontable->display();
    print_box_end();

    admin_externalpage_print_footer();
?>
