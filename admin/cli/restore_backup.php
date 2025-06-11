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
 * This script allows to restore a course from CLI.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', 1);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

list($options, $unrecognized) = cli_get_params([
    'file' => '',
    'categoryid' => '',
    'courseid' => '',
    'showdebugging' => false,
    'help' => false,
], [
    'f' => 'file',
    'c' => 'categoryid',
    'C' => 'courseid',
    's' => 'showdebugging',
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help'] || !($options['file']) || !($options['categoryid'] || $options['courseid'])) {
    $help = <<<EOL
Restore backup into provided category or course.
If courseid is set, course module/s will be added into the course.

Options:
-f, --file=STRING       Path to the backup file.
-c, --categoryid=INT    ID of the course category to restore to.
-C, --courseid=INT      ID of the course to restore to. This option is ignored if categoryid is set.
-s, --showdebugging     Show developer level debugging information
-h, --help              Print out this help.

Example:
\$sudo -u www-data /usr/bin/php admin/cli/restore_backup.php --file=/path/to/backup/file.mbz --categoryid=1\n
EOL;

    echo $help;
    exit(0);
}

if ($options['showdebugging']) {
    set_debugging(DEBUG_DEVELOPER, true);
}

if (!$admin = get_admin()) {
    throw new \moodle_exception('noadmins');
}

if (!file_exists($options['file'])) {
    throw new \moodle_exception('filenotfound');
}

if ($options['categoryid']) {
    if (!$category = $DB->get_record('course_categories', ['id' => $options['categoryid']], 'id')) {
        throw new \moodle_exception('invalidcategoryid');
    }
} else if ($options['courseid']) {
    if (!$course = $DB->get_record('course', ['id' => $options['courseid']], 'id')) {
        throw new \moodle_exception('invalidcourseid');
    }
} else {
    throw new \moodle_exception('invalidoption');
}

$backupdir = restore_controller::get_tempdir_name(SITEID, $USER->id);
$path = make_backup_temp_directory($backupdir);

cli_heading(get_string('extractingbackupfileto', 'backup', $path));
$fp = get_file_packer('application/vnd.moodle.backup');
$fp->extract_to_pathname($options['file'], $path);

cli_heading(get_string('preprocessingbackupfile'));
try {
    list($fullname, $shortname) = restore_dbops::calculate_course_names(0, get_string('restoringcourse', 'backup'),
        get_string('restoringcourseshortname', 'backup'));

    if (!empty($course)) {
        $courseid = $course->id;
        $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $admin->id, backup::TARGET_EXISTING_ADDING);
    } else {
        $courseid = restore_dbops::create_new_course($fullname, $shortname, $category->id);
        $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $admin->id, backup::TARGET_NEW_COURSE);
    }
    $rc->execute_precheck();
    $rc->execute_plan();
    $rc->destroy();

    // Rename course name if the backup is from course module and restore to category.
    if (empty($course)) {
        $course = get_course($courseid);
        list($fullname, $shortname) = restore_dbops::calculate_course_names(0, get_string('restoretonewcourse', 'backup'),
            get_string('newcourse'));
        $course->fullname = $fullname;
        $course->shortname = $shortname;
        $course->visible = 1;
        $DB->update_record('course', $course);
    }
} catch (Exception $e) {
    cli_heading(get_string('cleaningtempdata'));
    fulldelete($path);
    throw new \moodle_exception('generalexceptionmessage', 'error', '', $e->getMessage());
}

cli_heading(get_string('restoredcourseid', 'backup', $courseid));
exit(0);
