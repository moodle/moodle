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

namespace core_admin\reportbuilder\local\systemreports;

use core_admin\reportbuilder\local\filters\courserole;
use core\context\system;
use core_cohort\reportbuilder\local\entities\cohort;
use core_cohort\reportbuilder\local\entities\cohort_member;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\helpers\user_profile_fields;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\system_report;
use core_role\reportbuilder\local\entities\role;
use core_user\fields;
use lang_string;
use moodle_url;
use pix_icon;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->dirroot.'/user/lib.php');

/**
 * Browse users system report class implementation
 *
 * @package    core_admin
 * @copyright  2023 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        global $CFG;

        // Our main entity, it contains all of the column definitions that we need.
        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');

        $this->set_main_table('user', $entityuseralias);
        $this->add_entity($entityuser);

        // Any columns required by actions should be defined here to ensure they're always available.
        $fullnamefields = array_map(fn($field) => "{$entityuseralias}.{$field}", fields::get_name_fields());
        $this->add_base_fields("{$entityuseralias}.id, {$entityuseralias}.confirmed, {$entityuseralias}.mnethostid,
            {$entityuseralias}.suspended, {$entityuseralias}.username, " . implode(', ', $fullnamefields));

        if ($this->get_parameter('withcheckboxes', false, PARAM_BOOL)) {
            $canviewfullnames = has_capability('moodle/site:viewfullnames', \context_system::instance());
            $this->set_checkbox_toggleall(static function(\stdClass $row) use ($canviewfullnames): array {
                return [$row->id, fullname($row, $canviewfullnames)];
            });
        }

        $paramguest = database::generate_param_name();
        $this->add_base_condition_sql("{$entityuseralias}.deleted <> 1 AND {$entityuseralias}.id <> :{$paramguest}",
            [$paramguest => $CFG->siteguest]);

        $entitycohortmember = new cohort_member();
        $entitycohortmemberalias = $entitycohortmember->get_table_alias('cohort_members');
        $this->add_entity($entitycohortmember
            ->add_joins($entitycohortmember->get_joins())
            ->add_join("LEFT JOIN {cohort_members} {$entitycohortmemberalias}
                ON {$entityuseralias}.id = {$entitycohortmemberalias}.userid")
        );

        $entitycohort = new cohort();
        $entitycohortalias = $entitycohort->get_table_alias('cohort');
        $this->add_entity($entitycohort
            ->add_joins($entitycohort->get_joins())
            ->add_joins($entitycohortmember->get_joins())
            ->add_join("LEFT JOIN {cohort} {$entitycohortalias}
                ON {$entitycohortalias}.id = {$entitycohortmemberalias}.cohortid")
        );

        // Join the role entity (Needed for the system role filter).
        $roleentity = new role();
        $role = $roleentity->get_table_alias('role');
        $this->add_entity($roleentity
            ->add_join("LEFT JOIN (
                SELECT DISTINCT r0.id, ras.userid
                FROM {role} r0
                JOIN {role_assignments} ras ON ras.roleid = r0.id
                WHERE ras.contextid = ".SYSCONTEXTID."
             ) {$role} ON {$role}.userid = {$entityuseralias}.id")
        );

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        // Set if report can be downloaded.
        $this->set_downloadable(true);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_any_capability(['moodle/user:update', 'moodle/user:delete'], system::instance());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    public function add_columns(): void {
        $entityuser = $this->get_entity('user');
        $entityuseralias = $entityuser->get_table_alias('user');

        $this->add_column($entityuser->get_column('fullnamewithpicturelink'));

        // Include identity field columns.
        $identitycolumns = $entityuser->get_identity_columns($this->get_context());
        foreach ($identitycolumns as $identitycolumn) {
            $this->add_column($identitycolumn);
        }

        // Add "Last access" column.
        $this->add_column(($entityuser->get_column('lastaccess'))
            ->set_callback(static function ($value, \stdClass $row): string {
                if ($row->lastaccess) {
                    return format_time(time() - $row->lastaccess);
                }
                return get_string('never');
            })
        );

        if ($column = $this->get_column('user:fullnamewithpicturelink')) {
            $column
                ->add_fields("{$entityuseralias}.suspended, {$entityuseralias}.confirmed")
                ->add_callback(static function(string $fullname, \stdClass $row): string {
                    if ($row->suspended) {
                        $fullname .= ' ' . \html_writer::tag('span', get_string('suspended', 'moodle'),
                            ['class' => 'badge badge-secondary ml-1']);
                    }
                    if (!$row->confirmed) {
                        $fullname .= ' ' . \html_writer::tag('span', get_string('confirmationpending', 'admin'),
                            ['class' => 'badge badge-danger ml-1']);
                    }
                    return $fullname;
                });
        }

        $this->set_initial_sort_column('user:fullnamewithpicturelink', SORT_ASC);
        $this->set_default_no_results_notice(new lang_string('nousersfound', 'moodle'));
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $entityuser = $this->get_entity('user');
        $entityuseralias = $entityuser->get_table_alias('user');

        $filters = [
            'user:fullname',
            'user:firstname',
            'user:lastname',
            'user:username',
            'user:idnumber',
            'user:email',
            'user:department',
            'user:institution',
            'user:city',
            'user:country',
            'user:confirmed',
            'user:suspended',
            'user:timecreated',
            'user:lastaccess',
            'user:timemodified',
            'user:auth',
            'user:lastip',
            'cohort:idnumber',
            'role:name',
        ];
        $this->add_filters_from_entities($filters);

        // Enrolled in any course filter.
        $ue = database::generate_alias();
        [$now1, $now2] = database::generate_param_names(2);
        $now = time();
        $sql = "CASE WHEN ({$entityuseralias}.id IN (
            SELECT userid FROM {user_enrolments} {$ue}
            WHERE {$ue}.status = " . ENROL_USER_ACTIVE . "
            AND ({$ue}.timestart = 0 OR {$ue}.timestart < :{$now1})
            AND ({$ue}.timeend = 0 OR {$ue}.timeend > :{$now2})
            )) THEN 1 ELSE 0 END";

        $this->add_filter((new filter(
            boolean_select::class,
            'enrolledinanycourse',
            new lang_string('anycourses', 'filters'),
            $this->get_entity('user')->get_entity_name(),
        ))
            ->set_field_sql($sql, [
                $now1 => $now,
                $now2 => $now,
            ])
        );

        // Course role filter.
        $this->add_filter((new filter(
            courserole::class,
            'courserole',
            new lang_string('courserole', 'filters'),
            $this->get_entity('user')->get_entity_name(),
        ))
            ->set_field_sql("{$entityuseralias}.id")
        );

        // Add user profile fields filters.
        $userprofilefields = new user_profile_fields($entityuseralias . '.id', $entityuser->get_entity_name());
        foreach ($userprofilefields->get_filters() as $filter) {
            $this->add_filter($filter);
        }

        // Set options for system role filter.
        if ($filter = $this->get_filter('role:name')) {
            $filter
                ->set_header(new lang_string('globalrole', 'role'))
                ->set_options(get_assignable_roles(system::instance()));
        }
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {
        global $DB, $USER;

        $contextsystem = system::instance();

        // Action to edit users.
        $this->add_action((new action(
            new moodle_url('/user/editadvanced.php', ['id' => ':id', 'course' => get_site()->id]),
            new pix_icon('t/edit', ''),
            [],
            false,
            new lang_string('edit', 'moodle'),
        ))->add_callback(static function(\stdclass $row) use ($USER, $contextsystem): bool {
            return has_capability('moodle/user:update', $contextsystem) && (is_siteadmin($USER) || !is_siteadmin($row));
        }));

        // Action to suspend users (non mnet remote users).
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['suspend' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/show', ''),
            [],
            false,
            new lang_string('suspenduser', 'admin'),
        ))->add_callback(static function(\stdclass $row) use ($USER, $contextsystem): bool {
            return has_capability('moodle/user:update', $contextsystem) && !$row->suspended && !is_mnet_remote_user($row) &&
                !($row->id == $USER->id || is_siteadmin($row));
        }));

        // Action to unsuspend users (non mnet remote users).
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['unsuspend' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/hide', ''),
            [],
            false,
            new lang_string('unsuspenduser', 'admin'),
        ))->add_callback(static function(\stdclass $row) use ($USER, $contextsystem): bool {
            return has_capability('moodle/user:update', $contextsystem) && $row->suspended && !is_mnet_remote_user($row) &&
                !($row->id == $USER->id || is_siteadmin($row));
        }));

        // Action to unlock users (non mnet remote users).
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['unlock' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/unlock', ''),
            [],
            false,
            new lang_string('unlockaccount', 'admin'),
        ))->add_callback(static function(\stdclass $row) use ($contextsystem): bool {
            return has_capability('moodle/user:update', $contextsystem) && !is_mnet_remote_user($row) &&
                login_is_lockedout($row);
        }));

        // Action to suspend users (mnet remote users).
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['acl' => ':id', 'sesskey' => sesskey(), 'accessctrl' => 'deny']),
            new pix_icon('t/show', ''),
            [],
            false,
            new lang_string('denyaccess', 'mnet'),
        ))->add_callback(static function(\stdclass $row) use ($DB, $contextsystem): bool {
            if (!$accessctrl = $DB->get_field(table: 'mnet_sso_access_control', return: 'accessctrl',
                conditions: ['username' => $row->username, 'mnet_host_id' => $row->mnethostid]
            )) {
                $accessctrl = 'allow';
            }

            return has_capability('moodle/user:update', $contextsystem) && !$row->suspended &&
                is_mnet_remote_user($row) && $accessctrl == 'allow';
        }));

        // Action to unsuspend users (mnet remote users).
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['acl' => ':id', 'sesskey' => sesskey(), 'accessctrl' => 'allow']),
            new pix_icon('t/hide', ''),
            [],
            false,
            new lang_string('allowaccess', 'mnet'),
        ))->add_callback(static function(\stdclass $row) use ($DB, $contextsystem): bool {
            if (!$accessctrl = $DB->get_field(table: 'mnet_sso_access_control', return: 'accessctrl',
                conditions: ['username' => $row->username, 'mnet_host_id' => $row->mnethostid]
            )) {
                $accessctrl = 'allow';
            }

            return has_capability('moodle/user:update', $contextsystem) && !$row->suspended &&
                is_mnet_remote_user($row) && $accessctrl == 'deny';
        }));

        // Action to delete users.
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['delete' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/delete', ''),
            [
                'class' => 'text-danger',
                'data-modal' => 'confirmation',
                'data-modal-title-str' => json_encode(['deleteuser', 'admin']),
                'data-modal-content-str' => ':deletestr',
                'data-modal-yes-button-str' => json_encode(['delete', 'core']),
                'data-modal-destination' => ':deleteurl',

            ],
            false,
            new lang_string('delete', 'moodle'),
        ))->add_callback(static function(\stdclass $row) use ($USER, $contextsystem): bool {

            // Populate deletion modal attributes.
            $row->deletestr = json_encode([
                'deletecheckfull',
                'moodle',
                fullname($row, true),
            ]);

            $row->deleteurl = (new moodle_url('/admin/user.php', [
                'delete' => $row->id,
                'confirm' => md5($row->id),
                'sesskey' => sesskey(),
            ]))->out(false);

            return has_capability('moodle/user:delete', $contextsystem) &&
                !is_mnet_remote_user($row) && $row->id != $USER->id && !is_siteadmin($row);
        }));

        $this->add_action_divider();

        // Action to confirm users.
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['confirmuser' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/check', ''),
            [],
            false,
            new lang_string('confirmaccount', 'moodle'),
        ))->add_callback(static function(\stdclass $row) use ($contextsystem): bool {
            return has_capability('moodle/user:update', $contextsystem) && !$row->confirmed;
        }));

        // Action to resend email.
        $this->add_action((new action(
            new moodle_url('/admin/user.php', ['resendemail' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/email', ''),
            [],
            false,
            new lang_string('resendemail', 'moodle'),
        ))->add_callback(static function(\stdclass $row): bool {
            return !$row->confirmed && !is_mnet_remote_user($row);
        }));
    }

    /**
     * Row class
     *
     * @param \stdClass $row
     * @return string
     */
    public function get_row_class(\stdClass $row): string {
        return $row->suspended ? 'text-muted' : '';
    }
}
