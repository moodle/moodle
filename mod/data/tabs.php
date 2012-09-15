<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
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

// This file to be included so we can assume config.php has already been included.
// We also assume that $user, $course, $currenttab have been set


    if (empty($currenttab) or empty($data) or empty($course)) {
        print_error('cannotcallscript');
    }

    $context = context_module::instance($cm->id);

    $inactive = NULL;
    $activetwo = NULL;
    $tabs = array();
    $row = array();

    $row[] = new tabobject('list', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id, get_string('list','data'));

    if (isset($record)) {
        $row[] = new tabobject('single', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;rid='.$record->id, get_string('single','data'));
    } else {
        $row[] = new tabobject('single', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;mode=single', get_string('single','data'));
    }

    // Add an advanced search tab.
    $row[] = new tabobject('asearch', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&amp;mode=asearch', get_string('search', 'data'));

    if (isloggedin()) { // just a perf shortcut
        if (data_user_can_add_entry($data, $currentgroup, $groupmode, $context)) { // took out participation list here!
            $addstring = empty($editentry) ? get_string('add', 'data') : get_string('editentry', 'data');
            $row[] = new tabobject('add', $CFG->wwwroot.'/mod/data/edit.php?d='.$data->id, $addstring);
        }
        if (has_capability(DATA_CAP_EXPORT, $context)) {
            // The capability required to Export database records is centrally defined in 'lib.php'
            // and should be weaker than those required to edit Templates, Fields and Presets.
            $row[] = new tabobject('export', $CFG->wwwroot.'/mod/data/export.php?d='.$data->id,
                         get_string('export', 'data'));
        }
        if (has_capability('mod/data:managetemplates', $context)) {
            if ($currenttab == 'list') {
                $defaultemplate = 'listtemplate';
            } else if ($currenttab == 'add') {
                $defaultemplate = 'addtemplate';
            } else if ($currenttab == 'asearch') {
                $defaultemplate = 'asearchtemplate';
            } else {
                $defaultemplate = 'singletemplate';
            }

            $row[] = new tabobject('templates', $CFG->wwwroot.'/mod/data/templates.php?d='.$data->id.'&amp;mode='.$defaultemplate,
                         get_string('templates','data'));
            $row[] = new tabobject('fields', $CFG->wwwroot.'/mod/data/field.php?d='.$data->id,
                         get_string('fields','data'));
            $row[] = new tabobject('presets', $CFG->wwwroot.'/mod/data/preset.php?d='.$data->id,
                         get_string('presets', 'data'));
        }
    }

    $tabs[] = $row;

    if ($currenttab == 'templates' and isset($mode)) {

        $inactive = array();
        $inactive[] = 'templates';
        $templatelist = array ('listtemplate', 'singletemplate', 'asearchtemplate', 'addtemplate', 'rsstemplate', 'csstemplate', 'jstemplate');

        $row  = array();
        $currenttab ='';
        foreach ($templatelist as $template) {
            $row[] = new tabobject($template, "templates.php?d=$data->id&amp;mode=$template", get_string($template, 'data'));
            if ($template == $mode) {
                $currenttab = $template;
            }
        }
        if ($currenttab == '') {
            $currenttab = $mode = 'singletemplate';
        }
        $tabs[] = $row;
        $activetwo = array('templates');
    }

// Print out the tabs and continue!
    print_tabs($tabs, $currenttab, $inactive, $activetwo);


