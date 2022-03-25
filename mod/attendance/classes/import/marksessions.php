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
 * Import attendance sessions class.
 *
 * @package   mod_attendance
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\import;

defined('MOODLE_INTERNAL') || die();

use csv_import_reader;
use mod_attendance_notifyqueue;
use mod_attendance_structure;
use stdClass;

/**
 * Import attendance sessions.
 *
 * @package mod_attendance
 * @copyright 2020 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marksessions {

    /** @var string $error The errors message from reading the xml */
    protected $error = '';

    /** @var array $sessions The sessions info */
    protected $sessions = array();

    /** @var array $mappings The mappings info */
    protected $mappings = array();

    /** @var int The id of the csv import */
    protected $importid = 0;

    /** @var csv_import_reader|null  $importer */
    protected $importer = null;

    /** @var array $foundheaders */
    protected $foundheaders = array();

    /** @var bool $useprogressbar Control whether importing should use progress bars or not. */
    protected $useprogressbar = false;

    /** @var \core\progress\display_if_slow|null $progress The progress bar instance. */
    protected $progress = null;

    /** @var mod_attendance_structure $att - the mod_attendance_structure class */
    private $att;

    /**
     * Store an error message for display later
     *
     * @param string $msg
     */
    public function fail($msg) {
        $this->error = $msg;
        return false;
    }

    /**
     * Get the CSV import id
     *
     * @return string The import id.
     */
    public function get_importid() {
        return $this->importid;
    }

    /**
     * Get the list of headers found in the import.
     *
     * @return array The found headers (names from import)
     */
    public function list_found_headers() {
        return $this->foundheaders;
    }

    /**
     * Read the data from the mapping form.
     *
     * @param array $data The mapping data.
     */
    protected function read_mapping_data($data) {
        if ($data) {
            return array(
                'user' => $data->userfrom,
                'scantime' => $data->scantime,
                'status' => $data->status
            );
        } else {
            return array(
                'user' => 0,
                'scantime' => 1,
                'status' => 2
            );
        }
    }

    /**
     * Get the a column from the imported data.
     *
     * @param array $row The imported raw row
     * @param int $index The column index we want
     * @return string The column data.
     */
    protected function get_column_data($row, $index) {
        if ($index < 0) {
            return '';
        }
        return isset($row[$index]) ? $row[$index] : '';
    }

    /**
     * Constructor - parses the raw text for sanity.
     *
     * @param string $text The raw csv text.
     * @param mod_attendance_structure $att The current assignment
     * @param string $encoding The encoding of the csv file.
     * @param string $delimiter The specified delimiter for the file.
     * @param string $importid The id of the csv import.
     * @param array $mappingdata The mapping data from the import form.
     * @param bool $useprogressbar Whether progress bar should be displayed, to avoid html output on CLI.
     */
    public function __construct($text = null, $att, $encoding = null, $delimiter = null, $importid = 0,
                                $mappingdata = null, $useprogressbar = false) {
        global $CFG, $USER;

        require_once($CFG->libdir . '/csvlib.class.php');

        $type = 'marksessions';

        $this->att = $att;

        if (! $importid) {
            if ($text === null) {
                return;
            }
            $this->importid = csv_import_reader::get_new_iid($type);

            $this->importer = new csv_import_reader($this->importid, $type);

            if (! $this->importer->load_csv_content($text, $encoding, $delimiter)) {
                $this->fail(get_string('invalidimportfile', 'attendance'));
                $this->importer->cleanup();
                echo $text;
                return;
            }
        } else {
            $this->importid = $importid;

            $this->importer = new csv_import_reader($this->importid, $type);
        }

        if (! $this->importer->init()) {
            $this->fail(get_string('invalidimportfile', 'attendance'));
            $this->importer->cleanup();
            return;
        }

        $this->foundheaders = $this->importer->get_columns();

        $this->useprogressbar = $useprogressbar;

        $sesslog = array();

        $validusers = $this->att->get_users($this->att->pageparams->grouptype, 0);
        $users = array();

        // Re-key validusers based on the identifier used by import.
        if (!empty($mappingdata) && $mappingdata->userto !== 'id') {
            foreach ($validusers as $u) {
                if (!empty($u->{$mappingdata->userto})) {
                    $users[strtolower($u->{$mappingdata->userto})] = $u;
                }
            }
        } else {
            $users = $validusers;
        }

        $statuses = $this->att->get_statuses();
        $statusmap = array();
        foreach ($statuses as $st) {
            $statusmap[$st->acronym] = $st->id;
        }

        $sessioninfo = $this->att->get_session_info($this->att->pageparams->sessionid);

        while ($row = $this->importer->next()) {
            // This structure mimics what the UI form returns.
            if (empty($mappingdata)) {
                // Precheck - just return for now - would be nice to look at adding preview option in future.
                return;
            }
            $mapping = $this->read_mapping_data($mappingdata);

            // Get user.
            $extuser = strtolower($this->get_column_data($row, $mapping['user']));
            if (empty($users[$extuser])) {
                $a = new \stdClass();
                $a->extuser = $extuser;
                $a->userfield = $mappingdata->userto;
                \mod_attendance_notifyqueue::notify_problem(get_string('error:usernotfound', 'attendance', $a));
                continue;
            }
            $userid = $users[$extuser]->id;
            if (isset($sesslog[$userid])) {
                \mod_attendance_notifyqueue::notify_problem(get_string('error:userduplicate', 'attendance', $extuser));
                continue;
            }
            $sesslog[$userid] = new stdClass();
            $sesslog[$userid]->studentid = $userid;
            $sesslog[$userid]->statusset = $statuses;
            $sesslog[$userid]->remarks = '';
            $sesslog[$userid]->sessionid = $this->att->pageparams->sessionid;
            $sesslog[$userid]->timetaken = time();
            $sesslog[$userid]->takenby = $USER->id;

            $scantime = $this->get_column_data($row, $mapping['scantime']);
            if (!empty($scantime)) {
                $t = strtotime($scantime);
                if ($t === false) {
                    $a = new \stdClass();
                    $a->extuser = $extuser;
                    $a->scantime = $scantime;
                    \mod_attendance_notifyqueue::notify_problem(get_string('error:timenotreadable', 'attendance', $a));
                    continue;
                }

                $sesslog[$userid]->statusid = attendance_session_get_highest_status($this->att, $sessioninfo, $t);
            } else {
                $status = $this->get_column_data($row, $mapping['status']);
                if (!empty($statusmap[$status])) {
                    $sesslog[$userid]->statusid = $statusmap[$status];
                } else {
                    $a = new \stdClass();
                    $a->extuser = $extuser;
                    $a->status = $status;
                    \mod_attendance_notifyqueue::notify_problem(get_string('error:statusnotfound', 'attendance', $a));
                    continue;
                }
            }
        }
        $this->sessions = $sesslog;

        $this->importer->close();
        if (empty($sesslog)) {
            $this->fail(get_string('invalidimportfile', 'attendance'));
            return;
        } else {
            raise_memory_limit(MEMORY_EXTRA);

            // We are calling from browser, display progress bar.
            if ($this->useprogressbar === true) {
                $this->progress = new \core\progress\display_if_slow(get_string('processingfile', 'attendance'));
                $this->progress->start_html();
            } else {
                $this->progress = new \core\progress\none();
            }
            $this->progress->start_progress('', count($this->sessions));
            $this->progress->end_progress();
        }
    }

    /**
     * Get parse errors.
     *
     * @return array of errors from parsing the xml.
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Create sessions using the CSV data.
     *
     * @return void
     */
    public function import() {
        $this->att->save_log($this->sessions);
        \mod_attendance_notifyqueue::notify_success(get_string('sessionsupdated', 'mod_attendance'));
    }
}
