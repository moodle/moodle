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

/// This class represent one XMLDB Field

class xmldb_field extends xmldb_object {

    var $type;
    var $length;
    var $unsigned;
    var $notnull;
    var $default;
    var $sequence;
    var $decimals;

    /**
     * Creates one new xmldb_field
     */
    function __construct($name, $type=null, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null) {
        $this->type = NULL;
        $this->length = NULL;
        $this->unsigned = true;
        $this->notnull = false;
        $this->default = NULL;
        $this->sequence = false;
        $this->decimals = NULL;
        parent::__construct($name);
        $this->set_attributes($type, $precision, $unsigned, $notnull, $sequence, $default, $previous);
    }

/// TODO: Delete for 2.1 (deprecated in 2.0).
/// Deprecated API starts here

    function setAttributes($type, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $enum=null, $enumvalues=null, $default=null, $previous=null) {

        debugging('XMLDBField->setAttributes() has been deprecated in Moodle 2.0. Will be out in Moodle 2.1. Please use xmldb_field->set_attributes() instead.', DEBUG_DEVELOPER);
        if ($enum) {
            debugging('Also, ENUMs support has been dropped in Moodle 2.0. Your fields specs are incorrect because you are trying to introduce one new ENUM. Created DB estructures will ignore that.');
        }

        return $this->set_attributes($type, $precision, $unsigned, $notnull, $sequence, $default, $previous);
    }
/// Deprecated API ends here

    /**
     * Set all the attributes of one xmldb_field
     *
     * @param string type XMLDB_TYPE_INTEGER, XMLDB_TYPE_NUMBER, XMLDB_TYPE_CHAR, XMLDB_TYPE_TEXT, XMLDB_TYPE_BINARY
     * @param string precision length for integers and chars, two-comma separated numbers for numbers and 'small', 'medium', 'big' for texts and binaries
     * @param string unsigned XMLDB_UNSIGNED or null (or false)
     * @param string notnull XMLDB_NOTNULL or null (or false)
     * @param string sequence XMLDB_SEQUENCE or null (or false)
     * @param string default meaningful default o null (or false)
     */
    function set_attributes($type, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null) {
        $this->type = $type;
    /// Try to split the precision into length and decimals and apply
    /// each one as needed
        $precisionarr = explode(',', $precision);
        if (isset($precisionarr[0])) {
            $this->length = trim($precisionarr[0]);
        }
        if (isset($precisionarr[1])) {
            $this->decimals = trim($precisionarr[1]);
        }
        $this->precision = $type;
        $this->unsigned = !empty($unsigned) ? true : false;
        $this->notnull = !empty($notnull) ? true : false;
        $this->sequence = !empty($sequence) ? true : false;
        $this->setDefault($default);

        $this->previous = $previous;
    }

    /**
     * Get the type
     */
    function getType() {
        return $this->type;
    }

    /**
     * Get the length
     */
    function getLength() {
        return $this->length;
    }

    /**
     * Get the decimals
     */
    function getDecimals() {
        return $this->decimals;
    }

    /**
     * Get the notnull
     */
    function getNotNull() {
        return $this->notnull;
    }

    /**
     * Get the unsigned
     */
    function getUnsigned() {
        return $this->unsigned;
    }

    /**
     * Get the sequence
     */
    function getSequence() {
        return $this->sequence;
    }

    /**
     * Get the default
     */
    function getDefault() {
        return $this->default;
    }

    /**
     * Set the field type
     */
    function setType($type) {
        $this->type = $type;
    }

    /**
     * Set the field length
     */
    function setLength($length) {
        $this->length = $length;
    }

    /**
     * Set the field decimals
     */
    function setDecimals($decimals) {
        $this->decimals = $decimals;
    }

    /**
     * Set the field unsigned
     */
    function setUnsigned($unsigned=true) {
        $this->unsigned = $unsigned;
    }

    /**
     * Set the field notnull
     */
    function setNotNull($notnull=true) {
        $this->notnull = $notnull;
    }

    /**
     * Set the field sequence
     */
    function setSequence($sequence=true) {
        $this->sequence = $sequence;
    }

