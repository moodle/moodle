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

namespace mod_scorm;

use cm_info;
use context_module;
use stdClass;

/**
 * Scorm activity manager class
 *
 * @package    mod_scorm
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** Module name. */
    public const MODULE = 'scorm';

    /** The plugin name. */
    public const PLUGINNAME = 'mod_scorm';

    /** @var context_module the current context. */
    private context_module $context;

    /** @var stdClass $course record. */
    private stdClass $course;

    /** @var \moodle_database the database instance. */
    private \moodle_database $db;

    /** @var int|null $participantscount the number of participants who can attempt the activity. */
    private ?int $potentialparticipantscount = null;

    /**
     * Class constructor.
     *
     * @param cm_info $cm course module info object
     * @param stdClass $instance activity instance object.
     */
    public function __construct(
        /** @var cm_info course_modules record. */
        private readonly cm_info $cm,
        /** @var stdClass course_module record. */
        private readonly stdClass $instance,
    ) {
        $this->context = context_module::instance($cm->id);
        $this->db = \core\di::get(\moodle_database::class);
        $this->course = $cm->get_course();
    }

    /**
     * Create a manager instance from an instance record.
     *
     * @param stdClass $instance an activity record
     * @return manager
     */
    public static function create_from_instance(stdClass $instance): self {
        $cm = get_coursemodule_from_instance(self::MODULE, $instance->id);
        if (!$cm) {
            throw new \moodle_exception('invalidcoursemodule', self::PLUGINNAME, '', null, 'Invalid course module');
        }
        $cm = cm_info::create($cm);
        return new self($cm, $instance);
    }

    /**
     * Create a manager instance from a course_modules record.
     *
     * @param stdClass|cm_info $cm an activity record
     * @return manager
     */
    public static function create_from_coursemodule(stdClass|cm_info $cm): self {
        // Ensure that $this->cm is a cm_info object.
        $cm = cm_info::create($cm);
        $db = \core\di::get(\moodle_database::class);
        $instance = $db->get_record(self::MODULE, ['id' => $cm->instance], '*', MUST_EXIST);
        return new self($cm, $instance);
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
        return $this->cm;
    }

    /**
     * Check if a user can see the report links.
     *
     * @param stdClass|null $user user record (default $USER)
     * @return bool if the user can see the attempts link
     */
    public function can_view_reports(?stdClass $user = null): bool {
        global $USER;
        if (empty($user)) {
            $user = $USER;
        }
        return has_capability('mod/scorm:viewreport', $this->context, $user);
    }

    /**
     * Count the number of users who have attempted the SCORM activity.
     *
     * The filter will work with provided groups without validating group selection. For example,
     * if a user isn't a group member in separate groups mode, no group filtering
     * is applied (user should see no groups). Verify user can view actually access any content
     * before calling this method.
     *
     * @param array $groupids optional group id array, empty array means no group filtering.
     * @return int the number of users who have attempted the SCORM activity
     */
    public function count_users_who_attempted(array $groupids = []): int {
        $params = ['scormid' => $this->instance->id];
        $joins = '';
        $where = "WHERE st.scormid = :scormid";

        if ($groupids) {
            $sqljoin = groups_get_members_join($groupids, 'st.userid', $this->context);
            $joins = $sqljoin->joins;
            $where .= " AND $sqljoin->wheres";
            $params += $sqljoin->params;
        }

        $query = "SELECT COUNT(DISTINCT st.userid) FROM {scorm_attempt} st $joins $where";
        return $this->db->count_records_sql($query, $params);
    }

    /**
     * Count the total number of attempts for the SCORM activity.
     *
     * @param array $groupids optional group id array, empty array means no group filtering.
     * @return int the total number of attempts for the SCORM activity
     */
    public function count_all_attempts(array $groupids = []): int {
        $params = ['scormid' => $this->instance->id];
        $joins = '';
        $where = "WHERE a.scormid = :scormid";
        if ($groupids) {
            $sqljoin = groups_get_members_join($groupids, 'a.userid', $this->context);
            $joins = $sqljoin->joins;
            $where .= " AND $sqljoin->wheres";
            $params += $sqljoin->params;
        }
        $sql = "SELECT COUNT(DISTINCT a.id) FROM {scorm_attempt} a $joins $where";
        return $this->db->count_records_sql($sql, $params);
    }

    /**
     * Get the max attempt setting.
     *
     * Just a wrapper around the instance property.
     *
     * @return int
     */
    public function get_max_attempts(): int {
        return $this->instance->maxattempt;
    }

    /**
     * Count the users who can potentially participate in the SCORM activity excluding teachers.
     *
     *  The filter will work with provided groups without validating group selection. For example,
     *  if a user isn't a group member in separate groups mode, no group filtering
     *  is applied (user should see no groups). Verify user can view actually access any content
     *  before calling this method.
     *
     * @param array $groupids groupid array, if empty then do not filter by groups.
     * @return int
     */
    public function count_participants(array $groupids = []): int {
        $students = get_users_by_capability(
            context: $this->context,
            capability: 'mod/scorm:savetrack',
            // Filter by groups if provided, if not provides empty string to not filter by groups.
            groups: $groupids,
        );
        return count($students);
    }

    /**
     * Get the grading method for the SCORM activity.
     * @return string|null
     */
    public function get_grading_method(): ?string {
        $maxattempt = $this->get_max_attempts();
        if ($maxattempt == 1) {
            $grademethodarray = scorm_get_grade_method_array();
            return $grademethodarray[$this->instance->grademethod] ?? null;
        }
        $whatgrademethodarray = scorm_get_what_grade_array();
        return $whatgrademethodarray[$this->instance->whatgrade] ?? null;
    }
}
