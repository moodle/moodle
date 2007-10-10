<?php  /// $Id $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
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

/// Load XMLDB required Javascript libraries, adding them
/// before the standard one ($standard_javascript)

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

/// We use this globals to be able to generate the proper JavaScripts
    global $standard_javascript;

/// Load XMLDB javascript needed to handle some forms
    $action = optional_param('action', '', PARAM_ALPHAEXT);
    $postaction = optional_param('postaction', '', PARAM_ALPHAEXT);
/// If the js exists, load it
    if ($action) {
        $file    = $CFG->dirroot . '/'.$CFG->admin.'/xmldb/actions/' . $action . '/' . $action . '.js';
        $wwwfile = $CFG->wwwroot . '/'.$CFG->admin.'/xmldb/actions/' . $action . '/' . $action . '.js';
        if (file_exists($file) && is_readable($file)) {
            echo '<script type="text/javascript" src="' . $wwwfile . '"></script>' . "\n";
        } else {
        /// Try to load the postaction javascript if exists
            if ($postaction) {
                $file    = $CFG->dirroot . '/'.$CFG->admin.'/xmldb/actions/' . $postaction . '/' . $postaction . '.js';
                $wwwfile = $CFG->wwwroot . '/'.$CFG->admin.'/xmldb/actions/' . $postaction . '/' . $postaction . '.js';
                if (file_exists($file) && is_readable($file)) {
                    echo '<script type="text/javascript" src="' . $wwwfile . '"></script>' . "\n";
                }
            }
        }
    }

/// Load standard JavaScript
    include($standard_javascript);
?>
