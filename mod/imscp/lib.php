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
 * Mandatory public API of imscp module
 *
 * @package mod_imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/deprecatedlib.php');

/**
 * List of features supported in IMS CP module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function imscp_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_INTERACTIVECONTENT;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param stdClass $data the data submitted from the reset course.
 * @return array status array
 */
function imscp_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function imscp_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function imscp_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add imscp instance.
 * @param object $data
 * @param object $mform
 * @return int new imscp instance id
 */
function imscp_add_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    $cmid = $data->coursemodule;

    $data->timemodified = time();
    $data->revision     = 1;
    $data->structure    = null;

    $data->id = $DB->insert_record('imscp', $data);

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    $context = context_module::instance($cmid);
    $imscp = $DB->get_record('imscp', array('id' => $data->id), '*', MUST_EXIST);

    if (!empty($data->package)) {
        // Save uploaded files to 'backup' filearea.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_imscp', 'backup', 1);
        file_save_draft_area_files($data->package, $context->id, 'mod_imscp', 'backup',
            1, array('subdirs' => 0, 'maxfiles' => 1));
        // Get filename of zip that was uploaded.
        $files = $fs->get_area_files($context->id, 'mod_imscp', 'backup', 1, '', false);
        if ($files) {
            // Extract package content to 'content' filearea.
            $package = reset($files);
            $packer = get_file_packer('application/zip');
            $package->extract_to_storage($packer, $context->id, 'mod_imscp', 'content', 1, '/');
            $structure = imscp_parse_structure($imscp, $context);
            $imscp->structure = is_array($structure) ? serialize($structure) : null;
            $DB->update_record('imscp', $imscp);
        }
    }

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'imscp', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update imscp instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function imscp_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    $cmid = $data->coursemodule;

    $data->timemodified = time();
    $data->id           = $data->instance;
    $data->structure   = null; // Better reparse structure after each update.

    $DB->update_record('imscp', $data);

    $context = context_module::instance($cmid);
    $imscp = $DB->get_record('imscp', array('id' => $data->id), '*', MUST_EXIST);

    if (!empty($data->package) && ($draftareainfo = file_get_draft_area_info($data->package)) &&
            $draftareainfo['filecount']) {
        $fs = get_file_storage();

        $imscp->revision++;
        $DB->update_record('imscp', $imscp);

        // Get a list of existing packages before adding new package.
        if ($imscp->keepold > -1) {
            $packages = $fs->get_area_files($context->id, 'mod_imscp', 'backup', false, "itemid ASC", false);
        } else {
            $packages = array();
        }

        file_save_draft_area_files($data->package, $context->id, 'mod_imscp', 'backup',
            $imscp->revision, array('subdirs' => 0, 'maxfiles' => 1));
        $files = $fs->get_area_files($context->id, 'mod_imscp', 'backup', $imscp->revision, '', false);
        $package = reset($files);

        // Purge all extracted content.
        $fs->delete_area_files($context->id, 'mod_imscp', 'content');

        // Extract package content.
        if ($package) {
            $packer = get_file_packer('application/zip');
            $package->extract_to_storage($packer, $context->id, 'mod_imscp', 'content', $imscp->revision, '/');
        }

        // Cleanup old package files, keep current + keep old.
        while ($packages and (count($packages) > $imscp->keepold)) {
            $package = array_shift($packages);
            $fs->delete_area_files($context->id, 'mod_imscp', 'backup', $package->get_itemid());
        }
    }

    $structure = imscp_parse_structure($imscp, $context);
    $imscp->structure = is_array($structure) ? serialize($structure) : null;
    $DB->update_record('imscp', $imscp);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'imscp', $imscp->id, $completiontimeexpected);

    return true;
}

/**
 * Delete imscp instance.
 * @param int $id
 * @return bool true
 */
