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
 * OCI specific temptables store. Needed because temporary tables
 * in Oracle are global (to all sessions), so we need to rename them
 * on the fly in order to get local (different for each session) table names.
 * Also used to be able to retrieve temp table names included in the get_tables()
 * method of the DB.
 *
 * @package    core
 * @subpackage dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/dml/moodle_temptables.php');

class oci_native_moodle_temptables extends moodle_temptables {

    protected $unique_session_id; // To store unique_session_id. Needed for temp tables unique naming (upto 24cc)
    protected $counter; // To get incrementally different temptable names on each add_temptable() request

    /**
     * Creates new moodle_temptables instance
     * @param object moodle_database instance
     */
    public function __construct($mdb, $unique_session_id) {
        $this->unique_session_id = $unique_session_id;
        $this->counter = 1;
        parent::__construct($mdb);
    }

    /**
     * Add one temptable to the store.
     *
     * Overriden because OCI only support global temptables, so we need to change completely the name, based
     * in unique session identifier, to get local-like temp tables support
     * tables before the prefix.
     *
     * Given one moodle temptable name (without prefix), add it to the store, with the
     * key being the original moodle name and the value being the real db temptable name
     * already prefixed
     *
     * Override and use this *only* if the database requires modification in the table name.
     *
     * @param string $tablename name without prefix of the table created as temptable
     */
    public function add_temptable($tablename) {
        // TODO: throw exception if exists: if ($this->is_temptable...
        $this->temptables[$tablename] = $this->prefix . $this->unique_session_id . $this->counter;
        $this->counter++;
    }
}
