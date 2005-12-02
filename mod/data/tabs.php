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
    
    $row[] = new tabobject('browse', $CFG->wwwroot.'/mod/data/view.php?d='.$data->id, get_string('browse','data'));
    if (isteacher($course->id) or ($data->participants == PARTICIPANTS_S) or ($data->participants == PARTICIPANTS_TS)){
        $row[] = new tabobject('add', $CFG->wwwroot.'/mod/data/add.php?d='.$data->id, get_string('add','data'));
    }
    if (isteacher($course->id)) {
        $row[] = new tabobject('templates', $CFG->wwwroot.'/mod/data/templates.php?d='.$data->id.'&mode=singletemplate', get_string('templates','data'));
        $row[] = new tabobject('fields', $CFG->wwwroot.'/mod/data/fields.php?d='.$data->id, get_string('fields','data'));
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
            $row[] = new tabobject($template, "templates.php?d=$d&amp;mode=$template",
                                    get_string("$template", "data"));
            if ($template == $mode) {
                $currenttab = $template;
            }
        }
        $tabs[] = $row;
    }
    /*
    if ($currenttab == 'browse' and isset($mode)) {
        $inactive[] = 'browse';
        $viewlist = array ('singletemplate', 'listtemplate');   // Standard reports we want to show first

        $row  = array();
        $currenttab ='';
        foreach ($viewlist as $view) {
            $row[] = new tabobject($view, "view.php?d=$d&amp;mode=$view",
                                    get_string("$view", "data"));
            if ($view == $mode) {
                $currenttab = $view;
            }
        }
        $tabs[] = $row;
    }*/
    
    

/// Print out the tabs and continue!

    print_tabs($tabs, $currenttab, $inactive);

?>
