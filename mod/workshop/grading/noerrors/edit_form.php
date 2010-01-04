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
 * This file defines an mform to edit "Number of errors" grading strategy forms.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))).'/lib.php');   // module library
require_once(dirname(dirname(__FILE__)).'/edit_form.php');    // parent class definition

/**
 * Class for editing "Number of errors" grading strategy forms.
 *
 * @uses moodleform
 */
class workshop_edit_noerrors_strategy_form extends workshop_edit_strategy_form {

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @return void
     */
    protected function definition_inner(&$mform) {

        $workshopconfig = get_config('workshop');
        $weights = workshop_get_dimension_weights();

        $repeated = array();
        $repeated[] =& $mform->createElement('hidden', 'dimensionid', 0);
        $repeated[] =& $mform->createElement('header', 'dimension',
                                                get_string('dimensionnumbernoerrors', 'workshop', '{no}'));
        $repeated[] =& $mform->createElement('htmleditor', 'description',
                                                get_string('dimensiondescription', 'workshop'), array());
        $repeated[] =& $mform->createElement('text', 'grade0', get_string('noerrorsgrade0', 'workshop'), array('size'=>'15'));
        $repeated[] =& $mform->createElement('text', 'grade1', get_string('noerrorsgrade1', 'workshop'), array('size'=>'15'));
        $repeated[] =& $mform->createElement('select', 'weight', get_string('dimensionweight', 'workshop'), $weights);

        $repeatedoptions = array();
        $repeatedoptions['description']['type'] = PARAM_CLEANHTML;
        $repeatedoptions['description']['helpbutton'] = array('dimensiondescription',
                                                            get_string('dimensiondescription', 'workshop'), 'workshop');
        $repeatedoptions['grade0']['type'] = PARAM_TEXT;
        $repeatedoptions['grade0']['default'] = $workshopconfig->noerrorsgrade0;
        $repeatedoptions['grade1']['type'] = PARAM_TEXT;
        $repeatedoptions['grade1']['default'] = $workshopconfig->noerrorsgrade1;
        $repeatedoptions['weight']['default'] = 1;

        $numofdimensionstoadd   = 2;
        $numofinitialdimensions = 3;
        $numofdisplaydimensions = max($this->strategy->get_number_of_dimensions() + $numofdimensionstoadd,
                                                                                            $numofinitialdimensions);
        $numofdisplaydimensions = $this->repeat_elements($repeated, $numofdisplaydimensions,  $repeatedoptions,
                                                    'numofdimensions', 'adddimensions', $numofdimensionstoadd,
                                                    get_string('addmoredimensionsnoerrors', 'workshop', $numofdimensionstoadd));
        $mform->addElement('header', 'mappingheader', get_string('noerrorsgrademapping', 'workshop'));
        $mform->addElement('static', 'mappinginfo', get_string('noerrorsmaperror', 'workshop'),
                                                            get_string('noerrorsmapgrade', 'workshop'));
        $percents = array();
        $percents[''] = '';
        for ($i = 100; $i >= 0; $i--) {
            $percents[$i] = get_string('percents', 'workshop', $i);
        }
        $mform->addElement('static', 'mappingzero', 0, get_string('percents', 'workshop', 100));
        $mform->addElement('hidden', 'map[0]', 100);
        for ($i = 1; $i <= $numofdisplaydimensions; $i++) {
            $selects = array();
            $selects[] =& $mform->createElement('select', "map[$i]", $i, $percents);
            $selects[] =& $mform->createElement('static', "mapdefault[$i]", '',
                                        get_string('percents', 'workshop', floor(100 - $i * 100 / $numofdisplaydimensions)));
            $mform->addGroup($selects, "grademapping$i", $i, array(' '), false);
            $mform->setDefault("map[$i]", '');
        }

    }

}
