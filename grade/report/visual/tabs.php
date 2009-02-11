<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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


$row = $tabs = array();
    $tabcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
   

    if(!isset($visid)){
        $visid = optional_param('visid');
    }


    $row[] = new tabobject('visualreport',
                           $CFG->wwwroot.'/grade/report/visual/index.php?id='.$courseid.'&visid='.$visid,
                           get_string('modulename', 'gradereport_visual'));
    
    if (has_capability('moodle/grade:manage',$tabcontext ) ||
        has_capability('moodle/grade:edit', $tabcontext) ||
        has_capability('gradereport/visual:view', $tabcontext)) {
        $row[] = new tabobject('preferences',
                               $CFG->wwwroot.'/grade/report/visual/preferences.php?id='.$courseid.'&visid='.$visid,
                               get_string('myreportpreferences', 'grades'));
    }

    /// A bit of a hack to make the printable tab open a new window.
    $row[] = new tabobject('printable',
                           '#" onClick="javascript:window.open(\'' . $CFG->wwwroot. '/grade/report/visual/print.php?id=' . $courseid . '&visid=' . $visid . '\')',
                           get_string('printable', 'gradereport_visual'));

    $tabs[] = $row;
    echo '<div class="gradedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>
