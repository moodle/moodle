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
 * Upgrade script.
 *
 * @package block_microsoft
 * @author Akinsaya Delamarre <adelamarre@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

/**
 * Update plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_block_microsoft_upgrade($oldversion) {
    if ($oldversion < 2015111912) {
        $existingshowoutlooksyncsetting = get_config('block_microsoft', 'settings_showoutlooksync');
        if ($existingshowoutlooksyncsetting != 0    ) {
            add_to_config_log('settings_showoutlooksync', $existingshowoutlooksyncsetting, 0, 'block_microsoft');
        }
        set_config('settings_showoutlooksync', 0, 'block_microsoft');

        $existingshowmanageo365connectionsetting = get_config('block_microsoft', 'settings_showmanageo365conection');
        if ($existingshowmanageo365connectionsetting != 0) {
            add_to_config_log('settings_showmanageo365conection', $existingshowmanageo365connectionsetting, 0, 'block_microsoft');
        }
        set_config('settings_showmanageo365conection', 0, 'block_microsoft');
        upgrade_block_savepoint(true, 2015111912, 'microsoft');
    }

    return true;
}
