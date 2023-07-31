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
 * The mod_qbassign submission form viewed event.
 *
 * @package    mod_qbassign
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_qbassign submission form viewed event class.
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
class submission_form_viewed extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param \qbassign $qbassign
     * @param \stdClass $user
     * @return submission_form_viewed
     */
    public static function create_from_user(\qbassign $qbassign, \stdClass $user) {
        $data = array(
            'relateduserid' => $user->id,
            'context' => $qbassign->get_context(),
            'other' => array(
                'qbassignid' => $qbassign->get_instance()->id,
            ),
        );
        self::$preventcreatecall = false;
        /** @var submission_form_viewed $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_qbassign($qbassign);
        $event->add_record_snapshot('user', $user);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmissionformviewed', 'mod_qbassign');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        if ($this->userid != $this->relateduserid) {
            return "The user with id '$this->userid' viewed the submission form for the user with id '$this->relateduserid' " .
                "for the qbassignment with course module id '$this->contextinstanceid'.";
        }

        return "The user with id '$this->userid' viewed their submission for the qbassignment with course module id " .
            "'$this->contextinstanceid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        if ($this->relateduserid == $this->userid) {
            $title = get_string('editsubmission', 'qbassign');
        } else {
            $user = $this->get_record_snapshot('user', $this->relateduserid);
            $title = get_string('editsubmissionother', 'qbassign', fullname($user));
        }
        $this->set_legacy_logdata('view submit qbassignment form', $title);
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call submission_form_viewed::create() directly, use submission_form_viewed::create_from_user() instead.');
        }

        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

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
