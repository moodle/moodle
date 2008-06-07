<?php // $Id$

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

/// This class represent the base generator class where all the
/// needed functions to generate proper SQL are defined.

/// The rest of classes will inherit, by default, the same logic.
/// Functions will be overriden as needed to generate correct SQL.

class database_manager {

    protected $mdb;
    public $generator; // public because XMLDB editor needs to access it

    /**
     * Creates new database manager
     * @param object moodle_database instance
     */
    public function __construct($mdb, $generator) {
        global $CFG;

        $this->mdb       = $mdb;
        $this->generator = $generator;
    }

    /**
     * Release all resources
     */
    public function dispose() {
        if ($this->generator) {
            $this->generator->dispose();
            $this->generator = null;
        }
        $this->mdb       = null;
    }

    /**
     * This function will execute an array of SQL commands, returning
     * true/false if any error is found and stopping/continue as desired.
     * It's widely used by all the ddllib.php functions
     *
     * @param array $sqlarr array of sql statements to execute
     * @param boolean $continue to specify if must continue on error (true) or stop (false)
     * @param boolean $feedback to specify to show status info (true) or not (false)
     * @return boolean true if everything was ok, false if some error was found
     */
    protected function execute_sql_arr(array $sqlarr, $continue, $feedback=true) {
        $result = true;
        foreach ($sqlarr as $sql) {
            $result = $this->execute_sql($sql, $feedback) && $result;
            if (!$continue and !$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Execute a given sql command string - used in upgrades
     *
     * Completely general function - it just runs some SQL and reports success.
     *
     * @param string $command The sql string you wish to be executed.
     * @param bool $feedback Set this argument to true if the results generated should be printed. Default is true.
     * @return bool success
     */
    protected function execute_sql($sql, $feedback=true) {
        $result = $this->mdb->change_database_structure($sql);

        if ($feedback and !$result) {
            notify('<strong>' . get_string('error') . '</strong>');
        }

        return $result;
    }

    /**
     * Given one xmldb_table, check if it exists in DB (true/false)
     *
     * @param mixed the table to be searched (string name or xmldb_table instance)
     * @param bool temp table (might need different checks)
     * @return boolean true/false
     */
    public function table_exists($table, $temptable=false) {
        return $this->generator->table_exists($table, $temptable);
    }

    /**
     * Given one xmldb_field, check if it exists in DB (true/false)
     *
     * @param mixed the table to be searched (string name or xmldb_table instance)
     * @param mixed the field to be searched for (string name or xmldb_field instance)
     * @return boolean true/false
     */
    public function field_exists($table, $field) {
        $exists = true;

    /// Check the table exists
        if (!$this->table_exists($table)) {
            return false;
        }

    /// Do this function silenty (to avoid output in install/upgrade process)
        $olddbdebug = $this->mdb->get_debug();
        $this->mdb->set_debug(false);

        if (is_string($table)) {
            $tablename = $table;
        } else {
        /// Calculate the name of the table
            $tablename = $table->getName();
        }

        if (is_string($field)) {
            $fieldname = $field;
        } else {
        /// Calculate the name of the table
            $fieldname = $field->getName();
        }

    /// Get list of fields in table
        $columns = $this->mdb->get_columns($tablename, false);

        $exists = array_key_exists($fieldname,  $columns);

    /// Re-set original debug
        $this->mdb->set_debug($olddbdebug);

        return $exists;
    }

    /**
     * Given one xmldb_index, the function returns the name of the index in DB (if exists)
     * of false if it doesn't exist
     *
     * @param mixed the table to be searched (string name or xmldb_table instance)
     * @param xmldb_index the index to be searched
     * @return string index name of false
     */
    public function find_index_name($table, $xmldb_index) {
    /// Check the table exists
        if (!$this->table_exists($table)) {
            return false;
        }

    /// Do this function silenty (to avoid output in install/upgrade process)
        $olddbdebug = $this->mdb->get_debug();
        $this->mdb->set_debug(false);

    /// Extract index columns
        $indcolumns = $xmldb_index->getFields();

        if (is_string($table)) {
            $tablename = $table;
        } else {
        /// Calculate the name of the table
            $tablename = $table->getName();
        }

    /// Get list of indexes in table
        $indexes = $this->mdb->get_indexes($tablename);

    /// Iterate over them looking for columns coincidence
        foreach ($indexes as $indexname => $index) {
            $columns = $index['columns'];
        /// Check if index matchs queried index
            $diferences = array_merge(array_diff($columns, $indcolumns), array_diff($indcolumns, $columns));
        /// If no diferences, we have find the index
            if (empty($diferences)) {
                $this->mdb->set_debug($olddbdebug);
                return $indexname;
            }
        }

    /// Arriving here, index not found
        $this->mdb->set_debug($olddbdebug);
        return false;
    }

    /**
     * Given one xmldb_index, check if it exists in DB (true/false)
     *
     * @param mixed the table to be searched (string name or xmldb_table instance)
     * @param xmldb_index the index to be searched for
     * @return boolean true/false
     */
    public function index_exists($table, $xmldb_index) {
        return ($this->find_index_name($table, $xmldb_index) !== false);
    }

    /**
     * Given one xmldb_field, the function returns the name of the check constraint in DB (if exists)
     * of false if it doesn't exist. Note that XMLDB limits the number of check constrainst per field
     * to 1 "enum-like" constraint. So, if more than one is returned, only the first one will be
     * retrieved by this funcion.
     *
     * @uses, $db
     * @param xmldb_table the table to be searched
     * @param xmldb_field the field to be searched
     * @return string check constraint name or false
     */
    public function find_check_constraint_name($xmldb_table, $xmldb_field) {

    /// Check the table exists
        if (!$this->table_exists($xmldb_table)) {
            return false;
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            return false;
        }

    /// Do this function silenty (to avoid output in install/upgrade process)
        $olddbdebug = $this->mdb->get_debug();
        $this->mdb->set_debug(false);

    /// Get list of check_constraints in table/field
        $checks = false;
        if ($objchecks = $this->generator->getCheckConstraintsFromDB($xmldb_table, $xmldb_field)) {
        /// Get only the 1st element. Shouldn't be more than 1 under XMLDB
            $objcheck = array_shift($objchecks);
            if ($objcheck) {
                $checks = strtolower($objcheck->name);
            }
        }

    /// Arriving here, check not found
        $this->mdb->set_debug($olddbdebug);
        return $checks;
    }

    /**
     * Given one xmldb_field, check if it has a check constraint in DB
     *
     * @uses, $db
     * @param xmldb_table the table
     * @param xmldb_field the field to be searched for any existing constraint
     * @return boolean true/false
     */
    public function check_constraint_exists($xmldb_table, $xmldb_field) {
        return ($this->find_check_constraint_name($xmldb_table, $xmldb_field) !== false);
    }

    /**
     * This function IS NOT IMPLEMENTED. ONCE WE'LL BE USING RELATIONAL
     * INTEGRITY IT WILL BECOME MORE USEFUL. FOR NOW, JUST CALCULATE "OFFICIAL"
     * KEY NAMES WITHOUT ACCESSING TO DB AT ALL.
     * Given one xmldb_key, the function returns the name of the key in DB (if exists)
     * of false if it doesn't exist
     *
     * @uses, $db
     * @param xmldb_table the table to be searched
     * @param xmldb_key the key to be searched
     * @return string key name of false
     */
    public function find_key_name($xmldb_table, $xmldb_key) {

    /// Extract key columns
        if (!($xmldb_key instanceof xmldb_key)) {
            debugging("Wrong type for second parameter to database_manager::find_key_name. Should be xmldb_key, got " . gettype($xmldb_key));
            return false;
        }

        $keycolumns = $xmldb_key->getFields();

    /// Get list of keys in table
    /// first primaries (we aren't going to use this now, because the MetaPrimaryKeys is awful)
        ///TODO: To implement when we advance in relational integrity
    /// then uniques (note that Moodle, for now, shouldn't have any UNIQUE KEY for now, but unique indexes)
        ///TODO: To implement when we advance in relational integrity (note that AdoDB hasn't any MetaXXX for this.
    /// then foreign (note that Moodle, for now, shouldn't have any FOREIGN KEY for now, but indexes)
        ///TODO: To implement when we advance in relational integrity (note that AdoDB has one MetaForeignKeys()
        ///but it's far from perfect.
    /// TODO: To create the proper functions inside each generator to retrieve all the needed KEY info (name
    ///       columns, reftable and refcolumns

    /// So all we do is to return the official name of the requested key without any confirmation!)
    /// One exception, harcoded primary constraint names
        if ($this->generator->primary_key_name && $xmldb_key->getType() == XMLDB_KEY_PRIMARY) {
            return $this->generator->primary_key_name;
        } else {
        /// Calculate the name suffix
            switch ($xmldb_key->getType()) {
                case XMLDB_KEY_PRIMARY:
                    $suffix = 'pk';
                    break;
                case XMLDB_KEY_UNIQUE:
                    $suffix = 'uk';
                    break;
                case XMLDB_KEY_FOREIGN_UNIQUE:
                case XMLDB_KEY_FOREIGN:
                    $suffix = 'fk';
                    break;
            }
        /// And simply, return the oficial name
            return $this->generator->getNameForObject($xmldb_table->getName(), implode(', ', $xmldb_key->getFields()), $suffix);
        }
    }


    /**
     * Given one xmldb_table, the function returns the name of its sequence in DB (if exists)
     * of false if it doesn't exist
     *
     * @param xmldb_table the table to be searched
     * @return string sequence name of false
     */
    public function find_sequence_name($xmldb_table) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect find_sequence_name() $xmldb_table parameter');
            return false;
        }

        if (!$this->table_exists($xmldb_table)) {
            debugging('Table ' . $xmldb_table->getName() .
                      ' does not exist. Sequence not found', DEBUG_DEVELOPER);
            return false; //Table doesn't exist, nothing to do

        }

        $sequencename = false;

    /// Do this function silenty (to avoid output in install/upgrade process)
        $olddbdebug = $this->mdb->get_debug();
        $this->mdb->set_debug(false);

        $sequencename = $this->generator->getSequenceFromDB($xmldb_table);

        $this->mdb->set_debug($olddbdebug);
        return $sequencename;
    }

    /**
     * This function will delete all tables found in XMLDB file from db
     *
     * @param $file full path to the XML file to be used
     * @param $feedback
     * @return boolean (true on success, false on error)
     */
    public function delete_tables_from_xmldb_file($file, $feedback=true ) {

        $xmldb_file = new xmldb_file($file);

        if (!$xmldb_file->fileExists()) {
            return false;
        }

        $loaded    = $xmldb_file->loadXMLStructure();
        $structure = $xmldb_file->getStructure();

        if (!$loaded || !$xmldb_file->isLoaded()) {
        /// Show info about the error if we can find it
            if ($feedback and $structure) {
                if ($errors = $structure->getAllErrors()) {
                    notify('Errors found in XMLDB file: '. implode (', ', $errors));
                }
            }
            return false;
        }

        if ($xmldb_tables = $structure->getTables()) {
            foreach($xmldb_tables as $table) {
                if ($this->table_exists($table)) {
                    $this->drop_table($table, true, $feedback);
                }
            }
        }

        return true;
    }

    /**
     * This function will drop the table passed as argument
     * and all the associated objects (keys, indexes, constaints, sequences, triggers)
     * will be dropped too.
     *
     * @param xmldb_table table object (just the name is mandatory)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     * @TODO I don't think returning TRUE when trying to drop a non-existing table is a good idea.
     *       the point is, the method name is drop_table, and if it doesn't drop a table,
     *       it should be obvious to the code calling this method, and not rely on the visual
     *       feedback of debugging(). Exception handling may solve this.
     */
    public function drop_table($xmldb_table, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect drop_table() $xmldb_table parameter');
            return false;
        }

    /// Check table exists
        if (!$this->table_exists($xmldb_table)) {
            debugging('Table ' . $xmldb_table->getName() .
                      ' does not exist. Delete skipped', DEBUG_DEVELOPER);
            return true; //Table don't exist, nothing to do
        }

        if (!$sqlarr = $this->generator->getDropTableSQL($xmldb_table)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will load one entire XMLDB file, generating all the needed
     * SQL statements, specific for each RDBMS ($CFG->dbtype) and, finally, it
     * will execute all those statements against the DB.
     *
     * @param $file full path to the XML file to be used
     * @return boolean (true on success, false on error)
     */
    public function install_from_xmldb_file($file, $continue=true, $feedback=true) {
        $xmldb_file = new xmldb_file($file);

        if (!$xmldb_file->fileExists()) {
            return false;
        }

        $loaded = $xmldb_file->loadXMLStructure();
        if (!$loaded || !$xmldb_file->isLoaded()) {
        /// Show info about the error if we can find it
            if ($structure =& $xmldb_file->getStructure()) {
                if ($errors = $structure->getAllErrors()) {
                    notify('Errors found in XMLDB file: '. implode (', ', $errors));
                }
            }
            return false;
        }

        $xmldb_structure = $xmldb_file->getStructure();

        /// Do this function silenty (to avoid output in install/upgrade process)
        $olddbdebug = $this->mdb->get_debug();
        $this->mdb->set_debug(false);

        if (!$sqlarr = $this->generator->getCreateStructureSQL($xmldb_structure)) {
            return true; //Empty array = nothing to do = no error
        }

        $this->mdb->set_debug($olddbdebug);

        $result = $this->execute_sql_arr($sqlarr, $continue, $feedback);

        return $result;
    }

    /**
     * This function will create the table passed as argument with all its
     * fields/keys/indexes/sequences, everything based in the XMLDB object
     *
     * @param xmldb_table table object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     * @TODO I don't think returning TRUE when trying to create an existing table is a good idea.
     *       the point is, the method name is create_table, and if it doesn't create a table,
     *       it should be obvious to the code calling this method, and not rely on the visual
     *       feedback of debugging(). Exception handling may solve this.
     */
    public function create_table($xmldb_table, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect create_table() $xmldb_table parameter');
            return false;
        }

    /// Check table doesn't exist
        if ($this->table_exists($xmldb_table)) {
            debugging('Table ' . $xmldb_table->getName() .
                      ' already exists. Create skipped', DEBUG_DEVELOPER);
            return true; //Table exists, nothing to do
        }

        if (!$sqlarr = $this->generator->getCreateTableSQL($xmldb_table)) {
            return true; //Empty array = nothing to do = no error
        }
        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will create the temporary table passed as argument with all its
     * fields/keys/indexes/sequences, everything based in the XMLDB object
     *
     * If table already exists it will be dropped and recreated, please make sure
     * the table name does not collide with existing normal table!
     *
     * @param xmldb_table table object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return string tablename on success, false on error
     */
    public function create_temp_table($xmldb_table, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect create_table() $xmldb_table parameter');
            return false;
        }

    /// hack for mssql - it requires names to start with #
        $xmldb_table = $this->generator->tweakTempTable($xmldb_table);

    /// Check table doesn't exist
        if ($this->table_exists($xmldb_table, true)) {
            debugging('Temporary table ' . $xmldb_table->getName() .
                      ' already exists, dropping and recreating it.', DEBUG_DEVELOPER);
            if (!$this->drop_temp_table($xmldb_table, $continue, $feedback)) {
                return false;
            }
        }

        if (!$sqlarr = $this->generator->getCreateTempTableSQL($xmldb_table)) {
            return $xmldb_table->getName(); //Empty array = nothing to do = no error
        }

        if ($this->execute_sql_arr($sqlarr, $continue, $feedback)) {
            return $xmldb_table->getName();
        } else {
            return false;
        }
    }

    /**
     * This function will drop the temporary table passed as argument with all its
     * fields/keys/indexes/sequences, everything based in the XMLDB object
     *
     * It is recommended to drop temp table when not used anymore.
     *
     * @param xmldb_table table object
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return string tablename on success, false on error
     */
    public function drop_temp_table($xmldb_table, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect create_table() $xmldb_table parameter');
            return false;
        }

    /// mssql requires names to start with #
        $xmldb_table = $this->generator->tweakTempTable($xmldb_table);

    /// Check table doesn't exist
        if (!$this->table_exists($xmldb_table, true)) {
            return true;
        }

        if (!$sqlarr = $this->generator->getDropTempTableSQL($xmldb_table)) {
            return false; // error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will rename the table passed as argument
     * Before renaming the index, the function will check it exists
     *
     * @param xmldb_table table object (just the name is mandatory)
     * @param string new name of the index
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function rename_table($xmldb_table, $newname, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect rename_table() $xmldb_table parameter');
            return false;
        }

    /// Check newname isn't empty
        if (!$newname) {
            debugging('Error: new name for table ' . $xmldb_table->getName() .
                      ' is empty!');
            return false; //error!
        }

        $check = new xmldb_table($newname);

    /// Check table already renamed
        if (!$this->table_exists($xmldb_table) and $this->table_exists($check)) {
            debugging('Table ' . $xmldb_table->getName() .
                      ' already renamed. Rename skipped', DEBUG_DEVELOPER);
            return true; //ok fine
        }

    /// Check table exists
        if (!$this->table_exists($xmldb_table)) {
            debugging('Table ' . $xmldb_table->getName() .
                      ' does not exist. Rename skipped');
            return false; //error!
        }

    /// Check new table doesn't exist
        if ($this->table_exists($check)) {
            debugging('Table ' . $check->getName() .
                      ' already exists. Rename skipped');
            return false; //error!
        }

        if (!$sqlarr = $this->generator->getRenameTableSQL($xmldb_table, $newname)) {
            return true; //Empty array = nothing to do = no error (this is weird!)
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }


    /**
     * This function will add the field to the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function add_field($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect add_field() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect add_field() $xmldb_field parameter');
            return false;
        }
     /// Check the field doesn't exist
        if ($this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' already exists. Create skipped', DEBUG_DEVELOPER);
            return true;
        }

    /// If NOT NULL and no default given (we ask the generator about the
    /// *real* default that will be used) check the table is empty
        if ($xmldb_field->getNotNull() && $this->generator->getDefaultValue($xmldb_field) === NULL && $this->mdb->count_records($xmldb_table->getName())) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' cannot be added. Not null fields added to non empty tables require default value. Create skipped', DEBUG_DEVELOPER);
             return false; //error!!
        }

        if (!$sqlarr = $this->generator->getAddFieldSQL($xmldb_table, $xmldb_field)) {
            debugging('Error: No sql code for field adding found');
            return false;
        }
        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will drop the field from the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (just the name is mandatory)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function drop_field($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect drop_field() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect drop_field() $xmldb_field parameter');
            return false;
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Delete skipped', DEBUG_DEVELOPER);
            return true;
        }

        if (!$sqlarr = $this->generator->getDropFieldSQL($xmldb_table, $xmldb_field)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will change the type of the field in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_type($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect change_field_type() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect change_field_type() $xmldb_field parameter');
            return false;
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Change type skipped');
            return false;
        }

        if (!$sqlarr = $this->generator->getAlterFieldSQL($xmldb_table, $xmldb_field)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will change the precision of the field in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_precision($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {

    /// Just a wrapper over change_field_type. Does exactly the same processing
        return $this->change_field_type($xmldb_table, $xmldb_field, $continue, $feedback);
    }

    /**
     * This function will change the unsigned/signed of the field in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_unsigned($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {

    /// Just a wrapper over change_field_type. Does exactly the same processing
        return $this->change_field_type($xmldb_table, $xmldb_field, $continue, $feedback);
    }

    /**
     * This function will change the nullability of the field in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_notnull($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {

    /// Just a wrapper over change_field_type. Does exactly the same processing
        return $this->change_field_type($xmldb_table, $xmldb_field, $continue, $feedback);
    }

    /**
     * This function will change the enum status of the field in the table passed as arguments
     *
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_enum($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect change_field_enum() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect change_field_enum() $xmldb_field parameter');
            return false;
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Change type skipped');
            return false;
        }

    /// If enum is defined, we're going to create it, check it doesn't exist.
        if ($xmldb_field->getEnum()) {
            if ($this->check_constraint_exists($xmldb_table, $xmldb_field)) {
                debugging('Enum for ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                          ' already exists. Create skipped', DEBUG_DEVELOPER);
                return true; //Enum exists, nothing to do
            }
        } else { /// Else, we're going to drop it, check it exists
            if (!$this->check_constraint_exists($xmldb_table, $xmldb_field)) {
                debugging('Enum for ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                          ' does not exist. Delete skipped', DEBUG_DEVELOPER);
                return true; //Enum does not exist, nothing to delete
            }
        }

        if (!$sqlarr = $this->generator->getModifyEnumSQL($xmldb_table, $xmldb_field)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will change the default of the field in the table passed as arguments
     * One null value in the default field means delete the default
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field field object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function change_field_default($xmldb_table, $xmldb_field, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect change_field_default() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect change_field_default() $xmldb_field parameter');
            return false;
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Change type skipped');
            return false;
        }

        if (!$sqlarr = $this->generator->getModifyDefaultSQL($xmldb_table, $xmldb_field)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will rename the field in the table passed as arguments
     * Before renaming the field, the function will check it exists
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_field index object (full specs are required)
     * @param string new name of the field
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function rename_field($xmldb_table, $xmldb_field, $newname, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect rename_field() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_field instanceof xmldb_field)) {
            debugging('Incorrect rename_field() $xmldb_field parameter');
            return false;
        }

        if (empty($newname)) {
            debugging('New name for field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' is empty! Rename skipped', DEBUG_DEVELOPER);
            return false; //error
        }

    /// Check the field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Change type skipped');
            return false;
        }

    /// Check we have included full field specs
        if (!$xmldb_field->getType()) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' must contain full specs. Rename skipped', DEBUG_DEVELOPER);
            return false;
        }

    /// Check field isn't id. Renaming over that field is not allowed
        if ($xmldb_field->getName() == 'id') {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' cannot be renamed. Rename skipped', DEBUG_DEVELOPER);
            return false; //Field is "id", nothing to do
        }

    /// Check field exists
        if (!$this->field_exists($xmldb_table, $xmldb_field)) {
            debugging('Field ' . $xmldb_table->getName() . '->' . $xmldb_field->getName() .
                      ' does not exist. Rename skipped', DEBUG_DEVELOPER);
            $newfield = clone($xmldb_field);
            $newfield->setName($newname);
            if ($this->field_exists($xmldb_table, $newfield)) {
                return true; //ok
            } else {
                return false; //error
            }
        }

        if (!$sqlarr = $this->generator->getRenameFieldSQL($xmldb_table, $xmldb_field, $newname)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will create the key in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_key index object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function add_key($xmldb_table, $xmldb_key, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect add_key() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_key instanceof xmldb_key)) {
            debugging('Incorrect add_key() $xmldb_key parameter');
            return false;
        }

        if ($xmldb_key->getType() == XMLDB_KEY_PRIMARY) { // Prevent PRIMARY to be added (only in create table, being serious  :-P)
            debugging('Primary Keys can be added at table create time only', DEBUG_DEVELOPER);
            return true;
        }

        if (!$sqlarr = $this->generator->getAddKeySQL($xmldb_table, $xmldb_key)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will drop the key in the table passed as arguments
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_key key object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function drop_key($xmldb_table, $xmldb_key, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect drop_key() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_key instanceof xmldb_key)) {
            debugging('Incorrect drop_key() $xmldb_key parameter');
            return false;
        }

        if ($xmldb_key->getType() == XMLDB_KEY_PRIMARY) { // Prevent PRIMARY to be dropped (only in drop table, being serious  :-P)
            debugging('Primary Keys can be deleted at table drop time only', DEBUG_DEVELOPER);
            return true;
        }

        if(!$sqlarr = $this->generator->getDropKeySQL($xmldb_table, $xmldb_key)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will rename the key in the table passed as arguments
     * Experimental. Shouldn't be used at all in normal installation/upgrade!
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_key key object (full specs are required)
     * @param string new name of the key
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function rename_key($xmldb_table, $xmldb_key, $newname, $continue=true, $feedback=true) {
        debugging('rename_key() is one experimental feature. You must not use it in production!', DEBUG_DEVELOPER);

        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect rename_key() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_key instanceof xmldb_key)) {
            debugging('Incorrect rename_key() $xmldb_key parameter');
            return false;
        }

    /// Check newname isn't empty
        if (!$newname) {
            debugging('New name for key ' . $xmldb_table->getName() . '->' . $xmldb_key->getName() .
                      ' is empty! Rename skipped', DEBUG_DEVELOPER);
            return true; //Key doesn't exist, nothing to do
        }

        if (!$sqlarr = $this->generator->getRenameKeySQL($xmldb_table, $xmldb_key, $newname)) {
            debugging('Some DBs do not support key renaming (MySQL, PostgreSQL, MsSQL). Rename skipped', DEBUG_DEVELOPER);
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will create the index in the table passed as arguments
     * Before creating the index, the function will check it doesn't exists
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_index index object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function add_index($xmldb_table, $xmldb_intex, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect add_index() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_intex instanceof xmldb_index)) {
            debugging('Incorrect add_index() $xmldb_index parameter');
            return false;
        }

    /// Check index doesn't exist
        if ($this->index_exists($xmldb_table, $xmldb_intex)) {
            debugging('Index ' . $xmldb_table->getName() . '->' . $xmldb_intex->getName() .
                      ' already exists. Create skipped', DEBUG_DEVELOPER);
            return true; //Index exists, nothing to do
        }

        if (!$sqlarr = $this->generator->getAddIndexSQL($xmldb_table, $xmldb_intex)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will drop the index in the table passed as arguments
     * Before dropping the index, the function will check it exists
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_index index object (full specs are required)
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function drop_index($xmldb_table, $xmldb_intex, $continue=true, $feedback=true) {
        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect add_index() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_intex instanceof xmldb_index)) {
            debugging('Incorrect add_index() $xmldb_index parameter');
            return false;
        }

    /// Check index exists
        if (!$this->index_exists($xmldb_table, $xmldb_intex)) {
            debugging('Index ' . $xmldb_table->getName() . '->' . $xmldb_intex->getName() .
                      ' does not exist. Delete skipped', DEBUG_DEVELOPER);
            return true; //Index doesn't exist, nothing to do
        }

        if (!$sqlarr = $this->generator->getDropIndexSQL($xmldb_table, $xmldb_intex)) {
            return true; //Empty array = nothing to do = no error
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }

    /**
     * This function will rename the index in the table passed as arguments
     * Before renaming the index, the function will check it exists
     * Experimental. Shouldn't be used at all!
     *
     * @uses $CFG, $db
     * @param xmldb_table table object (just the name is mandatory)
     * @param xmldb_index index object (full specs are required)
     * @param string new name of the index
     * @param boolean continue to specify if must continue on error (true) or stop (false)
     * @param boolean feedback to specify to show status info (true) or not (false)
     * @return boolean true on success, false on error
     */
    public function rename_index($xmldb_table, $xmldb_intex, $newname, $continue=true, $feedback=true) {
        debugging('rename_index() is one experimental feature. You must not use it in production!', DEBUG_DEVELOPER);

        if (!($xmldb_table instanceof xmldb_table)) {
            debugging('Incorrect add_index() $xmldb_table parameter');
            return false;
        }

        if (!($xmldb_intex instanceof xmldb_index)) {
            debugging('Incorrect add_index() $xmldb_index parameter');
            return false;
        }

    /// Check newname isn't empty
        if (!$newname) {
            debugging('New name for index ' . $xmldb_table->getName() . '->' . $xmldb_intex->getName() .
                      ' is empty! Rename skipped', DEBUG_DEVELOPER);
            return true; //Index doesn't exist, nothing to do
        }

    /// Check index exists
        if (!$this->index_exists($xmldb_table, $xmldb_intex)) {
            debugging('Index ' . $xmldb_table->getName() . '->' . $xmldb_intex->getName() .
                      ' does not exist. Rename skipped', DEBUG_DEVELOPER);
            return true; //Index doesn't exist, nothing to do
        }

        if (!$sqlarr = $this->generator->getRenameIndexSQL($xmldb_table, $xmldb_intex, $newname)) {
            debugging('Some DBs do not support index renaming (MySQL). Rename skipped', DEBUG_DEVELOPER);
            return false; // Error - index not renamed
        }

        return $this->execute_sql_arr($sqlarr, $continue, $feedback);
    }
}

?>
