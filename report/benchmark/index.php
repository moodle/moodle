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
 * The BenchMark report
 *
 * @package    report
 * @subpackage benchmark
 * @copyright  Mickaël Pannequin, m.pannequin@xperteam.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link       https://github.com/mikasmart/benchmark Source on GitHub
 *
 */


// Timeout at 2 minutes
set_time_limit(120);

define('NO_OUTPUT_BUFFERING', true);

// Required config and set markers for the 1st test
define('BENCHSTART', microtime(true));
require_once('../../config.php');
define('BENCHSTOP', microtime(true));

// Required files
require_once($CFG->libdir .'/adminlib.php');
require_once($CFG->dirroot.'/report/benchmark/locallib.php');
require_once($CFG->dirroot.'/report/benchmark/testlib.php');

// Login and check capabilities
require_login();
require_capability('report/benchmark:view', context_system::instance());

// Get the step
$step = optional_param('step', false, PARAM_TEXT);

// Set link & Layout
admin_externalpage_setup('reportbenchmark');
$PAGE->set_url(new moodle_url('/report/benchmark/index.php'));
$PAGE->set_pagelayout('report');

// Rendering
$output = $PAGE->get_renderer('report_benchmark');
echo !$step ? $output->launcher() : $output->display();
