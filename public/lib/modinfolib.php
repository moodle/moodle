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
 * Functions/classes relating to cached information about module instances on a course.
 *
 * @package    core
 * @subpackage lib
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Sam Marshall
 */

use core_course\modinfo;
use core\exception\coding_exception;
use core\exception\moodle_exception;

if (!defined('MAX_MODINFO_CACHE_SIZE')) {
    /**
     * @deprecated Since 5.1 MDL-86155.
     * Replaced with \core_course\modinfo::MAX_CACHE_SIZE
     */
    define('MAX_MODINFO_CACHE_SIZE', 10);
}

/**
 * Returns reference to full info about modules in course (including visibility).
 * Cached and as fast as possible (0 or 1 db query).
 *
 * use get_fast_modinfo($courseid, 0, true) to reset the static cache for particular course
 * use get_fast_modinfo(0, 0, true) to reset the static cache for all courses
 *
 * use rebuild_course_cache($courseid, true) to reset the application AND static cache
 * for particular course when it's contents has changed
 *
 * @param int|stdClass $courseorid object from DB table 'course' (must have field 'id'
 *     and recommended to have field 'cacherev') or just a course id. Just course id
 *     is enough when calling get_fast_modinfo() for current course or site or when
 *     calling for any other course for the second time.
 * @param int $userid User id to populate 'availble' and 'uservisible' attributes of modules and sections.
 *     Set to 0 for current user (default). Set to -1 to avoid calculation of dynamic user-depended data.
 * @param bool $resetonly whether we want to get modinfo or just reset the cache
 * @return modinfo|null Module information for course, or null if resetting
 * @throws moodle_exception when course is not found (nothing is thrown if resetting)
 */
function get_fast_modinfo($courseorid, $userid = 0, $resetonly = false) {
    // Compatibility with syntax prior to 2.4.
    if ($courseorid === 'reset') {
        throw new coding_exception(
            'Using the string "reset" as the first argument of get_fast_modinfo() is deprecated. ' .
            'Use get_fast_modinfo(0,0,true) instead.',
        );
    }

    // Function get_fast_modinfo() can never be called during upgrade unless it is used for clearing cache only.
    if (!$resetonly) {
        \core\setup::ensure_upgrade_is_not_running();
    }

    // Function is called with $reset = true.
    if ($resetonly) {
        modinfo::clear_instance_cache($courseorid);
        return null;
    }

    // Function is called with $reset = false, retrieve modinfo.
    return modinfo::instance($courseorid, $userid);
}

/**
 * Efficiently retrieves the $course (stdclass) and $cm (cm_info) objects, given
 * a cmid. If module name is also provided, it will ensure the cm is of that type.
 *
 * Usage:
 * list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'forum');
 *
 * Using this method has a performance advantage because it works by loading
 * modinfo for the course - which will then be cached and it is needed later
 * in most requests. It also guarantees that the $cm object is a cm_info and
 * not a stdclass.
 *
 * The $course object can be supplied if already known and will speed
 * up this function - although it is more efficient to use this function to
 * get the course if you are starting from a cmid.
 *
 * To avoid security problems and obscure bugs, you should always specify
 * $modulename if the cmid value came from user input.
 *
 * By default this obtains information (for example, whether user can access
 * the activity) for current user, but you can specify a userid if required.
 *
 * @param stdClass|int $cmorid Id of course-module, or database object
 * @param string $modulename Optional modulename (improves security)
 * @param stdClass|int $courseorid Optional course object if already loaded
 * @param int $userid Optional userid (default = current)
 * @return array Array with 2 elements $course and $cm
 * @throws moodle_exception If the item doesn't exist or is of wrong module name
 */
