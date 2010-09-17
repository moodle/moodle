<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class verifies all the data introduced when editing a key for correctness,
 * performing changes / displaying errors depending of the results.
 *
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_key_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'keynameempty' => 'xmldb',
            'incorrectkeyname' => 'xmldb',
            'duplicatekeyname' => 'xmldb',
            'nofieldsspecified' => 'xmldb',
            'duplicatefieldsused' => 'xmldb',
            'fieldsnotintable' => 'xmldb',
            'fieldsusedinkey' => 'xmldb',
            'fieldsusedinindex' => 'xmldb',
            'noreftablespecified' => 'xmldb',
            'wrongnumberofreffields' => 'xmldb',
            'noreffieldsspecified' => 'xmldb',
            'nomasterprimaryuniquefound' => 'xmldb',
            'masterprimaryuniqueordernomatch' => 'xmldb',
            'primarykeyonlyallownotnullfields' => 'xmldb',
            'back' => 'xmldb',
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
        //$this->does_generate = ACTION_NONE;
        $this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed

        if (!data_submitted()) { ///Basic prevention
            print_error('wrongcall', 'error');
        }

    /// Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        $tableparam = strtolower(required_param('table', PARAM_PATH));
        $keyparam = strtolower(required_param('key', PARAM_PATH));
        $name = trim(strtolower(optional_param('name', $keyparam, PARAM_PATH)));

        $comment = required_param('comment', PARAM_CLEAN);
        $comment = trim($comment);

        $type = required_param('type', PARAM_INT);
        $fields = required_param('fields', PARAM_CLEAN);
        $fields = str_replace(' ', '', trim(strtolower($fields)));

        if ($type == XMLDB_KEY_FOREIGN ||
            $type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $reftable = trim(strtolower(required_param('reftable', PARAM_PATH)));
            $reffields= required_param('reffields', PARAM_CLEAN);
            $reffields = str_replace(' ', '', trim(strtolower($reffields)));
        }

        $editeddir =& $XMLDB->editeddirs[$dirpath];
        $structure =& $editeddir->xml_file->getStructure();
        $table =& $structure->getTable($tableparam);
        $key =& $table->getKey($keyparam);
        $oldhash = $key->getHash();

        $errors = array();    /// To store all the errors found

    /// Perform some checks
    /// Check empty name
        if (empty($name)) {
            $errors[] = $this->str['keynameempty'];
        }
    /// Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrectkeyname'];
        }
    /// Check duplicate name
        if ($keyparam != $name && $table->getKey($name)) {
            $errors[] = $this->str['duplicatekeyname'];
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
        /// If primary, check that all the fields are not null
            if ($type == XMLDB_KEY_PRIMARY) {
                foreach ($fieldsarr as $field) {
                    if ($fi = $table->getField($field)) {
                        if (!$fi->getNotNull()) {
                            $errors[] = $this->str['primarykeyonlyallownotnullfields'];
                            break;
                        }
                    }
                }
            }
        /// Check that there isn't any key using exactly the same fields
            $tablekeys = $table->getKeys();
            if ($tablekeys) {
                foreach ($tablekeys as $tablekey) {
                /// Skip checking against itself
                    if ($keyparam == $tablekey->getName()) {
                        continue;
                    }
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
                    $indexfieldsarr = $tableindex->getFields();
                /// Compare both arrays, looking for diferences
                    $diferences = array_merge(array_diff($fieldsarr, $indexfieldsarr), array_diff($indexfieldsarr, $fieldsarr));
                    if (empty($diferences)) {
                        $errors[] = $this->str['fieldsusedinindex'];
                        break;
                    }
                }
            }
        /// If foreign key
            if ($type == XMLDB_KEY_FOREIGN ||
                $type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $reffieldsarr = explode(',', $reffields);
            /// Check reftable is not empty
                if (empty($reftable)) {
                    $errors[] = $this->str['noreftablespecified'];
                } else
            /// Check reffields are not empty
                if (empty($reffieldsarr[0])) {
                    $errors[] = $this->str['noreffieldsspecified'];
                } else
            /// Check the number of fields is correct
                if (count($fieldsarr) != count($reffieldsarr)) {
                    $errors[] = $this->str['wrongnumberofreffields'];
                } else {
            /// Check, if pointing to one structure table, that there is one master key for this key
                    if ($rt = $structure->getTable($reftable)) {
                        $masterfound = false;
                        $reftablekeys = $rt->getKeys();
                        if ($reftablekeys) {
                            foreach ($reftablekeys as $reftablekey) {
                            /// Only compare with primary and unique keys
                                if ($reftablekey->getType() != XMLDB_KEY_PRIMARY && $reftablekey->getType() != XMLDB_KEY_UNIQUE) {
                                    continue;
                                }
                                $keyfieldsarr = $reftablekey->getFields();
                            /// Compare both arrays, looking for diferences
                                $diferences = array_merge(array_diff($reffieldsarr, $keyfieldsarr), array_diff($keyfieldsarr, $reffieldsarr));
                                if (empty($diferences)) {
                                    $masterfound = true;
                                    break;
                                }
                            }
                            if (!$masterfound) {
                                $errors[] = $this->str['nomasterprimaryuniquefound'];
                            } else {
                            /// Quick test of the order
                               if (implode(',', $reffieldsarr) != implode(',', $keyfieldsarr)) {
                                   $errors[] = $this->str['masterprimaryuniqueordernomatch'];
                               }
                            }
                        }
                    }
                }
            }
        }


        if (!empty($errors)) {
            $tempkey = new xmldb_key($name);
            $tempkey->setType($type);
            $tempkey->setFields($fieldsarr);
            if ($type == XMLDB_KEY_FOREIGN ||
                $type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $tempkey->setRefTable($reftable);
                $tempkey->setRefFields($reffieldsarr);
            }
        /// Prepare the output
            $o = '<p>' .implode(', ', $errors) . '</p>
                  <p>' . $name . ': ' . $tempkey->readableInfo() . '</p>';
            $o.= '<a href="index.php?action=edit_key&amp;key=' .$key->getName() . '&amp;table=' . $table->getName() .
                 '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
            $this->output = $o;
        }

    /// Continue if we aren't under errors
        if (empty($errors)) {
        /// If there is one name change, do it, changing the prev and next
        /// atributes of the adjacent fields
            if ($keyparam != $name) {
                $key->setName($name);
                if ($key->getPrevious()) {
                    $prev =& $table->getKey($key->getPrevious());
                    $prev->setNext($name);
                    $prev->setChanged(true);
                }
                if ($key->getNext()) {
                    $next =& $table->getKey($key->getNext());
                    $next->setPrevious($name);
                    $next->setChanged(true);
                }
            }

        /// Set comment
            $key->setComment($comment);

        /// Set the rest of fields
            $key->setType($type);
            $key->setFields($fieldsarr);
            if ($type == XMLDB_KEY_FOREIGN ||
                $type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $key->setRefTable($reftable);
                $key->setRefFields($reffieldsarr);
            }

        /// If the hash has changed from the old one, change the version
        /// and mark the structure as changed
            $key->calculateHash(true);
            if ($oldhash != $key->getHash()) {
                $key->setChanged(true);
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

