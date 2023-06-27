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
 * Contains the import_backup_helper class.
 *
 * @package tool_moodlenet
 * @copyright 2020 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet\local;

/**
 * The import_backup_helper class.
 *
 * The import_backup_helper objects provide a means to prepare a backup for for restoration of a course or activity backup file.
 *
 * @copyright 2020 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_backup_helper {

    /** @var remote_resource $remoteresource A file resource to be restored. */
    protected $remoteresource;

    /** @var user $user The user trying to restore a file. */
    protected $user;

    /** @var context $context The context we are trying to restore this file into. */
    protected $context;

    /** @var int $useruploadlimit The size limit that this user can upload in this context. */
    protected $useruploadlimit;

    /**
     * Constructor for the import backup helper.
     *
     * @param remote_resource $remoteresource A remote file resource
     * @param \stdClass       $user           The user importing a file.
     * @param \context        $context        Context to restore into.
     */
    public function __construct(remote_resource $remoteresource, \stdClass $user, \context $context) {
        $this->remoteresource = $remoteresource;
        $this->user = $user;
        $this->context = $context;

        $maxbytes = 0;
        if ($this->context->contextlevel == CONTEXT_COURSE) {
            $course = get_course($this->context->instanceid);
            $maxbytes = $course->maxbytes;
        }
        $this->useruploadlimit = get_user_max_upload_file_size($this->context, get_config('core', 'maxbytes'),
                $maxbytes, 0, $this->user);
    }

    /**
     * Return a stored user draft file for processing.
     *
     * @return \stored_file The imported file to ultimately be restored.
     */
    public function get_stored_file(): \stored_file {

        // Check if the user can upload a backup to this context.
        require_capability('moodle/restore:uploadfile', $this->context, $this->user->id);

        // Before starting a potentially lengthy download, try to ensure the file size does not exceed the upload size restrictions
        // for the user. This is a time saving measure.
        // This is a naive check, that serves only to catch files if they provide the content length header.
        // Because of potential content encoding (compression), the stored file will be checked again after download as well.
        $size = $this->remoteresource->get_download_size() ?? -1;
        if ($this->size_exceeds_upload_limit($size)) {
            throw new \moodle_exception('uploadlimitexceeded', 'tool_moodlenet', '', ['filesize' => $size,
                'uploadlimit' => $this->useruploadlimit]);
        }

        [$filepath, $filename] = $this->remoteresource->download_to_requestdir();
        \core\antivirus\manager::scan_file($filepath, $filename, true);

        // Check the final size of file against the user upload limits.
        $localsize = filesize(sprintf('%s/%s', $filepath, $filename));
        if ($this->size_exceeds_upload_limit($localsize)) {
            throw new \moodle_exception('uploadlimitexceeded', 'tool_moodlenet', '', ['filesize' => $localsize,
                'uploadlimit' => $this->useruploadlimit]);
        }

        return $this->create_user_draft_stored_file($filename, $filepath);
    }

    /**
     * Does the size exceed the upload limit for the current import, taking into account user and core settings.
     *
     * @param int $sizeinbytes
     * @return bool true if exceeded, false otherwise.
     */
    protected function size_exceeds_upload_limit(int $sizeinbytes): bool {
        $maxbytes = 0;
        if ($this->context->contextlevel == CONTEXT_COURSE) {
            $course = get_course($this->context->instanceid);
            $maxbytes = $course->maxbytes;
        }
        $maxbytes = get_user_max_upload_file_size($this->context, get_config('core', 'maxbytes'), $maxbytes, 0,
            $this->user);
        if ($maxbytes != USER_CAN_IGNORE_FILE_SIZE_LIMITS && $sizeinbytes > $maxbytes) {
            return true;
        }
        return false;
    }

    /**
     * Create a file in the user drafts ready for use by plugins implementing dndupload_handle().
     *
     * @param string $filename the name of the file on disk
     * @param string $path the path where the file is stored on disk
     * @return \stored_file
     */
    protected function create_user_draft_stored_file(string $filename, string $path): \stored_file {
        global $CFG;

        $record = new \stdClass();
        $record->filearea = 'draft';
        $record->component = 'user';
        $record->filepath = '/';
        $record->itemid   = file_get_unused_draft_itemid();
        $record->license  = $CFG->sitedefaultlicense;
        $record->author   = '';
        $record->filename = clean_param($filename, PARAM_FILE);
        $record->contextid = \context_user::instance($this->user->id)->id;
        $record->userid = $this->user->id;

        $fullpathwithname = sprintf('%s/%s', $path, $filename);

        $fs = get_file_storage();

        return  $fs->create_file_from_pathname($record, $fullpathwithname);
    }

    /**
     * Looks for a context that this user has permission to upload backup files to.
     * This gets a list of roles that the user has, checks for the restore:uploadfile capability and then sends back a context
     * that has this permission if available.
     *
     * This starts with the highest context level and moves down i.e. system -> category -> course.
     *
     * @param  int $userid The user ID that we are looking for a working context for.
     * @return \context A context that allows the upload of backup files.
     */
    public static function get_context_for_user(int $userid): ?\context {
        global $DB;

        if (is_siteadmin()) {
            return \context_system::instance();
        }

        $sql = "SELECT ctx.id, ctx.contextlevel, ctx.instanceid, ctx.path, ctx.depth, ctx.locked
                  FROM {context} ctx
                  JOIN {role_assignments} r ON ctx.id = r.contextid
                 WHERE r.userid = :userid AND ctx.contextlevel IN (:contextsystem, :contextcategory, :contextcourse)
              ORDER BY ctx.contextlevel ASC";

        $params = [
            'userid' => $userid,
            'contextsystem' => CONTEXT_SYSTEM,
            'contextcategory' => CONTEXT_COURSECAT,
            'contextcourse' => CONTEXT_COURSE
        ];
        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            \context_helper::preload_from_record($record);
            if ($record->contextlevel == CONTEXT_COURSECAT) {
                $context = \context_coursecat::instance($record->instanceid);
            } else if ($record->contextlevel == CONTEXT_COURSE) {
                $context = \context_course::instance($record->instanceid);
            } else {
                $context = \context_system::instance();
            }
            if (has_capability('moodle/restore:uploadfile', $context, $userid)) {
                return $context;
            }
        }
        return null;
    }
}
