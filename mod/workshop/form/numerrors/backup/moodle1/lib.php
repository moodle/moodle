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
 * @package    workshopform
 * @subpackage numerrors
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/workshop/form/numerrors/db/upgradelib.php');
require_once($CFG->libdir.'/gradelib.php'); // grade_floatval() called here

/**
 * Conversion handler for the numerrors grading strategy data
 */
class moodle1_workshopform_numerrors_handler extends moodle1_workshopform_handler {

    /** @var array */
    protected $mappings = array();

    /** @var array */
    protected $dimensions = array();

    /**
     * New workshop instance is being processed
     */
    public function on_elements_start() {
        $this->mappings = array();
        $this->dimensions = array();
    }

    /**
     * Converts <ELEMENT> into <workshopform_numerrors_dimension> and stores it for later writing
     *
     * @return array to be written to workshop.xml
     */
    public function process_legacy_element($data, $raw) {

        $workshop = $this->parenthandler->get_current_workshop();

        $mapping = array();
        $mapping['id'] = $data['id'];
        $mapping['nonegative'] = $data['elementno'];
        if ($workshop['grade'] == 0 or $data['maxscore'] == 0) {
            $mapping['grade'] = 0;
        } else {
            $mapping['grade'] = grade_floatval($data['maxscore'] / $workshop['grade'] * 100);
        }
        $this->mappings[] = $mapping;

        $converted = null;

        if (trim($data['description']) and $data['description'] <> '@@ GRADE_MAPPING_ELEMENT @@') {
            // prepare a fake record and re-use the upgrade logic
            $fakerecord = (object)$data;
            $converted = (array)workshopform_numerrors_upgrade_element($fakerecord, 12345678);
            unset($converted['workshopid']);

            $converted['id'] = $data['id'];
            $this->dimensions[] = $converted;
        }

        return $converted;
    }

    /**
     * Writes gathered mappings and dimensions
     */
    public function on_elements_end() {

        foreach ($this->mappings as $mapping) {
            $this->write_xml('workshopform_numerrors_map', $mapping, array('/workshopform_numerrors_map/id'));
        }

        foreach ($this->dimensions as $dimension) {
            $this->write_xml('workshopform_numerrors_dimension', $dimension, array('/workshopform_numerrors_dimension/id'));
        }
    }
}
