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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\controllers;

use block_lsuxe\persistents\mappings;
use block_lsuxe\persistents\moodles;

class form_controller {

    private $persistentname;
    private $persistpath;
    /**
     * Construct the form to work with the persistents.
     *
     * @param string  the name of the object/persistent we are working with
     */
    public function __construct($thisobj) {
        $this->persistentname = $thisobj;
        $this->persistpath = "\\block_lsuxe\\persistents\\";
    }

    /**
     * User clicks to save or update on any form, handle that action.
     * @param  object form data
     * @return the error, if any.
     */
    public function process_form($data) {

        // Update the record.
        if (isset($data->id) && $data->id != 0) {
            $this->update_record($data);
        } else {
            // Check to see if there are any matching records.
            if (false == $error = $this->check_for_existing($data)) {
                // Return an error to trigger a notification.
                return $error;
            }

            // Save the record.
            if (false == $error = $this->save_record($data)) {
                // Return an error to trigger a notification.
                return $error;
            }
        }
    }

    /**
     * Find if this record already exists based on a matching criteria.
     * @param  object $data
     * @return bool
     */
    public function check_for_existing($data) {
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname();
        $colprops = $po->column_record_check();
        $params = array();

        // Let's get the conditions for this persistent object.
        foreach ($colprops as $key => $val) {
            $params[$key] = $data->$val;
        }
        $count = $po::count_records($params);
        return $count > 1 ? false : true;
    }

    /**
     * Save this record.
     * @param  object $data
     * @return bool
     */
    public function save_record($data) {
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname();
        // Get the matching columns for this form object.
        $colprops = $po->column_form_symetric();
        $tosave = new \stdClass();

        // Let's gather the form data and map to the DB columns.
        foreach ($colprops as $key => $val) {
            $tosave->$key = $data->$val;
        }

        // Now to add any specific fields for this form object.
        $po->column_form_custom($tosave, $data);
        unset($po);

        try {
            // Check whether the object is valid.
            $po = new $pname(0, $tosave);
            if ($po->is_valid()) {
                $po->create();
            } else {
                $validateerrors = $po->get_errors();
                debugging("There is a validation error, please check the inputs.");
                // TODO: convert error_log to a logging system.
                return false;
            }

        } catch (invalid_persistent_exception $e) {
            // Whoops, something wrong happened.
            return false;
        }
    }

    /**
     * Recover this record.
     * @param  object $data
     * @return bool
     */
    public function recover_record($record) {
        global $USER;
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname($record);

        // Check whether a record exists.
        $exists = $po->record_exists($record);
        // Permanently delete the object from the database.
        if ($exists) {
            $po->set('userdeleted', $USER->id);
            $po->set('timedeleted', null);
            $po->update();
        }
    }

    /**
     * Update this record.
     * @param  object $data
     * @return bool
     */
    public function update_record($data) {
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname($data->id);
        // Get the matching columns for this form object.
        $colprops = $po->column_form_symetric();
        $tosave = new \stdClass();

        // Let's gather the form data and map to the DB columns.
        foreach ($colprops as $key => $val) {
            $tosave->$key = $data->$val;
        }

        // Now to add any specific fields for this form object.
        $po->column_form_custom($tosave, $data, true);

        // Let's gather the form data.
        foreach ($tosave as $key => $val) {
            $po->set($key, $val);
        }
        $po->update();
    }

    /**
     * Delete the record.
     * @param  int $record
     * @return bool
     */
    public function delete_record($record) {
        global $USER;
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname($record);

        // Check whether a record exists.
        $exists = $po->record_exists($record);
        // Permanently delete the object from the database.
        if ($exists) {
            $po->set('userdeleted', $USER->id);
            $po->set('timedeleted', time());
            $po->update();
        }
    }

    /**
     * Fetch all records for that persistent
     * @return array
     */
    public function get_records() {
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname();
        $records = array();

        $persistlist = $po->get_records();
        foreach ($persistlist as $pitem) {
            $records[] = $pitem->to_record();
        }
        return $records;
    }

    /**
     * Fetch all records for a given property
     * @param string - this will be the coloumn name
     * @return array
     */
    public function get_records_by_prop($property, $maintainid = false) {

        // Create object in the database.
        $pname = $this->persistpath . $this->persistentname;
        $po = new $pname();
        $records = array();

        $persistlist = $po->get_records(["timedeleted" => null]);
        foreach ($persistlist as $pitem) {
            $temprecord = $pitem->to_record();
            if ($maintainid) {
                $records[$temprecord->id] = $temprecord->$property;
            } else {
                $records[] = $temprecord->$property;
            }
        }
        return $records;
    }
}
