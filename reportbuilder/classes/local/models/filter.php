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
use lang_string;
use core\persistent;

/**
 * Persistent class to represent a report filter/condition
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter extends persistent {

    /** @var string The table name. */
    public const TABLE = 'reportbuilder_filter';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'reportid' => [
                'type' => PARAM_INT,
            ],
            'uniqueidentifier' => [
                'type' => PARAM_RAW,
            ],
            'heading' => [
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'iscondition' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'filterorder' => [
                'type' => PARAM_INT,
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
     * Validate reportid property
     *
     * @param int $reportid
     * @return bool|lang_string
     */
    protected function validate_reportid(int $reportid) {
        if (!report::record_exists($reportid)) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Return the report this filter belongs to
     *
     * @return report
     */
    public function get_report(): report {
        return new report($this->get('reportid'));
    }

    /**
     * Return filter record
     *
     * @param int $reportid
     * @param int $filterid
     * @return false|static
     */
    public static function get_filter_record(int $reportid, int $filterid) {
        return self::get_record(['id' => $filterid, 'reportid' => $reportid, 'iscondition' => 0]);
    }

    /**
     * Return filter records for report
     *
     * @param int $reportid
     * @param string $sort
     * @param string $order
     * @return static[]
     */
    public static function get_filter_records(int $reportid, string $sort = '', string $order = 'ASC'): array {
        return self::get_records(['reportid' => $reportid, 'iscondition' => 0], $sort, $order);
    }

    /**
     * Return condition record
     *
     * @param int $reportid
     * @param int $conditionid
     * @return false|static
     */
    public static function get_condition_record(int $reportid, int $conditionid) {
        return self::get_record(['id' => $conditionid, 'reportid' => $reportid, 'iscondition' => 1]);
    }

    /**
     * Return condition records for report
     *
     * @param int $reportid
     * @param string $sort
     * @param string $order
     * @return static[]
     */
    public static function get_condition_records(int $reportid, string $sort = '', string $order = 'ASC'): array {
        return self::get_records(['reportid' => $reportid, 'iscondition' => 1], $sort, $order);
    }

    /**
     * Helper method to return the current maximum filter order value for a report
     *
     * @param int $reportid
     * @param bool $iscondition
     * @return int
     */
    public static function get_max_filterorder(int $reportid, bool $iscondition = false): int {
        global $DB;

        $params = ['reportid' => $reportid, 'iscondition' => (int) $iscondition];

        return (int) $DB->get_field(static::TABLE, "MAX(filterorder)", $params, MUST_EXIST);
    }

    /**
     * Return formatted filter heading
     *
     * @param context|null $context If the context of the report is already known, it should be passed here
     * @return string
     */
    public function get_formatted_heading(?context $context = null): string {
        if ($context === null) {
            $context = $this->get_report()->get_context();
        }

        return format_string($this->raw_get('heading'), true, ['context' => $context]);
    }
}
