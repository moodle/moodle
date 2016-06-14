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
 * @package    moodlecore
 * @subpackage backup-logger
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Logger implementation that sends messages to database
 *
 * TODO: Finish phpdocs
 */
class database_logger extends base_logger {

    protected $datecol;    // Name of the field where the timestamp will be stored
    protected $levelcol;   // Name of the field where the level of the message will be stored
    protected $messagecol; // Name of the field where the message will be stored
    protected $logtable;   // Table, without prefix where information must be logged
    protected $columns;    // Array of columns and values to set in all actions logged

// Protected API starts here

    public function __construct($level, $datecol = false, $levelcol = false, $messagecol = null, $logtable = null, $columns = null) {
        // TODO check $datecol exists
        // TODO check $levelcol exists
        // TODO check $logtable exists
        // TODO check $messagecol exists
        // TODO check all $columns exist
        $this->datecol    = $datecol;
        $this->levelcol   = $levelcol;
        $this->messagecol = $messagecol;
        $this->logtable   = $logtable;
        $this->columns    = $columns;
        parent::__construct($level, (bool)$datecol, (bool)$levelcol);
    }

    protected function action($message, $level, $options = null) {
        $columns = $this->columns;
        if ($this->datecol) {
            $columns[$this->datecol] = time();
        }
        if ($this->levelcol) {
            $columns[$this->levelcol] = $level;
        }
        $columns[$this->messagecol] = clean_param($message, PARAM_NOTAGS);
        return $this->insert_log_record($this->logtable, $columns);
    }

    protected function insert_log_record($table, $columns) {
        // TODO: Allow to use an alternate connection (created in constructor)
        // based in some CFG->backup_database_logger_newconn = true in order
        // to preserve DB logs if the whole backup/restore transaction is
        // rollback
        global $DB;
        return $DB->insert_record($table, $columns, false); // Don't return inserted id
    }
}
