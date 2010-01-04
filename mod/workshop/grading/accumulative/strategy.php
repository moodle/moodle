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
 * This file defines a class with accumulative grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/strategy.php'); // parent class

/**
 * Accumulative grading strategy logic.
 */
class workshop_accumulative_strategy extends workshop_base_strategy {

    public function load_form() {
        global $DB;

        $dims = $DB->get_records('workshop_forms_' . $this->name, array('workshopid' => $this->workshop->id), 'sort');
        $this->nodimensions = count($dims);
        return $this->_cook_dimension_records($dims);
    }

    /**
     * Transpones the dimension data from DB so the assessment form editor can be populated by set_data
     *
     * Called internally from load_form(). Could be private but keeping protected
     * for unit testing purposes.
     *
     * @param array $raw Array of raw dimension records as fetched by get_record()
     * @return array Array of fields data to be used by the mform set_data
     */
    protected function _cook_dimension_records(array $raw) {

        $formdata = array();
        $key = 0;
        foreach ($raw as $dimension) {
            $formdata['dimensionid[' . $key . ']']       = $dimension->id;
            $formdata['description[' . $key . ']']       = $dimension->description;
            $formdata['descriptionformat[' . $key . ']'] = $dimension->descriptionformat;
            $formdata['grade[' . $key . ']']             = $dimension->grade;
            $formdata['weight[' . $key . ']']            = $dimension->weight;
            $key++;
        }
        return (object)$formdata;
    }

    /**
     * Save the assessment dimensions into database
     *
     * Saves data into the main strategy form table. If the record->id is null or zero,
     * new record is created. If the record->id is not empty, the existing record is updated. Records with
     * empty 'description' field are removed from database.
     * The passed data object are the raw data returned by the get_data().
     *
     * @uses $DB
     * @param object $data Raw data returned by the dimension editor form
     * @access public
     * @return void
     */
    public function save_form(stdClass $data) {
        global $DB;

        if (!isset($data->strategyname) || ($data->strategyname != $this->name)) {
            // the workshop strategy has changed since the form was opened for editing
            throw new moodle_exception('strategyhaschanged', 'workshop');
        }

        $data = $this->_cook_form_data($data);
        $todelete = array();
        foreach ($data as $record) {
            if (empty($record->description)) {
                if (!empty($record->id)) {
                    // existing record with empty description - to be deleted
                    $todelete[] = $record->id;
                }
                continue;
            }
            if (empty($record->id)) {
                // new field
                $record->id = $DB->insert_record('workshop_forms_' . $this->name, $record);
            } else {
                // exiting field
                $DB->update_record('workshop_forms_' . $this->name, $record);
            }
        }
        $DB->delete_records_list('workshop_forms_' . $this->name, 'id', $todelete);
    }

    /**
     * Prepares data returned by mform so they can be saved into database
     *
     * It automatically adds some columns into every record. The sorting is
     * done by the order of the returned array and starts with 1.
     * Called internally from save_form() only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param object $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function _cook_form_data(stdClass $raw) {

        $cook = array();

        for ($k = 0; $k < $raw->numofdimensions; $k++) {
            $cook[$k]                    = new stdClass();
            $cook[$k]->id                = isset($raw->dimensionid[$k]) ? $raw->dimensionid[$k] : null;
            $cook[$k]->workshopid        = $this->workshop->id;
            $cook[$k]->sort              = $k + 1;
            $cook[$k]->description       = isset($raw->description[$k]) ? $raw->description[$k] : null;
            $cook[$k]->descriptionformat = FORMAT_HTML;
            $cook[$k]->grade             = isset($raw->grade[$k]) ? $raw->grade[$k] : null;
            $cook[$k]->weight            = isset($raw->weight[$k]) ? $raw->weight[$k] : null;
        }
        return $cook;
    }

}
