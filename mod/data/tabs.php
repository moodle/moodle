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

    $row = array();

    $row[] = new tabobject('list', new moodle_url('/mod/data/view.php', array('d' => $data->id)), get_string('list','data'));

    if (isset($record)) {
        $row[] = new tabobject('single', new moodle_url('/mod/data/view.php', array('d' => $data->id, 'rid' => $record->id)), get_string('single','data'));
    } else {
        $row[] = new tabobject('single', new moodle_url('/mod/data/view.php', array('d' => $data->id, 'mode' => 'single')), get_string('single','data'));
    }

    // Add an advanced search tab.
    $row[] = new tabobject('asearch', new moodle_url('/mod/data/view.php', array('d' => $data->id, 'mode' => 'asearch')), get_string('search', 'data'));

    if (isloggedin()) { // just a perf shortcut
        if (data_user_can_add_entry($data, $currentgroup, $groupmode, $context)) { // took out participation list here!
            $addstring = empty($editentry) ? get_string('add', 'data') : get_string('editentry', 'data');
            $row[] = new tabobject('add', new moodle_url('/mod/data/edit.php', array('d' => $data->id)), $addstring);
        }
        if (has_capability(DATA_CAP_EXPORT, $context)) {
            // The capability required to Export database records is centrally defined in 'lib.php'
            // and should be weaker than those required to edit Templates, Fields and Presets.
            $row[] = new tabobject('export', new moodle_url('/mod/data/export.php', array('d' => $data->id)),
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

            $templatestab = new tabobject('templates', new moodle_url('/mod/data/templates.php', array('d' => $data->id, 'mode' => $defaultemplate)),
                         get_string('templates','data'));
            $row[] = $templatestab;
            $row[] = new tabobject('fields', new moodle_url('/mod/data/field.php', array('d' => $data->id)),
                         get_string('fields','data'));
            $row[] = new tabobject('presets', new moodle_url('/mod/data/preset.php', array('d' => $data->id)),
                         get_string('presets', 'data'));
        }
    }

    if ($currenttab == 'templates' and isset($mode) && isset($templatestab)) {
        $templatestab->inactive = true;
        $templatelist = array ('listtemplate', 'singletemplate', 'asearchtemplate', 'addtemplate', 'rsstemplate', 'csstemplate', 'jstemplate');

        $currenttab ='';
        foreach ($templatelist as $template) {
            $templatestab->subtree[] = new tabobject($template, new moodle_url('/mod/data/templates.php', array('d' => $data->id, 'mode' => $template)), get_string($template, 'data'));
            if ($template == $mode) {
                $currenttab = $template;
            }
        }
        if ($currenttab == '') {
            $currenttab = $mode = 'singletemplate';
        }
    }

// Print out the tabs and continue!
    echo $OUTPUT->tabtree($row, $currenttab);


