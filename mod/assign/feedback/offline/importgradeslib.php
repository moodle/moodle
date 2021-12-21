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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

/**
 * CSV Grade importer
 *
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_offline_grade_importer {

    /** @var string $importid - unique id for this import operation - must be passed between requests */
    public $importid;

    /** @var csv_import_reader $csvreader - the csv importer class */
    private $csvreader;

    /** @var assignment $assignment - the assignment class */
    private $assignment;

    /** @var int $gradeindex the column index containing the grades */
    private $gradeindex = -1;

    /** @var int $idindex the column index containing the unique id  */
    private $idindex = -1;

    /** @var int $modifiedindex the column index containing the last modified time */
    private $modifiedindex = -1;

    /** @var array $validusers only the enrolled users with the correct capability in this course */
    private $validusers;

    /** @var array $feedbackcolumnindexes A lookup of column indexes for feedback plugin text import columns */
    private $feedbackcolumnindexes = array();

    /** @var string $encoding Encoding to use when reading the csv file. Defaults to utf-8. */
    private $encoding;

    /** @var string $separator How each bit of information is separated in the file. Defaults to comma separated. */
    private $separator;

    /**
     * Constructor
     *
     * @param string $importid A unique id for this import
     * @param assign $assignment The current assignment
     */
    public function __construct($importid, assign $assignment, $encoding = 'utf-8', $separator = 'comma') {
        $this->importid = $importid;
        $this->assignment = $assignment;
        $this->encoding = $encoding;
        $this->separator = $separator;
    }

    /**
     * Parse a csv file and save the content to a temp file
     * Should be called before init()
     *
     * @param string $csvdata The csv data
     * @return bool false is a failed import
     */
    public function parsecsv($csvdata) {
        $this->csvreader = new csv_import_reader($this->importid, 'assignfeedback_offline');
        $this->csvreader->load_csv_content($csvdata, $this->encoding, $this->separator);
    }

    /**
     * Initialise the import reader and locate the column indexes.
     *
     * @return bool false is a failed import
     */
    public function init() {
        if ($this->csvreader == null) {
            $this->csvreader = new csv_import_reader($this->importid, 'assignfeedback_offline');
        }
        $this->csvreader->init();

        $columns = $this->csvreader->get_columns();

        $strgrade = get_string('gradenoun');
        $strid = get_string('recordid', 'assign');
        $strmodified = get_string('lastmodifiedgrade', 'assign');

        foreach ($this->assignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                foreach ($plugin->get_editor_fields() as $field => $description) {
                    $this->feedbackcolumnindexes[$description] = array('plugin'=>$plugin,
                                                                       'field'=>$field,
                                                                       'description'=>$description);
                }
            }
        }

        if ($columns) {
            foreach ($columns as $index => $column) {
                if (isset($this->feedbackcolumnindexes[$column])) {
                    $this->feedbackcolumnindexes[$column]['index'] = $index;
                }
                if ($column == $strgrade) {
                    $this->gradeindex = $index;
                }
                if ($column == $strid) {
                    $this->idindex = $index;
                }
                if ($column == $strmodified) {
                    $this->modifiedindex = $index;
                }
            }
        }

        if ($this->idindex < 0 || $this->gradeindex < 0 || $this->modifiedindex < 0) {
            return false;
        }

        $groupmode = groups_get_activity_groupmode($this->assignment->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->assignment->get_course_module(), true);
            $groupname = groups_get_group_name($groupid).'-';
        }
        $this->validusers = $this->assignment->list_participants($groupid, false);
        return true;
    }

    /**
     * Return the encoding for this csv import.
     *
     * @return string The encoding for this csv import.
     */
    public function get_encoding() {
        return $this->encoding;
    }

    /**
     * Return the separator for this csv import.
     *
     * @return string The separator for this csv import.
     */
    public function get_separator() {
        return $this->separator;
    }

    /**
     * Get the next row of data from the csv file (only the columns we care about)
     *
     * @return stdClass or false The stdClass is an object containing user, grade and lastmodified
     */
    public function next() {
        global $DB;
        $result = new stdClass();

        while ($record = $this->csvreader->next()) {
            $idstr = $record[$this->idindex];
            // Strip the integer from the end of the participant string.
            $id = substr($idstr, strlen(get_string('hiddenuser', 'assign')));
            if ($userid = $this->assignment->get_user_id_for_uniqueid($id)) {
                if (array_key_exists($userid, $this->validusers)) {
                    $result->grade = $record[$this->gradeindex];
                    $result->modified = strtotime($record[$this->modifiedindex]);
                    $result->user = $this->validusers[$userid];
                    $result->feedback = array();
                    foreach ($this->feedbackcolumnindexes as $description => $details) {
                        if (!empty($details['index'])) {
                            $details['value'] = $record[$details['index']];
                            $result->feedback[] = $details;
                        }
                    }

                    return $result;
                }
            }
        }

        // If we got here the csvreader had no more rows.
        return false;
    }

    /**
     * Close the grade importer file and optionally delete any temp files
     *
     * @param bool $delete
     */
    public function close($delete) {
        $this->csvreader->close();
        if ($delete) {
            $this->csvreader->cleanup();
        }
    }
}

