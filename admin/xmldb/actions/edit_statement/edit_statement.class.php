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

/// This class will provide the interface for all the edit statement actions

class edit_statement extends XMLDBAction {

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
            'newsentence' => 'xmldb',
            'sentences' => 'xmldb',
            'edit' => 'xmldb',
            'delete' => 'xmldb',
            'duplicate' => 'xmldb',
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
        $statementparam = optional_param('statement', NULL, PARAM_CLEAN);
    /// If no statement, then we are coming for a new one. Look for
    /// type and table and build the correct statementparam
        if (!$statementparam) {
            $typeparam = optional_param('type', NULL, PARAM_CLEAN);
            $tableparam = optional_param('table', NULL, PARAM_CLEAN);
            $typename = XMLDBStatement::getXMLDBStatementName($typeparam);
            $statementparam = trim(strtolower($typename . ' ' . $tableparam));
        }
        if (!$statement =& $structure->getStatement($statementparam)) {
        /// Arriving here from a name change, looking for the new statement name
            $statementname = required_param('name', PARAM_CLEAN);
            $statement =& $structure->getStatement($statementparam);
        }

        $dbdir =& $XMLDB->dbdirs[$dirpath];
        $origstructure =& $dbdir->xml_file->getStructure();

    /// Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '<div>';        
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="statement" value="' . $statementparam .'" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_statement_save" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_statement" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
        $o.= '      <tr valign="top"><td>Name:</td><td><input type="hidden" name ="name" value="' . s($statement->getName()) . '" />' . s($statement->getName()) .'</td></tr>';
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td><textarea name="comment" rows="3" cols="80" id="comment">' . s($statement->getComment()) . '</textarea></td></tr>';
        $o.= '      <tr valign="top"><td>&nbsp;</td><td><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
    /// Calculate the buttons
        $b = ' <p class="centerpara buttons">';
    /// The view original XML button
        if ($origstructure->getStatement($statementparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_statement_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;statement=' . $statementparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
    /// The view edited XML button
        if ($statement->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_statement_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;statement=' . $statementparam . '">[' . $this->str['viewedited'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
        }
    /// The new sentence button
        $b .= '&nbsp;<a href="index.php?action=new_sentence&amp;postaction=edit_sentence&amp;sesskey=' . sesskey() . '&amp;statement=' . $statementparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newsentence'] . ']</a>';
    /// The back to edit xml file button
        $b .= '&nbsp;<a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o .= $b;

    /// Delete any 'changeme' sentence
        ///$statement->deleteSentence('changeme');

    /// Add the fields list
        $sentences =& $statement->getSentences();
        if (!empty($sentences)) {
            $o .= '<h3 class="main">' . $this->str['sentences'] . '</h3>';
            $o .= '<table id="listfields" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($sentences as $key => $sentence) {
            /// Prepend some SQL
                if ($statement->getType() == XMLDB_STATEMENT_INSERT) {
                    $p = 'INSERT INTO ' . $statement->getTable() . ' ';
                } else {
                    $p = 'UNSUPPORTED SENTENCE TYPE ';
                }
            /// Calculate buttons
                $b = '</td><td class="button cell">';
            /// The edit button
                $b .= '<a href="index.php?action=edit_sentence&amp;sentence=' .$key . '&amp;statement=' . urlencode($statement->getName()) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                $b .= '</td><td class="button cell">';
            /// The duplicate button
                $b .= '<a href="index.php?action=new_sentence&amp;postaction=edit_sentence&amp;sesskey=' . sesskey() . '&amp;basesentence=' . $key . '&amp;statement=' . urlencode($statement->getName()) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['duplicate'] . ']</a>';
                $b .= '</td><td class="button cell">';
            /// The delete button
                $b .= '<a href="index.php?action=delete_sentence&amp;sesskey=' . sesskey() . '&amp;sentence=' . $key . '&amp;statement=' . urlencode($statement->getName()) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                $b .= '</td>';
            /// Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell">' . $p . $sentence . $b . '</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }

        $this->output = $o;

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
