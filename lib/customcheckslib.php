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
 * This is a place to put custom environment checks, if there is not a better place.
 *
 * This library contains a collection of functions able to perform
 * some custom checks executed by environmental tests (automatically
 * executed on install & upgrade and under petition in the admin block).
 *
 * Any function in this library gets a environment_results object passed in.
 * It must return:
 * - null: if the test isn't relevant and must not be showed (ignored)
 * - the environment_results object that was passed in, with the status set to:
 *     - true: if passed
 *     - false: if failed
 *
 * @package    core
 * @subpackage admin
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
