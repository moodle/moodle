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
 * This file defines a class with "Number of errors" grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/strategy.php'); // parent class

/**
 * "Number of errors" grading strategy logic.
 */
class workshop_noerrors_strategy extends workshop_base_strategy {

    public function load_form() {
        global $DB;

        $dims = $DB->get_records('workshop_forms_' . $this->name(), array('workshopid' => $this->workshop->id), 'sort');
        $maps = $DB->get_records('workshop_forms_noerrors_map', array('workshopid' => $this->workshop->id), 'nonegative');
        $this->nodimensions = count($dims);
        return $this->_cook_database_records($dims, $maps);
    }

    /**
     * Transpones the dimension data from DB so the assessment form editor can be populated by set_data
     *
     * Called internally from load_form(). Could be private but keeping protected
     * for unit testing purposes.
     *
     * @param array $dims Array of raw dimension records as fetched by get_record()
     * @param array $maps Array of grade mappings
     * @return array Object to be used by the mform set_data
     */
    protected function _cook_database_records(array $dims, array $maps) {

        $formdata = array();

        // cook dimensions
        $key = 0;
        foreach ($dims as $dimension) {
            $formdata['dimensionid[' . $key . ']']       = $dimension->id;
            $formdata['description[' . $key . ']']       = $dimension->description;
            $formdata['descriptionformat[' . $key . ']'] = $dimension->descriptionformat;
            $formdata['grade0[' . $key . ']']            = $dimension->grade0;
            $formdata['grade1[' . $key . ']']            = $dimension->grade1;
            $formdata['weight[' . $key . ']']            = $dimension->weight;
            $key++;
        }

        // cook grade mappings
        foreach ($maps as $map) {
            $formdata['map[' . $map->nonegative . ']'] = $map->grade;
        }

        return (object)$formdata;
    }

    /**
     * Save the definition of a "Number of errors" grading form
     *
     * The dimensions data are stored in workshop_forms_noerrors. The data that map the
     * number of errors to a grade are saved into workshop_forms_noerrors_map.
     *
     * @uses $DB
     * @param object $data Raw data returned by the dimension editor form
     * @return void
     */
    public function save_form(object $data) {
        global $DB;

        if (!isset($data->strategyname) || ($data->strategyname != $this->name())) {
            // the workshop strategy has changed since the form was opened for editing
            throw new moodle_exception('strategyhaschanged', 'workshop');
        }

        // save the dimensions data
        $dims = $this->_cook_form_data($data);
        $todelete = array();
        foreach ($dims as $record) {
            if (empty($record->description)) {
                if (!empty($record->id)) {
                    // existing record with empty description - to be deleted
                    $todelete[] = $record->id;
                }
                continue;
            }
            if (empty($record->id)) {
                // new field
                $record->id = $DB->insert_record('workshop_forms_' . $this->name(), $record);
            } else {
                // exiting field
                $DB->update_record('workshop_forms_' . $this->name(), $record);
            }
        }
        // delete dimensions if the teacher removed the description
        $DB->delete_records_list('workshop_forms_' . $this->name(), 'id', $todelete);

        // re-save the mappings
        $current  = array();
        $currentx = $DB->get_records('workshop_forms_noerrors_map', array('workshopid' => $this->workshop->id));
        foreach ($currentx as $id => $map) {
            $current[$map->nonegative] = $map->grade;
        }
        unset($currentx);
        $todelete = array();

        foreach ($data->map as $nonegative => $grade) {
            if ($nonegative == 0) {
                // no negative response automatically maps to 100%, do not save such mapping
                continue;
            }
            if (!is_numeric($grade)) {
                // no grade set for this number of negative responses
                $todelete[] = $nonegative;
                continue;
            }
            if (isset($current[$nonegative])) {
                $DB->set_field('workshop_forms_noerrors_map', 'grade', $grade,
                            array('workshopid' => $this->workshop->id, 'nonegative' => $nonegative));
            } else {
                $DB->insert_record('workshop_forms_noerrors_map',
                            (object)array('workshopid' => $this->workshop->id, 'nonegative' => $nonegative, 'grade' => $grade));
            }
        }
        // clear mappings that are not valid any more
        if (!empty($todelete)) {
            list($insql, $params) = $DB->get_in_or_equal($todelete);
            $insql = 'nonegative ' . $insql . ' OR ';
        } else {
            list($insql, $params) = array('', array());
        }
        $sql = 'DELETE FROM {workshop_forms_noerrors_map}
                WHERE ((' . $insql . 'nonegative > ?) AND (workshopid = ?))';
        $params[] = count($data->map) - 1;
        $params[] = $this->workshop->id;
        if (!$DB->execute($sql, $params)){
            print_error('err_removegrademappings', 'workshop');
        }
    }

    /**
     * Prepares dimensions data returned by mform so they can be saved into database
     *
     * It automatically adds some columns into every record. The sorting is
     * done by the order of the returned array and starts with 1.
     * Called internally from save_form() only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param object $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function _cook_form_data(object $raw) {

        $cook = array();

        for ($k = 0; $k < $raw->numofdimensions; $k++) {
            $cook[$k]                    = new object();
            $cook[$k]->id                = isset($raw->dimensionid[$k]) ? $raw->dimensionid[$k] : null;
            $cook[$k]->workshopid        = $this->workshop->id;
            $cook[$k]->sort              = $k + 1;
            $cook[$k]->description       = isset($raw->description[$k]) ? $raw->description[$k] : null;
            $cook[$k]->descriptionformat = FORMAT_HTML;
            $cook[$k]->grade0            = isset($raw->grade0[$k]) ? $raw->grade0[$k] : null;
            $cook[$k]->grade1            = isset($raw->grade1[$k]) ? $raw->grade1[$k] : null;
            $cook[$k]->weight            = isset($raw->weight[$k]) ? $raw->weight[$k] : null;
        }
        return $cook;
    }

}
