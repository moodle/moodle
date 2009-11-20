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

/// This class will delete completely one sentence

class delete_sentence extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'confirmdeletesentence' => 'xmldb',
            'yes' => '',
            'no' => ''
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
        $statementparam = required_param('statement', PARAM_CLEAN);
        $sentenceparam = required_param('sentence', PARAM_INT);

        $confirmed = optional_param('confirmed', false, PARAM_BOOL);

    /// If  not confirmed, show confirmation box
        if (!$confirmed) {
            $o = '<table width="60" class="generalbox" border="0" cellpadding="5" cellspacing="0" id="notice">';
            $o.= '  <tr><td class="generalboxcontent">';
            $o.= '    <p class="centerpara">' . $this->str['confirmdeletesentence'] . '</p>';
            $o.= '    <table class="boxaligncenter" cellpadding="20"><tr><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=delete_sentence&amp;sesskey=' . sesskey() . '&amp;confirmed=yes&amp;postaction=edit_statement&amp;sentence=' . $sentenceparam . '&amp;statement=' . urlencode($statementparam) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '" method="post"><fieldset class="invisiblefieldset">';
            $o.= '          <input type="submit" value="'. $this->str['yes'] .'" /></fieldset></form></div>';
            $o.= '      </td><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=edit_statement&amp;statement=' . urlencode($statementparam) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '" method="post"><fieldset class="invisiblefieldset">';
            $o.= '          <input type="submit" value="'. $this->str['no'] .'" /></fieldset></form></div>';
            $o.= '      </td></tr>';
            $o.= '    </table>';
            $o.= '  </td></tr>';
            $o.= '</table>';

            $this->output = $o;
        } else {
        /// Get the edited dir
            if (!empty($XMLDB->editeddirs)) {
                if (isset($XMLDB->editeddirs[$dirpath])) {
                    $dbdir =& $XMLDB->dbdirs[$dirpath];
                    $editeddir =& $XMLDB->editeddirs[$dirpath];
                    if ($editeddir) {
                        $structure =& $editeddir->xml_file->getStructure();
                        $statements =& $structure->getStatements();
                        $statement =& $structure->getStatement($statementparam);
                        $sentences =& $statement->getSentences();
                    /// Remove the sentence
                        unset ($sentences[$sentenceparam]);

                    /// The statement has changed
                        $statement->setChanged(true);

                    /// Recalculate the hash
                        $structure->calculateHash(true);

                    /// If the hash has changed from the original one, change the version
                    /// and mark the structure as changed
                        $origstructure =& $dbdir->xml_file->getStructure();
                        if ($structure->getHash() != $origstructure->getHash()) {
                            $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
                            $structure->setChanged(true);
                        }
                    }
                }
            }
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
