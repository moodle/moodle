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
 * Priority Queue class to sort out db entry contents.
 *
 * @package    mod_data
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_data\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Priority Queue class to sort out db entry contents.
 *
 * @package    mod_data
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sortedcontentqueue extends \SPLPriorityQueue {

    /**
     * @var array All contents that will be sorted.
     */
    private $contents;

    /**
     * contructor
     *
     * @param array $contents
     * @return void
     */
    public function __construct($contents) {
        $this->contents = $contents;
    }

    /**
     * comparator function overriden for sorting the records
     * ...as per 'required' and 'priotirity' field values
     *
     * @param int $key1
     * @param int $key2
     * @return int
     */
    public function compare($key1 , $key2): int {
        $record1 = $this->contents[$key1];
        $record2 = $this->contents[$key2];

        // If a content's fieldtype is compulsory in the database than it would have priority than any other noncompulsory content.
        if ( ($record1->required && $record2->required) || (!$record1->required && !$record2->required)) {
            if ($record1->priority === $record2->priority) {
                return $key1 < $key2 ? 1 : -1;
            }

            return $record1->priority < $record2->priority ? -1 : 1;

        } else if ($record1->required && !$record2->required) {
            return 1;
        } else {
            return -1;
        }

    }
}