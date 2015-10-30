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
 * This file defines an mform to edit rubric grading strategy forms.
 *
 * @package    workshopform_rubric
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)).'/edit_form.php');    // parent class definition

/**
 * Class for editing rubric grading strategy forms.
 *
 * @uses moodleform
 */
class workshop_edit_rubric_strategy_form extends workshop_edit_strategy_form {

    const MINLEVELS = 4;
    const ADDLEVELS = 2;

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @return void
     */
    protected function definition_inner(&$mform) {

        $norepeats          = $this->_customdata['norepeats'];          // number of dimensions to display
        $descriptionopts    = $this->_customdata['descriptionopts'];    // wysiwyg fields options
        $current            = $this->_customdata['current'];            // current data to be set

        $mform->addElement('hidden', 'norepeats', $norepeats);
        $mform->setType('norepeats', PARAM_INT);
        // value not to be overridden by submitted value
        $mform->setConstants(array('norepeats' => $norepeats));

        $levelgrades = array();
        for ($i = 100; $i >= 0; $i--) {
            $levelgrades[$i] = $i;
        }

        for ($i = 0; $i < $norepeats; $i++) {
            $mform->addElement('header', 'dimension'.$i, get_string('dimensionnumber', 'workshopform_rubric', $i+1));
            $mform->addElement('hidden', 'dimensionid__idx_'.$i);
            $mform->setType('dimensionid__idx_'.$i, PARAM_INT);
            $mform->addElement('editor', 'description__idx_'.$i.'_editor',
                                get_string('dimensiondescription', 'workshopform_rubric'), '', $descriptionopts);
            if (isset($current->{'numoflevels__idx_' . $i})) {
                $numoflevels = max($current->{'numoflevels__idx_' . $i} + self::ADDLEVELS, self::MINLEVELS);
            } else {
                $numoflevels = self::MINLEVELS;
            }
            $prevlevel = -1;
            for ($j = 0; $j < $numoflevels; $j++) {
                $mform->addElement('hidden', 'levelid__idx_' . $i . '__idy_' . $j);
                $mform->setType('levelid__idx_' . $i . '__idy_' . $j, PARAM_INT);
                $levelgrp = array();
                $levelgrp[] = $mform->createElement('select', 'grade__idx_'.$i.'__idy_'.$j,'', $levelgrades);
                $levelgrp[] = $mform->createElement('textarea', 'definition__idx_'.$i.'__idy_'.$j, '',  array('cols' => 60, 'rows' => 3));
                $mform->addGroup($levelgrp, 'level__idx_'.$i.'__idy_'.$j, get_string('levelgroup', 'workshopform_rubric'), array(' '), false);
                $mform->setDefault('grade__idx_'.$i.'__idy_'.$j, $prevlevel + 1);
                if (isset($current->{'grade__idx_'.$i.'__idy_'.$j})) {
                    $prevlevel = $current->{'grade__idx_'.$i.'__idy_'.$j};
                } else {
                    $prevlevel++;
                }
            }
        }

        $mform->registerNoSubmitButton('adddims');
        $mform->addElement('submit', 'adddims', get_string('addmoredimensions', 'workshopform_rubric',
                workshop_rubric_strategy::ADDDIMS));
        $mform->closeHeaderBefore('adddims');

        $mform->addElement('header', 'configheader', get_string('configuration', 'workshopform_rubric'));
        $layoutgrp = array();
        $layoutgrp[] = $mform->createElement('radio', 'config_layout', '',
                get_string('layoutlist', 'workshopform_rubric'), 'list');
        $layoutgrp[] = $mform->createElement('radio', 'config_layout', '',
                get_string('layoutgrid', 'workshopform_rubric'), 'grid');
        $mform->addGroup($layoutgrp, 'layoutgrp', get_string('layout', 'workshopform_rubric'), array('<br />'), false);
        $mform->setDefault('config_layout', 'list');
        $this->set_data($current);
    }
}
