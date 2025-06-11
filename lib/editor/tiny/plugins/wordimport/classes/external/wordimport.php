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
 * TinyMCE WordImport external API for filtering the wordimport.
 *
 * @package    tiny_wordimport
 * @copyright  2023 André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_wordimport\external;

use core\context;
use core\context\user;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use moodle_exception;
use tiny_wordimport\converter;

/**
 * TinyMCE WordImport external API for processing the word document to import.
 *
 * @package    tiny_wordimport
 * @copyright  2023 André Menrath <andre.menrath@uni-graz.at>
 *             2023 Huong Nguyen <huongnv13@gmail.com>
 *             Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wordimport extends external_api {
    /**
     * Describes the parameters for the wordimport.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'itemid' => new external_value(PARAM_INT, 'The file id of the draft upload', VALUE_REQUIRED),
            'contextid' => new external_value(PARAM_INT, 'The context ID', VALUE_REQUIRED),
            'filename' => new external_value(PARAM_TEXT, 'The filename of the imported word file', VALUE_REQUIRED),
        ]);
    }

    /**
     * External function for processing the wordimport.
     *
     * @param int    $itemid The id of the uploaded item.
     * @param int    $contextid Context ID.
     * @param string $filename The filename of the uploaded file.
     * @return array
     */
    public static function execute(int $itemid, int $contextid, string $filename): array {
        global $USER;
        [
            'itemid' => $itemid,
            'contextid' => $contextid,
            'filename' => $filename
        ] = self::validate_parameters(self::execute_parameters(), [
            'itemid' => $itemid,
            'contextid' => $contextid,
            'filename' => $filename,
        ]);

        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // The rest of this function is forked from atto_wordimport by Eoin Campbell.
        list($context, $course, $cm) = get_context_info_array($contextid);

        // Check that this user is logged in before proceeding.
        require_login($course, false, $cm);

        // Get the reference only of this users' uploaded file, to avoid rogue users' accessing other peoples files.
        $fs = get_file_storage();
        $usercontext = user::instance($USER->id);
        if (!$file = $fs->get_file($usercontext->id, 'user', 'draft', $itemid, '/', basename($filename))) {
            // File is not readable.
            throw new moodle_exception(get_string('errorreadingfile', 'error', basename($filename)));
        }

        // Save the uploaded file to a folder so we can process it using the PHP Zip library.
        if (!$tmpfilename = $file->copy_content_to_temp()) {
            // Cannot save file.
            throw new moodle_exception(get_string('errorcreatingfile', 'error', basename($filename)));
        } else {
            // Delete it from the draft file area to avoid possible name-clash messages if it is re-uploaded in the same edit.
            $file->delete();
        }

        // Convert the Word file into XHTML, store any images, and delete the temporary HTML file once we're finished.
        $htmltext = converter::docx_to_xhtml($tmpfilename, $usercontext->id, $itemid);

        if (!$htmltext) {
            // Error processing upload file.
            throw new moodle_exception(get_string('cannotuploadfile', 'error'));
        }

        return [
            'html' => $htmltext,
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'Processed content in raw html format'),
        ]);
    }
}
