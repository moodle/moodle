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
 * Contains renderer class.
 *
 * @package   mod_customcert
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Renderer class.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders the verify certificate results.
     *
     * Defer to template.
     *
     * @param \mod_customcert\output\verify_certificate_results $page
     * @return string html for the page
     */
    public function render_verify_certificate_results(verify_certificate_results $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_customcert/verify_certificate_results', $data);
    }

    /**
     * Formats the email used to send the certificate by the email_certificate_task.
     *
     * @param email_certificate $certificate The certificate to email
     * @return string
     */
    public function render_email_certificate(email_certificate $certificate) {
        $data = $certificate->export_for_template($this);
        return $this->render_from_template('mod_customcert/' . $this->get_template_name(), $data);
    }
}
