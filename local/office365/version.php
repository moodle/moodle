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
 * Plugin version information.
 *
 * @package local_office365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2022112805;
$plugin->requires = 2022112800;
$plugin->release = '4.1.1';
$plugin->component = 'local_office365';
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = [
    'auth_oidc' => 2022112805,
    'block_microsoft' => 2022112800,
    'local_o365' => 2022112805,
    'repository_office365' => 2022112800,
    'theme_boost_o365teams' => 2022112800,
];
