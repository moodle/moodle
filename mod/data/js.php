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
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_data
 */
use mod_data\manager;
use mod_data\preset;

define('NO_MOODLE_COOKIES', true); // session not used here

require_once('../../config.php');

$id = optional_param('id', 0, PARAM_INT); // Course module id.
$d = optional_param('d', 0, PARAM_INT); // Database id.
$presetfullname = optional_param('preset', '', PARAM_PATH); // The directory the preset is in.

$lifetime  = 600; // Seconds to cache this stylesheet.
$url = new moodle_url('/mod/data/js.php');

$manager = null;
if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, manager::MODULE);
    $manager = manager::create_from_coursemodule($cm);
    $instance = $manager->get_instance();
} else {
    // We must have the database activity id.
    $d = required_param('d', PARAM_INT);
    $instance = $DB->get_record('data', ['id' => $d], '*', MUST_EXIST);
    $manager = manager::create_from_instance($instance);
}
$url->param('d', $instance->id);

// Get the content.
if ($presetfullname) {
    $url->param('preset', $presetfullname);
    $preset = preset::create_from_fullname($manager, $presetfullname);
    $content = $preset->get_template_content('jstemplate');
    $lifetime  = 60; // Preset preview does not need a long cache.
} else {
    $content = $instance->jstemplate;
}

$PAGE->set_url($url);

header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $lifetime) . ' GMT');
header('Cache-control: max_age = '. $lifetime);
header('Pragma: ');
header('Content-type: application/javascript; charset=utf-8');  // Correct MIME type.

echo $content;
