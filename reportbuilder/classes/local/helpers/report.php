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

namespace core_reportbuilder\local\helpers;

use stdClass;
use invalid_parameter_exception;
use core\persistent;
use core_reportbuilder\datasource;
use core_reportbuilder\manager;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report as report_model;

/**
 * Helper class for manipulating custom reports and their elements (columns, filters, conditions, etc)
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report {

    /**
     * Create custom report
     *
     * @param stdClass $data
     * @param bool $default If $default is set to true it will populate report with default layout as defined by the selected
     *                      source. These include pre-defined columns, filters and conditions.
     * @return report_model
     */
    public static function create_report(stdClass $data, bool $default = true): report_model {
        // TODO move this properties_definition validation into the persistents, or resolve MDL-71086.
        $data = (object) array_merge(array_intersect_key((array) $data, report_model::properties_definition()), [
            'type' => datasource::TYPE_CUSTOM_REPORT,
        ]);

        $reportpersistent = manager::create_report_persistent($data);

        // Add datasource default columns, filters and conditions to the report.
        if ($default) {
            $source = $reportpersistent->get('source');
            /** @var datasource $datasource */
            $datasource = new $source($reportpersistent, []);
        }

        return $reportpersistent;
    }

    /**
     * Update custom report
     *
     * @param stdClass $data
     * @return report_model
     */
    public static function update_report(stdClass $data): report_model {
        $report = report_model::get_record(['id' => $data->id, 'type' => datasource::TYPE_CUSTOM_REPORT]);
        if ($report === false) {
            throw new invalid_parameter_exception('Invalid report');
        }

        $report->set('name', $data->name)
            ->update();

        return $report;
    }

    /**
     * Delete custom report
     *
     * @param int $reportid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report(int $reportid): bool {
        $report = report_model::get_record(['id' => $reportid, 'type' => datasource::TYPE_CUSTOM_REPORT]);
        if ($report === false) {
            throw new invalid_parameter_exception('Invalid report');
        }

        return $report->delete();
    }
}
