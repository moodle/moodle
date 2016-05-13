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
 * Class for course_competency_settings persistence.
 *
 * @package    core_competency
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;

use lang_string;
use context_course;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for course_competency_settings persistence.
 *
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_competency_settings extends persistent {

    /** Table name for plan_competency persistency */
    const TABLE = 'competency_coursecompsetting';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'courseid' => array(
                'type' => PARAM_INT,
            ),
            'pushratingstouserplans' => array(
                'type' => PARAM_BOOL,
                'default' => function() {
                    return get_config('core_competency', 'pushcourseratingstouserplans');
                }
            ),
        );
    }

    /**
     * Get a the course settings for a single course.
     *
     * @param int $courseid The course id
     * @return course_competency_settings
     */
    public static function get_by_courseid($courseid) {
        global $DB;

        $params = array(
            'courseid' => $courseid
        );

        $settings = new static(null, (object) $params);
        if ($record = $DB->get_record(self::TABLE, $params)) {
            $settings->from_record($record);
        }

        return $settings;
    }

    /**
     * Can the current user view competency settings for this course.
     *
     * @param int $courseid The course ID.
     * @return bool
     */
    public static function can_read($courseid) {
        $context = context_course::instance($courseid);

        $capabilities = array('moodle/competency:coursecompetencyview');

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user change competency settings for this course.
     *
     * @param int $courseid The course ID.
     * @return bool
     */
    public static function can_manage_course($courseid) {
        $context = context_course::instance($courseid);

        $capabilities = array('moodle/competency:coursecompetencyconfigure');

        return has_any_capability($capabilities, $context);
    }

    /**
     * Can the current user change competency settings for this course.
     *
     * @return bool
     */
    public function can_manage() {
        return static::can_manage_course($this->get_courseid());
    }

    /**
     * Validate course ID.
     *
     * @param int $data The course ID.
     * @return true|lang_string
     */
    protected function validate_courseid($data) {
        global $DB;
        if (!$DB->record_exists('course', array('id' => $data))) {
            return new lang_string('invalidcourseid', 'error');
        }
        return true;
    }

    /**
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context_course::instance($this->get_courseid());
    }
}
