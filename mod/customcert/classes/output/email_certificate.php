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
 * Email certificate renderable.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\output;

/**
 * Email certificate renderable.
 *
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_certificate implements \renderable, \templatable {

    /**
     * @var bool Are we emailing the student?
     */
    public $isstudent;

    /**
     * @var string The name of the user who owns the certificate.
     */
    public $userfullname;

    /**
     * @var string The course short name.
     */
    public $courseshortname;

    /**
     * @var string The course full name.
     */
    public $coursefullname;

    /**
     * @var int The certificate name.
     */
    public $certificatename;

    /**
     * @var int The course module id.
     */
    public $cmid;

    /**
     * Constructor.
     *
     * @param bool $isstudent Are we emailing the student?
     * @param string $userfullname The name of the user who owns the certificate.
     * @param string $courseshortname The short name of the course.
     * @param string $coursefullname The full name of the course.
     * @param string $certificatename The name of the certificate.
     * @param string $cmid The course module id.
     */
    public function __construct($isstudent, $userfullname, $courseshortname, $coursefullname, $certificatename, $cmid) {
        $this->isstudent = $isstudent;
        $this->userfullname = $userfullname;
        $this->courseshortname = $courseshortname;
        $this->coursefullname = $coursefullname;
        $this->certificatename = $certificatename;
        $this->cmid = $cmid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $renderer The render to be used for formatting the email
     * @return \stdClass The data ready for use in a mustache template
     */
    public function export_for_template(\renderer_base $renderer) {
        $data = new \stdClass();

        // Used for the body text.
        $info = new \stdClass();
        $info->userfullname = $this->userfullname;
        $info->certificatename = $this->certificatename;
        $info->courseshortname = $this->courseshortname;
        $info->coursefullname = $this->coursefullname;

        if ($this->isstudent) {
            $data->emailgreeting = get_string('emailstudentgreeting', 'customcert', $this->userfullname);
            $data->emailbody = get_string('emailstudentbody', 'customcert', $info);
            $data->emailbodyplaintext = get_string('emailstudentbodyplaintext', 'customcert', $info);
            $data->emailcertificatelink = new \moodle_url('/mod/customcert/view.php', ['id' => $this->cmid]);
            $data->emailcertificatelinktext = get_string('emailstudentcertificatelinktext', 'customcert');
        } else {
            $data->emailgreeting = get_string('emailnonstudentgreeting', 'customcert');
            $data->emailbody = get_string('emailnonstudentbody', 'customcert', $info);
            $data->emailbodyplaintext = get_string('emailnonstudentbodyplaintext', 'customcert', $info);
            $data->emailcertificatelink = new \moodle_url('/mod/customcert/view.php', ['id' => $this->cmid]);
            $data->emailcertificatelinktext = get_string('emailnonstudentcertificatelinktext', 'customcert');
        }

        return $data;
    }
}
