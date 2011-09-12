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
 * Class implementing the controller of any backup process
 *
 * This final class is in charge of controlling all the backup architecture, for any
 * type of backup. Based in type, format, interactivity and target, it stores the
 * whole execution plan and settings that will be used later by the @backup_worker,
 * applies all the defaults, performs all the security contraints and is in charge
 * of handling the ui if necessary. Also logging strategy is defined here.
 *
 * Note the class is 100% neutral and usable for *any* backup. It just stores/requests
 * all the needed information from other backup classes in order to have everything well
 * structured in order to allow the @backup_worker classes to do their job.
 *
 * In other words, a mammoth class, but don't worry, practically everything is delegated/
 * aggregated!)
 *
 * TODO: Finish phpdocs
 */
class backup_controller extends backup implements loggable {

    protected $backupid; // Unique identificator for this backup

    protected $type;   // Type of backup (activity, section, course)
    protected $id;     // Course/section/course_module id to backup
    protected $courseid; // courseid where the id belongs to
    protected $format; // Format of backup (moodle, imscc)
    protected $interactive; // yes/no
    protected $mode;   // Purpose of the backup (default settings)
    protected $userid; // user id executing the backup
    protected $operation; // Type of operation (backup/restore)

    protected $status; // Current status of the controller (created, planned, configured...)

    protected $plan;   // Backup execution plan

    protected $execution;     // inmediate/delayed
    protected $executiontime; // epoch time when we want the backup to be executed (requires cron to run)

    protected $destination; // Destination chain object (fs_moodle, fs_os, db, email...)
    protected $logger;      // Logging chain object (moodle, inline, fs, db, syslog)

    protected $checksum; // Cache @checksumable results for lighter @is_checksum_correct() uses

    /**
     * Constructor for the backup controller class.
     *
     * @param int $type Type of the backup; One of backup::TYPE_1COURSE, TYPE_1SECTION, TYPE_1ACTIVITY
     * @param int $id The ID of the item to backup; e.g the course id
     * @param int $format The backup format to use; Most likely backup::FORMAT_MOODLE
     * @param bool $interactive Whether this backup will require user interaction; backup::INTERACTIVE_YES or INTERACTIVE_NO
     * @param int $mode One of backup::MODE_GENERAL, MODE_IMPORT, MODE_SAMESITE, MODE_HUB, MODE_AUTOMATED
     * @param int $userid The id of the user making the backup
     */
    public function __construct($type, $id, $format, $interactive, $mode, $userid){
        $this->type = $type;
        $this->id   = $id;
        $this->courseid = backup_controller_dbops::get_courseid_from_type_id($this->type, $this->id);
        $this->format = $format;
        $this->interactive = $interactive;
        $this->mode = $mode;
        $this->userid = $userid;

        // Apply some defaults
        $this->execution = backup::EXECUTION_INMEDIATE;
        $this->operation = backup::OPERATION_BACKUP;
        $this->executiontime = 0;
        $this->checksum = '';

        // Apply current backup version and release if necessary
        backup_controller_dbops::apply_version_and_release();

        // Check format and type are correct
        backup_check::check_format_and_type($this->format, $this->type);

        // Check id is correct
        backup_check::check_id($this->type, $this->id);

        // Check user is correct
        backup_check::check_user($this->userid);

        // Calculate unique $backupid
        $this->calculate_backupid();

        // Default logger chain (based on interactive/execution)
        $this->logger = backup_factory::get_logger_chain($this->interactive, $this->execution, $this->backupid);

        // Instantiate the output_controller singleton and active it if interactive and inmediate
        $oc = output_controller::get_instance();
        if ($this->interactive == backup::INTERACTIVE_YES && $this->execution == backup::EXECUTION_INMEDIATE) {
            $oc->set_active(true);
        }

        $this->log('instantiating backup controller', backup::LOG_INFO, $this->backupid);

        // Default destination chain (based on type/mode/execution)
        $this->destination = backup_factory::get_destination_chain($this->type, $this->id, $this->mode, $this->execution);

        // Set initial status
        $this->set_status(backup::STATUS_CREATED);

        // Load plan (based on type/format)
        $this->load_plan();

        // Apply all default settings (based on type/format/mode)
        $this->apply_defaults();

        // Perform all initial security checks and apply (2nd param) them to settings automatically
        backup_check::check_security($this, true);

        // Set status based on interactivity
        if ($this->interactive == backup::INTERACTIVE_YES) {
            $this->set_status(backup::STATUS_SETTING_UI);
        } else {
            $this->set_status(backup::STATUS_AWAITING);
        }
    }

