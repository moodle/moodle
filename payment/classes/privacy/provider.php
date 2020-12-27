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
 * Privacy Subsystem implementation for core_payment.
 *
 * @package    core_payment
 * @category   privacy
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_payment\helper as payment_helper;

/**
 * Privacy Subsystem implementation for core_payment.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This component has data.
    // We need to return all payment information where the user is
    // listed in the payment.userid field.
    // We may also need to fetch this informtion from individual plugins in some cases.
    // e.g. to fetch the full and other gateway-specific meta-data.
    \core_privacy\local\metadata\provider,

    // This is a subsysytem which provides information to core.
    \core_privacy\local\request\subsystem\provider,

    // This is a subsysytem which provides information to plugins.
    \core_privacy\local\request\subsystem\plugin_provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,

    // This plugin is capable of determining which users have data within it for the plugins it provides data to.
    \core_privacy\local\request\shared_userlist_provider
{

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        // The 'payments' table contains data about payments.
        $collection->add_database_table('payments', [
            'userid'       => 'privacy:metadata:database:payments:userid',
            'amount'       => 'privacy:metadata:database:payments:amount',
            'currency'     => 'privacy:metadata:database:payments:currency',
            'gateway'      => 'privacy:metadata:database:payments:gateway',
            'timecreated'  => 'privacy:metadata:database:payments:timecreated',
            'timemodified' => 'privacy:metadata:database:payments:timemodified',
        ], 'privacy:metadata:database:payments');

        return $collection;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   int $userid The user to search.
     * @return  contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextids = [];
        $payments = $DB->get_recordset('payments', ['userid' => $userid]);
        foreach ($payments as $payment) {
            $contextids[] = \core_privacy\manager::component_class_callback(
                $payment->component,
                consumer_provider::class,
                'get_contextid_for_payment',
                [$payment->paymentarea, $payment->itemid]
            ) ?: SYSCONTEXTID;
        }
        $payments->close();

        $contextlist = new contextlist();

        if (!empty($contextids)) {
            [$insql, $inparams] = $DB->get_in_or_equal(array_unique($contextids), SQL_PARAMS_NAMED);
            $contextlist->add_from_sql("SELECT id FROM {context} WHERE id {$insql}", $inparams);
        }

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $providers = static::get_consumer_providers();

        foreach ($providers as $provider) {
            $provider::get_users_in_context($userlist);
        }

        // Orphaned payments.
        $context = $userlist->get_context();
        if ($context instanceof \context_system) {
            [$notinsql, $notinparams] = $DB->get_in_or_equal($providers, SQL_PARAMS_NAMED, 'param', false);
            $sql = "SELECT p.userid
                      FROM {payments} p
                     WHERE component $notinsql";

            $userlist->add_from_sql('userid', $sql, $notinparams);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $providers = static::get_consumer_providers();

        foreach ($providers as $provider) {
            $provider::export_user_data($contextlist);
        }

        // Orphaned payments.
        if (in_array(SYSCONTEXTID, $contextlist->get_contextids())) {
            [$notinsql, $notinparams] = $DB->get_in_or_equal($providers, SQL_PARAMS_NAMED, 'param', false);
            $params = ['userid' => $contextlist->get_user()->id] + $notinparams;
            $orphanedpayments = $DB->get_records_sql(
                "SELECT *
                   FROM {payments}
                  WHERE userid = :userid AND component $notinsql",
                $params
            );

            foreach ($orphanedpayments as $payment) {
                static::export_payment_data_for_user_in_context(
                    \context_system::instance(),
                    [''],
                    $payment->userid,
                    $payment->component,
                    $payment->paymentarea,
                    $payment->itemid
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        $providers = static::get_consumer_providers();

        foreach ($providers as $provider) {
            $provider::delete_data_for_all_users_in_context($context);
        }

        // Orphaned payments.
        if ($context instanceof \context_system) {
            [$notinsql, $params] = $DB->get_in_or_equal($providers, SQL_PARAMS_NAMED, 'param', false);
            $paymentsql = "SELECT id FROM {payments} WHERE component $notinsql";

            static::delete_data_for_payment_sql($paymentsql, $params);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $providers = static::get_consumer_providers();

        foreach ($providers as $provider) {
            $provider::delete_data_for_user($contextlist);
        }

        // Orphaned payments.
        if (in_array(SYSCONTEXTID, $contextlist->get_contextids())) {
            [$notinsql, $notinparams] = $DB->get_in_or_equal($providers, SQL_PARAMS_NAMED, 'param', false);
            $paymentsql = "SELECT id
                             FROM {payments}
                            WHERE userid = :userid AND component $notinsql";
            $paymentparams = ['userid' => $contextlist->get_user()->id] + $notinparams;

            static::delete_data_for_payment_sql($paymentsql, $paymentparams);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist   $userlist   The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $providers = static::get_consumer_providers();

        foreach ($providers as $provider) {
            $provider::delete_data_for_users($userlist);
        }

        // Orphaned payments.
        if ($userlist->get_context() instanceof \context_system) {
            [$notinsql, $notinparams] = $DB->get_in_or_equal($providers, SQL_PARAMS_NAMED, 'param', false);
            [$usersql, $userparams] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

            $paymentsql = "SELECT id
                             FROM {payments}
                            WHERE component $notinsql AND userid $usersql";
            $paymentparams = $notinparams + $userparams;

            static::delete_data_for_payment_sql($paymentsql, $paymentparams);
        }
    }

    /**
     * Returns the list of plugins that use the payment subsystem and implement the consumer_provider interface.
     *
     * @return string[] provider class names
     */
    private static function get_consumer_providers(): array {
        $providers = [];
        foreach (array_keys(\core_component::get_plugin_types()) as $plugintype) {
            $potentialproviders = \core_component::get_plugin_list_with_class($plugintype, 'privacy\provider');
            foreach ($potentialproviders as $potentialprovider) {
                if (is_a($potentialprovider, consumer_provider::class, true)) {
                    $providers[] = $potentialprovider;
                }
            }
        }
        return $providers;
    }

    /**
     * Export all user data for the specified user, in the specified context.
     *
     * @param \context $context The context that the payment belongs to
     * @param string[] $subpath Sub-path to be used during export
     * @param int $userid User id
     * @param string $component Component name
     * @param string $paymentarea Payment area
     * @param int $itemid An internal identifier that is used by the component
     */
    public static function export_payment_data_for_user_in_context(\context $context, array $subpath, int $userid,
            string $component, string $paymentarea, int $itemid) {
        global $DB;

        $payments = $DB->get_records('payments', [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'userid' => $userid,
        ]);

        foreach ($payments as $payment) {
            $data = (object) [
                'userid'       => transform::user($payment->userid),
                'amount'       => payment_helper::get_cost_as_string($payment->amount, $payment->currency),
                'timecreated'  => transform::datetime($payment->timecreated),
                'timemodified' => transform::datetime($payment->timemodified),
            ];
            $subcontext = array_merge(
                [get_string('payments', 'payment')],
                $subpath,
                ['payment-' . $payment->id]
            );
            writer::with_context($context)->export_data(
                $subcontext,
                $data
            );
            \core_privacy\manager::component_class_callback(
                'paygw_' . $payment->gateway,
                paygw_provider::class,
                'export_payment_data',
                [$context, $subcontext, $payment]
            );
        }
    }

    /**
     * Delete all user data related to the given payments.
     *
     * @param string $paymentsql SQL query that selects payment.id field for the payments
     * @param array $paymentparams Array of parameters for $paymentsql
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams) {
        global $DB;

        \core_privacy\manager::plugintype_class_callback(
            'paygw',
            paygw_provider::class,
            'delete_data_for_payment_sql',
            [$paymentsql, $paymentparams]
        );

        $DB->delete_records_subquery('payments', 'id', 'id', $paymentsql, $paymentparams);
    }
}
