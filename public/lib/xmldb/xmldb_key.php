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
 * This class represent one XMLDB Key
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_key extends xmldb_object {

    /** @var int type of key */
    protected $type;

    /** @var array of fields */
    protected $fields;

    /** @var string referenced table */
    protected $reftable;

    /** @var array referenced fields */
    protected $reffields;

    /**
     * Creates one new xmldb_key
     * @param string $name
     * @param string $type XMLDB_KEY_[PRIMARY|UNIQUE|FOREIGN|FOREIGN_UNIQUE]
     * @param array $fields an array of fieldnames to build the key over
     * @param string $reftable name of the table the FK points to or null
     * @param array $reffields an array of fieldnames in the FK table or null
     */
    public function __construct($name, $type=null, $fields=array(), $reftable=null, $reffields=null) {
        $this->type = null;
        $this->fields = array();
        $this->reftable = null;
        $this->reffields = array();
        parent::__construct($name);
        $this->set_attributes($type, $fields, $reftable, $reffields);
    }

    /**
     * Set all the attributes of one xmldb_key
     *
     * @param string $type XMLDB_KEY_[PRIMARY|UNIQUE|FOREIGN|FOREIGN_UNIQUE]
     * @param array $fields an array of fieldnames to build the key over
     * @param string $reftable name of the table the FK points to or null
     * @param array $reffields an array of fieldnames in the FK table or null
     */
    public function set_attributes($type, $fields, $reftable=null, $reffields=null) {
        $this->type = $type;
        $this->fields = $fields;
        $this->reftable = $reftable;
        $this->reffields = empty($reffields) ? array() : $reffields;
    }

    /**
     * Get the key type
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set the key type
     * @param int $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Set the key fields
     * @param array $fields
     */
    public function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * Set the key reftable
     * @param string $reftable
     */
    public function setRefTable($reftable) {
        $this->reftable = $reftable;
    }

    /**
     * Set the key reffields
     * @param array $reffields
     */
    public function setRefFields($reffields) {
        $this->reffields = $reffields;
    }

    /**
     * Get the key fields
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Get the key reftable
     * @return string
     */
    public function getRefTable() {
        return $this->reftable;
    }

    /**
     * Get the key reffields
     * @return array reference to ref fields
     */
    public function getRefFields() {
        return $this->reffields;
    }

    /**
     * Load data from XML to the key
     * @param array $xmlarr
     * @return bool success
     */
    public function arr2xmldb_key($xmlarr) {

        $result = true;

        // Debug the table
        // traverse_xmlize($xmlarr);                   //Debug
        // print_object ($GLOBALS['traverse_array']);  //Debug
        // $GLOBALS['traverse_array']="";              //Debug

        // Process key attributes (name, type, fields, reftable,
        // reffields, comment, previous, next)
        if (isset($xmlarr['@']['NAME'])) {
            $this->name = trim($xmlarr['@']['NAME']);
        } else {
            $this->errormsg = 'Missing NAME attribute';
            $this->debug($this->errormsg);
            $result = false;
        }

        if (isset($xmlarr['@']['TYPE'])) {
            // Check for valid type
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
        // Finally, set the array of fields
        $this->fields = $fieldsarr;

        if (isset($xmlarr['@']['REFTABLE'])) {
            // Check we are in a FK
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
        // Finally, set the reftable
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->reftable = $reftable;
        }

        if (isset($xmlarr['@']['REFFIELDS'])) {
            // Check we are in a FK
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
        // Finally, set the array of reffields
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $this->reffields = $reffieldsarr;
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
     * This function returns the correct XMLDB_KEY_XXX value for the
     * string passed as argument
     * @param string $type
     * @return int
     */
    public function getXMLDBKeyType($type) {

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
            // case 'check':  //Not supported
            //     $result = XMLDB_KEY_CHECK;
            //     break;
        }
        // Return the normalized XMLDB_KEY
        return $result;
    }

    /**
     * This function returns the correct name value for the
     * XMLDB_KEY_XXX passed as argument
     * @param int $type
     * @return string
     */
    public function getXMLDBKeyName($type) {

        $result = '';

        switch ($type) {
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
            // case XMLDB_KEY_CHECK:  //Not supported
            //     $result = 'check';
            //     break;
        }
        // Return the normalized name
        return $result;
    }

    /**
     * This function calculate and set the hash of one xmldb_key
     * @param bool $recursive
     */
     public function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = null;
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
     * @return string
     */
    public function xmlOutput() {
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
            $o.= ' COMMENT="' . htmlspecialchars($this->comment, ENT_COMPAT) . '"';
        }
        $o.= '/>' . "\n";

        return $o;
    }

    /**
     * This function will set all the attributes of the xmldb_key object
     * based on information passed in one ADOkey
     * @oaram array $adokey
     */
    public function setFromADOKey($adokey) {

        // Calculate the XMLDB_KEY
        switch (strtolower($adokey['name'])) {
            case 'primary':
                $this->type = XMLDB_KEY_PRIMARY;
                break;
            default:
                $this->type = XMLDB_KEY_UNIQUE;
        }
        // Set the fields, converting all them to lowercase
        $fields = array_flip(array_change_key_case(array_flip($adokey['columns'])));
        $this->fields = $fields;
        // Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one xmldb_key
     * @return string
     */
    public function getPHP() {

        $result = '';

        // The type
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
            case XMLDB_KEY_FOREIGN_UNIQUE:
                $result .= 'XMLDB_KEY_FOREIGN_UNIQUE' . ', ';
                break;
        }
        // The fields
        $keyfields = $this->getFields();
        if (!empty($keyfields)) {
            $result .= "['".  implode("', '", $keyfields) . "']";
        } else {
            $result .= 'null';
        }
        // The FKs attributes
        if ($this->getType() == XMLDB_KEY_FOREIGN ||
            $this->getType() == XMLDB_KEY_FOREIGN_UNIQUE) {
            // The reftable
            $reftable = $this->getRefTable();
            if (!empty($reftable)) {
                $result .= ", '" . $reftable . "', ";
            } else {
                $result .= 'null, ';
            }
            // The reffields
            $reffields = $this->getRefFields();
            if (!empty($reffields)) {
                $result .= "['".  implode("', '", $reffields) . "']";
            } else {
                $result .= 'null';
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
        $o .= $this->getXMLDBKeyName($this->type);
        // fields
        $o .= ' (' . implode(', ', $this->fields) . ')';
        // foreign key
        if ($this->type == XMLDB_KEY_FOREIGN ||
            $this->type == XMLDB_KEY_FOREIGN_UNIQUE) {
            $o .= ' references ' . $this->reftable . ' (' . implode(', ', $this->reffields) . ')';
        }

        return $o;
    }
}