    /**
     * Clean structures used by the backup_controller
     *
     * This method clean various structures used by the backup_controller,
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
            throw new backup_controller_exception('cannot_finish_ui_if_not_setting_ui');
        }
        $this->set_status(backup::STATUS_AWAITING);
    }

    public function process_ui_event() {

        // Perform security checks throwing exceptions (2nd param) if something is wrong
        backup_check::check_security($this, false);
    }

    public function set_status($status) {
        $this->log('setting controller status to', backup::LOG_DEBUG, $status);
        // TODO: Check it's a correct status
        $this->status = $status;
        // Ensure that, once set to backup::STATUS_AWAITING, controller is stored in DB
        // Note: never save_controller() after STATUS_EXECUTING or the whole controller,
        // containing all the steps will be sent to DB. 100% (monster) useless.
        if ($status == backup::STATUS_AWAITING) {
            $this->save_controller();
            $tbc = self::load_controller($this->backupid);
            $this->logger = $tbc->logger; // wakeup loggers
            $tbc->destroy(); // Clean temp controller structures
        }
    }

    public function set_execution($execution, $executiontime = 0) {
        $this->log('setting controller execution', backup::LOG_DEBUG);
        // TODO: Check valid execution mode
        // TODO: Check time in future
        // TODO: Check time = 0 if inmediate
        $this->execution = $execution;
        $this->executiontime = $executiontime;

        // Default destination chain (based on type/mode/execution)
        $this->destination = backup_factory::get_destination_chain($this->type, $this->id, $this->mode, $this->execution);

        // Default logger chain (based on interactive/execution)
        $this->logger = backup_factory::get_logger_chain($this->interactive, $this->execution, $this->backupid);
    }

// checksumable interface methods

    public function calculate_checksum() {
        // Reset current checksum to take it out from calculations!
        $this->checksum = '';
        // Init checksum
        $tempchecksum = md5('backupid-'   . $this->backupid .
                            'type-'       . $this->type .
                            'id-'         . $this->id .
                            'format-'     . $this->format .
                            'interactive-'. $this->interactive .
                            'mode-'       . $this->mode .
                            'userid-'     . $this->userid .
                            'operation-'  . $this->operation .
                            'status-'     . $this->status .
                            'execution-'  . $this->execution .
                            'plan-'       . backup_general_helper::array_checksum_recursive(array($this->plan)) .
                            'destination-'. backup_general_helper::array_checksum_recursive(array($this->destination)) .
                            'logger-'     . backup_general_helper::array_checksum_recursive(array($this->logger)));
        $this->log('calculating controller checksum', backup::LOG_DEBUG, $tempchecksum);
        return $tempchecksum;
    }

    public function is_checksum_correct($checksum) {
        return $this->checksum === $checksum;
    }

    public function get_backupid() {
        return $this->backupid;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_operation() {
        return $this->operation;
    }

    public function get_id() {
        return $this->id;
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
     * @return backup_plan
     */
    public function get_plan() {
        return $this->plan;
    }

    public function get_logger() {
        return $this->logger;
    }

    /**
     * Executes the backup
     * @return void Throws and exception of completes
     */
    public function execute_plan() {
        // Basic/initial prevention against time/memory limits
        set_time_limit(1 * 60 * 60); // 1 hour for 1 course initially granted
        raise_memory_limit(MEMORY_EXTRA);
        return $this->plan->execute();
    }

    public function get_results() {
        return $this->plan->get_results();
    }

    public function log($message, $level, $a = null, $depth = null, $display = false) {
        backup_helper::log($message, $level, $a, $depth, $display, $this->logger);
    }

    public function save_controller() {
        // Going to save controller to persistent storage, calculate checksum for later checks and save it
        // TODO: flag the controller as NA. Any operation on it should be forbidden util loaded back
        $this->log('saving controller to db', backup::LOG_DEBUG);
        $this->checksum = $this->calculate_checksum();
        backup_controller_dbops::save_controller($this, $this->checksum);
    }

    public static function load_controller($backupid) {
        // Load controller from persistent storage
        // TODO: flag the controller as available. Operations on it can continue
        $controller = backup_controller_dbops::load_controller($backupid);
        $controller->log('loading controller from db', backup::LOG_DEBUG);
        return $controller;
    }

// Protected API starts here

    protected function calculate_backupid() {
        // Current epoch time + type + id + format + interactive + mode + userid + operation
        // should be unique enough. Add one random part at the end
        $this->backupid = md5(time() . '-' . $this->type . '-' . $this->id . '-' . $this->format . '-' .
                              $this->interactive . '-' . $this->mode . '-' . $this->userid . '-' .
                              $this->operation . '-' . random_string(20));
    }

    protected function load_plan() {
        $this->log('loading controller plan', backup::LOG_DEBUG);
        $this->plan = new backup_plan($this);
        $this->plan->build(); // Build plan for this controller
        $this->set_status(backup::STATUS_PLANNED);
    }

    protected function apply_defaults() {
        $this->log('applying plan defaults', backup::LOG_DEBUG);
        backup_controller_dbops::apply_config_defaults($this);
        $this->set_status(backup::STATUS_CONFIGURED);
    }
}

/*
 * Exception class used by all the @backup_controller stuff
 */
class backup_controller_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
