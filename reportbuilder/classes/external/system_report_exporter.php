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

namespace core_reportbuilder\external;

use core\external\persistent_exporter;
use core_table\local\filter\integer_filter;
use core_table\local\filter\string_filter;
use core_reportbuilder\form\filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base;
use core_reportbuilder\table\system_report_table;
use core_reportbuilder\table\system_report_table_filterset;
use renderer_base;

/**
 * Report exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class system_report_exporter extends persistent_exporter {

    /**
     * Return the name of the class we are exporting
     *
     * @return string
     */
    protected static function define_class(): string {
        return report::class;
    }

    /**
     * Return a list of objects that are related to the persistent
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'source' => base::class,
            'parameters' => 'string',
        ];
    }

    /**
     * Return a list of additional properties used only for display
     *
     * @return array
     */
    protected static function define_other_properties(): array {
        return [
            'table' => ['type' => PARAM_RAW],
            'parameters' => ['type' => PARAM_RAW],
            'filterspresent' => ['type' => PARAM_BOOL],
            'filtersapplied' => ['type' => PARAM_INT],
            'filtersform' => [
                'type' => PARAM_RAW,
                'optional' => true,
            ],
        ];
    }

    /**
     * Get additional values to inject while exporting
     *
     * @uses \core_reportbuilder\output\renderer::render_system_report_table()
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        /** @var base $source */
        $source = $this->related['source'];

        /** @var string $parameters */
        $parameters = $this->related['parameters'];

        /** @var int $reportid */
        $reportid = $this->persistent->get('id');

        // We store the report ID and parameters within the table filterset so that they are available between AJAX requests.
        $filterset = new system_report_table_filterset();
        $filterset->add_filter(new integer_filter('reportid', null, [$reportid]));
        $filterset->add_filter(new string_filter('parameters', null, [$parameters]));

        $table = system_report_table::create($reportid, (array) json_decode($parameters, true));
        $table->set_filterset($filterset);

        // Generate filters form if report contains any filters.
        $filterspresent = !empty($source->get_active_filters());
        if ($filterspresent) {
            $filtersform = new filter(null, null, 'post', '', [], true, [
                'reportid' => $reportid,
                'parameters' => $parameters,
            ]);
            $filtersform->set_data_for_dynamic_submission();
        }

        return [
            'table' => $output->render($table),
            'parameters' => $parameters,
            'filterspresent' => $filterspresent,
            'filtersapplied' => $source->get_applied_filter_count(),
            'filtersform' => $filterspresent ? $filtersform->render() : '',
        ];
    }
}
