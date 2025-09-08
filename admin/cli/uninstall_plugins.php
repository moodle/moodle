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
 * CLI script to uninstall plugins.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/adminlib.php');

$help = "Command line tool to uninstall plugins.

Options:
    -h --help                   Print this help.
    --show-all                  Displays a list of all installed plugins.
    --show-contrib              Displays a list of all third-party installed plugins.
    --show-missing              Displays a list of plugins missing from disk.
    --purge-missing             Uninstall all missing from disk plugins.
    --plugins=<plugin name>     A comma separated list of plugins to be uninstalled. E.g. mod_assign,mod_forum
    --run                       Execute uninstall. If this option is not set, then the script will be run in a dry mode.
    --showsql                   Show sql queries before they are executed.
    --showdebugging             Show developer level debugging information.

Examples:

    # php uninstall_plugins.php  --show-all
        Prints tab-separated list of all installed plugins.

    # php uninstall_plugins.php  --show-contrib
        Prints tab-separated list of all third-party installed plugins.

    # php uninstall_plugins.php  --show-missing
        Prints tab-separated list of all missing from disk plugins.

    # php uninstall_plugins.php  --purge-missing
        A dry run of uninstalling all missing plugins.

    # php uninstall_plugins.php  --purge-missing --run
        Run uninstall of all missing plugins.

    # php uninstall_plugins.php  --plugins=mod_assign,mod_forum
        A dry run of uninstalling mod_assign and mod_forum plugins.

    # php uninstall_plugins.php  --plugins=mod_assign,mod_forum --run
        Run uninstall for mod_assign and mod_forum plugins.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'show-all' => false,
    'show-contrib' => false,
    'show-missing' => false,
    'purge-missing' => false,
    'plugins' => false,
    'run' => false,
    'showsql' => false,
    'showdebugging' => false,
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($help);
    exit(0);
}

if ($options['showdebugging']) {
    set_debugging(DEBUG_DEVELOPER, true);
}

if ($options['showsql']) {
    $DB->set_debug(true);
}

$pluginman = core_plugin_manager::instance();
$plugininfo = $pluginman->get_plugins();

if ($options['show-all'] || $options['show-missing'] || $options['show-contrib']) {
    foreach ($plugininfo as $type => $plugins) {
        foreach ($plugins as $name => $plugin) {
            if ($options['show-contrib'] && $plugin->is_standard()) {
                continue;
            }
            $pluginstring = $plugin->component . "\t" . $plugin->displayname;

            if ($options['show-all'] || $options['show-contrib']) {
                cli_writeln($pluginstring);
            } else {
                if ($plugin->get_status() === core_plugin_manager::PLUGIN_STATUS_MISSING) {
                    cli_writeln($pluginstring);
                }
            }
        }
    }

    exit(0);
}

if ($options['purge-missing']) {
    foreach ($plugininfo as $type => $plugins) {
        foreach ($plugins as $name => $plugin) {
            if ($plugin->get_status() === core_plugin_manager::PLUGIN_STATUS_MISSING) {

                $pluginstring = $plugin->component . "\t" . $plugin->displayname;

                if ($pluginman->can_uninstall_plugin($plugin->component)) {
                    if ($options['run']) {
                        cli_writeln('Uninstalling: ' . $pluginstring);

                        $progress = new progress_trace_buffer(new text_progress_trace(), true);
                        $pluginman->uninstall_plugin($plugin->component, $progress);
                        $progress->finished();
                        cli_write($progress->get_buffer());
                    } else {
                        cli_writeln('Will be uninstalled: ' . $pluginstring);
                    }
                } else {
                    cli_writeln('Can not be uninstalled: ' . $pluginstring);
                }
            }
        }
    }

    exit(0);
}

if ($options['plugins']) {
    $components = explode(',', $options['plugins']);
    foreach ($components as $component) {
        $plugin = $pluginman->get_plugin_info($component);

        if (is_null($plugin)) {
            cli_writeln('Unknown plugin: ' . $component);
        } else {
            $pluginstring = $component . "\t" . $plugin->displayname;

            if ($pluginman->can_uninstall_plugin($component)) {
                if ($options['run']) {
                    cli_writeln('Uninstalling: ' . $pluginstring);
                    $progress = new progress_trace_buffer(new text_progress_trace(), true);
                    $pluginman->uninstall_plugin($component, $progress);
                    $progress->finished();
                    cli_write($progress->get_buffer());
                } else {
                    cli_writeln('Will be uninstalled: ' . $pluginstring);
                }
            } else {
                cli_writeln('Can not be uninstalled: ' . $pluginstring);
            }
        }
    }

    exit(0);
}

cli_writeln($help);
exit(0);