    /**
     * Set the field default
     */
    function setDefault($default) {
    /// Check, warn and auto-fix '' (empty) defaults for CHAR NOT NULL columns, changing them
    /// to NULL so XMLDB will apply the proper default
        if ($this->type == XMLDB_TYPE_CHAR && $this->notnull && $default === '') {
            $this->errormsg = 'XMLDB has detected one CHAR NOT NULL column (' . $this->name . ") with '' (empty string) as DEFAULT value. This type of columns must have one meaningful DEFAULT declared or none (NULL). XMLDB have fixed it automatically changing it to none (NULL). The process will continue ok and proper defaults will be created accordingly with each DB requirements. Please fix it in source (XML and/or upgrade script) to avoid this message to be displayed.";
            $this->debug($this->errormsg);
            $default = null;
        }
    /// Check, warn and autofix TEXT|BINARY columns having a default clause (only null is allowed)
        if (($this->type == XMLDB_TYPE_TEXT || $this->type == XMLDB_TYPE_BINARY) && $default !== null) {
            $this->errormsg = 'XMLDB has detected one TEXT/BINARY column (' . $this->name . ") with some DEFAULT defined. This type of columns cannot have any default value. Please fix it in source (XML and/or upgrade script) to avoid this message to be displayed.";
            $this->debug($this->errormsg);
            $default = null;
        }
        $this->default = $default;
    }

