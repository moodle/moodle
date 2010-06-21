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
 * CLI sync for full category enrol synchronisation.
 *
 * @package   enrol_category
 * @copyright 2010 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    error_log("enrol/category/cli/sync.php can not be called from web server!");
    exit;
}

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once("$CFG->dirroot/enrol/category/locallib.php");

if (!enrol_is_enabled('category')) {
     die('enrol_category plugin is disabled, sync is disabled');
}

enrol_category_sync_full();
