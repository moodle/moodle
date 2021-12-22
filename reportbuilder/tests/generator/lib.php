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

use core_reportbuilder\local\helpers\report as helper;
use core_reportbuilder\local\helpers\schedule as schedule_helper;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\schedule;
use core_reportbuilder\local\audiences\base as audience_base;

/**
 * Report builder test generator
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_reportbuilder_generator extends component_generator_base {

    /**
     * Create report
     *
     * @param array|stdClass $record
     * @return report
     * @throws coding_exception
     */
    public function create_report($record): report {
        $record = (array) $record;

        if (!array_key_exists('name', $record)) {
            throw new coding_exception('Record must contain \'name\' property');
        }
        if (!array_key_exists('source', $record)) {
            throw new coding_exception('Record must contain \'source\' property');
        }

        // Include default setup unless specifically disabled in passed record.
        $default = (bool) ($record['default'] ?? true);

        return helper::create_report((object) $record, $default);
    }

    /**
     * Create report column
     *
     * @param array|stdClass $record
     * @return column
     * @throws coding_exception
     */
    public function create_column($record): column {
        $record = (array) $record;

        if (!array_key_exists('reportid', $record)) {
            throw new coding_exception('Record must contain \'reportid\' property');
        }
        if (!array_key_exists('uniqueidentifier', $record)) {
            throw new coding_exception('Record must contain \'uniqueidentifier\' property');
        }

        return helper::add_report_column($record['reportid'], $record['uniqueidentifier']);
    }

    /**
     * Create report filter
     *
     * @param array|stdClass $record
     * @return filter
     * @throws coding_exception
     */
    public function create_filter($record): filter {
        $record = (array) $record;

        if (!array_key_exists('reportid', $record)) {
            throw new coding_exception('Record must contain \'reportid\' property');
        }
        if (!array_key_exists('uniqueidentifier', $record)) {
            throw new coding_exception('Record must contain \'uniqueidentifier\' property');
        }

        return helper::add_report_filter($record['reportid'], $record['uniqueidentifier']);
    }

    /**
     * Create report condition
     *
     * @param array|stdClass $record
     * @return filter
     * @throws coding_exception
     */
    public function create_condition($record): filter {
        $record = (array) $record;

        if (!array_key_exists('reportid', $record)) {
            throw new coding_exception('Record must contain \'reportid\' property');
        }
        if (!array_key_exists('uniqueidentifier', $record)) {
            throw new coding_exception('Record must contain \'uniqueidentifier\' property');
        }

        return helper::add_report_condition($record['reportid'], $record['uniqueidentifier']);
    }

    /**
     * Create report audience
     *
     * @param array|stdClass $record
     * @return audience_base
     * @throws coding_exception
     */
    public function create_audience($record): audience_base {
        $record = (array) $record;

        // Required properties.
        if (!array_key_exists('reportid', $record)) {
            throw new coding_exception('Record must contain \'reportid\' property');
        }
        if (!array_key_exists('configdata', $record)) {
            throw new coding_exception('Record must contain \'configdata\' property');
        }

        // Default to all users if not specified, for convenience.
        /** @var audience_base $classname */
        $classname = $record['classname'] ??
            \core_reportbuilder\reportbuilder\audience\allusers::class;

        return ($classname)::create($record['reportid'], $record['configdata']);
    }

    /**
     * Create report schedule
     *
     * @param array|stdClass $record
     * @return schedule
     * @throws coding_exception
     */
    public function create_schedule($record): schedule {
        $record = (array) $record;

        // Required properties.
        if (!array_key_exists('reportid', $record)) {
            throw new coding_exception('Record must contain \'reportid\' property');
        }
        if (!array_key_exists('name', $record)) {
            throw new coding_exception('Record must contain \'name\' property');
        }

        // Optional properties.
        if (!array_key_exists('format', $record)) {
            $record['format'] = 'csv';
        }
        if (!array_key_exists('subject', $record)) {
            $record['subject'] = $record['name'] . ' subject';
        }
        if (!array_key_exists('message', $record)) {
            $record['message'] = $record['name'] . ' message';
        }
        if (!array_key_exists('timescheduled', $record)) {
            $record['timescheduled'] = usergetmidnight(time() + DAYSECS);
        }

        return schedule_helper::create_schedule((object) $record);
    }
}
