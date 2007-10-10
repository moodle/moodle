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

/// This class will save one edited xml file

class save_xml_file extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'filenotwriteable' => 'xmldb'
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
        $this->does_generate = ACTION_NONE;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed

    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);

    /// Get the edited dir
        if (!empty($XMLDB->editeddirs)) {
            if (isset($XMLDB->editeddirs[$dirpath])) {
                $editeddir =& $XMLDB->editeddirs[$dirpath];
            }
        }
    /// Copy the edited dir over the original one
        if (!empty($XMLDB->dbdirs)) {
            if (isset($XMLDB->dbdirs[$dirpath])) {
                $XMLDB->dbdirs[$dirpath] = unserialize(serialize($editeddir));
                $dbdir =& $XMLDB->dbdirs[$dirpath];
            }
        }

    /// Chech for perms
        if (!is_writeable($dirpath . '/install.xml')) {
            $this->errormsg = $this->str['filenotwriteable'] . '(' . $dirpath . '/install.xml)';
            return false;
        }

    /// Save the original dir
        $result = $dbdir->xml_file->saveXMLFile();

        if ($result) {
        /// Delete the edited dir
            unset ($XMLDB->editeddirs[$dirpath]);
        /// Unload de originaldir
            unset($XMLDB->dbdirs[$dirpath]->xml_file);
            unset($XMLDB->dbdirs[$dirpath]->xml_loaded);
            unset($XMLDB->dbdirs[$dirpath]->xml_changed);
            unset($XMLDB->dbdirs[$dirpath]->xml_exists);
            unset($XMLDB->dbdirs[$dirpath]->xml_writeable);
        } else {
            $this->errormsg = 'Error saving XML file (' . $dirpath . ')';
            return false;
        }

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
