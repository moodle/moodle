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
 * Experimental pdo recordset
 *
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/dml/moodle_recordset.php');

/**
 * Experimental pdo recordset
 */
class pdo_moodle_recordset extends moodle_recordset {

    private $sth;
    protected $fields;
    protected $rowCount = -1;

    public function __construct($sth) {
        $this->sth = $sth;
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
    }

    public function current() {
        return (object)$this->fields;
    }

    public function key() {
    /// return first column value as key
        return reset($this->fields);
    }

    public function next() {
        $this->fields = $this->sth->fetch();
        if ($this->fields) {
            ++$this->rowCount;
        }
        return $this->fields !== false;
    }

    public function valid() {
        if($this->rowCount < 0) {
            $this->rewind();
        }
        return $this->fields !== FALSE;
    }

    public function close() {
        $this->sth->closeCursor();
        $this->sth = null;
    }
}
