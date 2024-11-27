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
 * Educard pages.
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_educard_page', get_string('frontpagepage', 'theme_educard'));

require('pages/1-contact.php');
require('pages/2-persons.php');

$page->add(new admin_setting_heading('theme_educard_pageend', get_string('frontpagepageend', 'theme_educard'),
format_text(get_string('frontpagepageenddesc', 'theme_educard'), FORMAT_MARKDOWN)));
$settings->add($page);
