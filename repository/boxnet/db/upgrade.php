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
 * Upgrade.
 *
 * @package    repository_boxnet
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool result
 */
function xmldb_repository_boxnet_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2013110503) {
        // Delete old user preferences containing auth tokens.
        $DB->delete_records('user_preferences', array('name' => 'boxnet__auth_token'));
        upgrade_plugin_savepoint(true, 2013110503, 'repository', 'boxnet');
    }

    if ($oldversion < 2013110700) {
        require_once($CFG->dirroot . '/repository/lib.php');
        require_once($CFG->dirroot . '/repository/boxnet/db/upgradelib.php');

        $clientid = get_config('boxnet', 'clientid');
        $clientsecret = get_config('boxnet', 'clientsecret');

        // Only proceed if the repository hasn't been set for APIv2 yet.
        if ($clientid === false && $clientsecret === false) {
            $params = array();
            $params['context'] = array();
            $params['onlyvisible'] = false;
            $params['type'] = 'boxnet';
            $instances = repository::get_instances($params);

            // Notify the admin about the migration process if they are using the repo.
            if (!empty($instances)) {
                repository_boxnet_admin_upgrade_notification();
            }

            // Hide the repository.
            $repositorytype = repository::get_type_by_typename('boxnet');
            if (!empty($repositorytype)) {
                $repositorytype->update_visibility(false);
            }
        }

        upgrade_plugin_savepoint(true, 2013110700, 'repository', 'boxnet');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
