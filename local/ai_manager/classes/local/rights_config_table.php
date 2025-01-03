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

namespace local_ai_manager\local;

use html_writer;
use local_ai_manager\hook\usertable_extend;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Table class representing the table for configuring the rights and roles of users in the AI manager.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rights_config_table extends table_sql {
    /**
     * Constructor.
     *
     * @param string $uniqid a unique id to use for the table
     * @param tenant $tenant the tenant to display the table for
     * @param moodle_url $baseurl the current base url on which the table is being displayed
     * @param array $filterids list of ids to filter
     * @param array $rolefilterids list of role ids to filter
     */
    public function __construct(
            string $uniqid,
            tenant $tenant,
            moodle_url $baseurl,
            array $filterids,
            array $rolefilterids
    ) {
        global $DB;
        parent::__construct($uniqid);
        $this->set_attribute('id', $uniqid);
        $this->define_baseurl($baseurl);
        // Define the list of columns to show.
        $columns = ['checkbox', 'lastname', 'firstname', 'role', 'locked', 'confirmed'];
        $checkboxheader = html_writer::div('', 'rights-table-selection_info', ['id' => 'rights-table-selection_info']);
        $checkboxheader .= html_writer::empty_tag('input', ['type' => 'checkbox', 'id' => 'rights-table-selectall_checkbox']);
        $headers = [
                $checkboxheader,
                get_string('lastname'),
                get_string('firstname'),
                get_string('role', 'local_ai_manager'),
                get_string('locked', 'local_ai_manager'),
                get_string('confirmed', 'local_ai_manager'),
        ];

        $tenantfield = get_config('local_ai_manager', 'tenantcolumn');

        // This is a nightmare concerning performance, but showing the rights config table while also filtering roles does not
        // happen very often, so we should be fine.
        // On the other hand we cannot just apply the filter SQL to the table sql, because there is no SQL way to determine the
        // roles for users who do not have a userinfo record yet.
        $rolewhere = '';
        $roleparams = [];
        if (!empty($rolefilterids)) {

            if($tenantfield !== 'code'){
                $rolefiltersql = "SELECT u.id as userid, ui.role as role FROM {user} u "
                . "LEFT JOIN {local_ai_manager_userinfo} ui ON u.id = ui.userid "
                . "WHERE u.deleted != 1 AND u.suspended != 1 AND " . $tenantfield . " = :tenant";
            } else {
                $rolefiltersql = "SELECT u.id as userid, ui.role as role FROM {user} u "
                . "LEFT JOIN {local_ai_manager_userinfo} ui ON u.id = ui.userid "
                . "LEFT JOIN {company_users} cu ON u.id = cu.userid "
                . "LEFT JOIN {company} c ON c.id = cu.companyid "
                . "WHERE u.deleted != 1 AND u.suspended != 1 AND c.code = :tenant";
            }

            $rolefilterparams = ['tenant' => $tenant->get_sql_identifier()];

            $records = $DB->get_records_sql($rolefiltersql, $rolefilterparams);
            $roleuserids = [];
            foreach ($records as $record) {
                $userinfo = new userinfo($record->userid);
                $role = $record->role === null ? $userinfo->get_default_role() : $record->role;
                if (in_array($role, $rolefilterids)) {
                    $roleuserids[] = $record->userid;
                }
            }
            if (!empty($roleuserids)) {
                [$insql, $roleparams] = $DB->get_in_or_equal($roleuserids, SQL_PARAMS_NAMED);
                $rolewhere = ' AND u.id ' . $insql;
            }
        }

        if($tenantfield !== 'code'){
            $fields = 'u.id as id, lastname, firstname, role, locked, ui.confirmed';
            $from =
                    '{user} u LEFT JOIN {local_ai_manager_userinfo} ui ON u.id = ui.userid';
            $where = 'u.deleted != 1 AND u.suspended != 1 AND ' . $tenantfield . ' = :tenant' . $rolewhere;
        } else {
            $fields = 'u.id as id, lastname, firstname, role, locked, ui.confirmed';
            $from =
                    '{user} u LEFT JOIN {local_ai_manager_userinfo} ui ON u.id = ui.userid LEFT JOIN {company_users} cu ON u.id = cu.userid LEFT JOIN {company} c ON cu.companyid = c.id';
            $where = 'u.deleted != 1 AND u.suspended != 1 AND c.code = :tenant' . $rolewhere;
        }
        $params = array_merge(['tenant' => $tenant->get_sql_identifier()], $roleparams);
        $usertableextend = new usertable_extend($tenant, $columns, $headers, $filterids, $fields, $from, $where, $params);
        \core\di::get(\core\hook\manager::class)->dispatch($usertableextend);

        $this->define_columns($usertableextend->get_columns());
        // Define the titles of columns to show in header.
        $this->define_headers($usertableextend->get_headers());

        $this->no_sorting('checkbox');
        $this->no_sorting('role');
        $this->no_sorting('locked');
        $this->no_sorting('confirmed');
        $this->collapsible(false);
        $this->sortable(true, 'lastname');

        $this->set_sql($usertableextend->get_fields(), $usertableextend->get_from(),
                $usertableextend->get_where() . ' GROUP BY u.id, role, locked, ui.confirmed',
                $usertableextend->get_params());
        // We need to use this because we are using "GROUP BY" which is not being expected by the sql table.
        $this->set_count_sql("SELECT COUNT(*) FROM (SELECT " . $usertableextend->get_fields() . " FROM "
                . $usertableextend->get_from() . " WHERE " . $usertableextend->get_where() .
                " GROUP BY u.id, role, locked, ui.confirmed) AS subquery",
                $usertableextend->get_params());
        parent::setup();
    }

    /**
     * Convert the role identifier to a display name.
     *
     * @param stdClass $value the object containing the information of the current row
     * @return string the resulting string for the role column
     */
    public function col_role(stdClass $value): string {
        $role = $value->role;
        if (empty($role)) {
            $userinfo = new userinfo($value->id);
            $role = $userinfo->get_default_role();
        }
        return get_string(userinfo::get_role_as_string($role), 'local_ai_manager');
    }

    /**
     * Get the icon representing the lockes state.
     *
     * @param stdClass $value the object containing the information of the current row
     * @return string the resulting string for the locked column
     */
    public function col_locked(stdClass $value) {
        if (empty($value->locked)) {
            return '<i class="fa fa-unlock local_ai_manager-green"></i>';
        } else {
            return '<i class="fa fa-lock local_ai_manager-red"></i>';
        }
    }

    /**
     * Get the icon representing the user confirmed state.
     *
     * @param stdClass $value the object containing the information of the current row
     * @return string the resulting string for the confirmed column
     */
    public function col_confirmed($value) {
        if (!empty($value->confirmed)) {
            return '<i class="fa fa-unlock local_ai_manager-green"></i>';
        } else {
            return '<i class="fa fa-lock local_ai_manager-red"></i>';
        }
    }

    #[\Override]
    public function other_cols($column, $row) {
        if ($column === 'checkbox') {
            return '<input type="checkbox" data-userid="' . $row->id . '"/>';
        }
        return null;
    }

    #[\Override]
    public function show_hide_link($column, $index) {
        if ($column === 'checkbox') {
            return '';
        }
        return parent::show_hide_link($column, $index);
    }
}
