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
 * Tool for merging users in a database table.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://wwww.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger;
/**
 * This interface introduces the concept of table merger: a tool for merging
 * records of the given users to merge.
 *
 * The lifecycle will be:
 *
 * 1. user_merger will call to table_merger.get_tables_to_skip(), to get the list
 *    of tables not to be processed by others table_mergers. This step is done
 *    only once in the configuration phase of the user_merger.
 * 2. user_merger calls to the table_merger.merge() to actually merge records of
 *    the given $tablename. This call will update when necessary the list of
 *    errors (on $errorMessages) and the list of actions performed (on
 *    $actionLog).
 *
 * The goal of the table_merger.get_tables_to_skip() is to exclude from processing
 * specific tables under the control of the current table_merger.
 *
 * Example: Suppose that this table_merger is related to table1, and also
 * to table1_aux, having a direct relation between them. Suppose that
 * this relation makes that actions done in table1 makes certain changes on
 * table1_aux. Therefore, this table_merger can be implemented as follows:
 *
 * a. get_tables_to_skip() returns an array('table1_aux') to exclude that table from being processed
 *    by any other table_merger.
 * b. Invoking merge('table1',...) processes both tables, table1 and
 *    table1_aux according to some specific rules.
 *
 */
interface table_merger {
    /** @var string the field with name 'id' is the only considered as primary key on Moodle */
    const PRIMARY_KEY = 'id';

    /**
     * The given table_merger can assist the merging of the users in
     * a table, but affecting to multiple tables. If so, return an
     * array with the list of table names to skip.
     *
     * @return array List of database table names without the $CFG->prefix.
     * Returns an empty array when nothing to do.
     */
    public function get_tables_to_skip(): array;

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $logs list of action performed.
     * @param array $errors list of error messages.
     * @return void
     */
    public function merge(array $data, array &$logs, array &$errors): void;
}
