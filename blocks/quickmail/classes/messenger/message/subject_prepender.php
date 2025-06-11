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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\messenger\message;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_config;
use block_quickmail_string;

class subject_prepender {

    public $subject;

    /**
     * Construct the message subject prepender
     *
     * @param string  $subject   the message subject
     */
    public function __construct($subject) {
        $this->subject = $subject;
    }

    /**
     * Returns a formatted subject line for the given course and raw subject,
     * decorating with any prependages if necessary
     *
     * @param  object  $course
     * @param  string  $subject
     * @return string
     */
    public static function format_course_subject($course, $subject) {
        $prepender = new self($subject);

        return $prepender->get_course_formatted($course);
    }

    /**
     * Returns a subject line formatted for a send receipt email
     *
     * @param  string $subject
     * @return string
     */
    public static function format_for_receipt_subject($subject) {
        return block_quickmail_string::get('send_receipt_subject_addendage') . ': ' . $subject;
    }

    /**
     * Returns a subject prependage for the given course based on configuration
     *
     * @param  object  $course
     * @return string
     */
    public function get_course_formatted($course) {
        // Get course config.
        $setting = $this->get_course_config_setting($course);

        switch ($setting) {
            case 'idnumber':
                return $this->get_prepended_with($course->idnumber);
                break;

            case 'shortname':
                return $this->get_prepended_with($course->shortname);
                break;

            case 'fullname':
                return $this->get_prepended_with(format_string($course->fullname));
                break;

            default:
                return $this->subject;
                break;
        }
    }

    /**
     * Returns the given course's "prepend class" setting
     *
     * @param  object $course
     * @return string
     */
    private function get_course_config_setting($course) {
        return block_quickmail_config::get('prepend_class', $course);
    }

    /**
     * Returns the subject string prepended with course appendage string
     *
     * @param  string $value
     * @return string
     */
    private function get_prepended_with($value) {
        return $this->get_left_delimiter() . $value . $this->get_right_delimiter() . ' ' . $this->subject;
    }

    /**
     * Returns the delimiter to be rendered on the left side of the course appendage
     *
     * @return string
     */
    private function get_left_delimiter() {
        return '[';
    }

    /**
     * Returns the delimiter to be rendered on the right side of the course appendage
     *
     * @return string
     */
    private function get_right_delimiter() {
        return ']';
    }

}
