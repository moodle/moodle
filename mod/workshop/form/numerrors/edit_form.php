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
 * @package    workshopform_numerrors
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../lib.php');   // module library
require_once(__DIR__ . '/../edit_form.php');    // parent class definition

/**
 * Class for editing "Number of errors" grading strategy forms.
 *
 * @uses moodleform
 */
class workshop_edit_numerrors_strategy_form extends workshop_edit_strategy_form {

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @return void
     */
    protected function definition_inner(&$mform) {

        $plugindefaults     = get_config('workshopform_numerrors');
        $nodimensions       = $this->_customdata['nodimensions'];       // number of currently filled dimensions
        $norepeats          = $this->_customdata['norepeats'];          // number of dimensions to display
        $descriptionopts    = $this->_customdata['descriptionopts'];    // wysiwyg fields options
        $current            = $this->_customdata['current'];            // current data to be set

        $mform->addElement('hidden', 'norepeats', $norepeats);
        $mform->setType('norepeats', PARAM_INT);
        // value not to be overridden by submitted value
        $mform->setConstants(array('norepeats' => $norepeats));

        for ($i = 0; $i < $norepeats; $i++) {
            $mform->addElement('header', 'dimension'.$i, get_string('dimensionnumber', 'workshopform_numerrors', $i+1));
            $mform->addElement('hidden', 'dimensionid__idx_'.$i);   // the id in workshop_forms
            $mform->setType('dimensionid__idx_'.$i, PARAM_INT);
            $mform->addElement('editor', 'description__idx_'.$i.'_editor',
                    get_string('dimensiondescription', 'workshopform_numerrors'), '', $descriptionopts);
            $mform->addElement('text', 'grade0__idx_'.$i, get_string('grade0', 'workshopform_numerrors'), array('size'=>'15'));
            $mform->setDefault('grade0__idx_'.$i, $plugindefaults->grade0);
            $mform->setType('grade0__idx_'.$i, PARAM_TEXT);
            $mform->addElement('text', 'grade1__idx_'.$i, get_string('grade1', 'workshopform_numerrors'), array('size'=>'15'));
            $mform->setDefault('grade1__idx_'.$i, $plugindefaults->grade1);
            $mform->setType('grade1__idx_'.$i, PARAM_TEXT);
            $mform->addElement('select', 'weight__idx_'.$i,
                    get_string('dimensionweight', 'workshopform_numerrors'), workshop::available_dimension_weights_list());
            $mform->setDefault('weight__idx_'.$i, 1);
        }

        $mform->addElement('header', 'mappingheader', get_string('grademapping', 'workshopform_numerrors'));
        $mform->addElement('static', 'mappinginfo', get_string('maperror', 'workshopform_numerrors'),
                                                            get_string('mapgrade', 'workshopform_numerrors'));

        // get the total weight of all items == maximum weighted number of errors
        $totalweight = 0;
        for ($i = 0; $i < $norepeats; $i++) {
            if (!empty($current->{'weight__idx_'.$i})) {
                $totalweight += $current->{'weight__idx_'.$i};
            }
        }
        $totalweight = max($totalweight, $nodimensions);

        $percents = array();
        $percents[''] = '';
        for ($i = 100; $i >= 0; $i--) {
            $percents[$i] = get_string('percents', 'moodle', $i);
        }
        $mform->addElement('static', 'mappingzero', 0, get_string('percents', 'moodle', 100));
        for ($i = 1; $i <= $totalweight; $i++) {
            $selects = array();
            $selects[] = $mform->createElement('select', 'map__idx_'.$i, $i, $percents);
            $selects[] = $mform->createElement('static', 'mapdefault__idx_'.$i, '',
                                        get_string('percents', 'moodle', floor(100 - $i * 100 / $totalweight)));
            $mform->addGroup($selects, 'grademapping'.$i, $i, array(' '), false);
            $mform->setDefault('map__idx_'.$i, '');
        }

        $mform->registerNoSubmitButton('noadddims');
        $mform->addElement('submit', 'noadddims', get_string('addmoredimensions', 'workshopform_numerrors',
                workshop_numerrors_strategy::ADDDIMS));
        $mform->closeHeaderBefore('noadddims');
        $this->set_data($current);

    }

}
