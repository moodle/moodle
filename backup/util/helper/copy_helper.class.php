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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Copy helper class.
 *
 * @package    core_backup
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class copy_helper {

    /**
     * Process raw form data from copy_form.
     *
     * @param \stdClass $formdata Raw formdata
     * @return \stdClass Processed data for use with create_copy
     */
    public static function process_formdata(\stdClass $formdata): \stdClass {
        $requiredfields = [
            'courseid',  // Course id integer.
            'fullname', // Fullname of the destination course.
            'shortname', // Shortname of the destination course.
            'category', // Category integer ID that contains the destination course.
            'visible', // Integer to detrmine of the copied course will be visible.
            'startdate', // Integer timestamp of the start of the destination course.
            'enddate', // Integer timestamp of the end of the destination course.
            'idnumber', // ID of the destination course.
            'userdata', // Integer to determine if the copied course will contain user data.
        ];

        $missingfields = array_diff($requiredfields, array_keys((array)$formdata));
        if ($missingfields) {
            throw new \moodle_exception('copyfieldnotfound', 'backup', '', null, implode(", ", $missingfields));
        }

        // Remove any extra stuff in the form data.
        $processed = (object)array_intersect_key((array)$formdata, array_flip($requiredfields));
        $processed->keptroles = [];

        // Extract roles from the form data and add to keptroles.
        foreach ($formdata as $key => $value) {
            if ((substr($key, 0, 5) === 'role_') && ($value != 0)) {
                $processed->keptroles[] = $value;
            }
        }

        return $processed;
    }

    /**
     * Creates a course copy.
     * Sets up relevant controllers and adhoc task.
     *
     * @param \stdClass $copydata Course copy data from process_formdata
     * @return array $copyids The backup and restore controller ids
     */
    public static function create_copy(\stdClass $copydata): array {
        global $USER;
        $copyids = [];

        // Create the initial backupcontoller.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $copydata->courseid, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_COPY, $USER->id, \backup::RELEASESESSION_YES);
        $copyids['backupid'] = $bc->get_backupid();

        // Create the initial restore contoller.
        list($fullname, $shortname) = \restore_dbops::calculate_course_names(
            0, get_string('copyingcourse', 'backup'), get_string('copyingcourseshortname', 'backup'));
        $newcourseid = \restore_dbops::create_new_course($fullname, $shortname, $copydata->category);
        $rc = new \restore_controller($copyids['backupid'], $newcourseid, \backup::INTERACTIVE_NO,
            \backup::MODE_COPY, $USER->id, \backup::TARGET_NEW_COURSE, null,
            \backup::RELEASESESSION_NO, $copydata);
        $copyids['restoreid'] = $rc->get_restoreid();

        $bc->set_status(\backup::STATUS_AWAITING);
        $bc->get_status();
        $rc->save_controller();

        // Create the ad-hoc task to perform the course copy.
        $asynctask = new \core\task\asynchronous_copy_task();
        $asynctask->set_custom_data($copyids);
        \core\task\manager::queue_adhoc_task($asynctask);

        // Clean up the controller.
        $bc->destroy();

        return $copyids;
    }

    /**
     * Get the in progress course copy operations for a user.
     *
     * @param int $userid User id to get the course copies for.
     * @param int|null $courseid The optional source course id to get copies for.
     * @return array $copies Details of the inprogress copies.
     */
    public static function get_copies(int $userid, ?int $courseid = null): array {
        global $DB;
        $copies = [];
        [$insql, $inparams] = $DB->get_in_or_equal([\backup::STATUS_FINISHED_OK, \backup::STATUS_FINISHED_ERR]);
        $params = [
            $userid,
            \backup::EXECUTION_DELAYED,
            \backup::MODE_COPY,
            \backup::OPERATION_BACKUP,
            \backup::STATUS_FINISHED_OK,
            \backup::OPERATION_RESTORE
        ];

        // We exclude backups that finished with OK. Therefore if a backup is missing,
        // we can assume it finished properly.
        //
        // We exclude both failed and successful restores because both of those indicate that the whole
        // operation has completed.
        $sql = 'SELECT backupid, itemid, operation, status, timecreated, purpose
                  FROM {backup_controllers}
                 WHERE userid = ?
                       AND execution = ?
                       AND purpose = ?
                       AND ((operation = ? AND status <> ?) OR (operation = ? AND status NOT ' . $insql .'))
              ORDER BY timecreated DESC';

        $copyrecords = $DB->get_records_sql($sql, array_merge($params, $inparams));
        $idtorc = self::map_backupids_to_restore_controller($copyrecords);

        // Our SQL only gets controllers that have not finished successfully.
        // So, no restores => all restores have finished (either failed or OK) => all backups have too
        // Therefore there are no in progress copy operations, return early.
        if (empty($idtorc)) {
            return [];
        }

        foreach ($copyrecords as $copyrecord) {
            try {
                $isbackup = $copyrecord->operation == \backup::OPERATION_BACKUP;

                // The mapping is guaranteed to exist for restore controllers, but not
                // backup controllers.
                //
                // When processing backups we don't actually need it, so we just coalesce
                // to null.
                $rc = $idtorc[$copyrecord->backupid] ?? null;

                $cid = $isbackup ? $copyrecord->itemid : $rc->get_copy()->courseid;
                $course = get_course($cid);
                $copy = clone ($copyrecord);
                $copy->backupid = $isbackup ? $copyrecord->backupid : null;
                $copy->restoreid = $rc ? $rc->get_restoreid() : null;
                $copy->destination = $rc ? $rc->get_copy()->shortname : null;
                $copy->source = $course->shortname;
                $copy->sourceid = $course->id;
            } catch (\Exception $e) {
                continue;
            }

            // Filter out anything that's not relevant.
            if ($courseid) {
                if ($isbackup && $copyrecord->itemid != $courseid) {
                    continue;
                }

                if (!$isbackup && $rc->get_copy()->courseid != $courseid) {
                    continue;
                }
            }

            // A backup here means that the associated restore controller has not started.
            //
            // There's a few situations to consider:
            //
            // 1. The backup is waiting or in progress
            // 2. The backup failed somehow
            // 3. Something went wrong (e.g., solar flare) and the backup controller saved, but the restore controller didn't
            // 4. The restore hasn't been created yet (race condition)
            //
            // In the case of 1, we add it to the return list. In the case of 2, 3 and 4 we just ignore it and move on.
            // The backup cleanup task will take care of updating/deleting invalid controllers.
            if ($isbackup) {
                if ($copyrecord->status != \backup::STATUS_FINISHED_ERR && !is_null($rc)) {
                    $copies[] = $copy;
                }

                continue;
            }

            // A backup in copyrecords, indicates that the associated backup has not
            // successfully finished. We shouldn't do anything with this restore record.
            if ($copyrecords[$rc->get_tempdir()] ?? null) {
                continue;
            }

            // This is a restore record, and the backup has finished. Return it.
            $copies[] = $copy;
        }

        return $copies;
    }

    /**
     * Returns a mapping between copy controller IDs and the restore controller.
     * For example if there exists a copy with backup ID abc and restore ID 123
     * then this mapping will map both keys abc and 123 to the same (instantiated)
     * restore controller.
     *
     * @param array $backuprecords An array of records from {backup_controllers}
     * @return array An array of mappings between backup ids and restore controllers
     */
    private static function map_backupids_to_restore_controller(array $backuprecords): array {
        // Needed for PHP 7.3 - array_merge only accepts 0 parameters in PHP >= 7.4.
        if (empty($backuprecords)) {
            return [];
        }

        return array_merge(
            ...array_map(
                function (\stdClass $backuprecord): array {
                    $iscopyrestore = $backuprecord->operation == \backup::OPERATION_RESTORE &&
                            $backuprecord->purpose == \backup::MODE_COPY;
                    $isfinished = $backuprecord->status == \backup::STATUS_FINISHED_OK;

                    if (!$iscopyrestore || $isfinished) {
                        return [];
                    }

                    $rc = \restore_controller::load_controller($backuprecord->backupid);
                    return [$backuprecord->backupid => $rc, $rc->get_tempdir() => $rc];
                },
                array_values($backuprecords)
            )
        );
    }

    /**
     * Detects and deletes/fails controllers associated with a course copy that are
     * in an invalid state.
     *
     * @param array $backuprecords An array of records from {backup_controllers}
     * @param int $age How old a controller needs to be (in seconds) before its considered for cleaning
     * @return void
     */
    public static function cleanup_orphaned_copy_controllers(array $backuprecords, int $age = MINSECS): void {
        global $DB;

        $idtorc = self::map_backupids_to_restore_controller($backuprecords);

        // Helpful to test if a backup exists in $backuprecords.
        $bidstorecord = array_combine(
            array_column($backuprecords, 'backupid'),
            $backuprecords
        );

        foreach ($backuprecords as $record) {
            if ($record->purpose != \backup::MODE_COPY || $record->status == \backup::STATUS_FINISHED_OK) {
                continue;
            }

            $isbackup = $record->operation == \backup::OPERATION_BACKUP;
            $restoreexists = isset($idtorc[$record->backupid]);
            $nsecondsago = time() - $age;

            if ($isbackup) {
                // Sometimes the backup controller gets created, ""something happens"" (like a solar flare)
                // and the restore controller (and hence adhoc task) don't.
                //
                // If more than one minute has passed and the restore controller doesn't exist, it's likely that
                // this backup controller is orphaned, so we should remove it as the adhoc task to process it will
                // never be created.
                if (!$restoreexists && $record->timecreated <= $nsecondsago) {
                    // It would be better to mark the backup as failed by loading the controller
                    // and marking it as failed with $bc->set_status(), but we can't: MDL-74711.
                    //
                    // Deleting it isn't ideal either as maybe we want to inspect the backup
                    // for debugging. So manually updating the column seems to be the next best.
                    $record->status = \backup::STATUS_FINISHED_ERR;
                    $DB->update_record('backup_controllers', $record);
                }
                continue;
            }

            if ($rc = $idtorc[$record->backupid] ?? null) {
                $backuprecord = $bidstorecord[$rc->get_tempdir()] ?? null;

                // Check the status of the associated backup. If it's failed, then mark this
                // restore as failed too.
                if ($backuprecord && $backuprecord->status == \backup::STATUS_FINISHED_ERR) {
                    $rc->set_status(\backup::STATUS_FINISHED_ERR);
                }
            }
        }
    }
}
