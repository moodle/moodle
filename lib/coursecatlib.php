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
 * Deprecated file, classes moved to autoloaded locations
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

debugging('Class coursecat is now alias to autoloaded class core_course_category, ' .
    'course_in_list is an alias to core_course_list_element. '.
    'Class coursecat_sortable_records is deprecated without replacement. Do not include coursecatlib.php',
    DEBUG_DEVELOPER);

/**
 * An array of records that is sortable by many fields.
 *
 * For more info on the ArrayObject class have a look at php.net.
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_sortable_records extends ArrayObject {

    /**
     * An array of sortable fields.
     * Gets set temporarily when sort is called.
     * @var array
     */
    protected $sortfields = array();

    /**
     * Sorts this array using the given fields.
     *
     * @param array $records
     * @param array $fields
     * @return array
     */
    public static function sort(array $records, array $fields) {
        $records = new coursecat_sortable_records($records);
        $records->sortfields = $fields;
        $records->uasort(array($records, 'sort_by_many_fields'));
        return $records->getArrayCopy();
    }

    /**
     * Sorts the two records based upon many fields.
     *
     * This method should not be called itself, please call $sort instead.
     * It has been marked as access private as such.
     *
     * @access private
     * @param stdClass $a
     * @param stdClass $b
     * @return int
     */
    public function sort_by_many_fields($a, $b) {
        foreach ($this->sortfields as $field => $mult) {
            // Nulls first.
            if (is_null($a->$field) && !is_null($b->$field)) {
                return -$mult;
            }
            if (is_null($b->$field) && !is_null($a->$field)) {
                return $mult;
            }

            if (is_string($a->$field) || is_string($b->$field)) {
                // String fields.
                if ($cmp = strcoll($a->$field, $b->$field)) {
                    return $mult * $cmp;
                }
            } else {
                // Int fields.
                if ($a->$field > $b->$field) {
                    return $mult;
                }
                if ($a->$field < $b->$field) {
                    return -$mult;
                }
            }
        }
        return 0;
    }
}
