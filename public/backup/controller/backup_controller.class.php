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
 * Backup controller and related exception classes.
 *
 * @package core_backup
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
 */
class backup_controller extends base_controller {
    /** @var string Unique identifier for this backup */
    protected $backupid;

    /**
     * Type of item that is being stored in the backup.
     *
     * Should be selected from one of the backup::TYPE_ constants
     * for example backup::TYPE_1ACTIVITY
     *
     * @var string
     */
    protected $type;

    /** @var int Course/section/course_module id to backup */
    protected $id;

    /** @var false|int The id of the course the backup belongs to, or false if no course. */
    protected $courseid;

    /**
     * Format of backup (moodle, imscc).
     *
     * Should be one of the backup::FORMAT_ constants.
     * for example backup::FORMAT_MOODLE
     *
     * @var string
     */
    protected $format;

    /**
     * Whether this backup will require user interaction.
     *
     * Should be one of backup::INTERACTIVE_YES or INTERACTIVE_NO
     *
     * @var bool
     */
    protected $interactive;

    /**
     * Purpose of the backup (default settings)
     *
     * Should be one of the the backup::MODE_ constants,
     * for example backup::MODE_GENERAL
     *
     * @var int
     */
    protected $mode;

    /** @var int The id of the user executing the backup. */
    protected $userid;

    /**
     * Type of operation (backup/restore)
     *
     * Should be selected from: backup::OPERATION_BACKUP or OPERATION_RESTORE
     *
     * @var string
     */
    protected $operation;

    /**
     * Current status of the controller (created, planned, configured...)
     *
     * It should be one of the backup::STATUS_ constants,
     * for example backup::STATUS_AWAITING.
     *
     * @var int
     */
    protected $status;

    /** @var backup_plan Backup execution plan. */
    protected $plan;

    /** @var int Whether this backup includes files (1) or not (0). */
    protected $includefiles;

    /**
     * Immediate/delayed execution type.
     * @var int
     */
    protected $execution;

    /** @var int Epoch time when we want the backup to be executed (requires cron to run). */
    protected $executiontime;

    /** @var null Destination chain object (fs_moodle, fs_os, db, email...). */
    protected $destination;

    /** @var string Cache {@see \checksumable} results for lighter {@see \backup_controller::is_checksum_correct()} uses. */
    protected $checksum;

    /**
     * The role ids to keep in a copy operation.
     * @var array
     */
    protected $keptroles = array();

    /**
     * Constructor for the backup controller class.
     *
     * @param string $type Type of the backup; One of backup::TYPE_1COURSE, TYPE_1SECTION, TYPE_1ACTIVITY
     * @param int $id The ID of the item to backup; e.g the course id
     * @param string $format The backup format to use; Most likely backup::FORMAT_MOODLE
     * @param bool $interactive Whether this backup will require user interaction; backup::INTERACTIVE_YES or INTERACTIVE_NO
     * @param int $mode One of backup::MODE_GENERAL, MODE_IMPORT, MODE_SAMESITE, MODE_AUTOMATED
     * @param int $userid The id of the user making the backup
     * @param bool $releasesession Should release the session? backup::RELEASESESSION_YES or backup::RELEASESESSION_NO
     */
    public function __construct($type, $id, $format, $interactive, $mode, $userid, $releasesession = backup::RELEASESESSION_NO) {
        $this->type = $type;
        $this->id   = $id;
        $this->courseid = backup_controller_dbops::get_courseid_from_type_id($this->type, $this->id);
        $this->format = $format;
        $this->interactive = $interactive;
        $this->mode = $mode;
        $this->userid = $userid;
        $this->releasesession = $releasesession;

        // Apply some defaults
        $this->operation = backup::OPERATION_BACKUP;
        $this->executiontime = 0;
        $this->checksum = '';

        // Set execution based on backup mode.
        if ($mode == backup::MODE_ASYNC || $mode == backup::MODE_COPY) {
            $this->execution = backup::EXECUTION_DELAYED;
        } else {
            $this->execution = backup::EXECUTION_INMEDIATE;
        }

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

        // By default there is no progress reporter. Interfaces that wish to
        // display progress must set it.
        $this->progress = new \core\progress\none();

        // Instantiate the output_controller singleton and active it if interactive and immediate.
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
        // Loggers may have also chained references, destroy them. Also closing resources when needed.
        $this->logger->destroy();
    }

    /**
     * Declare that all user interaction with the backup controller is complete.
     *
     * After this the backup controller is waiting for processing.
     */
    public function finish_ui() {
        if ($this->status != backup::STATUS_SETTING_UI) {
            throw new backup_controller_exception('cannot_finish_ui_if_not_setting_ui');
        }
        $this->set_status(backup::STATUS_AWAITING);
    }

    /**
     * Validates the backup is valid after any user changes.
     *
     * A backup_controller_exception will be thrown if there is an issue.
     */
    public function process_ui_event() {

        // Perform security checks throwing exceptions (2nd param) if something is wrong
        backup_check::check_security($this, false);
    }

