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
 * mod_assign submission duplicated event.
 *
 * @package    mod_assign
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_assign submission duplicated event class.
 *
 * @package    mod_assign
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_duplicated extends base {
    /** @var \assign */
    protected $assign;
    /** @var \stdClass */
    protected $submission;
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
     * @param \assign $assign
     * @param \stdClass $submission
     * @return submission_duplicated
     */
    public static function create_from_submission(\assign $assign, \stdClass $submission) {
        $data = array(
            'objectid' => $submission->id,
            'context' => $assign->get_context(),
        );
        self::$preventcreatecall = false;
        /** @var submission_duplicated $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->assign = $assign;
        $event->submission = $submission;
        return $event;
    }

    /**
     * Get assign instance.
     *
     * NOTE: to be used from observers only.
     *
     * @since Moodle 2.7
     *
     * @return \assign
     */
    public function get_assign() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_assign() is intended for event observers only');
        }
        return $this->assign;
    }

    /**
     * Get submission instance.
     *
     * NOTE: to be used from observers only.
     *
     * @since Moodle 2.7
     *
     * @return \stdClass
     */
    public function get_submission() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_submission() is intended for event observers only');
        }
        return $this->submission;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user {$this->userid} duplicated his submission {$this->objectid}.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmissionduplicated', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'assign_submission';
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $this->set_legacy_logdata('submissioncopied', $this->assign->format_submission_for_log($this->submission));
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
}
