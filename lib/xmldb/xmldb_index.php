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
 * This class represent one XMLDB Index
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_index extends xmldb_object {

    /** @var bool is unique? */
    protected $unique;

    /** @var array index fields */
    protected $fields;

    /** @var array index hints */
    protected $hints;

    /**
     * Note:
     *  - MySQL: MyISAM has a limit of 1000 bytes for any key including composed, InnoDB has limit 3500 bytes.
     *
     * @const max length of composed indexes, one utf-8 char is 3 bytes in the worst case
     */
    const INDEX_COMPOSED_MAX_BYTES = 999;

    /**
     * Note:
     *  - MySQL: InnoDB limits size of index on single column to 767bytes (256 chars)
     *
     * @const single column index length limit, one utf-8 char is 3 bytes in the worst case
     */
    const INDEX_MAX_BYTES = 765;

    /**
     * Creates one new xmldb_index
     *
     * @param string $name
     * @param string $type XMLDB_INDEX_UNIQUE, XMLDB_INDEX_NOTUNIQUE
     * @param array $fields an array of fieldnames to build the index over
     * @param array $hints an array of optional hints
     */
    public function __construct($name, $type=null, $fields=array(), $hints=array()) {
        $this->unique = false;
        $this->fields = array();
        $this->hints = array();
        parent::__construct($name);
        $this->set_attributes($type, $fields, $hints);
    }

    /**
     * Set all the attributes of one xmldb_index
     *
     * @param string type XMLDB_INDEX_UNIQUE, XMLDB_INDEX_NOTUNIQUE
     * @param array fields an array of fieldnames to build the index over
     * @param array $hints array of optional hints
     */
    public function set_attributes($type, $fields, $hints = array()) {
        $this->unique = !empty($type) ? true : false;
        $this->fields = $fields;
        $this->hints = $hints;
    }

    /**
     * Get the index unique
     * @return bool
     */
    public function getUnique() {
        return $this->unique;
    }

    /**
     * Set the index unique
     * @param bool $unique
     */
    public function setUnique($unique = true) {
        $this->unique = $unique;
    }

    /**
     * Set the index fields
     * @param array $fields
     */
    public function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * Get the index fields
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Set optional index hints.
     * @param array $hints
     */
    public function setHints($hints) {
        $this->hints = $hints;
    }

    /**
     * Returns optional index hints.
     * @return array
     */
    public function getHints() {
        return $this->hints;
    }

    /**
     * Load data from XML to the index
     * @param $xmlarr array
     * @return bool
     */
    public function arr2xmldb_index($xmlarr) {

        $result = true;

        // Debug the table
        // traverse_xmlize($xmlarr);                   //Debug
        // print_object ($GLOBALS['traverse_array']);  //Debug
        // $GLOBALS['traverse_array']="";              //Debug

        // Process key attributes (name, unique, fields, comment, previous, next)
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
        // Finally, set the array of fields
        $this->fields = $fieldsarr;

        if (isset($xmlarr['@']['HINTS'])) {
            $this->hints = array();
            $hints = strtolower(trim($xmlarr['@']['HINTS']));
            if ($hints !== '') {
                $hints = explode(',', $hints);
                $this->hints = array_map('trim', $hints);
            }
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
     * This function calculate and set the hash of one xmldb_index
     * @retur nvoid, changes $this->hash
     */
     public function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = null;
        } else {
            $key = $this->unique . implode (', ', $this->fields) . implode (', ', $this->hints);
            $this->hash = md5($key);
        }
    }

    /**
     *This function will output the XML text for one index
     * @return string
     */
    public function xmlOutput() {
        $o = '';
        $o.= '        <INDEX NAME="' . $this->name . '"';
        if ($this->unique) {
            $unique = 'true';
        } else {
            $unique = 'false';
        }
        $o.= ' UNIQUE="' . $unique . '"';
        $o.= ' FIELDS="' . implode(', ', $this->fields) . '"';
        if ($this->hints) {
            $o.= ' HINTS="' . implode(', ', $this->hints) . '"';
        }
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment) . '"';
        }
        $o.= '/>' . "\n";

        return $o;
    }

    /**
     * This function will set all the attributes of the xmldb_index object
     * based on information passed in one ADOindex
     * @param array
     * @return void
     */
    public function setFromADOIndex($adoindex) {

        // Set the unique field
        $this->unique = false;
        // Set the fields, converting all them to lowercase
        $fields = array_flip(array_change_key_case(array_flip($adoindex['columns'])));
        $this->fields = $fields;
        // Some more fields
        $this->loaded = true;
        $this->changed = true;
    }

    /**
     * Returns the PHP code needed to define one xmldb_index
     * @return string
     */
    public function getPHP() {

        $result = '';

        // The type
        $unique = $this->getUnique();
        if (!empty($unique)) {
            $result .= 'XMLDB_INDEX_UNIQUE, ';
        } else {
            $result .= 'XMLDB_INDEX_NOTUNIQUE, ';
        }
        // The fields
        $indexfields = $this->getFields();
        if (!empty($indexfields)) {
            $result .= 'array(' . "'".  implode("', '", $indexfields) . "')";
        } else {
            $result .= 'null';
        }
        // Hints
        $hints = $this->getHints();
        if (!empty($hints)) {
            $result .= ', array(' . "'".  implode("', '", $hints) . "')";
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
        // unique
        if ($this->unique) {
            $o .= 'unique';
        } else {
            $o .= 'not unique';
        }
        // fields
        $o .= ' (' . implode(', ', $this->fields) . ')';

        if ($this->hints) {
            $o .= ' [' . implode(', ', $this->hints) . ']';
        }

        return $o;
    }

    /**
     * Validates the index restrictions.
     *
     * The error message should not be localised because it is intended for developers,
     * end users and admins should never see these problems!
     *
     * @param xmldb_table $xmldb_table optional when object is table
     * @return string null if ok, error message if problem found
     */
    public function validateDefinition(xmldb_table $xmldb_table=null) {
        if (!$xmldb_table) {
            return 'Invalid xmldb_index->validateDefinition() call, $xmldb_table is required.';
        }

        $total = 0;
        foreach ($this->getFields() as $fieldname) {
            if (!$field = $xmldb_table->getField($fieldname)) {
                // argh, we do not have the fields loaded yet, this should not happen during install
                continue;
            }

            switch ($field->getType()) {
                case XMLDB_TYPE_INTEGER:
                    $total += 8; // big int
                    break;

                case XMLDB_TYPE_NUMBER:
                    $total += 12; // this is just a guess
                    break;

                case XMLDB_TYPE_FLOAT:
                    $total += 8; // double precision
                    break;

                case XMLDB_TYPE_CHAR:
                    if ($field->getLength() > self::INDEX_MAX_BYTES / 3) {
                        return 'Invalid index definition in table {'.$xmldb_table->getName(). '}: XMLDB_TYPE_CHAR field "'.$field->getName().'" can not be indexed because it is too long.'
                                .' Limit is '.(self::INDEX_MAX_BYTES/3).' chars.';
                    }
                    $total += ($field->getLength() * 3); // the most complex utf-8 chars have 3 bytes
                    break;

                case XMLDB_TYPE_TEXT:
                    return 'Invalid index definition in table {'.$xmldb_table->getName(). '}: XMLDB_TYPE_TEXT field "'.$field->getName().'" can not be indexed';
                    break;

                case XMLDB_TYPE_BINARY:
                    return 'Invalid index definition in table {'.$xmldb_table->getName(). '}: XMLDB_TYPE_BINARY field "'.$field->getName().'" can not be indexed';
                    break;

                case XMLDB_TYPE_DATETIME:
                    $total += 8; // this is just a guess
                    break;

                case XMLDB_TYPE_TIMESTAMP:
                    $total += 8; // this is just a guess
                    break;
            }
        }

        if ($total > self::INDEX_COMPOSED_MAX_BYTES) {
            return 'Invalid index definition in table {'.$xmldb_table->getName(). '}: the composed index on fields "'.implode(',', $this->getFields()).'" is too long.'
                    .' Limit is '.self::INDEX_COMPOSED_MAX_BYTES.' bytes / '.(self::INDEX_COMPOSED_MAX_BYTES/3).' chars.';
        }

        return null;
    }
}
