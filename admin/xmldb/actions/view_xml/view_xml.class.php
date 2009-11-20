<?php // $Id$

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

/// This class will display one XML file

class view_xml extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();
    /// Set own core attributes
        $this->can_subaction = ACTION_NONE;
        //$this->can_subaction = ACTION_HAVE_SUBACTIONS;

    /// Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

    /// Get needed strings
        $this->loadStrings(array(
        /// 'key' => 'module',
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

    /// Set own core attributes
        $this->does_generate = ACTION_GENERATE_XML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed

    /// Get the file parameter
        $file = required_param('file', PARAM_PATH);
        $file = $CFG->dirroot . stripslashes_safe($file);
    /// File must be under $CFG->wwwroot and
    ///  under one db directory (simple protection)
        if (substr($file, 0, strlen($CFG->dirroot)) == $CFG->dirroot &&
            substr(dirname($file), -2, 2) == 'db') {
        /// Everything is ok. Load the file to memory
            $this->output = file_get_contents($file);
        } else {
        /// Switch to HTML and error
            $this->does_generate = ACTION_GENERATE_HTML;
            $this->errormsg = 'File not viewable (' . $file .')';
            $result = false;
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
