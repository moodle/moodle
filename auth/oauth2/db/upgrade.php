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
 * OAuth2 authentication plugin upgrade code
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_oauth2_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019052001) {
        // Fetch Facebook, Google, and Microsoft issuers. We use the URL field to determine the issuer type as it's the only
        // field that contains the keyword that can somewhat let us reliably determine the issuer type.
        $likefacebook = $DB->sql_like('oe.url', ':facebook');
        $likegoogle = $DB->sql_like('oe.url', ':google');
        $likemicrosoft = $DB->sql_like('oe.url', ':microsoft');

        $params = [
            'facebook' => '%facebook%',
            'google' => '%google%',
            'microsoft' => '%microsoft%',
        ];

        // We're querying from the oauth2_endpoint table because the base URLs of FB and Microsoft can be empty in the issuer table.
        $subsql = "
            SELECT DISTINCT oe.issuerid
                       FROM {oauth2_endpoint} oe
                      WHERE $likefacebook
                            OR $likegoogle
                            OR $likemicrosoft";

        // Update non-Facebook/Google/Microsoft issuers and set requireconfirmation to 1.
        $updatesql = "
            UPDATE {oauth2_issuer}
               SET requireconfirmation = 1
             WHERE id NOT IN ({$subsql})";
        $DB->execute($updatesql, $params);

        // Delete linked logins for non-Facebook/Google/Microsoft issuers. They can easily re-link their logins anyway.
        $DB->delete_records_select('auth_oauth2_linked_login', "issuerid NOT IN ($subsql)", $params);

        upgrade_plugin_savepoint(true, 2019052001, 'auth', 'oauth2');
    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.11.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
