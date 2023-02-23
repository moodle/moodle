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
 * Abstract recordset.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract class for resultsets returned from database functions.
 * This is a simple Iterator with needed recorset closing support.
 *
 * The difference from old recorset is that the records are returned
 * as objects, not arrays. You should use "foreach ($recordset as $record) {}"
 * followed by "$recordset->close()".
 *
 * Do not forget to close all recordsets when they are not needed anymore!
 */
abstract class moodle_recordset implements Iterator {

    /**
     * Returns current record - fields as object properties, lowercase
     * @return object
     */
    //public abstract function current();

    /**
     * Returns the key of current row
     * @return int current row
     */
    //public abstract function key();

    /**
     * Moves forward to next row
     * @return void
     */
    //public abstract function next();

    /**
     * Rewinds are not supported!
     * @return void
     */
    public function rewind(): void {
        // no seeking, sorry - let's ignore it ;-)
        return;
    }

    /**
     * Did we reach the end?
     * @return boolean
     */
    //public abstract function valid();

    /**
     * Free resources and connections, recordset can not be used anymore.
     * @return void
     */
    public abstract function close();
}
