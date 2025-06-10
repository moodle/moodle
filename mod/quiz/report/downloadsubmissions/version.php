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
 * Quiz downloadsubmissions report version information.
 *
 * @package   quiz_downloadsubmissions
 * @copyright 2017 IIT Bombay
 * @author    Kashmira Nagwekar
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020100700;
$plugin->requires  = 2017051500;
$plugin->cron      = 18000;
$plugin->component = 'quiz_downloadsubmissions';
$plugin->maturity  = MATURITY_BETA;
$plugin->release   = '1.1-r2';

