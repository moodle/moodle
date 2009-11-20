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

/// This class will provide the interface for all the edit sentence actions

class edit_sentence extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

    /// Get needed strings
        $this->loadStrings(array(
            'change' => 'xmldb',
            'vieworiginal' => 'xmldb',
            'viewedited' => 'xmldb',
            'back' => 'xmldb'
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
        $this->does_generate = ACTION_GENERATE_HTML;

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

    /// Fetch request data
        $statementparam = required_param('statement', PARAM_CLEAN);
        $sentenceparam  = optional_param('sentence', NULL, PARAM_CLEAN);

        if (!$statement =& $structure->getStatement($statementparam)) {
            $this->errormsg = 'Wrong statement specified: ' . $statementparam;
            return false;
        }
        $sentences =& $statement->getSentences();

    /// If no sentence has been specified, edit the last one
        if ($sentenceparam === NULL) {
            end($sentences);
            $sentenceparam = key($sentences);
        }

        if (!$sentence =& $sentences[$sentenceparam]) {
            $this->errormsg = 'Wrong Sentence: ' . $sentenceparam;
            return false;
        }

        $dbdir =& $XMLDB->dbdirs[$dirpath];
        $origstructure =& $dbdir->xml_file->getStructure();

    /// Based in the type of statement, print different forms
        if ($statement->getType() != XMLDB_STATEMENT_INSERT) {
        /// Only INSERT is allowed!!
            $this->errormsg = 'Wrong Statement Type. Only INSERT allowed';
            return false;
        } else {
        /// Prepare INSERT sentence
            $fields = $statement->getFieldsFromInsertSentence($sentence);
            $values = $statement->getValuesFromInsertSentence($sentence);

        /// Add the main form
            $o = '<form id="form" action="index.php" method="post">';
            $o.= '<div>';
            $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
            $o.= '    <input type="hidden" name ="statement" value="' . $statementparam .'" />';
            $o.= '    <input type="hidden" name ="sentence" value="' . $sentenceparam .'" />';
            $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
            $o.= '    <input type="hidden" name ="action" value="edit_sentence_save" />';
            $o.= '    <input type="hidden" name ="postaction" value="edit_statement" />';
            $o.= '    <table id="formelements" class="boxaligncenter">';
        /// The fields box
            $o.= '    <tr><td>INSERT INTO ' . s($statement->getTable()) . '</td></tr>';
            $o.= '    <tr><td><textarea name="fields" rows="2" cols="70" id="fields">' . s(implode(', ', $fields)) . '</textarea></td></tr>';
        /// The values box
            $o.= '    <tr><td>VALUES</td></tr>';
            $o.= '    <tr><td><textarea name="values" rows="2" cols="70" id="values">' . s(implode(', ', $values)) . '</textarea></td></tr>';
        /// The submit button
            $o.= '      <tr valign="top"><td><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
            $o.= '    </table>';
            $o.= '</div></form>';
        /// Calculate the buttons
            $b = ' <p class="centerpara buttons">';
        /// The back to edit statement button
            $b .= '&nbsp;<a href="index.php?action=edit_statement&amp;statement=' . urlencode($statementparam) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
            $b .= '</p>';
            $o .= $b;

            $this->output = $o;
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
