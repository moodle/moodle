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
 * Provides the {@link tool_policy\policy_version} persistent.
 *
 * @package    tool_policy
 * @copyright  2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Persistent model representing a single policy document version.
 *
 * @copyright  2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class policy_version extends persistent {

    /** @var string Table name this persistent is mapped to. */
    const TABLE = 'tool_policy_versions';

    /** @var int Site policy document. */
    const TYPE_SITE = 0;

    /** @var int Privacy policy document. */
    const TYPE_PRIVACY = 1;

    /** @var int Third party policy document. */
    const TYPE_THIRD_PARTY = 2;

    /** @var int Other policy document. */
    const TYPE_OTHER = 99;

    /** @var int Policy applies to all users. */
    const AUDIENCE_ALL = 0;

    /** @var int Policy applies to logged in users only. */
    const AUDIENCE_LOGGEDIN = 1;

    /** @var int Policy applies to guests only. */
    const AUDIENCE_GUESTS = 2;

    /** @var int Policy version is a draft. */
    const STATUS_DRAFT = 0;

    /** @var int Policy version is the active one. */
    const STATUS_ACTIVE = 1;

    /** @var int Policy version has been archived. */
    const STATUS_ARCHIVED = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => [
                'type' => PARAM_TEXT,
                'default' => '',
            ],
            'type' => [
                'type' => PARAM_INT,
                'choices' => [
                    self::TYPE_SITE,
                    self::TYPE_PRIVACY,
                    self::TYPE_THIRD_PARTY,
                    self::TYPE_OTHER,
                ],
                'default' => self::TYPE_SITE,
            ],
            'audience' => [
                'type' => PARAM_INT,
                'choices' => [
                    self::AUDIENCE_ALL,
                    self::AUDIENCE_LOGGEDIN,
                    self::AUDIENCE_GUESTS,
                ],
                'default' => self::AUDIENCE_ALL,
            ],
            'archived' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'policyid' => [
                'type' => PARAM_INT,
            ],
            'revision' => [
                'type' => PARAM_TEXT,
                'default' => '',
            ],
            'summary' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
            'summaryformat' => [
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
                'choices' => [
                    FORMAT_PLAIN,
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_MARKDOWN,
                ],
            ],
            'content' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
            'contentformat' => [
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
                'choices' => [
                    FORMAT_PLAIN,
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_MARKDOWN,
                ],
            ],
        ];
    }
}
