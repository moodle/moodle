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
 * A scheduled task for LDAP roles sync.
 *
 * @package    auth_ldap
 * @author     David Balch <david.balch@conted.ox.ac.uk>
 * @copyright  2017 The Chancellor Masters and Scholars of the University of Oxford {@link http://www.tall.ox.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_ldap\task;

defined('MOODLE_INTERNAL') || die();

/**
 * A scheduled task class for LDAP roles sync.
 *
 * @author     David Balch <david.balch@conted.ox.ac.uk>
 * @copyright  2017 The Chancellor Masters and Scholars of the University of Oxford {@link http://www.tall.ox.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_roles extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('syncroles', 'auth_ldap');
    }

    /**
     * Synchronise role assignments from LDAP.
     */
    public function execute() {
        global $DB;
        if (is_enabled_auth('ldap')) {
            $auth = get_auth_plugin('ldap');
            $users = $DB->get_records('user', array('auth' => 'ldap'));
            foreach ($users as $user) {
                $auth->sync_roles($user);
            }
        }
    }

}
