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

namespace core_h5p;

use core_external\external_api;
use core_external\external_files;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * This is the external API for this component.
 *
 * @package    core_h5p
 * @copyright  2019 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * get_trusted_h5p_file parameters.
     *
     * @since  Moodle 3.8
     * @return external_function_parameters
     */
    public static function get_trusted_h5p_file_parameters() {
        return new external_function_parameters(
            [
                'url' => new external_value(PARAM_URL, 'H5P file url.', VALUE_REQUIRED),
                'frame' => new external_value(PARAM_INT, 'The frame allow to show the bar options below the content', VALUE_DEFAULT, 0),
                'export' => new external_value(PARAM_INT, 'The export allow to download the package', VALUE_DEFAULT, 0),
                'embed' => new external_value(PARAM_INT, 'The embed allow to copy the code to your site', VALUE_DEFAULT, 0),
                'copyright' => new external_value(PARAM_INT, 'The copyright option', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Return the H5P file trusted.
     *
     * The Mobile App needs to work with an H5P package which can trust.
     * And this H5P package is only trusted by the Mobile App once it's been processed
     * by the core checking the right caps, validating the H5P package
     * and doing any clean-up process involved.
     *
     * @since  Moodle 3.8
     * @param  string $url H5P file url
     * @param  int $frame The frame allow to show the bar options below the content
     * @param  int $export The export allow to download the package
     * @param  int $embed The embed allow to copy the code to your site
     * @param  int $copyright The copyright option
     * @return array
     * @throws \moodle_exception
     */
    public static function get_trusted_h5p_file(string $url, int $frame, int $export, int $embed, int $copyright) {

        $result = [];
        $warnings = [];
        $params = external_api::validate_parameters(self::get_trusted_h5p_file_parameters(), [
            'url' => $url,
            'frame' => $frame,
            'export' => $export,
            'embed' => $embed,
            'copyright' => $copyright
        ]);
        $url = $params['url'];
        $config = new \stdClass();
        $config->frame = $params['frame'];
        $config->export = $params['export'];
        $config->embed = $params['embed'];
        $config->copyright = $params['copyright'];
        try {
            $h5pplayer = new player($url, $config);
            $messages = $h5pplayer->get_messages();
        } catch (\moodle_exception $e) {
            $messages = (object) [
                'code' => $e->getCode(),
            ];
            // To mantain the coherence between web coding error and Mobile coding errors.
            // We need to return the same message error to Mobile.
            // The 'out_al_local_url called on a non-local URL' error is provided by the \moodle_exception.
            // We have to translate to h5pinvalidurl which is the same coding error showed in web.
            if ($e->errorcode === 'codingerror' &&
                    $e->a === 'out_as_local_url called on a non-local URL') {
                $messages->exception = get_string('h5pinvalidurl', 'core_h5p');
            } else {
                $messages->exception = $e->getMessage();
            }
        }

        if (empty($messages->error) && empty($messages->exception)) {
            // Add H5P assets to the page.
            $h5pplayer->add_assets_to_page();
            // Check if there is some error when adding assets to the page.
            $messages = $h5pplayer->get_messages();
            if (empty($messages->error)) {
                $fileh5p = $h5pplayer->get_export_file();
                $result[] = $fileh5p;
            }
        }
        if (!empty($messages->error)) {
            foreach ($messages->error as $error) {
                // We have to apply clean_param because warningcode is a PARAM_ALPHANUM.
                // And H5P has some error code like 'total-size-too-large'.
                $warnings[] = [
                    'item' => $url,
                    'warningcode' => clean_param($error->code, PARAM_ALPHANUM),
                    'message' => $error->message
                ];
            }
        } else if (!empty($messages->exception)) {
            $warnings[] = [
                'item' => $url,
                'warningcode' => $messages->code,
                'message' => $messages->exception
            ];
        }

        return [
            'files' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * get_trusted_h5p_file return
     *
     * @since  Moodle 3.8
     * @return \core_external\external_description
     */
    public static function get_trusted_h5p_file_returns() {
        return new external_single_structure(
            [
                'files'    => new external_files('H5P file trusted.'),
                'warnings' => new external_warnings()
            ]
        );
    }
}
