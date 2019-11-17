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

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_repository_googledocs_upgrade($oldversion) {
    global $CFG;

    if ($oldversion < 2017011100) {
        // Set default import formats from Google.
        set_config('documentformat', 'rtf', 'googledocs');
        set_config('drawingformat', 'pdf', 'googledocs');
        set_config('presentationformat', 'pptx', 'googledocs');
        set_config('spreadsheetformat', 'xlsx', 'googledocs');

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2017011100, 'repository', 'googledocs');
    }
    if ($oldversion < 2017030500) {
        $clientid = get_config('clientid', 'googledocs');
        $secret = get_config('secret', 'googledocs');

        // Update from repo config to use an OAuth service.
        if (!empty($clientid) && !empty($secret)) {
            $issuer = \core\oauth2\api::create_standard_issuer('google');

            $issuer->set('clientid', $clientid);
            $issuer->set('secret', $secret);

            $issuer->update();

            set_config('issuerid', $issuer->get('id'), 'googledocs');
        }
        upgrade_plugin_savepoint(true, 2017030500, 'repository', 'googledocs');
    }
    if ($oldversion < 2017030600) {
        set_config('supportedfiles', 'both', 'googledocs');
        upgrade_plugin_savepoint(true, 2017030600, 'repository', 'googledocs');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
