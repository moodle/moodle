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
 * @package    ues_reprocess
 * @copyright  2024 onwards LSUOnline & Continuing Education
 * @copyright  2024 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Include the main Moodle config.
require(__DIR__ . '/../../../config.php');
require_once('workdaystudent.php');

$timestarted = microtime(true);

// Get settings.
$s = workdaystudent::get_settings();

$programs = workdaystudent::get_programs($s);

$numgrabbed = count($programs);

$gstime = round(microtime(true) - $timestarted, 3);
mtrace("Took $gstime seconds to fetch $numgrabbed remote programs of study.");
$timeustarted = microtime(true);

$pgms = workdaystudent::clear_insert_programs($programs);

$ugstime = round(microtime(true) - $timeustarted, 3);
$uttime = round(microtime(true) - $timestarted, 3);
mtrace("  Took $ugstime seconds to insert updated programs of study.");
mtrace("Took $uttime seconds to complete the process.");
