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
 * CLI script allowing to get and set config values.
 *
 * This is technically just a thin wrapper for {@link get_config()} and
 * {@link set_config()} functions.
 *
 * @package     core
 * @subpackage  cli
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

$usage = "Displays the current value of the given site setting. Allows to set it to the given value, too.

Usage:
    # php cfg.php [--component=<componentname>] [--json] [--shell-arg]
    # php cfg.php --name=<configname> [--component=<componentname>] [--shell-arg] [--no-eol]
    # php cfg.php --name=<configname> [--component=<componentname>] --set=<value>
    # php cfg.php --name=<configname> [--component=<componentname>] --unset
    # php cfg.php [--help|-h]

Options:
    -h --help                   Print this help.
    --component=<frankenstyle>  Name of the component the variable is part of. Defaults to core.
    --name=<configname>         Name of the configuration variable to get/set. If missing, print all
                                configuration variables of the given component.
    --set=<value>               Set the given variable to this value.
    --unset                     Unset the given variable.
    --shell-arg                 Escape output values so that they can be directly used as shell script arguments.
    --json                      Encode output list of values using JSON notation.
    --no-eol                    Do not include the trailing new line character when printing the value.

The list of all variables of the given component can be printed as
tab-separated list (default) or JSON object (--json). Particular values are
printed as raw text values, optionally escaped so that they can be directly
used as shell script arguments (--shell-arg). Single values are displayed with
trailing new line by default, unless explicitly disabled (--no-eol).

In the read mode, the script exits with success status 0 if the requested value
is found. If the requested variable is not set, the script exits with status 3.
When listing all variables of the component, the exit status is always 0 even
if no variables for the given component are found. When setting/unsetting a
value, the exit status is 0. When attempting to set/unset a value that has
already been hard-set in config.php, the script exits with error status 4. In
case of unexpected error, the script exits with error status 1.

Examples:

    # php cfg.php
        Prints tab-separated list of all core configuration variables and their values.

    # php cfg.php --json
        Prints list of all core configuration variables and their values as a JSON object.

    # php cfg.php --name=release
        Prints the given configuration variable - e.g. \$CFG->release in this case.

    # php cfg.php --component=tool_recyclebin
    #   Prints tab-separated list of the plugin's configuration variables.

    # export DATAROOT=\$(php cfg.php --name=dataroot --shell-arg --no-eol)
        Stores the given configuration variable in the shell variable, escaped
        so that it can be safely used as a shell argument.

    # php cfg.php --name=theme --set=classic
        Sets the given configuration variable to the given value.

    # php cfg.php --name=noemailever --unset
        Unsets the previously configured variable.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'component' => null,
    'name' => null,
    'set' => null,
    'unset' => false,
    'shell-arg' => false,
    'json' => false,
    'no-eol' => false,
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

if ($options['unset'] || $options['set'] !== null) {
    // Unset the variable or set it to the given value.
    if (empty($options['name'])) {
        cli_error('Missing configuration variable name', 2);
    }

    // Check that the variable is not hard-set in the main config.php already.
    if (array_key_exists($options['name'], $CFG->config_php_settings)) {
        cli_error('The configuration variable is hard-set in the config.php, unable to change.', 4);
    }

    $new = $options['set'];
    $old = get_config($options['component'], $options['name']);
    if ($new !== $old) {
        set_config($options['name'], $options['set'], $options['component']);
        add_to_config_log($options['name'], $old, $new, $options['component']);
    }
    exit(0);
}

if ($options['name'] === null) {
    // List all variables provided by the component (defaults to core).
    $got = get_config($options['component']);

    if ($options['json']) {
        cli_writeln(json_encode($got));

    } else {
        foreach ($got as $name => $value) {
            if ($options['shell-arg']) {
                $value = escapeshellarg($value);
            }
            cli_writeln($name."\t".$value);
        }
    }

    exit(0);

} else {
    // Display the value of a single variable.

    $got = get_config($options['component'], $options['name']);

    if ($got === false) {
        cli_error('No such configuration variable found.', 3);
    }

    if ($options['shell-arg']) {
        $got = escapeshellarg($got);
    }

    if ($options['json']) {
        $got = json_encode($got);
    }

    if ($options['no-eol']) {
        cli_write($got);
    } else {
        cli_writeln($got);
    }

    exit(0);
}
