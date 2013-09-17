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
 * CLI interface for creating a test course.
 *
 * @package tool_generator
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);

require(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir. '/clilib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/generator/classes/course_backend.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
        'shortname' => false,
        'size' => false,
        'fixeddataset' => false,
        'filesizelimit' => false,
        'bypasscheck' => false,
        'quiet' => false
    ),
    array(
        'h' => 'help'
    )
);

// Display help.
if (!empty($options['help']) || empty($options['shortname']) || empty($options['size'])) {
    echo "
Utility to create standard test course. (Also available in GUI interface.)

Not for use on live sites; only normally works if debugging is set to DEVELOPER
level.

Options:
--shortname      Shortname of course to create (required)
--size           Size of course to create XS, S, M, L, XL, or XXL (required)
--fixeddataset   Use a fixed data set instead of randomly generated data
--filesizelimit  Limits the size of the generated files to the specified bytes
--bypasscheck    Bypasses the developer-mode check (be careful!)
--quiet          Do not show any output

-h, --help     Print out this help

Example from Moodle root directory:
\$sudo -u www-data /usr/bin/php admin/tool/generator/cli/maketestcourse.php --shortname=SIZE_S --size=S
";
    // Exit with error unless we're showing this because they asked for it.
    exit(empty($options['help']) ? 1 : 0);
}

// Check debugging is set to developer level.
if (empty($options['bypasscheck']) && !debugging('', DEBUG_DEVELOPER)) {
    cli_error(get_string('error_notdebugging', 'tool_generator'));
}

// Get options.
$shortname = $options['shortname'];
$sizename = $options['size'];
$fixeddataset = $options['fixeddataset'];
$filesizelimit = $options['filesizelimit'];

// Check size.
try {
    $size = tool_generator_course_backend::size_for_name($sizename);
} catch (coding_exception $e) {
    cli_error("Invalid size ($sizename). Use --help for help.");
}

// Check shortname.
if ($error = tool_generator_course_backend::check_shortname_available($shortname)) {
    cli_error($error);
}

// Switch to admin user account.
session_set_user(get_admin());

// Do backend code to generate course.
$backend = new tool_generator_course_backend($shortname, $size, $fixeddataset, $filesizelimit, empty($options['quiet']));
$id = $backend->make();
