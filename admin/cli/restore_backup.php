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
-c, --categoryid=INT    ID of the course category to restore to. This option is ignored when restoring an activity and courseid is set.
-C, --courseid=INT      ID of the course to restore to. This option is ignored when restoring a course and the categoryid is set.
-s, --showdebugging     Show developer level debugging information
-h, --help              Print out this help.

Example:
\$sudo -u www-data /usr/bin/php admin/cli/restore_backup.php --file=/path/to/backup/coursebackup.mbz --categoryid=1\n
\$sudo -u www-data /usr/bin/php admin/cli/restore_backup.php --file=/path/to/backup/activitybackup.mbz --courseid=1\n
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
}

if ($options['courseid']) {
    if (!$course = $DB->get_record('course', ['id' => $options['courseid']], 'id')) {
        throw new \moodle_exception('invalidcourseid');
    }
}

if (empty($category) && empty($course)) {
    throw new \moodle_exception('invalidoption');
}

$backupdir = restore_controller::get_tempdir_name(SITEID, $USER->id);
$path = make_backup_temp_directory($backupdir);

cli_heading(get_string('extractingbackupfileto', 'backup', $path));
$fp = get_file_packer('application/vnd.moodle.backup');
$fp->extract_to_pathname($options['file'], $path);

cli_heading(get_string('preprocessingbackupfile'));

try {
    // Create a temporary restore controller to determine the restore type.
    $tmprc = new restore_controller($backupdir, SITEID, backup::INTERACTIVE_NO,
        backup::MODE_GENERAL, $admin->id, backup::TARGET_EXISTING_ADDING);
    // Restore the backup into a new course if:
    // - It is a course backup and the category is set.
    // - It is an activity backup and the course is not set.
    $restoreasnewcourse = ($tmprc->get_type() === backup::TYPE_1COURSE && !empty($category)) ||
        ($tmprc->get_type() !== backup::TYPE_1COURSE && empty($course));
    // Make sure to clean up the temporary restore controller.
    $tmprc->destroy();

    if ($restoreasnewcourse) {
        list($fullname, $shortname) = restore_dbops::calculate_course_names(0, get_string('restoringcourse', 'backup'),
            get_string('restoringcourseshortname', 'backup'));
        $courseid = restore_dbops::create_new_course($fullname, $shortname, $category->id);
        $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $admin->id, backup::TARGET_NEW_COURSE);
    } else {
        $courseid = $course->id;
        $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $admin->id, backup::TARGET_EXISTING_ADDING);
    }
    $rc->execute_precheck();
    $rc->execute_plan();
    $rc->destroy();

    // Rename the course's full and short names with the backup file's original names if the backup file is an activity backup
    // that is restored to a new course.
    if ($restoreasnewcourse && $rc->get_type() !== backup::TYPE_1COURSE) {
        $course = get_course($courseid);
        $backupinfo = $rc->get_info();
        $tmpfullname = $backupinfo->original_course_fullname ?? get_string('restoretonewcourse', 'backup');
        $tmpshortname = $backupinfo->original_course_shortname ?? get_string('newcourse');
        list($fullname, $shortname) = restore_dbops::calculate_course_names(
            courseid: 0,
            fullname: $tmpfullname,
            shortname: $tmpshortname,
        );
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
