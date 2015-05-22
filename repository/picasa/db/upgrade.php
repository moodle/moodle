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
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_repository_picasa_upgrade($oldversion) {
    global $CFG, $DB;
    require_once(__DIR__.'/upgradelib.php');

    $dbman = $DB->get_manager();

    if ($oldversion < 2012051400) {
        // Delete old user preferences storing authsub tokens.
        $DB->delete_records('user_preferences', array('name' => 'google_authsub_sesskey_picasa'));
        upgrade_plugin_savepoint(true, 2012051400, 'repository', 'picasa');
    }

    if ($oldversion < 2012053000) {
        require_once($CFG->dirroot.'/repository/lib.php');
        $existing = $DB->get_record('repository', array('type' => 'picasa'), '*', IGNORE_MULTIPLE);

        if ($existing) {
            $picasaplugin = new repository_type('picasa', array(), true);
            $picasaplugin->delete();
            repository_picasa_admin_upgrade_notification();
        }

        upgrade_plugin_savepoint(true, 2012053000, 'repository', 'picasa');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
