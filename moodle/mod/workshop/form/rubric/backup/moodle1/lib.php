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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    workshopform_rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Conversion handler for the rubric grading strategy data
 */
class moodle1_workshopform_rubric_handler extends moodle1_workshopform_handler {

    /** @var array legacy elements to process */
    protected $elements = array();

    /** @var array legacy rubrics records to process */
    protected $rubrics = array();

    /**
     * Prepare to gather legacy elements info for a new workshop instance
     */
    public function on_elements_start() {
        $this->elements = array();
        $this->rubrics = array();
    }

    /**
     * Processes one <ELEMENT>
     *
     * @param array $data legacy element data
     * @param array $raw raw element data
     */
    public function process_legacy_element(array $data, array $raw) {
        $this->elements[] = $data;
        $this->rubrics[$data['id']] = array();
    }

    /**
     * Processes one <RUBRIC>
     */
    public function process_legacy_rubric($data, $raw) {
        $this->rubrics[$data['elementid']][] = $data;
    }

    /**
     * Processes gathered elements and rubrics
     */
    public function on_elements_end() {

        $numofrubrics = 0;
        foreach ($this->rubrics as $itemid => $levels) {
            $numofrubrics += count($levels);
        }

        if ($numofrubrics == 0) {
            $this->convert_legacy_criterion_elements();

        } else {
            $this->convert_legacy_rubric_elements();
        }
    }

    /**
     * Processes gathered elements coming from the legacy criterion strategy
     *
     * Legacy criterion strategy is converted to a rubric with single rubric item
     * and the layout set to 'list'.
     */
    protected function convert_legacy_criterion_elements() {

        $this->write_xml('workshopform_rubric_config', array('layout' => 'list'));

        $firstelement = reset($this->elements);
        if ($firstelement === false) {
            // no elements defined in moodle.xml
            return;
        }

        // write the xml describing the artificial single rubric item
        $this->xmlwriter->begin_tag('workshopform_rubric_dimension', array('id' => $firstelement['id']));
        $this->xmlwriter->full_tag('sort', 1);
        $this->xmlwriter->full_tag('description', trim(get_string('dimensionnumber', 'workshopform_rubric', '')));
        $this->xmlwriter->full_tag('descriptionformat', FORMAT_HTML);

        foreach ($this->elements as $element) {
            $this->write_xml('workshopform_rubric_level', array(
                'id'               => $element['id'],
                'grade'            => $element['maxscore'],
                'definition'       => $element['description'],
                'definitionformat' => FORMAT_HTML
            ), array('/workshopform_rubric_level/id'));
        }

        $this->xmlwriter->end_tag('workshopform_rubric_dimension');
    }

    /**
     * Processes gathered elements coming from the legacy rubric strategy
     */
    protected function convert_legacy_rubric_elements() {
        $this->write_xml('workshopform_rubric_config', array('layout' => 'grid'));

        foreach ($this->elements as $element) {
            $this->xmlwriter->begin_tag('workshopform_rubric_dimension', array('id' => $element['id']));
            $this->xmlwriter->full_tag('sort', $element['elementno']);
            $this->xmlwriter->full_tag('description', $element['description']);
            $this->xmlwriter->full_tag('descriptionformat', FORMAT_HTML);

            foreach ($this->rubrics[$element['id']] as $rubric) {
                $fakerecord          = new stdClass();
                $fakerecord->rgrade  = $rubric['rubricno'];
                $fakerecord->eweight = $element['weight'];
                $fakerecord->rdesc   = $rubric['description'];
                $level = (array)workshopform_rubric_upgrade_rubric_level($fakerecord, $element['id']);
                unset($level['dimensionid']);
                $level['id'] = $this->converter->get_nextid();
                $this->write_xml('workshopform_rubric_level', $level, array('/workshopform_rubric_level/id'));
            }

            $this->xmlwriter->end_tag('workshopform_rubric_dimension');
        }
    }
}

/**
 * Transforms given record into an object to be saved into workshopform_rubric_levels
 *
 * This is used during Rubric 1.9 -> Rubric 2.0 conversion
 *
 * @param stdClass $old legacy record from joined workshop_elements_old + workshop_rubrics_old
 * @param int $newdimensionid id of the new workshopform_rubric dimension record to be linked to
 * @return stdclass to be saved in workshopform_rubric_levels
 */
function workshopform_rubric_upgrade_rubric_level(stdclass $old, $newdimensionid) {
    $new = new stdclass();
    $new->dimensionid = $newdimensionid;
    $new->grade = $old->rgrade * workshopform_rubric_upgrade_weight($old->eweight);
    $new->definition = $old->rdesc;
    $new->definitionformat = FORMAT_HTML;
    return $new;
}

/**
 * Given old workshop element weight, returns the weight multiplier
 *
 * Negative weights are not supported any more and are replaced with weight = 0.
 * Legacy workshop did not store the raw weight but the index in the array
 * of weights (see $WORKSHOP_EWEIGHTS in workshop 1.x). workshop 2.0 uses
 * integer weights only (0-16) so all previous weights are multiplied by 4.
 *
 * @param $oldweight index in legacy $WORKSHOP_EWEIGHTS
 * @return int new weight
 */
function workshopform_rubric_upgrade_weight($oldweight) {

    switch ($oldweight) {
        case 8: $weight = 1; break;
        case 9: $weight = 2; break;
        case 10: $weight = 3; break;
        case 11: $weight = 4; break;
        case 12: $weight = 6; break;
        case 13: $weight = 8; break;
        case 14: $weight = 16; break;
        default: $weight = 0;
    }
    return $weight;
}
