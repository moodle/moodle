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

use renderer_base;
use core\persistent;
use core\external\persistent_exporter;
use core_reportbuilder\manager;
use core_reportbuilder\datasource;
use core_reportbuilder\form\filter as form_filter;
use core_reportbuilder\local\models\report;
use core_reportbuilder\table\custom_report_table;
use core_reportbuilder\table\custom_report_table_filterset;
use core_reportbuilder\table\custom_report_table_view;
use core_reportbuilder\table\custom_report_table_view_filterset;
use core_table\local\filter\integer_filter;

/**
 * Custom report exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_exporter extends persistent_exporter {

    /** @var report The persistent object we will export. */
    protected $persistent = null;

    /** @var bool */
    protected $editmode;

    /** @var string */
    protected $download;

    /**
     * report_exporter constructor.
     *
     * @param persistent $persistent
     * @param array $related
     * @param bool $editmode
     * @param string $download
     */
    public function __construct(persistent $persistent, array $related = [], bool $editmode = true, string $download = '') {
        parent::__construct($persistent, $related);
        $this->editmode = $editmode;
        $this->download = $download;
    }
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
            'pagesize' => 'int?',
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
            'filtersapplied' => ['type' => PARAM_INT],
            'filterspresent' => ['type' => PARAM_BOOL],
            'filtersform' => ['type' => PARAM_RAW],
            'editmode' => ['type' => PARAM_BOOL],
            'sidebarmenucards' => [
                'type' => custom_report_column_cards_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'conditions' => [
                'type' => custom_report_conditions_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'filters' => [
                'type' => custom_report_filters_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'sorting' => [
                'type' => custom_report_columns_sorting_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'cardview' => [
                'type' => custom_report_card_view_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'javascript' => ['type' => PARAM_RAW],
        ];
    }

    /**
     * Get additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        /** @var datasource $report */
        $report = manager::get_report_from_persistent($this->persistent);

        $filterspresent = false;
        $filtersform = '';

        if ($this->editmode) {
            $table = custom_report_table::create($this->persistent->get('id'));
            $table->set_filterset(new custom_report_table_filterset());
        } else {
            // We store the pagesize within the table filterset so that it's available between AJAX requests.
            $filterset = new custom_report_table_view_filterset();
            $filterset->add_filter(new integer_filter('pagesize', null, [$this->related['pagesize']]));

            $table = custom_report_table_view::create($this->persistent->get('id'), $this->download);
            $table->set_filterset($filterset);

            // Generate filters form if report contains any filters.
            $filterspresent = !empty($report->get_active_filters());
            if ($filterspresent) {
                $filtersform = $this->generate_filters_form()->render();
            }
        }

        // If we are editing we need all this information for the template.
        $editordata = [];
        if ($this->editmode) {
            $menucardsexporter = new custom_report_column_cards_exporter(null, ['report' => $report]);
            $editordata['sidebarmenucards'] = (array) $menucardsexporter->export($output);

            $conditionsexporter = new custom_report_conditions_exporter(null, ['report' => $report]);
            $editordata['conditions'] = (array) $conditionsexporter->export($output);

            $filtersexporter = new custom_report_filters_exporter(null, ['report' => $report]);
            $editordata['filters'] = (array) $filtersexporter->export($output);

            $sortingexporter = new custom_report_columns_sorting_exporter(null, ['report' => $report]);
            $editordata['sorting'] = (array) $sortingexporter->export($output);

            $cardviewexporter = new custom_report_card_view_exporter(null, ['report' => $report]);
            $editordata['cardview'] = (array) $cardviewexporter->export($output);
        }

        return [
            'table' => $output->render($table),
            'filtersapplied' => $report->get_applied_filter_count(),
            'filterspresent' => $filterspresent,
            'filtersform' => $filtersform,
            'editmode' => $this->editmode,
            'javascript' => '',
        ] + $editordata;
    }

    /**
     * Generate filters form for the report
     *
     * @return form_filter
     */
    private function generate_filters_form(): form_filter {
        $filtersform = new form_filter(null, null, 'post', '', [], true, [
            'reportid' => $this->persistent->get('id'),
            'parameters' => json_encode([]),
        ]);
        $filtersform->set_data_for_dynamic_submission();

        return $filtersform;
    }
}
