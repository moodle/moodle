<?php // $Id$

///////////////////////////////////////////////////////////////////////////
// Defines core event handlers                                           //
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


$handlers = array (

/*
 * Grades added by activities
 * 
 * required parameters (object or array):
 *  itemid         - if from grade_items table, grade item must already exist
 *  userid         - each grade must be associated to existing user
 *
 * optional params:
 *  gradevalue     - raw grade value
 *  feedback       - graders feedback
 *  feedbackformat - text format of the feedback
 */
   'grade_updated' => array (
        'handlerfile'      => '/lib/gradelib.php',
        'handlerfunction'  => 'grade_handler', 
        'schedule'         => 'instant'
    ),

/*
 * Grades created/modified outside of activities (import, gradebook overrides, etc.)
 * 
 * required parameters (object or array):
 *  itemid         - id from grade_items table, grade item must already exist
 *  userid         - each grade must be associated with existing user
 *
 * optional params:
 *  gradevalue     - raw grade value
 *  feedback       - graders feedback
 *  feedbackformat - text format of the feedback
 *
 * optional params (improves performance):
 *  itemtype       - mod, block
 *  itemmodule     - assignment, etc.
 */
   'grade_updated_external' => array (
        'handlerfile'      => '/lib/gradelib.php',
        'handlerfunction'  => 'grade_handler', 
        'schedule'         => 'instant'
    )
);


?>
