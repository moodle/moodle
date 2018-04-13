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
 * Expired contexts manager for CONTEXT_COURSE, CONTEXT_MODULE and CONTEXT_BLOCK.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use tool_dataprivacy\purpose;

defined('MOODLE_INTERNAL') || die();

/**
 * Expired contexts manager for CONTEXT_COURSE, CONTEXT_MODULE and CONTEXT_BLOCK.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_course_related_contexts extends \tool_dataprivacy\expired_contexts_manager {

    /**
     * Course-related context levels.
     *
     * @return int[]
     */
    protected function get_context_levels() {
        return [CONTEXT_MODULE, CONTEXT_BLOCK, CONTEXT_COURSE];
    }

    /**
     * Returns a recordset with user context instances that are possibly expired (to be confirmed by get_recordset_callback).
     *
     * @return \stdClass[]
     */
    protected function get_expired_contexts() {
        global $DB;

        // Including context info + course end date + purposeid (this last one only if defined).
        $fields = 'ctx.id AS id, ctxcourse.enddate AS courseenddate, dpctx.purposeid AS purposeid, ' .
            \context_helper::get_preload_record_columns_sql('ctx');

        // We want all contexts at course-dependant levels.
        $parentpath = $DB->sql_concat('ctxcourse.path', "'/%'");

        // This SQL query returns all course-dependant contexts (including the course context)
        // which course end date already passed.
        $sql = "SELECT $fields
                  FROM {context} ctx
                  JOIN (
                        SELECT c.enddate, subctx.path
                          FROM {context} subctx
                          JOIN {course} c
                            ON subctx.contextlevel = ? AND subctx.instanceid = c.id
                         WHERE c.enddate < ? AND c.enddate > 0
                       ) ctxcourse
                    ON ctx.path LIKE {$parentpath} OR ctx.path = ctxcourse.path
             LEFT JOIN {tool_dataprivacy_ctxinstance} dpctx
                    ON dpctx.contextid = ctx.id
             LEFT JOIN {tool_dataprivacy_ctxexpired} expiredctx
                    ON ctx.id = expiredctx.contextid
                 WHERE expiredctx.id IS NULL
              ORDER BY ctx.contextlevel DESC, ctx.path";
        $possiblyexpired = $DB->get_recordset_sql($sql, [CONTEXT_COURSE, time()]);

        $expiredcontexts = [];
        $excludedcontextids = [];
        foreach ($possiblyexpired as $record) {

            \context_helper::preload_from_record($record);

            // No strict checking as the context may already be deleted (e.g. we just deleted a course,
            // module contexts below it will not exist).
            $context = \context::instance_by_id($record->id, false);
            if (!$context) {
                continue;
            }

            // We pass the value we just got from SQL so get_effective_context_purpose don't need to query
            // the db again to retrieve it. If there is no tool_dataprovider_ctxinstance record
            // $record->purposeid will be null which is ok as it would force get_effective_context_purpose
            // to return the default purpose for the context context level (no db queries involved).
            $purposevalue = $record->purposeid !== null ? $record->purposeid : context_instance::NOTSET;

            // It should be cheap as system purposes and context level purposes will be retrieved from a cache most of the time.
            $purpose = api::get_effective_context_purpose($context, $purposevalue);

            $dt = new \DateTime();
            $dt->setTimestamp($record->courseenddate);
            $di = new \DateInterval($purpose->get('retentionperiod'));
            $dt->add($di);

            if (time() < $dt->getTimestamp()) {
                // Exclude this context ID as it has not reached the retention period yet.
                $excludedcontextids[] = $context->id;
                continue;
            }

            // Check if this context has children that have not yet expired.
            $hasunexpiredchildren = false;
            $children = $context->get_child_contexts();
            foreach ($children as $child) {
                if (in_array($child->id, $excludedcontextids)) {
                    $hasunexpiredchildren = true;
                    break;
                }
            }
            if ($hasunexpiredchildren) {
                // Exclude this context ID as it has children that have not yet expired.
                $excludedcontextids[] = $context->id;
                continue;
            }

            $expiredcontexts[$context->id] = $context;
        }

        return $expiredcontexts;
    }
}
