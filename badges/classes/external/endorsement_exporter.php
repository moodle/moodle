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
 * Contains endorsement class for displaying a badge endorsement.
 *
 * @package   core_badges
 * @copyright 2018 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;

/**
 * Class for displaying a badge endorsement.
 *
 * @package   core_badges
 * @copyright 2018 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class endorsement_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Endorsement id',
            ],
            'badgeid' => [
                'type' => PARAM_INT,
                'description' => 'Badge id',
            ],
            'issuername' => [
                'type' => PARAM_TEXT,
                'description' => 'Endorsement issuer name',
            ],
            'issuerurl' => [
                'type' => PARAM_URL,
                'description' => 'Endorsement issuer URL',
            ],
            'issueremail' => [
                'type' => PARAM_RAW,
                'description' => 'Endorsement issuer email',
            ],
            'claimid' => [
                'type' => PARAM_URL,
                'description' => 'Claim URL',
                'null' => NULL_ALLOWED,
            ],
            'claimcomment' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Claim comment',
                'null' => NULL_ALLOWED,
            ],
            'dateissued' => [
                'type' => PARAM_INT,
                'description' => 'Date issued',
                'default' => 0,
            ]
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return array(
            'context' => 'context',
        );
    }
}
