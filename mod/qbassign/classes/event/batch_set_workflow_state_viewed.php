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
 * The mod_qbassign qbassignment batch set workflow stated viewed event.
 *
 * @package    mod_qbassign
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_qbassign qbassignment batch set workflow stated viewed event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int qbassignid: the id of the qbassignment.
 * }
 *
 * @package    mod_qbassign
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class batch_set_workflow_state_viewed extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param \qbassign $qbassign
     * @return batch_set_workflow_state_viewed
     */
    public static function create_from_qbassign(\qbassign $qbassign) {
        $data = array(
            'context' => $qbassign->get_context(),
            'other' => array(
                'qbassignid' => $qbassign->get_instance()->id,
            ),
        );
        self::$preventcreatecall = false;
        /** @var batch_set_workflow_state_viewed $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_qbassign($qbassign);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventbatchsetworkflowstateviewed', 'mod_qbassign');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the batch set workflow for the qbassignment with course " .
            "module id '$this->contextinstanceid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $logmessage = get_string('viewbatchsetmarkingworkflowstate', 'qbassign');
        $this->set_legacy_logdata('view batch set marking workflow state', $logmessage);
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call batch_set_workflow_state_viewed::create() directly, use batch_set_workflow_state_viewed::create_from_qbassign() instead.');
        }

        parent::validate_data();

        if (!isset($this->other['qbassignid'])) {
            throw new \coding_exception('The \'qbassignid\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['qbassignid'] = array('db' => 'qbassign', 'restore' => 'qbassign');

        return $othermapped;
    }
}
