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
 * File containing processor class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/csvlib.class.php');

/**
 * Processor class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_uploadcourse_processor {

    /**
     * Create courses that do not exist yet.
     */
    const MODE_CREATE_NEW = 1;

    /**
     * Create all courses, appending a suffix to the shortname if the course exists.
     */
    const MODE_CREATE_ALL = 2;

    /**
     * Create courses, and update the ones that already exist.
     */
    const MODE_CREATE_OR_UPDATE = 3;

    /**
     * Only update existing courses.
     */
    const MODE_UPDATE_ONLY = 4;

    /**
     * During update, do not update anything... O_o Huh?!
     */
    const UPDATE_NOTHING = 0;

    /**
     * During update, only use data passed from the CSV.
     */
    const UPDATE_ALL_WITH_DATA_ONLY = 1;

    /**
     * During update, use either data from the CSV, or defaults.
     */
    const UPDATE_ALL_WITH_DATA_OR_DEFAUTLS = 2;

    /**
     * During update, update missing values from either data from the CSV, or defaults.
     */
    const UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS = 3;

    /** @var int processor mode. */
    protected $mode;

    /** @var int upload mode. */
    protected $updatemode;

    /** @var bool are renames allowed. */
    protected $allowrenames = false;

    /** @var bool are deletes allowed. */
    protected $allowdeletes = false;

    /** @var bool are resets allowed. */
    protected $allowresets = false;

    /** @var string path to a restore file. */
    protected $restorefile;

    /** @var string shortname of the course to be restored. */
    protected $templatecourse;

    /** @var string reset courses after processing them. */
    protected $reset;

    /** @var string template to generate a course shortname. */
    protected $shortnametemplate;

    /** @var csv_import_reader */
    protected $cir;

    /** @var array default values. */
    protected $defaults = array();

    /** @var array CSV columns. */
    protected $columns = array();

    /** @var array of errors where the key is the line number. */
    protected $errors = array();

    /** @var int line number. */
    protected $linenb = 0;

    /** @var bool whether the process has been started or not. */
    protected $processstarted = false;

    /**
     * Constructor
     *
     * @param csv_import_reader $cir import reader object
     * @param array $options options of the process
     * @param array $defaults default data value
     */
    public function __construct(csv_import_reader $cir, array $options, array $defaults = array()) {

        if (!isset($options['mode']) || !in_array($options['mode'], array(self::MODE_CREATE_NEW, self::MODE_CREATE_ALL,
                self::MODE_CREATE_OR_UPDATE, self::MODE_UPDATE_ONLY))) {
            throw new coding_exception('Unknown process mode');
        }

        // Force int to make sure === comparison work as expected.
        $this->mode = (int) $options['mode'];

        $this->updatemode = self::UPDATE_NOTHING;
        if (isset($options['updatemode'])) {
            // Force int to make sure === comparison work as expected.
            $this->updatemode = (int) $options['updatemode'];
        }
        if (isset($options['allowrenames'])) {
            $this->allowrenames = $options['allowrenames'];
        }
        if (isset($options['allowdeletes'])) {
            $this->allowdeletes = $options['allowdeletes'];
        }
        if (isset($options['allowresets'])) {
            $this->allowresets = $options['allowresets'];
        }

        if (isset($options['restorefile'])) {
            $this->restorefile = $options['restorefile'];
        }
        if (isset($options['templatecourse'])) {
            $this->templatecourse = $options['templatecourse'];
        }
        if (isset($options['reset'])) {
            $this->reset = $options['reset'];
        }
        if (isset($options['shortnametemplate'])) {
            $this->shortnametemplate = $options['shortnametemplate'];
        }

        $this->cir = $cir;
        $this->columns = $cir->get_columns();
        $this->defaults = $defaults;
        $this->validate();
        $this->reset();
    }

    /**
     * Execute the process.
     *
     * @param object $tracker the output tracker to use.
     * @return void
     */
    public function execute($tracker = null) {
        if ($this->processstarted) {
            throw new coding_exception('Process has already been started');
        }
        $this->processstarted = true;

        if (empty($tracker)) {
            $tracker = new tool_uploadcourse_tracker(tool_uploadcourse_tracker::NO_OUTPUT);
        }
        $tracker->start();

        $total = 0;
        $created = 0;
        $updated = 0;
        $deleted = 0;
        $errors = 0;

        // We will most certainly need extra time and memory to process big files.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);

        // Loop over the CSV lines.
        while ($line = $this->cir->next()) {
            $this->linenb++;
            $total++;

            $data = $this->parse_line($line);
            $course = $this->get_course($data);
            if ($course->prepare()) {
                $course->proceed();

                $status = $course->get_statuses();
                if (array_key_exists('coursecreated', $status)) {
                    $created++;
                } else if (array_key_exists('courseupdated', $status)) {
                    $updated++;
                } else if (array_key_exists('coursedeleted', $status)) {
                    $deleted++;
                }

                $data = array_merge($data, $course->get_data(), array('id' => $course->get_id()));
                $tracker->output($this->linenb, true, $status, $data);
                if ($course->has_errors()) {
                    $errors++;
                    $tracker->output($this->linenb, false, $course->get_errors(), $data);
                }
            } else {
                $errors++;
                $tracker->output($this->linenb, false, $course->get_errors(), $data);
            }
        }

        $tracker->finish();
        $tracker->results($total, $created, $updated, $deleted, $errors);
    }

    /**
     * Return a course import object.
     *
     * @param array $data data to import the course with.
     * @return tool_uploadcourse_course
     */
    protected function get_course($data) {
        $importoptions = array(
            'candelete' => $this->allowdeletes,
            'canrename' => $this->allowrenames,
            'canreset' => $this->allowresets,
            'reset' => $this->reset,
            'restoredir' => $this->get_restore_content_dir(),
            'shortnametemplate' => $this->shortnametemplate
        );
        return new tool_uploadcourse_course($this->mode, $this->updatemode, $data, $this->defaults, $importoptions);
    }

    /**
     * Return the errors.
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Get the directory of the object to restore.
     *
     * @return string subdirectory in $CFG->backuptempdir/...
     */
    protected function get_restore_content_dir() {
        $backupfile = null;
        $shortname = null;

        if (!empty($this->restorefile)) {
            $backupfile = $this->restorefile;
        } else if (!empty($this->templatecourse) || is_numeric($this->templatecourse)) {
            $shortname = $this->templatecourse;
        }

        $dir = tool_uploadcourse_helper::get_restore_content_dir($backupfile, $shortname);
        return $dir;
    }

    /**
     * Log errors on the current line.
     *
     * @param array $errors array of errors
     * @return void
     */
    protected function log_error($errors) {
        if (empty($errors)) {
            return;
        }

        foreach ($errors as $code => $langstring) {
            if (!isset($this->errors[$this->linenb])) {
                $this->errors[$this->linenb] = array();
            }
            $this->errors[$this->linenb][$code] = $langstring;
        }
    }

    /**
     * Parse a line to return an array(column => value)
     *
     * @param array $line returned by csv_import_reader
     * @return array
     */
    protected function parse_line($line) {
        $data = array();
        foreach ($line as $keynum => $value) {
            if (!isset($this->columns[$keynum])) {
                // This should not happen.
                continue;
            }

            $key = $this->columns[$keynum];
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * Return a preview of the import.
     *
     * This only returns passed data, along with the errors.
     *
     * @param integer $rows number of rows to preview.
     * @param object $tracker the output tracker to use.
     * @return array of preview data.
     */
    public function preview($rows = 10, $tracker = null) {
        if ($this->processstarted) {
            throw new coding_exception('Process has already been started');
        }
        $this->processstarted = true;

        if (empty($tracker)) {
            $tracker = new tool_uploadcourse_tracker(tool_uploadcourse_tracker::NO_OUTPUT);
        }
        $tracker->start();

        // We might need extra time and memory depending on the number of rows to preview.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);

        // Loop over the CSV lines.
        $preview = array();
        while (($line = $this->cir->next()) && $rows > $this->linenb) {
            $this->linenb++;
            $data = $this->parse_line($line);
            $course = $this->get_course($data);
            $result = $course->prepare();
            if (!$result) {
                $tracker->output($this->linenb, $result, $course->get_errors(), $data);
            } else {
                $tracker->output($this->linenb, $result, $course->get_statuses(), $data);
            }
            $row = $data;
            $preview[$this->linenb] = $row;
        }

        $tracker->finish();

        return $preview;
    }

    /**
     * Reset the current process.
     *
     * @return void.
     */
    public function reset() {
        $this->processstarted = false;
        $this->linenb = 0;
        $this->cir->init();
        $this->errors = array();
    }

    /**
     * Validation.
     *
     * @return void
     */
    protected function validate() {
        if (empty($this->columns)) {
            throw new moodle_exception('cannotreadtmpfile', 'error');
        } else if (count($this->columns) < 2) {
            throw new moodle_exception('csvfewcolumns', 'error');
        }
    }
}
