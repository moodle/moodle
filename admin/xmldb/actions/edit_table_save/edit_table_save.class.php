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

/// This class will save the changes performed to the name and comment of
/// one table

class edit_table_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'tablenameempty' => 'xmldb',
            'incorrecttablename' => 'xmldb',
            'duplicatetablename' => 'xmldb',
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

        if (!data_submitted('nomatch')) { ///Basic prevention
            error('Wrong action call');
        }

    /// Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);

        $tableparam = strtolower(required_param('table', PARAM_PATH));
        $name = substr(trim(strtolower(required_param('name', PARAM_PATH))),0,28);
        $comment = required_param('comment', PARAM_CLEAN);
        $comment = stripslashes_safe($comment);

        $editeddir =& $XMLDB->editeddirs[$dirpath];
        $structure =& $editeddir->xml_file->getStructure();
        $table =& $structure->getTable($tableparam);

        $errors = array();    /// To store all the errors found

    /// Perform some checks
    /// Check empty name
        if (empty($name)) {
            $errors[] = $this->str['tablenameempty'];
        }
    /// Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrecttablename'];
        }
    /// Check duplicatename
        if ($tableparam != $name && $structure->getTable($name)) {
            $errors[] = $this->str['duplicatetablename'];
        }

        if (!empty($errors)) {
            $temptable = new XMLDBTable($name);
                                                                                                                                      /// Prepare the output
            $site = get_site();
            $navlinks = array();
            $navlinks[] = array('name' => $this->str['administration'], 'link' => '../index.php', 'type' => 'misc');
            $navlinks[] = array('name' => 'XMLDB', 'link' => 'index.php', 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header("$site->shortname: XMLDB", "$site->fullname", $navigation);
            notice ('<p>' .implode(', ', $errors) . '</p>
                     <p>' . $temptable->readableInfo(),
                     'index.php?action=edit_table&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)));
            die; /// re-die :-P
        }

    /// If there is one name change, do it, changing the prev and next
    /// atributes of the adjacent tables
        if ($tableparam != $name) {
            $table->setName($name);
            if ($table->getPrevious()) {
                $prev =& $structure->getTable($table->getPrevious());
                $prev->setNext($name);
                $prev->setChanged(true);
            }
            if ($table->getNext()) {
                $next =& $structure->getTable($table->getNext());
                $next->setPrevious($name);
                $next->setChanged(true);
            }
        }

    /// Set comment
        $table->setComment($comment);

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
