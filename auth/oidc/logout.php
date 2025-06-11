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
 * Single Sign Out end point.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:ignore moodle.Files.RequireLogin.Missing
require_once(__DIR__ . '/../../config.php');

$PAGE->set_url('/auth/oidc/logout.php');
$PAGE->set_context(context_system::instance());

$sid = optional_param('sid', '', PARAM_TEXT);

if ($sid) {
    if ($authoidcsidrecord = $DB->get_record('auth_oidc_sid', ['sid' => $sid])) {
        if ($authoidcsidrecord->userid == $USER->id) {
            $authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
            foreach ($authsequence as $authname) {
                $authplugin = get_auth_plugin($authname);
                $authplugin->logoutpage_hook();
            }

            $DB->delete_records('auth_oidc_sid', ['sid' => $sid]);
            require_logout();
        }
    }
}

die();
