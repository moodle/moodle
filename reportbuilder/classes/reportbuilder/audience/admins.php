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

use MoodleQuickForm;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\helpers\database;

/**
 * Administrators audience type
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admins extends base {

    /**
     * Add audience elements to the current form
     *
     * @param MoodleQuickForm $mform
     */
    public function get_config_form(MoodleQuickForm $mform): void {
        $mform->addElement('static', 'admins', get_string('siteadministrators', 'core_role'));
    }

    /**
     * Return SQL to retrieve users that match this audience
     *
     * @param string $usertablealias
     * @return array [$join, $select, $params]
     */
    public function get_sql(string $usertablealias): array {
        global $CFG, $DB;

        $siteadmins = array_map('intval', explode(',', $CFG->siteadmins));
        [$select, $params] = $DB->get_in_or_equal($siteadmins, SQL_PARAMS_NAMED, database::generate_param_name() . '_');

        return ['', "{$usertablealias}.id {$select}", $params];
    }

    /**
     * Return name of this audience
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('siteadministrators', 'core_role');
    }

    /**
     * Return description of this audience.
     *
     * @return string
     */
    public function get_description(): string {
        $siteadmins = array_map('fullname', get_admins());

        return $this->format_description_for_multiselect($siteadmins);
    }

    /**
     * Whether the current user is able to add this audience
     *
     * @return bool
     */
    public function user_can_add(): bool {
        return is_siteadmin();
    }

    /**
     * Whether the current user is able to edit this audience
     *
     * @return bool
     */
    public function user_can_edit(): bool {
        return $this->user_can_add();
    }
}
