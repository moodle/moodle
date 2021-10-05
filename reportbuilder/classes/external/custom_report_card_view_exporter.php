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

use core_reportbuilder\form\card_view;
use renderer_base;
use core\external\exporter;
use core_reportbuilder\local\report\base;

/**
 * Custom report card view exporter class
 *
 * @package     core_reportbuilder
 * @copyright   2021 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_card_view_exporter extends exporter {

    /**
     * Return a list of objects that are related to the exporter
     *
     * @return array
     */
    protected static function define_related(): array {
        return [
            'report' => base::class,
        ];
    }

    /**
     * Return the list of additional properties for read structure and export
     *
     * @return array[]
     */
    protected static function define_other_properties(): array {
        return [
            'form' => [
                'type' => PARAM_RAW,
                'optional' => true,
            ],
            'helpicon' => [
                'type' => PARAM_RAW,
                'optional' => true,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_other_values(renderer_base $output): array {
        /** @var base $report */
        $report = $this->related['report'];

        $reportid = $report->get_report_persistent()->get('id');

        $reportsettings = $report->get_settings_values();
        $cardviewsettings = [
            'showfirsttitle' => $reportsettings['cardview_showfirsttitle'] ?? 0,
            'visiblecolumns' => $reportsettings['cardview_visiblecolumns'] ?? 1,
        ];
        $cardviewform = new card_view(null, null, 'post', '', [], true,
            array_merge(['reportid' => $reportid], $cardviewsettings));
        $cardviewform->set_data_for_dynamic_submission();

        return [
            'form' => $cardviewform->render(),
            'helpicon' => $output->help_icon('cardview', 'core_reportbuilder'),
        ];
    }
}
