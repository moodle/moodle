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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\filemanager;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_config;
use block_quickmail_cache;
use block_quickmail\persistents\message;
use block_quickmail\persistents\message_attachment;
use context_course;

class message_file_handler {

    public static $pluginname = 'block_quickmail';

    public $message;
    public $course;
    public $context;
    public $file_storage;
    public $uploaded_files;

    public function __construct(message $message) {
        $this->message = $message;
        $this->course = $message->get_course();
        $this->context = $this->get_context();
        $this->file_storage = get_file_storage();
        $this->uploaded_files = [];
    }

    /**
     * Executes posted file attachments for the given message
     *
     * @param  message  $message
     * @param  object   $formdata   mform post data
     * @param  string   $filearea    "attachments"
     * @return void
     */
    public static function handle_posted_attachments($message, $formdata, $filearea) {
        $filehandler = new self($message);

        // Store the filearea's files within moodle.
        $filehandler->store_posted_filearea($formdata, $filearea);

        // Update this message's list of file attachments.
        $filehandler->sync_attachments();
    }

    /**
     * Duplicates files for a given message
     *
     * @param  message  $original
     * @param  message  $new
     * @param  string   $filearea    "attachments"
     * @return void
     */
    public static function duplicate_files($original, $new, $filearea) {
        $originalhandler = new self($original);
        $course = $original->get_course();
        $context = context_course::instance($course->id);

        $files = $originalhandler->fetch_uploaded_file_data($filearea);

        // Iterate through each uploaded file.
        $fs = get_file_storage();
        foreach ($files as $z) {
            $file = $fs->get_file($z->contextid, $z->component, $z->filearea,
                                  $z->itemid, $z->filepath, $z->filename);
            if ($file) {
                $filerecord = array(
                    'contextid' => $context->id,
                    'component' => 'block_quickmail',
                    'filearea' => $filearea,
                    'itemid' => $new->get('id'),
                    'filepath' => '/',
                    'filename' => $z->filename,
                    'timecreated' => time(),
                    'timemodified' => time()
                );

                $fs->create_file_from_storedfile($filerecord, $file->get_id());
            }
        }
    }

    /**
     * Zips all of the file attachments for the given message and makes available for the given user,
     * Returns the path in which the temp files are stored
     *
     * @param  message  $message
     * @param  object  $user      moodle user
     * @param  string  $filename   file name to name the temp zip file
     * @return string  path to the generated file
     */
    public static function zip_attachments_for_user($message, $user, $filename = 'attachments.zip') {
        global $CFG;

        $path = $CFG->tempdir . '/' . self::$pluginname . '/' . $user->id;

        if ( ! file_exists($path)) {
            mkdir($path, $CFG->directorypermissions, true);
        }

        $zipfilename = $path . '/' . $filename;

        $course = $message->get_course();

        $context = context_course::instance($course->id);

        $fs = get_file_storage();
        $packer = get_file_packer();

        $files = $fs->get_area_files(
            $context->id,
            self::$pluginname,
            'attachments',
            $message->get('id'),
            true
        );

        $storedfiles = [];

        // Iterate through each of the file records.
        foreach ($files as $file) {
            // If the record is a directory, skip.
            if ($file->is_directory() && $file->get_filename() == '.') {
                continue;
            }

            // Add the file references to the stack.
            $storedfiles[$file->get_filepath() . $file->get_filename()] = $file;
        }

        // Zip the files.
        $packer->archive_to_pathname($storedfiles, $zipfilename);

        return $zipfilename;
    }

    /**
     * Stores and renames the given filearea's files from the given posted data
     *
     * @param  object  $formdata  mform post data
     * @param  string  $filearea  "attachments"
     * @return void
     */
    private function store_posted_filearea($formdata, $filearea) {
        if (empty($formdata->attachments)) {
            return;
        }

        // Move the files from "user draft" to this filearea.
        file_save_draft_area_files(
            $formdata->$filearea,
            $this->context->id,
            self::$pluginname,
            $filearea,
            $this->message->get('id'),
            block_quickmail_config::get_filemanager_options()
        );

        // Iterate through each uploaded file.
        foreach ($this->fetch_uploaded_file_data($filearea) as $file) {
            // Add its data to the stack.
            $this->add_to_uploaded_files($filearea, $file->filepath, $file->filename);
        }
    }

    /**
     * Replaces all existing message_attachment records for this message with the given uploaded file data
     *
     * @param  array  $uploadedfiles
     * @return void
     */
    private function sync_attachments($uploadedfiles = []) {
        // Clear all current attachment records.
        message_attachment::clear_all_for_message($this->message);

        // Get uploaded attachment files from the stack, if any.
        $uploadedfiles = $this->get_uploaded_files('attachments');

        $count = 0;

        // Iterate through each file.
        foreach ($uploadedfiles as $file) {
            // If any exceptions, proceed gracefully to the next.
            try {
                message_attachment::create_for_message($this->message, [
                    'path' => $file['path'],
                    'filename' => $file['filename'],
                ]);

                $count++;
            } catch (\Exception $e) {
                // Most likely invalid user, exception thrown due to validation error.
                // Log this?
                continue;
            }
        }

        // Cache the count for external use.
        block_quickmail_cache::store('qm_msg_attach_count')->put($this->message->get('id'), $count);
    }

    /**
     * Returns all of the uploaded file records of the given filearea for this message
     *
     * @param  string  $filearea  "attachments"
     * @return array
     */
    private function fetch_uploaded_file_data($filearea) {
        global $DB;
        $sql = 'SELECT * FROM {files} WHERE component = ? AND filearea = ? AND itemid = ? AND filename <> ?';
        $files = $DB->get_records_sql($sql, [
            self::$pluginname,
            $filearea,
            $this->message->get('id'),
            '.'
        ]);

        return $files;
    }

    /**
     * Adds the given path and filename to the given filearea's uploaded file array
     *
     * @param string  $filearea  "attachments"
     * @param string  $path      file path
     * @param string  $filename
     * @return  void
     */
    private function add_to_uploaded_files($filearea, $path, $filename) {
        $this->uploaded_files[$filearea][] = [
            'path' => $path,
            'filename' => $filename,
        ];
    }

    /**
     * Returns all of the set uploaded file data for the given filearea
     *
     * @param string  $filearea  "attachments"
     * @return array
     */
    private function get_uploaded_files($filearea) {
        return ! array_key_exists($filearea, $this->uploaded_files)
            ? []
            : $this->uploaded_files[$filearea];
    }

    /**
     * Returns this handler's course context
     *
     * @return object
     */
    private function get_context() {
        return context_course::instance($this->course->id);
    }

}
