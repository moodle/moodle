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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php'); /// forms library

/**
 * Define form snippet for getting the userids of the two users to merge
 */
class selectuserform extends moodleform {

    /** @var UserSelectTable Table to select users. */
    protected $ust;

    public function __construct(UserSelectTable $ust = null)
    {
        //just before parent's construct
        $this->ust = $ust;
        parent::__construct();


    }

    /**
     * Form definition
     *
     * @uses $CFG
     */
    public function definition() {

        $mform =& $this->_form;

        // header
        $mform->addElement('header', 'selectusers', get_string('userselecttable_legend', 'tool_iomadmerge'));

        // table content
        $mform->addElement('static', 'selectuserslist', '', html_writer::table($this->ust));

        // hidden elements
        $mform->addElement('hidden', 'option', 'saveselection');
        $mform->setType('option', PARAM_RAW);
        $mform->addElement('hidden', 'selectedolduser', '');
        $mform->setType('selectedolduser', PARAM_RAW);
        $mform->addElement('hidden', 'selectednewuser', '');
        $mform->setType('selectednewuser', PARAM_RAW);

        // buttons
        $this->add_action_buttons(false, get_string('saveselection_submit', 'tool_iomadmerge'));
    }
}
