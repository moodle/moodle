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
 * Privacy Subsystem implementation for local_iomad.
 *
 * @package    local_iomad
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad\privacy;

use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\writer;
use \context_system;
use \context_user;

defined('MOODLE_INTERNAL') || die();

class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'company_users',
            [
                'companyid' => 'privacy:metadata:company_users:companyid',
                'userid' => 'privacy:metadata:company_users:userid',
                'managertype' => 'privacy:metadata:company_users:managertype',
                'departmentid' => 'privacy:metadata:company_users:departmentid',
                'suspended' => 'privacy:metadata:company_users:suspended',
            ],
            'privacy:metadata:company_users'
        );

        $collection->add_database_table(
            'companylicense_users',
            [
                'licenseid' => 'privacy:metadata:companylicense_users:licenseid',
                'userid' => 'privacy:metadata:companylicense_users:userid',
                'isusing' => 'privacy:metadata:companylicense_users:isusing',
                'timecompleted' => 'privacy:metadata:companylicense_users:timecompleted',
                'score' => 'privacy:metadata:companylicense_users:score',
                'result' => 'privacy:metadata:companylicense_users:result',
                'licensecourseid' => 'privacy:metadata:companylicense_users:licensecourseid',
                'issuedate' => 'privacy:metadata:companylicense_users:issuedate',
                'groupid' => 'privacy:metadata:companylicense_users:groupid',
            ],
            'privacy:metadata:companylicense_users'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
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

        // Get the company information.
        if ($companies = $DB->get_records('company_users', array('userid' => $user->id))) {
            $companiesout = (object) [];
            $companiesout->companies = $companies;
            writer::with_context($context)->export_data(array(get_string('companyusers', 'block_iomad_company_admin')), $companiesout);
        }

        // Get the license allocation information.
        if ($licenses = $DB->get_records('companylicense_users', array('userid' => $user->id))) {
            $licensesout = (object) [];
            foreach ($licenses as $id => $license) {
                if (!empty($license->issuedate)) {
                    $licenses[$id]->issuedate = transform::datetime($license->issuedate);
                }
                if (!empty($license->timecompleted)) {
                    $licenses[$id]->timecompleted = transform::datetime($license->timecompleted);
                }
            }
            $licensesout->licenses = $licenses;
            writer::with_context($context)->export_data(array(get_string('licenseusers', 'block_iomad_company_admin')), $licensesout);
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
        $DB->delete_records('company_users');
        $DB->delete_records('companylicense_users');
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
        $DB->delete_records('company_users', array('userid' => $userid));
        $DB->execute("UPDATE {companylicense_users} SET userid = -1 WHERE userid = :userid",
                      array('userid' => $userid));
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof context_user) {
            return;
        }

        $params = [
            'userid' => $context->id,
            'contextuser' => CONTEXT_USER,
        ];

        $sql = "SELECT cu.userid as userid
                  FROM {company_users} cu
                  JOIN {context} ctx
                       ON ctx.instanceid = cu.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof context_user) {
            $DB->delete_records('company_users', array('userid' => $context->id));
            $DB->execute("UPDATE {companylicense_users} SET userid = -1 WHERE userid = :userid",
                          array('userid' => $userid));
        }
    }
}
