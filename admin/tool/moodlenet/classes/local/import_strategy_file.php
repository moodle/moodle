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
 * Contains the import_strategy_file class.
 *
 * @package tool_moodlenet
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_moodlenet\local;

use core\antivirus\manager as avmanager;

/**
 * The import_strategy_file class.
 *
 * The import_strategy_file objects contains the setup steps needed to prepare a resource for import as a file into Moodle. This
 * ensures the remote_resource is first downloaded and put in a draft file area, ready for use as a file by the handling module.
 *
 * @copyright 2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_strategy_file implements import_strategy {

    /**
     * Get an array of import_handler_info objects representing modules supporting import of this file type.
     *
     * @param array $registrydata the fully populated registry.
     * @param remote_resource $resource the remote resource.
     * @return import_handler_info[] the array of import_handler_info objects.
     */
    public function get_handlers(array $registrydata, remote_resource $resource): array {
        $handlers = [];
        foreach ($registrydata['files'] as $index => $items) {
            foreach ($items as $item) {
                if ($index === $resource->get_extension() || $index === '*') {
                    $handlers[] = new import_handler_info($item['module'], $item['message'], $this);
                }
            }
        }
        return $handlers;
    }

    /**
     * Import the remote resource according to the rules of this strategy.
     *
     * @param remote_resource $resource the resource to import.
     * @param \stdClass $user the user to import on behalf of.
     * @param \stdClass $course the course into which the remote_resource is being imported.
     * @param int $section the section into which the remote_resource is being imported.
     * @return \stdClass the module data.
     * @throws \moodle_exception if the file size means the upload limit is exceeded for the user.
     */
    public function import(remote_resource $resource, \stdClass $user, \stdClass $course, int $section): \stdClass {
        // Before starting a potentially lengthy download, try to ensure the file size does not exceed the upload size restrictions
        // for the user. This is a time saving measure.
        // This is a naive check, that serves only to catch files if they provide the content length header.
        // Because of potential content encoding (compression), the stored file will be checked again after download as well.
        $size = $resource->get_download_size() ?? -1;
        $useruploadlimit = $this->get_user_upload_limit($user, $course);
        if ($this->size_exceeds_upload_limit($size, $useruploadlimit)) {
            throw new \moodle_exception('uploadlimitexceeded', 'tool_moodlenet', '', ['filesize' => $size,
                'uploadlimit' => $useruploadlimit]);
        }

        // Download the file into a request directory and scan it.
        [$filepath, $filename] = $resource->download_to_requestdir();
        avmanager::scan_file($filepath, $filename, true);

        // Check the final size of file against the user upload limits.
        $localsize = filesize(sprintf('%s/%s', $filepath, $filename));
        if ($this->size_exceeds_upload_limit($localsize, $useruploadlimit)) {
            throw new \moodle_exception('uploadlimitexceeded', 'tool_moodlenet', '', ['filesize' => $localsize,
                'uploadlimit' => $useruploadlimit]);
        }

        // Store in the user draft file area.
        $storedfile = $this->create_user_draft_stored_file($user, $filename, $filepath);

        // Prepare the data to be sent to the modules dndupload_handle hook.
        return $this->prepare_module_data($course, $resource, $storedfile->get_itemid());
    }


    /**
     * Creates the data to pass to the dndupload_handle() hooks.
     *
     * @param \stdClass $course the course record.
     * @param remote_resource $resource the resource being imported as a file.
     * @param int $draftitemid the itemid of the draft file.
     * @return \stdClass the data object.
     */
    protected function prepare_module_data(\stdClass $course, remote_resource $resource, int $draftitemid): \stdClass {
        $data = new \stdClass();
        $data->type = 'Files';
        $data->course = $course;
        $data->draftitemid = $draftitemid;
        $data->displayname = $resource->get_name();
        return $data;
    }

    /**
     * Get the max file size limit for the user in the course.
     *
     * @param \stdClass $user the user to check.
     * @param \stdClass $course the course to check in.
     * @return int the file size limit, in bytes.
     */
    protected function get_user_upload_limit(\stdClass $user, \stdClass $course): int {
        return get_user_max_upload_file_size(\context_course::instance($course->id), get_config('core', 'maxbytes'),
            $course->maxbytes, 0, $user);
    }

    /**
     * Does the size exceed the upload limit for the current import, taking into account user and core settings.
     *
     * @param int $sizeinbytes the size, in bytes.
     * @param int $useruploadlimit the upload limit, in bytes.
     * @return bool true if exceeded, false otherwise.
     * @throws \dml_exception
     */
    protected function size_exceeds_upload_limit(int $sizeinbytes, int $useruploadlimit): bool {
        if ($useruploadlimit != USER_CAN_IGNORE_FILE_SIZE_LIMITS && $sizeinbytes > $useruploadlimit) {
            return true;
        }
        return false;
    }

    /**
     * Create a file in the user drafts ready for use by plugins implementing dndupload_handle().
     *
     * @param \stdClass $user the user object.
     * @param string $filename the name of the file on disk
     * @param string $path the path where the file is stored on disk
     * @return \stored_file
     */
    protected function create_user_draft_stored_file(\stdClass $user, string $filename, string $path): \stored_file {
        global $CFG;

        $record = new \stdClass();
        $record->filearea = 'draft';
        $record->component = 'user';
        $record->filepath = '/';
        $record->itemid   = file_get_unused_draft_itemid();
        $record->license  = $CFG->sitedefaultlicense;
        $record->author   = '';
        $record->filename = clean_param($filename, PARAM_FILE);
        $record->contextid = \context_user::instance($user->id)->id;
        $record->userid = $user->id;

        $fullpathwithname = sprintf('%s/%s', $path, $filename);

        $fs = get_file_storage();

        return  $fs->create_file_from_pathname($record, $fullpathwithname);
    }
}
