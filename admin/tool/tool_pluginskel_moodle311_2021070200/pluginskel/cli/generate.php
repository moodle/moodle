<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * CLI script for generating a plugin.
 *
 * @package     tool_pluginskel
 * @subpackage  cli
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\util;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/pluginskel/vendor/autoload.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/pluginskel/locallib.php');

// Get cli options.
[$options, $positional] = cli_get_params([
    'target-moodle' => '',
    'target-dir' => '',
    'list-files' => false,
    'file' => '',
    'decode' => false,
    'loglevel' => 'WARNING',
    'help' => false,
    'recipe' => '',
], [
    'l' => 'list-files',
    'd' => 'decode',
    'h' => 'help'
]);

$loglevels = Logger::getLevels();
$loglevelnames = implode(', ', array_keys($loglevels));

$help = "
Generate a Moodle plugin skeleton from the recipe file.

Usage:
    \$ php generate.php [--loglevel=<level>] --target-moodle=<path> | --target-dir=<path> <path-to-recipe>
    \$ php generate.php --list-files | -l <path-to-recipe>
    \$ php generate.php --file=<filename> <path-to-recipe>
    \$ php generate.php --decode | -d <path-to-recipe>
    \$ php generate.php [--help | -h]

Options:
    --target-moodle=<path>  Full path to the root directory of the target Moodle installation.
                            [default: $CFG->dirroot].
    --target-dir=<path>     Full path to the target location of the plugin.
    --list-files -l         Display the list of files that would be generated without actually generating them.
    --file=<filename>       Print the contents of generated file of the given name.
    --decode -d             Decode the YAML recipe and print it as a native PHP data structure.
    --loglevel=<level>      Logging verbosity level [default: WARNING].
    --help -h               Display this help message.
    <path-to-recipe>        Recipe file location.

Valid log levels are: $loglevelnames.

By default, the plugin skeleton is generated to the current Moodle's dirroot.
You can let generate to another Moodle installation via the --target-moodle
argument, or explicitly define the target location via the --target-dir
argument.

Examples:

* Generate skeleton of the plugin described in myplugin.yaml in this Moodle installation:

    \$ php generate.php myplugin.yaml

* Generate skeleton of the plugin in another Moodle installation and be verbose while doing so:

    \$ php generate.php --loglevel=DEBUG --target-moodle=/var/www/vhost/moodle_dev myplugin.yaml

* Generare skeleton of the plugin in the given folder:

    \$ php generate.php --target-dir=/tmp myplugin.yaml
";

if ($options['help']) {
    cli_writeln($help);
    die();
}

if (!empty($options['recipe'])) {
    // Legacy style using --recipe=<path> argument takes precedence.
    $recipefile = $options['recipe'];

    // No positional arguments are expected when --recipe=<path> is used.
    if (!empty($positional)) {
        cli_error(get_string('cliunknowoption', 'admin',  implode("\n  ", $positional)));
    }

} else if (empty($positional)) {
    cli_writeln($help);
    cli_error("Recipe not specified!");

} else if (count($positional) > 1) {
    cli_error(get_string('cliunknowoption', 'admin', implode(' ', $positional)));

} else {
    $recipefile = array_shift($positional);
}

$recipefile = tool_pluginskel_expand_path($recipefile);

if ($recipefile === false) {
    cli_error("Invalid recipe file!");
}

if (!is_readable($recipefile)) {
    cli_error("Recipe file not readable!");
}

// Load the recipe from file.
$recipe = yaml::decode_file($recipefile);

if ($options['decode']) {
    // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
    print_r($recipe);
    exit(0);
}

if (empty($recipe['component'])) {
    cli_error("The recipe does not provide the component for the plugin!");
}

[$plugintype, $pluginname] = \core_component::normalize_component($recipe['component']);

if ($plugintype === 'core') {
    cli_error("Core components not supported!");
}

// Create and configure the logger.

$loglevel = $options['loglevel'];
if (!array_key_exists($loglevel, $loglevels)) {
    cli_error("Invalid log level!");
}

$logger = new Logger('tool_pluginskel');
$logger->pushHandler(new StreamHandler('php://stdout', constant('\Monolog\Logger::'.$loglevel)));
$logger->debug('Logger initialised');

if (!empty($options['recipe'])) {
    $logger->warning('YAML recipe specified via the legacy --recipe argument. See --help for usage.');
}

$manager = manager::instance($logger);
$manager->load_recipe($recipe);
$manager->make();

if (!empty($options['list-files'])) {
    $filenames = array_keys($manager->get_files_content());
    sort($filenames);
    cli_writeln(implode(PHP_EOL, $filenames));
    exit(0);
}

if (!empty($options['file'])) {
    $files = $manager->get_files_content();

    if (!isset($files[$options['file']])) {
        cli_error("No such file generated from the given recipe. Use --list-files to see the list of generated files.");
    }

    cli_writeln($files[$options['file']]);
    exit(0);
}

if (!empty($options['target-dir']) && !empty($options['target-moodle'])) {
    cli_error("Specify either 'target-dir' or 'target-moodle'!");
}

if (!empty($options['target-dir'])) {

    $targetdir = $options['target-dir'];
    $targetdir = tool_pluginskel_expand_path($targetdir);
    if ($targetdir === false) {
        cli_error("Invalid target directory!");
    }

    if (!is_writable($targetdir)) {
        cli_error("Target plugin location is not writable!");
    }

    $targetdir = $targetdir.'/'.$pluginname;

} else {

    if (empty($options['target-moodle'])) {
        $targetdir = $CFG->dirroot;
    } else {
        $targetdir = $options['target-moodle'];
        $targetdir = tool_pluginskel_expand_path($targetdir);
        if ($targetdir === false) {
            cli_error("Invalid target directory!");
        }
    }

    if (!is_writable($targetdir)) {
        cli_error("Target plugin location is not writable!");
    }

    $plugintypes = \core_component::get_plugin_types();

    if (empty($plugintypes[$plugintype])) {
        cli_error("Unknown plugin type '$plugintype'!");
    }

    $targetdir = $targetdir.substr($plugintypes[$plugintype], strlen($CFG->dirroot));
    $targetdir = $targetdir.'/'.$pluginname;
}

if (file_exists($targetdir)) {
    cli_error("Target plugin location already exists: ".$targetdir);
}

$manager->write_files($targetdir);
cli_writeln('Plugin skeleton files generated: '.$targetdir);
