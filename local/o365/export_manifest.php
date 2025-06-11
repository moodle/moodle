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
 * Download tab manifest file.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2018 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filestorage/zip_archive.php');
require_once($CFG->dirroot . '/local/o365/lib.php');

require_admin();

// Mark manifest file as downloaded.
$existingmanifestdownloadedsetting = get_config('local_o365', 'manifest_downloaded');
if (!$existingmanifestdownloadedsetting) {
    add_to_config_log('manifest_downloaded', $existingmanifestdownloadedsetting, true, 'local_o365');
}
set_config('manifest_downloaded', true, 'local_o365');
purge_all_caches();

[$errorcode, $manifestfilepath] = local_o365_create_manifest_file();

if ($manifestfilepath) {
    // Download manifest file.
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=manifest.zip");
    header("Content-length: " . filesize($manifestfilepath));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($manifestfilepath);
} else {
    throw new moodle_exception($errorcode, 'local_o365');
}
