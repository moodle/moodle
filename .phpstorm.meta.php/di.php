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
 * Helper file for PhpStorm, and other IDEs, to provide better code completion.
 *
 * @package   core
 * @copyright 2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace PHPSTORM_META;

// Assume that anything returned by \core\di::get('...') is an instance of the first argument.
override(\core\di::get(0), map([
    '' => '@',
]));

// Assume that anything returned by \Psr\Container\ContainerInterface::get('...') is an instance of the first argument.
override(\Psr\Container\ContainerInterface::get(0), map([
    '' => '@',
]));

// Assume that anything returned by \DI\Container::get('...') is an instance of the first argument.
override(\DI\Container::get(0), map([
    '' => '@',
]));
