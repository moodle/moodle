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
 * Strings for component 'atto_wordimport', language 'en'.
 *
 * @package    atto_wordimport
 * @copyright  2015 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin name is used as the Atto toolbar icon tooltop, so have to use suitable text there and use another label for real plugin name.
$string['pluginname'] = 'Import Word file';
$string['pluginname2'] = 'Microsoft Word File Import (Atto)'.
$string['uploading'] = 'Uploading, please wait...';
$string['privacy:metadata']      = 'The Microsoft Word file import plugin for Atto does not store personal data.';

// Strings used in JavaScript.
$string['transformationfailed'] = 'XSLT transformation failed (<b>{$a}</b>)';
$string['fileuploadfailed'] = 'File upload failed';
$string['fileconversionfailed'] = 'File conversion failed';

// Strings used in settings.
$string['settings'] = 'Microsoft Word File Import (Atto) settings';
$string['heading1stylelevel'] = 'Heading element level for Heading 1 style';
$string['heading1stylelevel_desc'] = 'HTML heading element level to which the Word "Heading 1" style should be mapped';
