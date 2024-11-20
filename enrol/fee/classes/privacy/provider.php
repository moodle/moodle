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
 * Privacy Subsystem implementation for enrol_fee.
 *
 * @package    enrol_fee
 * @category   privacy
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_fee\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_payment\helper as payment_helper;

/**
 * Privacy Subsystem for enrol_fee implementing null_provider.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\null_provider,
    \core_payment\privacy\consumer_provider
{
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    public static function get_contextid_for_payment(string $paymentarea, int $itemid): ?int {
        global $DB;

        $sql = "SELECT ctx.id
                  FROM {enrol} e
                  JOIN {context} ctx ON (e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse)
                 WHERE e.id = :enrolid AND e.enrol = :enrolname";
        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'enrolid' => $itemid,
            'enrolname' => 'fee',
        ];
        $contextid = $DB->get_field_sql($sql, $params);

        return $contextid ?: null;
    }

    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_course) {
            $sql = "SELECT p.userid
                      FROM {payments} p
                      JOIN {enrol} e ON (p.component = :component AND p.itemid = e.id)
                     WHERE e.courseid = :courseid";
            $params = [
                'component' => 'enrol_fee',
                'courseid' => $context->instanceid,
            ];
            $userlist->add_from_sql('userid', $sql, $params);
        } else if ($context instanceof \context_system) {
            // If context is system, then the enrolment belongs to a deleted enrolment.
            $sql = "SELECT p.userid
                      FROM {payments} p
                 LEFT JOIN {enrol} e ON p.itemid = e.id
                     WHERE p.component = :component AND e.id IS NULL";
            $params = [
                'component' => 'enrol_fee',
            ];
            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $subcontext = [
            get_string('pluginname', 'enrol_fee'),
        ];
        foreach ($contextlist as $context) {
            if (!$context instanceof \context_course) {
                continue;
            }
            $feeplugins = $DB->get_records('enrol', ['courseid' => $context->instanceid, 'enrol' => 'fee']);

            foreach ($feeplugins as $feeplugin) {
                \core_payment\privacy\provider::export_payment_data_for_user_in_context(
                    $context,
                    $subcontext,
                    $contextlist->get_user()->id,
                    'enrol_fee',
                    'fee',
                    $feeplugin->id
                );
            }
        }

        if (in_array(SYSCONTEXTID, $contextlist->get_contextids())) {
            // Orphaned payments.
            $sql = "SELECT p.*
                      FROM {payments} p
                 LEFT JOIN {enrol} e ON p.itemid = e.id
                     WHERE p.userid = :userid AND p.component = :component AND e.id IS NULL";
            $params = [
                'component' => 'enrol_fee',
                'userid' => $contextlist->get_user()->id,
            ];

            $orphanedpayments = $DB->get_recordset_sql($sql, $params);
            foreach ($orphanedpayments as $payment) {
                \core_payment\privacy\provider::export_payment_data_for_user_in_context(
                    \context_system::instance(),
                    $subcontext,
                    $payment->userid,
                    $payment->component,
                    $payment->paymentarea,
                    $payment->itemid
                );
            }
            $orphanedpayments->close();
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_course) {
            $sql = "SELECT p.id
                      FROM {payments} p
                      JOIN {enrol} e ON (p.component = :component AND p.itemid = e.id)
                     WHERE e.courseid = :courseid";
            $params = [
                'component' => 'enrol_fee',
                'courseid' => $context->instanceid,
            ];

            \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);
        } else if ($context instanceof \context_system) {
            // If context is system, then the enrolment belongs to a deleted enrolment.
            $sql = "SELECT p.id
                      FROM {payments} p
                 LEFT JOIN {enrol} e ON p.itemid = e.id
                     WHERE p.component = :component AND e.id IS NULL";
            $params = [
                'component' => 'enrol_fee',
            ];

            \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $contexts = $contextlist->get_contexts();

        $courseids = [];
        foreach ($contexts as $context) {
            if ($context instanceof \context_course) {
                $courseids[] = $context->instanceid;
            }
        }

        [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        $sql = "SELECT p.id
                  FROM {payments} p
                  JOIN {enrol} e ON (p.component = :component AND p.itemid = e.id)
                 WHERE p.userid = :userid AND e.courseid $insql";
        $params = $inparams + [
            'component' => 'enrol_fee',
            'userid' => $contextlist->get_user()->id,
        ];

        \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);

        if (in_array(SYSCONTEXTID, $contextlist->get_contextids())) {
            // Orphaned payments.
            $sql = "SELECT p.id
                      FROM {payments} p
                 LEFT JOIN {enrol} e ON p.itemid = e.id
                     WHERE p.component = :component AND p.userid = :userid AND e.id IS NULL";
            $params = [
                'component' => 'enrol_fee',
                'userid' => $contextlist->get_user()->id,
            ];

            \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_course) {
            [$usersql, $userparams] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $sql = "SELECT p.id
                      FROM {payments} p
                      JOIN {enrol} e ON (p.component = :component AND p.itemid = e.id)
                     WHERE e.courseid = :courseid AND p.userid $usersql";
            $params = $userparams + [
                'component' => 'enrol_fee',
                'courseid' => $context->instanceid,
            ];

            \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);
        } else if ($context instanceof \context_system) {
            // Orphaned payments.
            [$usersql, $userparams] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $sql = "SELECT p.id
                      FROM {payments} p
                 LEFT JOIN {enrol} e ON p.itemid = e.id
                     WHERE p.component = :component AND p.userid $usersql AND e.id IS NULL";
            $params = $userparams + [
                'component' => 'enrol_fee',
            ];

            \core_payment\privacy\provider::delete_data_for_payment_sql($sql, $params);
        }
    }
}
