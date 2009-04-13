<?php // $Id$

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
 * Processes actions from the admin_setting_managefilters object (defined in
 * adminlib.php).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package administration
 *//** */

    require_once(dirname(__FILE__) . '/../config.php');

    $action = optional_param('action', '', PARAM_ACTION);
    $filterpath = optional_param('filterpath', '', PARAM_PATH);

    require_login();
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    require_capability('moodle/site:config', $systemcontext);

    $returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=managefilters";

    if (!confirm_sesskey()) {
        redirect($returnurl);
    }

    $filters = filter_get_global_states();

    // In case any new filters have been installed, but not put in the table yet.
    $fitlernames = filter_get_all_installed();
    $newfilters = $fitlernames;
    foreach ($filters as $filter => $notused) {
        unset($newfilters[$filter]);
    }

    if (!isset($filters[$filterpath]) && !isset($newfilters[$filterpath])) {
        throw new moodle_exception('filternotinstalled', 'error', $returnurl, $filterpath);
    }

    switch ($action) {

    case 'setstate':
        if ($newstate = optional_param('newstate', '', PARAM_INTEGER)) {
            filter_set_global_state($filterpath, $newstate);
            if ($newstate == TEXTFILTER_DISABLED) {
                filter_set_applies_to_strings($filterpath, false);
            }
            unset($newfilters[$filterpath]);
        }
        break;

    case 'setapplyto':
        $applytostrings = optional_param('stringstoo', false, PARAM_BOOL);
        filter_set_applies_to_strings($filterpath, $applytostrings);
        break;

    case 'down':
        if (isset($filters[$filterpath])) {
            $oldpos = $filters[$filterpath]->sortorder;
            if ($oldpos <= count($filters)) {
                filter_set_global_state($filterpath, $filters[$filterpath]->active, $oldpos + 1);
            }
        }
        break;

    case 'up':
        if (isset($filters[$filterpath])) {
            $oldpos = $filters[$filterpath]->sortorder;
            if ($oldpos >= 1) {
                filter_set_global_state($filterpath, $filters[$filterpath]->active, $oldpos - 1);
            }
        }
        break;

    case 'delete':
        if (!empty($filternames[$filterpath])) {
            $filtername = $filternames[$filterpath];
        } else {
            $filtername = $filterpath;
        }

        if (substr($filterpath, 0, 4) == 'mod/') {
            $mod = basename($filterpath);
            $a = new stdClass;
            $a->filter = $filtername;
            $a->module = get_string('modulename', $mod);
            print_error('cannotdeletemodfilter', 'admin', admin_url('qtypes.php'), $a);
        }

        // If not yet confirmed, display a confirmation message.
        if (!optional_param('confirm', '', PARAM_BOOL)) {
            $title = get_string('deletefilterareyousure', 'admin', $filtername);
            print_header($title, $title);
            print_heading($title);
            notice_yesno(get_string('deletefilterareyousuremessage', 'admin', $filtername), $CFG->wwwroot . '/' . $CFG->admin .
                    '/filters.php?action=delete&amp;filterpath=' . $filterpath . '&amp;confirm=1&amp;sesskey=' . sesskey(),
                    "$CFG->wwwroot/$CFG->admin/settings.php", NULL, array('section' => 'managefilters'), 'post', 'get');
            print_footer('empty');
            exit;
        }

        // Do the deletion.
        $title = get_string('deletingfilter', 'admin', $filtername);
        print_header($title, $title);
        print_heading($title);

        // Delete all data for this plugin.
        filter_delete_all_data($filterpath);

        $a = new stdClass;
        $a->filter = $filtername;
        $a->directory = $filterpath;
        print_box(get_string('deletefilterfiles', 'admin', $a), 'generalbox', 'notice');
        print_continue($returnurl);
        print_footer('empty');
        exit;
    }

    // Add any missing filters to the DB table.
    foreach ($newfilters as $filter => $notused) {
        filter_set_global_state($filter, TEXTFILTER_DISABLED);
    }

    // Reset caches and return
    reset_text_filters_cache();
    redirect($returnurl);
