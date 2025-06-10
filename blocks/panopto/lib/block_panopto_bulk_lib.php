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
 * Main library functions for the Panoptop block bulk operations.
 *
 * Warning: These operations can work on many Panopto mappings for many Panopto servers at one time.
 *   Please make sure the user performing this work is an administrator on all Panopto servers to avoid
 *   an access headache.
 *
 * @package block_panopto
 * @copyright  Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Update the progress for Panopto bulk operation.
 *
 * @param int $currentprogress the current progress that the bar needs to reflect.
 * @param int $totalitems the total number of items in the current step to be processed.
 * @param int $progressstep if set update the progress step to this value.
 * @param int $taskstep which task step we are on currently.
 */
function panopto_update_task_progress($currentprogress, $totalitems, $progressstep, $taskstep) {
    if (isset($taskstep) && !empty($taskstep)) {
        \panopto_data::print_log(get_string('bulk_task_new_step', 'block_panopto', $taskstep));
    }

    if (!CLI_SCRIPT && $totalitems > 0) {
        $percentdone = (int)((float)$currentprogress / (float)$totalitems * 100.0);
        panopto_bulk_update_progress_bar($percentdone, $progressstep);
    }

    if ($currentprogress > 0) {
        $progressobject = new stdClass;
        $progressobject->currentprogress = $currentprogress;
        $progressobject->totalitems = $totalitems;
        \panopto_data::print_log(get_string('bulk_task_update_progress', 'block_panopto', $progressobject));
    }
}

/**
 * Checks to see if we need to throttle the bulk operation and does so if needed
 *
 * @param number $numberprocessed the current number of folders processed.
 */
function panopto_handle_bulk_throttle($numberprocessed) {
    if ($numberprocessed > 0 && $numberprocessed % 50 == 0) {
        // Put in a minor break between batches to not make API calls too quickly.
        sleep(10);
    }

    return ++$numberprocessed;
}

/**
 * Display the access error in the case the working user does not have the correct Panopto permissions
 *
 * @param string $targetuser the Panopto username for the current user (so the customer knows who to make an Admin)
 */
function panopto_bulk_display_access_error($targetuser) {
    if (!CLI_SCRIPT) {
        include('views/bulk_task_access_error.html.php');
    }

    \panopto_data::print_log(
        get_string(
            'bulk_task_access_error',
            'block_panopto',
            $targetuser
        )
    );
}

/**
 * Display progress bar for bulk update operation.
 *
 * @param int $percentdone % complete on the progress bar.
 * @param string $steptype progress step status such as 'validating', 'processing', etc.
 */
function panopto_bulk_update_progress_bar($percentdone, $steptype) {

    $periodstring = '';
    while (strlen($periodstring) < $percentdone % 4) {
        $periodstring .= '.';
    }
    // Javascript for updating the progress bar and information.
    $updatescriptstring = '<script language="javascript">' .
        'document.getElementById("panopto-progress-bar-' . $steptype . '").innerHTML="' .
        '<div style=\'width:'.$percentdone.'%;background-color: #228B22;z-index:0;\'>&nbsp;</div>' .
        '<span style=\'z-index: 1;position: absolute;top: 0px;transform: translateX(-50%);\'>' .
            $percentdone . '% done ' . $steptype . $periodstring .
        '</span>"' .
    '</script>';

    echo $updatescriptstring;
}

/**
 * This script will process all passed in folders using the passed in callback
 *
 * @param array $panoptocourseobjects all valid mappings ready to process
 * @param string $bulktaskname the name of the bulk task we are performing on the mappings
 * @param string $workercallback the name of the callback function we use to process a folder
 */
function panopto_bulk_process_folders($panoptocourseobjects, $bulktaskname, $workercallback) {
    $currindex = 0;
    $totaltaskfolders = count($panoptocourseobjects);
    if ($totaltaskfolders > 0) {
        panopto_update_task_progress($currindex, $totaltaskfolders, 'processing', $bulktaskname);
        foreach ($panoptocourseobjects as $mappablecourse) {

            $currindex = panopto_handle_bulk_throttle($currindex);

            panopto_update_task_progress($currindex, $totaltaskfolders, 'processing', null);

            call_user_func($workercallback, $mappablecourse);
        }
    }
}

