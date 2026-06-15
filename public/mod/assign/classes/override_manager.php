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

namespace mod_assign;

use assign;
use cache;
use calendar_event;
use context_module;
use core\exception\moodle_exception;
use core_course\cm_info;
use core_user;
use core_user\fields as user_field;
use invalid_parameter_exception;
use mod_assign\event\group_override_created;
use mod_assign\event\group_override_deleted;
use mod_assign\event\group_override_updated;
use mod_assign\event\user_override_created;
use mod_assign\event\user_override_deleted;
use mod_assign\event\user_override_updated;
use mod_assign\penalty\helper as penalty_helper;
use stdClass;

/**
 * Manager class for assignment overrides
 *
 * @package   mod_assign
 * @copyright 2025 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_manager {
    /** @var array assignment setting keys that can be overwritten **/
    private const OVERRIDEABLE_ASSIGN_SETTINGS = [
        'duedate',
        'cutoffdate',
        'allowsubmissionsfromdate',
        'timelimit',
    ];

    /** @var array override-only setting keys (not present on the assignment itself) **/
    private const OVERRIDE_ONLY_SETTINGS = [
        'reason',
        'reasonformat',
    ];

    /**
     * Create override manager
     *
     * @param stdClass $assign The assignment to link the manager to.
     * @param context_module $context Context being operated in
     */
    public function __construct(
        /** @var stdClass The assignment linked to this manager instance **/
        protected readonly stdClass $assign,
        /** @var context_module The context being operated in **/
        public readonly context_module $context
    ) {
        global $CFG;
        // Required for assign_* functions.
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
    }

    /**
     * Returns all overrides for the linked assignment.
     *
     * @return array of assign_override records
     */
    public function get_all_overrides(): array {
        global $DB;
        return $DB->get_records('assign_overrides', ['assignid' => $this->assign->id]);
    }

    /**
     * Returns all overrides for the linked assignment that the user can access.
     * Note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @return array of assign_override records that are accessible by the current user
     */
    public function get_accessible_overrides(): array {
        global $DB;

        $cm = get_coursemodule_from_instance('assign', $this->assign->id, $this->assign->course, false, MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $this->assign->course], '*', MUST_EXIST);

        // Filter for those overrides user can access.
        $filteredoverrides = array_filter(
            $this->get_all_overrides(),
            fn(stdClass $override) => $this->can_view_override($override, $course, $cm)
        );

        // Convert to array and reset keys.
        return array_values($filteredoverrides);
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

        // Keep original formdata to check what was actually provided.
        $originalformdata = $formdata;

        // Parse the formdata to only include overrideable settings and user/group info.
        $formdata = $this->parse_formdata($formdata);

        $formdata = (object) $formdata;

        $errors = [];

        // Ensure at least one of userid or groupid is set (an override must target either a user or a group).
        if (empty($formdata->userid) && empty($formdata->groupid)) {
            $errors['general'][] = get_string('overridemustseteitheruserorgroup', 'assign');
        }

        // Ensure not both userid and groupid are set.
        if (!empty($formdata->userid) && !empty($formdata->groupid)) {
            $errors['general'][] = get_string('overridecannotsetboth', 'assign');
        }

        // If group is set, ensure it is a real group.
        if (!empty($formdata->groupid) && !groups_get_group($formdata->groupid)) {
            $errors['groupid'][] = get_string('invalidgroupid', 'assign');
        }

        // If user is set, ensure it is a valid user.
        if (!empty($formdata->userid) && !core_user::is_real_user($formdata->userid, true)) {
            $errors['userid'][] = get_string('invaliduserid', 'assign');
        }

        // Validate date ordering: allowsubmissionsfromdate < duedate < cutoffdate.
        if (!empty($formdata->allowsubmissionsfromdate) && !empty($formdata->cutoffdate)) {
            if ($formdata->cutoffdate < $formdata->allowsubmissionsfromdate) {
                $errors['cutoffdate'][] = get_string('cutoffdatefromdatevalidation', 'assign');
            }
        }

        if (!empty($formdata->allowsubmissionsfromdate) && !empty($formdata->duedate)) {
            if ($formdata->duedate <= $formdata->allowsubmissionsfromdate) {
                $errors['duedate'][] = get_string('duedateaftersubmissionvalidation', 'assign');
            }
        }

        if (!empty($formdata->cutoffdate) && !empty($formdata->duedate)) {
            if ($formdata->cutoffdate < $formdata->duedate) {
                $errors['cutoffdate'][] = get_string('cutoffdatevalidation', 'assign');
            }
        }

        // Check extension due dates.
        $userid = $formdata->userid ?? null;
        if (!empty($userid)) {
            $userflags = $DB->get_record(
                'assign_user_flags',
                ['assignment' => $this->assign->id, 'userid' => $userid]
            );

            if ($userflags && !empty($userflags->extensionduedate)) {
                if (!empty($formdata->duedate) && $userflags->extensionduedate < $formdata->duedate) {
                    $errors['duedate'][] = get_string('extensionnotafterduedate', 'assign');
                }
                if (
                    !empty($formdata->allowsubmissionsfromdate) &&
                    $userflags->extensionduedate < $formdata->allowsubmissionsfromdate
                ) {
                    $errors['allowsubmissionsfromdate'][] = get_string('extensionnotafterfromdate', 'assign');
                }
            }
        }

        // Check for group extension dates if group override.
        $groupid = $formdata->groupid ?? null;
        if (!empty($groupid)) {
            $groupmembers = groups_get_members($groupid);
            $extensionmax = 0;

            if (!empty($groupmembers)) {
                // Get all user IDs from the group.
                $userids = array_keys($groupmembers);

                // Fetch the maximum extension due date in a single query.
                [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
                $params['assignment'] = $this->assign->id;

                $sql = "SELECT MAX(extensionduedate) as maxextension
                          FROM {assign_user_flags}
                         WHERE assignment = :assignment
                           AND userid $insql
                           AND extensionduedate IS NOT NULL
                           AND extensionduedate > 0";

                $result = $DB->get_record_sql($sql, $params);
                if ($result && $result->maxextension) {
                    $extensionmax = $result->maxextension;
                }
            }

            if ($extensionmax > 0) {
                if (!empty($formdata->duedate) && $extensionmax < $formdata->duedate) {
                    $errors['duedate'][] = get_string('extensionnotafterduedate', 'assign');
                }
                if (
                    !empty($formdata->allowsubmissionsfromdate) &&
                    $extensionmax < $formdata->allowsubmissionsfromdate
                ) {
                    $errors['allowsubmissionsfromdate'][] = get_string('extensionnotafterfromdate', 'assign');
                }
            }
        }

        // Ensure at least one setting was provided.
        // Use the original formdata to check what was actually sent, not the parsed version.
        // As the parsed version will clear values that match existing assignment's settings.
        $changed = false;
        foreach (array_merge(self::OVERRIDEABLE_ASSIGN_SETTINGS, self::OVERRIDE_ONLY_SETTINGS) as $key) {
            if (array_key_exists($key, $originalformdata)) {
                $changed = true;
                break;
            }
        }

        // If no settings were changed, return error.
        if (!$changed) {
            $errors['general'][] = get_string('nooverridedata', 'assign');
        }

        // If there is existing record, validate it against the existing record.
        if (!empty($formdata->id)) {
            $existingrecorderrors = $this->validate_against_existing_record($formdata->id, $formdata);
            $errors = array_merge($errors, $existingrecorderrors);
        }

        // Implode each value (array of error strings) into a single error string.
        foreach ($errors as $key => $value) {
            $errors[$key] = implode(",", $value);
        }

        return $errors;
    }

    /**
     * Returns the existing assign override record with the given ID or false if it does not exist.
     *
     * @param int $id existing assign override id
     * @return false|stdClass record, if exists
     * @throws \dml_exception
     */
    private function get_existing(int $id): false|stdClass {
        global $DB;
        return $DB->get_record('assign_overrides', ['id' => $id, 'assignid' => $this->assign->id]);
    }

    /**
     * Validates the formdata against an existing record.
     *
     * @param int $existingid id of existing assign override record
     * @param stdClass $formdata formdata, usually from moodleform or webservice call.
     * @return array array where the keys are error elements, and the values are lists of errors for each element.
     */
    private function validate_against_existing_record(int $existingid, stdClass $formdata): array {
        $existingrecord = $this->get_existing($existingid);
        $errors = [];

        // Existing record must exist.
        if (empty($existingrecord)) {
            $errors['general'][] = get_string('invalidoverrideid', 'assign');
        }

        // Group value must match existing record if it is set in the formdata.
        if (!empty($existingrecord) && !empty($formdata->groupid) && $existingrecord->groupid != $formdata->groupid) {
            $errors['groupid'][] = get_string('overridechangegroupid', 'assign');
        }

        // User value must match existing record if it is set in the formdata.
        if (!empty($existingrecord) && !empty($formdata->userid) && $existingrecord->userid != $formdata->userid) {
            $errors['userid'][] = get_string('overridechangeuserid', 'assign');
        }

        return $errors;
    }

    /**
     * Parses the formdata by finding only the OVERRIDEABLE_ASSIGN_SETTINGS,
     * clearing any values that match the existing assignment, and re-adds the user or group id.
     *
     * @param array $formdata data usually from moodleform or webservice call.
     * @return array array containing parsed formdata, with keys as the properties and values as the values.
     * Any values set the same as the existing assignment are set to null.
     */
    private function parse_formdata(array $formdata): array {
        // Get the data from the form that we want to update.
        $settings = array_intersect_key($formdata, array_flip(self::OVERRIDEABLE_ASSIGN_SETTINGS));

        // Remove values that are the same as currently in the assignment.
        $settings = $this->clear_unused_values($settings);

        // Get override-only settings (not present on the assignment).
        $overrideonly = array_intersect_key($formdata, array_flip(self::OVERRIDE_ONLY_SETTINGS));

        // Add the user / group back as applicable.
        $userorgroupdata = array_intersect_key($formdata, array_flip(['userid', 'groupid', 'assignid', 'id']));

        return array_merge($settings, $overrideonly, $userorgroupdata);
    }

    /**
     * Saves multiple overrides at once. Each override can contain an id for updating existing overrides.
     * Note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @param array $overridesdata array of override data, where each element is data usually from moodleform or webservice call.
     * @param bool $recalculatepenalties If true, recalculate penalties for all affected users after saving overrides.
     * @return array array of updated/inserted record ids
     */
    public function save_overrides(array $overridesdata, bool $recalculatepenalties = false): array {
        $ids = [];
        foreach ($overridesdata as $override) {
            $overrideid = $this->save_override($override);
            $ids[] = $overrideid;

            // Recalculate penalties if requested.
            if ($recalculatepenalties) {
                $userid = $override['userid'] ?? null;
                $groupid = $override['groupid'] ?? null;
                if ($userid || $groupid) {
                    $this->recalculate_penalties($userid, $groupid);
                }
            }
        }
        return $ids;
    }

    /**
     * Saves the given override. If an id is given, it updates, otherwise it creates a new one.
     *
     * @param array $override data usually from moodleform or webservice call.
     * @return int updated/inserted record id
     */
    private function save_override(array $override): int {
        global $DB;

        // Extract only the necessary data.
        $datatoset = $this->parse_formdata($override);
        $datatoset['assignid'] = $this->assign->id;

        // Validate the data.
        $errors = $this->validate_data($datatoset);
        if (!empty($errors)) {
            $errorstr = implode(',', $errors);
            throw new invalid_parameter_exception($errorstr);
        }

        // Convert to object for ease of use.
        $datatoset = (object) $datatoset;

        // Get existing override if updating.
        $existingoverride = null;
        if (!empty($datatoset->id)) {
            $existingoverride = $this->get_existing($datatoset->id);
        }

        // Check if user or group changed.
        $userorgroupchanged = false;
        if (empty($existingoverride)) {
            $userorgroupchanged = true;
        } else if (!empty($datatoset->userid)) {
            $userorgroupchanged = $datatoset->userid !== $existingoverride->userid;
        } else if (!empty($datatoset->groupid)) {
            $userorgroupchanged = $datatoset->groupid !== $existingoverride->groupid;
        }

        // Check for existing override with same user/group.
        if ($userorgroupchanged) {
            $conditions = [
                'assignid' => $this->assign->id,
                'userid' => empty($datatoset->userid) ? null : $datatoset->userid,
                'groupid' => empty($datatoset->groupid) ? null : $datatoset->groupid,
            ];
            if ($oldoverride = $DB->get_record('assign_overrides', $conditions)) {
                // On overrideedit.php form, user/group selection is disabled, so $userorgroupchanged is always false.
                // While the save_override() ws allows changing user/group, so we need to handle this case.
                // Don't delete the override we're currently updating.
                if (empty($existingoverride) || $oldoverride->id != $existingoverride->id) {
                    // Merge with old override.
                    foreach (self::OVERRIDEABLE_ASSIGN_SETTINGS as $key) {
                        if (is_null($datatoset->{$key})) {
                            $datatoset->{$key} = $oldoverride->{$key};
                        }
                    }
                    $this->delete_overrides_by_id([$oldoverride->id]);
                }
            }
        }

        $groupmode = !empty($datatoset->groupid);

        // Insert or update.
        $id = $datatoset->id ?? 0;
        if (!empty($id)) {
            // Update existing record.
            $DB->update_record('assign_overrides', $datatoset);
        } else {
            // Insert new record.
            unset($datatoset->id);
            $id = $DB->insert_record('assign_overrides', $datatoset);
            $datatoset->id = $id;

            // Set sort order for group overrides.
            if ($groupmode) {
                $overridecountgroup = $DB->count_records(
                    'assign_overrides',
                    ['userid' => null, 'assignid' => $this->assign->id]
                );
                $overridecountall = $DB->count_records('assign_overrides', ['assignid' => $this->assign->id]);

                if ((!$overridecountgroup) && ($overridecountall)) {
                    $datatoset->sortorder = 1;
                } else {
                    $datatoset->sortorder = $overridecountgroup;
                }
                $DB->update_record('assign_overrides', $datatoset);
                $this->reorder_group_overrides();
            }
        }

        $userid = $datatoset->userid ?? null;
        $groupid = $datatoset->groupid ?? null;

        // Clear cache.
        $this->clear_cache_for($userid, $groupid);

        // Trigger moodle events.
        if (empty($override['id'])) {
            $this->fire_created_event($id, $userid, $groupid);
        } else {
            $this->fire_updated_event($id, $userid, $groupid);
        }

        // Get cm for assign_update_events.
        $cm = get_coursemodule_from_instance('assign', $this->assign->id, $this->assign->course, false, MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $this->assign->course], '*', MUST_EXIST);

        // Create assign instance for calendar updates.
        $assigninstance = new assign($this->context, $cm, $course);

        // Update calendar events.
        assign_update_events($assigninstance, $datatoset);

        return $id;
    }

    /**
     * Deletes all the overrides for the linked assignment that the user can access.
     * Note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @param bool $shouldlog If true, will log a override_deleted event
     * @param bool $recalculatepenalties If true, recalculate penalties for affected users after deletion
     */
    public function delete_all_overrides(bool $shouldlog = true, bool $recalculatepenalties = false): void {
        global $DB;
        $overrides = $DB->get_records('assign_overrides', ['assignid' => $this->assign->id], '', 'id,userid,groupid');
        $this->delete_overrides($overrides, $shouldlog, $recalculatepenalties);
    }

    /**
     * Deletes overrides given just their ID.
     * Note, the given IDs must exist and user must have access to them otherwise an exception will be thrown.
     * Capabilities are not checked, {@see require_manage_capability()}
     *
     * @param array $ids IDs of overrides to delete
     * @param bool $shouldlog If true, will log a override_deleted event
     * @param bool $recalculatepenalties If true, recalculate penalties for affected users after deletion
     */
    public function delete_overrides_by_id(array $ids, bool $shouldlog = true, bool $recalculatepenalties = false): void {
        global $DB;

        // Early return if no IDs provided.
        if (empty($ids)) {
            return;
        }

        [$sql, $params] = $this->get_override_in_sql($this->assign->id, $ids);
        $overrides = $DB->get_records_select('assign_overrides', $sql, $params, '', 'id,userid,groupid');
        $this->delete_overrides($overrides, $shouldlog, $recalculatepenalties);
    }

    /**
     * Builds sql and parameters to find overrides in assignment with the given ids
     *
     * @param int $assignid id of assignment
     * @param array $ids array of assign override ids
     * @return array sql and params
     */
    private function get_override_in_sql(int $assignid, array $ids): array {
        global $DB;

        [$insql, $inparams] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $params = array_merge($inparams, ['assignid' => $assignid]);
        $sql = 'id ' . $insql . ' AND assignid = :assignid';
        return [$sql, $params];
    }

    /**
     * Deletes the given overrides in the assignment linked to the override manager.
     *
     * @param array $overrides override to delete. Must specify an id, assignid, and either a userid or groupid.
     * @param bool $shouldlog If true, will log an override_deleted event
     * @param bool $recalculatepenalties If true, recalculate penalties for affected users after deletion
     */
    private function delete_overrides(array $overrides, bool $shouldlog = true, bool $recalculatepenalties = false): void {
        global $DB;

        if (empty($overrides)) {
            return;
        }

        // Details to verify user can access all the overrides before deleting.
        $cm = get_coursemodule_from_instance('assign', $this->assign->id, $this->assign->course, false, MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $this->assign->course], '*', MUST_EXIST);

        // Check if any group overrides were deleted and reorder if needed.
        $hasgroupoverride = false;
        foreach ($overrides as $override) {
            if (empty($override->id)) {
                throw new invalid_parameter_exception("All overrides must specify an ID");
            }

            // Verify user can access override.
            if (!$this->can_view_override($override, $course, $cm)) {
                throw new invalid_parameter_exception(
                    'Override with id ' . $override->id . ' is not accessible by user.'
                );
            }

            // Sanity check that user xor group is specified.
            // User or group is required to clear the cache.
            $this->ensure_userid_xor_groupid_set($override->userid ?? null, $override->groupid ?? null);

            if (!empty($override->groupid)) {
                $hasgroupoverride = true;
            }
        }

        // Match id and assignid.
        [$sql, $params] = $this->get_override_in_sql($this->assign->id, array_column($overrides, 'id'));
        $DB->delete_records_select('assign_overrides', $sql, $params);

        // Reorder group overrides BEFORE cleanup/recalculation.
        // When users belong to multiple groups, override_exists() uses
        // "ORDER BY sortorder ASC" to select which override applies. Reordering ensures
        // the sortorder is sequential without gaps, so penalty calculations use the correct
        // override (the one with the lowest sortorder among remaining overrides).
        if ($hasgroupoverride) {
            $this->reorder_group_overrides();
        }

        // Perform other cleanup.
        foreach ($overrides as $override) {
            $userid = $override->userid ?? null;
            $groupid = $override->groupid ?? null;

            $this->clear_cache_for($userid, $groupid);
            $this->delete_override_events($userid, $groupid);

            if ($shouldlog) {
                $this->fire_deleted_event($override->id, $userid, $groupid);
            }

            // Recalculate grades if requested.
            if ($recalculatepenalties) {
                $this->recalculate_penalties($userid, $groupid);
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
    private function ensure_userid_xor_groupid_set(?int $userid = null, ?int $groupid = null): void {
        $groupset = !empty($groupid);
        $userset = !empty($userid);

        // If either set, but not both (xor).
        $xorset = $groupset ^ $userset;

        if (!$xorset) {
            throw new invalid_parameter_exception("Either userid or groupid must be specified, but not both.");
        }
    }

    /**
     * Clears the cache for a specific user or group override.
     *
     * @param ?int $userid or null if groupid is specified
     * @param ?int $groupid or null if the userid is specified
     */
    private function clear_cache_for(?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

        $cachekey = !empty($groupid) ?
            "{$this->assign->id}_g_{$groupid}" :
            "{$this->assign->id}_u_{$userid}";
        cache::make('mod_assign', 'overrides')->delete($cachekey);
    }

    /**
     * Deletes the events associated with the override.
     *
     * @param ?int $userid or null if groupid is specified
     * @param ?int $groupid or null if the userid is specified
     */
    private function delete_override_events(?int $userid = null, ?int $groupid = null): void {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/calendar/lib.php');

        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

        $eventssearchparams = ['modulename' => 'assign', 'instance' => $this->assign->id];

        if (!empty($userid)) {
            $eventssearchparams['userid'] = $userid;
        }

        if (!empty($groupid)) {
            $eventssearchparams['groupid'] = $groupid;
        }

        $events = $DB->get_records('event', $eventssearchparams);
        foreach ($events as $event) {
            $eventold = calendar_event::load($event);
            $eventold->delete();
        }
    }

    /**
     * Requires the user has the override management capability
     */
    public function require_manage_capability(): void {
        require_capability('mod/assign:manageoverrides', $this->context);
    }

    /**
     * Determine whether user can view a given override record
     *
     * @param stdClass $override
     * @param stdClass $course
     * @param stdClass|cm_info $cm
     * @return bool
     */
    private function can_view_override(stdClass $override, stdClass $course, stdClass|cm_info $cm): bool {
        if (!empty($override->groupid)) {
            return groups_group_visible($override->groupid, $course, $cm);
        } else if (!empty($override->userid)) {
            return groups_user_groups_visible($course, $override->userid, $cm);
        }
        return false;
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
                'assignid' => $this->assign->id,
            ],
            'objectid' => $id,
        ];
    }

    /**
     * Log that a given override was deleted
     *
     * @param int $id of assign override that was just deleted
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_deleted_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

        $params = $this->get_base_event_params($id);

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
     * @param int $id of assign override that was just created
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_created_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

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
     * @param int $id of assign override that was just updated
     * @param ?int $userid user attached to override record, or null
     * @param ?int $groupid group attached to override record, or null
     */
    private function fire_updated_event(int $id, ?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

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
     * Clears any overrideable settings in the formdata, where the value matches what is already in the assignment
     * If they match, the data is set to null.
     *
     * @param array $formdata data usually from moodleform or webservice call.
     * @return array formdata with same values cleared
     */
    private function clear_unused_values(array $formdata): array {
        foreach (self::OVERRIDEABLE_ASSIGN_SETTINGS as $key) {
            // If the formdata is the same as the current assignment object data, clear it.
            if (isset($formdata[$key]) && $formdata[$key] == $this->assign->$key) {
                $formdata[$key] = null;
            }

            // Ensure these keys always are set (even if null).
            $formdata[$key] = $formdata[$key] ?? null;

            // This avoids false, or '' into the DB since the override logic expects null for unchanged settings.
            // The value '0' mean the setting is disabled, so do not convert that to null.
            if (empty($formdata[$key]) && $formdata[$key] !== 0) {
                $formdata[$key] = null;
            }
        }

        return $formdata;
    }

    /**
     * Recalculate penalties for user(s) affected by an override.
     *
     * Only recalculates when penalties are enabled in the assignment.
     *
     * @param int|null $userid User ID for user override, or null for group override
     * @param int|null $groupid Group ID for group override, or null for user override
     */
    private function recalculate_penalties(?int $userid = null, ?int $groupid = null): void {
        // Sanity check.
        $this->ensure_userid_xor_groupid_set($userid, $groupid);

        // Only recalculate grades when penalties are enabled.
        if (!penalty_helper::is_penalty_enabled($this->assign->id)) {
            return;
        }

        $assigninstance = clone $this->assign;
        $cm = get_coursemodule_from_instance('assign', $this->assign->id, $this->assign->course, false, MUST_EXIST);
        $assigninstance->cmidnumber = $cm->idnumber;

        if (!empty($userid)) {
            // User override - recalculate for single user.
            assign_update_grades($assigninstance, $userid);
        } else {
            // Group override - recalculate for all group members.
            $groupmembers = groups_get_members($groupid);
            foreach ($groupmembers as $groupmember) {
                assign_update_grades($assigninstance, $groupmember->id);
            }
        }
    }

    /**
     * Delete orphaned group overrides (where the group no longer exists).
     *
     * @return int number of orphaned overrides deleted
     */
    public function delete_orphaned_group_overrides(): int {
        global $DB;

        $sql = 'SELECT o.id
                  FROM {assign_overrides} o
             LEFT JOIN {groups} g ON o.groupid = g.id
                 WHERE o.groupid IS NOT NULL
                       AND g.id IS NULL
                       AND o.assignid = ?';
        $params = [$this->assign->id];
        $orphaned = $DB->get_records_sql($sql, $params);

        if (!empty($orphaned)) {
            $DB->delete_records_list('assign_overrides', 'id', array_keys($orphaned));
            return count($orphaned);
        }

        return 0;
    }

    /**
     * Get group overrides for display in the overrides listing page.
     * Filters by groups the user has access to and includes group names.
     *
     * @param array $groups Array of group objects that the user has access to (keyed by group id)
     * @return array Array of override records with group information
     */
    public function get_group_overrides_for_listing(array $groups): array {
        global $DB;

        if (empty($groups)) {
            return [];
        }

        $params = ['assignid' => $this->assign->id];
        [$insql, $inparams] = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
        $params += $inparams;

        $sql = "SELECT o.*, g.name
                  FROM {assign_overrides} o
                  JOIN {groups} g ON o.groupid = g.id
                 WHERE o.assignid = :assignid AND g.id $insql
              ORDER BY o.sortorder";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get user overrides for display in the overrides listing page.
     * Filters by users in accessible groups if needed and includes user information.
     *
     * @param bool $accessallgroups Whether user can access all groups
     * @param array $groups Array of group objects that the user has access to (keyed by group id)
     * @return array Array of override records with user information
     */
    public function get_user_overrides_for_listing(bool $accessallgroups, array $groups): array {
        global $DB;

        [$sort, $params] = users_order_by_sql('u');
        $params['assignid'] = $this->assign->id;

        $userfieldsapi = user_field::for_name();

        if ($accessallgroups) {
            $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                      FROM {assign_overrides} o
                      JOIN {user} u ON o.userid = u.id
                     WHERE o.assignid = :assignid
                  ORDER BY ' . $sort;

            return $DB->get_records_sql($sql, $params);
        } else if (!empty($groups)) {
            [$insql, $inparams] = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
            $params += $inparams;

            $sql = 'SELECT o.*, ' . $userfieldsapi->get_sql('u', false, '', '', false)->selects . '
                      FROM {assign_overrides} o
                      JOIN {user} u ON o.userid = u.id
                      JOIN {groups_members} gm ON u.id = gm.userid
                     WHERE o.assignid = :assignid AND gm.groupid ' . $insql . '
                  ORDER BY ' . $sort;

            return $DB->get_records_sql($sql, $params);
        }

        return [];
    }

    /**
     * Move a group override up or down in the sort order.
     * Note, capabilities are not checked, {@see require_manage_capability()}
     *
     * @param int $overrideid ID of the override to move
     * @param string $direction Direction to move ('up' or 'down')
     * @return bool true if successful, false otherwise
     * @throws invalid_parameter_exception if parameters are invalid
     */
    public function move_group_override(int $overrideid, string $direction): bool {
        global $DB;

        if (!in_array($direction, ['up', 'down'])) {
            throw new invalid_parameter_exception('Direction must be "up" or "down"');
        }

        // Get the override object.
        $override = $DB->get_record(
            'assign_overrides',
            ['id' => $overrideid, 'assignid' => $this->assign->id],
            'id, sortorder, groupid'
        );

        if (!$override) {
            return false;
        }

        if (empty($override->groupid)) {
            throw new invalid_parameter_exception('Can only move group overrides');
        }

        // Count the number of group overrides.
        $overridecountgroup = $DB->count_records('assign_overrides', ['userid' => null, 'assignid' => $this->assign->id]);

        // Calculate the new sortorder.
        if (($direction == 'up') && ($override->sortorder > 1)) {
            $neworder = $override->sortorder - 1;
        } else if (($direction == 'down') && ($override->sortorder < $overridecountgroup)) {
            $neworder = $override->sortorder + 1;
        } else {
            return false;
        }

        // Retrieve the override object that is currently residing in the new position.
        $params = ['sortorder' => $neworder, 'assignid' => $this->assign->id];
        $swapoverride = $DB->get_record('assign_overrides', $params, 'id, sortorder, groupid');

        if ($swapoverride) {
            // Swap the sortorders.
            $swapoverride->sortorder = $override->sortorder;
            $override->sortorder = $neworder;

            // Update the override records.
            $DB->update_record('assign_overrides', $override);
            $DB->update_record('assign_overrides', $swapoverride);

            // Clear cache for the 2 records we updated above.
            $this->clear_cache_for(null, $override->groupid);
            $this->clear_cache_for(null, $swapoverride->groupid);
        }

        $this->reorder_group_overrides();
        return true;
    }

    /**
     * Reorder the group overrides for the assignment.
     * This ensures sortorder values are sequential starting from 1.
     */
    public function reorder_group_overrides(): void {
        global $DB;

        $i = 1;
        $overrides = $DB->get_records(
            'assign_overrides',
            ['userid' => null, 'assignid' => $this->assign->id],
            'sortorder ASC'
        );

        if ($overrides) {
            foreach ($overrides as $override) {
                $f = new stdClass();
                $f->id = $override->id;
                $f->sortorder = $i++;
                $DB->update_record('assign_overrides', $f);
                $this->clear_cache_for(null, $override->groupid);

                // Update priorities of group overrides.
                $params = [
                    'modulename' => 'assign',
                    'instance' => $override->assignid,
                    'groupid' => $override->groupid,
                ];
                $DB->set_field('event', 'priority', $f->sortorder, $params);
            }
        }
    }
}
