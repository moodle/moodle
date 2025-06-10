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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_login();
require_once("$CFG->dirroot/lib/filelib.php");

$relativepath = get_file_argument();
$forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);

global $DB, $CFG, $USER;
// Relative path must start with '/'.
if (!$relativepath) {
        print_error('invalidargorconf');
} else if ($relativepath[0] != '/') {
        print_error('pathdoesnotstartslash');
}

// Extract relative path components.
$args = explode('/', ltrim($relativepath, '/'));

if (count($args) < 3) { // Always at least context, component and filearea.
        print_error('invalidarguments');
}

$contextid = (int)array_shift($args);
$component = clean_param(array_shift($args), PARAM_ALPHA);
$filearea  = clean_param(array_shift($args), PARAM_ALPHA);

if ($component != 'user' || $filearea != 'csvprofile') {
        print_error('invalidargorconf');
}

$filename = array_pop($args);
$filepath = $args ? '/'.implode('/', $args).'/' : '/';

$fs = get_file_storage();
if (!$file = $fs->get_file($contextid, $component, $filearea, 0, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
}

send_stored_file($file, 10 * 60, 0, true); // Download MUST be forced - security!
