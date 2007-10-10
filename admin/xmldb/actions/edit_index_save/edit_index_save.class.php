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

/// This class will save the changes performed to one index

class edit_index_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'indexnameempty' => 'xmldb',
            'incorrectindexname' => 'xmldb',
            'duplicateindexname' => 'xmldb',
            'nofieldsspecified' => 'xmldb',
            'duplicatefieldsused' => 'xmldb',
            'fieldsnotintable' => 'xmldb',
            'fieldsusedinkey' => 'xmldb',
            'fieldsusedinindex' => 'xmldb',
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
        $indexparam = strtolower(required_param('index', PARAM_PATH));
        $name = trim(strtolower(optional_param('name', $indexparam, PARAM_PATH)));

        $comment = required_param('comment', PARAM_CLEAN);
        $comment = trim(stripslashes_safe($comment));

        $unique = required_param('unique', PARAM_INT);
        $fields = required_param('fields', PARAM_CLEAN);
        $fields = str_replace(' ', '', trim(strtolower(stripslashes_safe($fields))));

        $editeddir =& $XMLDB->editeddirs[$dirpath];
        $structure =& $editeddir->xml_file->getStructure();
        $table =& $structure->getTable($tableparam);
        $index =& $table->getIndex($indexparam);
        $oldhash = $index->getHash();

        $errors = array();    /// To store all the errors found

    /// Perform some checks
    /// Check empty name
        if (empty($name)) {
            $errors[] = $this->str['indexnameempty'];
        }
    /// Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrectindexname'];
        }
    /// Check duplicate name
        if ($indexparam != $name && $table->getIndex($name)) {
            $errors[] = $this->str['duplicateindexname'];
        }
        $fieldsarr = explode(',', $fields);
    /// Check the fields isn't empty
        if (empty($fieldsarr[0])) {
            $errors[] = $this->str['nofieldsspecified'];
        } else {
        /// Check that there aren't duplicate column names
            $uniquearr = array_unique($fieldsarr);
            if (count($fieldsarr) != count($uniquearr)) {
                $errors[] = $this->str['duplicatefieldsused'];
            }
        /// Check that all the fields in belong to the table
            foreach ($fieldsarr as $field) {
                if (!$table->getField($field)) {
                    $errors[] = $this->str['fieldsnotintable'];
                    break;
                }
            }
        /// Check that there isn't any key using exactly the same fields
            $tablekeys = $table->getKeys();
            if ($tablekeys) {
                foreach ($tablekeys as $tablekey) {
                    $keyfieldsarr = $tablekey->getFields();
                /// Compare both arrays, looking for diferences
                    $diferences = array_merge(array_diff($fieldsarr, $keyfieldsarr), array_diff($keyfieldsarr, $fieldsarr));
                    if (empty($diferences)) {
                        $errors[] = $this->str['fieldsusedinkey'];
                        break;
                    }
                }
            }
        /// Check that there isn't any index using exactlt the same fields
            $tableindexes = $table->getIndexes();
            if ($tableindexes) {
                foreach ($tableindexes as $tableindex) {
                /// Skip checking against itself
                    if ($indexparam == $tableindex->getName()) {
                        continue;
                    }
                    $indexfieldsarr = $tableindex->getFields();
                /// Compare both arrays, looking for diferences
                    $diferences = array_merge(array_diff($fieldsarr, $indexfieldsarr), array_diff($indexfieldsarr, $fieldsarr));
                    if (empty($diferences)) {
                        $errors[] = $this->str['fieldsusedinindex'];
                        break;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $tempindex = new XMLDBIndex($name);
            $tempindex->setUnique($unique);
            $tempindex->setFields($fieldsarr);
        /// Prepare the output
            $site = get_site();
            $navlinks = array();
            $navlinks[] = array('name' => $this->str['administration'], 'link' => '../index.php', 'type' => 'misc');
            $navlinks[] = array('name' => 'XMLDB', 'link' => 'index.php', 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header("$site->shortname: XMLDB", "$site->fullname", $navigation);
            notice ('<p>' .implode(', ', $errors) . '</p>
                     <p>' . $tempindex->readableInfo(),
                    'index.php?action=edit_index&amp;index=' .$index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)));
            die; /// re-die :-P
        }

    /// Continue if we aren't under errors
        if (empty($errors)) {
        /// If there is one name change, do it, changing the prev and next
        /// atributes of the adjacent fields
            if ($indexparam != $name) {
                $index->setName($name);
                if ($index->getPrevious()) {
                    $prev =& $table->getIndex($index->getPrevious());
                    $prev->setNext($name);
                    $prev->setChanged(true);
                }
                if ($index->getNext()) {
                    $next =& $table->getIndex($index->getNext());
                    $next->setPrevious($name);
                    $next->setChanged(true);
                }
            }

        /// Set comment
            $index->setComment($comment);

        /// Set the rest of fields
            $index->setUnique($unique);
            $index->setFields($fieldsarr);

        /// If the hash has changed from the old one, change the version
        /// and mark the structure as changed
            $index->calculateHash(true);
            if ($oldhash != $index->getHash()) {
                $index->setChanged(true);
                $table->setChanged(true);
            /// Recalculate the structure hash
                $structure->calculateHash(true);
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
