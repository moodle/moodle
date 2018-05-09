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
 * Expired contexts manager for CONTEXT_USER.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use tool_dataprivacy\purpose;
use tool_dataprivacy\context_instance;

defined('MOODLE_INTERNAL') || die();

/**
 * Expired contexts manager for CONTEXT_USER.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_user_contexts extends \tool_dataprivacy\expired_contexts_manager {

    /**
     * Only user level.
     *
     * @return int[]
     */
    protected function get_context_levels() {
        return [CONTEXT_USER];
    }

    /**
     * Returns the user context instances that are expired.
     *
     * @return \stdClass[]
     */
    protected function get_expired_contexts() {
        global $DB;

        // Including context info + last login timestamp.
        $fields = 'ctx.id AS id, ' . \context_helper::get_preload_record_columns_sql('ctx');

        $purpose = api::get_effective_contextlevel_purpose(CONTEXT_USER);

        // Calculate what is considered expired according to the context level effective purpose (= now + retention period).
        $expiredtime = new \DateTime();
        $retention = new \DateInterval($purpose->get('retentionperiod'));
        $expiredtime->sub($retention);

        $sql = "SELECT $fields FROM {context} ctx
                  JOIN {user} u ON ctx.contextlevel = ? AND ctx.instanceid = u.id
                  LEFT JOIN {tool_dataprivacy_ctxexpired} expiredctx ON ctx.id = expiredctx.contextid
                 WHERE u.lastaccess <= ? AND u.lastaccess > 0 AND expiredctx.id IS NULL
                ORDER BY ctx.path, ctx.contextlevel ASC";
        $possiblyexpired = $DB->get_recordset_sql($sql, [CONTEXT_USER, $expiredtime->getTimestamp()]);

        $expiredcontexts = [];
        foreach ($possiblyexpired as $record) {

            \context_helper::preload_from_record($record);

            // No strict checking as the context may already be deleted (e.g. we just deleted a course,
            // module contexts below it will not exist).
            $context = \context::instance_by_id($record->id, false);
            if (!$context) {
                continue;
            }

            if (is_siteadmin($context->instanceid)) {
                continue;
            }

            $courses = enrol_get_users_courses($context->instanceid, false, ['enddate']);
            foreach ($courses as $course) {
                if (!$course->enddate) {
                    // We can not know it what is going on here, so we prefer to be conservative.
                    continue 2;
                }

                if ($course->enddate >= time()) {
                    // Future or ongoing course.
                    continue 2;
                }
            }

            $expiredcontexts[$context->id] = $context;
        }

        return $expiredcontexts;
    }

    /**
     * Deletes user data from the provided context.
     *
     * Overwritten to delete the user.
     *
     * @param \core_privacy\manager $privacymanager
     * @param \tool_dataprivacy\expired_context $expiredctx
     * @return \context|false
     */
    protected function delete_expired_context(\core_privacy\manager $privacymanager, \tool_dataprivacy\expired_context $expiredctx) {
        $context = \context::instance_by_id($expiredctx->get('contextid'), IGNORE_MISSING);
        if (!$context) {
            api::delete_expired_context($expiredctx->get('contextid'));
            return false;
        }

        if (!PHPUNIT_TEST) {
            mtrace('Deleting context ' . $context->id . ' - ' .
                shorten_text($context->get_context_name(true, true)));
        }

        // To ensure that all user data is deleted, instead of deleting by context, we run through and collect any stray
        // contexts for the user that may still exist and call delete_data_for_user().
        $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);
        $approvedlistcollection = new \core_privacy\local\request\contextlist_collection($user->id);
        $contextlistcollection = $privacymanager->get_contexts_for_userid($user->id);

        foreach ($contextlistcollection as $contextlist) {
            $approvedlistcollection->add_contextlist(new \core_privacy\local\request\approved_contextlist(
                $user,
                $contextlist->get_component(),
                $contextlist->get_contextids()
            ));
        }

        $privacymanager->delete_data_for_user($approvedlistcollection);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_CLEANED);

        // Delete the user.
        delete_user($user);

        return $context;
    }
}
