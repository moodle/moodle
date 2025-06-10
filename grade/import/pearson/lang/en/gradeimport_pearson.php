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
 * @package    grade_import_pearson
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Robert Russo, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Link to get back.
$string['gradeimport_pearson_link_back'] = '<a href="'.$CFG->wwwroot.'/admin/settings.php?section=gradeimportpearson">Back to Pearson Importer</a>';

// General.
$string['pluginname'] = 'Pearson file';
$string['pearson:view'] = 'View the Pearson importer';
$string['maintitle'] = 'Pearson Importer';
$string['settings'] = 'Pearson Importer Settings';

// Import Form.
$string['file'] = 'File';
$string['upload_file'] = 'Upload File';
$string['file_type'] = 'File Type';
$string['my_math_lab'] = 'My Math Lab';
$string['my_stat_lab'] = 'My Stat Lab';
$string['mastering_chemistry'] = 'Mastering Chemistry';
$string['mastering_biology'] = 'Mastering Biology';
$string['mastering_physics'] = 'Mastering Physics';

// Mapping Form.
$string['map_grade_items'] = 'Map Grade Items';
$string['ignore_this_item'] = 'Ignore this item';

// Mapping Page.
$string['success'] = 'Grades were successfully imported';
$string['failure'] = 'An error has occurred';

// Results Form.
$string['import_results'] = 'Import Notices';
$string['user_not_found'] = '{$a} is not enrolled in this course and was skipped';

// Notifications.
$string['encodingtypepre'] = 'Warning: The file that has been uploaded is encoded as ';
$string['encodingtypepost'] = '. If the file does\'t import the grades please check the importer settings to enable file conversions.';

// Settings.
$string['gradeimport_pearson_convert_encoding_title'] = 'Find and convert non UTF-8 characters';
$string['gradeimport_pearson_convert_encoding_desc'] = 'If a file has been exported using a different type of encoding then when this option is on it will convert the file to UTF-8';
$string['gradeimport_pearson_encoding_list'] = 'List of encodings to check file against.';

$string['gradeimport_pearson_encoding_message_title'] = 'File encode type warning.';
$string['gradeimport_pearson_encoding_message_desc'] = 'If the file is not UTF-8 then a warning will be shown with the encoding type.';