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
 * Privacy Subsystem implementation for block_iomad_commerce.
 *
 * @package    block_iomad_commerce
 * @copyright  2018 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_commerce\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the choice activity module.
 *
 * @copyright  2018 E-Learn Design (http://www.e-learndesign.co.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider {
    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) {
        $items->add_database_table(
            'invoice',
            [
                'id' => 'privacy:metadata:invoice:id',
                'reference' => 'privacy:metadata:invoice:reference',
                'userid' => 'privacy:metadata:invoice:userid',
                'status' => 'privacy:metadata:invoice:status',
                'checkout_method' => 'privacy:metadata:invoice:checkout_method',
                'email' => 'privacy:metadata:invoice:email',
                'phone1' => 'privacy:metadata:invoice:phone1',
                'pp_payerid' => 'privacy:metadata:invoice:pp_payerid',
                'pp_payerstatus' => 'privacy:metadata:invoice:pp_payerstatus',
                'company' => 'privacy:metadata:invoice:company',
                'address' => 'privacy:metadata:invoice:address',
                'city' => 'privacy:metadata:invoice:city',
                'state' => 'privacy:metadata:invoice:state',
                'country' => 'privacy:metadata:invoice:country',
                'postcode' => 'privacy:metadata:invoice:postcode',
                'firstname' => 'privacy:metadata:invoice:firstname',
                'lastname' => 'privacy:metadata:invoice:lastname',
                'pp_ack' => 'privacy:metadata:invoice:pp_ack',
                'pp_transactionid' => 'privacy:metadata:invoice:pp_transactionid',
                'pp_transactiontype' => 'privacy:metadata:invoice:pp_transactiontype',
                'pp_paymenttype' => 'privacy:metadata:invoice:pp_paymenttype',
                'pp_ordertime' => 'privacy:metadata:invoice:pp_ordertime',
                'pp_currencycode' => 'privacy:metadata:invoice:pp_currencycode',
                'pp_amount' => 'privacy:metadata:invoice:pp_amount',
                'pp_feeamt' => 'privacy:metadata:invoice:pp_feeamt',
                'pp_settleamt' => 'privacy:metadata:invoice:pp_settleamt',
                'pp_taxamt' => 'privacy:metadata:invoice:pp_taxamt',
                'pp_exchangerate' => 'privacy:metadata:invoice:pp_exchangerate',
                'pp_paymentstatus' => 'privacy:metadata:invoice:pp_paymentstatus',
                'pp_pendingreason' => 'privacy:metadata:invoice:pp_pendingreason',
                'pp_reason' => 'privacy:metadata:invoice:pp_reason',
                'date' => 'privacy:metadata:invoice:date',
            ],
            'privacy:metadata:invoice'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid($userid) {
        // System context only.
        $sql = "SELECT c.id
                  FROM {context} c
                WHERE contextlevel = :contextlevel";

        $params = [
            'userid'  => $userid,
            'contextlevel'  => CONTEXT_SYSTEM,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $context = context_system::instance();

        // Get the invoice information.
        if ($invoices = $DB->get_records('invoice', array('userid' => $user->id))) {
            foreach ($invoices as $invoice) {
                writer::with_context($context)->export_data($context, $invoice);
            }
        }
    }


    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }
        $DB->delete_records('invoice');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $DB->delete_records('invoice', array('userid' => $userid));
    }
}
