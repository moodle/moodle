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
 * Defines the tab bar used on the manage/allow assign/allow overrides pages.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
    }

    $toprow = array();

    $toprow[] = new tabobject('manage', $CFG->wwwroot.'/'.$CFG->admin.'/roles/manage.php', get_string('manageroles', 'role'));

    $toprow[] = new tabobject('allowassign', $CFG->wwwroot.'/'.$CFG->admin.'/roles/allowassign.php', get_string('allowassign', 'role'));

    $toprow[] = new tabobject('allowoverride', $CFG->wwwroot.'/'.$CFG->admin.'/roles/allowoverride.php', get_string('allowoverride', 'role'));

    $tabs = array($toprow);

    print_tabs($tabs, $currenttab);

?>
