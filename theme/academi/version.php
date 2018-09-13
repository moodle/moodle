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
 * Version configuration for the theme academi
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version    = 2018052800; // This component release level.
$plugin->maturity   = MATURITY_STABLE; // This version's maturity level.
$plugin->release    = 'v3.4.0.3'; // This version's release version.
$plugin->requires   = 2017102500; // This version's moodle require release.
$plugin->component  = 'theme_academi'; // This component type_name.
$plugin->dependencies = array(
    'theme_boost'  => 2016120500, // This version's depended the component and its require release.
);
