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
 * Seed Fellowship + Employer demo accounts for local development.
 *
 * Usage: php local/launchpad/cli/seed_demo_users.php
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/user/lib.php');

use local_launchpad\account_persona;
use local_launchpad\account_provision;
use local_launchpad\registration;

[$options, $unrecognized] = cli_get_params(
    [
        'help' => false,
        'password' => 'DemoPass1!',
    ],
    [
        'h' => 'help',
        'p' => 'password',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!empty($options['help'])) {
    $help = <<<EOF
Seed ToBa Learn demo users (Fellowship + Employer).

Options:
  -h, --help         Print this help
  -p, --password     Shared password (default: DemoPass1!)

EOF;
    echo $help;
    exit(0);
}

$password = (string) $options['password'];
$demos = [
    [
        'username' => 'fellow.demo',
        'email' => 'fellow.demo@example.com',
        'firstname' => 'Fellow',
        'lastname' => 'Demo',
        'city' => 'Mumbai',
        'country' => 'IN',
        'portal' => registration::PORTAL_FELLOWSHIP,
        'label' => 'Fellowship',
    ],
    [
        'username' => 'employer.demo',
        'email' => 'employer.demo@example.com',
        'firstname' => 'Employer',
        'lastname' => 'Demo',
        'city' => 'Bengaluru',
        'country' => 'IN',
        'portal' => 'employer',
        'label' => 'Employer',
    ],
];

$created = [];

foreach ($demos as $demo) {
    $existing = $DB->get_record('user', ['username' => $demo['username'], 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0]);

    if ($existing) {
        $user = $existing;
        $user->password = $password;
        $user->email = $demo['email'];
        $user->firstname = $demo['firstname'];
        $user->lastname = $demo['lastname'];
        $user->city = $demo['city'];
        $user->country = $demo['country'];
        $user->confirmed = 1;
        $user->suspended = 0;
        $user->auth = 'manual';
        user_update_user($user, true, false);
        set_user_preference(registration::PREF_PORTAL, $demo['portal'], $user->id);
        $action = 'updated';
    } else {
        $user = new stdClass();
        $user->auth = 'manual';
        $user->username = $demo['username'];
        $user->password = $password;
        $user->firstname = $demo['firstname'];
        $user->lastname = $demo['lastname'];
        $user->email = $demo['email'];
        $user->emailstop = 0;
        $user->city = $demo['city'];
        $user->country = $demo['country'];
        $user->lang = 'en';
        $user->timezone = '99';
        $user->confirmed = 1;
        $user->mnethostid = $CFG->mnet_localhost_id;
        $userid = user_create_user($user, true, false);
        set_user_preference(registration::PREF_PORTAL, $demo['portal'], $userid);
        $user->id = $userid;
        $action = 'created';
    }

    $cohortnote = '';
    if ($demo['portal'] === 'employer') {
        // Demo employer needs lp-employee cohort to open /local/launchpad/employee.php.
        $cohortid = account_provision::ensure_employee_cohort();
        require_once($CFG->dirroot . '/cohort/lib.php');
        if (!cohort_is_member($cohortid, (int) $user->id)) {
            cohort_add_member($cohortid, (int) $user->id);
            $cohortnote = 'cohort=' . account_persona::EMPLOYEE_COHORT_IDNUMBER . ':added';
        } else {
            $cohortnote = 'cohort=' . account_persona::EMPLOYEE_COHORT_IDNUMBER . ':already';
        }
    }

    $created[] = [
        'label' => $demo['label'],
        'action' => $action,
        'username' => $demo['username'],
        'email' => $demo['email'],
        'portal' => $demo['portal'],
        'id' => $user->id,
        'cohort' => $cohortnote,
        'login' => (new moodle_url('/login/index.php', ['portal' => $demo['portal']]))->out(false),
    ];
}

cli_heading('ToBa Learn demo accounts');
foreach ($created as $row) {
    cli_writeln(sprintf(
        "[%s] %s  username=%s  email=%s  portal=%s  id=%d",
        strtoupper($row['action']),
        $row['label'],
        $row['username'],
        $row['email'],
        $row['portal'],
        $row['id']
    ));
    if (!empty($row['cohort'])) {
        cli_writeln('  ' . $row['cohort']);
    }
    cli_writeln('  login: ' . $row['login']);
    if ($row['portal'] === 'employer') {
        cli_writeln('  portal: ' . (new moodle_url('/local/launchpad/employee.php'))->out(false));
    }
}
cli_writeln('');
cli_writeln('Shared password: ' . $password);
exit(0);
