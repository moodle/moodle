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
 * External format_softcourse API
 *
 * @package format_softcourse
 * @copyright 2019 Pimenko <contact@pimenko.com>
 * @author 2019 Pimenko <contact@pimenko.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * External format_softcourse API class
 *
 *
 * @package format_softcourse
 * @copyright 2019 Pimenko <contact@pimenko.com>
 * @author 2019 Pimenko <contact@pimenko.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_softcourse_external extends external_api {

    /**
     * Updates the section image for a given course section.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the section.
     * @param string $imagedata The base64-encoded image data.
     * @param string $filename The original filename of the image.
     *
     * @return array An array containing the update status:
     *               - 'status': Boolean indicating if the update was successful.
     */
    public static function update_section_image($courseid, $sectionid, $imagedata, $filename): array {
        global $CFG;
        $params = self::validate_parameters(
            self::update_section_image_parameters(),
            [
                'courseid' => $courseid,
                'sectionid' => $sectionid,
                'imagedata' => $imagedata,
                'filename' => $filename,
            ],
        );
        $success = false;

        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability(
            'moodle/course:update',
            $context,
        );

        // Verify if context exist.
        if ($context) {
            $course = get_course($params['courseid']);
            $modinfo = get_fast_modinfo($course);
            $coursesection = $modinfo->get_section_info(
                $params['sectionid'],
                MUST_EXIST,
            );

            // Verify if the course section exist.
            if ($coursesection) {
                $fs = get_file_storage();
                $ext = strtolower(
                    pathinfo(
                        $params['filename'],
                        PATHINFO_EXTENSION,
                    ),
                );
                // Rename to sectionimage_sectionid.
                $filename = 'sectionimage_' . $params['courseid'] . '-' . $params['sectionid'] . '.' . $ext;
                // Check size.
                $binary = base64_decode($params['imagedata']);
                if (strlen($binary) > get_max_upload_file_size($CFG->maxbytes)) {
                    throw new \moodle_exception(
                        'error:maxsizeerror',
                        'format_softcourse',
                    );
                }
                $fileinfo = [
                    'contextid' => $context->id,
                    'component' => 'format_softcourse',
                    'filearea' => 'sectionimage',
                    'itemid' => $params['sectionid'],
                    'filepath' => '/',
                    'filename' => $filename,
                ];
                // 1st we delete existing bg.
                $fs->delete_area_files(
                    $fileinfo['contextid'],
                    $fileinfo['component'],
                    $fileinfo['filearea'],
                    $params['sectionid'],
                );
                // Create new one.
                $storedfile = $fs->create_file_from_string(
                    $fileinfo,
                    $binary,
                );
                if ($storedfile) {
                    $success = true;
                }
            }
        }

        $result = [];
        $result['status'] = $success;
        return $result;
    }

    /**
     * Update the parameters required for updating the image of a section
     *
     * @return array Array of parameters including course id, section id, image data, and file name
     */
    public static function update_section_image_parameters() {
        $parameters = [
            'courseid' => new \external_value(
                PARAM_INT,
                'Course id',
                VALUE_REQUIRED,
            ),
            'sectionid' => new \external_value(
                PARAM_INT,
                'Section id',
                VALUE_REQUIRED,
            ),
            'imagedata' => new \external_value(
                PARAM_TEXT,
                'Image data',
                VALUE_REQUIRED,
            ),
            'filename' => new \external_value(
                PARAM_TEXT,
                'File name of the image',
                VALUE_REQUIRED,
            ),
        ];
        return new \external_function_parameters($parameters);
    }

    /**
     * Update the section image and return the status result.
     *
     * @return array Array of warnings and status result.
     */
    public static function update_section_image_returns() {
        $keys = [
            'status' => new \external_value(
                PARAM_BOOL,
                'The section image was successfully changed',
                VALUE_REQUIRED,
            ),
        ];
        return new \external_single_structure(
            $keys,
            'sectionimage',
        );
    }

    /**
     * Deletes the section image for a given course section.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the section.
     *
     * @return array An array containing the deletion status:
     *               - 'status': Boolean indicating if the deletion was successful.
     */
    public static function delete_section_image($courseid, $sectionid): array {
        $params = self::validate_parameters(
            self::delete_section_image_parameters(),
            [
                'courseid' => $courseid,
                'sectionid' => $sectionid,
            ],
        );
        $success = false;

        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability(
            'moodle/course:update',
            $context,
        );
        require_capability(
            'moodle/course:update',
            $context,
        );

        // Verify if context exist.
        if ($context) {
            $course = get_course($params['courseid']);
            $modinfo = get_fast_modinfo($course);
            $coursesection = $modinfo->get_section_info(
                $params['sectionid'],
                MUST_EXIST,
            );

            // Verify if the course section exist.
            if ($coursesection) {
                $fs = get_file_storage();
                // Delete the file.
                $success = $fs->delete_area_files(
                    $context->id,
                    'format_softcourse',
                    'sectionimage',
                    $params['sectionid'],
                );
            }
        }

        $result = [];
        $result['status'] = $success;
        return $result;
    }

    /**
     * Retrieves the parameters required for deleting a section image.
     *
     * @return array An array of parameters for the delete_section_image method:
     *               - 'courseid': Course ID (Type: int, Description: Course id, Required: true)
     *               - 'sectionid': Section ID (Type: int, Description: Section id, Required: true)
     */
    public static function delete_section_image_parameters() {
        $parameters = [
            'courseid' => new \external_value(
                PARAM_INT,
                'Course id',
                VALUE_REQUIRED,
            ),
            'sectionid' => new \external_value(
                PARAM_INT,
                'Section id',
                VALUE_REQUIRED,
            ),
        ];
        return new \external_function_parameters($parameters);
    }

    /**
     * Deletes the section image for a given course section.
     *
     * @return array An array containing the deletion status:
     *               - 'status': Boolean indicating if the deletion was successful.
     */
    public static function delete_section_image_returns() {
        $keys = [
            'status' => new \external_value(
                PARAM_BOOL,
                'The section image was successfully deleted',
                VALUE_REQUIRED,
            ),
        ];
        return new \external_single_structure(
            $keys,
            'sectionimagedelete',
        );
    }
}
