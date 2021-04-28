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

use mod_h5pactivity\local\report\participants;
use mod_h5pactivity\local\report\attempts;
use mod_h5pactivity\local\report\results;
use context_module;
use cm_info;
use moodle_recordset;
use core_user;
use stdClass;
use core\dml\sql_join;
use mod_h5pactivity\event\course_module_viewed;

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

    /** Participants cannot review their own attempts. */
    const REVIEWNONE = 0;

    /** Participants can review their own attempts when have one attempt completed. */
    const REVIEWCOMPLETION = 1;

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
     * Return the selected attempt criteria.
     * @return string[] an array "grademethod value", "attempt description"
     */
    public function get_selected_attempt(): array {
        $types = [
            self::GRADEHIGHESTATTEMPT => get_string('attempt_highest', 'mod_h5pactivity'),
            self::GRADEAVERAGEATTEMPT => get_string('attempt_average', 'mod_h5pactivity'),
            self::GRADELASTATTEMPT => get_string('attempt_last', 'mod_h5pactivity'),
            self::GRADEFIRSTATTEMPT => get_string('attempt_first', 'mod_h5pactivity'),
            self::GRADEMANUAL => get_string('attempt_none', 'mod_h5pactivity'),
        ];
        if ($this->instance->enabletracking) {
            $key = $this->instance->grademethod;
        } else {
            $key = self::GRADEMANUAL;
        }
        return [$key, $types[$key]];
    }

    /**
     * Return the available review modes.
     *
     * @return string[] an array "option value" => "option description"
     */
    public static function get_review_modes(): array {
        return [
            self::REVIEWCOMPLETION => get_string('review_on_completion', 'mod_h5pactivity'),
            self::REVIEWNONE => get_string('review_none', 'mod_h5pactivity'),
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
     * Check if a user can see the activity attempts list.
     *
     * @param stdClass|null $user user record (default $USER)
     * @return bool if the user can see the attempts link
     */
    public function can_view_all_attempts(stdClass $user = null): bool {
        global $USER;
        if (!$this->instance->enabletracking) {
            return false;
        }
        if (empty($user)) {
            $user = $USER;
        }
        return has_capability('mod/h5pactivity:reviewattempts', $this->context, $user);
    }

    /**
     * Check if a user can see own attempts.
     *
     * @param stdClass|null $user user record (default $USER)
     * @return bool if the user can see the own attempts link
     */
    public function can_view_own_attempts(stdClass $user = null): bool {
        global $USER;
        if (!$this->instance->enabletracking) {
            return false;
        }
        if (empty($user)) {
            $user = $USER;
        }
        if (has_capability('mod/h5pactivity:reviewattempts', $this->context, $user, false)) {
            return true;
        }
        if ($this->instance->reviewmode == self::REVIEWNONE) {
            return false;
        }
        if ($this->instance->reviewmode == self::REVIEWCOMPLETION) {
            return true;
        }
        return false;

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
     * Count the activity completed attempts.
     *
     * If no user is provided the method will count all active users attempts.
     * Check get_active_users_join PHPdoc to a more detailed description of "active users".
     *
     * @param int|null $userid optional user id (default null)
     * @return int the total amount of attempts
     */
    public function count_attempts(int $userid = null): int {
        global $DB;

        // Counting records is enough for one user.
        if ($userid) {
            $params['userid'] = $userid;
            $params = [
                'h5pactivityid' => $this->instance->id,
                'userid' => $userid,
                'completion' => 1,
            ];
            return $DB->count_records('h5pactivity_attempts', $params);
        }

        $usersjoin = $this->get_active_users_join();

        // Final SQL.
        return $DB->count_records_sql(
            "SELECT COUNT(*)
               FROM {user} u $usersjoin->joins
              WHERE $usersjoin->wheres",
            array_merge($usersjoin->params)
        );
    }

    /**
     * Return the join to collect all activity active users.
     *
     * The concept of active user is relative to the activity permissions. All users with
     * "mod/h5pactivity:view" are potential users but those with "mod/h5pactivity:reviewattempts"
     * are evaluators and they don't count as valid submitters.
     *
     * Note that, in general, the active list has the same effect as checking for "mod/h5pactivity:submit"
     * but submit capability cannot be used because is a write capability and does not apply to frozen contexts.
     *
     * @since Moodle 3.11
     * @param bool $allpotentialusers if true, the join will return all active users, not only the ones with attempts.
     * @return sql_join the active users attempts join
     */
    public function get_active_users_join(bool $allpotentialusers = false): sql_join {

        // Only valid users counts. By default, all users with submit capability are considered potential ones.
        $context = $this->get_context();

        // We want to present all potential users.
        $capjoin = get_enrolled_with_capabilities_join($context, '', 'mod/h5pactivity:view');

        if ($capjoin->cannotmatchanyrows) {
            return $capjoin;
        }

        // But excluding all reviewattempts users converting a capabilities join into left join.
        $reviewersjoin = get_with_capability_join($context, 'mod/h5pactivity:reviewattempts', 'u.id');

        $capjoin = new sql_join(
            $capjoin->joins . "\n LEFT " . str_replace('ra', 'reviewer', $reviewersjoin->joins),
            $capjoin->wheres . " AND reviewer.userid IS NULL",
            $capjoin->params
        );

        if ($allpotentialusers) {
            return $capjoin;
        }

        // Add attempts join.
        $where = "ha.h5pactivityid = :h5pactivityid AND ha.completion = :completion";
        $params = [
            'h5pactivityid' => $this->instance->id,
            'completion' => 1,
        ];

        return new sql_join(
            $capjoin->joins . "\n JOIN {h5pactivity_attempts} ha ON ha.userid = u.id",
            $capjoin->wheres . " AND $where",
            array_merge($capjoin->params, $params)
        );
    }

    /**
     * Return an array of all users and it's total attempts.
     *
     * Note: this funciton only returns the list of users with attempts,
     * it does not check all participants.
     *
     * @return array indexed count userid => total number of attempts
     */
    public function count_users_attempts(): array {
        global $DB;
        $params = [
            'h5pactivityid' => $this->instance->id,
        ];
        $sql = "SELECT userid, count(*)
                  FROM {h5pactivity_attempts}
                 WHERE h5pactivityid = :h5pactivityid
                 GROUP BY userid";
        return $DB->get_records_sql_menu($sql, $params);
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
     * Return the current instance.
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

    /**
     * Return the suitable report to show the attempts.
     *
     * This method controls the access to the different reports
     * the activity have.
     *
     * @param int $userid an opional userid to show
     * @param int $attemptid an optional $attemptid to show
     * @return report|null available report (or null if no report available)
     */
    public function get_report(int $userid = null, int $attemptid = null): ?report {
        global $USER;

        // If tracking is disabled, no reports are available.
        if (!$this->instance->enabletracking) {
            return null;
        }

        $attempt = null;
        if ($attemptid) {
            $attempt = $this->get_attempt($attemptid);
            if (!$attempt) {
                return null;
            }
            // If we have and attempt we can ignore the provided $userid.
            $userid = $attempt->get_userid();
        }

        if ($this->can_view_all_attempts()) {
            $user = core_user::get_user($userid);
        } else if ($this->can_view_own_attempts()) {
            $user = core_user::get_user($USER->id);
            if ($userid && $user->id != $userid) {
                return null;
            }
        } else {
            return null;
        }

        // Only enrolled users has reports.
        if ($user && !is_enrolled($this->context, $user, 'mod/h5pactivity:view')) {
            return null;
        }

        // Create the proper report.
        if ($user && $attempt) {
            return new results($this, $user, $attempt);
        } else if ($user) {
            return new attempts($this, $user);
        }
        return new participants($this);
    }

    /**
     * Return a single attempt.
     *
     * @param int $attemptid the attempt id
     * @return attempt
     */
    public function get_attempt(int $attemptid): ?attempt {
        global $DB;
        $record = $DB->get_record('h5pactivity_attempts', [
            'id' => $attemptid,
            'h5pactivityid' => $this->instance->id,
        ]);
        if (!$record) {
            return null;
        }
        return new attempt($record);
    }

    /**
     * Return an array of all user attempts (including incompleted)
     *
     * @param int $userid the user id
     * @return attempt[]
     */
    public function get_user_attempts(int $userid): array {
        global $DB;
        $records = $DB->get_records(
            'h5pactivity_attempts',
            ['userid' => $userid, 'h5pactivityid' => $this->instance->id],
            'id ASC'
        );
        if (!$records) {
            return [];
        }
        $result = [];
        foreach ($records as $record) {
            $result[] = new attempt($record);
        }
        return $result;
    }

    /**
     * Trigger module viewed event and set the module viewed for completion.
     *
     * @param stdClass $course course object
     * @return void
     */
    public function set_module_viewed(stdClass $course): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        // Trigger module viewed event.
        $event = course_module_viewed::create([
            'objectid' => $this->instance->id,
            'context' => $this->context
        ]);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('course_modules', $this->coursemodule);
        $event->add_record_snapshot('h5pactivity', $this->instance);
        $event->trigger();

        // Completion.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($this->coursemodule);
    }
}
