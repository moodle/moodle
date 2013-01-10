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
 * Adds data generator support
 *
 * Deprecated file in favour of lib/testing/generator/lib.php, keeping
 * file for backwards reference just in case 3rd party applications are
 * using them.
 *
 * @deprecated
 * @todo       MDL-37517 This will be deleted in Moodle 2.7
 * @see        lib/testing/generator/lib.php
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


debugging('File lib/phpunit/generatorlib.php is deprecated, please use lib/testing/generator/lib.php instead', DEBUG_DEVELOPER);

require_once(__DIR__ . '/../testing/generator/lib.php');