    /**
     * Sets the new status of the backup.
     *
     * @param int $status
     */
    public function set_status($status) {
        // Note: never save_controller() with the object info after STATUS_EXECUTING or the whole controller,
        // containing all the steps will be sent to DB. 100% (monster) useless.
        $this->log('setting controller status to', backup::LOG_DEBUG, $status);
        // TODO: Check it's a correct status.
        $this->status = $status;
        // Ensure that, once set to backup::STATUS_AWAITING, controller is stored in DB.
        // Also save if executing so we can better track progress.
        if ($status == backup::STATUS_AWAITING || $status == backup::STATUS_EXECUTING) {
            $this->save_controller();
            $tbc = self::load_controller($this->backupid);
            $this->logger = $tbc->logger; // wakeup loggers
            $tbc->plan->destroy(); // Clean plan controller structures, keeping logger alive.

        } else if ($status == backup::STATUS_FINISHED_OK) {
            // If the operation has ended without error (backup::STATUS_FINISHED_OK)
            // proceed by cleaning the object from database. MDL-29262.
            $this->save_controller(false, true);
        } else if ($status == backup::STATUS_FINISHED_ERR) {
            // If the operation has ended with an error save the controller
            // preserving the object in the database. We may want it for debugging.
            $this->save_controller();
        }
    }

