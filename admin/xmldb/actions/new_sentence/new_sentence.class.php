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

/// This class will create a new default sentence to be edited.
/// If one previous sentence key is specified, it's used as
/// base to build the new setence, else a blank one is used

class new_sentence extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

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

    /// Get the correct dirs
        if (!empty($XMLDB->dbdirs)) {
            $dbdir =& $XMLDB->dbdirs[$dirpath];
        } else {
            return false;
        }
        if (!empty($XMLDB->editeddirs)) {
            $editeddir =& $XMLDB->editeddirs[$dirpath];
            $structure =& $editeddir->xml_file->getStructure();
        }
    /// ADD YOUR CODE HERE

        $statementparam = required_param('statement', PARAM_CLEAN);
        $basesentenceparam  = optional_param('basesentence', NULL, PARAM_CLEAN);

        $statement =& $structure->getStatement($statementparam);
        $sentences =& $statement->getSentences();

        $sentence = NULL;

    /// If some sentence has been specified, create the new one
    /// based on it
        if (!empty($basesentenceparam)) {
            $sentence = $sentences[$basesentenceparam];
        }
    /// Else, try to create the new one based in the last
        if (empty($sentence) && !empty($sentences)) {
            $sentence = end($sentences);
        }
    /// Else, create one sentence by hand
        if (empty($sentence)) {
            $sentence = "(list, of, fields) VALUES ('list', 'of', 'values')";
        }

    /// Add the sentence to the statement
        $statement->addSentence($sentence);

    /// We have one new sentence, so the statement and the structure has changed
        $statement->setChanged(true);
        $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
        $structure->setChanged(true);

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
