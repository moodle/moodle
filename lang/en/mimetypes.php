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
 * Strings for component 'mimetypes', language 'en', branch 'MOODLE_20_STABLE'
 *
 * Strings are used to display human-readable name of mimetype. Some mimetypes share the same
 * string. The following attributes are passed in the parameter when processing the string:
 *   $a->ext - filename extension in lower case
 *   $a->EXT - filename extension, capitalized
 *   $a->Ext - filename extension with first capital letter
 *   $a->mimetype - file mimetype
 *   $a->mimetype1 - first chunk of mimetype (before /)
 *   $a->mimetype2 - second chunk of mimetype (after /)
 *   $a->Mimetype, $a->MIMETYPE, $a->Mimetype1, $a->Mimetype2, $a->MIMETYPE1, $a->MIMETYPE2
 *      - the same with capitalized first/all letters
 *
 * @see       get_mimetypes_array()
 * @see       get_mimetype_description()
 * @package   mimetypes
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['application/msword'] = 'Word document';
$string['application/pdf'] = 'PDF document';
$string['application/vnd.moodle.backup'] = 'Moodle backup';
$string['application/vnd.ms-excel'] = 'Excel spreadsheet';
$string['application/vnd.ms-powerpoint'] = 'Powerpoint presentation';
$string['application/vnd.openxmlformats-officedocument.presentationml.presentation'] = 'Powerpoint presentation';
$string['application/vnd.openxmlformats-officedocument.presentationml.slideshow'] = 'Powerpoint slideshow';
$string['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] = 'Excel spreadsheet';
$string['application/vnd.openxmlformats-officedocument.spreadsheetml.template'] = 'Excel template';
$string['application/vnd.openxmlformats-officedocument.wordprocessingml.document'] = 'Word document';
$string['application/epub_zip'] = 'EPUB ebook';
$string['archive'] = 'Archive ({$a->EXT})';
$string['audio'] = 'Audio file ({$a->EXT})';
$string['default'] = '{$a->mimetype}';
$string['document/unknown'] = 'File';
$string['image'] = 'Image ({$a->MIMETYPE2})';
$string['text/html'] = 'HTML document';
$string['text/plain'] = 'Text file';
$string['text/rtf'] = 'RTF document';
