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
function xmldb_repository_dropbox_upgrade($oldversion) {
    global $CFG, $DB;

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {
        $key = get_config('dropbox', 'dropbox_key');
        $secret = get_config('dropbox', 'dropbox_secret');

        if ($key && $secret) {
            $params = [
                'name' => 'Dropbox',
                'clientid' => $key,
                'clientsecret' => $secret,
                'loginparamsoffline' => 'token_access_type=offline',
                'image' => '',
                'showonloginpage' => 0, // Internal services only.
            ];
            $record = $DB->get_record('oauth2_issuer', ['name' => 'Dropbox'], 'id');
            if (!$record) {
                $params = array_merge($params, [
                    'timecreated' => time(),
                    'timemodified' => time(),
                    'usermodified' => time(),
                    'baseurl' => 0,
                    'sortorder' => '',
                    'loginparams' => '',
                    'requireconfirmation' => 1,
                    'alloweddomains' => '',
                    'loginscopes' => 'openid profile email',
                    'loginscopesoffline' => 'openid profile email',
                ]);
                $id = $DB->insert_record('oauth2_issuer', $params);
            } else {
                $id = $record->id;
                $params['id'] = $id;
                $DB->update_record('oauth2_issuer', $params);
            }

            set_config('dropbox_issuerid', $id, 'dropbox');
            unset_config('dropbox_key', 'dropbox');
            unset_config('dropbox_secret', 'dropbox');
        }

        // Dropbox savepoint reached.
        upgrade_plugin_savepoint(true, 2021052501, 'repository', 'dropbox');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
