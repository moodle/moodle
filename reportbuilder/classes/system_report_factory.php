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

namespace core_reportbuilder;

use context;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base;

/**
 * Factory class for creating system report instances
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_factory {

    /**
     * Create and return instance of given system report source
     *
     * @param string $source Class path of system report definition
     * @param context $context
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @param array $parameters Simple key/value pairs, accessed inside reports using $this->get_parameter()
     * @return system_report
     * @throws source_invalid_exception
     */
    public static function create(string $source, context $context, string $component = '', string $area = '',
            int $itemid = 0, array $parameters = []): system_report {

        // Exit early if source isn't a system report.
        if (!manager::report_source_exists($source, system_report::class)) {
            throw new source_invalid_exception($source);
        }

        $report = static::get_report_persistent($source, $context, $component, $area, $itemid);

        return manager::get_report_from_persistent($report, $parameters);
    }

    /**
     * Given a report source, with accompanying context information, return a persistent report instance
     *
     * @param string $source
     * @param context $context
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @return report
     */
    private static function get_report_persistent(string $source, context $context, string $component = '', string $area = '',
            int $itemid = 0): report {

        $reportdata = [
            'type' => base::TYPE_SYSTEM_REPORT,
            'source' => $source,
            'contextid' => $context->id,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
        ];

        if ($report = report::get_record($reportdata)) {
             return $report;
        }

        return manager::create_report_persistent((object) $reportdata);
    }
}
