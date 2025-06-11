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
 * Helpers and methods relating to DML tables.
 *
 * @since      Moodle 3.7
 * @package    core
 * @category   dml
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\dml;

use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Helpers and methods relating to DML tables.
 *
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table {

    /** @var string Name of the table that this class represents */
    protected $tablename;

    /** @var string Table alias */
    protected $tablealias;

    /** @var string Prefix to place before each field */
    protected $fieldprefix;

    /** @var array List of fields */
    protected $fields;

    /**
     * Constructor for the table class.
     *
     * @param   string  $tablename The name of the table that this instance represents.
     * @param   string  $tablealias The alias to use when selecting the table
     * @param   string  $fieldprefix The prefix to use when selecting fields.
     */
    public function __construct(string $tablename, string $tablealias, string $fieldprefix) {
        $this->tablename = $tablename;
        $this->tablealias = $tablealias;
        $this->fieldprefix = $fieldprefix;
    }

    /**
     * Get the from TABLE ALIAS part of the FROM/JOIN string.
     *
     * @return  string
     */
    public function get_from_sql(): string {
        return "{{$this->tablename}} {$this->tablealias}";
    }

    /**
     * Get the list of fields in a table for use in preloading fields.
     *
     * @return  array       The list of columns in a table. The array key is the column name with an applied prefix.
     */
    protected function get_fieldlist(): array {
        global $DB;

        if (null === $this->fields) {
            $fields = [];
            foreach (array_keys($DB->get_columns($this->tablename)) as $fieldname) {
                $fields["{$this->fieldprefix}{$fieldname}"] = $fieldname;
            }

            $this->fields = $fields;
        }

        return $this->fields;
    }

    /**
     * Get the SELECT SQL to select a set of columns for this table.
     *
     * This function is intended to be used in combination with extract_from_result().
     *
     * @return  string      The SQL to use in the SELECT
     */
    public function get_field_select(): string {
        $fieldlist = $this->get_fieldlist();

        return implode(', ', array_map(function($fieldname, $fieldalias) {
            return "{$this->tablealias}.{$fieldname} AS {$fieldalias}";
        }, $fieldlist, array_keys($fieldlist)));
    }

    /**
     * Extract fields from the specified result. The fields are removed from the original object.
     *
     * This function is intended to be used in combination with get_field_select().
     *
     * @param   stdClass    $result The result retrieved from the database with fields to be extracted
     * @return  stdClass    The extracted result
     */
    public function extract_from_result(stdClass $result): stdClass {
        $record = new stdClass();

        $fieldlist = $this->get_fieldlist();
        foreach ($fieldlist as $fieldalias => $fieldname) {
            if (property_exists($result, $fieldalias)) {
                $record->$fieldname = $result->$fieldalias;
                unset($result->$fieldalias);
            } else {
                debugging("Field '{$fieldname}' not found", DEBUG_DEVELOPER);
            }
        }

        return $record;
    }
}
