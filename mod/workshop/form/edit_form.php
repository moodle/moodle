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
 * This file defines a base class for all grading strategy editing forms.
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php'); // parent class definition

/**
 * Base class for editing all the strategy grading forms.
 *
 * This defines the common fields that all strategy grading forms need.
 * Strategies should define their own  class that inherits from this one, and
 * implements the definition_inner() method.
 *
 * @uses moodleform
 */
class workshop_edit_strategy_form extends moodleform {

    /** strategy logic instance that this class is editor of */
    protected $strategy;

    /**
     * Add the fields that are common for all grading strategies.
     *
     * If the strategy does not support all these fields, then you can override
     * this method and remove the ones you don't want with
     * $mform->removeElement().
     * Stretegy subclassess should define their own fields in definition_inner()
     *
     * @return void
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $this->workshop = $this->_customdata['workshop'];
        $this->strategy = $this->_customdata['strategy'];

        $mform->addElement('hidden', 'workshopid', $this->workshop->id);        // workshopid
        $mform->setType('workshopid', PARAM_INT);

        $mform->addElement('hidden', 'strategy', $this->workshop->strategy);    // strategy name
        $mform->setType('strategy', PARAM_PLUGIN);

        $this->definition_inner($mform);

        // todo - tags support
        //if (!empty($CFG->usetags)) {
        //    $mform->addElement('header', 'tagsheader', get_string('tags'));
        //    $mform->addElement('tags', 'tags', get_string('tags'));
        //}

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'saveandcontinue', get_string('saveandcontinue', 'workshop'));
        $buttonarray[] = $mform->createElement('submit', 'saveandpreview', get_string('saveandpreview', 'workshop'));
        $buttonarray[] = $mform->createElement('submit', 'saveandclose', get_string('saveandclose', 'workshop'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Add any strategy specific form fields.
     *
     * @param stdClass $mform the form being built.
     */
    protected function definition_inner(&$mform) {
        // By default, do nothing.
    }

}
