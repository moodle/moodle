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
 * @package   core
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['application/epub_zip'] = 'EPUB ebook';
$string['application/json'] = '{$a->MIMETYPE2} text';
$string['application/msword'] = 'Word document';
$string['application/pdf'] = 'PDF document';
$string['application/vnd.moodle.backup'] = 'Moodle backup';
$string['application/vnd.ms-excel'] = 'Excel spreadsheet';
$string['application/vnd.ms-excel.sheet.macroEnabled.12'] = 'Excel 2007 macro-enabled workbook';
$string['application/vnd.ms-powerpoint'] = 'Powerpoint presentation';
$string['application/vnd.oasis.opendocument.spreadsheet'] = 'OpenDocument Spreadsheet';
$string['application/vnd.oasis.opendocument.spreadsheet-template'] = 'OpenDocument Spreadsheet template';
$string['application/vnd.oasis.opendocument.text'] = 'OpenDocument Text document';
$string['application/vnd.oasis.opendocument.text-template'] = 'OpenDocument Text template';
$string['application/vnd.oasis.opendocument.text-web'] = 'OpenDocument Web page template';
$string['application/vnd.openxmlformats-officedocument.presentationml.presentation'] = 'Powerpoint 2007 presentation';
$string['application/vnd.openxmlformats-officedocument.presentationml.slideshow'] = 'Powerpoint 2007 slideshow';
$string['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] = 'Excel 2007 spreadsheet';
$string['application/vnd.openxmlformats-officedocument.spreadsheetml.template'] = 'Excel 2007 template';
$string['application/vnd.openxmlformats-officedocument.wordprocessingml.document'] = 'Word 2007 document';
$string['application/x-iwork-keynote-sffkey'] = 'iWork Keynote presentation';
$string['application/x-iwork-numbers-sffnumbers'] = 'iWork Numbers spreadsheet';
$string['application/x-iwork-pages-sffpages'] = 'iWork Pages document';
$string['application/x-javascript'] = 'JavaScript source';
$string['application/x-mspublisher'] = 'Publisher document';
$string['application/x-shockwave-flash'] = 'Flash animation';
$string['application/xhtml_xml'] = 'XHTML document';
$string['archive'] = 'Archive ({$a->EXT})';
$string['audio'] = 'Audio file ({$a->EXT})';
$string['default'] = '{$a->mimetype}';
$string['document/unknown'] = 'File';
$string['group:archive'] = 'Archive files';
$string['group:audio'] = 'Audio files';
$string['group:document'] = 'Document files';
$string['group:html_audio'] = 'Audio files natively supported by browsers';
$string['group:html_track'] = 'HTML track files';
$string['group:html_video'] = 'Video files natively supported by browsers';
$string['group:image'] = 'Image files';
$string['group:presentation'] = 'Presentation files';
$string['group:sourcecode'] = 'Source code';
$string['group:spreadsheet'] = 'Spreadsheet files';
$string['group:video'] = 'Video files';
$string['group:web_audio'] = 'Audio files used on the web';
$string['group:web_file'] = 'Web files';
$string['group:web_image'] = 'Image files used on the web';
$string['group:web_video'] = 'Video files used on the web';
$string['image'] = 'Image ({$a->MIMETYPE2})';
$string['image/vnd.microsoft.icon'] = 'Windows icon';
$string['text/css'] = 'Cascading Style-Sheet';
$string['text/csv'] = 'Comma-separated values';
$string['text/html'] = 'HTML document';
$string['text/plain'] = 'Text file';
$string['text/rtf'] = 'RTF document';
$string['text/vtt'] = 'Web Video Text Track';
$string['video'] = 'Video file ({$a->EXT})';
