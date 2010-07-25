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
 * Delegated database transaction support.
 *
 * @package    core
 * @subpackage dml
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Delegated transaction class.
 */
class moodle_transaction {
    private $start_backtrace;
    private $database = null;

    /**
     * Delegated transaction constructor,
     * can be called only from moodle_database class.
     * Unfortunately PHP's protected keyword is useless.
     * @param moodle_database $database
     */
    public function __construct($database) {
        $this->database = $database;
        $this->start_backtrace = debug_backtrace();
        array_shift($this->start_backtrace);
    }

    /**
     * Returns backtrace of the code starting exception.
     * @return array
     */
    public function get_backtrace() {
        return $this->start_backtrace;
    }

    /**
     * Is the delegated transaction already used?
     * @return bool true if commit and rollback allowed, false if already done
     */
    public function is_disposed() {
        return empty($this->database);
    }

    /**
     * Mark transaction as disposed, no more
     * commits and rollbacks allowed.
     * To be used only from moodle_database class
     * @return unknown_type
     */
    public function dispose() {
        return $this->database = null;
    }

    /**
     * Commit delegated transaction.
     * The real database commit SQL is executed
     * only after committing all delegated transactions.
     *
     * Incorrect order of nested commits or rollback
     * at any level is resulting in rollback of SQL transaction.
     *
     * @return void
     */
    public function allow_commit() {
        if ($this->is_disposed()) {
            throw new dml_transaction_exception('Transactions already disposed', $this);
        }
        $this->database->commit_delegated_transaction($this);
    }

    /**
     * Rollback all current delegated transactions.
     *
     * @param Exception $e mandatory exception
     * @return void
     */
    public function rollback(Exception $e) {
        if ($this->is_disposed()) {
            throw new dml_transaction_exception('Transactions already disposed', $this);
        }
        $this->database->rollback_delegated_transaction($this, $e);
    }
}