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
 * Form for resetting the status
 *
 * @package   tool_crawler
 * @author    Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\form;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use moodleform;

require_once($CFG->libdir . '/formslib.php');

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Form for resetting the status
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for resetting the status
 *
 * @copyright Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $OUTPUT;
        $mform    = $this->_form;

        $warningmsg = get_string('resetprogress_warning', 'tool_crawler');

        $html  = html_writer::start_div('warning');
        $html .= $OUTPUT->notification($warningmsg, 'warning');
        $html .= html_writer::end_div();

        $mform->addElement('html', $html);
        $this->add_action_buttons(true, get_string('resetprogress_warning_button', 'tool_crawler'));

    }
}
