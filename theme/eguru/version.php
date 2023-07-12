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
 * version.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_eguru
 * @copyright   2015 LMSACE Dev Team, lmsace.com
 * @author      LMSACE Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// The current module version (Date: YYYYMMDDXX).
$plugin->version   = 2020120100;


// Version's maturity level.
$plugin->maturity = MATURITY_STABLE;

// Plugin release version.
$plugin->release = 'v3.10';

// Requires this Moodle version.
$plugin->requires  = 2020110900;
					
// Full name of the plugin (used for diagnostics).
$plugin->component = 'theme_eguru';

// Plugin dependencies and dependencies version.
$plugin->dependencies = array(
    'theme_boost'  => 2016120500,
);

