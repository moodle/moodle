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
 * Bulk course upload step 2.
 *
 * @package    tool_bulkenrol
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

/**
 * Specify course upload details.
 *
 * @package    tool_bulkenrol
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_bulkenrol_step2_form extends tool_bulkenrol_base_form {

    /**
     * The standard form definiton.
     * @return void.
     */
    public function definition () {
        global $CFG;

        $mform   = $this->_form;
        $data    = $this->_customdata['data'];
        $courseconfig = get_config('moodlecourse');

        // Import options.
        $this->add_import_options();

    
        // Hidden fields.
        $mform->addElement('hidden', 'importid');
        $mform->setType('importid', PARAM_INT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(true, get_string('bulkenrols', 'tool_bulkenrol'));

        $this->set_data($data);
    }

    /**
     * Add actopm buttons.
     *
     * @param bool $cancel whether to show cancel button, default true
     * @param string $submitlabel label for submit button, defaults to get_string('savechanges')
     * @return void
     */
    public function add_action_buttons($cancel = true, $submitlabel = null) {
        $mform =& $this->_form;
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'showpreview', get_string('preview', 'tool_bulkenrol'));
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

}
