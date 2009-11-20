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

/// This class will ask for one statement type and table
/// to be able to add sentences of that type

class new_statement extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'statementtype' => 'xmldb',
            'statementtable' => 'xmldb',
            'create' => 'xmldb',
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
        global $CFG, $XMLDB, $db;

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
        $tableparam = optional_param('table', NULL, PARAM_CLEAN);
        $typeparam = optional_param('type', NULL, PARAM_CLEAN);

    /// If no table or type, show form
        if (!$tableparam || !$typeparam) {
        /// No postaction here
            $this->postaction = NULL;
        /// Get list of tables
            $dbtables = $db->MetaTables('TABLES');
            $selecttables = array();
            foreach ($dbtables as $dbtable) {
                $dbtable = str_replace($CFG->prefix, '', $dbtable);
                $selecttables[$dbtable] = $dbtable;
            }
        /// Get list of statement types
            $typeoptions = array (XMLDB_STATEMENT_INSERT => XMLDBStatement::getXMLDBStatementName(XMLDB_STATEMENT_INSERT),
                                  XMLDB_STATEMENT_UPDATE => XMLDBStatement::getXMLDBStatementName(XMLDB_STATEMENT_UPDATE),
                                  XMLDB_STATEMENT_DELETE => XMLDBStatement::getXMLDBStatementName(XMLDB_STATEMENT_DELETE),
                                  XMLDB_STATEMENT_CUSTOM => XMLDBStatement::getXMLDBStatementName(XMLDB_STATEMENT_CUSTOM));
            if (!$selecttables) {
                $this->errormsg = 'No tables available to create statements';
                return false;
            }
        /// Now build the form
            $o = '<form id="form" action="index.php" method="post">';
            $o .= '<div>';
            $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
            $o.= '    <input type="hidden" name ="action" value="new_statement" />';
            $o.= '    <input type="hidden" name ="postaction" value="edit_statement" />';
            $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() . '" />';
            $o.= '    <table id="formelements" class="boxaligncenter" cellpadding="5">';
            $o.= '      <tr><td><label for="type" accesskey="t">' . $this->str['statementtype'] .' </label>' . choose_from_menu($typeoptions, 'type', '', 'choose', '', 0, true) . '<label for="table" accesskey="a">' . $this->str['statementtable'] . ' </label>' .choose_from_menu($selecttables, 'table', '', 'choose', '', 0, true) . '</td></tr>';
            $o.= '      <tr><td colspan="2" align="center"><input type="submit" value="' .$this->str['create'] . '" /></td></tr>';
            $o.= '      <tr><td colspan="2" align="center"><a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a></td></tr>';
            $o.= '    </table>';
            $o.= '</div></form>';

            $this->output = $o;


    /// If table, retrofit information and, if everything works,
    /// go to the table edit action
        } else {
        /// Get some params (table is mandatory here)
            $tableparam = required_param('table', PARAM_CLEAN);
            $typeparam  = required_param('type', PARAM_CLEAN);

        /// Only insert is allowed :-/
            if ($typeparam != XMLDB_STATEMENT_INSERT) {
                $this->errormsg = 'Only insert of records is supported';
                return false;
            }

        /// Calculate the name of the statement
            $typename = XMLDBStatement::getXMLDBStatementName($typeparam);
            $name = trim(strtolower($typename . ' ' . $tableparam));

        /// Check that this Statement hasn't been created before
            if ($structure->getStatement($name)) {
                $this->errormsg = 'The statement "' . $name . '" already exists, please use it to add more sentences';
                return false;
            }

        /// Create one new XMLDBStatement
            $statement = new XMLDBStatement($name);
            $statement->setType($typeparam);
            $statement->setTable($tableparam);
            $statement->setComment('Initial ' . $typename . ' of records on table ' . $tableparam);
        /// Finally, add the whole retroffited table to the structure
        /// in the place specified
            $structure->addStatement($statement);
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