function imscp_delete_instance($id) {
    global $DB;

    if (!$imscp = $DB->get_record('imscp', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('imscp', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'imscp', $id, null);

    // Note: all context files are deleted automatically.

    $DB->delete_records('imscp', array('id' => $imscp->id));

    return true;
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_imscp
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function imscp_get_file_areas($course, $cm, $context) {
    $areas = array();

    $areas['content'] = get_string('areacontent', 'imscp');
    $areas['backup']  = get_string('areabackup', 'imscp');

    return $areas;
}

/**
 * File browsing support for imscp module ontent area.
 *
 * @package  mod_imscp
 * @category files
 * @param file_browser $browser file browser
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function imscp_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    // Note: imscp_intro handled in file_browser automatically.

    if (!has_capability('moodle/course:managefiles', $context)) {
        // No peeking here for students!
        return null;
    }

    if ($filearea !== 'content' and $filearea !== 'backup') {
        return null;
    }

    require_once("$CFG->dirroot/mod/imscp/locallib.php");

    if (is_null($itemid)) {
        return new imscp_file_info($browser, $course, $cm, $context, $areas, $filearea, $itemid);
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$storedfile = $fs->get_file($context->id, 'mod_imscp', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }

    // Do not allow manual modification of any files!
    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $itemid, true, true, false, false); // No writing here!
}

/**
 * Serves the imscp files.
 *
 * @package  mod_imscp
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function imscp_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if ($filearea === 'content') {
        if (!has_capability('mod/imscp:view', $context)) {
            return false;
        }
        $revision = array_shift($args);
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        if ($relativepath === 'imsmanifest.xml') {
            if (!has_capability('moodle/course:managefiles', $context)) {
                // No stealing of detailed package info.
                return false;
            }
        }
        $fullpath = "/$context->id/mod_imscp/$filearea/$revision/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // Finally send the file.
        send_stored_file($file, null, 0, $forcedownload, $options);

    } else if ($filearea === 'backup') {
        if (!has_capability('moodle/course:managefiles', $context)) {
            // No stealing of package backups.
            return false;
        }
        $revision = array_shift($args);
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_imscp/$filearea/$revision/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // Finally send the file.
        send_stored_file($file, null, 0, $forcedownload, $options);

    } else {
        return false;
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array $modulepagetype list
 */
function imscp_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array('mod-imscp-*' => get_string('page-mod-imscp-x', 'imscp'));
    return $modulepagetype;
}

/**
 * Export imscp resource contents
 *
 * @param  stdClass $cm     Course module object
 * @param  string $baseurl  Base URL for file downloads
 * @return array of file content
 */
function imscp_export_contents($cm, $baseurl) {
    global $DB;

    $contents = array();
    $context = context_module::instance($cm->id);

    $imscp = $DB->get_record('imscp', array('id' => $cm->instance), '*', MUST_EXIST);

    // We export the IMSCP structure as json encoded string.
    $structure = array();
    $structure['type']         = 'content';
    $structure['filename']     = 'structure';
    $structure['filepath']     = '/';
    $structure['filesize']     = 0;
    $structure['fileurl']      = null;
    $structure['timecreated']  = $imscp->timemodified;
    $structure['timemodified'] = $imscp->timemodified;
    $structure['content']      = json_encode(unserialize_array($imscp->structure));
    $structure['sortorder']    = 0;
    $structure['userid']       = null;
    $structure['author']       = null;
    $structure['license']      = null;
    $contents[] = $structure;

    // Area files.
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_imscp', 'content', $imscp->revision, 'id ASC', false);
    foreach ($files as $fileinfo) {
        $file = array();
        $file['type']         = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = moodle_url::make_webservice_pluginfile_url(
                                    $context->id, 'mod_imscp', 'content', $imscp->revision,
                                    $fileinfo->get_filepath(), $fileinfo->get_filename())->out(false);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $file['mimetype']     = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $imscp   imscp object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function imscp_view($imscp, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $imscp->id
    );

    $event = \mod_imscp\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('imscp', $imscp);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function imscp_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_imscp_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory,
                                                      int $userid = 0) {
    $cm = get_fast_modinfo($event->courseid, $userid)->instances['imscp'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/imscp/view.php', ['id' => $cm->id]),
        1,
        true
    );
}
