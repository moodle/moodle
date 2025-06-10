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

/**
 * Class containing data report for user learning plan template.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use core_competency\api;
use core_competency\external\plan_exporter;

/**
 * Class containing data report for user learning plan template.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_report_page implements renderable, templatable {

    /** @var array|\core_competency\plan[] $plans List of plans. */
    protected $plans = array();

    /** @var int|null $userid Userid. */
    protected $userid = null;

    /**
     * Construct this renderable.
     *
     * @param int $userid The user id
     */
    public function __construct($userid) {
        $this->userid = $userid;
        $this->plans = api::list_user_plans($userid);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->userid = $this->userid;
        $data->cmcompgradingenabled = \report_lpmonitoring\api::is_cm_comptency_grading_enabled();

        // Attach standard objects as mustache can not parse \core_competency\plan objects.
        $data->plans = array();
        if ($this->plans) {
            foreach ($this->plans as $plan) {
                $exporter = new plan_exporter($plan, array('template' => $plan->get_template()));
                $record = $exporter->export($output);
                $data->plans[] = $record;
            }
        }

        return $data;
    }
}
