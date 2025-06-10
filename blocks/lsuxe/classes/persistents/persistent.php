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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\persistents;

// We need to alias the invalid_persistent_exception, because the persistent classes from
// core_competency used to throw a \core_competency\invalid_persistent_exception. They now
// fully inherit from \core\persistent which throws a core exception. Using class_alias
// ensures that previous try/catch statements still work. Also note that we always need
// need to alias, we cannot do it passively in the classloader because try/catch statements
// do not trigger a class loading. Note that for this trick to work, all the classes
// which were extending \core_competency\persistent still need to extend it or the alias
// won't be effective.

/**
 * Abstract class for core_competency objects saved to the DB.
 *
 * This is a legacy class which all core_competency persistent classes created prior
 * to 3.3 must extend.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent extends \core\persistent {
    /**
     * Get al the records for mappings. The returning array is formatted
     * for templates.
     * @return array
     */
    public static function get_all_records($thisform = null, $where = null, $order = "DESC") {
        global $DB;

        if ($where == null) {
            $where = "WHERE timedeleted IS NULL";
        }

        $sql = 'SELECT * FROM {' . static::TABLE . '}
            '. $where .'
            ORDER BY timecreated '. $order;

        $recordset = $DB->get_records_sql($sql);
        $thesemappings = array();
        foreach ($recordset as $record) {
            // Convert record from obj to array.
            $temp = (array) $record;
            $thesemappings[] = $temp;
        }

        return array($thisform => $thesemappings);
    }
}
