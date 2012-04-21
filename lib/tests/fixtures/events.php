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
 * Test event handler definition used only from unit tests.
 *
 * @package    core
 * @subpackage event
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$handlers = array (
    'test_instant' => array (
        'handlerfile'      => '/lib/tests/eventslib_test.php',
        'handlerfunction'  => 'eventslib_sample_function_handler',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'test_cron' => array (
        'handlerfile'      => '/lib/tests/eventslib_test.php',
        'handlerfunction'  => array('eventslib_sample_handler_class', 'static_method'),
        'schedule'         => 'cron',
        'internal'         => 1,
    )
);

