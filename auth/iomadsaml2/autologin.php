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
 * Page that gets redirected to after autologin is successful.
 *
 * @package   auth_iomadsaml2
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$url = required_param('url', PARAM_LOCALURL);
$success = required_param('success', PARAM_INT);

// If the login is OK (or failed expectedly), then redirect back to the destination.
\auth_iomadsaml2\auto_login::finish((bool)$success, new moodle_url($url));

// Something strange went wrong, or somebody tried to directly link here.
throw new moodle_exception('errorinvalidautologin', 'auth_iomadsaml2');
