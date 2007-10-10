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

/// This class represent one XMLDB table

class XMLDBTable extends XMLDBObject {

    var $fields;
    var $keys;
    var $indexes;

    /**
     * Creates one new XMLDBTable
     */
    function XMLDBTable($name) {
        parent::XMLDBObject($name);
        $this->fields = array();
        $this->keys = array();
        $this->indexes = array();
    }

    /**
     * Add one field to the table, allowing to specify the desired  order
     * If it's not specified, then the field is added at the end
     */
    function addField(&$field, $after=NULL) {

    /// Calculate the previous and next fields
        $prevfield = NULL;
        $nextfield = NULL;

        if (!$after) {
            $allfields =& $this->getFields();
            if (!empty($allfields)) {
                end($allfields);
                $prevfield =& $allfields[key($allfields)];
            }
        } else {
            $prevfield =& $this->getField($after);
        }
        if ($prevfield && $prevfield->getNext()) {
            $nextfield =& $this->getField($prevfield->getNext());
        }

    /// Set current field previous and next attributes
        if ($prevfield) {
            $field->setPrevious($prevfield->getName());
            $prevfield->setNext($field->getName());
        }
        if ($nextfield) {
            $field->setNext($nextfield->getName());
            $nextfield->setPrevious($field->getName());
        }
    /// Some more attributes
        $field->setLoaded(true);
        $field->setChanged(true);
    /// Add the new field
        $this->fields[] = $field;
    /// Reorder the field
        $this->orderFields($this->fields);
    /// Recalculate the hash
        $this->calculateHash(true);
    /// We have one new field, so the table has changed
        $this->setChanged(true);

        return $field;
    }

    /**
     * Add one key to the table, allowing to specify the desired  order
     * If it's not specified, then the key is added at the end
     */
    function addKey(&$key, $after=NULL) {

    /// Calculate the previous and next keys
        $prevkey = NULL;
        $nextkey = NULL;

        if (!$after) {
            $allkeys =& $this->getKeys();
            if (!empty($allkeys)) {
                end($allkeys);
                $prevkey =& $allkeys[key($allkeys)];
            }
        } else {
            $prevkey =& $this->getKey($after);
        }
        if ($prevkey && $prevkey->getNext()) {
            $nextkey =& $this->getKey($prevkey->getNext());
        }

    /// Set current key previous and next attributes
        if ($prevkey) {
            $key->setPrevious($prevkey->getName());
            $prevkey->setNext($key->getName());
        }
        if ($nextkey) {
            $key->setNext($nextkey->getName());
            $nextkey->setPrevious($key->getName());
        }
    /// Some more attributes
        $key->setLoaded(true);
        $key->setChanged(true);
    /// Add the new key
        $this->keys[] = $key;
    /// Reorder the keys
        $this->orderKeys($this->keys);
    /// Recalculate the hash
        $this->calculateHash(true);
    /// We have one new field, so the table has changed
        $this->setChanged(true);
    }

    /**
     * Add one index to the table, allowing to specify the desired  order
     * If it's not specified, then the index is added at the end
     */
    function addIndex(&$index, $after=NULL) {

    /// Calculate the previous and next indexes
        $previndex = NULL;
        $nextindex = NULL;

        if (!$after) {
            $allindexes =& $this->getIndexes();
            if (!empty($allindexes)) {
                end($allindexes);
                $previndex =& $allindexes[key($allindexes)];
            }
        } else {
            $previndex =& $this->getIndex($after);
        }
        if ($previndex && $previndex->getNext()) {
            $nextindex =& $this->getIndex($previndex->getNext());
        }

    /// Set current index previous and next attributes
        if ($previndex) {
            $index->setPrevious($previndex->getName());
            $previndex->setNext($index->getName());
        }
        if ($nextindex) {
            $index->setNext($nextindex->getName());
            $nextindex->setPrevious($index->getName());
        }

    /// Some more attributes
        $index->setLoaded(true);
        $index->setChanged(true);
    /// Add the new index
        $this->indexes[] = $index;
    /// Reorder the indexes
        $this->orderIndexes($this->indexes);
    /// Recalculate the hash
        $this->calculateHash(true);
    /// We have one new index, so the table has changed
        $this->setChanged(true);
    }

    /**
     * This function will return the array of fields in the table
     */
    function &getFields() {
        return $this->fields;
    }

