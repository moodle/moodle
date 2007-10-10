<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
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

    $row[] = new tabobject('lettersview',
                           $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$COURSE->id,
                           get_string('letters', 'grades'));

    if (has_capability('moodle/grade:manageletters', $context)) {
        $row[] = new tabobject('lettersedit',
                               $CFG->wwwroot.'/grade/edit/letter/edit.php?id='.$context->id,
                               get_string('edit'));
    }

    $tabs[] = $row;

    echo '<div class="letterdisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';

?>
