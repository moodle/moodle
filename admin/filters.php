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
 * Processes actions from the admin_setting_managefilters object (defined in
 * adminlib.php).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package administration
 *//** */

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->libdir . '/adminlib.php');

    $action = optional_param('action', '', PARAM_ALPHANUMEXT);
    $filterpath = optional_param('filterpath', '', PARAM_PATH);

    require_login();
    $systemcontext = context_system::instance();
    require_capability('moodle/site:config', $systemcontext);

    $returnurl = "$CFG->wwwroot/$CFG->admin/filters.php";
    admin_externalpage_setup('managefilters');

    $filters = filter_get_global_states();

    // In case any new filters have been installed, but not put in the table yet.
    $fitlernames = filter_get_all_installed();
    $newfilters = $fitlernames;
    foreach ($filters as $filter => $notused) {
        unset($newfilters[$filter]);
    }

/// Process actions ============================================================

    if ($action) {
        if (!isset($filters[$filterpath]) && !isset($newfilters[$filterpath])) {
            throw new moodle_exception('filternotinstalled', 'error', $returnurl, $filterpath);
        }

        if (!confirm_sesskey()) {
            redirect($returnurl);
        }
    }

    switch ($action) {

    case 'setstate':
        if ($newstate = optional_param('newstate', '', PARAM_INT)) {
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
            print_error('cannotdeletemodfilter', 'admin', $returnurl, $a);
        }

        // If not yet confirmed, display a confirmation message.
        if (!optional_param('confirm', '', PARAM_BOOL)) {
            $title = get_string('deletefilterareyousure', 'admin', $filtername);
            echo $OUTPUT->header();
            echo $OUTPUT->heading($title);

            $linkcontinue = new moodle_url($returnurl, array('action' => 'delete', 'filterpath' => $filterpath, 'confirm' => 1));
            $formcancel = new single_button(new moodle_url($returnurl), get_string('no'), 'get');
            echo $OUTPUT->confirm(get_string('deletefilterareyousuremessage', 'admin', $filtername), $linkcontinue, $formcancel);
            echo $OUTPUT->footer();
            exit;
        }

        // Do the deletion.
        $title = get_string('deletingfilter', 'admin', $filtername);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($title);

        // Delete all data for this plugin.
        filter_delete_all_for_filter($filterpath);

        $a = new stdClass;
        $a->filter = $filtername;
        $a->directory = $filterpath;
        echo $OUTPUT->box(get_string('deletefilterfiles', 'admin', $a), 'generalbox', 'notice');
        echo $OUTPUT->continue_button($returnurl);
        echo $OUTPUT->footer();
        exit;
    }

    // Add any missing filters to the DB table.
    foreach ($newfilters as $filter => $notused) {
        filter_set_global_state($filter, TEXTFILTER_DISABLED);
    }

    // Reset caches and return
    if ($action) {
        reset_text_filters_cache();
        redirect($returnurl);
    }

/// End of process actions =====================================================