    /**
     * Sets if the backup will be processed immediately, or later.
     *
     * @param int $execution Use backup::EXECUTION_INMEDIATE or backup::EXECUTION_DELAYED
     * @param int $executiontime The timestamp in the future when the task should be executed, or 0 for immediately.
     */
    public function set_execution($execution, $executiontime = 0) {
        $this->log('setting controller execution', backup::LOG_DEBUG);
        // TODO: Check valid execution mode.
        // TODO: Check time in future.
        // TODO: Check time = 0 if immediate.
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

    /**
     * Gets the unique identifier for this backup controller.
     *
     * @return string
     */
    public function get_backupid() {
        return $this->backupid;
    }

    /**
     * Gets the type of backup to be performed.
     *
     * Use {@see \backup_controller::get_id()} to find the instance being backed up.
     *
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Returns the current value of the include_files setting.
     * This setting is intended to ensure that files are not included in
     * generated backups.
     *
     * @return int Indicates whether files should be included in backups.
     */
    public function get_include_files() {
        return $this->includefiles;
    }

    /**
     * Returns the default value for $this->includefiles before we consider any settings.
     *
     * @return bool
     * @throws dml_exception
     */
    protected function get_include_files_default(): bool {
        // We normally include files.
        $includefiles = true;

        // In an import, we don't need to include files.
        if ($this->get_mode() === backup::MODE_IMPORT) {
            $includefiles = false;
        }

        // When a backup is intended for the same site, we don't need to include the files.
        // Note, this setting is only used for duplication of an entire course.
        if ($this->get_mode() === backup::MODE_SAMESITE || $this->get_mode() === backup::MODE_COPY) {
            $includefiles = false;
        }

        // If backup is automated and we have set auto backup config to exclude
        // files then set them to be excluded here.
        $backupautofiles = (bool) get_config('backup', 'backup_auto_files');
        if ($this->get_mode() === backup::MODE_AUTOMATED && !$backupautofiles) {
            $includefiles = false;
        }

        return $includefiles;
    }

    /**
     * Gets if this is a backup or restore.
     *
     * @return string
     */
    public function get_operation() {
        return $this->operation;
    }

    /**
     * Gets the instance id of the item being backed up.
     *
     * It's meaning is related to the type of backup {@see \backup_controller::get_type()}.
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Gets the course that the item being backed up is in.
     *
     * @return false|int
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Gets the format the backup is stored in.
     *
     * @return string
     */
    public function get_format() {
        return $this->format;
    }

    /**
     * Gets if user interaction is expected during the backup.
     *
     * @return bool
     */
    public function get_interactive() {
        return $this->interactive;
    }

    /**
     * Gets the mode that the backup will be performed in.
     *
     * @return int
     */
    public function get_mode() {
        return $this->mode;
    }

    /**
     * Get the id of the user who started the backup.
     *
     * @return int
     */
    public function get_userid() {
        return $this->userid;
    }

    /**
     * Get the current status of the backup.
     *
     * @return int
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Get if the backup will be executed immediately, or later.
     *
     * @return int
     */
    public function get_execution() {
        return $this->execution;
    }

    /**
     * Get when the backup will be executed.
     *
     * @return int 0 means now, otherwise a Unix timestamp
     */
    public function get_executiontime() {
        return $this->executiontime;
    }

    /**
     * Gets the plan that will be run during the backup.
     *
     * @return backup_plan
     */
    public function get_plan() {
        return $this->plan;
    }

    /**
     * For debug only. Get a simple test display of all the settings.
     *
     * @return string
     */
    public function debug_display_all_settings_values(): string {
        return $this->get_plan()->debug_display_all_settings_values();
    }

    /**
     * Sets the user roles that should be kept in the destination course
     * for a course copy operation.
     *
     * @param array $roleids
     * @throws backup_controller_exception
     */
    public function set_kept_roles(array $roleids): void {
        // Only allow of keeping user roles when controller is in copy mode.
        if ($this->mode != backup::MODE_COPY) {
            throw new backup_controller_exception('cannot_set_keep_roles_wrong_mode');
        }

        $this->keptroles = $roleids;
    }

    /**
     * Executes the backup
     * @return void Throws and exception of completes
     */
    public function execute_plan() {
        // Basic/initial prevention against time/memory limits
        core_php_time_limit::raise(1 * 60 * 60); // 1 hour for 1 course initially granted
        raise_memory_limit(MEMORY_EXTRA);

        // Release the session so other tabs in the same session are not blocked.
        if ($this->get_releasesession() === backup::RELEASESESSION_YES) {
            \core\session\manager::write_close();
        }

        // If the controller has decided that we can include files, then check the setting, otherwise do not include files.
        if ($this->get_include_files()) {
            $this->set_include_files((bool) $this->get_plan()->get_setting('files')->get_value());
        }

        // If this is not a course backup, or single activity backup (e.g. duplicate) inform the plan we are not
        // including all the activities for sure. This will affect any
        // task/step executed conditionally to stop including information
        // for section and activity backup. MDL-28180.
        if ($this->get_type() !== backup::TYPE_1COURSE && $this->get_type() !== backup::TYPE_1ACTIVITY) {
            $this->log('notifying plan about excluded activities by type', backup::LOG_DEBUG);
            $this->plan->set_excluding_activities();
        }

        // Handle copy operation specific settings.
        if ($this->mode == backup::MODE_COPY) {
            $this->plan->set_kept_roles($this->keptroles);
        }

        return $this->plan->execute();
    }

    /**
     * Gets the results of the plan execution for this backup.
     *
     * @return array
     */
    public function get_results() {
        return $this->plan->get_results();
    }

    /**
     * Save controller information
     *
     * @param bool $includeobj to decide if the object itself must be updated (true) or no (false)
     * @param bool $cleanobj to decide if the object itself must be cleaned (true) or no (false)
     */
    public function save_controller($includeobj = true, $cleanobj = false) {
        // Going to save controller to persistent storage, calculate checksum for later checks and save it.
        // TODO: flag the controller as NA. Any operation on it should be forbidden until loaded back.
        $this->log('saving controller to db', backup::LOG_DEBUG);
        if ($includeobj ) {  // Only calculate checksum if we are going to include the object.
            $this->checksum = $this->calculate_checksum();
        }
        backup_controller_dbops::save_controller($this, $this->checksum, $includeobj, $cleanobj);
    }

    /**
     * Loads a backup controller from the database.
     *
     * @param string $backupid The id of the backup controller.
     * @return \backup_controller
     */
    public static function load_controller($backupid) {
        // Load controller from persistent storage
        // TODO: flag the controller as available. Operations on it can continue
        $controller = backup_controller_dbops::load_controller($backupid);
        $controller->log('loading controller from db', backup::LOG_DEBUG);
        return $controller;
    }

// Protected API starts here

    /**
     * Creates a unique id for this backup.
     */
    protected function calculate_backupid() {
        // Current epoch time + type + id + format + interactive + mode + userid + operation
        // should be unique enough. Add one random part at the end
        $this->backupid = md5(time() . '-' . $this->type . '-' . $this->id . '-' . $this->format . '-' .
                              $this->interactive . '-' . $this->mode . '-' . $this->userid . '-' .
                              $this->operation . '-' . random_string(20));
    }

    /**
     * Builds the plan for this backup job so that it may be executed.
     */
    protected function load_plan() {
        $this->log('loading controller plan', backup::LOG_DEBUG);
        $this->plan = new backup_plan($this);
        $this->plan->build(); // Build plan for this controller
        $this->set_status(backup::STATUS_PLANNED);
    }

    /**
     * Sets default values for the backup controller.
     */
    protected function apply_defaults() {
        $this->log('applying plan defaults', backup::LOG_DEBUG);
        backup_controller_dbops::apply_config_defaults($this);
        $this->set_status(backup::STATUS_CONFIGURED);
        $this->set_include_files($this->get_include_files_default());
    }

    /**
     * Set the initial value for the include_files setting.
     *
     * @param bool $includefiles
     * @see backup_controller::get_include_files for further information on the purpose of this setting.
     */
    protected function set_include_files(bool $includefiles) {
        $this->log("setting file inclusion to {$this->includefiles}", backup::LOG_DEBUG);
        $this->includefiles = (int) $includefiles;
    }
}

/**
 * Exception class used by all the @backup_controller stuff
 */
class backup_controller_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
