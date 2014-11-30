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
 * Utility helper for automated backups run through cron.
 *
 * @package    core
 * @subpackage backup
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class is an abstract class with methods that can be called to aid the
 * running of automated backups over cron.
 */
abstract class backup_cron_automated_helper {

    /** Automated backups are active and ready to run */
    const STATE_OK = 0;
    /** Automated backups are disabled and will not be run */
    const STATE_DISABLED = 1;
    /** Automated backups are all ready running! */
    const STATE_RUNNING = 2;

    /** Course automated backup completed successfully */
    const BACKUP_STATUS_OK = 1;
    /** Course automated backup errored */
    const BACKUP_STATUS_ERROR = 0;
    /** Course automated backup never finished */
    const BACKUP_STATUS_UNFINISHED = 2;
    /** Course automated backup was skipped */
    const BACKUP_STATUS_SKIPPED = 3;
    /** Course automated backup had warnings */
    const BACKUP_STATUS_WARNING = 4;
    /** Course automated backup has yet to be run */
    const BACKUP_STATUS_NOTYETRUN = 5;

    /** Run if required by the schedule set in config. Default. **/
    const RUN_ON_SCHEDULE = 0;
    /** Run immediately. **/
    const RUN_IMMEDIATELY = 1;

    const AUTO_BACKUP_DISABLED = 0;
    const AUTO_BACKUP_ENABLED = 1;
    const AUTO_BACKUP_MANUAL = 2;

