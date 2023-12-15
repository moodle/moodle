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
 * This script allows you to view and change the emailstop flag of any user.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2023 Stephan Robotta (stephan.robotta@bfh.ch)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

// Define the input options.
$longparams = [
    'email' => '',
    'help' => false,
    'id' => '',
    'quiet' => false,
    'stop' => '',
    'username' => '',
];

$shortparams = [
    'e' => 'email',
    'h' => 'help',
    'i' => 'id',
    'q' => 'quiet',
    's' => 'stop',
    'u' => 'username',
];

// Define exit codes.
$exitsuccess = 0;
$exitunknownoption = 1;
$exitmissinguserarg = 2;
$exittoomanyuserarg = 3;
$exitnosearchargs = 4;
$exitnousersfound = 5;
$exitinvalidstopflag = 6;
$exiterrordb = 7;

// Now get cli options that are set by the caller.
list($options, $unrecognized) = cli_get_params($longparams, $shortparams);

$verbose = empty($options['quiet']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    if ($verbose) {
        cli_error(get_string('cliunknowoption', 'admin', $unrecognized), $exitunknownoption);
    }
    exit($exitunknownoption);
}

if ($options['help']) {
    $help =
        "Set/unset or show status of emailstop flag for a user, identified by username or email.

There are no security checks here because anybody who is able to
execute this file may execute any PHP too.

Options:
-h, --help                    Print out this help
-e, --email=email             Specify user by email, separate many users by comma
-i, --id=id                   Specify user by id, separate many users by comma
-q, --quiet                   No output to stdout
-s, --stop=0|1|off|on         Set new value for emailstop flag
-u, --username=username       Specify user by username, separate many users by comma

Example:
\$sudo -u www-data /usr/bin/php admin/cli/emailstop.php --email=student1@example.com --stop=1
\$sudo -u www-data /usr/bin/php admin/cli/emailstop.php --email=student1@example.com,student2@example.com
\$sudo -u www-data /usr/bin/php admin/cli/emailstop.php --u=student1,student2 -s=on
";

    echo $help;
    exit($exitsuccess);
}

$cntempty = 0;
$cntfilled = 0;
$searchargs = [];

// Try to find out which option is used to fetch the users from. Also do sanitize etc.
foreach (['email', 'username', 'id'] as $option) {
    if (empty($options[$option])) {
        $cntempty++;
    } else {
        $cntfilled++;
        $argname = $option;
        // The search args must be: split by the comma, trimmed, and empty elements filtered out.
        $searchargs = array_flip(array_filter(
            array_map(
                function ($item) {
                    return trim($item);
                },
                explode(',', $options[$option])
            ),
            function ($item) {
                return $item !== '';
            }
        ));
    }
}
if ($cntempty === 3) {
    if ($verbose) {
        cli_error('One of username, email, or id must be set.', $exitmissinguserarg);
    }
    exit($exitmissinguserarg);
}
if ($cntfilled > 1) {
    if ($verbose) {
        cli_error('Only one of email, username, or id can be set to identify a user.', $exittoomanyuserarg);
    }
    exit($exittoomanyuserarg);
}
if (empty($searchargs)) {
    if ($verbose) {
        cli_error('No values are provided for users.', $exitnosearchargs);
    }
    exit($exitnosearchargs);
}
try {
    $users = $DB->get_records_list('user', $argname, array_keys($searchargs));
} catch (Exception $e) {
    if ($verbose) {
        cli_error("Could not fetch data from db by {$argname}: '{$options[$argname]}'.", $exiterrordb);
    }
    exit($exiterrordb);
}
if (empty($users)) {
    if ($verbose) {
        cli_error("Can not find any user by {$argname}: '{$options[$argname]}'.", $exitnousersfound);
    }
    exit($exitnousersfound);
}

// No stop flag set, then just print the user and the current emailstop flag state.
if ($options['stop'] === '') {
    foreach ($users as $user) {
        if ($verbose) {
            echo 'user=' . $user->{$argname} . ' - emailstop=' . (int)$user->emailstop . PHP_EOL;
            unset($searchargs[$user->{$argname}]);
        }
    }
    if ($verbose) {
        foreach (array_keys($searchargs) as $arg) {
            echo 'user=' . $arg . ' - not found' . PHP_EOL;
        }
    }
    exit($exitsuccess);
}

// Allowed values for the stop flag enabled are 1 and on, for disabled are 0 and off.
$validvalues = ['0', '1', 'off', 'on'];
$stopflag = strtolower($options['stop']);
if (!in_array($stopflag, $validvalues)) {
    if ($verbose) {
        cli_error('Value for the emailstop flag must be one of: ' . implode(', ', $validvalues) . '.', $exitinvalidstopflag);
    }
    exit($exitinvalidstopflag);
}

foreach ($validvalues as $value) {
    if ($value === $stopflag) {
        $stopflag = ($value === '1' || $value === 'on') ? 1 : 0;
        break;
    }
}
// Update each user with the stop flag to be set if it is necessary.
foreach ($users as $user) {
    $line = 'Update user ' . $user->{$argname} . ($argname !== 'id' ? ' (' . $user->id . ')' : '') . ' - ';
    if ((int)$user->emailstop !== $stopflag) {
        $DB->set_field('user', 'emailstop', $stopflag, ['id' => $user->id]);
        $line .= 'ok';
    } else {
        $line .= 'already done';
    }
    if ($verbose) {
        echo $line . PHP_EOL;
        unset($searchargs[$user->{$argname}]);
    }

}
if ($verbose) {
    foreach (array_keys($searchargs) as $arg) {
        echo 'user=' . $arg . ' - not found' . PHP_EOL;
    }
}
exit($exitsuccess);

