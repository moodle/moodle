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
 * @package    local_syllabusuploader
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Syllabus Uploader';
$string['settings'] = 'Uploader Settings';
$string['foldername'] = 'Manage Syllabus';
$string['adminname'] = 'Manage Syllabus Uploads';

// Capabilities.
$string['syllabusuploader:admin'] = 'Administer the Syllabus Uploader.';

// General terms.
$string['backtohome'] = 'Back to home';

// Settings management.
$string['manage_uploader'] = 'File Uploader';
$string['manage_viewer'] = 'File Viewer';
$string['syllabusuploader_uploadstring'] = 'Upload a File';
$string['no_upload_permissions'] = 'You do not have permission to upload files.';

$string['syllabusuploader_path_settings_title'] = 'Path Settings';
$string['syllabusuploader_user_settings_title'] = 'User Settings';
$string['syllabusuploader_manager_settings_title'] = 'File Manager Settings';
$string['syllabusuploader_manager_max_files_title'] = 'Uploading Files Maximum';
$string['syllabusuploader_manager_max_files_desc'] = 'The maximum number of files that  a user can upload at once.';
$string['syllabusuploader_manager_acceptedtypes_title'] = 'Accepted File Types';
$string['syllabusuploader_manager_acceptedtypes_desc'] = 'A comma seperated list of the file types that are allowed to be uploaded. For example: pdf,doc,txt';
$string['syllabusuploader_file_link'] = 'File Link';
$string['syllabusuploader_filename'] = 'File Name';
$string['syllabusuploader_filecreated'] = 'Created';
$string['syllabusuploader_filemodified'] = 'Last Modified';
$string['syllabusuploader_copy'] = 'Copy File';
$string['syllabusuploader_delete'] = 'Delete File';
$string['syllabusuploader_nofiles'] = 'No Files To Display';
$string['syllabusuploader_settings'] = 'Syllabus Uploader';
$string['syllabusuploader_copy_file'] = 'Copy File Location';
$string['syllabusuploader_copy_file_desc'] = 'Files can be uploaded and copied to the location specified here. (include forward slash at the end/)';
$string['syllabusuploader_public_path'] = 'Public Path';
$string['syllabusuploader_public_path_desc'] = 'The (relative to moodle\'s wwwroot) public path you use for users to fetch syllabus files. (/include surrounding slashes/)';
$string['syllabusuploader_admins'] = 'Allowed Users';
$string['syllabusuploader_admins_desc'] = 'A comma seperated list of emails for allowed users. Those users must also have the "local/syllabusuploader:admin" capability.';
$string['no_upload_permissions'] = 'You do not have permission to upload and view files.';
