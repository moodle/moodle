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

declare(strict_types=1);

namespace core_reportbuilder\reportbuilder\audience;

use context_system;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\helpers\database;
use core_user;
use MoodleQuickForm;

/**
 * The backend class for Manually added users audience type
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manual extends base {

    /**
     * Adds audience's elements to the given mform
     *
     * @param MoodleQuickForm $mform The form to add elements to
     */
    public function get_config_form(MoodleQuickForm $mform): void {
        // Users selector.
        $options = [
            'ajax' => 'core_user/form_user_selector',
            'multiple' => true,
            'valuehtmlcallback' => function($userid) {
                $user = core_user::get_user($userid);
                return fullname($user, has_capability('moodle/site:viewfullnames', context_system::instance()));
            }
        ];

        $mform->addElement('autocomplete', 'users', get_string('addusers', 'core_reportbuilder'), [], $options);
        $mform->addRule('users', null, 'required', null, 'client');
    }

    /**
     * Helps to build SQL to retrieve users that matches the current report audience
     *
     * @param string $usertablealias
     * @return array array of three elements [$join, $where, $params]
     */
    public function get_sql(string $usertablealias): array {
        global $DB;

        $users = $this->get_configdata()['users'];
        [$insql, $inparams] = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, database::generate_param_name('_'));

        return ['', "{$usertablealias}.id $insql", $inparams];
    }

    /**
     * Return user friendly name of this audience type
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('manuallyaddedusers', 'core_reportbuilder');
    }

    /**
     * Return the description for the audience.
     *
     * @return string
     */
    public function get_description(): string {
        global $DB;

        $canviewfullnames = has_capability('moodle/site:viewfullnames', context_system::instance());

        $userslist = [];

        $userids = $this->get_configdata()['users'];
        [$sort] = users_order_by_sql();
        $users = $DB->get_records_list('user', 'id', $userids, $sort);
        foreach ($users as $user) {
            $userslist[] = fullname($user, $canviewfullnames);
        }

        return $this->format_description_for_multiselect($userslist);
    }

    /**
     * If the current user is able to add this audience.
     *
     * @return bool
     */
    public function user_can_add(): bool {
        return has_capability('moodle/user:viewalldetails', context_system::instance());
    }

    /**
     * If the current user is able to edit this audience.
     *
     * @return bool
     */
    public function user_can_edit(): bool {
        return has_capability('moodle/user:viewalldetails', context_system::instance());
    }
}
