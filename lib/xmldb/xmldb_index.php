<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

/// This class represent one XMLDB Index

class xmldb_index extends xmldb_object {

    var $unique;
    var $fields;

    /**
     * Creates one new xmldb_index
     */
    function __construct($name, $type=null, $fields=array()) {
        $this->unique = false;
        $this->fields = array();
        parent::__construct($name);
        return $this->set_attributes($type, $fields);
    }

/// TODO: Delete for 2.1 (deprecated in 2.0).
/// Deprecated API starts here
    function setAttributes($type, $fields) {

        debugging('XMLDBIndex->setAttributes() has been deprecated in Moodle 2.0. Will be out in Moodle 2.1. Please use xmldb_index->set_attributes() instead.', DEBUG_DEVELOPER);

        return $this->set_attributes($type, $fields);
    }
/// Deprecated API ends here

    /**
     * Set all the attributes of one xmldb_index
     *
     * @param string type XMLDB_INDEX_UNIQUE, XMLDB_INDEX_NOTUNIQUE
     * @param array fields an array of fieldnames to build the index over
     */
    function set_attributes($type, $fields) {
        $this->unique = !empty($type) ? true : false;
        $this->fields = $fields;
    }

    /**
     * Get the index unique
     */
    function getUnique() {
        return $this->unique;
    }

    /**
     * Set the index unique
     */
    function setUnique($unique = true) {
        $this->unique = $unique;
    }

    /**
     * Set the index fields
     */
    function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * Get the index fields
     */
    function &getFields() {
        return $this->fields;
    }

    /**
     * Load data from XML to the index
     */
    function arr2xmldb_index($xmlarr) {

        $result = true;

    /// Debug the table
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process key attributes (name, unique, fields, comment, previous, next)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['UNIQUE'])) {
            $unique = strtolower(trim($xmlarr['@']['UNIQUE']));
            if ($unique == 'true') {
                $this->unique = true;
            } else if ($unique == 'false') {
                $this->unique = false;
            } else {
                $this->errormsg = 'Incorrect UNIQUE attribute (true/false allowed)';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else {
                $this->errormsg = 'Undefined UNIQUE attribute';
                $this->debug($this->errormsg);
                $result = false;
        }

        if (isset($xmlarr['@']['FIELDS'])) {
            $fields = strtolower(trim($xmlarr['@']['FIELDS']));
            if ($fields) {
                $fieldsarr = explode(',',$fields);
                if ($fieldsarr) {
                    foreach ($fieldsarr as $key => $element) {
                        $fieldsarr [$key] = trim($element);
                    }
                } else {
                    $this->errormsg = 'Incorrect FIELDS attribute (comma separated of fields)';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            } else {
                $this->errormsg = 'Empty FIELDS attribute';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else {
            $this->errormsg = 'Missing FIELDS attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
    /// Finally, set the array of fields
        $this->fields = $fieldsarr;

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
     * This function calculate and set the hash of one xmldb_index
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->unique . implode (', ', $this->fields);
            $this->hash = md5($key);
        }
    }

    /**
     *This function will output the XML text for one index
     */
    function xmlOutput() {
        $o = '';
        $o.= '        <INDEX NAME="' . $this->name . '"';
        if ($this->unique) {
            $unique = 'true';
        } else {
            $unique = 'false';
        }
        $o.= ' UNIQUE="' . $unique . '"';
        $o.= ' FIELDS="' . implode(', ', $this->fields) . '"';
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"';
        }
        if ($this->previous) {
            $o.= ' PREVIOUS="' . $this->previous . '"';
        }
        if ($this->next) {
            $o.= ' NEXT="' . $this->next . '"';
        }
        $o.= '/>' . "\n";

        return $o;
    }

    /**
     * This function will set all the attributes of the xmldb_index object
     * based on information passed in one ADOindex
     */
    function setFromADOIndex($adoindex) {

    /// Set the unique field
        $this->unique = false;
    /// Set the fields, converting all them to lowercase
        $fields = array_flip(array_change_key_case(array_flip($adoindex['columns'])));
        $this->fields = $fields;
    /// Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one xmldb_index
     */
    function getPHP() {

        $result = '';

    /// The type
        $unique = $this->getUnique();
        if (!empty($unique)) {
            $result .= 'XMLDB_INDEX_UNIQUE, ';
        } else {
            $result .= 'XMLDB_INDEX_NOTUNIQUE, ';
        }
    /// The fields
        $indexfields = $this->getFields();
        if (!empty($indexfields)) {
            $result .= 'array(' . "'".  implode("', '", $indexfields) . "')";
        } else {
            $result .= 'null';
        }
    /// Return result
        return $result;
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
}

/// TODO: Delete for 2.1 (deprecated in 2.0).
/// Deprecated API starts here
class XMLDBIndex extends xmldb_index {

    function __construct($name) {
        parent::__construct($name);
    }

}
/// Deprecated API ends here
