<?php  // $Id$
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
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


    if (empty($currenttab) or empty($data) or empty($course)) {
        error('You cannot call this script in that way');
    }

    $inactive = NULL;
    $row = array();
    
    $row[] = new tabobject('list', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id, get_string('browse','data'), '', true);
    if (isset($record)) {
        $row[] = new tabobject('single', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&rid='.$record->id, get_string('detail','data'), '', true);
    } else {
        $row[] = new tabobject('single', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&mode=single', get_string('detail','data'), '', true);
    }
    if (isteacher($course->id) or ($data->participants == DATA_STUDENTS_ONLY) or ($data->participants == DATA_TEACHERS_AND_STUDENTS)){
        $addstring = ($rid) ? get_string('editentry', 'data') : get_string('add', 'data');
        $row[] = new tabobject('add', $CFG->wwwroot.'/mod/data/add.php?d='.$data->id, $addstring, '', true);
    }
    if (isteacher($course->id)) {
        if ($currenttab == 'list') {
            if (get_user_preferences('data_perpage') == 1) {
                $defaultemplate = 'singletemplate';
            } else {
                $defaultemplate = 'listtemplate';
            }
        } else if ($currenttab == 'add') {
            $defaultemplate = 'addtemplate';
        } else {
            $defaultemplate = 'singletemplate';
        }

        $row[] = new tabobject('templates', $CFG->wwwroot.'/mod/data/templates.php?d='.$data->id.'&amp;mode='.$defaultemplate, get_string('templates','data'));
        $row[] = new tabobject('fields', $CFG->wwwroot.'/mod/data/field.php?d='.$data->id, get_string('fields','data'), '', true);
    }

    $tabs[] = $row;
    /*****************************
    * stolen code from quiz report
    *****************************/
    if ($currenttab == 'templates' and isset($mode)) {
        $inactive[] = 'templates';
        $templatelist = array ('singletemplate', 'listtemplate', 'addtemplate', 'rsstemplate');   // Standard reports we want to show first

        $row  = array();
        $currenttab ='';
        foreach ($templatelist as $template) {
            $row[] = new tabobject($template, "templates.php?d=$data->id&amp;mode=$template",
                                    get_string("$template", "data"));
            if ($template == $mode) {
                $currenttab = $template;
            }
        }
        $tabs[] = $row;
    }
    

/// Print out the tabs and continue!

    print_tabs($tabs, $currenttab, $inactive);
    
?>
