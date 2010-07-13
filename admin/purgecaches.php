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
 * This script triggers a full purging of system caches,
 * this is useful mostly for developers who did not disable the caching.
 *
 * @package    core
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/purgecaches.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
require_sesskey();

purge_all_caches();

redirect(new moodle_url('/admin/index.php'), get_string('purgecachesfinished', 'admin'));
