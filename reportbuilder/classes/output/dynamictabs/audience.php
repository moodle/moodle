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

namespace core_reportbuilder\output\dynamictabs;

use core\output\dynamic_tabs\base;
use core_reportbuilder\external\custom_report_audience_cards_exporter;
use core_reportbuilder\local\helpers\audience as audience_helper;
use core_reportbuilder\local\models\report;
use core_reportbuilder\output\audience_heading_editable;
use core_reportbuilder\permission;
use renderer_base;

/**
 * Audience dynamic tab
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience extends base {

    /**
     * Export this for use in a mustache template context.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        // Get all the audiences types to populate the left menu.
        $menucardsexporter = new custom_report_audience_cards_exporter(null);
        $menucards = (array) $menucardsexporter->export($output);

        // Get all current audiences instances for this report.
        $audienceinstances = $this->get_all_report_audiences();

        $data = [
            'tabheading' => get_string('audience', 'core_reportbuilder'),
            'reportid' => $this->data['reportid'],
            'contextid' => (new report((int)$this->data['reportid']))->get('contextid'),
            'sidebarmenucards' => $menucards,
            'instances' => $audienceinstances,
            'hasinstances' => !empty($audienceinstances),
        ];

        return $data;
    }

    /**
     * The label to be displayed on the tab
     *
     * @return string
     */
    public function get_tab_label(): string {
        return get_string('audience', 'core_reportbuilder');
    }

    /**
     * Check permission of the current user to access this tab
     *
     * @return bool
     */
    public function is_available(): bool {
        $reportpersistent = new report((int)$this->data['reportid']);
        return permission::can_edit_report($reportpersistent);
    }

    /**
     * Template to use to display tab contents
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_reportbuilder/local/dynamictabs/audience';
    }

    /**
     * Get all current audiences instances for this report.
     *
     * @return array
     */
    private function get_all_report_audiences(): array {
        global $PAGE;

        $renderer = $PAGE->get_renderer('core');

        $audienceinstances = [];
        $reportaudiences = audience_helper::get_base_records((int)$this->data['reportid']);
        $showormessage = false;
        foreach ($reportaudiences as $reportaudience) {
            $persistent = $reportaudience->get_persistent();
            $canedit = $reportaudience->user_can_edit();

            $editable = new audience_heading_editable(0, $persistent);

            $params = [
                'instanceid' => $persistent->get('id'),
                'description' => $reportaudience->get_description(),
                'heading' => $reportaudience->get_name(),
                'headingeditable' => $editable->render($renderer),
                'canedit' => $canedit,
                'candelete' => $canedit,
                'showormessage' => $showormessage,
            ];
            $audienceinstances[] = $params;
            $showormessage = true;
        }

        return $audienceinstances;
    }
}