    /**
     * Runs the automated backups if required
     *
     * @global moodle_database $DB
     */
    public static function run_automated_backup($rundirective = self::RUN_ON_SCHEDULE) {
        global $CFG, $DB;

        $status = true;
        $emailpending = false;
        $now = time();
        $config = get_config('backup');

        mtrace("Checking automated backup status",'...');
        $state = backup_cron_automated_helper::get_automated_backup_state($rundirective);
        if ($state === backup_cron_automated_helper::STATE_DISABLED) {
            mtrace('INACTIVE');
            return $state;
        } else if ($state === backup_cron_automated_helper::STATE_RUNNING) {
            mtrace('RUNNING');
            if ($rundirective == self::RUN_IMMEDIATELY) {
                mtrace('Automated backups are already running. If this script is being run by cron this constitues an error. You will need to increase the time between executions within cron.');
            } else {
                mtrace("automated backup are already running. Execution delayed");
            }
            return $state;
        } else {
            mtrace('OK');
        }
        backup_cron_automated_helper::set_state_running();

        mtrace("Getting admin info");
        $admin = get_admin();
        if (!$admin) {
            mtrace("Error: No admin account was found");
            $state = false;
        }

        if ($status) {
            mtrace("Checking courses");
            mtrace("Skipping deleted courses", '...');
            mtrace(sprintf("%d courses", backup_cron_automated_helper::remove_deleted_courses_from_schedule()));
        }

        if ($status) {

            mtrace('Running required automated backups...');
            cron_trace_time_and_memory();

            // This could take a while!
            core_php_time_limit::raise();
            raise_memory_limit(MEMORY_EXTRA);

            $nextstarttime = backup_cron_automated_helper::calculate_next_automated_backup($admin->timezone, $now);
            $showtime = "undefined";
            if ($nextstarttime > 0) {
                $showtime = date('r', $nextstarttime);
            }

            $rs = $DB->get_recordset('course');
            foreach ($rs as $course) {
                $backupcourse = $DB->get_record('backup_courses', array('courseid' => $course->id));
                if (!$backupcourse) {
                    $backupcourse = new stdClass;
                    $backupcourse->courseid = $course->id;
                    $backupcourse->laststatus = self::BACKUP_STATUS_NOTYETRUN;
                    $DB->insert_record('backup_courses', $backupcourse);
                    $backupcourse = $DB->get_record('backup_courses', array('courseid' => $course->id));
                }

                // The last backup is considered as successful when OK or SKIPPED.
                $lastbackupwassuccessful =  ($backupcourse->laststatus == self::BACKUP_STATUS_SKIPPED ||
                                            $backupcourse->laststatus == self::BACKUP_STATUS_OK) && (
                                            $backupcourse->laststarttime > 0 && $backupcourse->lastendtime > 0);

                // Assume that we are not skipping anything.
                $skipped = false;
                $skippedmessage = '';

                // Check if we are going to be running the backup now.
                $shouldrunnow = (($backupcourse->nextstarttime > 0 && $backupcourse->nextstarttime < $now)
                    || $rundirective == self::RUN_IMMEDIATELY);

                // If config backup_auto_skip_hidden is set to true, skip courses that are not visible.
                if ($shouldrunnow && $config->backup_auto_skip_hidden) {
                    $skipped = ($config->backup_auto_skip_hidden && !$course->visible);
                    $skippedmessage = 'Not visible';
                }

                // If config backup_auto_skip_modif_days is set to true, skip courses
                // that have not been modified since the number of days defined.
                if ($shouldrunnow && !$skipped && $lastbackupwassuccessful && $config->backup_auto_skip_modif_days) {
                    $timenotmodifsincedays = $now - ($config->backup_auto_skip_modif_days * DAYSECS);
                    // Check log if there were any modifications to the course content.
                    $logexists = self::is_course_modified($course->id, $timenotmodifsincedays);
                    $skipped = ($course->timemodified <= $timenotmodifsincedays && !$logexists);
                    $skippedmessage = 'Not modified in the past '.$config->backup_auto_skip_modif_days.' days';
                }

                // If config backup_auto_skip_modif_prev is set to true, skip courses
                // that have not been modified since previous backup.
                if ($shouldrunnow && !$skipped && $lastbackupwassuccessful && $config->backup_auto_skip_modif_prev) {
                    // Check log if there were any modifications to the course content.
                    $logexists = self::is_course_modified($course->id, $backupcourse->laststarttime);
                    $skipped = ($course->timemodified <= $backupcourse->laststarttime && !$logexists);
                    $skippedmessage = 'Not modified since previous backup';
                }

                // Check if the course is not scheduled to run right now.
                if (!$shouldrunnow) {
                    $backupcourse->nextstarttime = $nextstarttime;
                    $DB->update_record('backup_courses', $backupcourse);
                    mtrace('Skipping ' . $course->fullname . ' (Not scheduled for backup until ' . $showtime . ')');
                } else if ($skipped) { // Must have been skipped for a reason.
                    $backupcourse->laststatus = self::BACKUP_STATUS_SKIPPED;
                    $backupcourse->nextstarttime = $nextstarttime;
                    $DB->update_record('backup_courses', $backupcourse);
                    mtrace('Skipping ' . $course->fullname . ' (' . $skippedmessage . ')');
                    mtrace('Backup of \'' . $course->fullname . '\' is scheduled on ' . $showtime);
                } else {
                    // Backup every non-skipped courses.
                    mtrace('Backing up '.$course->fullname.'...');

                    // We have to send an email because we have included at least one backup.
                    $emailpending = true;

                    // Only make the backup if laststatus isn't 2-UNFINISHED (uncontrolled error).
                    if ($backupcourse->laststatus != self::BACKUP_STATUS_UNFINISHED) {
                        // Set laststarttime.
                        $starttime = time();

                        $backupcourse->laststarttime = time();
                        $backupcourse->laststatus = self::BACKUP_STATUS_UNFINISHED;
                        $DB->update_record('backup_courses', $backupcourse);

                        $backupcourse->laststatus = backup_cron_automated_helper::launch_automated_backup($course, $backupcourse->laststarttime, $admin->id);
                        $backupcourse->lastendtime = time();
                        $backupcourse->nextstarttime = $nextstarttime;

                        $DB->update_record('backup_courses', $backupcourse);

                        if ($backupcourse->laststatus === self::BACKUP_STATUS_OK) {
                            // Clean up any excess course backups now that we have
                            // taken a successful backup.
                            $removedcount = backup_cron_automated_helper::remove_excess_backups($course);
                        }
                    }

                    mtrace("complete - next execution: $showtime");
                }
            }
            $rs->close();
        }

        //Send email to admin if necessary
        if ($emailpending) {
            mtrace("Sending email to admin");
            $message = "";

            $count = backup_cron_automated_helper::get_backup_status_array();
            $haserrors = ($count[self::BACKUP_STATUS_ERROR] != 0 || $count[self::BACKUP_STATUS_UNFINISHED] != 0);

            // Build the message text.
            // Summary.
            $message .= get_string('summary') . "\n";
            $message .= "==================================================\n";
            $message .= '  ' . get_string('courses') . '; ' . array_sum($count) . "\n";
            $message .= '  ' . get_string('ok') . '; ' . $count[self::BACKUP_STATUS_OK] . "\n";
            $message .= '  ' . get_string('skipped') . '; ' . $count[self::BACKUP_STATUS_SKIPPED] . "\n";
            $message .= '  ' . get_string('error') . '; ' . $count[self::BACKUP_STATUS_ERROR] . "\n";
            $message .= '  ' . get_string('unfinished') . '; ' . $count[self::BACKUP_STATUS_UNFINISHED] . "\n";
            $message .= '  ' . get_string('warning') . '; ' . $count[self::BACKUP_STATUS_WARNING] . "\n";
            $message .= '  ' . get_string('backupnotyetrun') . '; ' . $count[self::BACKUP_STATUS_NOTYETRUN]."\n\n";

            //Reference
            if ($haserrors) {
                $message .= "  ".get_string('backupfailed')."\n\n";
                $dest_url = "$CFG->wwwroot/report/backups/index.php";
                $message .= "  ".get_string('backuptakealook','',$dest_url)."\n\n";
                //Set message priority
                $admin->priority = 1;
                //Reset unfinished to error
                $DB->set_field('backup_courses','laststatus','0', array('laststatus'=>'2'));
            } else {
                $message .= "  ".get_string('backupfinished')."\n";
            }

            //Build the message subject
            $site = get_site();
            $prefix = format_string($site->shortname, true, array('context' => context_course::instance(SITEID))).": ";
            if ($haserrors) {
                $prefix .= "[".strtoupper(get_string('error'))."] ";
            }
            $subject = $prefix.get_string('automatedbackupstatus', 'backup');

            //Send the message
            $eventdata = new stdClass();
            $eventdata->modulename        = 'moodle';
            $eventdata->userfrom          = $admin;
            $eventdata->userto            = $admin;
            $eventdata->subject           = $subject;
            $eventdata->fullmessage       = $message;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';

            $eventdata->component         = 'moodle';
            $eventdata->name         = 'backup';

            message_send($eventdata);
        }

        //Everything is finished stop backup_auto_running
        backup_cron_automated_helper::set_state_running(false);

        mtrace('Automated backups complete.');

        return $status;
    }

