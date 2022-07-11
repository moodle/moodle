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

namespace core_reportbuilder\local\systemreports;

use core_reportbuilder\local\models\audience;
use core_reportbuilder\local\models\report;
use core_reportbuilder\permission;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\audience as audience_helper;

/**
 * Report access list
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_access_list extends system_report {

    /**
     * Initialise the report
     */
    protected function initialise(): void {
        $userentity = new user();
        $userentityalias = $userentity->get_table_alias('user');
        $this->set_main_table('user', $userentityalias);
        $this->add_entity($userentity);

        $reportid = $this->get_parameter('id', 0, PARAM_INT);

        // Find users allowed to view the report thru the report audiences.
        [$wheres, $params] = self::get_users_by_audience_sql($reportid, $userentityalias);

        if (!empty($wheres)) {
            // Wrap each OR condition into brackets.
            $allwheres = '(' . implode(') OR (', $wheres) . ')';
        } else {
            $allwheres = "1=0";
        }

        $this->add_base_condition_sql("($allwheres)", $params);

        $this->add_column_from_entity('user:fullnamewithpicturelink');
        $this->add_filter_from_entity('user:fullname');
        $this->set_downloadable(false);
    }

    /**
     * Ensure we can view the report
     *
     * @return bool
     */
    protected function can_view(): bool {
        $reportid = $this->get_parameter('id', 0, PARAM_INT);
        $report = report::get_record(['id' => $reportid]);

        return $report && permission::can_edit_report($report);
    }

    /**
     * Find users who can access this report based on the audience and add them to the report.
     *
     * @param int $reportid
     * @param string $usertablealias
     * @return array
     */
    protected static function get_users_by_audience_sql(int $reportid, string $usertablealias): array {
        $audiences = audience::get_records(['reportid' => $reportid]);

        return audience_helper::user_audience_sql($audiences, $usertablealias);
    }
}
