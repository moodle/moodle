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
 * CLI interface for creating an Iomad test site.
 *
 * @package tool_iomadsite
 * @copyright 2019 Howard Miller
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir. '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
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
if (!empty($options['help'])) {
    echo "
Utility to generate test data for an Iomad site. 

Not for use on live sites; only normally works if debugging is set to DEVELOPER
level.

Options:
--size           Size of the generated site, this value affects the number of courses and their size. Accepted values: XS, S, M, L, XL, or XXL (required)
--fixeddataset   Use a fixed data set instead of randomly generated data
--filesizelimit  Limits the size of the generated files to the specified bytes
--bypasscheck    Bypasses the developer-mode check (be careful!)
--quiet          Do not show any output

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/iomadsite/cli/maketestsite.php --size=S
";
    // Exit with error unless we're showing this because they asked for it.
    exit(empty($options['help']) ? 1 : 0);
}

// Check debugging is set to developer level.
if (empty($options['bypasscheck']) && !$CFG->debugdeveloper) {
    cli_error(get_string('error_notdebugging', 'tool_generator'));
}

// Get options.
//$sizename = $options['size'];
//$fixeddataset = $options['fixeddataset'];
//$filesizelimit = $options['filesizelimit'];

// Switch to admin user account.
\core\session\manager::set_user(get_admin());

// Do backend code to generate site.
$generate = new tool_iomadsite\generate();
$generate->companies();
