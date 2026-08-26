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
 * Starting point for drag and drop course uploads
 *
 * @package    core
 * @subpackage lib
 * @copyright  2012 Davo smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../config.php');

$courseid = required_param('course', PARAM_INT);
$section = required_param('section', PARAM_INT);
$type = required_param('type', PARAM_TEXT);
$modulename = required_param('module', PARAM_PLUGIN);
$displayname = optional_param('displayname', null, PARAM_TEXT);
$contents = optional_param('contents', null, PARAM_RAW); // It will be up to each plugin to clean this data, before saving it.

// Optional image details, collected by the "Add media to course page" shortcut before an image is added
// to a Text and media activity, so the author can set alternative text, mark the image decorative and
// choose its display size. Handlers that do not deal with images simply ignore these.
$imagedetails = null;
if (optional_param('imagedetailsset', 0, PARAM_BOOL)) {
    $imagedetails = [
        'alt' => optional_param('imagealt', '', PARAM_TEXT),
        'presentation' => optional_param('imagepresentation', 0, PARAM_BOOL),
        'width' => optional_param('imagewidth', 0, PARAM_INT),
        'height' => optional_param('imageheight', 0, PARAM_INT),
    ];
}

$PAGE->set_url('/course/dndupload.php');

$dndproc = new \core_course\dndupload_ajax_processor($courseid, $section, $type, $modulename);
$dndproc->process($displayname, $contents, $imagedetails);
