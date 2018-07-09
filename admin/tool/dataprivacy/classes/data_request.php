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
 * Class for loading/storing data requests from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class for loading/storing competencies from the DB.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_request extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_request';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'type' => [
                'choices' => [
                    api::DATAREQUEST_TYPE_EXPORT,
                    api::DATAREQUEST_TYPE_DELETE,
                    api::DATAREQUEST_TYPE_OTHERS,
                ],
                'type' => PARAM_INT
            ],
            'comments' => [
                'type' => PARAM_TEXT,
                'default' => ''
            ],
            'commentsformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
            'userid' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'requestedby' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'status' => [
                'default' => api::DATAREQUEST_STATUS_PENDING,
                'choices' => [
                    api::DATAREQUEST_STATUS_PENDING,
                    api::DATAREQUEST_STATUS_PREPROCESSING,
                    api::DATAREQUEST_STATUS_AWAITING_APPROVAL,
                    api::DATAREQUEST_STATUS_APPROVED,
                    api::DATAREQUEST_STATUS_PROCESSING,
                    api::DATAREQUEST_STATUS_COMPLETE,
                    api::DATAREQUEST_STATUS_CANCELLED,
                    api::DATAREQUEST_STATUS_REJECTED,
                ],
                'type' => PARAM_INT
            ],
            'dpo' => [
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ],
            'dpocomment' => [
                'default' => '',
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ],
            'dpocommentformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
        ];
    }
}
