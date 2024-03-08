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

    /** @var \progress_trace Trace tool for logging */
    protected $trace = null;

    /**
     * Constructor for the expired_contexts_manager.
     *
     * @param   \progress_trace $trace
     */
    public function __construct(\progress_trace $trace = null) {
        if (null === $trace) {
            $trace = new \null_progress_trace();
        }

        $this->trace = $trace;
    }

    /**
     * Flag expired contexts as expired.
     *
     * @return  int[]   The number of contexts flagged as expired for courses, and users.
     */
    public function flag_expired_contexts(): array {
        $this->trace->output('Checking requirements');
        if (!$this->check_requirements()) {
            $this->trace->output('Requirements not met. Cannot process expired retentions.', 1);
            return [0, 0];
        }

        // Clear old and stale records first.
        $this->trace->output('Clearing obselete records.', 0);
        static::clear_old_records();
        $this->trace->output('Done.', 1);

        $this->trace->output('Calculating potential course expiries.', 0);
        $data = static::get_nested_expiry_info_for_courses();

        $coursecount = 0;
        $this->trace->output('Updating course expiry data.', 0);
        foreach ($data as $expiryrecord) {
            if ($this->update_from_expiry_info($expiryrecord)) {
                $coursecount++;
            }
        }
        $this->trace->output('Done.', 1);

        $this->trace->output('Calculating potential user expiries.', 0);
        $data = static::get_nested_expiry_info_for_user();

        $usercount = 0;
        $this->trace->output('Updating user expiry data.', 0);
        foreach ($data as $expiryrecord) {
            if ($this->update_from_expiry_info($expiryrecord)) {
                $usercount++;
            }
        }
        $this->trace->output('Done.', 1);

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
    protected static function get_nested_expiry_info($contextpath = ''): array {
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
    protected static function get_nested_expiry_info_for_courses($contextpath = ''): array {
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
    protected static function get_nested_expiry_info_for_user($contextpath = ''): array {
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
    protected static function get_nested_expiry_info_from_sql(string $sql, array $params): array {
        global $DB;

        $fulllist = $DB->get_recordset_sql($sql, $params);
        $datalist = [];
        $expiredcontents = [];
        $pathstoskip = [];

        $userpurpose = data_registry::get_effective_contextlevel_value(CONTEXT_USER, 'purpose');
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

            if ($context instanceof \context_user) {
                $purpose = $userpurpose;
            } else {
                $purposevalue = $record->purposeid !== null ? $record->purposeid : context_instance::NOTSET;
                $purpose = api::get_effective_context_purpose($context, $purposevalue);
            }

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
    protected static function is_eligible_for_deletion(array &$pathstoskip, \context $context): bool {
        $shouldskip = false;
        // Check whether any of the child contexts are ineligble.
        $shouldskip = !empty(array_filter($pathstoskip, function($path) use ($context) {
            // If any child context has already been skipped then it will appear in this list.
            // Since paths include parents, test if the context under test appears as the haystack in the skipped
            // context's needle.
            return false !== (strpos($context->path, $path));
        }));

        if (!$shouldskip && $context instanceof \context_user) {
            $shouldskip = !self::are_user_context_dependencies_expired($context);
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
    public function process_approved_deletions(): array {
        $this->trace->output('Checking requirements');
        if (!$this->check_requirements()) {
            $this->trace->output('Requirements not met. Cannot process expired retentions.', 1);
            return [0, 0];
        }

        $this->trace->output('Fetching all approved and expired contexts for deletion.');
        $expiredcontexts = expired_context::get_records(['status' => expired_context::STATUS_APPROVED]);
        $this->trace->output('Done.', 1);
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

            $this->trace->output("Deleting data for " . $context->get_context_name(), 2);
            if ($this->delete_expired_context($expiredctx)) {
                $this->trace->output("Done.", 3);
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
        if ($expiredctx->is_fully_expired()) {
            if ($context instanceof \context_user) {
                $this->delete_expired_user_context($expiredctx);
            } else {
                // This context is fully expired - that is that the default retention period has been reached, and there are
                // no remaining overrides.
                $privacymanager->delete_data_for_all_users_in_context($context);
            }

            // Mark the record as cleaned.
            $expiredctx->set('status', expired_context::STATUS_CLEANED);
            $expiredctx->save();

            return $context;
        }

        // We need to find all users in the context, and delete just those who have expired.
        $collection = $privacymanager->get_users_in_context($context);

        // Apply the expired and unexpired filters to remove the users in these categories.
        $userassignments = $this->get_role_users_for_expired_context($expiredctx, $context);
        $approvedcollection = new \core_privacy\local\request\userlist_collection($context);
        foreach ($collection as $pendinguserlist) {
            $userlist = filtered_userlist::create_from_userlist($pendinguserlist);
            $userlist->apply_expired_context_filters($userassignments->expired, $userassignments->unexpired);
            if (count($userlist)) {
                $approvedcollection->add_userlist($userlist);
            }
        }

        if (count($approvedcollection)) {
            // Perform the deletion with the newly approved collection.
            $privacymanager->delete_data_for_users_in_context($approvedcollection);
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
    protected static function require_all_end_dates_for_user_deletion(): bool {
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
    protected static function has_expired(string $period, int $comparisondate): bool {
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
    protected static function get_expiry_info(purpose $purpose, int $comparisondate = 0): expiry_info {
        $overrides = $purpose->get_purpose_overrides();
        $expiredroles = $unexpiredroles = [];
        if (empty($overrides)) {
            // There are no overrides for this purpose.
            if (empty($comparisondate)) {
                // The date is empty, therefore this context cannot be considered for automatic expiry.
                $defaultexpired = false;
            } else {
                $defaultexpired = static::has_expired($purpose->get('retentionperiod'), $comparisondate);
            }

            return new expiry_info($defaultexpired, $purpose->get('protected'), [], [], []);
        } else {
            $protectedroles = [];
            foreach ($overrides as $override) {
                if (static::has_expired($override->get('retentionperiod'), $comparisondate)) {
                    // This role has expired.
                    $expiredroles[] = $override->get('roleid');
                } else {
                    // This role has not yet expired.
                    $unexpiredroles[] = $override->get('roleid');

                    if ($override->get('protected')) {
                        $protectedroles[$override->get('roleid')] = true;
                    }
                }
            }

            $defaultexpired = false;
            if (static::has_expired($purpose->get('retentionperiod'), $comparisondate)) {
                $defaultexpired = true;
            }

            if ($defaultexpired) {
                $expiredroles = [];
            }

            return new expiry_info($defaultexpired, $purpose->get('protected'), $expiredroles, $unexpiredroles, $protectedroles);
        }
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
        if ($isanyexpired = $expiryrecord->info->is_any_expired()) {
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

            if ($expiryrecord->context instanceof \context_user) {
                $userassignments = $this->get_role_users_for_expired_context($expiredcontext, $expiryrecord->context);
                if (!empty($userassignments->unexpired)) {
                    $expiredcontext->delete();

                    return null;
                }
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
     * Get the list of actual users for the combination of expired, and unexpired roles.
     *
     * @param   expired_context $expiredctx
     * @param   \context        $context
     * @return  \stdClass
     */
    protected function get_role_users_for_expired_context(expired_context $expiredctx, \context $context): \stdClass {
        $expiredroles = $expiredctx->get('expiredroles');
        $expiredroleusers = [];
        if (!empty($expiredroles)) {
            // Find the list of expired role users.
            $expiredroleuserassignments = get_role_users($expiredroles, $context, true, 'ra.id, u.id AS userid', 'ra.id');
            $expiredroleusers = array_map(function($assignment) {
                return $assignment->userid;
            }, $expiredroleuserassignments);
        }
        $expiredroleusers = array_unique($expiredroleusers);

        $unexpiredroles = $expiredctx->get('unexpiredroles');
        $unexpiredroleusers = [];
        if (!empty($unexpiredroles)) {
            // Find the list of unexpired role users.
            $unexpiredroleuserassignments = get_role_users($unexpiredroles, $context, true, 'ra.id, u.id AS userid', 'ra.id');
            $unexpiredroleusers = array_map(function($assignment) {
                return $assignment->userid;
            }, $unexpiredroleuserassignments);
        }
        $unexpiredroleusers = array_unique($unexpiredroleusers);

        if (!$expiredctx->get('defaultexpired')) {
            $tofilter = get_users_roles($context, $expiredroleusers);
            $tofilter = array_filter($tofilter, function($userroles) use ($expiredroles) {
                // Each iteration contains the list of role assignment for a specific user.
                // All roles that the user holds must match those in the list of expired roles.
                foreach ($userroles as $ra) {
                    if (false === array_search($ra->roleid, $expiredroles)) {
                        // This role was not found in the list of assignments.
                        return true;
                    }
                }

                return false;
            });
            $unexpiredroleusers = array_merge($unexpiredroleusers, array_keys($tofilter));
        }

        return (object) [
            'expired' => $expiredroleusers,
            'unexpired' => $unexpiredroleusers,
        ];
    }

    /**
     * Determine whether the supplied context has expired.
     *
     * @param   \context    $context
     * @return  bool
     */
    public static function is_context_expired(\context $context): bool {
        $parents = $context->get_parent_contexts(true);
        foreach ($parents as $parent) {
            if ($parent instanceof \context_course) {
                // This is a context within a course. Check whether _this context_ is expired as a function of a course.
                return self::is_course_context_expired($context);
            }

            if ($parent instanceof \context_user) {
                // This is a context within a user. Check whether the _user_ has expired.
                return self::are_user_context_dependencies_expired($parent);
            }
        }

        return false;
    }

    /**
     * Check whether the course has expired.
     *
     * @param   \stdClass   $course
     * @return  bool
     */
    protected static function is_course_expired(\stdClass $course): bool {
        $context = \context_course::instance($course->id);

        return self::is_course_context_expired($context);
    }

    /**
     * Determine whether the supplied course-related context has expired.
     * Note: This is not necessarily a _course_ context, but a context which is _within_ a course.
     *
     * @param   \context        $context
     * @return  bool
     */
    protected static function is_course_context_expired(\context $context): bool {
        $expiryrecords = self::get_nested_expiry_info_for_courses($context->path);

        return !empty($expiryrecords[$context->path]) && $expiryrecords[$context->path]->info->is_fully_expired();
    }

    /**
     * Determine whether the supplied user context's dependencies have expired.
     *
     * This checks whether courses have expired, and some other check, but does not check whether the user themself has expired.
     *
     * Although this seems unusual at first, each location calling this actually checks whether the user is elgible for
     * deletion, irrespective if they have actually expired.
     *
     * For example, a request to delete the user only cares about course dependencies and the user's lack of expiry
     * should not block their own request to be deleted; whilst the expiry eligibility check has already tested for the
     * user being expired.
     *
     * @param   \context_user   $context
     * @return  bool
     */
    protected static function are_user_context_dependencies_expired(\context_user $context): bool {
        // The context instanceid is the user's ID.
        if (isguestuser($context->instanceid) || is_siteadmin($context->instanceid)) {
            // This is an admin, or the guest and cannot expire.
            return false;
        }

        $courses = enrol_get_users_courses($context->instanceid, false, ['enddate']);
        $requireenddate = self::require_all_end_dates_for_user_deletion();

        $expired = true;

        foreach ($courses as $course) {
            if (empty($course->enddate)) {
                // This course has no end date.
                if ($requireenddate) {
                    // Course end dates are required, and this course has no end date.
                    $expired = false;
                    break;
                }

                // Course end dates are not required. The subsequent checks are pointless at this time so just
                // skip them.
                continue;
            }

            if ($course->enddate >= time()) {
                // This course is still in the future.
                $expired = false;
                break;
            }

            // This course has an end date which is in the past.
            if (!self::is_course_expired($course)) {
                // This course has not expired yet.
                $expired = false;
                break;
            }
        }

        return $expired;
    }

    /**
     * Determine whether the supplied context has expired or unprotected for the specified user.
     *
     * @param   \context    $context
     * @param   \stdClass   $user
     * @return  bool
     */
    public static function is_context_expired_or_unprotected_for_user(\context $context, \stdClass $user): bool {
        // User/course contexts can't expire if no purpose is set in the system context.
        if (!data_registry::defaults_set()) {
            return false;
        }

        $parents = $context->get_parent_contexts(true);
        foreach ($parents as $parent) {
            if ($parent instanceof \context_course) {
                // This is a context within a course. Check whether _this context_ is expired as a function of a course.
                return self::is_course_context_expired_or_unprotected_for_user($context, $user);
            }

            if ($parent instanceof \context_user) {
                // This is a context within a user. Check whether the _user_ has expired.
                return self::are_user_context_dependencies_expired($parent);
            }
        }

        return false;
    }

    /**
     * Determine whether the supplied course-related context has expired, or is unprotected.
     * Note: This is not necessarily a _course_ context, but a context which is _within_ a course.
     *
     * @param   \context        $context
     * @param   \stdClass       $user
     * @return  bool
     */
    protected static function is_course_context_expired_or_unprotected_for_user(\context $context, \stdClass $user) {

        if ($context->get_course_context()->instanceid == SITEID) {
            // The is an activity in the site course (front page).
            $purpose = data_registry::get_effective_contextlevel_value(CONTEXT_SYSTEM, 'purpose');
            $info = static::get_expiry_info($purpose);

        } else {
            $expiryrecords = self::get_nested_expiry_info_for_courses($context->path);
            $info = $expiryrecords[$context->path]->info;
        }

        if ($info->is_fully_expired()) {
            // This context is fully expired.
            return true;
        }

        // Now perform user checks.
        $userroles = array_map(function($assignment) {
            return $assignment->roleid;
        }, get_user_roles($context, $user->id));

        $unexpiredprotectedroles = $info->get_unexpired_protected_roles();
        if (!empty(array_intersect($unexpiredprotectedroles, $userroles))) {
            // The user holds an unexpired and protected role.
            return false;
        }

        $unprotectedoverriddenroles = $info->get_unprotected_overridden_roles();
        $matchingroles = array_intersect($unprotectedoverriddenroles, $userroles);
        if (!empty($matchingroles)) {
            // This user has at least one overridden role which is not a protected.
            // However, All such roles must match.
            // If the user has multiple roles then all must be expired, otherwise we should fall back to the default behaviour.
            if (empty(array_diff($userroles, $unprotectedoverriddenroles))) {
                // All roles that this user holds are a combination of expired, or unprotected.
                return true;
            }
        }

        if ($info->is_default_expired()) {
            // If the user has no unexpired roles, and the context is expired by default then this must be expired.
            return true;
        }

        return !$info->is_default_protected();
    }

    /**
     * Create a new instance of the privacy manager.
     *
     * @return  manager
     */
    protected function get_privacy_manager(): manager {
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
    protected function get_delete_limit(): int {
        return self::DELETE_LIMIT;
    }

    /**
     * Get the progress tracer.
     *
     * @return  \progress_trace
     */
    protected function get_progress(): \progress_trace {
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
    public function set_progress(\progress_trace $trace): expired_contexts_manager {
        $this->progresstracer = $trace;

        return $this;
    }
}
