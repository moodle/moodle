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

namespace aiplacement_editor\external;

use aiplacement_editor\utils;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * External API to call an action for this placement.
 *
 * @package    aiplacement_editor
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generate_image extends external_api {
    /**
     * Generate image parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(
                PARAM_INT,
                'The context ID',
                VALUE_REQUIRED,
            ),
            'prompttext' => new external_value(
                PARAM_RAW,
                'The prompt text for the AI service',
                VALUE_REQUIRED,
            ),
            'aspectratio' => new external_value(
                PARAM_ALPHA,
                'The aspect ratio of the image',
                VALUE_REQUIRED,
            ),
            'quality' => new external_value(
                PARAM_ALPHA,
                'The quality of the image',
                VALUE_REQUIRED,
            ),
            'numimages' => new external_value(
                PARAM_INT,
                'The number of images to generate',
                VALUE_DEFAULT,
                1,
            ),
            'style' => new external_value(
                PARAM_ALPHA,
                'The style of the image',
                VALUE_DEFAULT,
                'natural',
            ),
        ]);
    }

    /**
     * Generate image from the AI placement.
     *
     * @param int $contextid The context ID.
     * @param string $prompttext The data encoded as a json array.
     * @param string $aspectratio The aspect ratio of the image.
     * @param string $quality The quality of the image.
     * @param string $numimages The number of images to generate.
     * @param string $style The style of the image.
     * @return array The generated content.
     * @since  Moodle 4.5
     */
    public static function execute(
        int $contextid,
        string $prompttext,
        string $aspectratio,
        string $quality,
        string $numimages,
        string $style = '',
    ): array {
        global $USER;
        // Parameter validation.
        [
            'contextid' => $contextid,
            'prompttext' => $prompttext,
            'aspectratio' => $aspectratio,
            'quality' => $quality,
            'numimages' => $numimages,
            'style' => $style,
        ] = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'prompttext' => $prompttext,
            'aspectratio' => $aspectratio,
            'quality' => $quality,
            'numimages' => $numimages,
            'style' => $style,
        ]);
        // Context validation and permission check.
        // Get the context from the passed in ID.
        $context = \context::instance_by_id($contextid);

        // Check the user has permission to use the AI service.
        self::validate_context($context);
        if (!utils::is_html_editor_placement_action_available($context, 'generate_text',
                \core_ai\aiactions\generate_image::class)) {
            throw new \moodle_exception('noeditor', 'aiplacement_editor');
        }

        // Prepare the action.
        $action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: $USER->id,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style,
        );

        // Send the action to the AI manager.
        $manager = \core\di::get(\core_ai\manager::class);
        $response = $manager->process_action($action);

        // If we have a successful response, generate the URL for the draft file.
        if ($response->get_success()) {
            $draftfile = $response->get_response_data()['draftfile'];
            $drafturl = \moodle_url::make_draftfile_url(
                $draftfile->get_itemid(),
                $draftfile->get_filepath(),
                $draftfile->get_filename(),
                false,
            )->out(false);

        } else {
            $drafturl = '';
        }

        // Return the response.
        return [
            'success' => $response->get_success(),
            'revisedprompt' => $response->get_response_data()['revisedprompt'] ?? '',
            'drafturl' => $drafturl,
            'errorcode' => $response->get_errorcode(),
            'error' => $response->get_error(),
            'errormessage' => $response->get_errormessage(),
        ];
    }

    /**
     * Generate content return value.
     *
     * @return external_function_parameters
     * @since  Moodle 4.5
     */
    public static function execute_returns(): external_function_parameters {
        return new external_function_parameters([
            'success' => new external_value(
                PARAM_BOOL,
                'Was the request successful',
                VALUE_REQUIRED,
            ),
            'revisedprompt' => new external_value(
                PARAM_TEXT,
                'Revised prompt generated by the AI',
                VALUE_DEFAULT,
                '',
            ),
            'drafturl' => new external_value(
                PARAM_URL,
                'Draft file URL for the image',
                VALUE_DEFAULT,
                '',
            ),
            'errorcode' => new external_value(
                PARAM_INT,
                'Error code if any',
                VALUE_DEFAULT,
                0,
            ),
            'error' => new external_value(
                PARAM_TEXT,
                'Error name if any',
                VALUE_DEFAULT,
                '',
            ),
            'errormessage' => new external_value(
                PARAM_TEXT,
                'Error message if any',
                VALUE_DEFAULT,
                '',
            ),
        ]);
    }
}