/**
 * Callback used to bulk reprovision all folders and imports
 *
 * @param object $mappablecourse the current folder we are provisioning
 */
function panopto_bulk_reprovision_callback($mappablecourse) {

    $provisioningdata = $mappablecourse->provisioninginfo;
    $provisioneddata = $mappablecourse->panopto->provision_course($provisioningdata, true);

    if (!CLI_SCRIPT) {
        include('views/provisioned_course.html.php');
    }
    $mappablecourse->panopto->ensure_auth_manager();
    $activepanoptoserverversion = $mappablecourse->panopto->authmanager->get_server_version();
    $useccv2 = version_compare(
        $activepanoptoserverversion,
        \panopto_data::$ccv2requiredpanoptoversion,
        '>='
    );

    $courseimports = \panopto_data::get_import_list($mappablecourse->panopto->moodlecourseid);
    foreach ($courseimports as $importedcourse) {
        if ($useccv2) {
            $mappablecourse->panopto->copy_panopto_content($importedcourse);
        } else {
            $mappablecourse->panopto->init_and_sync_import_ccv1($importedcourse);
        }
    }
}

/**
 * Callback used to bulk update all folder names to match course names
 *
 * @param object $mappablecourse the current folder we are provisioning
 */
function panopto_bulk_rename_callback($mappablecourse) {
    // Display the name information for the user.
    // Provisioninginfo should still contain the original name.
    $currentfoldernamecontainer = new stdClass;
    $currentfoldernamecontainer->oldname = $mappablecourse->provisioninginfo->fullname;
    $currentfoldernamecontainer->moodleid = $mappablecourse->panopto->moodlecourseid;
    if ($currentfoldernamecontainer->oldname != $mappablecourse->panopto->get_new_folder_name(null, null)) {
        if ($mappablecourse->panopto->update_folder_name()) {

            // The currentcoursename should get updated during the update_folder_name call.
            $currentfoldernamecontainer->newname = $mappablecourse->panopto->currentcoursename;

            \panopto_data::print_log(
                get_string('bulk_rename_single_success', 'block_panopto', $currentfoldernamecontainer)
            );

            if (!CLI_SCRIPT) {
                include('views/bulk_rename_single_success.html.php');
            }
        } else {
            \panopto_data::print_log(
                get_string('bulk_rename_single_failed', 'block_panopto', $mappablecourse->panopto->moodlecourseid)
            );
            if (!CLI_SCRIPT) {
                include('views/bulk_rename_single_failed.html.php');
            }
        }
    } else {
        // The currentcoursename should get updated during the update_folder_name call.
        $currentfoldernamecontainer->newname = $mappablecourse->panopto->currentcoursename;

        \panopto_data::print_log(
            get_string('bulk_rename_single_unnecessary', 'block_panopto', $currentfoldernamecontainer->oldname)
        );

        if (!CLI_SCRIPT) {
            include('views/bulk_rename_single_success.html.php');
        }
    }
}

/**
 * Grabs every mapping existing in the Panopto FolderMap table and checks that the mapping is
 *   still in a good state (e.g. Folder Deleted in Panopto, Course in Moodle doesn't exist
 *   anymore, or any necessary DB fields still exist). Returns an array of Panopto mapping data
 *   for the mappings that are still in a good state or can be recovered by fresh provisining.
 *
 * @param array $params beginning index and number to process.
 * @param bool $skipimports whether or not to process imported folders.
 */
