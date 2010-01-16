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
 * This is a tiny standalone diagnostic script to test that sessions
 * are working correctly on a given server.
 *
 * Just run it from a browser.   The first time you run it will
 * set a new variable, and after that it will try to find it again.
 * The random number is just to prevent browser caching.
 *
 * @todo add code that actually tests moodle sessions, the old one only tested
 *       PHP sessions used from installer, not the real moodle sessions
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Include config {@see config.php} */
require '../config.php';

$PAGE->set_url('/lib/session-test.php');

error('session test not reimplemented yet'); //DO NOT localize or use print_error()!
//
//TODO: add code that actually tests moodle sessions, the old one only tested PHP sessions used from installer, not the real moodle sessions
