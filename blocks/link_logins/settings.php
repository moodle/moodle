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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $DB;

if ($ADMIN->fulltree) {

    // Get the list of issuers for later.
    $datas = $DB->get_records(
        'oauth2_issuer',
        array('enabled' => 1, 'showonloginpage' => 1),
        $sort='', 'id, name');

    // Set up the empty array.
    $issuers = array();

    // Loop through the data and build the key / value pair.
    foreach ($datas as $data) {
        // Set the key / value pair here.
        $issuers[$data->id] = $data->name;
    }

    // Set up the allowed users.
    $settings->add(
        new admin_setting_configtext(
            'block_link_logins_allowed',
            get_string('allowed_users', 'block_link_logins'),
            get_string('allowed_users_desc', 'block_link_logins'),
            'admin'
        )
    );

    // Set the home domain.
    $settings->add(
        new admin_setting_configtext(
            'block_link_logins_homedomain',
            get_string('homedomain', 'block_link_logins'),
            get_string('homedomain_desc', 'block_link_logins'),
            '@lsu.edu'
        )
    );

    // Set the remote domain.
    $settings->add(
        new admin_setting_configtext(
            'block_link_logins_extdomain',
            get_string('extdomain', 'block_link_logins'),
            get_string('extdomain_desc', 'block_link_logins'),
            'admin'
        )
    );

    // Set the issuerid.
    if (!empty($issuers)) {
        $settings->add(
            new admin_setting_configselect(
                'block_link_logins_issuerid',
                get_string('issuerid', 'block_link_logins'),
                get_string('issuerid_desc', 'block_link_logins'),
                1,
                $issuers
            )
        );
    }
}
