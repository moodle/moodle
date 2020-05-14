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
 * H5P activity manager class
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;

use context_module;
use cm_info;
use moodle_recordset;
use stdClass;

/**
 * Class manager for H5P activity
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** No automathic grading using attempt results. */
    const GRADEMANUAL = 0;

    /** Use highest attempt results for grading. */
    const GRADEHIGHESTATTEMPT = 1;

    /** Use average attempt results for grading. */
    const GRADEAVERAGEATTEMPT = 2;

    /** Use last attempt results for grading. */
    const GRADELASTATTEMPT = 3;

    /** Use first attempt results for grading. */
    const GRADEFIRSTATTEMPT = 4;

    /** @var stdClass course_module record. */
    private $instance;

    /** @var context_module the current context. */
    private $context;

    /** @var cm_info course_modules record. */
    private $coursemodule;

    /**
     * Class contructor.
     *
     * @param cm_info $coursemodule course module info object
     * @param stdClass $instance H5Pactivity instance object.
     */
    public function __construct(cm_info $coursemodule, stdClass $instance) {
        $this->coursemodule = $coursemodule;
        $this->instance = $instance;
        $this->context = context_module::instance($coursemodule->id);
        $this->instance->cmidnumber = $coursemodule->idnumber;
    }

    /**
     * Create a manager instance from an instance record.
     *
     * @param stdClass $instance a h5pactivity record
     * @return manager
     */
    public static function create_from_instance(stdClass $instance): self {
        $coursemodule = get_coursemodule_from_instance('h5pactivity', $instance->id);
        // Ensure that $this->coursemodule is a cm_info object.
        $coursemodule = cm_info::create($coursemodule);
        return new self($coursemodule, $instance);
    }

    /**
     * Create a manager instance from an course_modules record.
     *
     * @param stdClass|cm_info $coursemodule a h5pactivity record
     * @return manager
     */
    public static function create_from_coursemodule($coursemodule): self {
        global $DB;
        // Ensure that $this->coursemodule is a cm_info object.
        $coursemodule = cm_info::create($coursemodule);
        $instance = $DB->get_record('h5pactivity', ['id' => $coursemodule->instance], '*', MUST_EXIST);
        return new self($coursemodule, $instance);
    }

    /**
     * Return the available grading methods.
     * @return string[] an array "option value" => "option description"
     */
    public static function get_grading_methods(): array {
        return [
            self::GRADEHIGHESTATTEMPT => get_string('grade_highest_attempt', 'mod_h5pactivity'),
            self::GRADEAVERAGEATTEMPT => get_string('grade_average_attempt', 'mod_h5pactivity'),
            self::GRADELASTATTEMPT => get_string('grade_last_attempt', 'mod_h5pactivity'),
            self::GRADEFIRSTATTEMPT => get_string('grade_first_attempt', 'mod_h5pactivity'),
            self::GRADEMANUAL => get_string('grade_manual', 'mod_h5pactivity'),
        ];
    }

    /**
     * Check if tracking is enabled in a particular h5pactivity for a specific user.
     *
     * @param stdClass|null $user user record (default $USER)
     * @return bool if tracking is enabled in this activity
     */
    public function is_tracking_enabled(stdClass $user = null): bool {
        global $USER;
        if (!$this->instance->enabletracking) {
            return false;
        }
        if (empty($user)) {
            $user = $USER;
        }
        return has_capability('mod/h5pactivity:submit', $this->context, $user, false);
    }

    /**
     * Return a relation of userid and the valid attempt's scaled score.
     *
     * The returned elements contain a record
     * of userid, scaled value, attemptid and timemodified. In case the grading method is "GRADEAVERAGEATTEMPT"
     * the attemptid will be zero. In case that tracking is disabled or grading method is "GRADEMANUAL"
     * the method will return null.
     *
     * @param int $userid a specific userid or 0 for all user attempts.
     * @return array|null of userid, scaled value and, if exists, the attempt id
     */
    public function get_users_scaled_score(int $userid = 0): ?array {
        global $DB;

        $scaled = [];
        if (!$this->instance->enabletracking) {
            return null;
        }

        if ($this->instance->grademethod == self::GRADEMANUAL) {
            return null;
        }

        $sql = '';

        // General filter.
        $where = 'a.h5pactivityid = :h5pactivityid';
        $params['h5pactivityid'] = $this->instance->id;

        if ($userid) {
            $where .= ' AND a.userid = :userid';
            $params['userid'] = $userid;
        }

        // Average grading needs aggregation query.
        if ($this->instance->grademethod == self::GRADEAVERAGEATTEMPT) {
            $sql = "SELECT a.userid, AVG(a.scaled) AS scaled, 0 AS attemptid, MAX(timemodified) AS timemodified
                      FROM {h5pactivity_attempts} a
                     WHERE $where AND a.completion = 1
                  GROUP BY a.userid";
        }

        if (empty($sql)) {
            // Decide which attempt is used for the calculation.
            $condition = [
                self::GRADEHIGHESTATTEMPT => "a.scaled < b.scaled",
                self::GRADELASTATTEMPT => "a.attempt < b.attempt",
                self::GRADEFIRSTATTEMPT => "a.attempt > b.attempt",
            ];
            $join = $condition[$this->instance->grademethod] ?? $condition[self::GRADEHIGHESTATTEMPT];

            $sql = "SELECT a.userid, a.scaled, MAX(a.id) AS attemptid, MAX(a.timemodified) AS timemodified
                      FROM {h5pactivity_attempts} a
                 LEFT JOIN {h5pactivity_attempts} b ON a.h5pactivityid = b.h5pactivityid
                           AND a.userid = b.userid AND b.completion = 1
                           AND $join
                     WHERE $where AND b.id IS NULL AND a.completion = 1
                  GROUP BY a.userid, a.scaled";
        }

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Return the current context.
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->context;
    }

    /**
     * Return the current context.
     *
     * @return stdClass the instance record
     */
    public function get_instance(): stdClass {
        return $this->instance;
    }

    /**
     * Return the current cm_info.
     *
     * @return cm_info the course module
     */
    public function get_coursemodule(): cm_info {
        return $this->coursemodule;
    }

    /**
     * Return the specific grader object for this activity.
     *
     * @return grader
     */
    public function get_grader(): grader {
        $idnumber = $this->coursemodule->idnumber ?? '';
        return new grader($this->instance, $idnumber);
    }
}
