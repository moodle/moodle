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

        $workshopconfig     = get_config('workshop');
        $norepeats          = $this->_customdata['norepeats'];          // number of dimensions to display
        $descriptionopts    = $this->_customdata['descriptionopts'];    // wysiwyg fields options
        $current            = $this->_customdata['current'];            // current data to be set

        $mform->addElement('hidden', 'norepeats', $norepeats);
        // value not to be overridden by submitted value
        $mform->setConstants(array('norepeats' => $norepeats));

        $weights = workshop_get_dimension_weights();

        for ($i = 0; $i < $norepeats; $i++) {
            $mform->addElement('header', 'dimension'.$i, get_string('dimensionnumbernoerrors', 'workshop', $i+1));
            $mform->addElement('hidden', 'dimensionid__idx_'.$i);   // the id in workshop_forms
            $mform->addElement('editor', 'description__idx_'.$i.'_editor', get_string('dimensiondescription', 'workshop'),
                                    '', $descriptionopts);
            $mform->addElement('text', 'grade0__idx_'.$i, get_string('noerrorsgrade0', 'workshop'), array('size'=>'15'));
            $mform->setDefault('grade0__idx_'.$i, $workshopconfig->noerrorsgrade0);
            $mform->setType('grade0__idx_'.$i, PARAM_TEXT);
            $mform->addElement('text', 'grade1__idx_'.$i, get_string('noerrorsgrade1', 'workshop'), array('size'=>'15'));
            $mform->setDefault('grade1__idx_'.$i, $workshopconfig->noerrorsgrade1);
            $mform->setType('grade1__idx_'.$i, PARAM_TEXT);
            $mform->addElement('select', 'weight__idx_'.$i, get_string('dimensionweight', 'workshop'), $weights);
            $mform->setDefault('weight__idx_'.$i, 1);
        }

        $mform->addElement('header', 'mappingheader', get_string('noerrorsgrademapping', 'workshop'));
        $mform->addElement('static', 'mappinginfo', get_string('noerrorsmaperror', 'workshop'),
                                                            get_string('noerrorsmapgrade', 'workshop'));
        $percents = array();
        $percents[''] = '';
        for ($i = 100; $i >= 0; $i--) {
            $percents[$i] = get_string('percents', 'workshop', $i);
        }
        $mform->addElement('static', 'mappingzero', 0, get_string('percents', 'workshop', 100));
        $mform->addElement('hidden', 'map__idx_0', 100);
        for ($i = 1; $i <= $norepeats; $i++) {
            $selects = array();
            $selects[] = $mform->createElement('select', 'map__idx_'.$i, $i, $percents);
            $selects[] = $mform->createElement('static', 'mapdefault__idx_'.$i, '',
                                        get_string('percents', 'workshop', floor(100 - $i * 100 / $norepeats)));
            $mform->addGroup($selects, 'grademapping'.$i, $i, array(' '), false);
            $mform->setDefault('map__idx_'.$i, '');
        }

        $mform->registerNoSubmitButton('noadddims');
        $mform->addElement('submit', 'noadddims', get_string('addmoredimensionsaccumulative', 'workshop',
                                                                    WORKSHOP_STRATEGY_ADDDIMS));
        $mform->closeHeaderBefore('noadddims');
        $this->set_data($current);

    }

}
