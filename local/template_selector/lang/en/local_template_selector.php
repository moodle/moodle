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
 * @package   local_template_selector
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   Standard Moodle template selector
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Strings for component 'local_template_selector', language 'en'
 */

$string['templateselectortoomany'] = 'template_selector got more than one selected template, even though multiselect is false.';
$string['cannotcallusgetselectedtemplate'] = 'You cannot call template_selector::get_selected_template if multi select is true.';
$string['clear'] = 'Clear';
$string['searchoptions'] = 'Search options';
$string['templateselectorpreserveselected'] = 'Keep selected templates, even if they no longer match the search';
$string['templateselectorautoselectunique'] = 'If only one template matches the search, select it automatically';
$string['templateselectorsearchanywhere'] = 'Match the search text anywhere in the template\'s name';
$string['toomanytemplatesmatchsearch'] = 'Too many templates ({$a->count}) match \'{$a->search}\'';
$string['pleasesearchmore'] = 'Please search some more';
$string['toomanytemplatestoshow'] = 'Too many templates ({$a}) to show';
$string['pleaseusesearch'] = 'Please use the search';
$string['nomatchingtemplates'] = 'No templates match \'{$a}\'';
$string['none'] = 'None';
$string['pluginname'] = 'Template selectors';
$string['previouslyselectedtemplates'] = 'Previously selected templates not matching \'{$a}\'';
$string['privacy:metadata'] = 'The Locatio IOMAD Template selectors plugin only shows data stored in other locations.';
$string['search'] = 'Search';