/// Print the page heading.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('filtersettings', 'admin'));

    $activechoices = array(
        TEXTFILTER_DISABLED => get_string('disabled', 'filters'),
        TEXTFILTER_OFF => get_string('offbutavailable', 'filters'),
        TEXTFILTER_ON => get_string('on', 'filters'),
    );
    $applytochoices = array(
        0 => get_string('content', 'filters'),
        1 => get_string('contentandheadings', 'filters'),
    );

    $filters = filter_get_global_states();

    // In case any new filters have been installed, but not put in the table yet.
    $filternames = filter_get_all_installed();
    $newfilters = $filternames;
    foreach ($filters as $filter => $notused) {
        unset($newfilters[$filter]);
    }
    $stringfilters = filter_get_string_filters();

    $table = new html_table();
    $table->head  = array(get_string('filter'), get_string('isactive', 'filters'),
            get_string('order'), get_string('applyto', 'filters'), get_string('settings'), get_string('delete'));
    $table->align = array('left', 'left', 'center', 'left', 'left');
    $table->width = '100%';
    $table->data  = array();

    $lastactive = null;
    foreach ($filters as $filter => $filterinfo) {
        if ($filterinfo->active != TEXTFILTER_DISABLED) {
            $lastactive = $filter;
        }
    }

    // iterate through filters adding to display table
    $firstrow = true;
    foreach ($filters as $filter => $filterinfo) {
        $applytostrings = isset($stringfilters[$filter]) && $filterinfo->active != TEXTFILTER_DISABLED;
        $row = get_table_row($filterinfo, $firstrow, $filter == $lastactive, $applytostrings);
        $table->data[] = $row;
        if ($filterinfo->active == TEXTFILTER_DISABLED) {
            $table->rowclasses[] = 'dimmed_text';
        } else {
            $table->rowclasses[] = '';
        }
        $firstrow = false;
    }
    foreach ($newfilters as $filter => $filtername) {
        $filterinfo = new stdClass;
        $filterinfo->filter = $filter;
        $filterinfo->active = TEXTFILTER_DISABLED;
        $row = get_table_row($filterinfo, false, false, false);
        $table->data[] = $row;
        $table->rowclasses[] = 'dimmed_text';
    }

    echo html_writer::table($table);
    echo '<p class="filtersettingnote">' . get_string('filterallwarning', 'filters') . '</p>';
    echo $OUTPUT->footer();

/// Display helper functions ===================================================

function filters_action_url($filterpath, $action) {
    return new moodle_url('/admin/filters.php', array('sesskey'=>sesskey(), 'filterpath'=>$filterpath, 'action'=>$action));
}

function get_table_row($filterinfo, $isfirstrow, $islastactive, $applytostrings) {
    global $CFG, $OUTPUT, $activechoices, $applytochoices, $filternames; //TODO: this is sloppy coding style!!
    $row = array();
    $filter = $filterinfo->filter;

    // Filter name
    if (!empty($filternames[$filter])) {
        $row[] = $filternames[$filter];
    } else {
        $row[] = '<span class="error">' . get_string('filemissing', '', $filter) . '</span>';
    }

    // Disable/off/on
    $select = new single_select(filters_action_url($filter, 'setstate'), 'newstate', $activechoices, $filterinfo->active, null, 'active' . basename($filter));
    $select->set_label(get_string('isactive', 'filters'), array('class' => 'accesshide'));
    $row[] = $OUTPUT->render($select);

    // Re-order
    $updown = '';
    $spacer = '<img src="' . $OUTPUT->pix_url('spacer') . '" class="iconsmall" alt="" />';
    if ($filterinfo->active != TEXTFILTER_DISABLED) {
        if (!$isfirstrow) {
            $updown .= $OUTPUT->action_icon(filters_action_url($filter, 'up'), new pix_icon('t/up', get_string('up'), '', array('class' => 'iconsmall')));
        } else {
            $updown .= $spacer;
        }
        if (!$islastactive) {
            $updown .= $OUTPUT->action_icon(filters_action_url($filter, 'down'), new pix_icon('t/down', get_string('down'), '', array('class' => 'iconsmall')));
        } else {
            $updown .= $spacer;
        }
    }
    $row[] = $updown;

    // Apply to strings.
    $select = new single_select(filters_action_url($filter, 'setapplyto'), 'stringstoo', $applytochoices, $applytostrings, null, 'applyto' . basename($filter));
    $select->set_label(get_string('applyto', 'filters'), array('class' => 'accesshide'));
    $select->disabled = $filterinfo->active == TEXTFILTER_DISABLED;
    $row[] = $OUTPUT->render($select);

    // Settings link, if required
    if (filter_has_global_settings($filter)) {
        $row[] = '<a href="' . $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=filtersetting' .
                str_replace('/', '',$filter) . '">' . get_string('settings') . '</a>';
    } else {
        $row[] = '';
    }

    // Delete
    if (substr($filter, 0, 4) != 'mod/') {
        $row[] = '<a href="' . filters_action_url($filter, 'delete') . '">' . get_string('delete') . '</a>';
    } else {
        $row[] = '';
    }

    return $row;
}
