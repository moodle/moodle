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

/// This class will save the changes performed to one sentence

class edit_sentence_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'cannotuseidfield' => 'xmldb',
            'missingfieldsinsentence' => 'xmldb',
            'missingvaluesinsentence' => 'xmldb',
            'wrongnumberoffieldsorvalues' => 'xmldb',
            'administration' => ''
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

    /// Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);

        $statementparam = strtolower(required_param('statement', PARAM_CLEAN));
        $sentenceparam = strtolower(required_param('sentence', PARAM_ALPHANUM));

        $fields = required_param('fields', PARAM_CLEAN);
        $fields = trim(stripslashes_safe($fields));
        $values = required_param('values', PARAM_CLEAN);
        $values = trim(stripslashes_safe($values));

        $editeddir =& $XMLDB->editeddirs[$dirpath];
        $structure =& $editeddir->xml_file->getStructure();
        $statement =& $structure->getStatement($statementparam);
        $sentences =& $statement->getSentences();

        $oldsentence = $sentences[$sentenceparam];

        if (!$statement) {
            $this->errormsg = 'Wrong statement specified: ' . $statementparam;
            return false;
        }

    /// For now, only insert sentences are allowed
        if ($statement->getType() != XMLDB_STATEMENT_INSERT) {
            $this->errormsg = 'Wrong Statement Type. Only INSERT allowed';
            return false;
        }

        $errors = array();    /// To store all the errors found

    /// Build the whole sentence
        $sentence = '(' . $fields . ') VALUES (' . $values . ')';

    /// Perform some checks
        $fields = $statement->getFieldsFromInsertSentence($sentence);
        $values = $statement->getValuesFromInsertSentence($sentence);

        if (in_array('id', $fields)) {
            $errors[] = $this->str['cannotuseidfield'];
        }
        if ($result && count($fields) == 0) {
            $errors[] = $this->str['missingfieldsinsentence'];
        }
        if ($result && count($values) == 0) {
            $errors[] = $this->str['missingvaluesinsentence'];
        }
        if ($result && count($fields) != count($values)) {
            $errors[] = $this->str['wrongnumberoffieldsorvalues'];
        }

        if (!empty($errors)) {
        /// Prepare the output
            $site = get_site();
            $navlinks = array();
            $navlinks[] = array('name' => $this->str['administration'], 'link' => '../index.php', 'type' => 'misc');
            $navlinks[] = array('name' => 'XMLDB', 'link' => 'index.php', 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header("$site->shortname: XMLDB", "$site->fullname", $navigation);
            notice ('<p>' .implode(', ', $errors) . '</p>
                     <p>' . s($sentence),
                    'index.php?action=edit_sentence&amp;sentence=' .$sentenceparam . '&amp;statement=' . urlencode($statementparam) . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)));
            die; /// re-die :-P
        }

    /// Continue if we aren't under errors
        if (empty($errors)) {
            $sentences[$sentenceparam] = $sentence;

        /// If the sentence has changed from the old one, change the version
        /// and mark the statement and structure as changed
            if ($oldsentence != $sentence) {
                $statement->setChanged(true);
                $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
            /// Mark as changed
                $structure->setChanged(true);
            }

        /// Launch postaction if exists (leave this here!)
            if ($this->getPostAction() && $result) {
                return $this->launch($this->getPostAction());
            }
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
