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
 * Makes sure that all general code needed by backup-convert code is included
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/interfaces/loggable.class.php'); // converters are loggable
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php'); // req by backup.class.php
require_once($CFG->dirroot . '/backup/backup.class.php'); // provides backup::FORMAT_xxx constants
require_once($CFG->dirroot . '/backup/util/helper/convert_helper.class.php');
require_once($CFG->dirroot . '/backup/util/factories/convert_factory.class.php');
require_once($CFG->libdir . '/filelib.php');
