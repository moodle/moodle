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

/// This class represent one XMLDB Key

class XMLDBKey extends XMLDBObject {

    var $type;
    var $fields;
    var $reftable;
    var $reffields;

    /**
     * Creates one new XMLDBKey
     */
    function XMLDBKey($name) {
        parent::XMLDBObject($name);
        $this->type = NULL;
        $this->fields = array();
        $this->reftable = NULL;
        $this->reffields = array();
    }

    /**
     * Set all the attributes of one XMLDBKey
     *
     * @param string type XMLDB_KEY_PRIMARY, XMLDB_KEY_UNIQUE, XMLDB_KEY_FOREIGN
     * @param array fields an array of fieldnames to build the key over
     * @param string reftable name of the table the FK points to or null
     * @param array reffields an array of fieldnames in the FK table or null
     */
    function setAttributes($type, $fields, $reftable=null, $reffields=null) {
        $this->type = $type;
        $this->fields = $fields;
        $this->reftable = $reftable;
        $this->reffields = $reffields;
    }

    /**
     * Get the key type
     */
    function getType() {
        return $this->type;
    }

    /**
     * Set the key type
     */
    function setType($type) {
        $this->type = $type;
    }

    /**
     * Set the key fields
     */
    function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * Set the key reftable
     */
    function setRefTable($reftable) {
        $this->reftable = $reftable;
    }

    /**
     * Set the key reffields
     */
    function setRefFields($reffields) {
        $this->reffields = $reffields;
    }

    /**
     * Get the key fields
     */
    function &getFields() {
        return $this->fields;
    }

    /**
     * Get the key reftable
     */
    function &getRefTable() {
        return $this->reftable;
    }

    /**
     * Get the key reffields
     */
    function &getRefFields() {
        return $this->reffields;
    }

