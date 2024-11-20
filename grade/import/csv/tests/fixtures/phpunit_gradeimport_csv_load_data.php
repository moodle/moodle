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

require_once($CFG->dirroot . '/grade/import/csv/classes/load_data.php');
require_once($CFG->dirroot . '/grade/import/lib.php');

/**
 * Class to open up private methods in gradeimport_csv_load_data().
 */
class phpunit_gradeimport_csv_load_data extends gradeimport_csv_load_data {

    /**
     * Method to open up the appropriate method for unit testing.
     *
     * @param object $record
     * @param int $studentid
     * @param grade_item $gradeitem
     */
    public function test_insert_grade_record($record, $studentid, grade_item $gradeitem) {
        $this->importcode = 00001;
        $this->insert_grade_record($record, $studentid, $gradeitem);
    }

    /**
     * Method to open up the appropriate method for unit testing.
     */
    public function get_importcode() {
        return $this->importcode;
    }

    /**
     * Method to open up the appropriate method for unit testing.
     *
     * @param array $header The column headers from the CSV file.
     * @param int $key Current row identifier.
     * @param string $value The value for this row (final grade).
     * @return array new grades that are ready for commiting to the gradebook.
     */
    public function test_import_new_grade_item($header, $key, $value) {
        $this->newgradeitems = null;
        $this->importcode = 00001;
        return $this->import_new_grade_item($header, $key, $value);
    }

    /**
     * Method to open up the appropriate method for unit testing.
     *
     * @param string $value The value, from the csv file, being mapped to identify the user.
     * @param array $userfields Contains the field and label being mapped from.
     * @return int Returns the user ID if it exists, otherwise null.
     */
    public function test_check_user_exists($value, $userfields) {
        return $this->check_user_exists($value, $userfields);
    }

    /**
     * Method to open up the appropriate method for unit testing.
     *
     * @param int $courseid The course ID.
     * @param int $itemid The ID of the grade item that the feedback relates to.
     * @param string $value The actual feedback being imported.
     * @return object Creates a feedback object with the item ID and the feedback value.
     */
    public function test_create_feedback($courseid, $itemid, $value) {
        return $this->create_feedback($courseid, $itemid, $value);
    }

    /**
     * Method to open up the appropriate method for unit testing.
     */
    public function test_update_grade_item($courseid, $map, $key, $verbosescales, $value) {
        return $this->update_grade_item($courseid, $map, $key, $verbosescales, $value);
    }

    /**
     * Method to open up the appropriate method for unit testing.
     *
     * @param int $courseid The course ID.
     * @param array $map Mapping information provided by the user.
     * @param int $key The line that we are currently working on.
     * @param bool $verbosescales Form setting for grading with scales.
     * @param string $value The grade value .
     * @return array grades to be updated.
     */
    public function test_map_user_data_with_value($mappingidentifier, $value, $header, $map, $key, $courseid, $feedbackgradeid,
            $verbosescales) {
        // Set an import code.
        $this->importcode = 00001;
        $this->map_user_data_with_value($mappingidentifier, $value, $header, $map, $key, $courseid, $feedbackgradeid,
                $verbosescales);

        switch ($mappingidentifier) {
            case 'userid':
            case 'useridnumber':
            case 'useremail':
            case 'username':
                return $this->studentid;
            break;
            case 'new':
                return $this->newgrades;
            break;
            case 'feedback':
                return $this->newfeedbacks;
            break;
            default:
                return $this->newgrades;
            break;
        }
    }
}
