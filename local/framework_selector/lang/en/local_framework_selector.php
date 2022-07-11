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
 * @package   local_framework_selector
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   standard Moodle framework_selector
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'local_framework_selector', language 'en'
 */

$string['frameworkselectortoomany'] = 'framework_selector got more than one selected framework, even though multiselect is false.';
$string['cannotcallusgetselectedframework'] = 'You cannot call framework_selector::get_selected_framework if multi select is true.';
$string['clear'] = 'Clear';
$string['searchoptions'] = 'Search options';
$string['frameworkselectorpreserveselected'] = 'Keep selected frameworks, even if they no longer match the search';
$string['frameworkselectorautoselectunique'] = 'If only one framework matches the search, select it automatically';
$string['frameworkselectorsearchanywhere'] = 'Match the search text anywhere in the framework\'s name';
$string['toomanyframeworksmatchsearch'] = 'Too many frameworks ({$a->count}) match \'{$a->search}\'';
$string['pleasesearchmore'] = 'Please search some more';
$string['toomanyframeworkstoshow'] = 'Too many frameworks ({$a}) to show';
$string['pleaseusesearch'] = 'Please use the search';
$string['nomatchingframeworks'] = 'No frameworks match \'{$a}\'';
$string['none'] = 'None';
$string['pluginname'] = 'Framework selectors';
$string['previouslyselectedframeworks'] = 'Previously selected frameworks not matching \'{$a}\'';
$string['privacy:metadata'] = 'The IOMAD Local framework selector plugin only shows data stored in other locations.';
$string['search'] = 'Search';
