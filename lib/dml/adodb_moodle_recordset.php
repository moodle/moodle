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
 * General adodb recordset.
 *
 * TODO: delete before branching 2.0
 *
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/dml/moodle_recordset.php');

/**
 * Adodb basic moodle recordset class
 */
class adodb_moodle_recordset extends moodle_recordset {

    protected $rs; ///ADOdb recordset

    public function __construct($rs) {
        $this->rs = $rs;
    }

    public function current() {
        return (object)$this->rs->fields;
    }

    public function key() {
    /// return first column value as key
        return reset($this->rs->fields);
    }

    public function next() {
        $this->rs->MoveNext();
    }

    public function valid() {
        return !$this->rs->EOF;
    }

    public function close() {
        $this->rs->Close();
        $this->rs = null;
    }
}
