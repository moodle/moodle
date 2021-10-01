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

declare(strict_types=1);

namespace core_reportbuilder\local\models;

use context;
use context_system;
use core\persistent;
use core_reportbuilder\local\report\base;

/**
 * Persistent class to represent a report
 *
 * @package     core_reportbuilder
 * @copyright   2018 Alberto Lara Hernández <albertolara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends persistent {

    /** @var string The table name. */
    public const TABLE = 'reportbuilder_report';

    /**
     * Return the definition of the properties of this model
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'source' => [
                'type' => PARAM_RAW,
            ],
            'type' => [
                'type' => PARAM_INT,
                'choices' => [
                    base::TYPE_CUSTOM_REPORT,
                    base::TYPE_SYSTEM_REPORT,
                ],
            ],
            'contextid' => [
                'type' => PARAM_INT,
                'default' => static function(): int {
                    return context_system::instance()->id;
                }
            ],
            'component' => [
                'type' => PARAM_COMPONENT,
                'default' => '',
            ],
            'area' => [
                'type' => PARAM_AREA,
                'default' => '',
            ],
            'itemid' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'usercreated' => [
                'type' => PARAM_INT,
                'default' => static function(): int {
                    global $USER;

                    return (int) $USER->id;
                },
            ],
        ];
    }

    /**
     * Return report context, used by exporters
     *
     * @return context
     */
    public function get_context(): context {
        return context::instance_by_id($this->raw_get('contextid'));
    }
}
