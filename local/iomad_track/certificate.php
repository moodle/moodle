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

// Downloads the chosen certificate from the id in local_iomad_track_certs
// TODO: This really needs some checks that the current user
// has the right to see the certificate but I'm unsure how that's done :(

/**
 * @package   local_iomad_track
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

$id = required_param('id', PARAM_INT);

// Security stuff (should) go here
$systemcontext = context_system::instance();
require_login();
iomad::require_capability('local/report_completion:view', $systemcontext);

// Get the details fro db
$certificate = $DB->get_record('local_iomad_track_certs', array('id' => $id), '*', MUST_EXIST);
$track = $DB->get_record('local_iomad_track', array('id' => $certificate->trackid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $track->courseid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $track->userid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

// Get the certificate pdf from filesystem
$fs = get_file_storage();
$component = 'local_iomad_track';
$filearea = 'issue';
$filepath = '/';
$file = $fs->get_file(
    $context->id,
    $component,
    $filearea,
    $track->id,
    $filepath,
    $certificate->filename
);
if (!$file) {
    die;
}

// send the file
send_stored_file($file, null, 0, true);
