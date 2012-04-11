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
 * Provides base converter classes
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/convert_includes.php');

/**
 * Base converter class
 *
 * All Moodle backup converters are supposed to extend this base class.
 *
 * @throws convert_exception
 */
abstract class base_converter implements loggable {

    /** @var string unique identifier of this converter instance */
    protected $id;

    /** @var string the name of the directory containing the unpacked backup being converted */
    protected $tempdir;

    /** @var string the name of the directory where the backup is converted to */
    protected $workdir;

    /** @var null|base_logger logger to use during the conversion */
    protected $logger = null;

    /**
     * Constructor
     *
     * @param string $tempdir the relative path to the directory containing the unpacked backup to convert
     * @param null|base_logger logger to use during the conversion
     */
    public function __construct($tempdir, $logger = null) {

        $this->tempdir  = $tempdir;
        $this->id       = convert_helper::generate_id($tempdir);
        $this->workdir  = $tempdir . '_' . $this->get_name() . '_' . $this->id;
        $this->set_logger($logger);
        $this->log('instantiating '.$this->get_name().' converter '.$this->get_id(), backup::LOG_DEBUG);
        $this->log('conversion source directory', backup::LOG_DEBUG, $this->tempdir);
        $this->log('conversion target directory', backup::LOG_DEBUG, $this->workdir);
        $this->init();
    }

    /**
     * Sets the logger to use during the conversion
     *
     * @param null|base_logger $logger
     */
    public function set_logger($logger) {
        if (is_null($logger) or ($logger instanceof base_logger)) {
            $this->logger = $logger;
        }
    }

    /**
     * If the logger was set for the converter, log the message
     *
     * If the $display is enabled, the spaces in the $message text are removed
     * and the text is used as a string identifier in the core_backup language file.
     *
     * @see backup_helper::log()
     * @param string $message message text
     * @param int $level message level {@example backup::LOG_WARNING}
     * @param null|mixed $a additional information
     * @param null|int $depth the message depth
     * @param bool $display whether the message should be sent to the output, too
     */
    public function log($message, $level, $a = null, $depth = null, $display = false) {
        if ($this->logger instanceof base_logger) {
            backup_helper::log($message, $level, $a, $depth, $display, $this->logger);
        }
    }

    /**
     * Get instance identifier
     *
     * @return string the unique identifier of this converter instance
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get converter name
     *
     * @return string the system name of the converter
     */
    public function get_name() {
        $parts = explode('_', get_class($this));
        return array_shift($parts);
    }

    /**
     * Converts the backup directory
     */
    public function convert() {

        try {
            $this->log('creating the target directory', backup::LOG_DEBUG);
            $this->create_workdir();

            $this->log('executing the conversion', backup::LOG_DEBUG);
            $this->execute();

            $this->log('replacing the source directory with the converted version', backup::LOG_DEBUG);
            $this->replace_tempdir();
        } catch (Exception $e) {
        }

        // clean-up stuff if needed
        $this->destroy();

        // eventually re-throw the execution exception
        if (isset($e) and ($e instanceof Exception)) {
            throw $e;
        }
    }

    /**
     * @return string the full path to the working directory
     */
    public function get_workdir_path() {
        global $CFG;

        return "$CFG->tempdir/backup/$this->workdir";
    }

    /**
     * @return string the full path to the directory with the source backup
     */
    public function get_tempdir_path() {
        global $CFG;

        return "$CFG->tempdir/backup/$this->tempdir";
    }

    /// public static methods //////////////////////////////////////////////////

    /**
     * Makes sure that this converter is available at this site
     *
     * This is intended for eventual PHP extensions check, environment check etc.
     * All checks that do not depend on actual backup data should be done here.
     *
     * @return boolean true if this converter should be considered as available
     */
    public static function is_available() {
        return true;
    }

    /**
     * Detects the format of the backup directory
     *
     * Moodle 2.x format is being detected by the core itself. The converters are
     * therefore supposed to detect the source format. Eventually, if the target
     * format os not {@link backup::FORMAT_MOODLE} then they should be able to
     * detect both source and target formats.
     *
     * @param string $tempdir the name of the backup directory
     * @return null|string null if not recognized, backup::FORMAT_xxx otherwise
     */
    public static function detect_format($tempdir) {
        return null;
    }

    /**
     * Returns the basic information about the converter
     *
     * The returned array must contain the following keys:
     * 'from' - the supported source format, eg. backup::FORMAT_MOODLE1
     * 'to'   - the supported target format, eg. backup::FORMAT_MOODLE
     * 'cost' - the cost of the conversion, non-negative non-zero integer
     */
    public static function description() {

        return array(
            'from'  => null,
            'to'    => null,
            'cost'  => null,
        );
    }

    /// end of public API //////////////////////////////////////////////////////

    /**
     * Initialize the instance if needed, called by the constructor
     */
    protected function init() {
    }

    /**
     * Converts the contents of the tempdir into the target format in the workdir
     */
    protected abstract function execute();

    /**
     * Prepares a new empty working directory
     */
    protected function create_workdir() {

        fulldelete($this->get_workdir_path());
        if (!check_dir_exists($this->get_workdir_path())) {
            throw new convert_exception('failed_create_workdir');
        }
    }

    /**
     * Replaces the source backup directory with the converted version
     *
     * If $CFG->keeptempdirectoriesonbackup is defined, the original source
     * source backup directory is kept for debugging purposes.
     */
    protected function replace_tempdir() {
        global $CFG;

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($this->get_tempdir_path());
        } else {
            if (!rename($this->get_tempdir_path(), $this->get_tempdir_path()  . '_' . $this->get_name() . '_' . $this->id . '_source')) {
                throw new convert_exception('failed_rename_source_tempdir');
            }
        }

        if (!rename($this->get_workdir_path(), $this->get_tempdir_path())) {
            throw new convert_exception('failed_move_converted_into_place');
        }
    }

    /**
     * Cleans up stuff after the execution
     *
     * Note that we do not know if the execution was successful or not.
     * An exception might have been thrown.
     */
    protected function destroy() {
        global $CFG;

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($this->get_workdir_path());
        }
    }
}

/**
 * General convert-related exception
 *
 * @author David Mudrak <david@moodle.com>
 */
class convert_exception extends moodle_exception {

    /**
     * Constructor
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
