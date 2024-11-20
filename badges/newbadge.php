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
 * First step page for creating a new badge
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 * @deprecated since 4.5. Use badges/edit.php instead.
 * @todo       MDL-82383 This file will be deleted in Moodle 6.0.
 */

require_once(__DIR__ . '/../config.php');

$courseid = optional_param('id', 0, PARAM_INT);

require_login();

$newpageurl = new moodle_url('/badges/edit.php', ['courseid' => $courseid, 'action' => 'new']);
redirect($newpageurl, get_string('newbadgedeprecated', 'core_badges'));