function get_course_and_cm_from_cmid($cmorid, $modulename = '', $courseorid = 0, $userid = 0) {
    global $DB;
    if (is_object($cmorid)) {
        $cmid = $cmorid->id;
        if (isset($cmorid->course)) {
            $courseid = (int)$cmorid->course;
        } else {
            $courseid = 0;
        }
    } else {
        $cmid = (int)$cmorid;
        $courseid = 0;
    }

    // Validate module name if supplied.
    if ($modulename && !core_component::is_valid_plugin_name('mod', $modulename)) {
        throw new coding_exception('Invalid modulename parameter');
    }

    // Get course from last parameter if supplied.
    $course = null;
    if (is_object($courseorid)) {
        $course = $courseorid;
    } else if ($courseorid) {
        $courseid = (int)$courseorid;
    }

    if (!$course) {
        if ($courseid) {
            // If course ID is known, get it using normal function.
            $course = get_course($courseid);
        } else {
            // Get course record in a single query based on cmid.
            $course = $DB->get_record_sql("
                    SELECT c.*
                      FROM {course_modules} cm
                      JOIN {course} c ON c.id = cm.course
                     WHERE cm.id = ?", [$cmid], MUST_EXIST);
        }
    }

    // Get cm from get_fast_modinfo.
    $modinfo = get_fast_modinfo($course, $userid);
    $cm = $modinfo->get_cm($cmid);
    if ($modulename && $cm->modname !== $modulename) {
        throw new moodle_exception('invalidcoursemoduleid', 'error', '', $cmid);
    }
    return [$course, $cm];
}

/**
 * Efficiently retrieves the $course (stdclass) and $cm (cm_info) objects, given
 * an instance id or record and module name.
 *
 * Usage:
 * list($course, $cm) = get_course_and_cm_from_instance($forum, 'forum');
 *
 * Using this method has a performance advantage because it works by loading
 * modinfo for the course - which will then be cached and it is needed later
 * in most requests. It also guarantees that the $cm object is a cm_info and
 * not a stdclass.
 *
 * The $course object can be supplied if already known and will speed
 * up this function - although it is more efficient to use this function to
 * get the course if you are starting from an instance id.
 *
 * By default this obtains information (for example, whether user can access
 * the activity) for current user, but you can specify a userid if required.
 *
 * @param stdclass|int $instanceorid Id of module instance, or database object
 * @param string $modulename Modulename (required)
 * @param stdClass|int $courseorid Optional course object if already loaded
 * @param int $userid Optional userid (default = current)
 * @return array Array with 2 elements $course and $cm
 * @throws moodle_exception If the item doesn't exist or is of wrong module name
 */
function get_course_and_cm_from_instance($instanceorid, $modulename, $courseorid = 0, $userid = 0) {
    global $DB;

    // Get data from parameter.
    if (is_object($instanceorid)) {
        $instanceid = $instanceorid->id;
        if (isset($instanceorid->course)) {
            $courseid = (int)$instanceorid->course;
        } else {
            $courseid = 0;
        }
    } else {
        $instanceid = (int)$instanceorid;
        $courseid = 0;
    }

    // Get course from last parameter if supplied.
    $course = null;
    if (is_object($courseorid)) {
        $course = $courseorid;
    } else if ($courseorid) {
        $courseid = (int)$courseorid;
    }

    // Validate module name if supplied.
    if (!core_component::is_valid_plugin_name('mod', $modulename)) {
        throw new coding_exception('Invalid modulename parameter');
    }

    if (!$course) {
        if ($courseid) {
            // If course ID is known, get it using normal function.
            $course = get_course($courseid);
        } else {
            // Get course record in a single query based on instance id.
            $pagetable = '{' . $modulename . '}';
            $course = $DB->get_record_sql("
                    SELECT c.*
                      FROM $pagetable instance
                      JOIN {course} c ON c.id = instance.course
                     WHERE instance.id = ?", [$instanceid], MUST_EXIST);
        }
    }

    // Get cm from get_fast_modinfo.
    $modinfo = get_fast_modinfo($course, $userid);
    $instance = $modinfo->get_instance_of($modulename, $instanceid, MUST_EXIST);
    return [$course, $instance];
}


/**
 * Rebuilds or resets the cached list of course activities stored in MUC.
 *
 * rebuild_course_cache() must NEVER be called from lib/db/upgrade.php.
 * At the same time course cache may ONLY be cleared using this function in
 * upgrade scripts of plugins.
 *
 * During the bulk operations if it is necessary to reset cache of multiple
 * courses it is enough to call {@see increment_revision_number()} for the
 * table 'course' and field 'cacherev' specifying affected courses in select.
 *
 * Cached course information is stored in MUC core/coursemodinfo and is
 * validated with the DB field {course}.cacherev
 *
 * @param int $courseid id of course to rebuild, empty means all
 * @param boolean $clearonly only clear the cache, gets rebuild automatically on the fly.
 *     Recommended to set to true to avoid unnecessary multiple rebuilding.
 * @param boolean $partialrebuild will not delete the whole cache when it's true.
 *     use purge_module_cache() or purge_section_cache() must be
 *         called before when partialrebuild is true.
 *     use purge_module_cache() to invalidate mod cache.
 *     use purge_section_cache() to invalidate section cache.
 *
 * @return void
 * @throws coding_exception
 */
function rebuild_course_cache(int $courseid = 0, bool $clearonly = false, bool $partialrebuild = false): void {
    global $COURSE, $SITE, $DB;

    if ($courseid == 0 && $partialrebuild) {
        throw new coding_exception('partialrebuild only works when a valid course id is provided.');
    }

    // Function rebuild_course_cache() can not be called during upgrade unless it's clear only.
    if (!$clearonly && \core\setup::warn_if_upgrade_is_running()) {
        $clearonly = true;
    }

    // Destroy navigation caches.
    navigation_cache::destroy_volatile_caches();

    core_courseformat\base::reset_course_cache($courseid);

    $cachecoursemodinfo = cache::make('core', 'coursemodinfo');
    if (empty($courseid)) {
        // Clearing caches for all courses.
        increment_revision_number('course', 'cacherev', '');
        if (!$partialrebuild) {
            $cachecoursemodinfo->purge();
        }
        // Clear memory static cache.
        modinfo::clear_instance_cache();
        // Update global values too.
        $sitecacherev = $DB->get_field('course', 'cacherev', ['id' => SITEID]);
        $SITE->cachrev = $sitecacherev;
        if ($COURSE->id == SITEID) {
            $COURSE->cacherev = $sitecacherev;
        } else {
            $COURSE->cacherev = $DB->get_field('course', 'cacherev', ['id' => $COURSE->id]);
        }
    } else {
        // Clearing cache for one course, make sure it is deleted from user request cache as well.
        // Because this is a versioned cache, there is no need to actually delete the cache item,
        // only increase the required version number.
        increment_revision_number('course', 'cacherev', 'id = :id', ['id' => $courseid]);
        $cacherev = $DB->get_field('course', 'cacherev', ['id' => $courseid]);
        // Clear memory static cache.
        modinfo::clear_instance_cache($courseid, $cacherev);
        // Update global values too.
        if ($courseid == $COURSE->id || $courseid == $SITE->id) {
            if ($courseid == $COURSE->id) {
                $COURSE->cacherev = $cacherev;
            }
            if ($courseid == $SITE->id) {
                $SITE->cacherev = $cacherev;
            }
        }
    }

    if ($clearonly) {
        return;
    }

    if ($courseid) {
        $select = ['id' => $courseid];
    } else {
        $select = [];
        // This could take a while -- See MDL-10954 for further information.
        core_php_time_limit::raise();
    }

    $fields = 'id,' . join(',', modinfo::$cachedfields);
    $sort = '';
    $rs = $DB->get_recordset("course", $select, $sort, $fields);

    // Rebuild cache for each course.
    foreach ($rs as $course) {
        modinfo::build_course_cache($course, $partialrebuild);
    }
    $rs->close();
}
