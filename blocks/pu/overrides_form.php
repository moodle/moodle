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

class pu_overrides_form extends moodleform {
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
    function __construct($actionurl, $guildcourses, $page) {
        global $CFG;
        $this->sitedefaults = $CFG->block_pu_defaultcodes;
        $this->page = $page;
        $this->guildcourses = $guildcourses;
        parent::__construct($actionurl);
    }

    /**
     * The main form definition.
     */
    function definition() {
        $mform =& $this->_form;

        // Add the header.
        $mform->addElement('header', 'overidesheader', get_string('manage_overrides', 'block_pu'));

        // Add a description.
        $mform->addElement('static', 'overrideshelp', get_string('manage_overrides_help2', 'block_pu', array('percourse' => $this->sitedefaults)));

        // Loop through the list of guildcourses.
        foreach ($this->guildcourses as $guildcourse) {

            // Build the course object from the courseid.
            $course = get_course($guildcourse->course);

            // Set the override.
            $override = block_pu_helpers::pu_override($course->id);

            // Add a starting div to wrap these forms.
            $mform->addElement('html', '<div class="pu_course">');

            // Add the course/setting with null overrides.
            $mform->addElement('text', 'codecount_' . $override->course, '<strong>' . $course->fullname . "</strong><br>" . get_string('override_numcodes', 'block_pu'), array('class' => 'codecount'));
            $mform->setType('codecount_' . $override->course, PARAM_RAW);

            // If we have an override value, set the default override value based on it.
            if ($override->overridecode) {
                $mform->setDefault('codecount_'. $override->course, $override->codecount);
            }

            // Add the course/setting with null invalids.
            $mform->addElement('text', 'invalidcount_' . $override->course, get_string('override_numinvalid', 'block_pu'), array('class' => 'invalidcount'));
            $mform->setType('invalidcount_' . $override->course, PARAM_RAW);

            // If we have an invalid override value, set the defualt invalid value based on it.
            if ($override->overrideinvalid) {
                $mform->setDefault('invalidcount_'. $override->course, $override->invalidcount);
            }

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