function panopto_bulk_sanitize_and_get_mappings($params, $skipimports) {
    global $DB;

    // Get all active courses mapped to Panopto.
    $oldpanoptocourses = $DB->get_records(
        'block_panopto_foldermap',
        null,
        null,
        'id,moodleid'
    );

    $currindex = 0;
    $totaltasksteps = count($oldpanoptocourses);

    $beginningindex = isset($params[1]) ? max(intval($params[1]), 1) : 1;
    $numbertoprocess = isset($params[2]) ? intval($params[2]) : $totaltasksteps;
    $endingindex = isset($params[2]) ? $beginningindex + intval($params[2]) : $totaltasksteps + 1;

    $processcountobject = new stdClass;
    $processcountobject->beginningindex = $beginningindex;
    $processcountobject->endingindex = $endingindex - 1;

    \panopto_data::print_log(get_string('bulk_task_working_count', 'block_panopto', $processcountobject));

    $upgradestep = get_string('verifying_permission', 'block_panopto');

    $panoptocourseobjects = [];

    $getunamepanopto = new \panopto_data(null);

    $numberprocessed = 0;
    panopto_update_task_progress($numberprocessed, $numbertoprocess, 'validating', $upgradestep);
    foreach ($oldpanoptocourses as $oldcourse) {
        ++$currindex;

        if ($currindex < $beginningindex) {
            \panopto_data::print_log(get_string('bulk_task_skipping_folder', 'block_panopto', $currindex));
            continue;
        } else if ($currindex >= $endingindex) {
            \panopto_data::print_log(get_string('bulk_task_reached_count', 'block_panopto'));
            break;
        }

        $numberprocessed = panopto_handle_bulk_throttle($numberprocessed);
        panopto_update_task_progress($numberprocessed, $numbertoprocess, 'validating', null);

        $oldpanoptocourse = new stdClass;
        $oldpanoptocourse->panopto = new \panopto_data($oldcourse->moodleid);

        $existingmoodlecourse = $DB->get_record('course', ['id' => $oldcourse->moodleid]);

        $moodlecourseexists = isset($existingmoodlecourse) && $existingmoodlecourse !== false;
        $hasvalidpanoptodata = isset($oldpanoptocourse->panopto->servername) && !empty($oldpanoptocourse->panopto->servername) &&
            isset($oldpanoptocourse->panopto->applicationkey) && !empty($oldpanoptocourse->panopto->applicationkey);

        if ($moodlecourseexists && $hasvalidpanoptodata) {
            if (isset($oldpanoptocourse->panopto->uname) && !empty($oldpanoptocourse->panopto->uname)) {
                $oldpanoptocourse->panopto->ensure_auth_manager();
                $activepanoptoserverversion = $oldpanoptocourse->panopto->authmanager->get_server_version();
                if (!version_compare($activepanoptoserverversion, \panopto_data::$requiredpanoptoversion, '>=')) {
                    if (!CLI_SCRIPT) {
                        include('views/bulk_task_version_error.html.php');
                    }

                    \panopto_data::print_log(
                        get_string('bulk_task_version_error', 'block_panopto') . '\n' .
                        get_string('impacted_server', 'block_panopto', $oldpanoptocourse->panopto->servername) . '\n' .
                        get_string('minimum_required_version', 'block_panopto', \panopto_data::$requiredpanoptoversion) . '\n' .
                        get_string('current_version', 'block_panopto', $activepanoptoserverversion)
                    );
                }
            } else {

                panopto_bulk_display_access_error($getunamepanopto->panopto_decorate_username($getunamepanopto->uname));

                // If the user does not have access on even one of the folders return nothing.
                return [];
            }
        } else {
            // Shouldn't hit this case, but in the case a row in the DB has invalid data move it to the old_foldermap.
            \panopto_data::print_log(get_string('bulk_task_removing_bad_folder_row', 'block_panopto', $oldcourse->moodleid));
            \panopto_data::delete_panopto_relation($oldcourse->moodleid, true);
            // Continue to the next entry assuming this one was cleanup.
            continue;
        }

        $oldpanoptocourse->provisioninginfo = $oldpanoptocourse->panopto->get_provisioning_info();
        if (isset($oldpanoptocourse->provisioninginfo->accesserror) &&
            $oldpanoptocourse->provisioninginfo->accesserror === true) {

            panopto_bulk_display_access_error($getunamepanopto->panopto_decorate_username($getunamepanopto->uname));

            // If the user does not have access on even one of the folders return nothing.
            return [];
        } else {
            if (isset($oldpanoptocourse->provisioninginfo->couldnotfindmappedfolder) &&
                $oldpanoptocourse->provisioninginfo->couldnotfindmappedfolder === true) {
                // Course was mapped to a folder but that folder was not found, most likely folder was deleted on Panopto side.
                // The true parameter moves the row to the old_foldermap instead of deleting it.
                \panopto_data::delete_panopto_relation($oldcourse->moodleid, true);

                // Recreate the default role mappings that were deleted by the above line.
                $oldpanoptocourse->panopto->check_course_role_mappings();

                // Imports SHOULD still work for this case, so continue to below code.
            }

            if (!$skipimports) {
                $courseimports = \panopto_data::get_import_list($oldpanoptocourse->panopto->moodlecourseid);
                foreach ($courseimports as $courseimport) {
                    $importpanopto = new \panopto_data($courseimport);

                    $existingmoodlecourse = $DB->get_record('course', ['id' => $courseimport]);

                    $moodlecourseexists = isset($existingmoodlecourse) && $existingmoodlecourse !== false;
                    $hasvalidpanoptodata = isset($importpanopto->servername) && !empty($importpanopto->servername) &&
                        isset($importpanopto->applicationkey) && !empty($importpanopto->applicationkey);

                    // Only perform the actions below if the import is in a valid state, otherwise remove it.
                    if ($moodlecourseexists && $hasvalidpanoptodata) {
                        $importpanoptofolder = $importpanopto->get_folders_by_id_no_sync();

                        if ($importpanoptofolder == null ||
                            (isset($importpanoptofolder->notfound) &&
                            $importpanoptofolder->notfound == true)) {
                            // In this case the folder was not found, not an access issue.
                            // Most likely the folder was deleted and this is an old entry.
                            // Move the entry to the old_foldermap so user still has a reference.
                            \panopto_data::delete_panopto_relation($courseimport, true);
                            // We can still continue on with the upgrade.
                            // Assume this was an old entry that was deleted from Panopto side.
                        } else if (!empty($importpanoptofolder->errormessage)) {

                            // This case means the user failed to get the folder.
                            continue;
                        }
                    } else {
                        \panopto_data::print_log(get_string('bulk_task_removing_bad_folder_row', 'block_panopto', $courseimport));
                        \panopto_data::delete_panopto_relation($courseimport, true);
                        // Continue to the next entry assuming this one was cleanup.
                        continue;
                    }
                }
            }
        }
        $panoptocourseobjects[] = $oldpanoptocourse;
    }

    return $panoptocourseobjects;
}

