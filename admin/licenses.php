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
 * Allows admin to configure licenses.
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/licenselib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=managelicenses";

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$license = optional_param('license', '', PARAM_SAFEDIR);

////////////////////////////////////////////////////////////////////////////////
// process actions

if (!confirm_sesskey()) {
    redirect($returnurl);
}

$return = true;
switch ($action) {
    case 'disable':
        license_manager::disable($license);
        break;

    case 'enable':
        license_manager::enable($license);
        break;

    default:
        break;
}

if ($return) {
    redirect ($returnurl);
}
