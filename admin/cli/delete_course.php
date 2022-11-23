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
 * CLI script to delete a course.
 *
 * @package    core
 * @subpackage cli
 * @author     Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @copyright  2022 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/cronlib.php');

list($options, $unrecognized) = cli_get_params(
    [
        'courseid' => false,
        'help' => false,
        'showsql' => false,
        'showdebugging' => false,
        'disablerecyclebin' => false,
        'non-interactive' => false,
    ], [
        'c' => 'courseid',
        'h' => 'help',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] || empty($options['courseid'])) {
    $help = <<<EOT
CLI script to delete a course.

Options:
 -h, --help                Print out this help
     --showsql             Show sql queries before they are executed
     --showdebugging       Show developer level debugging information
     --disablerecyclebin   Skip backing up the course
     --non-interactive     No interactive questions or confirmations
 -c, --courseid            Course id to be deleted

Example:
\$sudo -u www-data /usr/bin/php admin/cli/delete_course.php --courseid=123456
\$sudo -u www-data /usr/bin/php admin/cli/delete_course.php --courseid=123456 --showdebugging
\$sudo -u www-data /usr/bin/php admin/cli/delete_course.php --courseid=123456 --disablerecyclebin

EOT;

    echo $help;
    die;
}

$interactive = empty($options['non-interactive']);

if ($options['showdebugging']) {
    mtrace('Enabling debugging...');
    set_debugging(DEBUG_DEVELOPER, true);
}

if ($options['showsql']) {
    mtrace('Enabling SQL debugging...');
    $DB->set_debug(true);
}

if (CLI_MAINTENANCE) {
    cli_error('CLI maintenance mode active, CLI execution suspended');
}

if (moodle_needs_upgrading()) {
    cli_error('Moodle upgrade pending, CLI execution suspended');
}

$course = $DB->get_record('course', array('id' => $options['courseid']));
if (empty($course)) {
    cli_error('Course not found');
}

mtrace('Deleting course id ' . $course->id);
mtrace('Course name: ' . $course->fullname);
mtrace('Short name: ' . $course->shortname);

if ($interactive) {
    mtrace('');
    $input = cli_input('Are you sure you wish to delete this course? (y/N)', 'N', ['y', 'Y', 'n', 'N']);
    if (strtolower($input) != 'y') {
        exit(0);
    }
}

if ($options['disablerecyclebin']) {
    mtrace('Disabling recycle bin...');
    $overrideconfig = ['tool_recyclebin' => ['coursebinenable' => false, 'categorybinenable' => false]];
    $CFG->forced_plugin_settings = array_merge($CFG->forced_plugin_settings, $overrideconfig);
}

core_php_time_limit::raise();
delete_course($course);

mtrace('Updating course count in categories...');
fix_course_sortorder();

mtrace('Done!');
