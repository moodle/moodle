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
 * Moodle form for selecting users.
 *
 * @package   tool_mergeusers
 * @copyright Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @copyright John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\output;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use html_writer;
use moodleform;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Define form snippet for getting the user.ids of the two users to merge.
 *
 * @package   tool_mergeusers
 * @copyright Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @copyright John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select_user_form extends moodleform {
    /** @var user_select_table Table to select users. */
    protected user_select_table $ust;

    /**
     * Builds the form.
     *
     * @param user_select_table|null $ust
     */
    public function __construct(?user_select_table $ust = null) {
        // Just before parent's constructor.
        $this->ust = $ust;
        parent::__construct();
    }

    /**
     * Form definition.
     *
     * @throws coding_exception
     */
    public function definition(): void {
        $mform =& $this->_form;

        $mform->addElement('header', 'selectusers', get_string('userselecttable_legend', 'tool_mergeusers'));

        // Add the table content.
        $mform->addElement('static', 'selectuserslist', '', html_writer::table($this->ust));

        // Provide all necessary hidden elements.
        $mform->addElement('hidden', 'option', 'saveselection');
        $mform->setType('option', PARAM_RAW);
        $mform->addElement('hidden', 'selectedolduser', '');
        $mform->setType('selectedolduser', PARAM_RAW);
        $mform->addElement('hidden', 'selectednewuser', '');
        $mform->setType('selectednewuser', PARAM_RAW);

        $this->add_action_buttons(false, get_string('saveselection_submit', 'tool_mergeusers'));
    }
}
