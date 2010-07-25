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
 * @package    core
 * @subpackage dml
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/dml/moodle_recordset.php');

/**
 * Experimental pdo recordset
 */
class pdo_moodle_recordset extends moodle_recordset {

    private $sth;
    protected $current;

    public function __construct($sth) {
        $this->sth = $sth;
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if ($row = $this->sth->fetch()) {
            $row = array_change_key_case($row, CASE_LOWER);
        }
        return $row;
    }

    public function current() {
        return (object)$this->current;
    }

    public function key() {
    /// return first column value as key
        if (!$this->current) {
            return false;
        }
        $key = reset($this->current);
        return $key;
    }

    public function next() {
        $this->current = $this->fetch_next();
    }

    public function valid() {
        return !empty($this->current);
    }

    public function close() {
        if ($this->sth) {
            $this->sth->closeCursor();
            $this->sth = null;
        }
        $this->current = null;
    }
}
