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

namespace core_search\external;

use core\external\exporter;

/**
 * Contains related class for displaying information of a search result.
 *
 * @package   core_search
 * @since     Moodle 4.3
 */
class document_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'itemid' => [
                'type' => PARAM_INT,
                'description' => 'unique id in the search area scope',
            ],
            'componentname' => [
                'type' => PARAM_ALPHANUMEXT,
                'description' => 'component name',
            ],
            'areaname' => [
                'type' => PARAM_ALPHANUMEXT,
                'description' => 'search area name',
            ],
            'courseurl' => [
                'type' => PARAM_URL,
                'description' => 'result course url',
            ],
            'coursefullname' => [
                'type' => PARAM_RAW,
                'description' => 'result course fullname',
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'result modified time',
            ],
            'title' => [
                'type' => PARAM_RAW,
                'description' => 'result title',
            ],
            'docurl' => [
                'type' => PARAM_URL,
                'description' => 'result url',
            ],
            'iconurl' => [
                'type' => PARAM_URL,
                'description' => 'icon url',
                'optional' => true,
                'default' => '',
                'null' => NULL_ALLOWED,
            ],
            'content' => [
                'type' => PARAM_RAW,
                'description' => 'result contents',
                'optional' => true,
                'default' => '',
                'null' => NULL_ALLOWED,
            ],
            'contextid' => [
                'type' => PARAM_INT,
                'description' => 'result context id',
            ],
            'contexturl' => [
                'type' => PARAM_URL,
                'description' => 'result context url',
            ],
            'description1' => [
                'type' => PARAM_RAW,
                'description' => 'extra result contents, depends on the search area',
                'optional' => true,
                'default' => '',
                'null' => NULL_ALLOWED,
            ],
            'description2' => [
                'type' => PARAM_RAW,
                'description' => 'extra result contents, depends on the search area',
                'optional' => true,
                'default' => '',
                'null' => NULL_ALLOWED,
            ],
            'multiplefiles' => [
                'type' => PARAM_INT,
                'description' => 'whether multiple files are returned or not',
                'optional' => true,
            ],
            'filenames' => [
                'type' => PARAM_RAW,
                'description' => 'result file names if present',
                'muultiple' => true,
                'optional' => true,
            ],
            'filename' => [
                'type' => PARAM_RAW,
                'description' => 'result file name if present',
                'optional' => true,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'user id',
                'optional' => true,
            ],
            'userurl' => [
                'type' => PARAM_URL,
                'description' => 'user url',
                'optional' => true,
            ],
            'userfullname' => [
                'type' => PARAM_RAW,
                'description' => 'user fullname',
                'optional' => true,
            ],
            'textformat' => [
                'type' => PARAM_INT,
                'description' => 'text fields format, it is the same for all of them',
            ]
        ];
    }
}