    /**
     * Load data from XML to the table
     */
    function arr2xmldb_field($xmlarr) {

        $result = true;

    /// Debug the table
    /// traverse_xmlize($xmlarr);                   //Debug
    /// print_object ($GLOBALS['traverse_array']);  //Debug
    /// $GLOBALS['traverse_array']="";              //Debug

    /// Process table attributes (name, type, length, unsigned,
    /// notnull, sequence, decimals, comment, previous, next)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['TYPE'])) {
        /// Check for valid type
            $type = $this->getXMLDBFieldType(trim($xmlarr['@']['TYPE']));
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

        if (isset($xmlarr['@']['LENGTH'])) {
            $length = trim($xmlarr['@']['LENGTH']);
        /// Check for integer values
            if ($this->type == XMLDB_TYPE_INTEGER ||
                $this->type == XMLDB_TYPE_NUMBER ||
                $this->type == XMLDB_TYPE_CHAR) {
                if (!(is_numeric($length)&&(intval($length)==floatval($length)))) {
                    $this->errormsg = 'Incorrect LENGTH attribute for int, number or char fields';
                    $this->debug($this->errormsg);
                    $result = false;
                } else if (!$length) {
                    $this->errormsg = 'Zero LENGTH attribute';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        /// Check for big, medium, small to be applied to text and binary
            if ($this->type == XMLDB_TYPE_TEXT ||
                $this->type == XMLDB_TYPE_BINARY) {
                if (!$length) {
                    $length == 'big';
                }
                if ($length != 'big' &&
                    $length != 'medium' &&
                    $length != 'small') {
                    $this->errormsg = 'Incorrect LENGTH attribute for text and binary fields (only big, medium and small allowed)';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            }
        /// Finally, set the length
            $this->length = $length;
        }

        if (isset($xmlarr['@']['UNSIGNED'])) {
            $unsigned = strtolower(trim($xmlarr['@']['UNSIGNED']));
            if ($unsigned == 'true') {
                $this->unsigned = true;
            } else if ($unsigned == 'false') {
                $this->unsigned = false;
            } else {
                $this->errormsg = 'Incorrect UNSIGNED attribute (true/false allowed)';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

        if (isset($xmlarr['@']['NOTNULL'])) {
            $notnull = strtolower(trim($xmlarr['@']['NOTNULL']));
            if ($notnull == 'true') {
                $this->notnull = true;
            } else if ($notnull == 'false') {
                $this->notnull = false;
            } else {
                $this->errormsg = 'Incorrect NOTNULL attribute (true/false allowed)';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

        if (isset($xmlarr['@']['SEQUENCE'])) {
            $sequence = strtolower(trim($xmlarr['@']['SEQUENCE']));
            if ($sequence == 'true') {
                $this->sequence = true;
            } else if ($sequence == 'false') {
                $this->sequence = false;
            } else {
                $this->errormsg = 'Incorrect SEQUENCE attribute (true/false allowed)';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

        if (isset($xmlarr['@']['DEFAULT'])) {
            $this->setDefault(trim($xmlarr['@']['DEFAULT']));
        }

        $decimals = NULL;
        if (isset($xmlarr['@']['DECIMALS'])) {
            $decimals = trim($xmlarr['@']['DECIMALS']);
        /// Check for integer values
            if ($this->type == XMLDB_TYPE_NUMBER ||
                $this->type == XMLDB_TYPE_FLOAT) {
                if (!(is_numeric($decimals)&&(intval($decimals)==floatval($decimals)))) {
                    $this->errormsg = 'Incorrect DECIMALS attribute for number field';
                    $this->debug($this->errormsg);
                    $result = false;
                } else if ($this->length <= $decimals){
                    $this->errormsg = 'Incorrect DECIMALS attribute (bigget than length)';
                    $this->debug($this->errormsg);
                    $result = false;
                }
            } else {
                $this->errormsg = 'Incorrect DECIMALS attribute for non-number field';
                $this->debug($this->errormsg);
                $result = false;
            }
        } else {
            if ($this->type == XMLDB_TYPE_NUMBER) {
                $decimals = 0;
            }
        }
     // Finally, set the decimals
        if ($this->type == XMLDB_TYPE_NUMBER ||
            $this->type == XMLDB_TYPE_FLOAT) {
            $this->decimals = $decimals;
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

    /// TODO: Drop this check in Moodle 2.1
    /// Detect if there is old enum information in the XML file
        if (isset($xmlarr['@']['ENUM']) && isset($xmlarr['@']['ENUMVALUES'])) {
            $this->hasenums = true;
            if ($xmlarr['@']['ENUM'] == 'true') {
                $this->hasenumsenabled = true;
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
     * This function returns the correct XMLDB_TYPE_XXX value for the
     * string passed as argument
     */
    function getXMLDBFieldType($type) {

        $result = XMLDB_TYPE_INCORRECT;

        switch (strtolower($type)) {
            case 'int':
                $result = XMLDB_TYPE_INTEGER;
                break;
            case 'number':
                $result = XMLDB_TYPE_NUMBER;
                break;
            case 'float':
                $result = XMLDB_TYPE_FLOAT;
                break;
            case 'char':
                $result = XMLDB_TYPE_CHAR;
                break;
            case 'text':
                $result = XMLDB_TYPE_TEXT;
                break;
            case 'binary':
                $result = XMLDB_TYPE_BINARY;
                break;
            case 'datetime':
                $result = XMLDB_TYPE_DATETIME;
                break;
        }
    /// Return the normalized XMLDB_TYPE
        return $result;
    }

    /**
     * This function returns the correct name value for the
     * XMLDB_TYPE_XXX passed as argument
     */
    function getXMLDBTypeName($type) {

        $result = "";

        switch (strtolower($type)) {
            case XMLDB_TYPE_INTEGER:
                $result = 'int';
                break;
            case XMLDB_TYPE_NUMBER:
                $result = 'number';
                break;
            case XMLDB_TYPE_FLOAT:
                $result = 'float';
                break;
            case XMLDB_TYPE_CHAR:
                $result = 'char';
                break;
            case XMLDB_TYPE_TEXT:
                $result = 'text';
                break;
            case XMLDB_TYPE_BINARY:
                $result = 'binary';
                break;
            case XMLDB_TYPE_DATETIME:
                $result = 'datetime';
                break;
        }
    /// Return the normalized name
        return $result;
    }

    /**
     * This function calculate and set the hash of one xmldb_field
     */
     function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = NULL;
        } else {
            $key = $this->name . $this->type . $this->length .
                   $this->unsigned . $this->notnull . $this->sequence .
                   $this->decimals . $this->comment;
            $this->hash = md5($key);
        }
    }

    /**
     *This function will output the XML text for one field
     */
    function xmlOutput() {
        $o = '';
        $o.= '        <FIELD NAME="' . $this->name . '"';
        $o.= ' TYPE="' . $this->getXMLDBTypeName($this->type) . '"';
        if ($this->length) {
            $o.= ' LENGTH="' . $this->length . '"';
        }
        if ($this->notnull) {
            $notnull = 'true';
        } else {
            $notnull = 'false';
        }
        $o.= ' NOTNULL="' . $notnull . '"';
        if ($this->type == XMLDB_TYPE_INTEGER ||
            $this->type == XMLDB_TYPE_NUMBER ||
            $this->type == XMLDB_TYPE_FLOAT) {
            if ($this->unsigned) {
                $unsigned = 'true';
            } else {
                $unsigned = 'false';
            }
            $o.= ' UNSIGNED="' . $unsigned . '"';
        }
        if (!$this->sequence && $this->default !== NULL) {
            $o.= ' DEFAULT="' . $this->default . '"';
        }
        if ($this->sequence) {
            $sequence = 'true';
        } else {
            $sequence = 'false';
        }
        $o.= ' SEQUENCE="' . $sequence . '"';
        if ($this->decimals !== NULL) {
            $o.= ' DECIMALS="' . $this->decimals . '"';
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
     * This function will set all the attributes of the xmldb_field object
     * based on information passed in one ADOField
     */
    function setFromADOField($adofield) {

    /// Calculate the XMLDB_TYPE
        switch (strtolower($adofield->type)) {
            case 'int':
            case 'tinyint':
            case 'smallint':
            case 'bigint':
            case 'integer':
                $this->type = XMLDB_TYPE_INTEGER;
                break;
            case 'number':
            case 'decimal':
            case 'dec':
            case 'numeric':
                $this->type = XMLDB_TYPE_NUMBER;
                break;
            case 'float':
            case 'double':
                $this->type = XMLDB_TYPE_FLOAT;
                break;
            case 'char':
            case 'varchar':
            case 'enum':
                $this->type = XMLDB_TYPE_CHAR;
                break;
            case 'text':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
                $this->type = XMLDB_TYPE_TEXT;
                break;
            case 'blob':
            case 'tinyblob':
            case 'mediumblob':
            case 'longblob':
                $this->type = XMLDB_TYPE_BINARY;
                break;
            case 'datetime':
            case 'timestamp':
                $this->type = XMLDB_TYPE_DATETIME;
                break;
            default:
                $this->type = XMLDB_TYPE_TEXT;
        }
    /// Calculate the length of the field
        if ($adofield->max_length > 0 &&
               ($this->type == XMLDB_TYPE_INTEGER ||
                $this->type == XMLDB_TYPE_NUMBER  ||
                $this->type == XMLDB_TYPE_FLOAT   ||
                $this->type == XMLDB_TYPE_CHAR)) {
            $this->length = $adofield->max_length;
        }
        if ($this->type == XMLDB_TYPE_TEXT) {
            switch (strtolower($adofield->type)) {
                case 'tinytext':
                case 'text':
                    $this->length = 'small';
                    break;
                case 'mediumtext':
                    $this->length = 'medium';
                    break;
                case 'longtext':
                    $this->length = 'big';
                    break;
                default:
                    $this->length = 'small';
            }
        }
        if ($this->type == XMLDB_TYPE_BINARY) {
            switch (strtolower($adofield->type)) {
                case 'tinyblob':
                case 'blob':
                    $this->length = 'small';
                    break;
                case 'mediumblob':
                    $this->length = 'medium';
                    break;
                case 'longblob':
                    $this->length = 'big';
                    break;
                default:
                    $this->length = 'small';
            }
        }
    /// Calculate the decimals of the field
        if ($adofield->max_length > 0 &&
            $adofield->scale &&
               ($this->type == XMLDB_TYPE_NUMBER ||
                $this->type == XMLDB_TYPE_FLOAT)) {
            $this->decimals = $adofield->scale;
        }
    /// Calculate the unsigned field
        if ($adofield->unsigned &&
               ($this->type == XMLDB_TYPE_INTEGER ||
                $this->type == XMLDB_TYPE_NUMBER  ||
                $this->type == XMLDB_TYPE_FLOAT)) {
            $this->unsigned = true;
        }
    /// Calculate the notnull field
        if ($adofield->not_null) {
            $this->notnull = true;
        }
    /// Calculate the default field
        if ($adofield->has_default) {
            $this->default = $adofield->default_value;
        }
    /// Calculate the sequence field
        if ($adofield->auto_increment) {
            $this->sequence = true;
        /// Sequence fields are always unsigned
            $this->unsigned = true;
        }
    /// Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one xmldb_field
     */
    function getPHP($includeprevious=true) {

        $result = '';

    /// The XMLDBTYPE
        switch ($this->getType()) {
            case XMLDB_TYPE_INTEGER:
                $result .= 'XMLDB_TYPE_INTEGER' . ', ';
                break;
            case XMLDB_TYPE_NUMBER:
                $result .= 'XMLDB_TYPE_NUMBER' . ', ';
                break;
            case XMLDB_TYPE_FLOAT:
                $result .= 'XMLDB_TYPE_FLOAT' . ', ';
                break;
            case XMLDB_TYPE_CHAR:
                $result .= 'XMLDB_TYPE_CHAR' . ', ';
                break;
            case XMLDB_TYPE_TEXT:
                $result .= 'XMLDB_TYPE_TEXT' . ', ';
                break;
            case XMLDB_TYPE_BINARY:
                $result .= 'XMLDB_TYPE_BINARY' . ', ';
                break;
            case XMLDB_TYPE_DATETIME:
                $result .= 'XMLDB_TYPE_DATETIME' . ', ';
                break;
            case XMLDB_TYPE_TIMESTAMP:
                $result .= 'XMLDB_TYPE_TIMESTAMP' . ', ';
                break;
        }
    /// The length
        $length = $this->getLength();
        $decimals = $this->getDecimals();
        if (!empty($length)) {
            $result .= "'" . $length;
            if (!empty($decimals)) {
                $result .= ', ' . $decimals;
            }
            $result .= "', ";
        } else {
            $result .= 'null, ';
        }
    /// Unsigned (only applicable to numbers)
        $unsigned = $this->getUnsigned();
        if (!empty($unsigned) &&
           ($this->getType() == XMLDB_TYPE_INTEGER || $this->getType() == XMLDB_TYPE_NUMBER || $this->getType() == XMLDB_TYPE_FLOAT)) {
            $result .= 'XMLDB_UNSIGNED' . ', ';
        } else {
            $result .= 'null, ';
        }
    /// Not Null
        $notnull = $this->getNotnull();
        if (!empty($notnull)) {
            $result .= 'XMLDB_NOTNULL' . ', ';
        } else {
            $result .= 'null, ';
        }
    /// Sequence
        $sequence = $this->getSequence();
        if (!empty($sequence)) {
            $result .= 'XMLDB_SEQUENCE' . ', ';
        } else {
            $result .= 'null, ';
        }
    /// Default
        $default =  $this->getDefault();
        if ($default !== null && !$this->getSequence()) {
            $result .= "'" . $default . "'";
        } else {
            $result .= 'null';
        }
    /// Previous (decided by parameter)
        if ($includeprevious) {
            $previous = $this->getPrevious();
            if (!empty($previous)) {
                $result .= ", '" . $previous . "'";
            } else {
                $result .= ', null';
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
        $o .= $this->getXMLDBTypeName($this->type);
    /// length
        if ($this->type == XMLDB_TYPE_INTEGER ||
            $this->type == XMLDB_TYPE_NUMBER  ||
            $this->type == XMLDB_TYPE_FLOAT   ||
            $this->type == XMLDB_TYPE_CHAR) {
            if ($this->length) {
                $o .= ' (' . $this->length;
                if ($this->type == XMLDB_TYPE_NUMBER  ||
                    $this->type == XMLDB_TYPE_FLOAT) {
                    if ($this->decimals !== NULL) {
                        $o .= ', ' . $this->decimals;
                    }
                }
                $o .= ')';
            }
        }
        if ($this->type == XMLDB_TYPE_TEXT ||
            $this->type == XMLDB_TYPE_BINARY) {
                $o .= ' (' . $this->length . ')';
        }
    /// unsigned
        if ($this->type == XMLDB_TYPE_INTEGER ||
            $this->type == XMLDB_TYPE_NUMBER ||
            $this->type == XMLDB_TYPE_FLOAT) {
            if ($this->unsigned) {
                $o .= ' unsigned';
            } else {
                $o .= ' signed';
            }
        }
    /// not null
        if ($this->notnull) {
            $o .= ' not null';
        }
    /// default
        if ($this->default !== NULL) {
            $o .= ' default ';
            if ($this->type == XMLDB_TYPE_CHAR ||
                $this->type == XMLDB_TYPE_TEXT) {
                    $o .= "'" . $this->default . "'";
            } else {
                $o .= $this->default;
            }
        }
    /// sequence
        if ($this->sequence) {
            $o .= ' auto-numbered';
        }

        return $o;
    }
}

/// TODO: Delete for 2.1 (deprecated in 2.0).
/// Deprecated API starts here
class XMLDBField extends xmldb_field {

    function __construct($name) {
        parent::__construct($name);
    }

}
/// Deprecated API ends here
