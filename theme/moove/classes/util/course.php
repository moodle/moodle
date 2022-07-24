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
 * Course class utility class
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\util;

use moodle_url;
use core_course_list_element;
use coursecat_helper;
use core_course_category;

/**
 * Course class utility class
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course {
    /**
     * @var \stdClass $course The course object.
     */
    protected $course;

    /**
     * Class constructor
     *
     * @param core_course_list_element $course
     *
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Returns the first course's summary image url
     *
     * @return string
     */
    public function get_summary_image() {
        global $CFG, $OUTPUT;

        foreach ($this->course->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$file->is_valid_image());

                return $url->out();
            }
        }

        return $OUTPUT->get_generated_image_for_id($this->course->id);
    }

    /**
     * Returns HTML to display course contacts.
     *
     * @return array
     */
    public function get_course_contacts() {
        $theme = \theme_config::load('moove');

        $contacts = [];
        if ($this->course->has_course_contacts() && !($theme->settings->disableteacherspic)) {
            $instructors = $this->course->get_course_contacts();

            foreach ($instructors as $instructor) {
                $user = $instructor['user'];
                $userutil = new user($user->id);

                $contacts[] = [
                    'id' => $user->id,
                    'fullname' => fullname($user),
                    'userpicture' => $userutil->get_user_picture(),
                    'role' => $instructor['role']->displayname
                ];
            }
        }

        return $contacts;
    }

    /**
     * Returns HTML to display course category name.
     *
     * @return string
     *
     * @throws \moodle_exception
     */
    public function get_category(): string {
        $cat = core_course_category::get($this->course->category, IGNORE_MISSING);

        if (!$cat) {
            return '';
        }

        return $cat->get_formatted_name();
    }

    /**
     * Returns course summary.
     *
     * @param coursecat_helper $chelper
     */
    public function get_summary(coursecat_helper $chelper): string {
        if ($this->course->has_summary()) {
            return $chelper->get_course_formatted_summary($this->course,
                ['overflowdiv' => true, 'noclean' => true, 'para' => false]
            );
        }

        return false;
    }

    /**
     * Returns course custom fields.
     *
     * @return string
     */
    public function get_custom_fields(): string {
        if ($this->course->has_custom_fields()) {
            $handler = \core_course\customfield\course_handler::create();

            return $handler->display_custom_fields_data($this->course->get_custom_fields());
        }

        return '';
    }

    /**
     * Returns HTML to display course enrolment icons.
     *
     * @return array
     */
    public function get_enrolment_icons(): array {
        if ($icons = enrol_get_course_info_icons($this->course)) {
            return $icons;
        }

        return [];
    }

    /**
     * Get the user progress in the course.
     *
     * @param null $userid
     *
     * @return mixed
     */
    public function get_progress($userid = null) {
        return \core_completion\progress::get_course_progress_percentage($this->course, $userid);
    }
}
