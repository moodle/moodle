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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    https://intelliboard.net/
 */

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\ExportHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\export_service;
use local_intellidata\services\migration_service;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\helpers\MigrationHelper;

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

raise_memory_limit(MEMORY_HUGE);

$longoptions = [
    'datatype'  => '',
    'noreset' => '',
    'help' => false,
];
list($options, $unrecognized) = cli_get_params($longoptions, ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    // The indentation of this string is "wrong" but this is to avoid a extra whitespace in console output.
    $help = <<<EOF
Invalidates Moodle internal caches

Specific caches can be defined (alone or in combination) using arguments. If none are specified,
all caches will be purged.

Options:
-h, --help            Print out this help
    --datatype        Datatype name to export data
    --noreset         Disable reset migrations and start from beginning

Example:
\$ sudo -u www-data /usr/bin/php local/intellidata/cli/migration_process.php

EOF;

    echo $help;
    exit(0);
}

// Validate if plugin is enabled and configured.
if (!TrackingHelper::enabled()) {
    mtrace(get_string('pluginnotconfigured', 'local_intellidata'));
    exit(0);
}

// Validate if forcedisablemigration setting enabled.
if (MigrationHelper::migration_disabled()) {
    mtrace(get_string('migrationdisabled', 'local_intellidata'));
    exit(0);
}

DebugHelper::enable_moodle_debug();

$exportservice = new export_service();

// Disable reset migrations.
if (empty($options['noreset'])) {
    // Clean migration details and logs database.
    MigrationHelper::reset_migration_details();

    // Delete all IntelliData files.
    $filesrecords = $exportservice->delete_all_files(['timemodified' => time()]);
}

$params = [];
if (!empty($options['datatype'])) {
    $params['datatype'] = $options['datatype'];
}

// Set migration time.
SettingsHelper::set_lastmigrationdate();

MigrationHelper::disable_sheduled_tasks();

try {
    // Export static tables.
    $exportservice->set_migration_mode();
    $migrationservice = new migration_service(null, $exportservice);
    $migrationservice->process($params);

    if (empty($params['datatype'])) {
        // Export files.
        $params = [];
        if (TrackingHelper::new_tracking_enabled()) {
            $params['forceexport'] = true;
        }

        ExportHelper::process_export($exportservice, $params);

        // Send callback to IBN.
        MigrationHelper::send_callback();
    }

    MigrationHelper::enable_sheduled_tasks(['\local_intellidata\task\migration_task']);
} catch (\Exception $e) {
    MigrationHelper::enable_sheduled_tasks(['\local_intellidata\task\migration_task']);

    throw new \Exception($e);
}

exit(0);
