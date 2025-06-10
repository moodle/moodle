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

use core_calendar\action_factory;
use core_calendar\local\event\entities\action_interface;

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
            return true;
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

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($hvp->completionexpected) ? $hvp->completionexpected : null;
        \core_completion\api::update_completion_date_event($hvp->coursemodule, 'hvp', $hvp->id, $completiontimeexpected);
    }

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

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($hvp->completionexpected) ? $hvp->completionexpected : null;
        \core_completion\api::update_completion_date_event($hvp->coursemodule, 'hvp', $hvp->id, $completiontimeexpected);
    }

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

    // Only log event if we found library.
    if ($library) {
        // Log content delete.
        new \mod_hvp\event(
            'content', 'delete',
            $hvp->id, $hvp->name,
            $library->name, $library->major_version . '.' . $library->minor_version
        );
    }

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
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                return false; // Invalid context.
            }

            // Check permissions.
            if (!has_capability('mod/hvp:getcachedassets', $context)) {
                return false;
            }

            $itemid = 0;
            break;
        case 'cachedassets':
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                return false; // Invalid context.
            }

            // Check permissions.
            if (!has_capability('mod/hvp:getcachedassets', $context)) {
                return false;
            }

            $options['cacheability'] = 'public';
            $options['immutable'] = true;

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

            // Allow download if valid temporary hash.
            $ishub = false;
            $hub = optional_param('hub', null, PARAM_RAW);
            if ($hub) {
                list($time, $hash) = explode('.', $hub, 2);
                $time = hvp_base64_decode($time);
                $hash = hvp_base64_decode($hash);

                $data = $time . ':' . get_config('mod_hvp', 'site_uuid');
                $signature = hash_hmac('SHA512', $data, get_config('mod_hvp', 'hub_secret'), true);

                if ($time < (time() - 43200) || !hash_equals($signature, $hash)) {
                    // No valid hash.
                    return false;
                }
                $ishub = true;
            } else if (!has_capability('mod/hvp:view', $context)) {
                // No permission.
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
            if (!$displayoptions['export'] && !$ishub) {
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

    // Totara: use allowxss option to prevent application/x-javascript mimetype
    // from being converted to application/x-forcedownload.
    $options['allowxss'] = '1';

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

/**
 * Obtains the automatic completion state for this H5P activity on any conditions
 * in settings, such as if a certain grade is achieved.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function hvp_get_completion_state($course, $cm, $userid, $type) {
    global $DB, $CFG;
    $hvp = $DB->get_record('hvp', array('id' => $cm->instance), '*', MUST_EXIST);
    if (!$hvp->completionpass) {
        return $type;
    }
    // Check for passing grade.
    if ($hvp->completionpass) {
        require_once($CFG->libdir . '/gradelib.php');
        $item = grade_item::fetch(array('courseid' => $course->id, 'itemtype' => 'mod',
                'itemmodule' => 'hvp', 'iteminstance' => $cm->instance, 'outcomeid' => null));
        if ($item) {
            $grades = grade_grade::fetch_users_grades($item, array($userid), false);
            if (!empty($grades[$userid])) {
                return $grades[$userid]->is_passed($item);
            }
        }
    }
    return false;
}

/**
 * URL compatible base64 decoding.
 *
 * @param string $string
 * @return string
 */
function hvp_base64_decode($string) {
    $r = strlen($string) % 4;
    if ($r) {
        $l = 4 - $r;
        $string .= str_repeat('=', $l);
    }
    return base64_decode(strtr($string, '-_', '+/'));
}


/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param action_factory $factory
 * @return action_interface|null
 */
function mod_hvp_core_calendar_provide_event_action(calendar_event $event, action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['hvp'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
            get_string('view'),
            new moodle_url('/mod/hvp/view.php', ['id' => $cm->id]),
            1,
            true
    );
}

