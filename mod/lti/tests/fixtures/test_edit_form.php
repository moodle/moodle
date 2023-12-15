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
 * Unit tests for mod_lti edit_form
 *
 * @package    mod_lti
 * @copyright  2023 Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/edit_form.php');

/**
 * Testing fixture.
 *
 * @package    mod_lti
 * @copyright  2023 Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class test_edit_form extends \mod_lti_edit_types_form {

    /**
     * Expose the internal moodleform's MoodleQuickForm
     *
     * @return MoodleQuickForm
     */
    public function get_quick_form() {
        return $this->_form;
    }
}
