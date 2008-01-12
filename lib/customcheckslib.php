<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

/// This library contains a collection of functions able to perform
/// some custom checks executed by environmental tests (automatically
/// executed on install & upgrade and under petition in the admin block).
///
/// Any function in this library must return:
/// - null: if the test isn't relevant and must not be showed (ignored)
/// - environment_results object with the status set to:
///     - true: if passed
///     - false: if failed

/**
 * This function will look for the risky PHP setting register_globals
 * in order to inform about. MDL-12914
 *
 * @param $result the environment_results object to be modified
 * @return mixed null if the test is irrelevant or environment_results object with
 *               status set to true (test passed) or false (test failed)
 */
function php_check_register_globals($result) {

/// Check for register_globals. If enabled, security warning
    if (ini_get_bool('register_globals')) {
        $result->status = false;
    } else {
        $result = null;
    }

    return $result;
}

?>
