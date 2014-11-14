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
 * The mod_assign submission form viewed event.
 *
 * @package    mod_assign
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_assign submission form viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int assignid: the id of the assignment.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_confirmation_form_viewed extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param \assign $assign
     * @return submission_confirmation_form_viewed
     */
    public static function create_from_assign(\assign $assign) {
        $data = array(
            'context' => $assign->get_context(),
            'other' => array(
                'assignid' => $assign->get_instance()->id,
            ),
        );
        self::$preventcreatecall = false;
        /** @var submission_confirmation_form_viewed $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
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
        return get_string('eventsubmissionconfirmationformviewed', 'mod_assign');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the submission confirmation form for the assignment with " .
            "course module id '$this->contextinstanceid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $logmessage = get_string('viewownsubmissionform', 'assign');
        $this->set_legacy_logdata('view confirm submit assignment form', $logmessage);
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
            throw new \coding_exception('cannot call submission_confirmation_form_viewed::create() directly, use submission_confirmation_form_viewed::create_from_assign() instead.');
        }

        parent::validate_data();

        if (!isset($this->other['assignid'])) {
            throw new \coding_exception('The \'assignid\' value must be set in other.');
        }
    }
}