    /**
     * Load data from XML to the key
     */
    function arr2XMLDBKey($xmlarr) {

        $result = true;

    /// Debug the table
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process key attributes (name, type, fields, reftable,
    /// reffields, comment, previous, next)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['TYPE'])) {
        /// Check for valid type
            $type = $this->getXMLDBKeyType(trim($xmlarr['@']['TYPE']));
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

        if (isset($xmlarr['@']['REFTABLE'])) {
        /// Check we are in a FK
            if ($this->type == XMLDB_KEY_FOREIGN ||
                $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $reftable = strtolower(trim($xmlarr['@']['REFTABLE']));
                if (!$reftable) {
                    $this->errormsg = 'Empty REFTABLE attribute';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            } else {
                $this->errormsg = 'Wrong REFTABLE attribute (only FK can have it)';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else if ($this->type == XMLDB_KEY_FOREIGN ||
                   $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->errormsg = 'Missing REFTABLE attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
    /// Finally, set the reftable
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->reftable = $reftable;
        }

        if (isset($xmlarr['@']['REFFIELDS'])) {
        /// Check we are in a FK
            if ($this->type == XMLDB_KEY_FOREIGN ||
                $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $reffields = strtolower(trim($xmlarr['@']['REFFIELDS']));
                if ($reffields) {
                    $reffieldsarr = explode(',',$reffields);
                    if ($reffieldsarr) {
                        foreach ($reffieldsarr as $key => $element) {
                            $reffieldsarr [$key] = trim($element);
                        }
                    } else {
                        $this->errormsg = 'Incorrect REFFIELDS attribute (comma separated of fields)';
                        $this->debug($this->errormsg);
                        $result = false;
                    }
                } else {
                    $this->errormsg = 'Empty REFFIELDS attribute';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            } else {
                $this->errormsg = 'Wrong REFFIELDS attribute (only FK can have it)';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else if ($this->type == XMLDB_KEY_FOREIGN ||
                   $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->errormsg = 'Missing REFFIELDS attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
    /// Finally, set the array of reffields
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->reffields = $reffieldsarr;
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
     * This function returns the correct XMLDB_KEY_XXX value for the
     * string passed as argument
     */
    function getXMLDBKeyType($type) {

        $result = XMLDB_KEY_INCORRECT;

        switch (strtolower($type)) {
            case 'primary':
                $result = XMLDB_KEY_PRIMARY;
                break;
            case 'unique':
                $result = XMLDB_KEY_UNIQUE;
                break;
            case 'foreign':
                $result = XMLDB_KEY_FOREIGN;
                break;
            case 'foreign-unique':
                $result = XMLDB_KEY_FOREIGN_UNIQUE;
                break;
        /// case 'check':  //Not supported
        ///     $result = XMLDB_KEY_CHECK;
        ///     break;
        }
    /// Return the normalized XMLDB_KEY
        return $result;
    }

    /**
     * This function returns the correct name value for the
     * XMLDB_KEY_XXX passed as argument
     */
    function getXMLDBKeyName($type) {

        $result = '';

        switch (strtolower($type)) {
            case XMLDB_KEY_PRIMARY:
                $result = 'primary';
                break;
            case XMLDB_KEY_UNIQUE:
                $result = 'unique';
                break;
            case XMLDB_KEY_FOREIGN:
                $result = 'foreign';
                break;
            case XMLDB_KEY_FOREIGN_UNIQUE:
                $result = 'foreign-unique';
                break;
        /// case XMLDB_KEY_CHECK:  //Not supported
        ///     $result = 'check';
        ///     break;
        }
    /// Return the normalized name
        return $result;
    }

    /**
     * This function calculate and set the hash of one XMLDBKey
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->type . implode(', ', $this->fields);
            if ($this->type == XMLDB_KEY_FOREIGN ||
                $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
                $key .= $this->reftable . implode(', ', $this->reffields);
            }
                    ;
            $this->hash = md5($key);
        }
    }

    /**
     *This function will output the XML text for one key
     */
    function xmlOutput() {
        $o = '';
        $o.= '        <KEY NAME="' . $this->name . '"';
        $o.= ' TYPE="' . $this->getXMLDBKeyName($this->type) . '"';
        $o.= ' FIELDS="' . implode(', ', $this->fields) . '"';
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $o.= ' REFTABLE="' . $this->reftable . '"';
            $o.= ' REFFIELDS="' . implode(', ', $this->reffields) . '"';
        }
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
     * This function will set all the attributes of the XMLDBKey object
     * based on information passed in one ADOkey
     */
    function setFromADOKey($adokey) {

    /// Calculate the XMLDB_KEY
        switch (strtolower($adokey['name'])) {
            case 'primary':
                $this->type = XMLDB_KEY_PRIMARY;
                break;
            default:
                $this->type = XMLDB_KEY_UNIQUE;
        }
    /// Set the fields, converting all them to lowercase
        $fields = array_flip(array_change_key_case(array_flip($adokey['columns'])));
        $this->fields = $fields;
    /// Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one XMLDBKey
     */
    function getPHP() {

        $result = '';

    /// The type
        switch ($this->getType()) {
            case XMLDB_KEY_PRIMARY:
                $result .= 'XMLDB_KEY_PRIMARY' . ', ';
                break;
            case XMLDB_KEY_UNIQUE:
                $result .= 'XMLDB_KEY_UNIQUE' . ', ';
                break;
            case XMLDB_KEY_FOREIGN:
                $result .= 'XMLDB_KEY_FOREIGN' . ', ';
                break;
        }
    /// The fields
        $keyfields = $this->getFields();
        if (!empty($keyfields)) {
            $result .= 'array(' . "'".  implode("', '", $keyfields) . "')";
        } else {
            $result .= 'null';
        }
    /// The FKs attributes
        if ($this->getType() == XMLDB_KEY_FOREIGN) {
        /// The reftable
            $reftable = $this->getRefTable();
            if (!empty($reftable)) {
                $result .= ", '" . $reftable . "', ";
            } else {
                $result .= 'null, ';
            }
        /// The reffields
            $reffields = $this->getRefFields();
            if (!empty($reffields)) {
                $result .= 'array(' . "'".  implode("', '", $reffields) . "')";
            } else {
                $result .= 'null';
            }
        }
    /// Return result
        return $result;
    }

    /**
     * Shows info in a readable format
     */
    function readableInfo() {
        $o = '';
    /// type
        $o .= $this->getXMLDBKeyName($this->type);
    /// fields
        $o .= ' (' . implode(', ', $this->fields) . ')';
    /// foreign key
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $o .= ' references ' . $this->reftable . ' (' . implode(', ', $this->reffields) . ')';
        }

        return $o;
    }
}

?>
