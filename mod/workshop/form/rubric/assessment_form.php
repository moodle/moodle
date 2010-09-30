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
 * This file defines mforms to assess a submission by rubric grading strategy
 *
 * Rubric can be displayed in two possible layouts - list or grid. This file defines
 * therefore defines two classes, respectively.
 *
 * @package    workshopform
 * @subpackage rubric
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)).'/assessment_form.php');    // parent class definition

/**
 * Base class representing a form for assessing submissions by rubric grading strategy
 */
abstract class workshop_rubric_assessment_form extends workshop_assessment_form {

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);
        for ($i = 0; isset($data['dimensionid__idx_'.$i]); $i++) {
            if (empty($data['chosenlevelid__idx_'.$i])) {
                $errors['chosenlevelid__idx_'.$i] = get_string('mustchooseone', 'workshopform_rubric'); // used in grid
                $errors['levelgrp__idx_'.$i] = get_string('mustchooseone', 'workshopform_rubric');      // used in list
            }
        }
        return $errors;
    }
}

/**
 * Class representing a form for assessing submissions by rubric grading strategy - list layout
 */
class workshop_rubric_list_assessment_form extends workshop_rubric_assessment_form {

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @return void
     */
    protected function definition_inner(&$mform) {
        $workshop   = $this->_customdata['workshop'];
        $fields     = $this->_customdata['fields'];
        $current    = $this->_customdata['current'];
        $nodims     = $this->_customdata['nodims'];     // number of assessment dimensions

        for ($i = 0; $i < $nodims; $i++) {
            // dimension header
            $dimtitle = get_string('dimensionnumber', 'workshopform_rubric', $i+1);
            $mform->addElement('header', 'dimensionhdr__idx_'.$i, $dimtitle);

            // dimension id
            $mform->addElement('hidden', 'dimensionid__idx_'.$i, $fields->{'dimensionid__idx_'.$i});
            $mform->setType('dimensionid__idx_'.$i, PARAM_INT);

            // grade id
            $mform->addElement('hidden', 'gradeid__idx_'.$i);   // value set by set_data() later
            $mform->setType('gradeid__idx_'.$i, PARAM_INT);

            // dimension description
            $desc = '<div id="id_dim_'.$fields->{'dimensionid__idx_'.$i}.'_desc" class="fitem description rubric">'."\n";
            $desc .= format_text($fields->{'description__idx_'.$i}, $fields->{'description__idx_'.$i.'format'});
            $desc .= "\n</div>";
            $mform->addElement('html', $desc);

            $numoflevels = $fields->{'numoflevels__idx_'.$i};
            $levelgrp   = array();
            for ($j = 0; $j < $numoflevels; $j++) {
                $levelid = $fields->{'levelid__idx_'.$i.'__idy_'.$j};
                $definition = $fields->{'definition__idx_'.$i.'__idy_'.$j};
                $definitionformat = $fields->{'definition__idx_'.$i.'__idy_'.$j.'format'};
                $levelgrp[] = $mform->createElement('radio', 'chosenlevelid__idx_'.$i, '',
                        format_text($definition, $definitionformat, null, $workshop->course->id), $levelid);
            }
            $mform->addGroup($levelgrp, 'levelgrp__idx_'.$i, '', "<br />\n", false);
        }
        $this->set_data($current);
    }
}

/**
 * Class representing a form for assessing submissions by rubric grading strategy - grid layout
 */
class workshop_rubric_grid_assessment_form extends workshop_rubric_assessment_form {

    /**
     * Define the elements to be displayed at the form
     *
     * Called by the parent::definition()
     *
     * @return void
     */
    protected function definition_inner(&$mform) {
        $workshop   = $this->_customdata['workshop'];
        $fields     = $this->_customdata['fields'];
        $current    = $this->_customdata['current'];
        $nodims     = $this->_customdata['nodims'];     // number of assessment dimensions

        // get the number of required grid columns
        $levelcounts = array();
        for ($i = 0; $i < $nodims; $i++) {
            if ($fields->{'numoflevels__idx_'.$i} > 0) {
                $levelcounts[] = $fields->{'numoflevels__idx_'.$i};
            }
        }
        $numofcolumns = array_reduce($levelcounts, 'workshop::lcm', 1);

        $mform->addElement('header', 'rubric-grid-wrapper', get_string('layoutgrid', 'workshopform_rubric'));

        $mform->addElement('html', '<table class="rubric-grid">' . "\n");
        $mform->addElement('html', '<th class="header">' . get_string('criteria', 'workshopform_rubric') . '</th>');
        $mform->addElement('html', '<th class="header" colspan="'.$numofcolumns.'">'.get_string('levels', 'workshopform_rubric').'</th>');

        for ($i = 0; $i < $nodims; $i++) {

            $mform->addElement('html', '<tr class="r'. $i % 2  .'"><td class="criterion">' . "\n");

            // dimension id
            $mform->addElement('hidden', 'dimensionid__idx_'.$i, $fields->{'dimensionid__idx_'.$i});
            $mform->setType('dimensionid__idx_'.$i, PARAM_INT);

            // given grade id
            $mform->addElement('hidden', 'gradeid__idx_'.$i);   // value set by set_data() later
            $mform->setType('gradeid__idx_'.$i, PARAM_INT);

            // dimension description
            $desc = format_text($fields->{'description__idx_'.$i}, $fields->{'description__idx_'.$i.'format'});
            $desc .= "</td>\n";
            $mform->addElement('html', $desc);

            $numoflevels = $fields->{'numoflevels__idx_'.$i};
            for ($j = 0; $j < $numoflevels; $j++) {
                $colspan = $numofcolumns / $numoflevels;
                $mform->addElement('html', '<td class="level c' . $j % 2  . '" colspan="' . $colspan . '">' . "\n");
                $levelid = $fields->{'levelid__idx_'.$i.'__idy_'.$j};
                $definition = $fields->{'definition__idx_'.$i.'__idy_'.$j};
                $definitionformat = $fields->{'definition__idx_'.$i.'__idy_'.$j.'format'};
                $mform->addElement('radio', 'chosenlevelid__idx_'.$i, '',
                        format_text($definition, $definitionformat, null, $workshop->course->id), $levelid);
                $mform->addElement('html', '</td>' . "\n");
            }
            $mform->addElement('html', '</tr>' . "\n");
        }
        $mform->addElement('html', '</table>' . "\n");

        $this->set_data($current);
    }
}
