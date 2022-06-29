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

use context_system;
use renderer_base;
use core\output\dynamic_tabs\base;
use core_reportbuilder\permission;
use core_reportbuilder\system_report_factory;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\systemreports\report_schedules;

/**
 * Schedules dynamic tab
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedules extends base {

    /**
     * Export this for use in a mustache template context
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $report = system_report_factory::create(report_schedules::class, context_system::instance(), '', '', 0,
            ['reportid' => $this->data['reportid']]);

        return [
            'reportid' => $this->data['reportid'],
            'report' => $report->output(),
        ];
    }

    /**
     * The label to be displayed on the tab
     *
     * @return string
     */
    public function get_tab_label(): string {
        return get_string('schedules', 'core_reportbuilder');
    }

    /**
     * Check permission of the current user to access this tab
     *
     * @return bool
     */
    public function is_available(): bool {
        return permission::can_edit_report(new report($this->data['reportid']));
    }

    /**
     * Template to use to display tab contents
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_reportbuilder/local/dynamictabs/schedules';
    }
}
