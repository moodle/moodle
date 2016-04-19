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
 * Evidence created event.
 *
 * @package    core_competency
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

use core\event\base;
use core_competency\evidence;
use core_competency\user_competency;

defined('MOODLE_INTERNAL') || die();

/**
 * Evidence created event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int usercompetencyid: The user_competency ID linked to the evidence.
 *      - int competencyid: The competency ID linked to the evidence from user_competency.
 *      - int action: The action constant.
 *      - bool recommend: The recommend flag.
 * }
 *
 * @package    core_competency
 * @since      Moodle 3.1
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_evidence_created extends base {

    /**
     * Convenience method to instantiate the event.
     *
     * @param evidence $evidence The evidence.
     * @param user_competency $usercompetency The user competency object linked to the evidence.
     * @param bool $recommend The recommend flag.
     * @return evidence_created
     * @throws \coding_exception
     */
    public static final function create_from_evidence(evidence $evidence, user_competency $usercompetency, $recommend) {
        // Make sure we have a valid evidence.
        if (!$evidence->get_id()) {
            throw new \coding_exception('The evidence ID must be set.');
        }

        // Make sure we have a valid user competency.
        if (!$usercompetency->get_id()) {
            throw new \coding_exception('The user competency ID must be set.');
        }

        // Make sure that the a proper user competecy is linked to the evidence.
        if ($evidence->get_usercompetencyid() != $usercompetency->get_id()) {
            throw new \coding_exception('The user competency linked with this evidence is invalid.');
        }

        $event = static::create([
            'contextid'  => $evidence->get_contextid(),
            'objectid' => $evidence->get_id(),
            'userid' => $evidence->get_actionuserid(),
            'relateduserid' => $usercompetency->get_userid(),
            'other' => [
                'usercompetencyid' => $usercompetency->get_id(),
                'competencyid' => $usercompetency->get_competencyid(),
                'action' => $evidence->get_action(),
                'recommend' => $recommend
            ]
        ]);

        // Add record snapshot for the evidence.
        $event->add_record_snapshot(evidence::TABLE, $evidence->to_record());

        // Add record snapshot for the user competency.
        $event->add_record_snapshot(user_competency::TABLE, $usercompetency->to_record());

        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventevidencecreated', 'core_competency');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created an evidence with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return \core_competency\url::user_competency($this->other['usercompetencyid']);
    }

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = evidence::TABLE;
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
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
     * Validate the data.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['usercompetencyid'])) {
            throw new \coding_exception('The \'usercompetencyid\' data in \'other\' must be set.');
        }

        if (!isset($this->other['competencyid'])) {
            throw new \coding_exception('The \'competencyid\' data in \'other\' must be set.');
        }

        if (!isset($this->other['action'])) {
            throw new \coding_exception('The \'action\' data in \'other\' must be set.');
        }

        if (!isset($this->other['recommend'])) {
            throw new \coding_exception('The \'recommend\' data in \'other\' must be set.');
        }
    }
}
