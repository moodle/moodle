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
 * Replace a file.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\webservice;

use tool_ally\local_file;

/**
 * Replace a file.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class replace_file extends loggable_external_api {
    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {
        return new \external_function_parameters([
            'id' => new \external_value(PARAM_ALPHANUM, 'File path name SHA1 hash'),
            'userid' => new \external_value(PARAM_INT, 'User id with access to file'),
            'draftitemid' => new \external_value(PARAM_INT, 'itemid of new file uploaded'),
        ]);
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return new \external_single_structure([
            'success'    => new \external_value(PARAM_BOOL, 'File replaced succesfully?'),
            'newid'      => new \external_value(PARAM_ALPHANUM, 'New file path name hash'),
        ]);
    }

    /**
     * @param $id
     * @param $userid
     * @param $draftitemid
     * @return array
     * @throws \WebserviceInvalidParameterException
     * @throws \WebserviceParameterException
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws \stored_file_creation_exception
     */
    public static function execute_service($id, $userid, $draftitemid) {
        global $DB, $USER;

        $params = [
            'id' => $id,
            'userid' => $userid,
            'draftitemid' => $draftitemid,
        ];
        $params = self::validate_parameters(self::service_parameters(), $params);

        $fs = get_file_storage();
        $oldfile = $fs->get_file_by_hash($params['id']);
        if (!$oldfile instanceof \stored_file) {
            throw new \moodle_exception('filenotfound', 'error');
        }
        $oldfilename = $oldfile->get_filename();

        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        $context = \context::instance_by_id($oldfile->get_contextid());

        self::validate_context($context);
        require_capability('moodle/course:view', $context);
        require_capability('moodle/course:viewhiddencourses', $context);
        require_capability('moodle/course:managefiles', $context);

        $replaced = false;
        $capabilities = array(
            'moodle/course:update',
            'moodle/course:managefiles',
        );

        if (!has_all_capabilities($capabilities, $context, $user)) {
            throw new \moodle_exception('usercapabilitymissing', 'tool_ally');
        }

        $filerecord = new \stdClass();
        $filerecord->contextid = $oldfile->get_contextid();
        $filerecord->component = $oldfile->get_component();
        $filerecord->filearea  = $oldfile->get_filearea();
        $filerecord->filepath  = $oldfile->get_filepath();
        $filerecord->itemid    = $oldfile->get_itemid();
        $filerecord->userid    = $oldfile->get_userid();
        $filerecord->license   = $oldfile->get_license();
        $filerecord->author    = $oldfile->get_author();
        $filerecord->source    = $oldfile->get_source();

        $usercontext = \context_user::instance($USER->id);

        $uploadedfiles = $fs->get_area_files(
            $usercontext->id,
            'user',
            'draft',
            $draftitemid,
            '',
            false
        );
        $newfile = reset($uploadedfiles);

        $filerecord->filename = $newfile->get_filename();

        $oldfile->delete();

        $filename = $fs->get_unused_filename($filerecord->contextid, $filerecord->component, $filerecord->filearea,
                $filerecord->itemid, $filerecord->filepath, $filerecord->filename);
        $filerecord->filename = $filename;

        $file = $fs->create_file_from_storedfile($filerecord, $newfile);
        $replaced = true;

        if ($oldfilename != $file->get_filename()) {
            local_file::replace_html_links($oldfilename, $file);

            // We have to do this so that it will regen module text with the new file path.
            rebuild_course_cache($context->get_course_context()->instanceid, true);
        }

        return [
            'success' => $replaced,
            'newid' => $file->get_pathnamehash(),
        ];
    }
}
