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
 * Performance helper.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency\external;
defined('MOODLE_INTERNAL') || die();

use core_competency\competency;
use core_competency\competency_framework;

/**
 * Performance helper class.
 *
 * This tool keeps a local cache of certain items, which means that subsequent
 * calls to get the resource will not query the database. You will want to use
 * this when many resources could be shared and need to be queried in a loop.
 *
 * Note that some of these improvements can only be achieved by knowing the
 * logic deeper in other modules. For instance we know that a competency's context
 * is the one of its framework. This tool must be kept in sync with those APIs.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class performance_helper {

    /** @var \context Cache of contexts by framework ID. */
    protected $frameworkscontexts = [];

    /** @var competency_framework Cache of frameworks by framework ID. */
    protected $frameworks = [];

    /** @var \grade_scale[] Cache of scales by scale ID. */
    protected $scales = [];

    /**
     * Get the context of a competency.
     *
     * @param competency $competency The competency.
     * @return \context
     */
    public function get_context_from_competency(competency $competency) {
        $frameworkid = $competency->get('competencyframeworkid');
        if (!isset($this->frameworkscontexts[$frameworkid])) {
            $framework = $this->get_framework_from_competency($competency);
            $this->frameworkscontexts[$frameworkid] = $framework->get_context();
        }
        return $this->frameworkscontexts[$frameworkid];
    }

    /**
     * Get the framework of a competency.
     *
     * @param competency $competency The competency.
     * @return competency_framework
     */
    public function get_framework_from_competency(competency $competency) {
        $frameworkid = $competency->get('competencyframeworkid');
        if (!isset($this->frameworks[$frameworkid])) {
            $this->frameworks[$frameworkid] = $competency->get_framework();
        }
        return $this->frameworks[$frameworkid];
    }

    /**
     * Get the scale of a competency.
     *
     * /!\ Make sure that this is always kept in sync with:
     *  - core_competency\competency::get_scale()
     *  - core_competency\competency_framework::get_scale()
     *
     * @param competency $competency The competency.
     * @return \grade_scale
     */
    public function get_scale_from_competency(competency $competency) {
        $scaleid = $competency->get('scaleid');
        if ($scaleid !== null && !isset($this->scales[$scaleid])) {
            $this->scales[$scaleid] = $competency->get_scale();

        } else if ($scaleid === null) {
            $framework = $this->get_framework_from_competency($competency);
            $scaleid = $framework->get('scaleid');
            if (!isset($this->scales[$scaleid])) {
                $this->scales[$scaleid] = $framework->get_scale();
            }
        }

        return $this->scales[$scaleid];
    }

    /**
     * Ingest a framework to avoid additional fetching.
     *
     * @param competency_framework $framework The framework.
     * @return void
     */
    public function ingest_framework(competency_framework $framework) {
        $id = $framework->get('id');
        $this->frameworks[$id] = $framework;
    }

}
