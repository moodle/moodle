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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Tim Hunt, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Inlcude the requisite helpers functionality.
// require_once('classes/helpers.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/blocklib.php');

class pu_validates_form extends moodleform {
    /**
     * The block.
     * @var moodle_block
     */
     public $block;

    /**
     * The page.
     * @var moodle_page
     */
    public $page;

    /**
     * The main constructor.
     * @param array $actionurl, $block, $page
     */
    function __construct($actionurl, $invalids, $page) {
        global $CFG;
        $this->page = $page;
        $this->invalids = $invalids;
        parent::__construct($actionurl);
    }

    /**
     * The main form definition.
     */
    function definition() {
        $mform =& $this->_form;

        // Add the header.
        $mform->addElement('header', 'overidesheader', get_string('manage_invalids', 'block_pu'));

        // Add a description.
        $mform->addElement('static', 'invalidshelp', get_string('manage_invalids_help', 'block_pu'));

        // Set up the options for the form.
        $opts0 = get_string('opts0', 'block_pu');
        $opts1 = get_string('opts1', 'block_pu');
        $opts2 = get_string('opts2', 'block_pu');
        $opts = array(0 => $opts0, 1 => $opts1, 2 => $opts2);

        // Loop through the list of guildcourses.
        foreach ($this->invalids as $invalid) {

            // Set up the string.
            $icstring = $invalid->pcmid != null ? get_string('invalid_code', 'block_pu', $invalid) : $invalid->accesscode;

            // Add a starting div to wrap these forms.
            $mform->addElement('html', '<div class="pu_course">');

            // Add the course/setting with null invalids.
            $mform->addElement('select', 'pcid_' . $invalid->pcid . '_pcmid_' . $invalid->pcmid, $icstring, $opts);
            $mform->setType('pcid_' . $invalid->pcid, PARAM_TEXT);

            // Add the closing div.
            $mform->addElement('html', '</div>');
        }

        // Add the action buttons.
        $this->add_action_buttons();
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            return (object)((array)$data);
        }
        return $data;
    }
}
