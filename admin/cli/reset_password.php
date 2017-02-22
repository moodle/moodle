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
 * This script allows you to reset any local user password.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

// Define the input options.
$longparams = array(
        'help' => false,
        'username' => '',
        'password' => '',
        'ignore-password-policy' => false
);

$shortparams = array(
        'h' => 'help',
        'u' => 'username',
        'p' => 'password',
        'i' => 'ignore-password-policy'
);

// now get cli options
list($options, $unrecognized) = cli_get_params($longparams, $shortparams);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Reset local user passwords, useful especially for admin acounts.

There are no security checks here because anybody who is able to
execute this file may execute any PHP too.

Options:
-h, --help                    Print out this help
-u, --username=username       Specify username to change
-p, --password=newpassword    Specify new password
--ignore-password-policy      Ignore password policy when setting password

Example:
\$sudo -u www-data /usr/bin/php admin/cli/reset_password.php
\$sudo -u www-data /usr/bin/php admin/cli/reset_password.php --username=rosaura --password=jiu3jiu --ignore-password-policy
";

    echo $help;
    die;
}
if ($options['username'] == '' ) {
    cli_heading('Password reset');
    $prompt = "Enter username (manual authentication only)";
    $username = cli_input($prompt);
} else {
    $username = $options['username'];
}

if (!$user = $DB->get_record('user', array('auth'=>'manual', 'username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
    cli_error("Can not find user '$username'");
}

if ($options['password'] == '' ) {
    $prompt = "Enter new password";
    $password = cli_input($prompt);
} else {
    $password = $options['password'];
}

$errmsg = '';//prevent eclipse warning
if (!$options['ignore-password-policy'] ) {
    if (!check_password_policy($password, $errmsg)) {
        cli_error(html_to_text($errmsg, 0));
    }
}

$hashedpassword = hash_internal_user_password($password);

$DB->set_field('user', 'password', $hashedpassword, array('id'=>$user->id));

echo "Password changed\n";

exit(0); // 0 means success.
