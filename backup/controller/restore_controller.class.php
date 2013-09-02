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
 * @package moodlecore
 * @subpackage backup-controller
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class implementing the controller of any restore process
 *
 * This final class is in charge of controlling all the restore architecture, for any
 * type of backup.
 *
 * TODO: Finish phpdocs
 */
class restore_controller extends backup implements loggable {

    protected $tempdir;   // Directory under tempdir/backup awaiting restore
    protected $restoreid; // Unique identificator for this restore

    protected $courseid; // courseid where restore is going to happen

    protected $type;   // Type of backup (activity, section, course)
    protected $format; // Format of backup (moodle, imscc)
    protected $interactive; // yes/no
    protected $mode;   // Purpose of the backup (default settings)
    protected $userid; // user id executing the restore
    protected $operation; // Type of operation (backup/restore)
    protected $target;    // Restoring to new/existing/current_adding/_deleting
    protected $samesite;  // Are we restoring to the same site where the backup was generated

    protected $status; // Current status of the controller (created, planned, configured...)
    protected $precheck;    // Results of the execution of restore prechecks

    protected $info;   // Information retrieved from backup contents
    protected $plan;   // Restore execution plan

    protected $execution;     // inmediate/delayed
    protected $executiontime; // epoch time when we want the restore to be executed (requires cron to run)

    protected $logger;      // Logging chain object (moodle, inline, fs, db, syslog)

    protected $checksum; // Cache @checksumable results for lighter @is_checksum_correct() uses

    /**
     *
     * @param string $tempdir Directory under tempdir/backup awaiting restore
     * @param int $courseid Course id where restore is going to happen
     * @param bool $interactive backup::INTERACTIVE_YES[true] or backup::INTERACTIVE_NO[false]
     * @param int $mode backup::MODE_[ GENERAL | HUB | IMPORT | SAMESITE ]
     * @param int $userid
     * @param int $target backup::TARGET_[ NEW_COURSE | CURRENT_ADDING | CURRENT_DELETING | EXISTING_ADDING | EXISTING_DELETING ]
     */
    public function __construct($tempdir, $courseid, $interactive, $mode, $userid, $target){
        $this->tempdir = $tempdir;
        $this->courseid = $courseid;
        $this->interactive = $interactive;
        $this->mode = $mode;
        $this->userid = $userid;
        $this->target = $target;

        // Apply some defaults
        $this->type = '';
        $this->format = backup::FORMAT_UNKNOWN;
        $this->execution = backup::EXECUTION_INMEDIATE;
        $this->operation = backup::OPERATION_RESTORE;
        $this->executiontime = 0;
        $this->samesite = false;
        $this->checksum = '';
        $this->precheck = null;

        // Apply current backup version and release if necessary
        backup_controller_dbops::apply_version_and_release();

        // Check courseid is correct
        restore_check::check_courseid($this->courseid);

        // Check user is correct
        restore_check::check_user($this->userid);

        // Calculate unique $restoreid
        $this->calculate_restoreid();

        // Default logger chain (based on interactive/execution)
        $this->logger = backup_factory::get_logger_chain($this->interactive, $this->execution, $this->restoreid);

        // Instantiate the output_controller singleton and active it if interactive and inmediate
        $oc = output_controller::get_instance();
        if ($this->interactive == backup::INTERACTIVE_YES && $this->execution == backup::EXECUTION_INMEDIATE) {
            $oc->set_active(true);
        }

        $this->log('instantiating restore controller', backup::LOG_INFO, $this->restoreid);

        // Set initial status
        $this->set_status(backup::STATUS_CREATED);

        // Calculate original restore format
        $this->format = backup_general_helper::detect_backup_format($tempdir);

        // If format is not moodle2, set to conversion needed
        if ($this->format !== backup::FORMAT_MOODLE) {
            $this->set_status(backup::STATUS_REQUIRE_CONV);

        // Else, format is moodle2, load plan, apply security and set status based on interactivity
        } else {
            // Load plan
            $this->load_plan();

            // Perform all initial security checks and apply (2nd param) them to settings automatically
            restore_check::check_security($this, true);

            if ($this->interactive == backup::INTERACTIVE_YES) {
                $this->set_status(backup::STATUS_SETTING_UI);
            } else {
                $this->set_status(backup::STATUS_NEED_PRECHECK);
            }
        }
    }

