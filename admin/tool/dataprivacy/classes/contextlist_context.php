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

namespace tool_dataprivacy;

use core\persistent;

/**
 * The contextlist_context persistent.
 *
 * @package    tool_dataprivacy
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class contextlist_context extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_dataprivacy_ctxlst_ctx';

    /** This context is pending approval. */
    const STATUS_PENDING = 0;

    /** This context has been approved. */
    const STATUS_APPROVED = 1;

    /** This context has been rejected. */
    const STATUS_REJECTED = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'contextid' => [
                'type' => PARAM_INT
            ],
            'contextlistid' => [
                'type' => PARAM_INT
            ],
            'status' => [
                'choices' => [
                    self::STATUS_PENDING,
                    self::STATUS_APPROVED,
                    self::STATUS_REJECTED,
                ],
                'default' => self::STATUS_PENDING,
                'type' => PARAM_INT,
            ],
        ];
    }
}
