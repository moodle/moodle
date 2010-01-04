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
 * This file defines an mform to edit accumulative grading strategy forms.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))).'/lib.php');   // module library
require_once(dirname(dirname(__FILE__)).'/gradingform.php');    // parent class definition


/**
 * Class for editing accumulative grading strategy forms.
 *
 * @uses moodleform
 */
class workshop_edit_accumulative_strategy_form extends workshop_edit_strategy_form {

    /**
     * Define the elements to be displayed at the form 
     *
     * Called by the parent::definition()
     * 
     * @access protected
     * @return void
     */
    protected function definition_inner(&$mform) {

        $gradeoptions = array(20 => 20, 10 => 10, 5 => 5);
        $weights = workshop_get_dimension_weights();

        $repeated = array();
        $repeated[] =& $mform->createElement('hidden', 'dimensionid', 0);
        $repeated[] =& $mform->createElement('header', 'dimension', 
                                                get_string('dimensionnumberaccumulative', 'workshop', '{no}'));
        $repeated[] =& $mform->createElement('htmleditor', 'description',
                                                get_string('dimensiondescription', 'workshop'), array());
        $repeated[] =& $mform->createElement('select', 'grade', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('select', 'weight', get_string('dimensionweight', 'workshop'), $weights);
        
        $repeatedoptions = array();
        $repeatedoptions['description']['type'] = PARAM_CLEANHTML;
        $repeatedoptions['description']['helpbutton'] = array('dimensiondescription', 
                                                            get_string('dimensiondescription', 'workshop'), 'workshop');
        $repeatedoptions['grade']['default'] = 10;
        $repeatedoptions['weight']['default'] = 1;

        $numofdimensionstoadd   = 2;
        $numofinitialdimensions = 3;
        $numofdisplaydimensions = max($this->nocurrentdims + $numofdimensionstoadd, $numofinitialdimensions);
        $this->repeat_elements($repeated, $numofdisplaydimensions,  $repeatedoptions, 'numofdimensions', 'adddimensions', $numofdimensionstoadd, get_string('addmoredimensionsaccumulative', 'workshop', $numofdimensionstoadd));
    }


}
