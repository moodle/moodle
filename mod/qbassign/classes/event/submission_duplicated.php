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
 * The mod_qbassign submission duplicated event.
 *
 * @package    mod_qbassign
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_qbassign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_qbassign submission duplicated event class.
 *
 * @package    mod_qbassign
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_duplicated extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @since Moodle 2.7
     *
     * @param \qbassign $qbassign
     * @param \stdClass $submission
     * @return submission_duplicated
     */
    public static function create_from_submission(\qbassign $qbassign, \stdClass $submission) {
        $data = array(
            'objectid' => $submission->id,
            'context' => $qbassign->get_context(),
        );
        self::$preventcreatecall = false;
        /** @var submission_duplicated $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_qbassign($qbassign);
        $event->add_record_snapshot('qbassign_submission', $submission);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' duplicated their submission with id '$this->objectid' for the " .
            "qbassignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmissionduplicated', 'mod_qbassign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'qbassign_submission';
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $submission = $this->get_record_snapshot('qbassign_submission', $this->objectid);
        $this->set_legacy_logdata('submissioncopied', $this->qbassign->format_submission_for_log($submission));
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call submission_duplicated::create() directly, use submission_duplicated::create_from_submission() instead.');
        }

        parent::validate_data();
    }

    public static function get_objectid_mapping() {
        return array('db' => 'qbassign_submission', 'restore' => 'submission');
    }
}
