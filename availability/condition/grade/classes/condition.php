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
 * Condition on grades of current user.
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_grade;

defined('MOODLE_INTERNAL') || die();

/**
 * Condition on grades of current user.
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var int Grade item id */
    private $gradeitemid;

    /** @var float|null Min grade (must be >= this) or null if none */
    private $min;

    /** @var float|null Max grade (must be < this) or null if none */
    private $max;

    /**
     * Constructor.
     *
     * @param stdClass $structure Data structure from JSON decode
     * @throws coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get grade item id.
        if (isset($structure->id) && is_int($structure->id)) {
            $this->gradeitemid = $structure->id;
        } else {
            throw new \coding_exception('Missing or invalid ->id for grade condition');
        }

        // Get min and max.
        if (!property_exists($structure, 'min')) {
            $this->min = null;
        } else if (is_float($structure->min) || is_int($structure->min)) {
            $this->min = $structure->min;
        } else {
            throw new \coding_exception('Missing or invalid ->min for grade condition');
        }
        if (!property_exists($structure, 'max')) {
            $this->max = null;
        } else if (is_float($structure->max) || is_int($structure->max)) {
            $this->max = $structure->max;
        } else {
            throw new \coding_exception('Missing or invalid ->max for grade condition');
        }
    }

    public function save() {
        $result = (object)array('type' => 'grade', 'id' => $this->gradeitemid);
        if (!is_null($this->min)) {
            $result->min = $this->min;
        }
        if (!is_null($this->max)) {
            $result->max = $this->max;
        }
        return $result;
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        $course = $info->get_course();
        $score = $this->get_cached_grade_score($this->gradeitemid, $course->id, $grabthelot, $userid);
        $allow = $score !== false &&
                (is_null($this->min) || $score >= $this->min) &&
                (is_null($this->max) || $score < $this->max);
        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        $course = $info->get_course();
        // String depends on type of requirement. We are coy about
        // the actual numbers, in case grades aren't released to
        // students.
        if (is_null($this->min) && is_null($this->max)) {
            $string = 'any';
        } else if (is_null($this->max)) {
            $string = 'min';
        } else if (is_null($this->min)) {
            $string = 'max';
        } else {
            $string = 'range';
        }
        if ($not) {
            // The specific strings don't make as much sense with 'not'.
            if ($string === 'any') {
                $string = 'notany';
            } else {
                $string = 'notgeneral';
            }
        }
        $name = self::get_cached_grade_name($course->id, $this->gradeitemid);
        return get_string('requires_' . $string, 'availability_grade', $name);
    }

    protected function get_debug_string() {
        $out = '#' . $this->gradeitemid;
        if (!is_null($this->min)) {
            $out .= ' >= ' . sprintf('%.5f', $this->min);
        }
        if (!is_null($this->max)) {
            if (!is_null($this->min)) {
                $out .= ',';
            }
            $out .= ' < ' . sprintf('%.5f', $this->max);
        }
        return $out;
    }

    /**
     * Obtains the name of a grade item, also checking that it exists. Uses a
     * cache. The name returned is suitable for display.
     *
     * @param int $courseid Course id
     * @param int $gradeitemid Grade item id
     * @return string Grade name or empty string if no grade with that id
     */
    private static function get_cached_grade_name($courseid, $gradeitemid) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        // Get all grade item names from cache, or using db query.
        $cache = \cache::make('availability_grade', 'items');
        if (($cacheditems = $cache->get($courseid)) === false) {
            // We cache the whole items table not the name; the format_string
            // call for the name might depend on current user (e.g. multilang)
            // and this is a shared cache.
            $cacheditems = $DB->get_records('grade_items', array('courseid' => $courseid));
            $cache->set($courseid, $cacheditems);
        }

        // Return name from cached item or a lang string.
        if (!array_key_exists($gradeitemid, $cacheditems)) {
            return get_string('missing', 'availability_grade');
        }
        $gradeitemobj = $cacheditems[$gradeitemid];
        $item = new \grade_item;
        \grade_object::set_properties($item, $gradeitemobj);
        return $item->get_name();
    }

    /**
     * Obtains a grade score. Note that this score should not be displayed to
     * the user, because gradebook rules might prohibit that. It may be a
     * non-final score subject to adjustment later.
     *
     * @param int $gradeitemid Grade item ID we're interested in
     * @param int $courseid Course id
     * @param bool $grabthelot If true, grabs all scores for current user on
     *   this course, so that later ones come from cache
     * @param int $userid Set if requesting grade for a different user (does
     *   not use cache)
     * @return float Grade score as a percentage in range 0-100 (e.g. 100.0
     *   or 37.21), or false if user does not have a grade yet
     */
    protected static function get_cached_grade_score($gradeitemid, $courseid,
            $grabthelot=false, $userid=0) {
        global $USER, $DB;
        if (!$userid) {
            $userid = $USER->id;
        }
        $cache = \cache::make('availability_grade', 'scores');
        if (($cachedgrades = $cache->get($userid)) === false) {
            $cachedgrades = array();
        }
        if (!array_key_exists($gradeitemid, $cachedgrades)) {
            if ($grabthelot) {
                // Get all grades for the current course.
                $rs = $DB->get_recordset_sql('
                        SELECT
                            gi.id,gg.finalgrade,gg.rawgrademin,gg.rawgrademax
                        FROM
                            {grade_items} gi
                            LEFT JOIN {grade_grades} gg ON gi.id=gg.itemid AND gg.userid=?
                        WHERE
                            gi.courseid = ?', array($userid, $courseid));
                foreach ($rs as $record) {
                    if (is_null($record->finalgrade)) {
                        // No grade = false.
                        $cachedgrades[$record->id] = false;
                    } else {
                        // Otherwise convert grade to percentage.
                        $cachedgrades[$record->id] =
                                (($record->finalgrade - $record->rawgrademin) * 100) /
                                ($record->rawgrademax - $record->rawgrademin);
                    }
                }
                $rs->close();
                // And if it's still not set, well it doesn't exist (eg
                // maybe the user set it as a condition, then deleted the
                // grade item) so we call it false.
                if (!array_key_exists($gradeitemid, $cachedgrades)) {
                    $cachedgrades[$gradeitemid] = false;
                }
            } else {
                // Just get current grade.
                $record = $DB->get_record('grade_grades', array(
                    'userid' => $userid, 'itemid' => $gradeitemid));
                if ($record && !is_null($record->finalgrade)) {
                    $score = (($record->finalgrade - $record->rawgrademin) * 100) /
                        ($record->rawgrademax - $record->rawgrademin);
                } else {
                    // Treat the case where row exists but is null, same as
                    // case where row doesn't exist.
                    $score = false;
                }
                $cachedgrades[$gradeitemid] = $score;
            }
            $cache->set($userid, $cachedgrades);
        }
        return $cachedgrades[$gradeitemid];
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        global $DB;
        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'grade_item', $this->gradeitemid);
        if (!$rec || !$rec->newitemid) {
            // If we are on the same course (e.g. duplicate) then we can just
            // use the existing one.
            if ($DB->record_exists('grade_items',
                    array('id' => $this->gradeitemid, 'courseid' => $courseid))) {
                return false;
            }
            // Otherwise it's a warning.
            $this->gradeitemid = 0;
            $logger->process('Restored item (' . $name .
                    ') has availability condition on grade that was not restored',
                    \backup::LOG_WARNING);
        } else {
            $this->gradeitemid = (int)$rec->newitemid;
        }
        return true;
    }

    public function update_dependency_id($table, $oldid, $newid) {
        if ($table === 'grade_items' && (int)$this->gradeitemid === (int)$oldid) {
            $this->gradeitemid = $newid;
            return true;
        } else {
            return false;
        }
    }
}
