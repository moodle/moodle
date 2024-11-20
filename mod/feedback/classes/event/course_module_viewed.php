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
 * The mod_feedback course module viewed event.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_feedback course module viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int anonymous if feedback is anonymous.
 * }
 *
 * @package    mod_feedback
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'feedback';
    }

    /**
     * Creates an instance from feedback record
     *
     * @param stdClass $feedback
     * @param cm_info|stdClass $cm
     * @param stdClass $course
     * @return course_module_viewed
     */
    public static function create_from_record($feedback, $cm, $course) {
        $event = self::create(array(
            'objectid' => $feedback->id,
            'context' => \context_module::instance($cm->id),
            'anonymous' => ($feedback->anonymous == FEEDBACK_ANONYMOUS_YES),
            'other' => array(
                'anonymous' => $feedback->anonymous // Deprecated.
            )
        ));
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('feedback', $feedback);
        return $event;
    }

    /**
     * Define whether a user can view the event or not. Make sure no one except admin can see details of an anonymous response.
     *
     * @deprecated since 2.7
     *
     * @param int|\stdClass $userorid ID of the user.
     * @return bool True if the user can view the event, false otherwise.
     */
    public function can_view($userorid = null) {
        global $USER;
        debugging('can_view() method is deprecated, use anonymous flag instead if necessary.', DEBUG_DEVELOPER);

        if (empty($userorid)) {
            $userorid = $USER;
        }
        if ($this->anonymous) {
            return is_siteadmin($userorid);
        } else {
            return has_capability('mod/feedback:viewreports', $this->context, $userorid);
        }
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception in case of any problems.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['anonymous'])) {
            throw new \coding_exception('The \'anonymous\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'feedback', 'restore' => 'feedback');
    }

    public static function get_other_mapping() {
        // No need to map the 'anonymous' flag.
        return false;
    }
}

