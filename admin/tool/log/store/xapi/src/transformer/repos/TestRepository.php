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

namespace src\transformer\repos;
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/Repository.php');

/**
 * Test repository. Used in unit testing.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestRepository extends Repository {
    /** @var object Object to store the test data within. */
    private $testdata;

    /**
     * Create the object.
     *
     * @param object $testdata Location to store all testing data.
     * @return void
     */
    public function __construct($testdata) {
        $this->testdata = $testdata;
    }

    /**
     * Reads an array of objects from the store with the given type and query.
     *
     * @param string $type The name of the table to retrieve from.
     * @param array $query Any additional conditions to add to the query.
     * @return array
     */
    public function read_records(string $type, array $query) {
        $records = $this->testdata->$type;
        $matchingrecords = [];

        foreach ($records as $record) {
            foreach ($query as $key => $value) {
                if ($record->$key === $value) {
                    $matchingrecords[] = (object) $record;
                }
            }
        }

        return $matchingrecords;
    }
}
