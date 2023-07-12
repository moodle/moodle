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
 * The mod_assign feedback viewed event.
 *
 * @package    block_use_stats
 * @category   blocks
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_use_stats\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The class for registering a keepalive event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int assignid: the id of the assignment.
 * }
 */
class block_use_stats_keepalive extends \core\event\base {

    /**
     * Create instance of event. Passes to internal processing the dynamic object table through 'other'.
     *
     * @param \assign $assign
     * @param \stdClass $grade
     * @return feedback_viewed
     */
    public static function create_from_cm($cm) {
        global $USER, $COURSE;

        $data = array();

        if (!is_null($cm)) {
            $data['objectid'] = $cm->id;
            $data['other'] = 'course_modules';
            $context = \context_module::instance($cm->id);
        } else {
            if ($COURSE->id == SITEID) {
                $data['objectid'] = $USER->id;
                $data['other'] = 'user';
                $context = \context_system::instance();
            } else {
                $data['objectid'] = $COURSE->id;
                $data['other'] = 'course';
                $context = \context_course::instance($COURSE->id);
            }
        }

        $data['relateduserid'] = $USER->id;
        $data['context'] = $context;

        $event = self::create($data);
        if (!is_null($cm)) {
            $event->add_record_snapshot('course_modules', $cm);
        }
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventusestatskeepalive', 'block_use_stats');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' is still in session ";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $logmessage = get_string('keepuseralive', 'block_use_stats', $this->relateduserid);
        $this->set_legacy_logdata('keepalive', $logmessage);
        return parent::get_legacy_logdata();
    }

    /**
     * Set the legacy log data.
     *
     * @param array $legacylogdata
     * @return void
     */
    public function set_legacy_logdata($legacylogdata, $msg) {
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {

        $this->data['objecttable'] = $this->data['other'];

        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

    }
}
