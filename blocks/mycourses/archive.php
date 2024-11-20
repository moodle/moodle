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
 * @package   block_mycourses
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE
require_once('locallib.php');

require_login();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

$context = context_system::instance();

$url = '/blocks/mycourses/archive.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('archivetitle', 'block_mycourses'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);

$output = $PAGE->get_renderer('block_mycourses');

// Get the cut off date.
$cutoffdate = time() - ($CFG->mycourses_archivecutoff * 24 * 60 * 60);

$myarchive = mycourses_get_my_archive($cutoffdate);

echo $output->header();

echo $output->display_archive($myarchive);

echo $output->footer();
