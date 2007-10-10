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

/// This class represent one XMLDB structure

class XMLDBStructure extends XMLDBObject {

    var $path;
    var $version;
    var $tables;
    var $statements;

    /**
     * Creates one new XMLDBStructure
     */
    function XMLDBStructure($name) {
        parent::XMLDBObject($name);
        $this->path = NULL;
        $this->version = NULL;
        $this->tables = array();
        $this->statements = array();
    }

    /**
     * Returns the path of the structure
     */
    function getPath() {
        return $this->path;
    }

    /**
     * Returns the version of the structure
     */
    function getVersion() {
        return $this->version;
    }

    /**
     * Returns one XMLDBTable
     */
    function &getTable($tablename) {
        $i = $this->findTableInArray($tablename);
        if ($i !== NULL) {
            return $this->tables[$i];
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the position of one table in the array.
     */
    function &findTableInArray($tablename) {
        foreach ($this->tables as $i => $table) {
            if ($tablename == $table->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the position of one statement in the array.
     */
    function &findStatementInArray($statementname) {
        foreach ($this->statements as $i => $statement) {
            if ($statementname == $statement->getName()) {
                return $i;
            }
        }
        $null = NULL;
        return $null;
    }

    /**
     * This function will reorder the array of tables
     */
    function orderTables() {
        $result = $this->orderElements($this->tables);
        if ($result) {
            $this->setTables($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function will reorder the array of statements
     */
    function orderStatements() {
        $result = $this->orderElements($this->statements);
        if ($result) {
            $this->setStatements($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the tables of the structure
     */
    function &getTables() {
        return $this->tables;
    }

    /**
     * Returns one XMLDBStatement
     */
    function &getStatement($statementname) {
        $i = $this->findStatementInArray($statementname);
        if ($i !== NULL) {
            return $this->statements[$i];
        }
        $null = NULL;
        return $null;
    }

    /**
     * Returns the statements of the structure
     */
    function &getStatements() {
        return $this->statements;
    }

    /**
     * Set the structure version
     */
    function setVersion($version) {
        $this->version = $version;
    }

    /**
     * Add one table to the structure, allowing to specify the desired order
     * If it's not specified, then the table is added at the end.
     */
    function addTable(&$table, $after=NULL) {

    /// Calculate the previous and next tables
        $prevtable = NULL;
        $nexttable = NULL;

        if (!$after) {
            $alltables =& $this->getTables();
            if ($alltables) {
                end($alltables);
                $prevtable =& $alltables[key($alltables)];
            }
        } else {
            $prevtable =& $this->getTable($after);
        }
        if ($prevtable && $prevtable->getNext()) {
            $nexttable =& $this->getTable($prevtable->getNext());
        }

    /// Set current table previous and next attributes
        if ($prevtable) {
            $table->setPrevious($prevtable->getName());
            $prevtable->setNext($table->getName());
        }
        if ($nexttable) {
            $table->setNext($nexttable->getName());
            $nexttable->setPrevious($table->getName());
        }
    /// Some more attributes
        $table->setLoaded(true);
        $table->setChanged(true);
    /// Add the new table
        $this->tables[] =& $table;
    /// Reorder the whole structure
        $this->orderTables($this->tables);
    /// Recalculate the hash
        $this->calculateHash(true);
    /// We have one new table, so the structure has changed
        $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
        $this->setChanged(true);
    }

    /**
     * Add one statement to the structure, allowing to specify the desired order
     * If it's not specified, then the statement is added at the end.
     */
    function addStatement(&$statement, $after=NULL) {

    /// Calculate the previous and next tables
        $prevstatement = NULL;
        $nextstatement = NULL;

        if (!$after) {
            $allstatements =& $this->getStatements();
            if ($allstatements) {
                end($allstatements);
                $prevstatement =& $allstatements[key($allstatements)];
            }
        } else {
            $prevstatement =& $this->getStatement($after);
        }
        if ($prevstatement && $prevstatement->getNext()) {
            $nextstatement =& $this->getStatement($prevstatement->getNext());
        }

    /// Set current statement previous and next attributes
        if ($prevstatement) {
            $statement->setPrevious($prevstatement->getName());
            $prevstatement->setNext($statement->getName());
        }
        if ($nextstatement) {
            $statement->setNext($nextstatement->getName());
            $nextstatement->setPrevious($statement->getName());
        }
    /// Some more attributes
        $statement->setLoaded(true);
        $statement->setChanged(true);
    /// Add the new statement
        $this->statements[] =& $statement;
    /// Reorder the whole structure
        $this->orderStatements($this->statements);
    /// Recalculate the hash
        $this->calculateHash(true);
    /// We have one new statement, so the structure has changed
        $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
        $this->setChanged(true);
    }

    /**
     * Delete one table from the Structure
     */
    function deleteTable($tablename) {

        $table =& $this->getTable($tablename);
        if ($table) {
            $i = $this->findTableInArray($tablename);
            $prevtable = NULL;
            $nexttable = NULL;
        /// Look for prev and next table
            $prevtable =& $this->getTable($table->getPrevious());
            $nexttable =& $this->getTable($table->getNext());
        /// Change their previous and next attributes
            if ($prevtable) {
                $prevtable->setNext($table->getNext());
            }
            if ($nexttable) {
                $nexttable->setPrevious($table->getPrevious());
            }
        /// Delete the table
            unset($this->tables[$i]);
        /// Reorder the tables
            $this->orderTables($this->tables);
        /// Recalculate the hash
            $this->calculateHash(true);
        /// We have one deleted table, so the structure has changed
            $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
            $this->setChanged(true);
        }
    }

    /**
     * Delete one statement from the Structure
     */
    function deleteStatement($statementname) {

        $statement =& $this->getStatement($statementname);
        if ($statement) {
            $i = $this->findStatementInArray($statementname);
            $prevstatement = NULL;
            $nextstatement = NULL;
        /// Look for prev and next statement
            $prevstatement =& $this->getStatement($statement->getPrevious());
            $nextstatement =& $this->getStatement($statement->getNext());
        /// Change their previous and next attributes
            if ($prevstatement) {
                $prevstatement->setNext($statement->getNext());
            }
            if ($nextstatement) {
                $nextstatement->setPrevious($statement->getPrevious());
            }
        /// Delete the statement
            unset($this->statements[$i]);
        /// Reorder the statements
            $this->orderStatements($this->statements);
        /// Recalculate the hash
            $this->calculateHash(true);
        /// We have one deleted statement, so the structure has changed
            $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
            $this->setChanged(true);
        }
    }

    /**
     * Set the tables
     */
    function setTables(&$tables) {
        $this->tables = $tables;
    }

    /**
     * Set the statements
     */
    function setStatements(&$statements) {
        $this->statements = $statements;
    }

    /**
     * Load data from XML to the structure
     */
    function arr2XMLDBStructure($xmlarr) {

        global $CFG;

        $result = true;

    /// Debug the structure
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process structure attributes (path, comment and version)
        if (isset($xmlarr['XMLDB']['@']['PATH'])) {
            $this->path = trim($xmlarr['XMLDB']['@']['PATH']);
        } else {
            $this->errormsg = 'Missing PATH attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
        if (isset($xmlarr['XMLDB']['@']['VERSION'])) {
            $this->version = trim($xmlarr['XMLDB']['@']['VERSION']);
        } else {
            $this->errormsg = 'Missing VERSION attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
        if (isset($xmlarr['XMLDB']['@']['COMMENT'])) {
            $this->comment = trim($xmlarr['XMLDB']['@']['COMMENT']);
        } else if (!empty($CFG->xmldbdisablecommentchecking)) {
            $this->comment = '';
        } else {
            $this->errormsg = 'Missing COMMENT attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

    /// Iterate over tables
        if (isset($xmlarr['XMLDB']['#']['TABLES']['0']['#']['TABLE'])) {
            foreach ($xmlarr['XMLDB']['#']['TABLES']['0']['#']['TABLE'] as $xmltable) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmltable['@']['NAME']);
                $table = new XMLDBTable($name);
                $table->arr2XMLDBTable($xmltable);
                $this->tables[] = $table;
                if (!$table->isLoaded()) {
                    $this->errormsg = 'Problem loading table ' . $name;
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        } else {
            $this->errormsg = 'Missing TABLES section';
            $this->debug($this->errormsg);
            $result = false;
        }

    /// Perform some general checks over tables
        if ($result && $this->tables) {
        /// Check tables names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->tables)) {
                $this->errormsg = 'Some TABLES name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Check previous & next are ok (duplicates and existing tables)
            $this->fixPrevNext($this->tables);
            if ($result && !$this->checkPreviousNextValues($this->tables)) {
                $this->errormsg = 'Some TABLES previous/next values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Order tables
            if ($result && !$this->orderTables($this->tables)) {
                $this->errormsg = 'Error ordering the tables';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

    /// Iterate over statements
        if (isset($xmlarr['XMLDB']['#']['STATEMENTS']['0']['#']['STATEMENT'])) {
            foreach ($xmlarr['XMLDB']['#']['STATEMENTS']['0']['#']['STATEMENT'] as $xmlstatement) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmlstatement['@']['NAME']);
                $statement = new XMLDBStatement($name);
                $statement->arr2XMLDBStatement($xmlstatement);
                $this->statements[] = $statement;
                if (!$statement->isLoaded()) {
                    $this->errormsg = 'Problem loading statement ' . $name;
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        }

    /// Perform some general checks over statements
        if ($result && $this->statements) {
        /// Check statements names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->statements)) {
                $this->errormsg = 'Some STATEMENTS name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Check previous & next are ok (duplicates and existing statements)
            $this->fixPrevNext($this->statements);
            if ($result && !$this->checkPreviousNextValues($this->statements)) {
                $this->errormsg = 'Some STATEMENTS previous/next values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
        /// Order statements
            if ($result && !$this->orderStatements($this->statements)) {
                $this->errormsg = 'Error ordering the statements';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

    /// Set some attributes
        if ($result) {
            $this->loaded = true;
        }
        $this->calculateHash();
        return $result;
    }

    /**
     * This function calculate and set the hash of one XMLDBStructure
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->name . $this->path . $this->comment;
            if ($this->tables) {
                foreach ($this->tables as $tbl) {
                    $table =& $this->getTable($tbl->getName());
                    if ($recursive) {
                        $table->calculateHash($recursive);
                    }
                    $key .= $table->getHash();
                }
            }
            if ($this->statements) {
                foreach ($this->statements as $sta) {
                    $statement =& $this->getStatement($sta->getName());
                    if ($recursive) {
                        $statement->calculateHash($recursive);
                    }
                    $key .= $statement->getHash();
                }
            }
            $this->hash = md5($key);
        }
    }

    /**
     * This function will output the XML text for one structure
     */
    function xmlOutput() {
        $o = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $o.= '<XMLDB PATH="' . $this->path . '"';
        $o.= ' VERSION="' . $this->version . '"';
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"'."\n";
        }
        $rel = array_fill(0, count(explode('/', $this->path)), '..');
        $rel = implode('/', $rel);
        $o.= '    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n";
        $o.= '    xsi:noNamespaceSchemaLocation="'.$rel.'/lib/xmldb/xmldb.xsd"'."\n";
        $o.= '>' . "\n";
    /// Now the tables
        if ($this->tables) {
            $o.= '  <TABLES>' . "\n";
            foreach ($this->tables as $table) {
                $o.= $table->xmlOutput();
            }
            $o.= '  </TABLES>' . "\n";
        }
    /// Now the statements
        if ($this->statements) {
            $o.= '  <STATEMENTS>' . "\n";
            foreach ($this->statements as $statement) {
                $o.= $statement->xmlOutput();
            }
            $o.= '  </STATEMENTS>' . "\n";
        }
        $o.= '</XMLDB>';

        return $o;
    }

    /**
     * This function returns the number of uses of one table inside
     * a whole XMLDStructure. Useful to detect if the table must be
     * locked. Return false if no uses are found.
     */
    function getTableUses($tablename) {

        $uses = array();

    /// Check if some foreign key in the whole structure is using it
    /// (by comparing the reftable with the tablename)
        $alltables = $this->getTables();
        if ($alltables) {
            foreach ($alltables as $table) {
                $keys = $table->getKeys();
                if ($keys) {
                    foreach ($keys as $key) {
                        if ($key->getType() == XMLDB_KEY_FOREIGN) {
                            if ($tablename == $key->getRefTable()) {
                                $uses[] = 'table ' . $table->getName() . ' key ' . $key->getName();
                            }
                        }
                    }
                }
            }
        }

    /// Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one field inside
     * a whole XMLDBStructure. Useful to detect if the field must be
     * locked. Return false if no uses are found.
     */
    function getFieldUses($tablename, $fieldname) {

        $uses = array();

    /// Check if any key in the table is using it
        $table = $this->getTable($tablename);
        if ($keys = $table->getKeys()) {
            foreach ($keys as $key) {
                if (in_array($fieldname, $key->getFields()) ||
                    in_array($fieldname, $key->getRefFields())) {
                        $uses[] = 'table ' . $table->getName() . ' key ' . $key->getName();
                }
            }
        }
    /// Check if any index in the table is using it
        $table = $this->getTable($tablename);
        if ($indexes = $table->getIndexes()) {
            foreach ($indexes as $index) {
                if (in_array($fieldname, $index->getFields())) {
                    $uses[] = 'table ' . $table->getName() . ' index ' . $index->getName();
                }
            }
        }
    /// Check if some foreign key in the whole structure is using it
    /// By comparing the reftable and refields with the field)
        $alltables = $this->getTables();
        if ($alltables) {
            foreach ($alltables as $table) {
                $keys = $table->getKeys();
                if ($keys) {
                    foreach ($keys as $key) {
                        if ($key->getType() == XMLDB_KEY_FOREIGN) {
                            if ($tablename == $key->getRefTable()) {
                                $reffieds = $key->getRefFields();
                                if (in_array($fieldname, $key->getRefFields())) {
                                    $uses[] = 'table ' . $table->getName() . ' key ' . $key->getName();
                                }
                            }
                        }
                    }
                }
            }
        }

    /// Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one key inside
     * a whole XMLDBStructure. Useful to detect if the key must be
     * locked. Return false if no uses are found.
     */
    function getKeyUses($tablename, $keyname) {

        $uses = array();

    /// Check if some foreign key in the whole structure is using it
    /// (by comparing the reftable and reffields with the fields in the key)
        $mytable = $this->getTable($tablename);
        $mykey = $mytable->getKey($keyname);
        $alltables = $this->getTables();
        if ($alltables && $mykey) {
            foreach ($alltables as $table) {
                $allkeys = $table->getKeys();
                if ($allkeys) {
                    foreach ($allkeys as $key) {
                        if ($key->getType() != XMLDB_KEY_FOREIGN) {
                            continue;
                        }
                        if ($key->getRefTable() == $tablename &&
                            implode(',', $key->getRefFields()) == implode(',', $mykey->getFields())) {
                                $uses[] = 'table ' . $table->getName() . ' key ' . $key->getName();
                        }
                    }
                }
            }
        }

    /// Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one index inside
     * a whole XMLDBStructure. Useful to detect if the index must be
     * locked. Return false if no uses are found.
     */
    function getIndexUses($tablename, $indexname) {

        $uses = array();

    /// Nothing to check, beause indexes haven't uses! Leave it here
    /// for future checks...

    /// Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function will return all the errors found in one structure
     * looking recursively inside each table/statement. Returns
     * an array of errors or false
     */
    function getAllErrors() {

        $errors = array();
    /// First the structure itself
        if ($this->getError()) {
            $errors[] = $this->getError();
        }
    /// Delegate to tables
        if ($tables = $this->getTables()) {
            foreach ($tables as $table) {
                if ($tableerrors = $table->getAllErrors()) {

                }
            }
        /// Add them to the errors array
            if ($tableerrors) {
                $errors = array_merge($errors, $tableerrors);
            }
        }
    /// Delegate to statements
        if ($statements = $this->getStatements()) {
            foreach ($statements as $statement) {
                if ($statement->getError()) {
                    $errors[] = $statement->getError();
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
    function getCreateStructureSQL ($dbtype, $prefix, $statement_end=true) {

        $results = array();

        if ($tables = $this->getTables()) {
            foreach ($tables as $table) {
                $results = array_merge($results, $table->getCreateTableSQL($dbtype, $prefix, $statement_end));
            }
        }

        if ($statements = $this->getStatements()) {
            foreach ($statements as $statement) {
                $results = array_merge($results, $statement->getExecuteStatementSQL($dbtype, $prefix, $statement_end));
            }
        }
        return $results;
    }
}

?>
