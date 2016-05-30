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
 * User competency plan viewed event.
 *
 * @package    core_competency
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

use core\event\base;
use core_competency\user_competency_plan;

defined('MOODLE_INTERNAL') || die();

/**
 * User competency plan viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int planid: id of plan for which competency is associated.
 *      - int competencyid: id of the competency.
 * }
 *
 * @package    core_competency
 * @since      Moodle 3.1
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_user_competency_plan_viewed extends base {

    /**
     * Convenience method to instantiate the event.
     *
     * @param user_competency_plan $usercompetencyplan The user competency plan.
     * @return self
     */
    public static function create_from_user_competency_plan(user_competency_plan $usercompetencyplan) {
        if (!$usercompetencyplan->get_id()) {
            throw new \coding_exception('The user competency plan ID must be set.');
        }
        $event = static::create(array(
            'contextid' => $usercompetencyplan->get_context()->id,
            'objectid' => $usercompetencyplan->get_id(),
            'relateduserid' => $usercompetencyplan->get_userid(),
            'other' => array(
                'planid' => $usercompetencyplan->get_planid(),
                'competencyid' => $usercompetencyplan->get_competencyid()
            )
        ));
        $event->add_record_snapshot(user_competency_plan::TABLE, $usercompetencyplan->to_record());
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the user competency plan with id '$this->objectid'";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventusercompetencyplanviewed', 'core_competency');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return \core_competency\url::user_competency_in_plan($this->relateduserid, $this->other['competencyid'],
            $this->other['planid']);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = user_competency_plan::TABLE;
    }

    /**
     * Get_objectid_mapping method.
     *
     * @return string the name of the restore mapping the objectid links to
     */
    public static function get_objectid_mapping() {
        return base::NOT_MAPPED;
    }

    /**
     * Custom validation.
     *
     * Throw \coding_exception notice in case of any problems.
     */
    protected function validate_data() {
        if ($this->other === null) {
            throw new \coding_exception('The \'competencyid\' and \'planid\' values must be set.');
        }

        if (!isset($this->other['competencyid'])) {
            throw new \coding_exception('The \'competencyid\' value must be set.');
        }

        if (!isset($this->other['planid'])) {
            throw new \coding_exception('The \'planid\' value must be set.');
        }
    }

}