    /**
     * This function will return the array of keys in the table
     */
    function &getKeys() {
        return $this->keys;
    }

    /**
     * This function will return the array of indexes in the table
     */
    function &getIndexes() {
        return $this->indexes;
    }

    /**
     * Returns one XMLDBField
     */
    function &getField($fieldname) {
        $i = $this->findFieldInArray($fieldname);
        if ($i !== NULL) {
            return $this->fields[$i];
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the position of one field in the array.
     */
    function &findFieldInArray($fieldname) {
        foreach ($this->fields as $i => $field) {
            if ($fieldname == $field->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * This function will reorder the array of fields
     */
    function orderFields() {
        $result = $this->orderElements($this->fields);
        if ($result) {
            $this->setFields($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns one XMLDBKey
     */
    function &getKey($keyname) {
        $i = $this->findKeyInArray($keyname);
        if ($i !== NULL) {
            return $this->keys[$i];
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the position of one key in the array.
     */
    function &findKeyInArray($keyname) {
        foreach ($this->keys as $i => $key) {
            if ($keyname == $key->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * This function will reorder the array of keys
     */
    function orderKeys() {
        $result = $this->orderElements($this->keys);
        if ($result) {
            $this->setKeys($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns one XMLDBIndex
     */
    function &getIndex($indexname) {
        $i = $this->findIndexInArray($indexname);
        if ($i !== NULL) {
            return $this->indexes[$i];
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the position of one index in the array.
     */
    function &findIndexInArray($indexname) {
        foreach ($this->indexes as $i => $index) {
            if ($indexname == $index->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * This function will reorder the array of indexes
     */
    function orderIndexes() {
        $result = $this->orderElements($this->indexes);
        if ($result) {
            $this->setIndexes($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function will set the array of fields in the table
     */
    function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * This function will set the array of keys in the table
     */
    function setKeys($keys) {
        $this->keys = $keys;
    }

    /**
     * This function will set the array of indexes in the table
     */
    function setIndexes($indexes) {
        $this->indexes = $indexes;
    }

    /**
     * Delete one field from the table
     */
    function deleteField($fieldname) {

        $field =& $this->getField($fieldname);
        if ($field) {
            $i = $this->findFieldInArray($fieldname);
            $prevfield = NULL;
            $nextfield = NULL;
        /// Look for prev and next field
            $prevfield =& $this->getField($field->getPrevious());
            $nextfield =& $this->getField($field->getNext());
        /// Change their previous and next attributes
            if ($prevfield) {
                $prevfield->setNext($field->getNext());
            }
            if ($nextfield) {
                $nextfield->setPrevious($field->getPrevious());
            }
        /// Delete the field
            unset($this->fields[$i]);
        /// Reorder the whole structure
            $this->orderFields($this->fields);
        /// Recalculate the hash
            $this->calculateHash(true);
        /// We have one deleted field, so the table has changed
            $this->setChanged(true);
        }
    }

    /**
     * Delete one key from the table
     */
    function deleteKey($keyname) {

        $key =& $this->getKey($keyname);
        if ($key) {
            $i = $this->findKeyInArray($keyname);
            $prevkey = NULL;
            $nextkey = NULL;
        /// Look for prev and next key
            $prevkey =& $this->getKey($key->getPrevious());
            $nextkey =& $this->getKey($key->getNext());
        /// Change their previous and next attributes
            if ($prevkey) {
                $prevkey->setNext($key->getNext());
            }
            if ($nextkey) {
                $nextkey->setPrevious($key->getPrevious());
            }
        /// Delete the key
            unset($this->keys[$i]);
        /// Reorder the Keys
            $this->orderKeys($this->keys);
        /// Recalculate the hash
            $this->calculateHash(true);
        /// We have one deleted key, so the table has changed
            $this->setChanged(true);
        }
    }

    /**
     * Delete one index from the table
     */
    function deleteIndex($indexname) {

        $index =& $this->getIndex($indexname);
        if ($index) {
            $i = $this->findIndexInArray($indexname);
            $previndex = NULL;
            $nextindex = NULL;
        /// Look for prev and next index
            $previndex =& $this->getIndex($index->getPrevious());
            $nextindex =& $this->getIndex($index->getNext());
        /// Change their previous and next attributes
            if ($previndex) {
                $previndex->setNext($index->getNext());
            }
            if ($nextindex) {
                $nextindex->setPrevious($index->getPrevious());
            }
        /// Delete the index
            unset($this->indexes[$i]);
        /// Reorder the indexes
            $this->orderIndexes($this->indexes);
        /// Recalculate the hash
            $this->calculateHash(true);
        /// We have one deleted index, so the table has changed
            $this->setChanged(true);
        }
    }

    /**
     * Load data from XML to the table
     */
    function arr2XMLDBTable($xmlarr) {

        global $CFG;

        $result = true;

    /// Debug the table
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process table attributes (name, comment, previoustable and nexttable)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
        if (isset($xmlarr['@']['COMMENT'])) {
            $this->comment = trim($xmlarr['@']['COMMENT']);
        } else if (!empty($CFG->xmldbdisablecommentchecking)) {
            $this->comment = '';
        } else {
            $this->errormsg = 'Missing COMMENT attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
        if (isset($xmlarr['@']['PREVIOUS'])) {
            $this->previous = trim($xmlarr['@']['PREVIOUS']);
        }
        if (isset($xmlarr['@']['NEXT'])) {
            $this->next = trim($xmlarr['@']['NEXT']);
        }

    /// Iterate over fields
        if (isset($xmlarr['#']['FIELDS']['0']['#']['FIELD'])) {
            foreach ($xmlarr['#']['FIELDS']['0']['#']['FIELD'] as $xmlfield) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmlfield['@']['NAME']);
                $field = new XMLDBField($name);
                $field->arr2XMLDBField($xmlfield);
                $this->fields[] = $field;
                if (!$field->isLoaded()) {
                    $this->errormsg = 'Problem loading field ' . $name;
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        } else {
            $this->errormsg = 'Missing FIELDS section';
            $this->debug($this->errormsg);
            $result = false;
        }

    /// Perform some general checks over fields
        if ($result && $this->fields) {
        /// Check field names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->fields)) {
                $this->errormsg = 'Some FIELDS name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Check previous & next are ok (duplicates and existing fields)
            $this->fixPrevNext($this->fields);
            if ($result && !$this->checkPreviousNextValues($this->fields)) {
                $this->errormsg = 'Some FIELDS previous/next values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Order fields
            if ($result && !$this->orderFields($this->fields)) {
                $this->errormsg = 'Error ordering the fields';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

    /// Iterate over keys
        if (isset($xmlarr['#']['KEYS']['0']['#']['KEY'])) {
            foreach ($xmlarr['#']['KEYS']['0']['#']['KEY'] as $xmlkey) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmlkey['@']['NAME']);
                $key = new XMLDBKey($name);
                $key->arr2XMLDBKey($xmlkey);
                $this->keys[] = $key;
                if (!$key->isLoaded()) {
                    $this->errormsg = 'Problem loading key ' . $name;
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        } else {
            $this->errormsg = 'Missing KEYS section (at least one PK must exist)';
            $this->debug($this->errormsg);
            $result = false;
        }

    /// Perform some general checks over keys
        if ($result && $this->keys) {
        /// Check keys names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->keys)) {
                $this->errormsg = 'Some KEYS name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Check previous & next are ok (duplicates and existing keys)
            $this->fixPrevNext($this->keys);
            if ($result && !$this->checkPreviousNextValues($this->keys)) {
                $this->errormsg = 'Some KEYS previous/next values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Order keys
            if ($result && !$this->orderKeys($this->keys)) {
                $this->errormsg = 'Error ordering the keys';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// TODO: Only one PK
        /// TODO: Not keys with repeated fields
        /// TODO: Check fields and reffieds exist in table
        }

    /// Iterate over indexes
        if (isset($xmlarr['#']['INDEXES']['0']['#']['INDEX'])) {
            foreach ($xmlarr['#']['INDEXES']['0']['#']['INDEX'] as $xmlindex) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmlindex['@']['NAME']);
                $index = new XMLDBIndex($name);
                $index->arr2XMLDBIndex($xmlindex);
                $this->indexes[] = $index;
                if (!$index->isLoaded()) {
                    $this->errormsg = 'Problem loading index ' . $name;
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        }

    /// Perform some general checks over indexes
        if ($result && $this->indexes) {
        /// Check field names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->indexes)) {
                $this->errormsg = 'Some INDEXES name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Check previous & next are ok (duplicates and existing INDEXES)
            $this->fixPrevNext($this->indexes);
            if ($result && !$this->checkPreviousNextValues($this->indexes)) {
                $this->errormsg = 'Some INDEXES previous/next values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Order indexes
            if ($result && !$this->orderIndexes($this->indexes)) {
                $this->errormsg = 'Error ordering the indexes';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// TODO: Not indexes with repeated fields
        /// TODO: Check fields exist in table
        }

    /// Set some attributes
        if ($result) {
            $this->loaded = true;
        }
        $this->calculateHash();
        return $result;
    }

    /**
     * This function calculate and set the hash of one XMLDBTable
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->name . $this->comment;
            if ($this->fields) {
                foreach ($this->fields as $fie) {
                    $field =& $this->getField($fie->getName());
                    if ($recursive) {
                        $field->calculateHash($recursive);
                    }
                    $key .= $field->getHash();
                }
            }
            if ($this->keys) {
                foreach ($this->keys as $ke) {
                    $k =& $this->getKey($ke->getName());
                    if ($recursive) {
                        $k->calculateHash($recursive);
                    }
                    $key .= $k->getHash();
                }
            }
            if ($this->indexes) {
                foreach ($this->indexes as $in) {
                    $index =& $this->getIndex($in->getName());
                    if ($recursive) {
                        $index->calculateHash($recursive);
                    }
                    $key .= $index->getHash();
                }
            }
            $this->hash = md5($key);
        }
    }

    /**
     * This function will output the XML text for one table
     */
    function xmlOutput() {
        $o = '';
        $o.= '    <TABLE NAME="' . $this->name . '"';
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"';
        }
        if ($this->previous) {
            $o.= ' PREVIOUS="' . $this->previous . '"';
        }
        if ($this->next) {
            $o.= ' NEXT="' . $this->next . '"';
        }
            $o.= '>' . "\n";
    /// Now the fields
        if ($this->fields) {
            $o.= '      <FIELDS>' . "\n";
            foreach ($this->fields as $field) {
                $o.= $field->xmlOutput();
            }
            $o.= '      </FIELDS>' . "\n";
        }
    /// Now the keys
        if ($this->keys) {
            $o.= '      <KEYS>' . "\n";
            foreach ($this->keys as $key) {
                $o.= $key->xmlOutput();
            }
            $o.= '      </KEYS>' . "\n";
        }
    /// Now the indexes
        if ($this->indexes) {
            $o.= '      <INDEXES>' . "\n";
            foreach ($this->indexes as $index) {
                $o.= $index->xmlOutput();
            }
            $o.= '      </INDEXES>' . "\n";
        }
        $o.= '    </TABLE>' . "\n";

        return $o;
    }

    /**
     * This function will add one new field to the table with all
     * its attributes defined
     *
     * @param string name name of the field
     * @param string type XMLDB_TYPE_INTEGER, XMLDB_TYPE_NUMBER, XMLDB_TYPE_CHAR, XMLDB_TYPE_TEXT, XMLDB_TYPE_BINARY
     * @param string precision length for integers and chars, two-comma separated numbers for numbers and 'small', 'medium', 'big' for texts and binaries
     * @param string unsigned XMLDB_UNSIGNED or null (or false)
     * @param string notnull XMLDB_NOTNULL or null (or false)
     * @param string sequence XMLDB_SEQUENCE or null (or false)
     * @param string enum XMLDB_ENUM or null (or false)
     * @param array enumvalues an array of possible values if XMLDB_ENUM is set
     * @param string default meaningful default o null (or false)
     * @param string previous name of the previous field in the table or null (or false)
     */
    function addFieldInfo($name, $type, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $enum=null, $enumvalues=null, $default=null, $previous=null) {
        $field = new XMLDBField($name);
        $field->setAttributes($type, $precision, $unsigned, $notnull, $sequence, $enum, $enumvalues, $default);
        $this->addField($field, $previous);

        return $field;
    }

    /**
     * This function will add one new key to the table with all
     * its attributes defined
     *
     * @param string name name of the key
     * @param string type XMLDB_KEY_PRIMARY, XMLDB_KEY_UNIQUE, XMLDB_KEY_FOREIGN
     * @param array fields an array of fieldnames to build the key over
     * @param string reftable name of the table the FK points to or null
     * @param array reffields an array of fieldnames in the FK table or null
     */
    function addKeyInfo($name, $type, $fields, $reftable=null, $reffields=null) {
        $key = new XMLDBKey($name);
        $key->setAttributes($type, $fields, $reftable, $reffields);
        $this->addKey($key);
    }

    /**
     * This function will add one new index to the table with all
     * its attributes defined
     *
     * @param string name name of the index
     * @param string type XMLDB_INDEX_UNIQUE, XMLDB_INDEX_NOTUNIQUE
     * @param array fields an array of fieldnames to build the index over
     */
    function addIndexInfo($name, $type, $fields) {
        $index = new XMLDBIndex($name);
        $index->setAttributes($type, $fields);
        $this->addIndex($index);
    }

    /**
     * This function will return all the errors found in one table
     * looking recursively inside each field/key/index. Returns
     * an array of errors or false
     */
    function getAllErrors() {

        $errors = array();
    /// First the table itself
        if ($this->getError()) {
            $errors[] = $this->getError();
        }
    /// Delegate to fields
        if ($fields = $this->getFields()) {
            foreach ($fields as $field) {
                if ($field->getError()) {
                    $errors[] = $field->getError();
                }
            }
        }
    /// Delegate to keys
        if ($keys = $this->getKeys()) {
            foreach ($keys as $key) {
                if ($key->getError()) {
                    $errors[] = $key->getError();
                }
            }
        }
    /// Delegate to indexes
        if ($indexes = $this->getIndexes()) {
            foreach ($indexes as $index) {
                if ($index->getError()) {
                    $errors[] = $index->getError();
                }
            }
        }
    /// Return decision
        if (count($errors)) {
            return $errors;
        } else {
            return false;
        }
    }

    /**
     * This function will return the SQL code needed to create the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getCreateTableSQL ($dbtype, $prefix, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getCreateTableSQL($this);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to create the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getRenameTableSQL ($dbtype, $prefix, $newname, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getRenameTableSQL($this, $newname);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to drop the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getDropTableSQL ($dbtype, $prefix, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getDropTableSQL($this);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to add one field to the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getAddFieldSQL ($dbtype, $prefix, $xmldb_field, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getAddFieldSQL($this, $xmldb_field);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to drop one field from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getDropFieldSQL ($dbtype, $prefix, $xmldb_field, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getDropFieldSQL($this, $xmldb_field);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to rename one field from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getRenameFieldSQL ($dbtype, $prefix, $xmldb_field, $newname, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getRenameFieldSQL($this, $xmldb_field, $newname);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to alter one field in the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getAlterFieldSQL ($dbtype, $prefix, $xmldb_field, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getAlterFieldSQL($this, $xmldb_field);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to modify the enum of one field in the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getModifyEnumSQL ($dbtype, $prefix, $xmldb_field, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getModifyEnumSQL($this, $xmldb_field);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to modify the default of one field in the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getModifyDefaultSQL ($dbtype, $prefix, $xmldb_field, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getModifyDefaultSQL($this, $xmldb_field);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to add one key to the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getAddKeySQL ($dbtype, $prefix, $xmldb_key, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getAddKeySQL($this, $xmldb_key);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to drop one key from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getDropKeySQL ($dbtype, $prefix, $xmldb_key, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getDropKeySQL($this, $xmldb_key);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to rename one key from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getRenameKeySQL ($dbtype, $prefix, $xmldb_key, $newname, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getRenameKeySQL($this, $xmldb_key, $newname);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to add one index to the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getAddIndexSQL ($dbtype, $prefix, $xmldb_index, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getAddIndexSQL($this, $xmldb_index);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to drop one index from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     */
    function getDropIndexSQL ($dbtype, $prefix, $xmldb_index, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getDropIndexSQL($this, $xmldb_index);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the SQL code needed to rename one index from the table for the specified DB and
     * prefix. Just one simple wrapper over generators.
     * Experimental. Shouldn't be used at all!
     */
    function getRenameIndexSQL ($dbtype, $prefix, $xmldb_index, $newname, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        $results = $generator->getRenameIndexSQL($this, $xmldb_index, $newname);
        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }
        return $results;
    }

    /**
     * This function will return the name of the sequence created for the pk of the table specified
     * Just one simple wrapper over generators. Returns false if not found
     * Note that not all DB use sequences (only Oracle and PostgreSQL)
     */
    function getSequenceFromDB($dbtype, $prefix) {

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);
        return $generator->getSequenceFromDB($this);
    }
}

?>