    /**
     * Gets the results from the last automated backup that was run based upon
     * the statuses of the courses that were looked at.
     *
     * @global moodle_database $DB
     * @return array
     */
    public static function get_backup_status_array() {
        global $DB;

        $result = array(
            self::BACKUP_STATUS_ERROR => 0,
            self::BACKUP_STATUS_OK => 0,
            self::BACKUP_STATUS_UNFINISHED => 0,
            self::BACKUP_STATUS_SKIPPED => 0,
            self::BACKUP_STATUS_WARNING => 0,
            self::BACKUP_STATUS_NOTYETRUN => 0
        );

        $statuses = $DB->get_records_sql('SELECT DISTINCT bc.laststatus, COUNT(bc.courseid) AS statuscount FROM {backup_courses} bc GROUP BY bc.laststatus');

        foreach ($statuses as $status) {
            if (empty($status->statuscount)) {
                $status->statuscount = 0;
            }
            $result[(int)$status->laststatus] += $status->statuscount;
        }

        return $result;
    }

    /**
     * Works out the next time the automated backup should be run.
     *
     * @param mixed $timezone user timezone
     * @param int $now timestamp, should not be in the past, most likely time()
     * @return int timestamp of the next execution at server time
     */
    public static function calculate_next_automated_backup($timezone, $now) {

        $result = 0;
        $config = get_config('backup');
        $autohour = $config->backup_auto_hour;
        $automin = $config->backup_auto_minute;

        // Gets the user time relatively to the server time.
        $date = usergetdate($now, $timezone);
        $usertime = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);
        $diff = $now - $usertime;

