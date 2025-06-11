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

namespace mod_assign;

use assign;
use core_php_time_limit;
use mod_assign\event\all_submissions_downloaded;
use core\session\manager as sessionmanager;
use core_files\archive_writer;
use stdClass;
use assign_plugin;
use stored_file;

/**
 * Class to download user submissions.
 *
 * @package    mod_assign
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class downloader {
    /** @var assign the module manager instance. */
    private $manager;

    /** @var stdClass the assign instance record. */
    private $instance;

    /** @var array|null the selected user ids, if any. */
    private $userids = null;

    /** @var int $groupmode the activity group mode. */
    private $groupmode = '';

    /** @var int $groupid the exported groupid. */
    private $groupid = 0;

    /** @var array $filesforzipping the files to zipo (path => file) */
    protected $filesforzipping;

    /** @var array $prefixes all loaded the student prefixes.
     *
     * A prefix will be converted into a file prefix or a folder name (depends on downloadasfolders).
     */
    private $prefixes;

    /** @var int $downloadasfolders the files to zipo (path => file) */
    private $downloadasfolders;

    /**
     * Class constructor.
     *
     * @param assign $manager the instance manager
     * @param int[]|null $userids the user ids to download.
     */
    public function __construct(assign $manager, ?array $userids = null) {
        $this->manager = $manager;
        $this->userids = $userids;
        $this->instance = $manager->get_instance();

        $this->downloadasfolders = get_user_preferences('assign_downloadasfolders', 1);

        $cm = $manager->get_course_module();
        $this->groupmode = groups_get_activity_groupmode($cm);
        if ($this->groupmode) {
            $this->groupid = groups_get_activity_group($cm, true);
        }
    }

    /**
     * Load the filelist.
     *
     * @return bool true if there are some files to zip.
     */
    public function load_filelist(): bool {
        $manager = $this->manager;
        $groupid = $this->groupid;

        // Increase the server timeout to handle the creation and sending of large zip files.
        core_php_time_limit::raise();

        $manager->require_view_grades();

        // Load all users with submit.
        $students = get_enrolled_users(
            $manager->get_context(),
            "mod/assign:submit",
            0,
            'u.*',
            null,
            0,
            0,
            $manager->show_only_active_users()
        );

        // Build a list of files to zip.
        $this->filesforzipping = [];

        // Get all the files for each student.
        foreach ($students as $student) {
            // Download all assigments submission or only selected users.
            if ($this->userids && !in_array($student->id, $this->userids)) {
                continue;
            }
            if (!groups_is_member($groupid, $student->id) && $this->groupmode && $groupid) {
                continue;
            }
            $this->load_student_filelist($student);
        }
        return !empty($this->filesforzipping);
    }

    /**
     * Load an individual student filelist.
     *
     * @param stdClass $student the user record
     */
    private function load_student_filelist(stdClass $student) {
        $submission = $this->get_student_submission($student);
        if (!$submission) {
            return;
        }
        $prefix = $this->get_student_prefix($student);
        if (isset($this->prefixes[$prefix])) {
            // We already send that file (in group mode).
            return;
        }
        $this->prefixes[$prefix] = $student->id;

        foreach ($this->manager->get_submission_plugins() as $plugin) {
            if (!$plugin->is_enabled() || !$plugin->is_visible()) {
                continue;
            }
            $this->load_submissionplugin_filelist($student, $plugin, $submission, $prefix);
        }
    }

    /**
     * Return the student submission if any.
     *
     * @param stdClass $student the user record
     * @return stdClass|null the user submission or null if none
     */
    private function get_student_submission(stdClass $student): ?stdClass {
        if ($this->instance->teamsubmission) {
            $submission = $this->manager->get_group_submission($student->id, 0, false);
        } else {
            $submission = $this->manager->get_user_submission($student->id, false);
        }
        return $submission ?: null;
    }

    /**
     * Return the file prefix used to generate the each submission folder or file.
     *
     * @param stdClass $student the user record
     * @return string the submission prefix
     */
    private function get_student_prefix(stdClass $student): string {
        $manager = $this->manager;

        // Team submissions are by group, not by student.
        if ($this->instance->teamsubmission) {
            $submissiongroup = $manager->get_submission_group($student->id);
            if ($submissiongroup) {
                $groupname = format_string($submissiongroup->name, true, ['context' => $manager->get_context()]);
                $groupinfo = '_' . $submissiongroup->id;
            } else {
                $groupname = get_string('defaultteam', 'mod_assign');
                $groupinfo = '';
            }
            $prefix = str_replace('_', ' ', $groupname);
            return clean_filename($prefix . $groupinfo);
        }
        // Individual submissions are by user.
        if ($manager->is_blind_marking()) {
            $fullname = get_string('participant', 'mod_assign');
        } else {
            $fullname = fullname($student, has_capability('moodle/site:viewfullnames', $manager->get_context()));
        }
        $prefix = str_replace('_', ' ', $fullname);
        $prefix = clean_filename($prefix . '_' . $manager->get_uniqueid_for_user($student->id));
        return $prefix;
    }

    /**
     * Load a submission plugin filelist for a specific user.
     *
     * @param stdClass $student the user record
     * @param assign_plugin $plugin the submission plugin instance
     * @param stdClass $submission the submission object
     * @param string $prefix the files prefix
     */
    private function load_submissionplugin_filelist(
        stdClass $student,
        assign_plugin $plugin,
        stdClass $submission,
        string $prefix
    ) {
        $subtype = $plugin->get_subtype();
        $type = $plugin->get_type();

        if ($this->downloadasfolders) {
            // Create a folder for each user for each assignment plugin.
            // This is the default behavior for version of Moodle >= 3.1.
            $submission->exportfullpath = true;
            $pluginfiles = $plugin->get_files($submission, $student);
            foreach ($pluginfiles as $zipfilepath => $file) {
                $zipfilename = basename($zipfilepath);
                $prefixedfilename = clean_filename($prefix . '_' . $subtype . '_' . $type);
                if ($type == 'file') {
                    $pathfilename = $prefixedfilename . $file->get_filepath() . $zipfilename;
                } else {
                    $pathfilename = $prefixedfilename . '/' . $zipfilename;
                }
                $pathfilename = clean_param($pathfilename, PARAM_PATH);
                $this->filesforzipping[$pathfilename] = $file;
            }
        } else {
            // Create a single folder for all users of all assignment plugins.
            // This was the default behavior for version of Moodle < 3.1.
            $submission->exportfullpath = false;
            $pluginfiles = $plugin->get_files($submission, $student);
            foreach ($pluginfiles as $zipfilename => $file) {
                $prefixedfilename = clean_filename($prefix . '_' . $subtype . '_' . $type . '_' . $zipfilename);
                $this->filesforzipping[$prefixedfilename] = $file;
            }
        }
    }

    /**
     * Download the exported zip.
     *
     * This method will terminate the current script when the file is send.
     */
    public function download_zip() {
        $filename = $this->get_zip_filename();
        all_submissions_downloaded::create_from_assign($this->manager)->trigger();
        sessionmanager::write_close();
        $zipwriter = archive_writer::get_stream_writer($filename, archive_writer::ZIP_WRITER);

        // Stream the files into the zip.
        foreach ($this->filesforzipping as $pathinzip => $file) {
            if ($file instanceof stored_file) {
                // Most of cases are stored_file.
                $zipwriter->add_file_from_stored_file($pathinzip, $file);
            } else if (is_array($file)) {
                // Save $file as contents, from onlinetext subplugin.
                $content = reset($file);
                $zipwriter->add_file_from_string($pathinzip, $content);
            }
        }
        // Finish the archive.
        $zipwriter->finish();
        exit();
    }

    /**
     * Generate the zip filename.
     *
     * @return string the zip filename
     */
    private function get_zip_filename(): string {
        $manager = $this->manager;
        $filenameparts = [
            $manager->get_course()->shortname,
            $this->instance->name,
        ];
        if (!empty($this->groupid)) {
            $filenameparts[] = format_string(groups_get_group_name($this->groupid), true, ['context' => $manager->get_context()]);
        }
        $filenameparts[] = $manager->get_course_module()->id;

        return clean_filename(implode('-', $filenameparts). '.zip');
    }
}
