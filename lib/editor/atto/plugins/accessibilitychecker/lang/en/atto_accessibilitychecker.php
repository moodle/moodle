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
 * Strings for component 'atto_accessibilitychecker', language 'en'.
 *
 * @package    atto_accessibilitychecker
 * @copyright  2014 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['emptytext'] = 'Empty text';
$string['entiredocument'] = 'Entire document';
$string['imagesmissingalt'] = 'Images require alternative text. To fix this warning, add an alt attribute to your img tags. An empty alt attribute may be used, but only when the image is purely decorative and carries no information.';
$string['needsmorecontrast'] = 'The colours of the foreground and background text do not have enough contrast. To fix this warning, change either foreground or background colour of the text so that it is easier to read.';
$string['needsmoreheadings'] = 'There is a lot of text with no headings. Headings will allow screen reader users to navigate through the page easily and will make the page more usable for everyone.';
$string['nowarnings'] = 'Congratulations, no accessibility problems found!';
$string['pluginname'] = 'Accessibility checker';
$string['report'] = 'Accessibility report:';
$string['tablesmissingcaption'] = 'Tables should have captions. While it is not necessary for each table to have a caption, a caption is generally very helpful.';
$string['tablesmissingheaders'] = 'Tables should use row and/or column headers.';
$string['tableswithmergedcells'] = 'Tables should not contain merged cells. Despite being standard markup for tables for many years, some screen readers still do not fully support complex tables. When possible, try to "flatten" the table and avoid merged cells.';
$string['privacy:metadata'] = 'The atto_accessibilitychecker plugin does not store any personal data.';