    /**
     * Clean structures used by the restore_controller
     *
     * This method clean various structures used by the restore_controller,
     * destroying them in an ordered way, so their memory will be gc properly
     * by PHP (mainly circular references).
     *
     * Note that, while it's not mandatory to execute this method, it's highly
     * recommended to do so, specially in scripts performing multiple operations
     * (like the automated backups) or the system will run out of memory after
     * a few dozens of backups)
     */
    public function destroy() {
        // Only need to destroy circulars under the plan. Delegate to it.
        $this->plan->destroy();
    }

    public function finish_ui() {
        if ($this->status != backup::STATUS_SETTING_UI) {
            throw new restore_controller_exception('cannot_finish_ui_if_not_setting_ui');
        }
        $this->set_status(backup::STATUS_NEED_PRECHECK);
    }

    public function process_ui_event() {

        // Perform security checks throwing exceptions (2nd param) if something is wrong
        restore_check::check_security($this, false);
    }

    public function set_status($status) {
        // Note: never save_controller() with the object info after STATUS_EXECUTING or the whole controller,
        // containing all the steps will be sent to DB. 100% (monster) useless.
        $this->log('setting controller status to', backup::LOG_DEBUG, $status);
        // TODO: Check it's a correct status.
        $this->status = $status;
        // Ensure that, once set to backup::STATUS_AWAITING | STATUS_NEED_PRECHECK, controller is stored in DB.
        if ($status == backup::STATUS_AWAITING || $status == backup::STATUS_NEED_PRECHECK) {
            $this->save_controller();
            $tbc = self::load_controller($this->restoreid);
            $this->logger = $tbc->logger; // wakeup loggers
            $tbc->destroy(); // Clean temp controller structures

        } else if ($status == backup::STATUS_FINISHED_OK) {
            // If the operation has ended without error (backup::STATUS_FINISHED_OK)
            // proceed by cleaning the object from database. MDL-29262.
            $this->save_controller(false, true);
        }
    }

    public function set_execution($execution, $executiontime = 0) {
        $this->log('setting controller execution', backup::LOG_DEBUG);
        // TODO: Check valid execution mode
        // TODO: Check time in future
        // TODO: Check time = 0 if inmediate
        $this->execution = $execution;
        $this->executiontime = $executiontime;

        // Default logger chain (based on interactive/execution)
        $this->logger = backup_factory::get_logger_chain($this->interactive, $this->execution, $this->restoreid);
    }

// checksumable interface methods

    public function calculate_checksum() {
        // Reset current checksum to take it out from calculations!
        $this->checksum = '';
        // Init checksum
        $tempchecksum = md5('tempdir-'    . $this->tempdir .
                            'restoreid-'  . $this->restoreid .
                            'courseid-'   . $this->courseid .
                            'type-'       . $this->type .
                            'format-'     . $this->format .
                            'interactive-'. $this->interactive .
                            'mode-'       . $this->mode .
                            'userid-'     . $this->userid .
                            'target-'     . $this->target .
                            'samesite-'   . $this->samesite .
                            'operation-'  . $this->operation .
                            'status-'     . $this->status .
                            'precheck-'   . backup_general_helper::array_checksum_recursive(array($this->precheck)) .
                            'execution-'  . $this->execution .
                            'plan-'       . backup_general_helper::array_checksum_recursive(array($this->plan)) .
                            'info-'       . backup_general_helper::array_checksum_recursive(array($this->info)) .
                            'logger-'     . backup_general_helper::array_checksum_recursive(array($this->logger)));
        $this->log('calculating controller checksum', backup::LOG_DEBUG, $tempchecksum);
        return $tempchecksum;
    }

