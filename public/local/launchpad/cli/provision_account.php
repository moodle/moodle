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
 * CLI: staff-controlled LaunchPad account create / fellow enrol / mentor assign.
 *
 * Default is dry-run. --apply writes only on positively verified local targets.
 * Never grants Admin or Employee. Does not change signup/auth settings.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_launchpad\account_provision;
use local_launchpad\account_provision_exception;
use local_launchpad\apply_target;

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'apply' => false,
    'action' => '',
    'username' => '',
    'email' => '',
    'firstname' => '',
    'lastname' => '',
    'course' => '',
    'role' => account_provision::DEFAULT_MENTOR_ROLE,
], [
    'h' => 'help',
]);

if ($unrecognized) {
    cli_error(get_string('cliunknowoption', 'admin', implode("\n  ", $unrecognized)));
}

if (!empty($options['help']) || $options['action'] === '') {
    $help = <<<EOF
Staff-controlled LaunchPad account provisioning (Fellowship / Mentor only).

Options:
  -h, --help              Show this help
      --apply             Write (default dry-run). Local target only (same guard as structure CLI).
      --action=NAME       create-user | enrol-fellow | assign-mentor | grant-employee
      --username=NAME     Moodle username
      --email=ADDR        Required for create-user
      --firstname=NAME    Required for create-user
      --lastname=NAME     Required for create-user
      --course=IDNUMBER   LaunchPad course idnumber (lp-*), required for enrol/assign
      --role=SHORTNAME    Mentor role: editingteacher (default) or teacher

Refused by design: grant-admin, non-lp-* mentor/fellow courses.
Employee uses cohort lp-employee only (no course roles).
Does not enable public signup or change authentication settings.

Examples:
  php public/local/launchpad/cli/provision_account.php --action=create-user \\
      --username=fellow1 --email=fellow1@example.local --firstname=A --lastname=B
  php public/local/launchpad/cli/provision_account.php --action=enrol-fellow \\
      --username=fellow1 --course=lp-ai-pilot --apply
  php public/local/launchpad/cli/provision_account.php --action=assign-mentor \\
      --username=mentor1 --course=lp-ai-pilot --role=editingteacher --apply

EOF;
    cli_writeln($help);
    exit(0);
}

if (during_initial_install()) {
    cli_error('Moodle is not installed.');
}

$action = strtolower(trim((string) $options['action']));
$apply = !empty($options['apply']);

if ($apply && !apply_target::is_verified_local($CFG->wwwroot, $CFG->dbname)) {
    cli_error(apply_target::refusal_reason($CFG->wwwroot, $CFG->dbname));
}

// CLI runs as the first site admin (no interactive session).
$adminids = array_filter(array_map('intval', explode(',', (string) $CFG->siteadmins)));
if ($adminids === []) {
    cli_error('No site administrators configured.');
}
$actor = \core_user::get_user(reset($adminids), '*', MUST_EXIST);

$authsnapshot = account_provision::auth_config_snapshot();

try {
    switch ($action) {
        case 'create-user':
            $plan = [
                'action' => $action,
                'username' => $options['username'],
                'email' => $options['email'],
                'firstname' => $options['firstname'],
                'lastname' => $options['lastname'],
            ];
            cli_writeln(($apply ? 'APPLY' : 'DRY-RUN') . ' create-user ' . json_encode($plan));
            if ($apply) {
                $result = account_provision::create_manual_user($actor, [
                    'username' => (string) $options['username'],
                    'email' => (string) $options['email'],
                    'firstname' => (string) $options['firstname'],
                    'lastname' => (string) $options['lastname'],
                ]);
                cli_writeln('OK ' . json_encode($result));
            }
            break;

        case 'enrol-fellow':
            $course = account_provision::require_launchpad_course_by_idnumber((string) $options['course']);
            $user = $DB->get_record('user', [
                'username' => \core_text::strtolower(trim((string) $options['username'])),
                'mnethostid' => $CFG->mnet_localhost_id,
                'deleted' => 0,
            ], '*', MUST_EXIST);
            cli_writeln(($apply ? 'APPLY' : 'DRY-RUN') . " enrol-fellow user={$user->id} course={$course->id} {$course->idnumber}");
            if ($apply) {
                $result = account_provision::enrol_fellow($actor, (int) $user->id, (int) $course->id);
                cli_writeln('OK ' . json_encode($result));
            }
            break;

        case 'assign-mentor':
            $course = account_provision::require_launchpad_course_by_idnumber((string) $options['course']);
            $user = $DB->get_record('user', [
                'username' => \core_text::strtolower(trim((string) $options['username'])),
                'mnethostid' => $CFG->mnet_localhost_id,
                'deleted' => 0,
            ], '*', MUST_EXIST);
            $role = (string) $options['role'];
            cli_writeln(($apply ? 'APPLY' : 'DRY-RUN') .
                " assign-mentor user={$user->id} course={$course->id} {$course->idnumber} role={$role}");
            if ($apply) {
                $result = account_provision::assign_mentor($actor, (int) $user->id, (int) $course->id, $role);
                cli_writeln('OK ' . json_encode($result));
            }
            break;

        case 'grant-employee':
            $user = $DB->get_record('user', [
                'username' => \core_text::strtolower(trim((string) $options['username'])),
                'mnethostid' => $CFG->mnet_localhost_id,
                'deleted' => 0,
            ], '*', MUST_EXIST);
            cli_writeln(($apply ? 'APPLY' : 'DRY-RUN') . " grant-employee user={$user->id}");
            if ($apply) {
                $result = account_provision::grant_employee($actor, (int) $user->id);
                cli_writeln('OK ' . json_encode($result));
            }
            break;

        case 'grant-admin':
            account_provision::assert_grant_allowed('admin');
            break;

        default:
            cli_error('Unknown action. Use create-user, enrol-fellow, assign-mentor, or grant-employee.');
    }
} catch (account_provision_exception $e) {
    cli_error($e->getMessage());
}

$after = account_provision::auth_config_snapshot();
if ($after !== $authsnapshot) {
    cli_error('Refusing to continue: auth/signup config changed unexpectedly during provisioning.');
}

cli_writeln('Auth/signup config unchanged.');
exit(0);
