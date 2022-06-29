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
 * Moodle PHPUnit integration
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: MOODLE_INTERNAL is not verified here because we load this before setup.php!

require_once(__DIR__.'/classes/util.php');
require_once(__DIR__.'/classes/phpunit_dataset.php');
require_once(__DIR__.'/classes/event_mock.php');
require_once(__DIR__.'/classes/event_sink.php');
require_once(__DIR__.'/classes/message_sink.php');
require_once(__DIR__.'/classes/phpmailer_sink.php');
require_once(__DIR__.'/classes/base_testcase.php');
require_once(__DIR__.'/classes/basic_testcase.php');
require_once(__DIR__.'/classes/database_driver_testcase.php');
require_once(__DIR__.'/classes/advanced_testcase.php');
require_once(__DIR__.'/classes/constraint_object_is_equal_with_exceptions.php');
require_once(__DIR__.'/../testing/classes/test_lock.php');
require_once(__DIR__.'/../testing/classes/tests_finder.php');
