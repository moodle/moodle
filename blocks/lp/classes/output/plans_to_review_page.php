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
 * Plans to review renderable.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_lp\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use core_competency\api;
use core_competency\external\plan_exporter;
use core_user\external\user_summary_exporter;

/**
 * Plans to review renderable class.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plans_to_review_page implements renderable, templatable {

    /** @var array Plans to review. */
    protected $planstoreview;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->planstoreview = api::list_plans_to_review(0, 1000);
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        $planstoreview = array();
        foreach ($this->planstoreview['plans'] as $plandata) {
            $planexporter = new plan_exporter($plandata->plan, array('template' => $plandata->template));
            $userexporter = new user_summary_exporter($plandata->owner);
            $planstoreview[] = array(
                'plan' => $planexporter->export($output),
                'user' => $userexporter->export($output),
            );
        }

        $data = array(
            'plans' => $planstoreview,
            'pluginbaseurl' => (new moodle_url('/blocks/lp'))->out(false),
        );

        return $data;
    }

}
