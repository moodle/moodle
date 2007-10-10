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

/// This class will

class create_xml_file extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();
    /// Set own core attributes
        $this->can_subaction = ACTION_NONE;
        //$this->can_subaction = ACTION_HAVE_SUBACTIONS;

    /// Set own custom attributes

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
        $this->does_generate = ACTION_NONE;
        //$this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed

    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);
        $file = $dirpath . '/install.xml';

    /// Some variables
        $xmlpath = dirname(str_replace($CFG->dirroot . '/', '', $file));
        $xmlversion = userdate(time(), '%Y%m%d', 99, false);
        $xmlcomment = 'XMLDB file for Moodle ' . dirname($xmlpath);

        $xmltable = strtolower(basename(dirname($xmlpath)));

    /// Initial contents
        $c = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $c.= '  <XMLDB PATH="' . $xmlpath . '" VERSION="' . $xmlversion .'" COMMENT="' . $xmlcomment .'">' . "\n";
        $c.= '    <TABLES>' . "\n";
        $c.= '      <TABLE NAME="' . $xmltable . '" COMMENT="Default comment for ' . $xmltable .', please edit me">' . "\n";
        $c.= '        <FIELDS>' . "\n";
        $c.= '          <FIELD NAME="id" TYPE="int" LENGTH="10" UNSIGNED="true" NOTNULL="true" SEQUENCE="true" />' . "\n";
        $c.= '        </FIELDS>' . "\n";
        $c.= '        <KEYS>' . "\n";
        $c.= '          <KEY NAME="primary" TYPE="primary" FIELDS="id" />' . "\n";
        $c.= '        </KEYS>' . "\n";
        $c.= '      </TABLE>' . "\n";
        $c.= '    </TABLES>' . "\n";
        $c.= '  </XMLDB>';

        if (!file_put_contents($file, $c)) {
            $errormsg = 'Error creando fichero ' . $file;
            $result = false;
        }

    /// Launch postaction if exists
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
