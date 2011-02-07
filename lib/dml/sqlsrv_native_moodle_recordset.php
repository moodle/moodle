<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
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
 * sqlsrv specific recorset.
 *
 * @package    core
 * @subpackage dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/dml/moodle_recordset.php');

class sqlsrv_native_moodle_recordset extends moodle_recordset {

    protected $rsrc;
    protected $current;

    public function __construct($rsrc) {
        $this->rsrc  = $rsrc;
        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if ($row = sqlsrv_fetch_array($this->rsrc, SQLSRV_FETCH_ASSOC)) {
            unset($row['sqlsrvrownumber']);
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
        if ($this->rsrc) {
            sqlsrv_free_stmt($this->rsrc);
            $this->rsrc  = null;
        }
        $this->current = null;
    }
}
