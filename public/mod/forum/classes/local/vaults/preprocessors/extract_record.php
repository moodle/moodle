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
 * Extract record vault preprocessor.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults\preprocessors;

defined('MOODLE_INTERNAL') || die();

use moodle_database;
use core\dml\table as dml_table;

/**
 * Extract record vault preprocessor.
 *
 * Extract record vault preprocessor. Typically used to separate out records
 * when two tables have been joined together in a query.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extract_record {
    /** @var \core\dml\table $table The table object relating to the table that the records were loaded from */
    private $table;

    /**
     * Constructor.
     *
     * @param string $table The table name where the records were loaded from
     * @param string $alias The table alias used as the record property prefix
     */
    public function __construct(string $table, string $alias) {
        $this->table = new dml_table($table, $alias, $alias);
    }

    /**
     * Extract a record embedded in the properties of another record out into a
     * separate record.
     *
     * @param stdClass[] $records The list of records to process
     * @return stdClass[] The extracted records
     */
    public function execute(array $records): array {
        return array_map(function($record) {
            return $this->table->extract_from_result($record);
        }, $records);
    }
}
