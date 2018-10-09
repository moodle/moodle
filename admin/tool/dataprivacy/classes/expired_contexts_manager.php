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
 * Expired contexts manager.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use core_privacy\manager;
use tool_dataprivacy\expired_context;

defined('MOODLE_INTERNAL') || die();

/**
 * Expired contexts manager.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_contexts_manager {

    /**
     * Number of deleted contexts for each scheduled task run.
     */
    const DELETE_LIMIT = 200;

    /** @var progress_trace The log progress tracer */
    protected $progresstracer = null;

    /** @var manager The privacy manager */
    protected $manager = null;

    /**
     * Flag expired contexts as expired.
     *
     * @return  int[]   The number of contexts flagged as expired for courses, and users.
     */
    public function flag_expired_contexts() : array {
        if (!$this->check_requirements()) {
            return [0, 0];
        }

        // Clear old and stale records first.
        static::clear_old_records();

        $data = static::get_nested_expiry_info_for_courses();
        $coursecount = 0;
        foreach ($data as $expiryrecord) {
            if ($this->update_from_expiry_info($expiryrecord)) {
                $coursecount++;
            }
        }

        $data = static::get_nested_expiry_info_for_user();
        $usercount = 0;
        foreach ($data as $expiryrecord) {
            if ($this->update_from_expiry_info($expiryrecord)) {
                $usercount++;
            }
        }

        return [$coursecount, $usercount];
    }

    /**
     * Clear old and stale records.
     */
    protected static function clear_old_records() {
        global $DB;

        $sql = "SELECT dpctx.*
                  FROM {tool_dataprivacy_ctxexpired} dpctx
             LEFT JOIN {context} ctx ON ctx.id = dpctx.contextid
                 WHERE ctx.id IS NULL";

        $orphaned = $DB->get_recordset_sql($sql);
        foreach ($orphaned as $orphan) {
            $expiredcontext = new expired_context(0, $orphan);
            $expiredcontext->delete();
        }

        // Delete any child of a user context.
        $parentpath = $DB->sql_concat('ctxuser.path', "'/%'");
        $params = [
            'contextuser' => CONTEXT_USER,
        ];

        $sql = "SELECT dpctx.*
                  FROM {tool_dataprivacy_ctxexpired} dpctx
                 WHERE dpctx.contextid IN (
                    SELECT ctx.id
                        FROM {context} ctxuser
                        JOIN {context} ctx ON ctx.path LIKE {$parentpath}
                       WHERE ctxuser.contextlevel = :contextuser
                    )";
        $userchildren = $DB->get_recordset_sql($sql, $params);
        foreach ($userchildren as $child) {
            $expiredcontext = new expired_context(0, $child);
            $expiredcontext->delete();
        }
    }

    /**
     * Get the full nested set of expiry data relating to all contexts.
     *
     * @param   string      $contextpath A contexpath to restrict results to
     * @return  \stdClass[]
     */
    protected static function get_nested_expiry_info($contextpath = '') : array {
        $coursepaths = self::get_nested_expiry_info_for_courses($contextpath);
        $userpaths = self::get_nested_expiry_info_for_user($contextpath);

        return array_merge($coursepaths, $userpaths);
    }

    /**
     * Get the full nested set of expiry data relating to course-related contexts.
     *
     * @param   string      $contextpath A contexpath to restrict results to
     * @return  \stdClass[]
     */
    protected static function get_nested_expiry_info_for_courses($contextpath = '') : array {
        global $DB;

        $contextfields = \context_helper::get_preload_record_columns_sql('ctx');
        $expiredfields = expired_context::get_sql_fields('expiredctx', 'expiredctx');
        $purposefields = 'dpctx.purposeid';
        $coursefields = 'ctxcourse.expirydate AS expirydate';
        $fields = implode(', ', ['ctx.id', $contextfields, $expiredfields, $coursefields, $purposefields]);

        // We want all contexts at course-dependant levels.
        $parentpath = $DB->sql_concat('ctxcourse.path', "'/%'");

        // This SQL query returns all course-dependant contexts (including the course context)
        // which course end date already passed.
        // This is ordered by the context path in reverse order, which will give the child nodes before any parent node.
        $params = [
            'contextlevel' => CONTEXT_COURSE,
        ];
        $where = '';

        if (!empty($contextpath)) {
            $where = "WHERE (ctx.path = :pathmatchexact OR ctx.path LIKE :pathmatchchildren)";
            $params['pathmatchexact'] = $contextpath;
            $params['pathmatchchildren'] = "{$contextpath}/%";
        }

        $sql = "SELECT $fields
                  FROM {context} ctx
                  JOIN (
                        SELECT c.enddate AS expirydate, subctx.path
                          FROM {context} subctx
                          JOIN {course} c
                            ON subctx.contextlevel = :contextlevel
                           AND subctx.instanceid = c.id
                           AND c.format != 'site'
                       ) ctxcourse
                    ON ctx.path LIKE {$parentpath} OR ctx.path = ctxcourse.path
             LEFT JOIN {tool_dataprivacy_ctxinstance} dpctx
                    ON dpctx.contextid = ctx.id
             LEFT JOIN {tool_dataprivacy_ctxexpired} expiredctx
                    ON ctx.id = expiredctx.contextid
                 {$where}
              ORDER BY ctx.path DESC";

        return self::get_nested_expiry_info_from_sql($sql, $params);
    }

    /**
     * Get the full nested set of expiry data.
     *
     * @param   string      $contextpath A contexpath to restrict results to
     * @return  \stdClass[]
     */
    protected static function get_nested_expiry_info_for_user($contextpath = '') : array {
        global $DB;

        $contextfields = \context_helper::get_preload_record_columns_sql('ctx');
        $expiredfields = expired_context::get_sql_fields('expiredctx', 'expiredctx');
        $purposefields = 'dpctx.purposeid';
        $userfields = 'u.lastaccess AS expirydate';
        $fields = implode(', ', ['ctx.id', $contextfields, $expiredfields, $userfields, $purposefields]);

        // We want all contexts at user-dependant levels.
        $parentpath = $DB->sql_concat('ctxuser.path', "'/%'");

        // This SQL query returns all user-dependant contexts (including the user context)
        // This is ordered by the context path in reverse order, which will give the child nodes before any parent node.
        $params = [
            'contextlevel' => CONTEXT_USER,
        ];
        $where = '';

        if (!empty($contextpath)) {
            $where = "AND ctx.path = :pathmatchexact";
            $params['pathmatchexact'] = $contextpath;
        }

        $sql = "SELECT $fields, u.deleted AS userdeleted
                  FROM {context} ctx
                  JOIN {user} u ON ctx.instanceid = u.id
             LEFT JOIN {tool_dataprivacy_ctxinstance} dpctx
                    ON dpctx.contextid = ctx.id
             LEFT JOIN {tool_dataprivacy_ctxexpired} expiredctx
                    ON ctx.id = expiredctx.contextid
                 WHERE ctx.contextlevel = :contextlevel {$where}
              ORDER BY ctx.path DESC";

        return self::get_nested_expiry_info_from_sql($sql, $params);
    }

    /**
     * Get the full nested set of expiry data given appropriate SQL.
     * Only contexts which have expired will be included.
     *
     * @param   string      $sql The SQL used to select the nested information.
     * @param   array       $params The params required by the SQL.
     * @return  \stdClass[]
     */
    protected static function get_nested_expiry_info_from_sql(string $sql, array $params) : array {
        global $DB;

        $fulllist = $DB->get_recordset_sql($sql, $params);
        $datalist = [];
        $expiredcontents = [];
        $pathstoskip = [];
        foreach ($fulllist as $record) {
            \context_helper::preload_from_record($record);
            $context = \context::instance_by_id($record->id, false);

            if (!self::is_eligible_for_deletion($pathstoskip, $context)) {
                // We should skip this context, and therefore all of it's children.
                $datalist = array_filter($datalist, function($data, $path) use ($context) {
                    // Remove any child of this context.
                    // Technically this should never be fulfilled because the query is ordered in path DESC, but is kept
                    // in to be certain.
                    return (false === strpos($path, "{$context->path}/"));
                }, ARRAY_FILTER_USE_BOTH);

                if ($record->expiredctxid) {
                    // There was previously an expired context record.
                    // Delete it to be on the safe side.
                    $expiredcontext = new expired_context(null, expired_context::extract_record($record, 'expiredctx'));
                    $expiredcontext->delete();
                }
                continue;
            }

            $purposevalue = $record->purposeid !== null ? $record->purposeid : context_instance::NOTSET;
            $purpose = api::get_effective_context_purpose($context, $purposevalue);

            if ($context instanceof \context_user && !empty($record->userdeleted)) {
                $expiryinfo = static::get_expiry_info($purpose, $record->userdeleted);
            } else {
                $expiryinfo = static::get_expiry_info($purpose, $record->expirydate);
            }
            foreach ($datalist as $path => $data) {
                // Merge with already-processed children.
                if (strpos($path, $context->path) !== 0) {
                    continue;
                }

                $expiryinfo->merge_with_child($data->info);
            }
            $datalist[$context->path] = (object) [
                'context' => $context,
                'record' => $record,
                'purpose' => $purpose,
                'info' => $expiryinfo,
            ];
        }
        $fulllist->close();

        return $datalist;
    }

    /**
     * Check whether the supplied context would be elible for deletion.
     *
     * @param   array       $pathstoskip A set of paths which should be skipped
     * @param   \context    $context
     * @return  bool
     */
    protected static function is_eligible_for_deletion(array &$pathstoskip, \context $context) : bool {
        $shouldskip = false;
        // Check whether any of the child contexts are ineligble.
        $shouldskip = !empty(array_filter($pathstoskip, function($path) use ($context) {
            // If any child context has already been skipped then it will appear in this list.
            // Since paths include parents, test if the context under test appears as the haystack in the skipped
            // context's needle.
            return false !== (strpos($context->path, $path));
        }));

        if (!$shouldskip && $context instanceof \context_user) {
            // The context instanceid is the user's ID.
            if (isguestuser($context->instanceid) || is_siteadmin($context->instanceid)) {
                // This is an admin, or the guest and cannot be deleted.
                $shouldskip = true;
            }

            if (!$shouldskip) {
                $courses = enrol_get_users_courses($context->instanceid, false, ['enddate']);
                $requireenddate = self::require_all_end_dates_for_user_deletion();

                foreach ($courses as $course) {
                    if (empty($course->enddate)) {
                        // This course has no end date.
                        if ($requireenddate) {
                            // Course end dates are required, and this course has no end date.
                            $shouldskip = true;
                            break;
                        }

                        // Course end dates are not required. The subsequent checks are pointless at this time so just
                        // skip them.
                        continue;
                    }

                    if ($course->enddate >= time()) {
                        // This course is still in the future.
                        $shouldskip = true;
                        break;
                    }

                    // This course has an end date which is in the past.
                    if (!self::is_course_expired($course)) {
                        // This course has not expired yet.
                        $shouldskip = true;
                        break;
                    }
                }
            }
        }

        if ($shouldskip) {
            // Add this to the list of contexts to skip for parentage checks.
            $pathstoskip[] = $context->path;
        }

        return !$shouldskip;
    }

    /**
     * Deletes the expired contexts.
     *
     * @return  int[]       The number of deleted contexts.
     */
    public function process_approved_deletions() : array {
        if (!$this->check_requirements()) {
            return [0, 0];
        }

        $expiredcontexts = expired_context::get_records(['status' => expired_context::STATUS_APPROVED]);
        $totalprocessed = 0;
        $usercount = 0;
        $coursecount = 0;
        foreach ($expiredcontexts as $expiredctx) {
            $context = \context::instance_by_id($expiredctx->get('contextid'), IGNORE_MISSING);
            if (empty($context)) {
                // Unable to process this request further.
                // We have no context to delete.
                $expiredctx->delete();
                continue;
            }

            if ($this->delete_expired_context($expiredctx)) {
                if ($context instanceof \context_user) {
                    $usercount++;
                } else {
                    $coursecount++;
                }

                $totalprocessed++;
                if ($totalprocessed >= $this->get_delete_limit()) {
                    break;
                }
            }
        }

        return [$coursecount, $usercount];
    }

    /**
     * Deletes user data from the provided context.
     *
     * @param expired_context $expiredctx
     * @return \context|false
     */
    protected function delete_expired_context(expired_context $expiredctx) {
        $context = \context::instance_by_id($expiredctx->get('contextid'));

        $this->get_progress()->output("Deleting context {$context->id} - " . $context->get_context_name(true, true));

        // Update the expired_context and verify that it is still ready for deletion.
        $expiredctx = $this->update_expired_context($expiredctx);
        if (empty($expiredctx)) {
            $this->get_progress()->output("Context has changed since approval and is no longer pending approval. Skipping", 1);
            return false;
        }

        if (!$expiredctx->can_process_deletion()) {
            // This only happens if the record was updated after being first fetched.
            $this->get_progress()->output("Context has changed since approval and must be re-approved. Skipping", 1);
            $expiredctx->set('status', expired_context::STATUS_EXPIRED);
            $expiredctx->save();

            return false;
        }

        $privacymanager = $this->get_privacy_manager();
        if ($context instanceof \context_user) {
            $this->delete_expired_user_context($expiredctx);
        } else {
            // This context is fully expired - that is that the default retention period has been reached.
            $privacymanager->delete_data_for_all_users_in_context($context);
        }

        // Mark the record as cleaned.
        $expiredctx->set('status', expired_context::STATUS_CLEANED);
        $expiredctx->save();

        return $context;
    }

    /**
     * Deletes user data from the provided user context.
     *
     * @param expired_context $expiredctx
     */
    protected function delete_expired_user_context(expired_context $expiredctx) {
        global $DB;

        $contextid = $expiredctx->get('contextid');
        $context = \context::instance_by_id($contextid);
        $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);

        $privacymanager = $this->get_privacy_manager();

        // Delete all child contexts of the user context.
        $parentpath = $DB->sql_concat('ctxuser.path', "'/%'");

        $params = [
            'contextlevel'  => CONTEXT_USER,
            'contextid'     => $expiredctx->get('contextid'),
        ];

        $fields = \context_helper::get_preload_record_columns_sql('ctx');
        $sql = "SELECT ctx.id, $fields
                  FROM {context} ctxuser
                  JOIN {context} ctx ON ctx.path LIKE {$parentpath}
                 WHERE ctxuser.contextlevel = :contextlevel AND ctxuser.id = :contextid
              ORDER BY ctx.path DESC";

        $children = $DB->get_recordset_sql($sql, $params);
        foreach ($children as $child) {
            \context_helper::preload_from_record($child);
            $context = \context::instance_by_id($child->id);

            $privacymanager->delete_data_for_all_users_in_context($context);
        }
        $children->close();

        // Delete all unprotected data that the user holds.
        $approvedlistcollection = new \core_privacy\local\request\contextlist_collection($user->id);
        $contextlistcollection = $privacymanager->get_contexts_for_userid($user->id);

        foreach ($contextlistcollection as $contextlist) {
            $contextids = [];
            $approvedlistcollection->add_contextlist(new \core_privacy\local\request\approved_contextlist(
                    $user,
                    $contextlist->get_component(),
                    $contextlist->get_contextids()
                ));
        }
        $privacymanager->delete_data_for_user($approvedlistcollection, $this->get_progress());

        // Delete the user context.
        $context = \context::instance_by_id($expiredctx->get('contextid'));
        $privacymanager->delete_data_for_all_users_in_context($context);

        // This user is now fully expired - finish by deleting the user.
        delete_user($user);
    }

    /**
     * Whether end dates are required on all courses in order for a user to be expired from them.
     *
     * @return bool
     */
    protected static function require_all_end_dates_for_user_deletion() : bool {
        $requireenddate = get_config('tool_dataprivacy', 'requireallenddatesforuserdeletion');

        return !empty($requireenddate);
    }

    /**
     * Check that the requirements to start deleting contexts are satisified.
     *
     * @return bool
     */
    protected function check_requirements() {
        if (!data_registry::defaults_set()) {
            return false;
        }
        return true;
    }

    /**
     * Check whether a date is beyond the specified period.
     *
     * @param   string      $period The Expiry Period
     * @param   int         $comparisondate The date for comparison
     * @return  bool
     */
    protected static function has_expired(string $period, int $comparisondate) : bool {
        $dt = new \DateTime();
        $dt->setTimestamp($comparisondate);
        $dt->add(new \DateInterval($period));

        return (time() >= $dt->getTimestamp());
    }

    /**
     * Get the expiry info object for the specified purpose and comparison date.
     *
     * @param   purpose     $purpose The purpose of this context
     * @param   int         $comparisondate The date for comparison
     * @return  expiry_info
     */
    protected static function get_expiry_info(purpose $purpose, int $comparisondate = 0) : expiry_info {
        if (empty($comparisondate)) {
            // The date is empty, therefore this context cannot be considered for automatic expiry.
            $defaultexpired = false;
        } else {
            $defaultexpired = static::has_expired($purpose->get('retentionperiod'), $comparisondate);
        }

        return new expiry_info($defaultexpired);
    }

    /**
     * Update or delete the expired_context from the expiry_info object.
     * This function depends upon the data structure returned from get_nested_expiry_info.
     *
     * If the context is expired in any way, then an expired_context will be returned, otherwise null will be returned.
     *
     * @param   \stdClass   $expiryrecord
     * @return  expired_context|null
     */
    protected function update_from_expiry_info(\stdClass $expiryrecord) {
        if ($expiryrecord->info->is_any_expired()) {
            // The context is expired in some fashion.
            // Create or update as required.
            if ($expiryrecord->record->expiredctxid) {
                $expiredcontext = new expired_context(null, expired_context::extract_record($expiryrecord->record, 'expiredctx'));
                $expiredcontext->update_from_expiry_info($expiryrecord->info);

                if ($expiredcontext->is_complete()) {
                    return null;
                }
            } else {
                $expiredcontext = expired_context::create_from_expiry_info($expiryrecord->context, $expiryrecord->info);
            }

            return $expiredcontext;
        } else {
            // The context is not expired.
            if ($expiryrecord->record->expiredctxid) {
                // There was previously an expired context record, but it is no longer relevant.
                // Delete it to be on the safe side.
                $expiredcontext = new expired_context(null, expired_context::extract_record($expiryrecord->record, 'expiredctx'));
                $expiredcontext->delete();
            }

            return null;
        }
    }

    /**
     * Update the expired context record.
     *
     * Note: You should use the return value as the provided value will be used to fetch data only.
     *
     * @param   expired_context $expiredctx The record to update
     * @return  expired_context|null
     */
    protected function update_expired_context(expired_context $expiredctx) {
        // Fetch the context from the expired_context record.
        $context = \context::instance_by_id($expiredctx->get('contextid'));

        // Fetch the current nested expiry data.
        $expiryrecords = self::get_nested_expiry_info($context->path);

        // Find the current record.
        if (empty($expiryrecords[$context->path])) {
            $expiredctx->delete();
            return null;
        }

        // Refresh the record.
        // Note: Use the returned expiredctx.
        $expiredctx = $this->update_from_expiry_info($expiryrecords[$context->path]);
        if (empty($expiredctx)) {
            return null;
        }

        if (!$context instanceof \context_user) {
            // Where the target context is not a user, we check all children of the context.
            // The expiryrecords array only contains children, fetched from the get_nested_expiry_info call above.
            // No need to check that these _are_ children.
            foreach ($expiryrecords as $expiryrecord) {
                if ($expiryrecord->context->id === $context->id) {
                    // This is record for the context being tested that we checked earlier.
                    continue;
                }

                if (empty($expiryrecord->record->expiredctxid)) {
                    // There is no expired context record for this context.
                    // If there is no record, then this context cannot have been approved for removal.
                    return null;
                }

                // Fetch the expired_context object for this record.
                // This needs to be updated from the expiry_info data too as there may be child changes to consider.
                $expiredcontext = new expired_context(null, expired_context::extract_record($expiryrecord->record, 'expiredctx'));
                $expiredcontext->update_from_expiry_info($expiryrecord->info);
                if (!$expiredcontext->is_complete()) {
                    return null;
                }
            }
        }

        return $expiredctx;
    }

    /**
     * Check whether the course has expired.
     *
     * @param   \stdClass   $course
     * @return  bool
     */
    protected static function is_course_expired(\stdClass $course) : bool {
        $context = \context_course::instance($course->id);
        $expiryrecords = self::get_nested_expiry_info_for_courses($context->path);

        return !empty($expiryrecords[$context->path]) && $expiryrecords[$context->path]->info->is_fully_expired();
    }

    /**
     * Create a new instance of the privacy manager.
     *
     * @return  manager
     */
    protected function get_privacy_manager() : manager {
        if (null === $this->manager) {
            $this->manager = new manager();
            $this->manager->set_observer(new \tool_dataprivacy\manager_observer());
        }

        return $this->manager;
    }

    /**
     * Fetch the limit for the maximum number of contexts to delete in one session.
     *
     * @return  int
     */
    protected function get_delete_limit() : int {
        return self::DELETE_LIMIT;
    }

    /**
     * Get the progress tracer.
     *
     * @return  \progress_trace
     */
    protected function get_progress() : \progress_trace {
        if (null === $this->progresstracer) {
            $this->set_progress(new \text_progress_trace());
        }

        return $this->progresstracer;
    }

    /**
     * Set a specific tracer for the task.
     *
     * @param   \progress_trace $trace
     * @return  $this
     */
    public function set_progress(\progress_trace $trace) : expired_contexts_manager {
        $this->progresstracer = $trace;

        return $this;
    }
}
