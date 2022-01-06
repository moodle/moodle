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
 * schoollege backgrounds.
 *
 * @package    theme_schoollege
 * @copyright  2020 Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

$plugin->version = 2021042600;
$plugin->requires = 2020061500;
$plugin->component = 'theme_schoollege';
$plugin->dependencies = [
    'theme_boost' => 2020061500,
];
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'Schoollege Theme 1.2';
