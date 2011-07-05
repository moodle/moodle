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
 * Manual authentication plugin upgrade code
 *
 * @package    auth
 * @subpackage manual
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_manual_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    if ($oldversion < 2011022700) {
        // force creation of missing passwords
        $createpassword = hash_internal_user_password('');
        $rs = $DB->get_recordset('user', array('password'=>$createpassword, 'auth'=>'manual'));
        foreach ($rs as $user) {
            if (validate_email($user->email)) {
                $DB->set_field('user', 'password', 'to be created', array('id'=>$user->id));
                unset_user_preference('auth_forcepasswordchange', $user);
                set_user_preference('create_password', 1, $user);
            }
        }
        $rs->close();
        upgrade_plugin_savepoint(true, 2011022700, 'auth', 'manual');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