/**
 * Gets all existing mapped panopto folders, verifies the folders existand then renames them
 * to match their mapped course name.
 *
 * @param array $params
 * $params[1] - The index you would like to start with in the rename process.
 * $params[2] - The number of folders you would like to work on this execution.
 */
function panopto_rename_all_folders($params) {
    // This can take a very long time if users have many folders.
    core_php_time_limit::raise();

    if (!CLI_SCRIPT) {
        include('views/bulk_task_progress_bar.html.php');
    }

    panopto_bulk_process_folders(
        panopto_bulk_sanitize_and_get_mappings($params, true),
        get_string('bulk_rename_start_renaming', 'block_panopto'),
        'panopto_bulk_rename_callback'
    );
}

/**
 * This script will reprovision all existing Panopto folders associated with this Moodle site.
 * For users experiencing issues with large amounts of courses
 * the following two parameters can be added to the CLI to batching purposes.
 *
 * e.g. upgrade_all_folders.php 550 500
 * This line will start at folder 550 and will process/upgrade up to folder 1050
 *
 * @param array $params
 * $params[1] - The index you would like to start with in the upgrade process.
 * $params[2] - The number of folders you would like to work on this execution.
 */
function panopto_upgrade_all_folders($params) {
    // This can take a very long time if users have many folders.
    core_php_time_limit::raise();

    if (!CLI_SCRIPT) {
        include('views/bulk_task_progress_bar.html.php');
    }

    // We do not want to skip imports when bulk reprovisioning all folders.
    panopto_bulk_process_folders(
        panopto_bulk_sanitize_and_get_mappings($params, false),
        get_string('bulk_reprovision_begin_reprovision', 'block_panopto'),
        'panopto_bulk_reprovision_callback'
    );
}
/* End of file block_panopto_bulk_lib.php */
