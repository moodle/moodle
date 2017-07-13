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
 * Google Drive Rest API.
 *
 * @package    fileconverter_googledrive
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace fileconverter_googledrive;

defined('MOODLE_INTERNAL') || die();

/**
 * Google Drive Rest API.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rest extends \core\oauth2\rest {

    /**
     * Define the functions of the rest API.
     *
     * @return array Example:
     *  [ 'listFiles' => [ 'method' => 'get', 'endpoint' => 'http://...', 'args' => [ 'folder' => PARAM_STRING ] ] ]
     */
    public function get_api_functions() {
        return [
            'upload' => [
                'endpoint' => 'https://www.googleapis.com/upload/drive/v3/files',
                'method' => 'post',
                'args' => [
                    'uploadType' => PARAM_RAW,
                    'fields' => PARAM_RAW
                ],
                'response' => 'headers'
            ],
            'upload_content' => [
                'endpoint' => '{uploadurl}',
                'method' => 'put',
                'args' => [
                    'uploadurl' => PARAM_URL
                ],
                'response' => 'json'
            ],
            'create' => [
                'endpoint' => 'https://www.googleapis.com/drive/v3/files',
                'method' => 'post',
                'args' => [
                    'fields' => PARAM_RAW
                ],
                'response' => 'json'
            ],
            'delete' => [
                'endpoint' => 'https://www.googleapis.com/drive/v3/files/{fileid}',
                'method' => 'delete',
                'args' => [
                    'fileid' => PARAM_RAW
                ],
                'response' => 'json'
            ],
        ];
    }
}
