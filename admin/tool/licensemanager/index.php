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
 * License manager page.
 *
 * @package   tool_licensemanager
 * @copyright 2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/licenselib.php');

require_admin();

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$license = optional_param('license', '', PARAM_SAFEDIR);

// Route via the manager.
$licensemanager = new \tool_licensemanager\manager();
$PAGE->set_context(context_system::instance());
$PAGE->set_url(\tool_licensemanager\helper::get_licensemanager_url());
$PAGE->set_title(get_string('licensemanager', 'tool_licensemanager'));

$licensemanager->execute($action, $license);
