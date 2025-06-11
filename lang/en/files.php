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
 * Language file.
 *
 * @package    core_files
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['contenthash'] = 'Content hash';
$string['eventfileaddedtodraftarea'] = 'File added to draft area';
$string['eventfiledeletedfromdraftarea'] = 'File deleted from draft area';
$string['redactor'] = 'File redaction';
$string['redactor:exifremover'] = 'EXIF remover';
$string['redactor:exifremover:emptyremovetags'] = 'Remove tags can not be empty!';
$string['redactor:exifremover:enabled'] = 'Enable EXIF remover';
$string['redactor:exifremover:enabled_desc'] = 'By default, EXIF Remover only supports JPG files using PHP GD, or ExifTool if it is configured.
This degrades the quality of the image and removes the orientation tag.

To enhance the performance of EXIF Remover, please configure the ExifTool settings below.

More information about installing ExifTool can be found at {$a->link}';
$string['redactor:exifremover:failedprocessexiftool'] = 'Redaction failed: failed to process file with ExifTool!';
$string['redactor:exifremover:failedprocessgd'] = 'Redaction failed: failed to process file with PHP gd!';
$string['redactor:exifremover:heading'] = 'ExifTool';
$string['redactor:exifremover:mimetype'] = 'Supported MIME types';
$string['redactor:exifremover:mimetype_desc'] = 'To add new MIME types, ensure they\'re included in the <a href="./tool/filetypes/index.php">File Types</a>.';
$string['redactor:exifremover:removetags'] = 'The EXIF tags that will be removed.';
$string['redactor:exifremover:removetags_desc'] = 'The EXIF tags that need to be removed.';
$string['redactor:exifremover:tag:all'] = 'All';
$string['redactor:exifremover:tag:gps'] = 'GPS only';
$string['redactor:exifremover:tooldoesnotexist'] = 'Redaction failed: ExifTool does not exist!';
$string['redactor:exifremover:toolpath'] = 'Path to ExifTool';
$string['redactor:exifremover:toolpath_desc'] = 'To use the ExifTool, please provide the path to the ExifTool executable.
Typically, on Unix/Linux systems, the path is /usr/bin/exiftool.';
$string['privacy:metadata:file_conversions'] = 'A record of the file conversions performed by a user.';
$string['privacy:metadata:file_conversion:usermodified'] = 'The user who started the file conversion.';
$string['privacy:metadata:files'] = 'A record of the files uploaded or shared by users';
$string['privacy:metadata:files:author'] = 'The author of the file\'s content';
$string['privacy:metadata:files:contenthash'] = 'A hash of the file\'s content';
$string['privacy:metadata:files:filename'] = 'The name of the file in its file area';
$string['privacy:metadata:files:filepath'] = 'The path to the file in its file area';
$string['privacy:metadata:files:filesize'] = 'The size of the file';
$string['privacy:metadata:files:license'] = 'The licence of the file\'s content';
$string['privacy:metadata:files:mimetype'] = 'The MIME type of the file';
$string['privacy:metadata:files:source'] = 'The source of the file';
$string['privacy:metadata:files:timecreated'] = 'The time when the file was created';
$string['privacy:metadata:files:timemodified'] = 'The time when the file was last modified';
$string['privacy:metadata:files:userid'] = 'The user who created the file';
$string['privacy:metadata:core_userkey'] = 'A private token is generated and stored. This token can be used to access Moodle files without requiring you to log in.';
