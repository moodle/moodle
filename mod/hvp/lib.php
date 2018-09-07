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
 * Library of interface functions and constants for module hvp.
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the hvp specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once('autoloader.php');

 /* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function hvp_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * Saves a new instance of the hvp into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $hvp Submitted data from the form in mod_form.php
 * @return int The id of the newly inserted newmodule record
 */
function hvp_add_instance($hvp) {
    // Save content.
    $hvp->id = hvp_save_content($hvp);

    // Set and create grade item.
    hvp_grade_item_update($hvp);

    return $hvp->id;
}

/**
 * Updates an instance of the hvp in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $hvp An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function hvp_update_instance($hvp) {
    // Make ID available for core to save.
    $hvp->id = $hvp->instance;

    // Save content.
    hvp_save_content($hvp);
    hvp_grade_item_update($hvp);
    return true;
}

/**
 * Does the actual process of saving the H5P content that's submitted through
 * the activity form
 *
 * @param stdClass $hvp
 * @return int Content ID
 */
function hvp_save_content($hvp) {
    // Determine disabled content features.
    $hvp->disable = hvp_get_disabled_content_features($hvp);

    // Determine if we're uploading or creating.
    if ($hvp->h5paction === 'upload') {
        // Save uploaded package.
        $hvp->uploaded = true;
        $h5pstorage = \mod_hvp\framework::instance('storage');
        $h5pstorage->savePackage((array)$hvp);
        $hvp->id = $h5pstorage->contentId;
    } else {
        // Save newly created or edited content.
        $core = \mod_hvp\framework::instance();
        $editor = \mod_hvp\framework::instance('editor');

        if (!empty($hvp->id)) {
            // Load existing content to get old parameters for comparison.
            $content = $core->loadContent($hvp->id);
            $oldlib = $content['library'];
            $oldparams = json_decode($content['params']);
        }

        // Make params and library available for core to save.
        $hvp->params = $hvp->h5pparams;
        $hvp->library = H5PCore::libraryFromString($hvp->h5plibrary);
        $hvp->library['libraryId'] = $core->h5pF->getLibraryId($hvp->library['machineName'],
                                                               $hvp->library['majorVersion'],
                                                               $hvp->library['minorVersion']);

        $hvp->id = $core->saveContent((array)$hvp);
        // We need to process the parameters to move any images or files and
        // to determine which dependencies the content has.

        // Prepare current parameters.
        $params = json_decode($hvp->params);

        // Move any uploaded images or files. Determine content dependencies.
        $editor->processParameters($hvp, $hvp->library, $params,
                                   isset($oldlib) ? $oldlib : null,
                                   isset($oldparams) ? $oldparams : null);
    }

    return $hvp->id;
}

/**
 * Help determine which content features have been disabled through the
 * activity form submitted.
 *
 * @param stdClass $hvp
 * @return int Disabled flags
 */
function hvp_get_disabled_content_features($hvp) {
    $disablesettings = [
        \H5PCore::DISPLAY_OPTION_FRAME     => isset($hvp->frame) ? $hvp->frame : 0,
        \H5PCore::DISPLAY_OPTION_DOWNLOAD  => isset($hvp->export) ? $hvp->export : 0,
        \H5PCore::DISPLAY_OPTION_EMBED     => isset($hvp->embed) ? $hvp->embed : 0,
        \H5PCore::DISPLAY_OPTION_COPYRIGHT => isset($hvp->copyright) ? $hvp->copyright : 0,
    ];
    $core            = \mod_hvp\framework::instance();
    return $core->getStorableDisplayOptions($disablesettings, 0);
}

/**
 * Removes an instance of the hvp from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function hvp_delete_instance($id) {
    global $DB;

    // Load content record.
    if (! $hvp = $DB->get_record('hvp', array('id' => "$id"))) {
        return false;
    }

    // Load CM.
    $cm = \get_coursemodule_from_instance('hvp', $id);

    // Delete content.
    $h5pstorage = \mod_hvp\framework::instance('storage');
    $h5pstorage->deletePackage(array('id' => $hvp->id, 'slug' => $hvp->slug, 'coursemodule' => $cm->id));

    // Delete xAPI statements.
    $DB->delete_records('hvp_xapi_results', array (
      'content_id' => $hvp->id
    ));

    // Get library details.
    $library = $DB->get_record_sql(
            "SELECT machine_name AS name, major_version, minor_version
               FROM {hvp_libraries}
              WHERE id = ?",
            array($hvp->main_library_id)
    );

    // Log content delete.
    new \mod_hvp\event(
            'content', 'delete',
            $hvp->id, $hvp->name,
            $library->name, $library->major_version . '.' . $library->minor_version
    );

    return true;
}

/**
 * Serves the files from the hvp file areas
 *
 * @package mod_hvp
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the newmodule's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 *
 * @return true|false Success
 */