    public function is_checksum_correct($checksum) {
        return $this->checksum === $checksum;
    }

    public function get_tempdir() {
        return $this->tempdir;
    }

    public function get_restoreid() {
        return $this->restoreid;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_operation() {
        return $this->operation;
    }

    public function get_courseid() {
        return $this->courseid;
    }

    public function get_format() {
        return $this->format;
    }

    public function get_interactive() {
        return $this->interactive;
    }

    public function get_mode() {
        return $this->mode;
    }

    public function get_userid() {
        return $this->userid;
    }

    public function get_target() {
        return $this->target;
    }

    public function is_samesite() {
        return $this->samesite;
    }

    public function get_status() {
        return $this->status;
    }

    public function get_execution() {
        return $this->execution;
    }

    public function get_executiontime() {
        return $this->executiontime;
    }

    /**
     * Returns the restore plan
     * @return restore_plan
     */
    public function get_plan() {
        return $this->plan;
    }

    public function get_info() {
        return $this->info;
    }

    public function get_logger() {
        return $this->logger;
    }

    public function execute_plan() {
        // Basic/initial prevention against time/memory limits
        set_time_limit(1 * 60 * 60); // 1 hour for 1 course initially granted
        raise_memory_limit(MEMORY_EXTRA);
        // If this is not a course restore, inform the plan we are not
        // including all the activities for sure. This will affect any
        // task/step executed conditionally to stop processing information
        // for section and activity restore. MDL-28180.
        if ($this->get_type() !== backup::TYPE_1COURSE) {
            $this->log('notifying plan about excluded activities by type', backup::LOG_DEBUG);
            $this->plan->set_excluding_activities();
        }
        return $this->plan->execute();
    }

    /**
     * Execute the restore prechecks to detect any problem before proceed with restore
     *
     * This function checks various parts of the restore (versions, users, roles...)
     * returning true if everything was ok or false if any warning/error was detected.
     * Any warning/error is returned by the get_precheck_results() method.
     * Note: if any problem is found it will, automatically, drop all the restore temp
     * tables as far as the next step is to inform about the warning/errors. If no problem
     * is found, then default behaviour is to keep the temp tables so, in the same request
     * restore will be executed, saving a lot of checks to be executed again.
     * Note: If for any reason (UI to show after prechecks...) you want to force temp tables
     * to be dropped always, you can pass true to the $droptemptablesafter parameter
     */
    public function execute_precheck($droptemptablesafter = false) {
        if (is_array($this->precheck)) {
            throw new restore_controller_exception('precheck_alredy_executed', $this->status);
        }
        if ($this->status != backup::STATUS_NEED_PRECHECK) {
            throw new restore_controller_exception('cannot_precheck_wrong_status', $this->status);
        }
        // Basic/initial prevention against time/memory limits
        set_time_limit(1 * 60 * 60); // 1 hour for 1 course initially granted
        raise_memory_limit(MEMORY_EXTRA);
        $this->precheck = restore_prechecks_helper::execute_prechecks($this, $droptemptablesafter);
        if (!array_key_exists('errors', $this->precheck)) { // No errors, can be executed
            $this->set_status(backup::STATUS_AWAITING);
        }
        if (empty($this->precheck)) { // No errors nor warnings, return true
            return true;
        }
        return false;
    }

    public function get_results() {
        return $this->plan->get_results();
    }

    /**
     * Returns true if the prechecks have been executed
     * @return bool
     */
    public function precheck_executed() {
        return (is_array($this->precheck));
    }

    public function get_precheck_results() {
        if (!is_array($this->precheck)) {
            throw new restore_controller_exception('precheck_not_executed');
        }
        return $this->precheck;
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        backup_helper::log($message, $level, $a, $depth, $display, $this->logger);
    }

    /**
     * Save controller information
     *
     * @param bool $includeobj to decide if the object itself must be updated (true) or no (false)
     * @param bool $cleanobj to decide if the object itself must be cleaned (true) or no (false)
     */
    public function save_controller($includeobj = true, $cleanobj = false) {
        // Going to save controller to persistent storage, calculate checksum for later checks and save it
        // TODO: flag the controller as NA. Any operation on it should be forbidden util loaded back
        $this->log('saving controller to db', backup::LOG_DEBUG);
        if ($includeobj ) {  // Only calculate checksum if we are going to include the object.
            $this->checksum = $this->calculate_checksum();
        }
        restore_controller_dbops::save_controller($this, $this->checksum, $includeobj, $cleanobj);
    }

    public static function load_controller($restoreid) {
        // Load controller from persistent storage
        // TODO: flag the controller as available. Operations on it can continue
        $controller = restore_controller_dbops::load_controller($restoreid);
        $controller->log('loading controller from db', backup::LOG_DEBUG);
        return $controller;
    }

    /**
     * class method to provide pseudo random unique "correct" tempdir names
     */
    public static function get_tempdir_name($courseid = 0, $userid = 0) {
        // Current epoch time + courseid + userid + random bits
        return md5(time() . '-' . $courseid . '-'. $userid . '-'. random_string(20));
    }

    /**
     * Converts from current format to backup::MOODLE format
     */
    public function convert() {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/helper/convert_helper.class.php');

        // Basic/initial prevention against time/memory limits
        set_time_limit(1 * 60 * 60); // 1 hour for 1 course initially granted
        raise_memory_limit(MEMORY_EXTRA);

        if ($this->status != backup::STATUS_REQUIRE_CONV) {
            throw new restore_controller_exception('cannot_convert_not_required_status');
        }

        $this->log('backup format conversion required', backup::LOG_INFO);

        // Run conversion to the proper format
        if (!convert_helper::to_moodle2_format($this->get_tempdir(), $this->format, $this->get_logger())) {
            // todo - unable to find the conversion path, what to do now?
            // throwing the exception as a temporary solution
            throw new restore_controller_exception('unable_to_find_conversion_path');
        }

        $this->log('backup format conversion successful', backup::LOG_INFO);

        // If no exceptions were thrown, then we are in the proper format
        $this->format = backup::FORMAT_MOODLE;

        // Load plan, apply security and set status based on interactivity
        $this->load_plan();

        // Perform all initial security checks and apply (2nd param) them to settings automatically
        restore_check::check_security($this, true);

        if ($this->interactive == backup::INTERACTIVE_YES) {
            $this->set_status(backup::STATUS_SETTING_UI);
        } else {
            $this->set_status(backup::STATUS_NEED_PRECHECK);
        }
    }

// Protected API starts here

    protected function calculate_restoreid() {
        // Current epoch time + tempdir + courseid + interactive + mode + userid + target + operation + random bits
        $this->restoreid = md5(time() . '-' . $this->tempdir . '-' . $this->courseid . '-'. $this->interactive . '-' .
                               $this->mode . '-' . $this->userid . '-'. $this->target . '-' . $this->operation . '-' .
                               random_string(20));
    }

    protected function load_plan() {
        // First of all, we need to introspect the moodle_backup.xml file
        // in order to detect all the required stuff. So, create the
        // monster $info structure where everything will be defined
        $this->log('loading backup info', backup::LOG_DEBUG);
        $this->info = backup_general_helper::get_backup_information($this->tempdir);

        // Set the controller type to the one found in the information
        $this->type = $this->info->type;

        // Set the controller samesite flag as needed
        $this->samesite = backup_general_helper::backup_is_samesite($this->info);

        // Now we load the plan that will be configured following the
        // information provided by the $info
        $this->log('loading controller plan', backup::LOG_DEBUG);
        $this->plan = new restore_plan($this);
        $this->plan->build(); // Build plan for this controller
        $this->set_status(backup::STATUS_PLANNED);
    }
}

/*
 * Exception class used by all the @restore_controller stuff
 */
class restore_controller_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
