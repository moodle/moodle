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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2018 Gareth J Barnard
 * @copyright   2017 Gareth J Barnard
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version = 2018051901; // YYYYMMDDVV.
$plugin->maturity = MATURITY_BETA; // This version's maturity level.
$plugin->release = '3.5.0.2 (Build: 2018051901)';
$plugin->requires  = 2018051700.00; // 3.5 (Build: 20180517).
$plugin->component = 'theme_essential';
$plugin->dependencies = array(
    'theme_bootstrapbase'  => 2018051400
);