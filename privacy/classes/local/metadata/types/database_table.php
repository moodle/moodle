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
 * This file defines an item of metadata which encapsulates a database table.
 *
 * @package core_privacy
 * @copyright 2018 Zig Tan <zig@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\metadata\types;

defined('MOODLE_INTERNAL') || die();

/**
 * The database_table type.
 *
 * @copyright 2018 Zig Tan <zig@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_table implements type {

    /**
     * @var string  Database table name.
     */
    protected $name;

    /**
     * @var array Fields which contain user information within the table.
     */
    protected $privacyfields;

    /**
     * @var string  A description of what this table is used for.
     */
    protected $summary;

    /**
     * Constructor to create a new database_table type.
     *
     * @param   string  $name The name of the database table being described.
     * @param   array   $privacyfields A list of fields with their description.
     * @param   string  $summary A description of what the table is used for.
     */
    public function __construct($name, array $privacyfields = [], $summary = '') {
        if (debugging('', DEBUG_DEVELOPER)) {
            if (empty($privacyfields)) {
                debugging("Table '{$name}' was supplied without any fields.", DEBUG_DEVELOPER);
            }

            foreach ($privacyfields as $key => $field) {
                $teststring = clean_param($field, PARAM_STRINGID);
                if ($teststring !== $field) {
                    debugging("Field '{$key}' passed for table '{$name}' has an invalid langstring identifier: '{$field}'",
                        DEBUG_DEVELOPER);
                }
            }

            $teststring = clean_param($summary, PARAM_STRINGID);
            if ($teststring !== $summary) {
                debugging("Summary information for the '{$name}' table has an invalid langstring identifier: '{$summary}'",
                    DEBUG_DEVELOPER);
            }
        }

        $this->name = $name;
        $this->privacyfields = $privacyfields;
        $this->summary = $summary;
    }

    /**
     * The name of the database table.
     *
     * @return  string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * The list of fields within the table which contain user data, with a description of each field.
     *
     * @return  array
     */
    public function get_privacy_fields() {
        return $this->privacyfields;
    }

    /**
     * A summary of what this table is used for.
     *
     * @return  string
     */
    public function get_summary() {
        return $this->summary;
    }
}
