<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Import Word file language strings.
 *
 * @package    booktool_wordimport
 * @copyright  2016 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Microsoft Word file Import/Export (Book)';
$string['wordimport:export'] = 'Export Microsoft Word file';
$string['wordimport:import'] = 'Import Microsoft Word file';
$string['wordfile'] = 'Microsoft Word file';
$string['wordfile_help'] = 'Upload <i>.docx</i> file saved from Microsoft Word or LibreOffice';
$string['importchapters'] = 'Import from Microsoft Word';
$string['exportbook'] = 'Export book to Microsoft Word';
$string['exportchapter'] = 'Export this chapter to Microsoft Word';
$string['nochapters'] = 'No book chapters found, so unable to export to Microsoft Word.';
$string['splitonsubheadings'] = 'Create subchapters based on subheadings';
$string['splitonsubheadings_help'] = 'Subchapters will be created on "Heading 2" styles';
$string['replacebook'] = 'Replace book';
$string['replacebook_help'] = 'Delete the current content of book before importing';
$string['insertionpoint'] = 'Insert before current chapter';
$string['insertionpoint_help'] = 'Insert content before the current chapter, keeping all existing content';
$string['replacechapter'] = 'Replace current chapter';
$string['replacechapter_help'] = 'Replace the content chapter with the first chapter from the file, but keep all other chapters';
$string['transformationfailed'] = 'XSLT transformation failed';
$string['xsltunavailable'] = 'You need the XSLT library installed in PHP to save this Word file';
$string['stylesheetunavailable'] = 'XSLT Stylesheet <b>{$a}</b> is not available';
$string['cannotopentempfile'] = 'Cannot open temporary file <b>{$a}</b>';
$string['privacy:metadata']      = 'The Microsoft Word file import/export tool for books does not store personal data.';

// Strings used in settings.
$string['settings'] = 'Word file import settings';
$string['heading1stylelevel'] = 'Heading element level for Heading 1 style';
$string['heading1stylelevel_desc'] = 'HTML heading element level to which the Word "Heading 1" style should be mapped';
