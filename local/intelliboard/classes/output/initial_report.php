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

namespace local_intelliboard\output;

defined('MOODLE_INTERNAL') || die();

use local_intelliboard\output\tables\intelliboard_table;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class containing data of "Report" page
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */
class initial_report implements renderable, templatable {

    var $params = [];

    public function __construct($params = []) {
        $this->params = $params;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE, $CFG;

        $reportname = '\local_intelliboard\output\tables\initial_reports\report' . $this->params["report_id"];
        /** @var intelliboard_table $report */
        $report = new $reportname("freereport{$this->params["report_id"]}", [
            "search" => $this->params["search"],
        ]);
        $report->is_downloading('', '', '');
        /** @var moodle_url $baseurl */
        $baseurl = clone $PAGE->url;
        $baseurl->remove_params("q");

        return [
            "showalert" => empty($this->params["intelliboard"]->token),
            "show_search_filter" => true,
            "report_table" => $report->export_for_template(10),
            "base_url" => $baseurl->out(),
            "search_val" => $this->params["search"],
            "dashboard_url" => new \moodle_url("/local/intelliboard"),
            "initial_reports" => intelli_initial_reports(),
            "connect_url" => new \moodle_url("/local/intelliboard/setup.php")
        ];
    }
}
