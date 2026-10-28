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
 * CLI: create LaunchPad categories, hidden courses, sections, placeholders.
 *
 * Default is dry-run. Pass --apply to write. --apply is fail-closed and only
 * runs when the target is positively verified as local.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'apply' => false,
    'cohort' => 'Pilot',
], [
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = <<<EOF
Create LaunchPad category/course/section/activity placeholders (additive).

Options:
  -h, --help         Show this help
      --apply        Write to the database (default is dry-run).
                     Refuses unless the site is positively verified as local.
      --cohort=X     Cohort label in course names (default: Pilot).
                     Must yield a non-empty slug of letters/digits; validated
                     against Moodle shortname/idnumber length limits.

Examples:
  php public/local/launchpad/cli/setup_structure.php
  php public/local/launchpad/cli/setup_structure.php --cohort=2026-Pilot
  php public/local/launchpad/cli/setup_structure.php --cohort=2026-Pilot --apply

Local --apply requires loopback wwwroot (localhost / 127.0.0.1 / ::1) plus one of:
  - dbname like moodle_phpunit or *_local
  - MOODLE_DOCKER_PHPUNIT set
  - MOODLE_LAUNCHPAD_ALLOW_APPLY=1
Denied hosts (e.g. *.toba-tech*, learn.*) never allow --apply.

Safe retry after a partial failure:
  Re-run the same command. Creates are idempotent by idnumber; existing
  visibility / name / placement are preserved. Fix the reported operation
  (course/activity) then retry — do not reset the database.

EOF;
    cli_writeln($help);
    exit(0);
}

if (during_initial_install()) {
    cli_error('Moodle is not installed. Run install before using this CLI.');
}

$admin = get_admin();
if (!$admin) {
    cli_error('No admin user found.');
}
\core\session\manager::set_user($admin);

$apply = !empty($options['apply']);
$cohort = (string) $options['cohort'];

cli_writeln('local_launchpad setup_structure');
cli_writeln('wwwroot: ' . $CFG->wwwroot);
cli_writeln('dbname: ' . $CFG->dbname);

try {
    $slug = \local_launchpad\cohort::validate($cohort);
} catch (\local_launchpad\cohort_exception $e) {
    cli_error($e->getMessage());
}

cli_writeln('cohort: ' . trim($cohort) . ' (slug=' . $slug . ')');

if ($apply) {
    $dbname = (string) $CFG->dbname;
    if (!\local_launchpad\apply_target::is_verified_local($CFG->wwwroot, $dbname)) {
        $reason = \local_launchpad\apply_target::refusal_reason($CFG->wwwroot, $dbname);
        cli_error('Refusing --apply: ' . $reason
            . '. Dry-run remains available without --apply.');
    }
    cli_writeln('apply_target: verified local');
}

$setup = new \local_launchpad\setup($cohort, !$apply);
try {
    $setup->execute();
} catch (\local_launchpad\operation_failure $e) {
    cli_writeln('ERROR: ' . $e->getMessage());
    cli_writeln('Safe retry: re-run the same command after fixing the failed operation; '
        . 'creates are idempotent by idnumber; existing visibility is preserved.');
    exit(1);
} catch (\local_launchpad\cohort_exception $e) {
    cli_error($e->getMessage());
} catch (Throwable $e) {
    cli_writeln('ERROR: ' . $e->getMessage());
    cli_writeln('Safe retry: re-run the same command after fixing the cause; '
        . 'creates are idempotent by idnumber; existing visibility is preserved.');
    exit(1);
}

exit(0);