        // Get number of days (from user's today) to execute backups.
        $automateddays = substr($config->backup_auto_weekdays, $date['wday']) . $config->backup_auto_weekdays;
        $daysfromnow = strpos($automateddays, "1");

        // Error, there are no days to schedule the backup for.
        if ($daysfromnow === false) {
            return 0;
        }

        // Checks if the date would happen in the future (of the user).
        $userresult = mktime($autohour, $automin, 0, $date['mon'], $date['mday'] + $daysfromnow, $date['year']);
        if ($userresult <= $usertime) {
            // If not, we skip the first scheduled day, that should fix it.
            $daysfromnow = strpos($automateddays, "1", 1);
            $userresult = mktime($autohour, $automin, 0, $date['mon'], $date['mday'] + $daysfromnow, $date['year']);
        }

        // Now we generate the time relative to the server.
        $result = $userresult + $diff;

        // If that time is past, call the function recursively to obtain the next valid day.
        if ($result <= $now) {
            // Checking time() in here works, but makes PHPUnit Tests extremely hard to predict.
            // $now should never be earlier than time() anyway...
            $result = self::calculate_next_automated_backup($timezone, $now + DAYSECS);
        }

        return $result;
    }

    /**
     * Launches a automated backup routine for the given course
     *
     * @param stdClass $course
     * @param int $starttime
     * @param int $userid
     * @return bool
     */
    public static function launch_automated_backup($course, $starttime, $userid) {

        $outcome = self::BACKUP_STATUS_OK;
        $config = get_config('backup');
        $dir = $config->backup_auto_destination;
        $storage = (int)$config->backup_auto_storage;

        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO,
                backup::MODE_AUTOMATED, $userid);

        try {

            // Set the default filename.
            $format = $bc->get_format();
            $type = $bc->get_type();
            $id = $bc->get_id();
            $users = $bc->get_plan()->get_setting('users')->get_value();
            $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
            $bc->get_plan()->get_setting('filename')->set_value(backup_plan_dbops::get_default_backup_filename($format, $type,
                    $id, $users, $anonymised));

            $bc->set_status(backup::STATUS_AWAITING);

            $bc->execute_plan();
            $results = $bc->get_results();
            $outcome = self::outcome_from_results($results);
            $file = $results['backup_destination']; // May be empty if file already moved to target location.

            if (empty($dir) && $storage !== 0) {
                // This is intentionally left as a warning instead of an error because of the current behaviour of backup settings.
                // See MDL-48266 for details.
                $bc->log('No directory specified for automated backups',
                        backup::LOG_WARNING);
                $outcome = self::BACKUP_STATUS_WARNING;
            } else if (!file_exists($dir) || !is_dir($dir) || !is_writable($dir) && $storage !== 0) {
                // If we need to copy the backup file to an external dir and it is not writable, change status to error.
                $bc->log('Specified backup directory is not writable - ',
                        backup::LOG_ERROR, $dir);
                $dir = null;
                $outcome = self::BACKUP_STATUS_ERROR;
            }

            // Copy file only if there was no error.
            if ($file && !empty($dir) && $storage !== 0 && $outcome != self::BACKUP_STATUS_ERROR) {
                $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $course->id, $users, $anonymised,
                        !$config->backup_shortname);
                if (!$file->copy_content_to($dir.'/'.$filename)) {
                    $bc->log('Attempt to copy backup file to the specified directory failed - ',
                            backup::LOG_ERROR, $dir);
                    $outcome = self::BACKUP_STATUS_ERROR;
                }
                if ($outcome != self::BACKUP_STATUS_ERROR && $storage === 1) {
                    if (!$file->delete()) {
                        $outcome = self::BACKUP_STATUS_WARNING;
                        $bc->log('Attempt to delete the backup file from course automated backup area failed - ',
                                backup::LOG_WARNING, $file->get_filename());
                    }
                }
            }

        } catch (moodle_exception $e) {
            $bc->log('backup_auto_failed_on_course', backup::LOG_ERROR, $course->shortname); // Log error header.
            $bc->log('Exception: ' . $e->errorcode, backup::LOG_ERROR, $e->a, 1); // Log original exception problem.
            $bc->log('Debug: ' . $e->debuginfo, backup::LOG_DEBUG, null, 1); // Log original debug information.
            $outcome = self::BACKUP_STATUS_ERROR;
        }

        // Delete the backup file immediately if something went wrong.
        if ($outcome === self::BACKUP_STATUS_ERROR) {

            // Delete the file from file area if exists.
            if (!empty($file)) {
                $file->delete();
            }

            // Delete file from external storage if exists.
            if ($storage !== 0 && !empty($filename) && file_exists($dir.'/'.$filename)) {
                @unlink($dir.'/'.$filename);
            }
        }

        $bc->destroy();
        unset($bc);

        return $outcome;
    }

    /**
     * Returns the backup outcome by analysing its results.
     *
     * @param array $results returned by a backup
     * @return int {@link self::BACKUP_STATUS_OK} and other constants
     */
    public static function outcome_from_results($results) {
        $outcome = self::BACKUP_STATUS_OK;
        foreach ($results as $code => $value) {
            // Each possible error and warning code has to be specified in this switch
            // which basically analyses the results to return the correct backup status.
            switch ($code) {
                case 'missing_files_in_pool':
                    $outcome = self::BACKUP_STATUS_WARNING;
                    break;
            }
            // If we found the highest error level, we exit the loop.
            if ($outcome == self::BACKUP_STATUS_ERROR) {
                break;
            }
        }
        return $outcome;
    }

    /**
     * Removes deleted courses fromn the backup_courses table so that we don't
     * waste time backing them up.
     *
     * @global moodle_database $DB
     * @return int
     */
    public static function remove_deleted_courses_from_schedule() {
        global $DB;
        $skipped = 0;
        $sql = "SELECT bc.courseid FROM {backup_courses} bc WHERE bc.courseid NOT IN (SELECT c.id FROM {course} c)";
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $deletedcourse) {
            //Doesn't exist, so delete from backup tables
            $DB->delete_records('backup_courses', array('courseid'=>$deletedcourse->courseid));
            $skipped++;
        }
        $rs->close();
        return $skipped;
    }

    /**
     * Gets the state of the automated backup system.
     *
     * @global moodle_database $DB
     * @return int One of self::STATE_*
     */
    public static function get_automated_backup_state($rundirective = self::RUN_ON_SCHEDULE) {
        global $DB;

        $config = get_config('backup');
        $active = (int)$config->backup_auto_active;
        $weekdays = (string)$config->backup_auto_weekdays;

        // In case of automated backup also check that it is scheduled for at least one weekday.
        if ($active === self::AUTO_BACKUP_DISABLED ||
                ($rundirective == self::RUN_ON_SCHEDULE && $active === self::AUTO_BACKUP_MANUAL) ||
                ($rundirective == self::RUN_ON_SCHEDULE && strpos($weekdays, '1') === false)) {
            return self::STATE_DISABLED;
        } else if (!empty($config->backup_auto_running)) {
            // Detect if the backup_auto_running semaphore is a valid one
            // by looking for recent activity in the backup_controllers table
            // for backups of type backup::MODE_AUTOMATED
            $timetosee = 60 * 90; // Time to consider in order to clean the semaphore
            $params = array( 'purpose'   => backup::MODE_AUTOMATED, 'timetolook' => (time() - $timetosee));
            if ($DB->record_exists_select('backup_controllers',
                "operation = 'backup' AND type = 'course' AND purpose = :purpose AND timemodified > :timetolook", $params)) {
                return self::STATE_RUNNING; // Recent activity found, still running
            } else {
                // No recent activity found, let's clean the semaphore
                mtrace('Automated backups activity not found in last ' . (int)$timetosee/60 . ' minutes. Cleaning running status');
                backup_cron_automated_helper::set_state_running(false);
            }
        }
        return self::STATE_OK;
    }

    /**
     * Sets the state of the automated backup system.
     *
     * @param bool $running
     * @return bool
     */
    public static function set_state_running($running = true) {
        if ($running === true) {
            if (self::get_automated_backup_state() === self::STATE_RUNNING) {
                throw new backup_helper_exception('backup_automated_already_running');
            }
            set_config('backup_auto_running', '1', 'backup');
        } else {
            unset_config('backup_auto_running', 'backup');
        }
        return true;
    }

    /**
     * Removes excess backups from the external system and the local file system.
     *
     * The number of backups keep comes from $config->backup_auto_keep.
     *
     * @param stdClass $course object
     * @return bool
     */
    public static function remove_excess_backups($course) {
        $config = get_config('backup');
        $keep =     (int)$config->backup_auto_keep;
        $storage =  $config->backup_auto_storage;
        $dir =      $config->backup_auto_destination;

        if ($keep == 0) {
            // Means keep all backup files.
            return true;
        }

        if (!file_exists($dir) || !is_dir($dir) || !is_writable($dir)) {
            $dir = null;
        }

        // Clean up excess backups in the course backup filearea.
        if ($storage == 0 || $storage == 2) {
            $fs = get_file_storage();
            $context = context_course::instance($course->id);
            $component = 'backup';
            $filearea = 'automated';
            $itemid = 0;
            $files = array();
            // Store all the matching files into timemodified => stored_file array.
            foreach ($fs->get_area_files($context->id, $component, $filearea, $itemid) as $file) {
                $files[$file->get_timemodified()] = $file;
            }
            if (count($files) <= $keep) {
                // There are less matching files than the desired number to keep there is nothing to clean up.
                return 0;
            }
            // Sort by keys descending (newer to older filemodified).
            krsort($files);
            $remove = array_splice($files, $keep);
            foreach ($remove as $file) {
                $file->delete();
            }
            //mtrace('Removed '.count($remove).' old backup file(s) from the automated filearea');
        }

        // Clean up excess backups in the specified external directory.
        if (!empty($dir) && ($storage == 1 || $storage == 2)) {
            // Calculate backup filename regex, ignoring the date/time/info parts that can be
            // variable, depending of languages, formats and automated backup settings.
            $filename = backup::FORMAT_MOODLE . '-' . backup::TYPE_1COURSE . '-' . $course->id . '-';
            $regex = '#' . preg_quote($filename, '#') . '.*\.mbz$#';

            // Store all the matching files into filename => timemodified array.
            $files = array();
            foreach (scandir($dir) as $file) {
                // Skip files not matching the naming convention.
                if (!preg_match($regex, $file, $matches)) {
                    continue;
                }

                // Read the information contained in the backup itself.
                try {
                    $bcinfo = backup_general_helper::get_backup_information_from_mbz($dir . '/' . $file);
                } catch (backup_helper_exception $e) {
                    mtrace('Error: ' . $file . ' does not appear to be a valid backup (' . $e->errorcode . ')');
                    continue;
                }

                // Make sure this backup concerns the course and site we are looking for.
                if ($bcinfo->format === backup::FORMAT_MOODLE &&
                        $bcinfo->type === backup::TYPE_1COURSE &&
                        $bcinfo->original_course_id == $course->id &&
                        backup_general_helper::backup_is_samesite($bcinfo)) {
                    $files[$file] = $bcinfo->backup_date;
                }
            }
            if (count($files) <= $keep) {
                // There are less matching files than the desired number to keep there is nothing to clean up.
                return 0;
            }
            // Sort by values descending (newer to older filemodified).
            arsort($files);
            $remove = array_splice($files, $keep);
            foreach (array_keys($remove) as $file) {
                unlink($dir . '/' . $file);
            }
            //mtrace('Removed '.count($remove).' old backup file(s) from external directory');
        }

        return true;
    }

    /**
     * Check logs to find out if a course was modified since the given time.
     *
     * @param int $courseid course id to check
     * @param int $since timestamp, from which to check
     *
     * @return bool true if the course was modified, false otherwise. This also returns false if no readers are enabled. This is
     * intentional, since we cannot reliably determine if any modification was made or not.
     */
    protected static function is_course_modified($courseid, $since) {
        $logmang = get_log_manager();
        $readers = $logmang->get_readers('core\log\sql_select_reader');
        $where = "courseid = :courseid and timecreated > :since and crud <> 'r'";
        $params = array('courseid' => $courseid, 'since' => $since);
        foreach ($readers as $reader) {
            if ($reader->get_events_select_count($where, $params)) {
                return true;
            }
        }
        return false;
    }
}
