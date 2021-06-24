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
 * Contains class used to prepare a verification result for display.
 *
 * @package   mod_customcert
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;

/**
 * Class to prepare a verification result for display.
 *
 * @package   mod_customcert
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_certificate_result implements templatable, renderable {

    /**
     * @var string The URL to the user's profile.
     */
    public $userprofileurl;

    /**
     * @var string The user's fullname.
     */
    public $userfullname;

    /**
     * @var string The URL to the course page.
     */
    public $courseurl;

    /**
     * @var string The course's fullname.
     */
    public $coursefullname;

    /**
     * @var string The certificate's name.
     */
    public $certificatename;

    /**
     * Constructor.
     *
     * @param \stdClass $result
     */
    public function __construct($result) {
        $cm = get_coursemodule_from_instance('customcert', $result->certificateid);
        $context = \context_module::instance($cm->id);

        $this->userprofileurl = new \moodle_url('/user/view.php', array('id' => $result->userid,
            'course' => $result->courseid));
        $this->userfullname = fullname($result);
        $this->courseurl = new \moodle_url('/course/view.php', array('id' => $result->courseid));
        $this->coursefullname = format_string($result->coursefullname, true, ['context' => $context]);
        $this->certificatename = format_string($result->certificatename, true, ['context' => $context]);
    }

    /**
     * Function to export the renderer data in a format that is suitable for a mustache template.
     *
     * @param \renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return \stdClass|array
     */
    public function export_for_template(\renderer_base $output) {
        $result = new \stdClass();
        $result->userprofileurl = $this->userprofileurl;
        $result->userfullname = $this->userfullname;
        $result->coursefullname = $this->coursefullname;
        $result->courseurl = $this->courseurl;
        $result->certificatename = $this->certificatename;

        return $result;
    }
}
