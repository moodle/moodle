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

/// This class represent one XMLDB Statement
/// (a group of SQL arbitrary sentences)
/// (only INSERT is allowed for now)

class XMLDBStatement extends XMLDBObject {

    var $table;     // Table we are handling
    var $type;      // XMLDB_STATEMENT_TYPE
    var $sentences; // Collection of sentences in the statement

    /**
     * Creates one new XMLDBStatement
     */
    function XMLDBStatement($name) {
        parent::XMLDBObject($name);
        $this->table     = NULL;
        $this->type      = XMLDB_STATEMENT_INCORRECT;
        $this->sentences = array();
    }

    /**
     * Get the statement table
     */
    function getTable() {
        return $this->table;
    }

    /**
     * Get the statement type
     */
    function getType() {
        return $this->type;
    }

    /**
     * Get the statement sentences
     */
    function &getSentences() {
        return $this->sentences;
    }

    /**
     * Set the statement table
     */
    function setTable($table) {
        $this->table = $table;
    }

    /**
     * Set the statement type
     */
    function setType($type) {
        $this->type = $type;
    }

    /**
     * Add one statement sentence
     */
    function addSentence($sentence) {
        $this->sentences[] = $sentence;
    }

    /**
     * Load data from XML to the index
     */
    function arr2XMLDBStatement($xmlarr) {

        $result = true;

    /// Debug the table
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process key attributes (table, type, comment, previous, next)
        if (isset($xmlarr['@']['TABLE'])) {
            $this->table = strtolower(trim($xmlarr['@']['TABLE']));
        } else {
            $this->errormsg = 'Missing TABLE attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['TYPE'])) {
        /// Check for valid type
            $type = $this->getXMLDBStatementType(trim($xmlarr['@']['TYPE']));
            if ($type) {
                $this->type = $type;
            } else {
                $this->errormsg = 'Invalid TYPE attribute';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else {
            $this->errormsg = 'Missing TYPE attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

    /// Look for sentences
        $sentencesarr = array();

        if (isset($xmlarr['#']['SENTENCES'])) {
            $sentences = $xmlarr['#']['SENTENCES'][0]['#']['SENTENCE'];
            if ($sentences) {
                foreach ($sentences as $sentence) {
                    if (isset($sentence['@']['TEXT'])) {
                        $sentencesarr[] = trim($sentence['@']['TEXT']);
                    } else {
                        $this->errormsg = 'Missing TEXT attribute in sentence';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                }
            }
        }

    /// Finally, set the array of sentences
        $this->sentences = $sentencesarr;

    /// Now, perform some validations over sentences
    /// XMLDB_STATEMENT_INSERT checks
        if ($this->type == XMLDB_STATEMENT_INSERT) {
        /// Separate fields and values into two arrays
            if ($this->sentences) {
                foreach ($this->sentences as $sentence) {
                    $fields = $this->getFieldsFromInsertSentence($sentence);
                    $values = $this->getValuesFromInsertSentence($sentence);
                /// Check that we aren't inserting the id field
                    if (in_array('id', $fields)) {
                        $this->errormsg = 'Cannot insert the "id" field. It is an autonumeric column';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                    if ($result && count($fields) == 0) {
                        $this->errormsg = 'Missing fields in sentence "' . $sentence . '"';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                    if ($result && count($values) == 0) {
                        $this->errormsg = 'Missing values in sentence "' . $sentence . '"';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                    if ($result && count($fields) != count($values)) {
                        $this->errormsg = 'Incorrect number of fields (' .implode(', ', $fields) . ') or values (' . implode(', ', $values) . ')';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                }
            }
        } else {
        /// Sentences different from INSERT are not valid for now
            $this->errormsg = 'Only INSERT statements are supported';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['COMMENT'])) {
            $this->comment = trim($xmlarr['@']['COMMENT']);
        }

        if (isset($xmlarr['@']['PREVIOUS'])) {
            $this->previous = trim($xmlarr['@']['PREVIOUS']);
        }

        if (isset($xmlarr['@']['NEXT'])) {
            $this->next = trim($xmlarr['@']['NEXT']);
        }

    /// Set some attributes
        if ($result) {
            $this->loaded = true;
        }
        $this->calculateHash();
        return $result;
    }

    /**
     * This function returns the correct XMLDB_STATEMENT_XXX value for the
     * string passed as argument
     */
    function getXMLDBStatementType($type) {

        $result = XMLDB_STATEMENT_INCORRECT;

        switch (strtolower($type)) {
            case 'insert':
                $result = XMLDB_STATEMENT_INSERT;
                break;
            case 'update':
                $result = XMLDB_STATEMENT_UPDATE;
                break;
            case 'delete':
                $result = XMLDB_STATEMENT_DELETE;
                break;
            case 'custom':
                $result = XMLDB_STATEMENT_CUSTOM;
                break;
        }
    /// Return the normalized XMLDB_STATEMENT
        return $result;
    }

    /**
     * This function returns the correct name value for the
     * XMLDB_STATEMENT_XXX passed as argument
     */
    function getXMLDBStatementName($type) {

        $result = '';

        switch (strtolower($type)) {
            case XMLDB_STATEMENT_INSERT:
                $result = 'insert';
                break;
            case XMLDB_STATEMENT_UPDATE:
                $result = 'update';
                break;
            case XMLDB_STATEMENT_DELETE:
                $result = 'delete';
                break;
            case XMLDB_STATEMENT_CUSTOM:
                $result = 'custom';
                break;
        }
    /// Return the normalized name
        return $result;
    }

    /**
     * This function calculate and set the hash of one XMLDBStatement
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->table . $this->type . implode (', ', $this->sentences);
            $this->hash = md5($key);
        }
    }

    /**
     * This function will output the XML text for one statement
     */
    function xmlOutput() {
        $o = '';
        $o.= '    <STATEMENT NAME="' . $this->name . '" TYPE="' . XMLDBStatement::getXMLDBStatementName($this->type) . '" TABLE="' . $this->table . '"';
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"';
        }
        if ($this->previous) {
            $o.= ' PREVIOUS="' . $this->previous . '"';
        }
        if ($this->next) {
            $o.= ' NEXT="' . $this->next . '"';
        }
        if ($this->sentences) {
            $o.= '>' . "\n";
            $o.= '      <SENTENCES>' . "\n";
            foreach ($this->sentences as $sentence) {
                $o.= '        <SENTENCE TEXT="' . htmlspecialchars($sentence) . '" />' . "\n";
            }
            $o.= '      </SENTENCES>' . "\n";
            $o.= '    </STATEMENT>' . "\n";
        } else {
            $o.= '/>' . "\n";
        }

        return $o;
    }

    /**
     * This function will set all the attributes of the XMLDBIndex object
     * based on information passed in one ADOindex
     */
    function setFromADOIndex($adoindex) {

    /// Set the unique field
        $this->unique = false;
    /// Set the fields
        $this->fields = $adoindex['columns'];
    /// Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Shows info in a readable format
     */
    function readableInfo() {
        $o = '';
    /// unique
        if ($this->unique) {
            $o .= 'unique';
        } else {
            $o .= 'not unique';
        }
    /// fields
        $o .= ' (' . implode(', ', $this->fields) . ')';

        return $o;
    }

    /**
     * This function will return an array of fields from one INSERT sentence
     */
    function getFieldsFromInsertSentence($sentence) {

        $fields = array();

    /// Get first part from the sentence (before VALUES)
        preg_match('/^\((.*)\)\s+VALUES/Us', $sentence, $matches);
        if (isset($matches[1])) {
            $part = $matches[1];
        /// Convert the comma separated string to an array
            $arr = $this->comma2array($part);
            if ($arr) {
                $fields = $arr;
            }
        }

        return $fields;
    }

    /**
     * This function will return an array of values from one INSERT sentence
     */
    function getValuesFromInsertSentence($sentence) {

        $values = array();

    /// Get second part from the sentence (after VALUES)
        preg_match('/VALUES\s*\((.*)\)$/is', $sentence, $matches);
        if (isset($matches[1])) {
            $part = $matches[1];
        /// Convert the comma separated string to an array
            $arr = $this->comma2array($part);
            if ($arr) {
                $values = $arr;
            }
        }

        return $values;
    }

    /**
     * This function will return the code needed to execute a collection
     * of sentences present inside one statement for the specified BD
     * and prefix.
     * For now it only supports INSERT statements
     */
    function getExecuteStatementSQL ($dbtype, $prefix, $statement_end=true) {

        $results = array();

    /// Based on statement type
        switch ($this->type) {
            case XMLDB_STATEMENT_INSERT:
                $results = $this->getExecuteInsertSQL($dbtype, $prefix, $statement_end);
                break;
            case XMLDB_STATEMENT_UPDATE:
                break;
            case XMLDB_STATEMENT_DELETE:
                break;
            case XMLDB_STATEMENT_CUSTOM:
                break;
        }

        return $results;
    }

    /**
     * This function will return the code needed to execute a collection
     * of insert sentences present inside the statement for the specified BD
     * and prefix. Just one simple wrapper over generators.
     */
    function getExecuteInsertSQL ($dbtype, $prefix, $statement_end=true) {

        $results = array();

        $classname = 'XMLDB' . $dbtype;
        $generator = new $classname();
        $generator->setPrefix($prefix);

        $results = $generator->getExecuteInsertSQL($this);

        if ($statement_end) {
            $results = $generator->getEndedStatements($results);
        }

        return $results;
    }

}

?>