function hvp_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    switch ($filearea) {
        default:
            return false; // Invalid file area.

        case 'libraries':
        case 'cachedassets':
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                return false; // Invalid context.
            }

            // Check permissions.
            if (!has_capability('mod/hvp:getcachedassets', $context)) {
                return false;
            }

            $itemid = 0;
            break;

        case 'content':
            if ($context->contextlevel != CONTEXT_MODULE) {
                return false; // Invalid context.
            }

            // Check permissions.
            if (!has_capability('mod/hvp:view', $context)) {
                return false;
            }

            $itemid = array_shift($args);
            break;

        case 'exports':
            if ($context->contextlevel != CONTEXT_MODULE) {
                return false; // Invalid context.
            }

            // Check permission.
            if (!has_capability('mod/hvp:view', $context)) {
                return false;
            }
            // Note that the getexport permission is checked after loading the content.

            // Get core.
            $h5pinterface = \mod_hvp\framework::instance('interface');
            $h5pcore = \mod_hvp\framework::instance('core');

            $matches = array();

            // Get content id from filename.
            if (!preg_match('/(\d*).h5p$/', $args[0], $matches)) {
                // Did not find any content ID.
                return false;
            }

            $contentid = $matches[1];
            $content = $h5pinterface->loadContent($contentid);
            $displayoptions = $h5pcore->getDisplayOptionsForView($content['disable'], $context->instanceid);

            // Check permissions.
            if (!$displayoptions['export']) {
                return false;
            }

            $itemid = 0;

            // Change context to course for retrieving file.
            $cm = get_coursemodule_from_id('hvp', $context->instanceid);
            $context = context_course::instance($cm->course);
            break;

        case 'editor':
            $cap = ($context->contextlevel === CONTEXT_COURSE ? 'addinstance' : 'manage');

            // Check permissions.
            if (!has_capability("mod/hvp:$cap", $context)) {
                return false;
            }

            $itemid = 0;
            break;
    }

    $filename = array_pop($args);
    $filepath = (!$args ? '/' : '/' .implode('/', $args) . '/');

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_hvp', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // No such file.
    }

    send_stored_file($file, 86400, 0, $forcedownload, $options);

    return true;
}

/**
 * Create/update grade item for given hvp
 *
 * @category grade
 * @param stdClass $hvp object with extra cmidnumber
 * @param mixed $grades Optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int, 0 if ok, error code otherwise
 */
function hvp_grade_item_update($hvp, $grades=null) {
    global $CFG;

    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir . '/gradelib.php');
    }

    $params = array('itemname' => $hvp->name, 'idnumber' => $hvp->cmidnumber);

    if (isset($hvp->maximumgrade)) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $hvp->maximumgrade;
    }

    // Recalculate rawgrade relative to grademax.
    if (isset($hvp->rawgrade) && isset($hvp->rawgrademax) && $hvp->rawgrademax != 0) {
        // Get max grade Obs: do not try to use grade_get_grades because it
        // requires context which we don't have inside an ajax.
        $gradeitem = grade_item::fetch(array(
            'itemtype' => 'mod',
            'itemmodule' => 'hvp',
            'iteminstance' => $hvp->id,
            'courseid' => $hvp->course
        ));

        if (isset($gradeitem) && isset($gradeitem->grademax)) {
            $grades->rawgrade = ($hvp->rawgrade / $hvp->rawgrademax) * $gradeitem->grademax;
        }
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/hvp', $hvp->course, 'mod', 'hvp', $hvp->id, 0, $grades, $params);
}

/**
 * Update activity grades
 *
 * @category grade
 * @param stdClass $hvp null means all hvps (with extra cmidnumber property)
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone If true and the user has no grade then a grade item with rawgrade == null will be inserted
 */
function hvp_update_grades($hvp=null, $userid=0, $nullifnone=true) {
    if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = null;
        hvp_grade_item_update($hvp, $grade);

    } else {
        hvp_grade_item_update($hvp);
    }
}
