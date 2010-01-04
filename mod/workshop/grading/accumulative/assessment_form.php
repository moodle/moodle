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
 * This file defines an mform to assess a submission by accumulative grading strategy
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//require_once(dirname(dirname(dirname(__FILE__))).'/lib.php');   // module library
require_once(dirname(dirname(__FILE__)).'/assessment_form.php');    // parent class definition

/**
 * Class representing a form for assessing submissions by accumulative grading strategy
 *
 * @uses moodleform
 */
class workshop_accumulative_assessment_form extends workshop_assessment_form {

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @access protected
     * @return void
     */
    protected function definition_inner(&$mform) {

        for ($i = 0; $i < $this->strategy->get_number_of_dimensions(); $i++) {

            // dimension header
            $mform->addElement('header', "dimensionhdr[$i]",
                                    str_replace('{no}', $i+1, get_string('dimensionnumberaccumulative', 'workshop', '{no}')));

            // dimension description
            $desc = '<div id="id_dim_'.$this->fields["dimensionid[$i]"] . '_desc" class="fitem description accumulative">' . "\n";
            $desc .= format_text($this->fields["description[$i]"], $this->fields["descriptionformat[$i]"]);
            $desc .= "\n</div>";
            $mform->addElement('html', $desc);

            // grade for this aspect
            $label = 'Grade'; // todo
            $options = array(10,9,8,7,6,5,4,3,2,1,0); // todo
            $mform->addElement('select', "grade[$i]", $label, $options);

            // comment
            $label = 'Comment'; //todo
            $mform->addElement('htmleditor', "comment[$i]", $label, array());

        }

    }

}
