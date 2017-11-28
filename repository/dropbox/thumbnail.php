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
 * This script displays one thumbnail of the image in current user's dropbox.
 * If {@link repository_dropbox::send_thumbnail()} can not display image
 * the default 64x64 filetype icon is returned
 *
 * @package    repository_dropbox
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$repoid     = optional_param('repo_id', 0, PARAM_INT);           // Repository ID
$contextid = optional_param('ctx_id', SYSCONTEXTID, PARAM_INT); // Context ID
$source    = optional_param('source', '', PARAM_TEXT);          // File path in current user's dropbox

$thumbnailavailable = isloggedin();
$thumbnailavailable = $thumbnailavailable && $repoid;
$thumbnailavailable = $thumbnailavailable && $source;
$thumbnailavailable = $thumbnailavailable && ($repo = repository::get_repository_by_id($repoid, $contextid));
$thumbnailavailable = $thumbnailavailable && method_exists($repo, 'send_thumbnail');
if ($thumbnailavailable) {
    // Try requesting thumbnail and outputting it.
    // This function exits if thumbnail was retrieved.
    $repo->send_thumbnail($source);
}

// Send default icon for the file type.
$fileicon = file_extension_icon($source, 64);
send_file($CFG->dirroot . '/pix/' . $fileicon . '.png', basename($fileicon) . '.png');
