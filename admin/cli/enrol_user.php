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
 * CLI script to enrol a user in a course.
 *
 * @package    core
 * @subpackage cli
 * @author     Ernesto Serrano <info@ernesto.es>
 * @copyright  2023 Ernesto Serrano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params(
    [
        'help'        => false,
        'user'        => false,
        'courseid'    => false,
        'role'        => 'student',
    ],
    [
        'h' => 'help',
        'u' => 'user',
        'c' => 'courseid',
        'r' => 'role',
    ]
);

if ($options['help'] || empty($options['user']) || empty($options['courseid'])) {
    $help = <<<EOT
CLI script to enroll a user in a course.

Options:
-h, --help            Print out this help
-u, --user            Username of the user
-c, --courseid        ID of the course
-r, --role            Role of the user in the course (default is student)

Example:
\$sudo -u www-data /usr/bin/php admin/enrol.php --user=admin --courseid=2";
\$sudo -u www-data /usr/bin/php admin/enrol.php --user=moodleuser --courseid=2 --role=student";
\$sudo -u www-data /usr/bin/php admin/enrol.php --user=moodleuser --courseid=2 --role=teacher";

EOT;

    echo $help;
    die;
}

if (CLI_MAINTENANCE) {
    cli_error('CLI maintenance mode active, CLI execution suspended');
}

// Check for missing values and print error messages.
if ($options['username'] === false) {
    cli_error('Error: Missing username. Use --username to specify the username.');
}

if ($options['courseid'] === false) {
    cli_error('Error: Missing course ID. Use --courseid to specify the course ID.');
}

$username   = $options['user'];
$courseid   = $options['courseid'];
$rolename   = $options['role'];

$user = $DB->get_record('user', ['username' => $username], '*', MUST_EXIST);
if (empty($user)) {
    cli_error('User not found');
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
if (empty($course)) {
    cli_error('Course not found');
}

$role = $DB->get_record('role', ['shortname' => $rolename], '*', MUST_EXIST);
if (empty($role)) {
    cli_error('Role not found');
}

$context = context_course::instance($course->id);

mtrace('Enroling user...');
role_assign($role->id, $user->id, $context->id);

echo "User '{$username}' has been enrolled in course '{$course->fullname}' as a '{$rolename}'.\n";

mtrace('Done!');
