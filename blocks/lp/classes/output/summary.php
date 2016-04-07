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
 * Summary renderable.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lp\output;
defined('MOODLE_INTERNAL') || die();

use core_competency\api;
use core_competency\external\competency_exporter;
use core_competency\external\plan_exporter;
use core_competency\external\user_competency_exporter;
use core_competency\external\user_summary_exporter;
use core_competency\plan;
use core_competency\url;
use renderable;
use renderer_base;
use templatable;

/**
 * Summary renderable class.
 *
 * @package    block_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary implements renderable, templatable {

    /** @var array Active plans. */
    protected $activeplans = array();
    /** @var array Competencies to review. */
    protected $compstoreview = array();
    /** @var array Plans to review. */
    protected $planstoreview = array();
    /** @var array Plans. */
    protected $plans = array();
    /** @var stdClass The user. */
    protected $user;

    /**
     * Constructor.
     * @param stdClass $user The user.
     */
    public function __construct($user = null) {
        global $USER;
        if (!$user) {
            $user = $USER;
        }
        $this->user = $user;

        // Get the plans.
        $this->plans = api::list_user_plans($this->user->id);

        // Get the competencies to review.
        $this->compstoreview = api::list_user_competencies_to_review(0, 3);

        // Get the plans to review.
        $this->planstoreview = api::list_plans_to_review(0, 3);
    }

    public function export_for_template(renderer_base $output) {
        $plans = array();
        foreach ($this->plans as $plan) {
            if (count($plans) >= 3) {
                break;
            }
            if ($plan->get_status() == plan::STATUS_ACTIVE) {
                $plans[] = $plan;
            }
        }
        $activeplans = array();
        foreach ($plans as $plan) {
            $planexporter = new plan_exporter($plan, array('template' => $plan->get_template()));
            $activeplans[] = $planexporter->export($output);
        }

        $compstoreview = array();
        foreach ($this->compstoreview['competencies'] as $compdata) {
            $ucexporter = new user_competency_exporter($compdata->usercompetency,
                array('scale' => $compdata->competency->get_scale()));
            $compexporter = new competency_exporter($compdata->competency,
                array('context' => $compdata->competency->get_context()));
            $userexporter = new user_summary_exporter($compdata->user);
            $compstoreview[] = array(
                'usercompetency' => $ucexporter->export($output),
                'competency' => $compexporter->export($output),
                'user' => $userexporter->export($output),
            );
        }

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
            'hasplans' => !empty($this->plans),
            'hasactiveplans' => !empty($activeplans),
            'hasmoreplans' => count($this->plans) > count($activeplans),
            'activeplans' => $activeplans,

            'compstoreview' => $compstoreview,
            'hascompstoreview' => $this->compstoreview['count'] > 0,
            'hasmorecompstoreview' => $this->compstoreview['count'] > 3,

            'planstoreview' => $planstoreview,
            'hasplanstoreview' => $this->planstoreview['count'] > 0,
            'hasmoreplanstoreview' => $this->planstoreview['count'] > 3,

            'plansurl' => url::plans($this->user->id)->out(false),
            'pluginbaseurl' => (new \moodle_url('/blocks/lp'))->out(false),
            'userid' => $this->user->id,
        );

        return $data;
    }

    /**
     * Returns whether there is content in the summary.
     *
     * @return boolean
     */
    public function has_content() {
        return !empty($this->plans) || $this->planstoreview['count'] > 0 || $this->compstoreview['count'] > 0;
    }

}
