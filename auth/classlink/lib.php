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
 * @package auth_classlink
 * @author Gopal Sharma <gopalsharma66@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2020 Gopal Sharma <gopalsharma66@gmail.com>
 */

function auth_classlink_initialize_customicon($filefullname) {
    global $CFG;
    $file = get_config('auth_classlink', 'customicon');
    $systemcontext = \context_system::instance();
    $fullpath = "/{$systemcontext->id}/auth_classlink/customicon/0{$file}";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    $pixpluginsdir = 'pix_plugins/auth/classlink/0';
    $pixpluginsdirparts = explode('/', $pixpluginsdir);
    $curdir = $CFG->dataroot;
    foreach ($pixpluginsdirparts as $dir) {
        $curdir .= '/'.$dir;
        if (!file_exists($curdir)) {
            mkdir($curdir);
        }
    }

    if (file_exists($CFG->dataroot.'/pix_plugins/auth/classlink/0')) {
        $file->copy_content_to($CFG->dataroot.'/pix_plugins/auth/classlink/0/customicon.jpg');
        theme_reset_all_caches();
    }
}

/**
 * Check for connection abilities.
 *
 * @param int $userid Moodle user id to check permissions for.
 * @param string $mode Mode to check
 *                     'connect' to check for connect specific capability
 *                     'disconnect' to check for disconnect capability.
 *                     'both' to check for disconnect and connect capability.
 * @param boolean $require Use require_capability rather than has_capability.
 * @return boolean True if has capability.
 */
function auth_classlink_connectioncapability($userid, $mode = 'connect', $require = false) {
    $check = 'has_capability';
    if ($require) {
        // If requiring the capability and user has manageconnection than checking connect and disconnect is not needed.
        $check = 'require_capability';
        if (has_capability('auth/classlink:manageconnection', \context_user::instance($userid), $userid)) {
            return true;
        }
    } else if ($check('auth/classlink:manageconnection', \context_user::instance($userid), $userid)) {
        return true;
    }

    $result = false;
    switch ($mode) {
        case "connect":
            $result = $check('auth/classlink:manageconnectionconnect', \context_user::instance($userid), $userid);
            break;
        case "disconnect":
            $result = $check('auth/classlink:manageconnectiondisconnect', \context_user::instance($userid), $userid);
            break;
        case "both":
            $result = $check('auth/classlink:manageconnectionconnect', \context_user::instance($userid), $userid);
            $result = $result && $check('auth/classlink:manageconnectiondisconnect', \context_user::instance($userid), $userid);
    }
    if ($require) {
        return true;
    }
    return $result;
}
