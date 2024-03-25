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

namespace mod_quiz\local;

use mod_quiz\event\group_override_created;
use mod_quiz\event\group_override_deleted;
use mod_quiz\event\group_override_updated;
use mod_quiz\event\user_override_created;
use mod_quiz\event\user_override_deleted;
use mod_quiz\event\user_override_updated;

/**
 * Manager class for quiz overrides
 *
 * @package   mod_quiz
 * @copyright 2024 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_manager {
    /** @var array quiz setting keys that can be overwritten **/
    private const OVERRIDEABLE_QUIZ_SETTINGS = ['timeopen', 'timeclose', 'timelimit', 'attempts', 'password'];

    /**
     * Create override manager
     *
     * @param \stdClass $quiz The quiz to link the manager to.
     * @param \context_module $context Context being operated in
     */
    public function __construct(
        /** @var \stdClass The quiz linked to this manager instance **/
        protected readonly \stdClass $quiz,
        /** @var \context_module The context being operated in **/
        public readonly \context_module $context
    ) {
        global $CFG;
        // Required for quiz_* methods.
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');

        // Sanity check that the context matches the quiz.
        if (empty($quiz->cmid) || $quiz->cmid != $context->instanceid) {
            throw new \coding_exception("Given context does not match the quiz object");
        }
    }

    /**
     * Returns all overrides for the linked quiz.
     *
     * @return array of quiz_override records
     */
    public function get_all_overrides(): array {
        global $DB;
        return $DB->get_records('quiz_overrides', ['quiz' => $this->quiz->id]);
    }

    /**
     * Validates the data, usually from a moodleform or a webservice call.
     * If it contains an 'id' property, additional validation is performed against the existing record.
     *
     * @param array $formdata data from moodleform or webservice call.
     * @return array array where the keys are error elements, and the values are lists of errors for each element.
     */
    public function validate_data(array $formdata): array {
        global $DB;

        // Because this can be called directly (e.g. via edit_override_form)
        // and not just through save_override, we must ensure the data
        // is parsed in the same way.
        $formdata = $this->parse_formdata($formdata);

        $formdata = (object) $formdata;

        $errors = [];

        // Ensure at least one of the overrideable settings is set.
        $keysthatareset = array_map(function ($key) use ($formdata) {
            return isset($formdata->$key) && !is_null($formdata->$key);
        }, self::OVERRIDEABLE_QUIZ_SETTINGS);

        if (!in_array(true, $keysthatareset)) {
            $errors['general'][] = new \lang_string('nooverridedata', 'quiz');
        }

        // Ensure quiz is a valid quiz.
        if (empty($formdata->quiz) || empty(get_coursemodule_from_instance('quiz', $formdata->quiz))) {
            $errors['quiz'][] = new \lang_string('overrideinvalidquiz', 'quiz');
        }

        // Ensure either userid or groupid is set.
        if (empty($formdata->userid) && empty($formdata->groupid)) {
            $errors['general'][] = new \lang_string('overridemustsetuserorgroup', 'quiz');
        }

        // Ensure not both userid and groupid are set.
        if (!empty($formdata->userid) && !empty($formdata->groupid)) {
            $errors['general'][] = new \lang_string('overridecannotsetbothgroupanduser', 'quiz');
        }

        // If group is set, ensure it is a real group.
        if (!empty($formdata->groupid) && empty(groups_get_group($formdata->groupid))) {
            $errors['groupid'][] = new \lang_string('overrideinvalidgroup', 'quiz');
        }

        // If user is set, ensure it is a valid user.
        if (!empty($formdata->userid) && !\core_user::is_real_user($formdata->userid, true)) {
            $errors['userid'][] = new \lang_string('overrideinvaliduser', 'quiz');
        }

        // Ensure timeclose is later than timeopen, if both are set.
        if (!empty($formdata->timeclose) && !empty($formdata->timeopen) && $formdata->timeclose <= $formdata->timeopen) {
            $errors['timeclose'][] = new \lang_string('closebeforeopen', 'quiz');
        }

        // Ensure attempts is a integer greater than or equal to 0 (0 is unlimited attempts).
        if (isset($formdata->attempts) && ((int) $formdata->attempts < 0)) {
            $errors['attempts'][] = new \lang_string('overrideinvalidattempts', 'quiz');
        }

        // Ensure timelimit is greather than zero.
        if (!empty($formdata->timelimit) && $formdata->timelimit <= 0) {
            $errors['timelimit'][] = new \lang_string('overrideinvalidtimelimit', 'quiz');
        }

        // Ensure other records do not exist with the same group or user.
        if (!empty($formdata->quiz) && (!empty($formdata->userid) || !empty($formdata->groupid))) {
            $existingrecordparams = ['quiz' => $formdata->quiz, 'groupid' => $formdata->groupid ?? null,
                'userid' => $formdata->userid ?? null, ];
            $records = $DB->get_records('quiz_overrides', $existingrecordparams, '', 'id');

            // Ignore self if updating.
            if (!empty($formdata->id)) {
                unset($records[$formdata->id]);
            }

            // If count is not zero, it means existing records exist already for this user/group.
            if (!empty($records)) {
                $errors['general'][] = new \lang_string('overridemultiplerecordsexist', 'quiz');
            }
        }

        // If is existing record, validate it against the existing record.
        if (!empty($formdata->id)) {
            $existingrecorderrors = self::validate_against_existing_record($formdata->id, $formdata);
            $errors = array_merge($errors, $existingrecorderrors);
        }

        // Implode each value (array of error strings) into a single error string.
        foreach ($errors as $key => $value) {
            $errors[$key] = implode(",", $value);
        }

        return $errors;
    }

    /**
     * Returns the existing quiz override record with the given ID or null if does not exist.
     *
     * @param int $id existing quiz override id
     * @return ?\stdClass record, if exists
     */
    private static function get_existing(int $id): ?\stdClass {
        global $DB;
        return $DB->get_record('quiz_overrides', ['id' => $id]) ?: null;
    }

    /**
     * Validates the formdata against an existing record.
     *
     * @param int $existingid id of existing quiz override record
     * @param \stdClass $formdata formdata, usually from moodleform or webservice call.
     * @return array array where the keys are error elements, and the values are lists of errors for each element.
     */
    private static function validate_against_existing_record(int $existingid, \stdClass $formdata): array {
        $existingrecord = self::get_existing($existingid);
        $errors = [];

        // Existing record must exist.
        if (empty($existingrecord)) {
            $errors['general'][] = new \lang_string('overrideinvalidexistingid', 'quiz');
        }

        // Group value must match existing record if it is set in the formdata.
        if (!empty($existingrecord) && !empty($formdata->groupid) && $existingrecord->groupid != $formdata->groupid) {
            $errors['groupid'][] = new \lang_string('overridecannotchange', 'quiz');
        }

        // User value must match existing record if it is set in the formdata.
        if (!empty($existingrecord) && !empty($formdata->userid) && $existingrecord->userid != $formdata->userid) {
            $errors['userid'][] = new \lang_string('overridecannotchange', 'quiz');
        }

        return $errors;
    }

    /**
     * Parses the formdata by finding only the OVERRIDEABLE_QUIZ_SETTINGS,
     * clearing any values that match the existing quiz, and re-adds the user or group id.
     *
     * @param array $formdata data usually from moodleform or webservice call.
     * @return array array containing parsed formdata, with keys as the properties and values as the values.
     * Any values set the same as the existing quiz are set to null.
     */
    public function parse_formdata(array $formdata): array {
        // Get the data from the form that we want to update.
        $settings = array_intersect_key($formdata, array_flip(self::OVERRIDEABLE_QUIZ_SETTINGS));

        // Remove values that are the same as currently in the quiz.
        $settings = $this->clear_unused_values($settings);

        // Add the user / group back as applicable.
        $userorgroupdata = array_intersect_key($formdata, array_flip(['userid', 'groupid', 'quiz', 'id']));

        return array_merge($settings, $userorgroupdata);
    }

    /**
     * Saves the given override. If an id is given, it updates, otherwise it creates a new one.
     * Note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @param array $formdata data usually from moodleform or webservice call.
     * @return int updated/inserted record id
     */
    public function save_override(array $formdata): int {
        global $DB;

        // Extract only the necessary data.
        $datatoset = $this->parse_formdata($formdata);
        $datatoset['quiz'] = $this->quiz->id;

        // Validate the data is OK.
        $errors = $this->validate_data($datatoset);
        if (!empty($errors)) {
            $errorstr = implode(',', $errors);
            throw new \invalid_parameter_exception($errorstr);
        }

        // Insert or update.
        $id = $datatoset['id'] ?? 0;
        if (!empty($id)) {
            $DB->update_record('quiz_overrides', $datatoset);
        } else {
            $id = $DB->insert_record('quiz_overrides', $datatoset);
        }

        $userid = $datatoset['userid'] ?? null;
        $groupid = $datatoset['groupid'] ?? null;

        // Clear the cache.
        $cache = new override_cache($this->quiz->id);
        $cache->clear_for($userid, $groupid);

        // Trigger moodle events.
        if (empty($formdata['id'])) {
            $this->fire_created_event($id, $userid, $groupid);
        } else {
            $this->fire_updated_event($id, $userid, $groupid);
        }

        // Update open events.
        quiz_update_open_attempts(['quizid' => $this->quiz->id]);

        // Update calendar events.
        $isgroup = !empty($datatoset['groupid']);
        if ($isgroup) {
            // If is group, must update the entire quiz calendar events.
            quiz_update_events($this->quiz);
        } else {
            // If is just a user, can update only their calendar event.
            quiz_update_events($this->quiz, (object) $datatoset);
        }

        return $id;
    }

    /**
     * Deletes all the overrides for the linked quiz
     *
     * @param bool $shouldlog If true, will log a override_deleted event
     */
    public function delete_all_overrides(bool $shouldlog = true): void {
        global $DB;
        $overrides = $DB->get_records('quiz_overrides', ['quiz' => $this->quiz->id], '', 'id,userid,groupid');
        $this->delete_overrides($overrides, $shouldlog);
    }

    /**
     * Deletes overrides given just their ID.
     * Note, the given IDs must exist otherwise an exception will be thrown.
     * Also note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @param array $ids IDs of overrides to delete
     * @param bool $shouldlog If true, will log a override_deleted event
     */
    public function delete_overrides_by_id(array $ids, bool $shouldlog = true): void {
        global $DB;
        [$sql, $params] = self::get_override_in_sql($this->quiz->id, $ids);
        $records = $DB->get_records_select('quiz_overrides', $sql, $params, '', 'id,userid,groupid');

        // Ensure all the given ids exist, so the user is aware if they give a dodgy id.
        $missingids = array_diff($ids, array_keys($records));

        if (!empty($missingids)) {
            throw new \invalid_parameter_exception(get_string('overridemissingdelete', 'quiz', implode(',', $missingids)));
        }

        $this->delete_overrides($records, $shouldlog);
    }


    /**
     * Builds sql and parameters to find overrides in quiz with the given ids
     *
     * @param int $quizid id of quiz
     * @param array $ids array of quiz override ids
     * @return array sql and params
     */
    private static function get_override_in_sql(int $quizid, array $ids): array {
        global $DB;

        [$insql, $inparams] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['quizid' => $quizid]);
        $sql = 'id ' . $insql . ' AND quiz = :quizid';
        return [$sql, $params];
    }

    /**
     * Deletes the given overrides in the quiz linked to the override manager.
     * Note - capabilities are not checked, {@see require_manage_capability()}
     *
     * @param array $overrides override to delete. Must specify an id, quizid, and either a userid or groupid.
     * @param bool $shouldlog If true, will log a override_deleted event
     */
    public function delete_overrides(array $overrides, bool $shouldlog = true): void {
        global $DB;

        foreach ($overrides as $override) {
            if (empty($override->id)) {
                throw new \coding_exception("All overrides must specify an ID");
            }

            // Sanity check that user xor group is specified.
            // User or group is required to clear the cache.
            self::ensure_userid_xor_groupid_set($override->userid ?? null, $override->groupid ?? null);
        }

        if (empty($overrides)) {
            // Exit early, since delete select requires at least 1 record.
            return;
        }

        // Match id and quiz.
        [$sql, $params] = self::get_override_in_sql($this->quiz->id, array_column($overrides, 'id'));
        $DB->delete_records_select('quiz_overrides', $sql, $params);

        $cache = new override_cache($this->quiz->id);

        // Perform other cleanup.
        foreach ($overrides as $override) {
            $userid = $override->userid ?? null;
            $groupid = $override->groupid ?? null;

            $cache->clear_for($userid, $groupid);
            $this->delete_override_events($userid, $groupid);

            if ($shouldlog) {
                $this->fire_deleted_event($override->id, $userid, $groupid);
            }
        }
    }

    /**
     * Ensures either userid or groupid is set, but not both.
     * If neither or both are set, a coding exception is thrown.
     *
     * @param ?int $userid user for the record, or null
     * @param ?int $groupid group for the record, or null
     */
    private static function ensure_userid_xor_groupid_set(?int $userid = null, ?int $groupid = null): void {
        $groupset = !empty($groupid);
        $userset = !empty($userid);

        // If either set, but not both (xor).
        $xorset = $groupset ^ $userset;

        if (!$xorset) {
            throw new \coding_exception("Either userid or groupid must be specified, but not both.");
        }
    }

    /**
     * Deletes the events associated with the override.
     *
     * @param ?int $userid or null if groupid is specified
     * @param ?int $groupid or null if the userid is specified
     */
    private function delete_override_events(?int $userid = null, ?int $groupid = null): void {
        global $DB;

        // Sanity check.
        self::ensure_userid_xor_groupid_set($userid, $groupid);

        $eventssearchparams = ['modulename' => 'quiz', 'instance' => $this->quiz->id];

        if (!empty($userid)) {
            $eventssearchparams['userid'] = $userid;
        }

        if (!empty($groupid)) {
            $eventssearchparams['groupid'] = $groupid;
        }

        $events = $DB->get_records('event', $eventssearchparams);
        foreach ($events as $event) {
            $eventold = \calendar_event::load($event);
            $eventold->delete();
        }
    }

    /**
     * Requires the user has the override management capability
     */
    public function require_manage_capability(): void {
        require_capability('mod/quiz:manageoverrides', $this->context);
    }

    /**
     * Requires the user has the override viewing capability
     */
    public function require_read_capability(): void {
        // If user can manage, they can also view.
        // It would not make sense to be able to create and edit overrides without being able to view them.
        if (!has_any_capability(['mod/quiz:viewoverrides', 'mod/quiz:manageoverrides'], $this->context)) {
            throw new \required_capability_exception($this->context, 'mod/quiz:viewoverrides', 'nopermissions', '');
        }
    }

    /**
     * Builds common event data
     *
     * @param int $id override id
     * @return array of data to add as parameters to an event.
     */
    private function get_base_event_params(int $id): array {
        return [
            'context' => $this->context,
            'other' => [
                'quizid' => $this->quiz->id,
            ],
            'objectid' => $id,
        ];
    }

    /**
     * Log that a given override was deleted
     *
     * @param int $id of quiz override that was just deleted
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_deleted_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        self::ensure_userid_xor_groupid_set($userid, $groupid);

        $params = $this->get_base_event_params($id);
        $params['objectid'] = $id;

        if (!empty($userid)) {
            $params['relateduserid'] = $userid;
            user_override_deleted::create($params)->trigger();
        }

        if (!empty($groupid)) {
            $params['other']['groupid'] = $groupid;
            group_override_deleted::create($params)->trigger();
        }
    }


    /**
     * Log that a given override was created
     *
     * @param int $id of quiz override that was just created
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_created_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        self::ensure_userid_xor_groupid_set($userid, $groupid);

        $params = $this->get_base_event_params($id);

        if (!empty($userid)) {
            $params['relateduserid'] = $userid;
            user_override_created::create($params)->trigger();
        }

        if (!empty($groupid)) {
            $params['other']['groupid'] = $groupid;
            group_override_created::create($params)->trigger();
        }
    }

    /**
     * Log that a given override was updated
     *
     * @param int $id of quiz override that was just updated
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_updated_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        self::ensure_userid_xor_groupid_set($userid, $groupid);

        $params = $this->get_base_event_params($id);

        if (!empty($userid)) {
            $params['relateduserid'] = $userid;
            user_override_updated::create($params)->trigger();
        }

        if (!empty($groupid)) {
            $params['other']['groupid'] = $groupid;
            group_override_updated::create($params)->trigger();
        }
    }

    /**
     * Clears any overrideable settings in the formdata, where the value matches what is already in the quiz
     * If they match, the data is set to null.
     *
     * @param array $formdata data usually from moodleform or webservice call.
     * @return array formdata with same values cleared
     */
    private function clear_unused_values(array $formdata): array {
        foreach (self::OVERRIDEABLE_QUIZ_SETTINGS as $key) {
            // If the formdata is the same as the current quiz object data, clear it.
            if (isset($formdata[$key]) && $formdata[$key] == $this->quiz->$key) {
                $formdata[$key] = null;
            }

            // Ensure these keys always are set (even if null).
            $formdata[$key] = $formdata[$key] ?? null;

            // If the formdata is empty, set it to null.
            // This avoids putting 0, false, or '' into the DB since the override logic expects null.
            // Attempts is the exception, it can have a integer value of '0', so we use is_numeric instead.
            if ($key != 'attempts' && empty($formdata[$key])) {
                $formdata[$key] = null;
            }

            if ($key == 'attempts' && !is_numeric($formdata[$key])) {
                $formdata[$key] = null;
            }
        }

        return $formdata;
    }

    /**
     * Deletes orphaned group overrides in a given course.
     * Note - permissions are not checked and events are not logged for performance reasons.
     *
     * @param int $courseid ID of course to delete orphaned group overrides in
     * @return array array of quizzes that had orphaned group overrides.
     */
    public static function delete_orphaned_group_overrides_in_course(int $courseid): array {
        global $DB;

        // It would be nice if we got the groupid that was deleted.
        // Instead, we just update all quizzes with orphaned group overrides.
        $sql = "SELECT o.id, o.quiz, o.groupid
                  FROM {quiz_overrides} o
                  JOIN {quiz} quiz ON quiz.id = o.quiz
             LEFT JOIN {groups} grp ON grp.id = o.groupid
                 WHERE quiz.course = :courseid
                   AND o.groupid IS NOT NULL
                   AND grp.id IS NULL";
        $params = ['courseid' => $courseid];
        $records = $DB->get_records_sql($sql, $params);

        $DB->delete_records_list('quiz_overrides', 'id', array_keys($records));

        // Purge cache for each record.
        foreach ($records as $record) {
            $cache = new override_cache($record->quiz);
            $cache->clear_for_group($record->groupid);
        }
        return array_unique(array_column($records, 'quiz'));
    }
}
