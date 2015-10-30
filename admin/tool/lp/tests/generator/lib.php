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
 * Tool LP data generator.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_lp\competency;
use tool_lp\competency_framework;
use tool_lp\external;
use tool_lp\plan;
use tool_lp\related_competency;
use tool_lp\user_competency;
use tool_lp\user_competency_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Tool LP data generator.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_generator extends component_generator_base {

    /** @var int Number of created competencies. */
    protected $competencycount = 0;

    /** @var int Number of created frameworks. */
    protected $frameworkcount = 0;

     /** @var int Number of created plans. */
    protected $plancount = 0;

    /** @var stdClass Scale that we might need. */
    protected $scale;

    /**
     * Reset process.
     *
     * Do not call directly.
     *
     * @return void
     */
    public function reset() {
        $this->competencycount = 0;
        $this->frameworkcount = 0;
        $this->scale = null;
    }

    /**
     * Create a new competency.
     *
     * @param array|stdClass $record
     * @return competency
     */
    public function create_competency($record = null) {
        $this->frameworkcount++;
        $i = $this->frameworkcount;
        $record = (object) $record;

        if (!isset($record->competencyframeworkid)) {
            throw new coding_exception('The competencyframeworkid value is required.');
        }
        if (!isset($record->shortname)) {
            $record->shortname = "Competency shortname $i";
        }
        if (!isset($record->idnumber)) {
            $record->idnumber = "cmp{$i}";
        }
        if (!isset($record->description)) {
            $record->description = "Competency $i description ";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (!isset($record->visible)) {
            $record->visible = 1;
        }

        $competency = new competency(0, $record);
        $competency->create();

        return $competency;
    }

    /**
     * Create a new framework.
     *
     * @param array|stdClass $record
     * @return competency_framework
     */
    public function create_framework($record = null) {
        $generator = phpunit_util::get_data_generator();
        $this->frameworkcount++;
        $i = $this->frameworkcount;
        $record = (object) $record;

        if (!isset($record->shortname)) {
            $record->shortname = "Framework shortname $i";
        }
        if (!isset($record->idnumber)) {
            $record->idnumber = "frm{$i}";
        }
        if (!isset($record->description)) {
            $record->description = "Framework $i description ";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (!isset($record->visible)) {
            $record->visible = 1;
        }
        // TODO MDL-51442 make sure this passes validation.
        if (!isset($record->scaleid)) {
            if (isset($record->scaleconfiguration)) {
                throw new coding_exception('Scale configuration must be provided with a scale.');
            }
            if (!$this->scale) {
                $this->scale = $generator->create_scale();
            }
            $record->scaleid = $this->scale->id;
        }
        // TODO MDL-51442 make sure this passes validation.
        if (!isset($record->scaleconfiguration)) {
            $values = external::get_scale_values($record->scaleid);
            $scaleconfig = array(array('scaleid' => $record->scaleid));
            $scaleconfig[] = array(
                'name' => $values[0]['name'],
                'id' => $values[0]['id'],
                'scaledefault' => 1,
                'proficient' => 1,
            );
            $record->scaleconfiguration = json_encode($scaleconfig);
        }
        if (!isset($record->contextid)) {
            $record->contextid = context_system::instance()->id;
        }

        $framework = new competency_framework(0, $record);
        $framework->create();

        return $framework;
    }

    /**
     * Create a related competency.
     *
     * @param array|stdClass $record
     * @return related_competency
     */
    public function create_related_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->competencyid)) {
            throw new coding_exception('Property competencyid is required.');
        }
        if (!isset($record->relatedcompetencyid)) {
            throw new coding_exception('Property relatedcompetencyid is required.');
        }

        $relation = related_competency::get_relation($record->competencyid, $record->relatedcompetencyid);
        if ($relation->get_id()) {
            throw new coding_exception('Relation already exists');
        }
        $relation->create();

        return $relation;
    }

    /**
     * Create a new user competency.
     *
     * @param array|stdClass $record
     * @return user_competency
     */
    public function create_user_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $usercompetency = new user_competency(0, $record);
        $usercompetency->create();

        return $usercompetency;
    }

    /**
     * Create a new plan.
     *
     * @param array|stdClass $record
     * @return plan
     */
    public function create_plan($record = null) {
        $this->plancount++;
        $i = $this->plancount;
        $record = (object) $record;

        if (!isset($record->name)) {
            $record->name = "Plan shortname $i";
        }
        if (!isset($record->description)) {
            $record->description = "Plan $i description";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }

        $plan = new plan(0, $record);
        $plan->create();

        return $plan;
    }

    /**
     * Create a new user competency plan.
     *
     * @param array|stdClass $record
     * @return user_competency_plan
     */
    public function create_user_competency_plan($record = null) {
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        if (!isset($record->planid)) {
            throw new coding_exception('The planid value is required.');
        }

        $usercompetencyplan = new user_competency_plan(0, $record);
        $usercompetencyplan->create();

        return $usercompetencyplan;
    }

}

