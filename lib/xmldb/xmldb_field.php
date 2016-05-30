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
 * This class represent one XMLDB Field
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_field extends xmldb_object {

    /** @var int XMLDB_TYPE_ constants */
    protected $type;

    /** @var int size of field */
    protected $length;

    /** @var bool is null forbidden? XMLDB_NOTNULL */
    protected $notnull;

    /** @var mixed default value */
    protected $default;

    /** @var bool use automatic counter */
    protected $sequence;

    /** @var int number of decimals */
    protected $decimals;

    /**
     * Note:
     *  - Oracle: VARCHAR2 has a limit of 4000 bytes
     *  - SQL Server: NVARCHAR has a limit of 40000 chars
     *  - MySQL: VARCHAR 65,535 chars
     *  - PostgreSQL: no limit
     *
     * @const maximum length of text field
     */
    const CHAR_MAX_LENGTH = 1333;


    /**
     * @const maximum number of digits of integers
     */
    const INTEGER_MAX_LENGTH = 20;

    /**
     * @const max length of decimals
     */
    const NUMBER_MAX_LENGTH = 20;

    /**
     * @const max length of floats
     */
    const FLOAT_MAX_LENGTH = 20;

    /**
     * Note:
     *  - Oracle has 30 chars limit for all names
     *
     * @const maximumn length of field names
     */
    const NAME_MAX_LENGTH = 30;

    /**
     * Creates one new xmldb_field
     * @param string $name of field
     * @param int $type XMLDB_TYPE_INTEGER, XMLDB_TYPE_NUMBER, XMLDB_TYPE_CHAR, XMLDB_TYPE_TEXT, XMLDB_TYPE_BINARY
     * @param string $precision length for integers and chars, two-comma separated numbers for numbers
     * @param bool $unsigned XMLDB_UNSIGNED or null (or false)
     * @param bool $notnull XMLDB_NOTNULL or null (or false)
     * @param bool $sequence XMLDB_SEQUENCE or null (or false)
     * @param mixed $default meaningful default o null (or false)
     * @param xmldb_object $previous
     */
    public function __construct($name, $type=null, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null) {
        $this->type = null;
        $this->length = null;
        $this->notnull = false;
        $this->default = null;
        $this->sequence = false;
        $this->decimals = null;
        parent::__construct($name);
        $this->set_attributes($type, $precision, $unsigned, $notnull, $sequence, $default, $previous);
    }

    /**
     * Set all the attributes of one xmldb_field
     *
     * @param int $type XMLDB_TYPE_INTEGER, XMLDB_TYPE_NUMBER, XMLDB_TYPE_CHAR, XMLDB_TYPE_TEXT, XMLDB_TYPE_BINARY
     * @param string $precision length for integers and chars, two-comma separated numbers for numbers
     * @param bool $unsigned XMLDB_UNSIGNED or null (or false)
     * @param bool $notnull XMLDB_NOTNULL or null (or false)
     * @param bool $sequence XMLDB_SEQUENCE or null (or false)
     * @param mixed $default meaningful default o null (or false)
     * @param xmldb_object $previous
     */
    public function set_attributes($type, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null) {
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
        $this->notnull = !empty($notnull) ? true : false;
        $this->sequence = !empty($sequence) ? true : false;
        $this->setDefault($default);

        if ($this->type == XMLDB_TYPE_BINARY || $this->type == XMLDB_TYPE_TEXT) {
            $this->length = null;
            $this->decimals = null;
        }

        $this->previous = $previous;
    }

    /**
     * Get the type
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the length
     * @return int
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * Get the decimals
     * @return string
     */
    public function getDecimals() {
        return $this->decimals;
    }

    /**
     * Get the notnull
     * @return bool
     */
    public function getNotNull() {
        return $this->notnull;
    }

    /**
     * Get the unsigned
     * @deprecated since moodle 2.3
     * @return bool
     */
    public function getUnsigned() {
        return false;
    }

    /**
     * Get the sequence
     * @return bool
     */
    public function getSequence() {
        return $this->sequence;
    }

    /**
     * Get the default
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * Set the field type
     * @param int $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Set the field length
     * @param int $length
     */
    public function setLength($length) {
        $this->length = $length;
    }

    /**
     * Set the field decimals
     * @param string
     */
    public function setDecimals($decimals) {
        $this->decimals = $decimals;
    }

    /**
     * Set the field unsigned
     * @deprecated since moodle 2.3
     * @param bool $unsigned
     */
    public function setUnsigned($unsigned=true) {
    }

    /**
     * Set the field notnull
     * @param bool $notnull
     */
    public function setNotNull($notnull=true) {
        $this->notnull = $notnull;
    }

    /**
     * Set the field sequence
     * @param bool $sequence
     */
    public function setSequence($sequence=true) {
        $this->sequence = $sequence;
    }

    /**
     * Set the field default
     * @param mixed $default
     */
    public function setDefault($default) {
        // Check, warn and auto-fix '' (empty) defaults for CHAR NOT NULL columns, changing them
        // to NULL so XMLDB will apply the proper default
        if ($this->type == XMLDB_TYPE_CHAR && $this->notnull && $default === '') {
            $this->errormsg = 'XMLDB has detected one CHAR NOT NULL column (' . $this->name . ") with '' (empty string) as DEFAULT value. This type of columns must have one meaningful DEFAULT declared or none (NULL). XMLDB have fixed it automatically changing it to none (NULL). The process will continue ok and proper defaults will be created accordingly with each DB requirements. Please fix it in source (XML and/or upgrade script) to avoid this message to be displayed.";
            $this->debug($this->errormsg);
            $default = null;
        }
        // Check, warn and autofix TEXT|BINARY columns having a default clause (only null is allowed)
        if (($this->type == XMLDB_TYPE_TEXT || $this->type == XMLDB_TYPE_BINARY) && $default !== null) {
            $this->errormsg = 'XMLDB has detected one TEXT/BINARY column (' . $this->name . ") with some DEFAULT defined. This type of columns cannot have any default value. Please fix it in source (XML and/or upgrade script) to avoid this message to be displayed.";
            $this->debug($this->errormsg);
            $default = null;
        }
        $this->default = $default;
    }

    /**
     * Load data from XML to the table
     * @param array $xmlarr
     * @return mixed
     */
    public function arr2xmldb_field($xmlarr) {

        $result = true;

        // Debug the table
        // traverse_xmlize($xmlarr);                   //Debug
        // print_object ($GLOBALS['traverse_array']);  //Debug
        // $GLOBALS['traverse_array']="";              //Debug

        // Process table attributes (name, type, length
        // notnull, sequence, decimals, comment, previous, next)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['TYPE'])) {
            // Check for valid type
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
            // Check for integer values
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
            // Remove length from text and binary
            if ($this->type == XMLDB_TYPE_TEXT ||
                $this->type == XMLDB_TYPE_BINARY) {
                $length = null;
            }
            // Finally, set the length
            $this->length = $length;
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

        $decimals = null;
        if (isset($xmlarr['@']['DECIMALS'])) {
            $decimals = trim($xmlarr['@']['DECIMALS']);
            // Check for integer values
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

        // Set some attributes
        if ($result) {
            $this->loaded = true;
        }
        $this->calculateHash();
        return $result;
    }

    /**
     * This function returns the correct XMLDB_TYPE_XXX value for the
     * string passed as argument
     * @param string $type
     * @return int
     */
    public function getXMLDBFieldType($type) {

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
        // Return the normalized XMLDB_TYPE
        return $result;
    }

    /**
     * This function returns the correct name value for the
     * XMLDB_TYPE_XXX passed as argument
     * @param int $type
     * @return string
     */
    public function getXMLDBTypeName($type) {

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
        // Return the normalized name
        return $result;
    }

    /**
     * This function calculate and set the hash of one xmldb_field
     * @param bool $recursive
     * @return void, modifies $this->hash
     */
     public function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = null;
        } else {
            $defaulthash = is_null($this->default) ? '' : sha1($this->default);
            $key = $this->name . $this->type . $this->length .
                   $this->notnull . $this->sequence .
                   $this->decimals . $this->comment . $defaulthash;
            $this->hash = md5($key);
        }
    }

    /**
     * This function will output the XML text for one field
     * @return string
     */
    public function xmlOutput() {
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
        if (!$this->sequence && $this->default !== null) {
            $o.= ' DEFAULT="' . $this->default . '"';
        }
        if ($this->sequence) {
            $sequence = 'true';
        } else {
            $sequence = 'false';
        }
        $o.= ' SEQUENCE="' . $sequence . '"';
        if ($this->decimals !== null) {
            $o.= ' DECIMALS="' . $this->decimals . '"';
        }
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"';
        }
        $o.= '/>' . "\n";

        return $o;
    }

    /**
     * This function will set all the attributes of the xmldb_field object
     * based on information passed in one ADOField
     * @param string $adofield
     * @return void, sets $this->type
     */
    public function setFromADOField($adofield) {

        // Calculate the XMLDB_TYPE
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
        // Calculate the length of the field
        if ($adofield->max_length > 0 &&
               ($this->type == XMLDB_TYPE_INTEGER ||
                $this->type == XMLDB_TYPE_NUMBER  ||
                $this->type == XMLDB_TYPE_FLOAT   ||
                $this->type == XMLDB_TYPE_CHAR)) {
            $this->length = $adofield->max_length;
        }
        if ($this->type == XMLDB_TYPE_TEXT) {
            $this->length = null;
        }
        if ($this->type == XMLDB_TYPE_BINARY) {
            $this->length = null;
        }
        // Calculate the decimals of the field
        if ($adofield->max_length > 0 &&
            $adofield->scale &&
               ($this->type == XMLDB_TYPE_NUMBER ||
                $this->type == XMLDB_TYPE_FLOAT)) {
            $this->decimals = $adofield->scale;
        }
        // Calculate the notnull field
        if ($adofield->not_null) {
            $this->notnull = true;
        }
        // Calculate the default field
        if ($adofield->has_default) {
            $this->default = $adofield->default_value;
        }
        // Calculate the sequence field
        if ($adofield->auto_increment) {
            $this->sequence = true;
        }
        // Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one xmldb_field
     * @param bool $includeprevious
     * @return string
     */
    public function getPHP($includeprevious=true) {

        $result = '';

        // The XMLDBTYPE
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
        // The length
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
        // Unsigned is not used any more since Moodle 2.3
        $result .= 'null, ';
        // Not Null
        $notnull = $this->getNotnull();
        if (!empty($notnull)) {
            $result .= 'XMLDB_NOTNULL' . ', ';
        } else {
            $result .= 'null, ';
        }
        // Sequence
        $sequence = $this->getSequence();
        if (!empty($sequence)) {
            $result .= 'XMLDB_SEQUENCE' . ', ';
        } else {
            $result .= 'null, ';
        }
        // Default
        $default =  $this->getDefault();
        if ($default !== null && !$this->getSequence()) {
            $result .= "'" . $default . "'";
        } else {
            $result .= 'null';
        }
        // Previous (decided by parameter)
        if ($includeprevious) {
            $previous = $this->getPrevious();
            if (!empty($previous)) {
                $result .= ", '" . $previous . "'";
            } else {
                $result .= ', null';
            }
        }
        // Return result
        return $result;
    }

    /**
     * Shows info in a readable format
     * @return string
     */
    public function readableInfo() {
        $o = '';
        // type
        $o .= $this->getXMLDBTypeName($this->type);
        // length
        if ($this->type == XMLDB_TYPE_INTEGER ||
            $this->type == XMLDB_TYPE_NUMBER  ||
            $this->type == XMLDB_TYPE_FLOAT   ||
            $this->type == XMLDB_TYPE_CHAR) {
            if ($this->length) {
                $o .= ' (' . $this->length;
                if ($this->type == XMLDB_TYPE_NUMBER  ||
                    $this->type == XMLDB_TYPE_FLOAT) {
                    if ($this->decimals !== null) {
                        $o .= ', ' . $this->decimals;
                    }
                }
                $o .= ')';
            }
        }
        // not null
        if ($this->notnull) {
            $o .= ' not null';
        }
        // default
        if ($this->default !== null) {
            $o .= ' default ';
            if ($this->type == XMLDB_TYPE_CHAR ||
                $this->type == XMLDB_TYPE_TEXT) {
                    $o .= "'" . $this->default . "'";
            } else {
                $o .= $this->default;
            }
        }
        // sequence
        if ($this->sequence) {
            $o .= ' auto-numbered';
        }

        return $o;
    }

    /**
     * Validates the field restrictions.
     *
     * The error message should not be localised because it is intended for developers,
     * end users and admins should never see these problems!
     *
     * @param xmldb_table $xmldb_table optional when object is table
     * @return string null if ok, error message if problem found
     */
    public function validateDefinition(xmldb_table $xmldb_table=null) {
        if (!$xmldb_table) {
            return 'Invalid xmldb_field->validateDefinition() call, $xmldb_table is required.';
        }

        $name = $this->getName();
        if (strlen($name) > self::NAME_MAX_LENGTH) {
            return 'Invalid field name in table {'.$xmldb_table->getName().'}: field "'.$this->getName().'" name is too long.'
                .' Limit is '.self::NAME_MAX_LENGTH.' chars.';
        }
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
            return 'Invalid field name in table {'.$xmldb_table->getName().'}: field "'.$this->getName().'" name includes invalid characters.';
        }

        switch ($this->getType()) {
            case XMLDB_TYPE_INTEGER:
                $length = $this->getLength();
                if (!is_number($length) or $length <= 0 or $length > self::INTEGER_MAX_LENGTH) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_INTEGER field "'.$this->getName().'" has invalid length';
                }
                $default = $this->getDefault();
                if (!empty($default) and !is_number($default)) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_INTEGER field "'.$this->getName().'" has invalid default';
                }
                break;

            case XMLDB_TYPE_NUMBER:
                $maxlength = self::NUMBER_MAX_LENGTH;
                if ($xmldb_table->getName() === 'question_numerical_units' and $name === 'multiplier') {
                    //TODO: remove after MDL-32113 is resolved
                    $maxlength = 40;
                }
                $length = $this->getLength();
                if (!is_number($length) or $length <= 0 or $length > $maxlength) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_NUMBER field "'.$this->getName().'" has invalid length';
                }
                $decimals = $this->getDecimals();
                $decimals = empty($decimals) ? 0 : $decimals; // fix missing decimals
                if (!is_number($decimals) or $decimals < 0 or $decimals > $length) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_NUMBER field "'.$this->getName().'" has invalid decimals';
                }
                $default = $this->getDefault();
                if (!empty($default) and !is_numeric($default)) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_NUMBER field "'.$this->getName().'" has invalid default';
                }
                break;

            case XMLDB_TYPE_FLOAT:
                $length = $this->getLength();
                $length = empty($length) ? 6 : $length; // weird, it might be better to require something here...
                if (!is_number($length) or $length <= 0 or $length > self::FLOAT_MAX_LENGTH) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_FLOAT field "'.$this->getName().'" has invalid length';
                }
                $decimals = $this->getDecimals();
                $decimals = empty($decimals) ? 0 : $decimals; // fix missing decimals
                if (!is_number($decimals) or $decimals < 0 or $decimals > $length) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_FLOAT field "'.$this->getName().'" has invalid decimals';
                }
                $default = $this->getDefault();
                if (!empty($default) and !is_numeric($default)) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName().'}: XMLDB_TYPE_FLOAT field "'.$this->getName().'" has invalid default';
                }
                break;

            case XMLDB_TYPE_CHAR:
                if ($this->getLength() > self::CHAR_MAX_LENGTH) {
                    return 'Invalid field definition in table {'.$xmldb_table->getName(). '}: XMLDB_TYPE_CHAR field "'.$this->getName().'" is too long.'
                           .' Limit is '.self::CHAR_MAX_LENGTH.' chars.';
                }
                break;

            case XMLDB_TYPE_TEXT:
                break;

            case XMLDB_TYPE_BINARY:
                break;

            case XMLDB_TYPE_DATETIME:
                break;

            case XMLDB_TYPE_TIMESTAMP:
                break;
        }

        return null;
    }
}
