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
 * The mod_choice answer deleted event.
 *
 * @package    mod_choice
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_choice\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_choice answer deleted event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int choiceid: id of choice.
 *      - int optionid: id of the option.
 * }
 *
 * @package    mod_choice
 * @since      Moodle 3.1
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class answer_deleted extends \core\event\base {

    /**
     * Creates an instance of the event from the records
     *
     * @param stdClass $choiceanswer record from 'choice_answers' table
     * @param stdClass $choice record from 'choice' table
     * @param stdClass $cm record from 'course_modules' table
     * @param stdClass $course
     * @return self
     */
    public static function create_from_object($choiceanswer, $choice, $cm, $course) {
        global $USER;
        $eventdata = array();
        $eventdata['objectid'] = $choiceanswer->id;
        $eventdata['context'] = \context_module::instance($cm->id);
        $eventdata['userid'] = $USER->id;
        $eventdata['courseid'] = $course->id;
        $eventdata['relateduserid'] = $choiceanswer->userid;
        $eventdata['other'] = array();
        $eventdata['other']['choiceid'] = $choice->id;
        $eventdata['other']['optionid'] = $choiceanswer->optionid;
        $event = self::create($eventdata);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('choice', $choice);
        $event->add_record_snapshot('choice_answers', $choiceanswer);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has deleted the option with id '" . $this->other['optionid'] . "' for the
            user with id '$this->relateduserid' from the choice activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventanswerdeleted', 'mod_choice');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/choice/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'choice_answers';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['choiceid'])) {
            throw new \coding_exception('The \'choiceid\' value must be set in other.');
        }

        if (!isset($this->other['optionid'])) {
            throw new \coding_exception('The \'optionid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'choice_answers', 'restore' => \core\event\base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['choiceid'] = array('db' => 'choice', 'restore' => 'choice');
        $othermapped['optionid'] = array('db' => 'choice_options', 'restore' => 'choice_option');

        return $othermapped;
    }
}
