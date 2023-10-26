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
 * This class represent one XMLDB structure
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class xmldb_structure extends xmldb_object {

    /** @var string */
    protected $path;

    /** @var string */
    protected $version;

    /** @var array tables */
    protected $tables;

    /**
     * Creates one new xmldb_structure
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct($name);
        $this->path = null;
        $this->version = null;
        $this->tables = array();
    }

    /**
     * Returns the path of the structure
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the version of the structure
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Returns one xmldb_table
     * @param string $tablename
     * @return xmldb_table
     */
    public function getTable($tablename) {
        $i = $this->findTableInArray($tablename);
        if ($i !== null) {
            return $this->tables[$i];
        }
        return null;
    }

    /**
     * Returns the position of one table in the array.
     * @param string $tablename
     * @return mixed
     */
    public function findTableInArray($tablename) {
        foreach ($this->tables as $i => $table) {
            if ($tablename == $table->getName()) {
                return $i;
            }
        }
        return null;
    }

    /**
     * This function will reorder the array of tables
     * @return bool success
     */
    public function orderTables() {
        $result = $this->orderElements($this->tables);
        if ($result) {
            $this->setTables($result);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the tables of the structure
     * @return array
     */
    public function getTables() {
        return $this->tables;
    }

    /**
     * Set the structure version
     * @param string version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * Add one table to the structure, allowing to specify the desired order
     * If it's not specified, then the table is added at the end.
     * @param xmldb_table $table
     * @param mixed $after
     */
    public function addTable($table, $after=null) {

        // Calculate the previous and next tables
        $prevtable = null;
        $nexttable = null;

        if (!$after) {
            if ($this->tables) {
                end($this->tables);
                $prevtable = $this->tables[key($this->tables)];
            }
        } else {
            $prevtable = $this->getTable($after);
        }
        if ($prevtable && $prevtable->getNext()) {
            $nexttable = $this->getTable($prevtable->getNext());
        }

        // Set current table previous and next attributes
        if ($prevtable) {
            $table->setPrevious($prevtable->getName());
            $prevtable->setNext($table->getName());
        }
        if ($nexttable) {
            $table->setNext($nexttable->getName());
            $nexttable->setPrevious($table->getName());
        }
        // Some more attributes
        $table->setLoaded(true);
        $table->setChanged(true);
        // Add the new table
        $this->tables[] = $table;
        // Reorder the whole structure
        $this->orderTables($this->tables);
        // Recalculate the hash
        $this->calculateHash(true);
        // We have one new table, so the structure has changed
        $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
        $this->setChanged(true);
    }

    /**
     * Delete one table from the Structure
     * @param string $tablename
     */
    public function deleteTable($tablename) {

        $table = $this->getTable($tablename);
        if ($table) {
            $i = $this->findTableInArray($tablename);
            // Look for prev and next table
            $prevtable = $this->getTable($table->getPrevious());
            $nexttable = $this->getTable($table->getNext());
            // Change their previous and next attributes
            if ($prevtable) {
                $prevtable->setNext($table->getNext());
            }
            if ($nexttable) {
                $nexttable->setPrevious($table->getPrevious());
            }
            // Delete the table
            unset($this->tables[$i]);
            // Reorder the tables
            $this->orderTables($this->tables);
            // Recalculate the hash
            $this->calculateHash(true);
            // We have one deleted table, so the structure has changed
            $this->setVersion(userdate(time(), '%Y%m%d', 99, false));
            $this->setChanged(true);
        }
    }

    /**
     * Set the tables
     * @param array $tables
     */
    public function setTables($tables) {
        $this->tables = $tables;
    }

    /**
     * Load data from XML to the structure
     * @param array $xmlarr
     * @return bool
     */
    public function arr2xmldb_structure($xmlarr) {

        global $CFG;

        $result = true;

        // Debug the structure
        // traverse_xmlize($xmlarr);                   //Debug
        // print_object ($GLOBALS['traverse_array']);  //Debug
        // $GLOBALS['traverse_array']="";              //Debug

        // Process structure attributes (path, comment and version)
        if (isset($xmlarr['XMLDB']['@']['PATH'])) {
            $this->path = trim($xmlarr['XMLDB']['@']['PATH']);
        } else {
            $this->errormsg = 'Missing PATH attribute';
            $this->debug($this->errormsg);
            $result = false;
        }
        // Normalize paths to compare them.
        $filepath = realpath($this->name); // File path comes in name.
        $filename = basename($filepath);
        $normalisedpath = $this->path;
        if ($CFG->admin !== 'admin') {
            $needle = 'admin/';
            if (strpos($this->path, $needle) === 0) {
                $normalisedpath = substr_replace($this->path, "$CFG->admin/", 0, strlen($needle));
            }
        }
        $structurepath = realpath($CFG->dirroot . DIRECTORY_SEPARATOR . $normalisedpath . DIRECTORY_SEPARATOR . $filename);
        if ($filepath !== $structurepath) {
            $relativepath = dirname(str_replace(realpath($CFG->dirroot) . DIRECTORY_SEPARATOR, '', $filepath));
            $this->errormsg = 'PATH attribute does not match file directory: ' . $relativepath;
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

        // Iterate over tables
        if (isset($xmlarr['XMLDB']['#']['TABLES']['0']['#']['TABLE'])) {
            foreach ($xmlarr['XMLDB']['#']['TABLES']['0']['#']['TABLE'] as $xmltable) {
                if (!$result) { //Skip on error
                    continue;
                }
                $name = trim($xmltable['@']['NAME']);
                $table = new xmldb_table($name);
                $table->arr2xmldb_table($xmltable);
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

        // Perform some general checks over tables
        if ($result && $this->tables) {
            // Check tables names are ok (lowercase, a-z _-)
            if (!$this->checkNameValues($this->tables)) {
                $this->errormsg = 'Some TABLES name values are incorrect';
                $this->debug($this->errormsg);
                $result = false;
            }
            // Compute prev/next.
            $this->fixPrevNext($this->tables);
            // Order tables
            if ($result && !$this->orderTables($this->tables)) {
                $this->errormsg = 'Error ordering the tables';
                $this->debug($this->errormsg);
                $result = false;
            }
        }

        // Set some attributes
        if ($result) {
            $this->loaded = true;
        }
        $this->calculateHash();
        return $result;
    }

    /**
     * This function calculate and set the hash of one xmldb_structure
     * @param bool $recursive
     */
     public function calculateHash($recursive = false) {
        if (!$this->loaded) {
            $this->hash = null;
        } else {
            $key = $this->name . $this->path . $this->comment;
            if ($this->tables) {
                foreach ($this->tables as $tbl) {
                    $table = $this->getTable($tbl->getName());
                    if ($recursive) {
                        $table->calculateHash($recursive);
                    }
                    $key .= $table->getHash();
                }
            }
            $this->hash = md5($key);
        }
    }

    /**
     * This function will output the XML text for one structure
     * @return string
     */
    public function xmlOutput() {
        $o = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $o.= '<XMLDB PATH="' . $this->path . '"';
        $o.= ' VERSION="' . $this->version . '"';
        if ($this->comment) {
            $o.= ' COMMENT="' . htmlspecialchars($this->comment, ENT_COMPAT) . '"'."\n";
        }
        $rel = array_fill(0, count(explode('/', $this->path)), '..');
        $rel = implode('/', $rel);
        $o.= '    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n";
        $o.= '    xsi:noNamespaceSchemaLocation="'.$rel.'/lib/xmldb/xmldb.xsd"'."\n";
        $o.= '>' . "\n";
        // Now the tables
        if ($this->tables) {
            $o.= '  <TABLES>' . "\n";
            foreach ($this->tables as $table) {
                $o.= $table->xmlOutput();
            }
            $o.= '  </TABLES>' . "\n";
        }
        $o.= '</XMLDB>' . "\n";

        return $o;
    }

    /**
     * This function returns the number of uses of one table inside
     * a whole XMLDStructure. Useful to detect if the table must be
     * locked. Return false if no uses are found.
     * @param string $tablename
     * @return mixed
     */
    public function getTableUses($tablename) {

        $uses = array();

        // Check if some foreign key in the whole structure is using it
        // (by comparing the reftable with the tablename)
        if ($this->tables) {
            foreach ($this->tables as $table) {
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

        // Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one field inside
     * a whole xmldb_structure. Useful to detect if the field must be
     * locked. Return false if no uses are found.
     * @param string $tablename
     * @param string $fieldname
     * @return mixed
     */
    public function getFieldUses($tablename, $fieldname) {

        $uses = array();

        // Check if any key in the table is using it
        $table = $this->getTable($tablename);
        if ($keys = $table->getKeys()) {
            foreach ($keys as $key) {
                if (in_array($fieldname, $key->getFields()) ||
                    in_array($fieldname, $key->getRefFields())) {
                        $uses[] = 'table ' . $table->getName() . ' key ' . $key->getName();
                }
            }
        }
        // Check if any index in the table is using it
        $table = $this->getTable($tablename);
        if ($indexes = $table->getIndexes()) {
            foreach ($indexes as $index) {
                if (in_array($fieldname, $index->getFields())) {
                    $uses[] = 'table ' . $table->getName() . ' index ' . $index->getName();
                }
            }
        }
        // Check if some foreign key in the whole structure is using it
        // By comparing the reftable and refields with the field)
        if ($this->tables) {
            foreach ($this->tables as $table) {
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

        // Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one key inside
     * a whole xmldb_structure. Useful to detect if the key must be
     * locked. Return false if no uses are found.
     * @param string $tablename
     * @param string $keyname
     * @return mixed
     */
    public function getKeyUses($tablename, $keyname) {

        $uses = array();

        // Check if some foreign key in the whole structure is using it
        // (by comparing the reftable and reffields with the fields in the key)
        $mytable = $this->getTable($tablename);
        $mykey = $mytable->getKey($keyname);
        if ($this->tables && $mykey) {
            foreach ($this->tables as $table) {
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

        // Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function returns the number of uses of one index inside
     * a whole xmldb_structure. Useful to detect if the index must be
     * locked. Return false if no uses are found.
     * @param string $tablename
     * @param string $indexname
     * @return mixed
     */
    public function getIndexUses($tablename, $indexname) {

        $uses = array();

        // Nothing to check, because indexes haven't uses! Leave it here
        // for future checks...

        // Return result
        if (!empty($uses)) {
            return $uses;
        } else {
            return false;
        }
    }

    /**
     * This function will return all the errors found in one structure
     * looking recursively inside each table. Returns
     * an array of errors or false
     * @return mixed
     */
    public function getAllErrors() {

        $errors = array();
        // First the structure itself
        if ($this->getError()) {
            $errors[] = $this->getError();
        }
        // Delegate to tables
        if ($this->tables) {
            foreach ($this->tables as $table) {
                if ($tableerrors = $table->getAllErrors()) {

                }
            }
            // Add them to the errors array
            if ($tableerrors) {
                $errors = array_merge($errors, $tableerrors);
            }
        }
        // Return decision
        if (count($errors)) {
            return $errors;
        } else {
            return false;
        }
    }
}
