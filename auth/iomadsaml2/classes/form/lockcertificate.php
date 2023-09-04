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
 * Form for locking the certificates.
 *
 * @package   auth_iomadsaml2
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2\form;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use moodleform;

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for locking the certificates.
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lockcertificate extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $OUTPUT;
        $mform = $this->_form;
        $buttonarray = [];

        $certslockwarning  = html_writer::start_div('warning');
        if (get_config('auth_iomadsaml2', 'certs_locked') == false) {
            // The certs are unlocked.
            $certslockwarning .= $OUTPUT->notification(get_string('certificatelock_warning', 'auth_iomadsaml2'), 'warning');
            $certslockwarning .= html_writer::end_div();
            $mform->addElement('html', $certslockwarning);
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('certificatelock', 'auth_iomadsaml2'));
        } else {
            // The certs are locked.
            $certslockwarning .= $OUTPUT->notification(get_string('certificatelock_lockedmessage', 'auth_iomadsaml2'), 'warning');
            $certslockwarning .= html_writer::end_div();
            $mform->addElement('html', $certslockwarning);
            $buttonarray[] = &$mform->createElement('submit', 'unlockcertsbutton',
                    get_string('certificatelock_unlock', 'auth_iomadsaml2'));
        }

        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}
