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
 * Rector Bootstrap.
 *
 * @package    core
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState

global $CFG;

$CFG = (object) [
    'admin' => 'admin',
    'root' => dirname(__DIR__),
    'dirroot' => dirname(__DIR__) . '/public',
    'libdir' => dirname(__DIR__) . '/public/lib',
    'cachedir' => sys_get_temp_dir(),
];

require_once(__DIR__ . '/../public/lib/classes/component.php');
ini_set('include_path', $CFG->libdir . '/pear' . PATH_SEPARATOR . ini_get('include_path'));

// Component's autoloader only resolves classes under `<component>/tests/classes` and
// `<component>/tests/behat` (e.g. component-specific test base classes/traits) when
// PHPUNIT_TEST is truthy. Without this, Rector's reflection cannot resolve the class
// hierarchy of test classes which extend such helper classes, so it silently fails to
// recognise them as PHPUnit test classes and skips converting their annotations
// (@covers, @dataProvider, etc.) to attributes.
define('PHPUNIT_TEST', true);
\core\component::register_autoloader();

define('CACHE_DISABLE_ALL', false);
define('MOODLE_INTERNAL', true);
define('IGNORE_COMPONENT_CACHE', true);

require_once($CFG->libdir . '/setuplib.php');        // Functions that MUST be loaded first.

// Load up standard libraries.
require_once($CFG->libdir . '/filterlib.php');       // Functions for filtering test as it is output.
require_once($CFG->libdir . '/weblib.php');          // Functions relating to HTTP and content.
require_once($CFG->libdir . '/outputlib.php');       // Functions for generating output.
require_once($CFG->libdir . '/dmllib.php');          // Database access.
require_once($CFG->libdir . '/datalib.php');         // Legacy lib with a big-mix of functions..
require_once($CFG->libdir . '/accesslib.php');       // Access control functions.
require_once($CFG->libdir . '/deprecatedlib.php');   // Deprecated functions included for backward compatibility.
require_once($CFG->libdir . '/moodlelib.php');       // Other general-purpose functions.
require_once($CFG->libdir . '/enrollib.php');        // Enrolment related functions.
require_once($CFG->libdir . '/pagelib.php');         // Library that defines the moodle_page class, used for $PAGE.
require_once($CFG->libdir . '/blocklib.php');        // Library for controlling blocks.
require_once($CFG->libdir . '/grouplib.php');        // Groups functions.
require_once($CFG->libdir . '/sessionlib.php');      // All session and cookie related stuff.
require_once($CFG->libdir . '/editorlib.php');       // All text editor related functions and classes.
require_once($CFG->libdir . '/messagelib.php');      // Messagelib functions.
require_once($CFG->libdir . '/modinfolib.php');      // Cached information on course-module instances.

// Load PHPUnit library for testing.
require_once($CFG->libdir . '/phpunit/lib.php');
