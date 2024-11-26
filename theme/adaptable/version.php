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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// The theme name.
$plugin->component = 'theme_adaptable';

// Adaptable version date (YYYYMMDDrr where rr is the release number).
$plugin->version = 2024100501;

$plugin->requires = 2024100700.00; // 4.5 (Build: 20241007).

$plugin->supported = [405, 405];

// Adaptable version using SemVer (https://semver.org).
$plugin->release = '405.1.0';

// Adaptable maturity (do not use ALPHA or BETA versions in production sites).
$plugin->maturity = MATURITY_STABLE;

// Adaptable dependencies (Only Boost as it's the parent theme).
$plugin->dependencies = [
    'theme_boost' => 2024100700,
];
