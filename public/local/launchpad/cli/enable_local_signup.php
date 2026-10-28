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
 * Enable Moodle email self-registration on a verified local site only.
 *
 * Does not assign Fellowship/Mentor/Admin/Employee. New accounts are ordinary
 * authenticated users until staff enrol them.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/authlib.php');

use local_launchpad\apply_target;

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'apply' => false,
    'disable' => false,
], ['h' => 'help']);

if ($unrecognized) {
    cli_error(get_string('cliunknowoption', 'admin', implode("\n  ", $unrecognized)));
}

if (!empty($options['help'])) {
    cli_writeln(<<<EOF
Enable or disable email self-registration (local sites only).

  --apply     Write config (default dry-run)
  --disable   Clear registerauth (turn signup off)

New signups never receive Mentor/Admin/Employee or lp-* enrolment automatically.
EOF);
    exit(0);
}

if (!apply_target::is_verified_local($CFG->wwwroot, $CFG->dbname)) {
    cli_error(apply_target::refusal_reason($CFG->wwwroot, $CFG->dbname));
}

$disable = !empty($options['disable']);
$apply = !empty($options['apply']);
$target = $disable ? '' : 'email';

cli_writeln(($apply ? 'APPLY' : 'DRY-RUN') . ' registerauth=' . var_export($target, true));
cli_writeln('Current registerauth=' . var_export((string) ($CFG->registerauth ?? ''), true));
cli_writeln('signup_is_enabled=' . (signup_is_enabled() ? 'yes' : 'no'));

if ($apply) {
    // Ensure email auth plugin remains listed (manual/nologin are always on).
    $auths = array_filter(array_map('trim', explode(',', (string) ($CFG->auth ?? ''))));
    if (!$disable && !in_array('email', $auths, true)) {
        $auths[] = 'email';
        set_config('auth', implode(',', $auths));
        cli_writeln('Enabled auth plugin: email');
    }
    set_config('registerauth', $target);
    purge_caches(['config']);
    // Reload.
    $CFG->registerauth = $target;
    cli_writeln('Wrote registerauth=' . var_export($target, true));
    cli_writeln('signup_is_enabled=' . (signup_is_enabled() ? 'yes' : 'no'));
}

exit(0);
