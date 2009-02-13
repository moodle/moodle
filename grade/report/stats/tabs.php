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

    /**
     * Sets up the tabs for the report/stats plug-in and displays them.
     * @package gradebook
     */

    $row = $tabs = array();
    $tabcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    $row[] = new tabobject('statsreport',
                           $CFG->wwwroot.'/grade/report/stats/index.php?id='.$courseid,
                           get_string('modulename', 'gradereport_stats'));
    
    $row[] = new tabobject('preferences',
                           $CFG->wwwroot.'/grade/report/stats/preferences.php?id='.$courseid,
                           get_string('myreportpreferences', 'grades'));
    
    /// A bit of a hack to make the printable tab open a new window.
    $row[] = new tabobject('printable',
                           '#" onClick="javascript:window.open(\'' . $CFG->wwwroot. '/grade/report/stats/print.php?id=' . $courseid . '\')',
                           get_string('printable', 'gradereport_stats'));

    $tabs[] = $row;
    echo '<div class="gradedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>