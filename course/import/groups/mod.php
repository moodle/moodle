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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$streditmyprofile = get_string("editmyprofile");
$stradministration = get_string("administration");
$strchoose = get_string("choose");
$struser = get_string("user");
$strusers = get_string("users");
$strusersnew = get_string("usersnew");
$strimportgroups = get_string("importgroups");

echo $OUTPUT->heading($strimportgroups);

// use formslib
include_once('import_form.php');
$mform_post = new course_import_groups_form($CFG->wwwroot.'/course/import/groups/index.php?id='.$id);
$mform_post ->display();
