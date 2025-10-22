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
 * Library of useful functions
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_course
 */

defined('MOODLE_INTERNAL') || die;

use core\di;
use core\hook;
use core_courseformat\base as course_format;
use core_courseformat\formatactions;
use core_courseformat\sectiondelegate;
use core\output\local\action_menu\subpanel as action_menu_subpanel;

require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/datalib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // Records.
define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds.

/**
 * Number of courses to display when summaries are included.
 * @var int
 * @deprecated since 2.4, use $CFG->courseswithsummarieslimit instead.
 */
define('COURSE_MAX_SUMMARIES_PER_PAGE', 10);

// Max courses in log dropdown before switching to optional.
define('COURSE_MAX_COURSES_PER_DROPDOWN', 1000);
// Max users in log dropdown before switching to optional.
define('COURSE_MAX_USERS_PER_DROPDOWN', 1000);
define('FRONTPAGENEWS', '0');
define('FRONTPAGECATEGORYNAMES', '2');
define('FRONTPAGECATEGORYCOMBO', '4');
define('FRONTPAGEENROLLEDCOURSELIST', '5');
define('FRONTPAGEALLCOURSELIST', '6');
define('FRONTPAGECOURSESEARCH', '7');
// Important! Replaced with $CFG->frontpagecourselimit - maximum number of courses displayed on the frontpage.
define('EXCELROWS', 65535);
define('FIRSTUSEDEXCELROW', 3);

define('MOD_CLASS_ACTIVITY', 0);
define('MOD_CLASS_RESOURCE', 1);

define('COURSE_TIMELINE_ALLINCLUDINGHIDDEN', 'allincludinghidden');
define('COURSE_TIMELINE_ALL', 'all');
define('COURSE_TIMELINE_PAST', 'past');
define('COURSE_TIMELINE_INPROGRESS', 'inprogress');
define('COURSE_TIMELINE_FUTURE', 'future');
define('COURSE_TIMELINE_SEARCH', 'search');
define('COURSE_FAVOURITES', 'favourites');
define('COURSE_TIMELINE_HIDDEN', 'hidden');
define('COURSE_CUSTOMFIELD', 'customfield');
define('COURSE_DB_QUERY_LIMIT', 1000);
/** Searching for all courses that have no value for the specified custom field. */
define('COURSE_CUSTOMFIELD_EMPTY', -1);

// Course activity chooser footer default display option.
define('COURSE_CHOOSER_FOOTER_NONE', 'hidden');

// Download course content options.
define('DOWNLOAD_COURSE_CONTENT_DISABLED', 0);
define('DOWNLOAD_COURSE_CONTENT_ENABLED', 1);
define('DOWNLOAD_COURSE_CONTENT_SITE_DEFAULT', 2);

function make_log_url($module, $url) {
    switch ($module) {
        case 'course':
            if (strpos($url, 'report/') === 0) {
                // there is only one report type, course reports are deprecated
                $url = "/$url";
                break;
            }
        case 'file':
        case 'login':
        case 'lib':
        case 'admin':
        case 'category':
        case 'mnet course':
            if (strpos($url, '../') === 0) {
                $url = ltrim($url, '.');
            } else {
                $url = "/course/$url";
            }
            break;
        case 'calendar':
            $url = "/calendar/$url";
            break;
        case 'user':
        case 'blog':
            $url = "/$module/$url";
            break;
        case 'upload':
            $url = $url;
            break;
        case 'coursetags':
            $url = '/'.$url;
            break;
        case 'library':
        case '':
            $url = '/';
            break;
        case 'message':
            $url = "/message/$url";
            break;
        case 'notes':
            $url = "/notes/$url";
            break;
        case 'tag':
            $url = "/tag/$url";
            break;
        case 'role':
            $url = '/'.$url;
            break;
        case 'grade':
            $url = "/grade/$url";
            break;
        default:
            $url = "/mod/$module/$url";
            break;
    }

    //now let's sanitise urls - there might be some ugly nasties:-(
    $parts = explode('?', $url);
    $script = array_shift($parts);
    if (strpos($script, 'http') === 0) {
        $script = clean_param($script, PARAM_URL);
    } else {
        $script = clean_param($script, PARAM_PATH);
    }

    $query = '';
    if ($parts) {
        $query = implode('', $parts);
        $query = str_replace('&amp;', '&', $query); // both & and &amp; are stored in db :-|
        $parts = explode('&', $query);
        $eq = urlencode('=');
        foreach ($parts as $key=>$part) {
            $part = urlencode(urldecode($part));
            $part = str_replace($eq, '=', $part);
            $parts[$key] = $part;
        }
        $query = '?'.implode('&amp;', $parts);
    }

    return $script.$query;
}


function build_mnet_logs_array($hostid, $course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {
    global $CFG, $DB;

    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    /// Setup for group handling.

    // TODO: I don't understand group/context/etc. enough to be able to do
    // something interesting with it here
    // What is the context of a remote course?

    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    //if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', context_course::instance($course->id))) {
    //    $groupid = get_current_group($course->id);
    //}
    /// If this course doesn't have groups, no groupid can be specified.
    //else if (!$course->groupmode) {
    //    $groupid = 0;
    //}

    $groupid = 0;

    $joins = array();
    $where = '';

    $qry = "SELECT l.*, u.firstname, u.lastname, u.picture
              FROM {mnet_log} l
               LEFT JOIN {user} u ON l.userid = u.id
              WHERE ";
    $params = array();

    $where .= "l.hostid = :hostid";
    $params['hostid'] = $hostid;

    // TODO: Is 1 really a magic number referring to the sitename?
    if ($course != SITEID || $modid != 0) {
        $where .= " AND l.course=:courseid";
        $params['courseid'] = $course;
    }

    if ($modname) {
        $where .= " AND l.module = :modname";
        $params['modname'] = $modname;
    }

    if ('site_errors' === $modid) {
        $where .= " AND ( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        //TODO: This assumes that modids are the same across sites... probably
        //not true
        $where .= " AND l.cmid = :modid";
        $params['modid'] = $modid;
    }

    if ($modaction) {
        $firstletter = substr($modaction, 0, 1);
        if ($firstletter == '-') {
            $where .= " AND ".$DB->sql_like('l.action', ':modaction', false, true, true);
            $params['modaction'] = '%'.substr($modaction, 1).'%';
        } else {
            $where .= " AND ".$DB->sql_like('l.action', ':modaction', false);
            $params['modaction'] = '%'.$modaction.'%';
        }
    }

    if ($user) {
        $where .= " AND l.userid = :user";
        $params['user'] = $user;
    }

    if ($date) {
        $enddate = $date + 86400;
        $where .= " AND l.time > :date AND l.time < :enddate";
        $params['date'] = $date;
        $params['enddate'] = $enddate;
    }

    $result = array();
    $result['totalcount'] = $DB->count_records_sql("SELECT COUNT('x') FROM {mnet_log} l WHERE $where", $params);
    if(!empty($result['totalcount'])) {
        $where .= " ORDER BY $order";
        $result['logs'] = $DB->get_records_sql("$qry $where", $params, $limitfrom, $limitnum);
    } else {
        $result['logs'] = array();
    }
    return $result;
}

/**
 * Checks the integrity of the course data.
 *
 * In summary - compares course_sections.sequence and course_modules.section.
 *
 * More detailed, checks that:
 * - course_sections.sequence contains each module id not more than once in the course
 * - for each moduleid from course_sections.sequence the field course_modules.section
 *   refers to the same section id (this means course_sections.sequence is more
 *   important if they are different)
 * - ($fullcheck only) each module in the course is present in one of
 *   course_sections.sequence
 * - ($fullcheck only) removes non-existing course modules from section sequences
 *
 * If there are any mismatches, the changes are made and records are updated in DB.
 *
 * Course cache is NOT rebuilt if there are any errors!
 *
 * This function is used each time when course cache is being rebuilt with $fullcheck = false
 * and in CLI script admin/cli/fix_course_sequence.php with $fullcheck = true
 *
 * @param int $courseid id of the course
 * @param array $rawmods result of funciton {@link get_course_mods()} - containst
 *     the list of enabled course modules in the course. Retrieved from DB if not specified.
 *     Argument ignored in cashe of $fullcheck, the list is retrieved form DB anyway.
 * @param array $sections records from course_sections table for this course.
 *     Retrieved from DB if not specified
 * @param bool $fullcheck Will add orphaned modules to their sections and remove non-existing
 *     course modules from sequences. Only to be used in site maintenance mode when we are
 *     sure that another user is not in the middle of the process of moving/removing a module.
 * @param bool $checkonly Only performs the check without updating DB, outputs all errors as debug messages.
 * @return array|false array of messages with found problems. Empty output means everything is ok
 */
function course_integrity_check($courseid, $rawmods = null, $sections = null, $fullcheck = false, $checkonly = false) {
    global $DB;
    $messages = array();
    if ($sections === null) {
        $sections = $DB->get_records('course_sections', array('course' => $courseid), 'section', 'id,section,sequence');
    }
    if ($fullcheck) {
        // Retrieve all records from course_modules regardless of module type visibility.
        $rawmods = $DB->get_records('course_modules', array('course' => $courseid), 'id', 'id,section');
    }
    if ($rawmods === null) {
        $rawmods = get_course_mods($courseid);
    }
    if (!$fullcheck && (empty($sections) || empty($rawmods))) {
        // If either of the arrays is empty, no modules are displayed anyway.
        return true;
    }
    $debuggingprefix = 'Failed integrity check for course ['.$courseid.']. ';

    // First make sure that each module id appears in section sequences only once.
    // If it appears in several section sequences the last section wins.
    // If it appears twice in one section sequence, the first occurence wins.
    $modsection = array();
    foreach ($sections as $sectionid => $section) {
        $sections[$sectionid]->newsequence = $section->sequence;
        if (!empty($section->sequence)) {
            $sequence = explode(",", $section->sequence);
            $sequenceunique = array_unique($sequence);
            if (count($sequenceunique) != count($sequence)) {
                // Some course module id appears in this section sequence more than once.
                ksort($sequenceunique); // Preserve initial order of modules.
                $sequence = array_values($sequenceunique);
                $sections[$sectionid]->newsequence = join(',', $sequence);
                $messages[] = $debuggingprefix.'Sequence for course section ['.
                        $sectionid.'] is "'.$sections[$sectionid]->sequence.'", must be "'.$sections[$sectionid]->newsequence.'"';
            }
            foreach ($sequence as $cmid) {
                if (array_key_exists($cmid, $modsection) && isset($rawmods[$cmid])) {
                    // Some course module id appears to be in more than one section's sequences.
                    $wrongsectionid = $modsection[$cmid];
                    $sections[$wrongsectionid]->newsequence = trim(preg_replace("/,$cmid,/", ',', ','.$sections[$wrongsectionid]->newsequence. ','), ',');
                    $messages[] = $debuggingprefix.'Course module ['.$cmid.'] must be removed from sequence of section ['.
                            $wrongsectionid.'] because it is also present in sequence of section ['.$sectionid.']';
                }
                $modsection[$cmid] = $sectionid;
            }
        }
    }

    // Add orphaned modules to their sections if they exist or to section 0 otherwise.
    if ($fullcheck) {
        foreach ($rawmods as $cmid => $mod) {
            if (!isset($modsection[$cmid])) {
                // This is a module that is not mentioned in course_section.sequence at all.
                // Add it to the section $mod->section or to the last available section.
                if ($mod->section && isset($sections[$mod->section])) {
                    $modsection[$cmid] = $mod->section;
                } else {
                    $firstsection = reset($sections);
                    $modsection[$cmid] = $firstsection->id;
                }
                $sections[$modsection[$cmid]]->newsequence = trim($sections[$modsection[$cmid]]->newsequence.','.$cmid, ',');
                $messages[] = $debuggingprefix.'Course module ['.$cmid.'] is missing from sequence of section ['.
                        $modsection[$cmid].']';
            }
        }
        foreach ($modsection as $cmid => $sectionid) {
            if (!isset($rawmods[$cmid])) {
                // Section $sectionid refers to module id that does not exist.
                $sections[$sectionid]->newsequence = trim(preg_replace("/,$cmid,/", ',', ','.$sections[$sectionid]->newsequence.','), ',');
                $messages[] = $debuggingprefix.'Course module ['.$cmid.
                        '] does not exist but is present in the sequence of section ['.$sectionid.']';
            }
        }
    }

    // Update changed sections.
    if (!$checkonly && !empty($messages)) {
        foreach ($sections as $sectionid => $section) {
            if ($section->newsequence !== $section->sequence) {
                $DB->update_record('course_sections', array('id' => $sectionid, 'sequence' => $section->newsequence));
            }
        }
    }

    // Now make sure that all modules point to the correct sections.
    foreach ($rawmods as $cmid => $mod) {
        if (isset($modsection[$cmid]) && $modsection[$cmid] != $mod->section) {
            if (!$checkonly) {
                $DB->update_record('course_modules', array('id' => $cmid, 'section' => $modsection[$cmid]));
            }
            $messages[] = $debuggingprefix.'Course module ['.$cmid.
                    '] points to section ['.$mod->section.'] instead of ['.$modsection[$cmid].']';
        }
    }

    return $messages;
}

/**
 * Returns an array where the key is the module name (component name without 'mod_')
 * and the value is a lang_string object with a human-readable string.
 *
 * @param bool $plural If true, the function returns the plural forms of the names.
 * @param bool $resetcache If true, the static cache will be reset
 * @return lang_string[] Localised human-readable names of all used modules.
 */
function get_module_types_names($plural = false, $resetcache = false) {
    static $modnames = null;
    global $DB, $CFG;
    if ($modnames === null || empty($modnames[0]) || $resetcache) {
        $modnames = array(0 => array(), 1 => array());
        if ($allmods = $DB->get_records("modules")) {
            foreach ($allmods as $mod) {
                if (file_exists("$CFG->dirroot/mod/$mod->name/lib.php") && $mod->visible) {
                    $modnames[0][$mod->name] = get_string("modulename", "$mod->name", null, true);
                    $modnames[1][$mod->name] = get_string("modulenameplural", "$mod->name", null, true);
                }
            }
        }
    }
    return $modnames[(int)$plural];
}

/**
 * Set highlighted section. Only one section can be highlighted at the time.
 *
 * @param int $courseid course id
 * @param int $marker highlight section with this number, 0 means remove higlightin
 * @return void
 */
function course_set_marker($courseid, $marker) {
    global $DB, $COURSE;
    $DB->set_field("course", "marker", $marker, array('id' => $courseid));
    if ($COURSE && $COURSE->id == $courseid) {
        $COURSE->marker = $marker;
    }
    core_courseformat\base::reset_course_cache($courseid);
    course_modinfo::clear_instance_cache($courseid);
}

/**
 * For a given course section, marks it visible or hidden,
 * and does the same for every activity in that section
 *
 * @param int $courseid course id
 * @param int $sectionnumber The section number to adjust
 * @param int $visibility The new visibility
 * @return array A list of resources which were hidden in the section
 */
function set_section_visible($courseid, $sectionnumber, $visibility) {
    global $DB;

    $resourcestotoggle = array();
    if ($section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionnumber))) {
        course_update_section($courseid, $section, array('visible' => $visibility));

        // Determine which modules are visible for AJAX update
        $modules = !empty($section->sequence) ? explode(',', $section->sequence) : array();
        if (!empty($modules)) {
            list($insql, $params) = $DB->get_in_or_equal($modules);
            $select = 'id ' . $insql . ' AND visible = ?';
            array_push($params, $visibility);
            if (!$visibility) {
                $select .= ' AND visibleold = 1';
            }
            $resourcestotoggle = $DB->get_fieldset_select('course_modules', 'id', $select, $params);
        }
    }
    return $resourcestotoggle;
}

/**
 * Return the course category context for the category with id $categoryid, except
 * that if $categoryid is 0, return the system context.
 *
 * @param integer $categoryid a category id or 0.
 * @return context the corresponding context
 */
function get_category_or_system_context($categoryid) {
    if ($categoryid) {
        return context_coursecat::instance($categoryid, IGNORE_MISSING);
    } else {
        return context_system::instance();
    }
}

/**
 * Does the user have permission to edit things in this category?
 *
 * @param integer $categoryid The id of the category we are showing, or 0 for system context.
 * @return boolean has_any_capability(array(...), ...); in the appropriate context.
 */
function can_edit_in_category($categoryid = 0) {
    $context = get_category_or_system_context($categoryid);
    return has_any_capability(array('moodle/category:manage', 'moodle/course:create'), $context);
}

/// MODULE FUNCTIONS /////////////////////////////////////////////////////////////////

function add_course_module($mod) {
    global $DB;

    $mod->added = time();
    unset($mod->id);

    $cmid = $DB->insert_record("course_modules", $mod);
    rebuild_course_cache($mod->course, true);
    return $cmid;
}

/**
 * Creates a course section and adds it to the specified position
 *
 * @param int|stdClass $courseorid course id or course object
 * @param int $position position to add to, 0 means to the end. If position is greater than
 *        number of existing secitons, the section is added to the end. This will become sectionnum of the
 *        new section. All existing sections at this or bigger position will be shifted down.
 * @param bool $skipcheck the check has already been made and we know that the section with this position does not exist
 * @return stdClass created section object
 */
function course_create_section($courseorid, $position = 0, $skipcheck = false) {
    return formatactions::section($courseorid)->create($position, $skipcheck);
}

/**
 * Creates missing course section(s) and rebuilds course cache
 *
 * @param int|stdClass $courseorid course id or course object
 * @param int|array $sections list of relative section numbers to create
 * @return bool if there were any sections created
 */
function course_create_sections_if_missing($courseorid, $sections) {
    if (!is_array($sections)) {
        $sections = array($sections);
    }
    return formatactions::section($courseorid)->create_if_missing($sections);
}

/**
 * Adds an existing module to the section
 *
 * Updates both tables {course_sections} and {course_modules}
 *
 * Note: This function does not use modinfo PROVIDED that the section you are
 * adding the module to already exists. If the section does not exist, it will
 * build modinfo if necessary and create the section.
 *
 * @param int|stdClass $courseorid course id or course object
 * @param int $cmid id of the module already existing in course_modules table
 * @param int $sectionnum relative number of the section (field course_sections.section)
 *     If section does not exist it will be created
 * @param int|stdClass $beforemod id or object with field id corresponding to the module
 *     before which the module needs to be included. Null for inserting in the
 *     end of the section
 * @param string $modname Optional, name of the module in the modules table. We need to do some checks
 *      to see if this module type can be displayed to the course page.
 *      If not passed a DB query will need to be run instead.
 * @return int The course_sections ID where the module is inserted
 * @throws moodle_exception if a module that has feature flag FEATURE_CAN_DISPLAY set to false is attempted to be moved to
 * a section number other than 0.
 */
function course_add_cm_to_section($courseorid, $cmid, $sectionnum, $beforemod = null, string $modname = '') {
    global $DB, $COURSE;
    if (is_object($beforemod)) {
        $beforemod = $beforemod->id;
    }
    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
    } else {
        $courseid = $courseorid;
    }

    if (!$modname) {
        $sql = "SELECT name
                  FROM {modules} m
                  JOIN {course_modules} cm ON cm.module = m.id
                 WHERE cm.id = :cmid";
        $modname = $DB->get_field_sql($sql, ['cmid' => $cmid], MUST_EXIST);
    }

    // Modules not visible on the course must ALWAYS be in section 0.
    if ($sectionnum != 0 && !course_modinfo::is_mod_type_visible_on_course($modname)) {
        throw new moodle_exception("Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0");
    }

    // Do not try to use modinfo here, there is no guarantee it is valid!
    $section = $DB->get_record('course_sections',
            array('course' => $courseid, 'section' => $sectionnum), '*', IGNORE_MISSING);
    if (!$section) {
        // This function call requires modinfo.
        course_create_sections_if_missing($courseorid, $sectionnum);
        $section = $DB->get_record('course_sections',
                array('course' => $courseid, 'section' => $sectionnum), '*', MUST_EXIST);
    }

    $modarray = explode(",", trim($section->sequence));
    if (empty($section->sequence)) {
        $newsequence = "$cmid";
    } else if ($beforemod && ($key = moodle_array_keys_filter($modarray, $beforemod))) {
        $insertarray = array($cmid, $beforemod);
        array_splice($modarray, $key[0], 1, $insertarray);
        $newsequence = implode(",", $modarray);
    } else {
        $newsequence = "$section->sequence,$cmid";
    }
    $DB->set_field("course_sections", "sequence", $newsequence, array("id" => $section->id));
    $DB->set_field('course_modules', 'section', $section->id, array('id' => $cmid));
    rebuild_course_cache($courseid, true);
    return $section->id;     // Return course_sections ID that was used.
}

/**
 * Change the group mode of a course module.
 *
 * Note: Do not forget to trigger the event \core\event\course_module_updated as it needs
 * to be triggered manually, refer to {@link \core\event\course_module_updated::create_from_cm()}.
 *
 * @param int $id course module ID.
 * @param int $groupmode the new groupmode value.
 * @return bool True if the $groupmode was updated.
 */
function set_coursemodule_groupmode($id, $groupmode) {
    global $DB;
    $cm = $DB->get_record('course_modules', array('id' => $id), 'id,course,groupmode', MUST_EXIST);
    if ($cm->groupmode != $groupmode) {
        $DB->set_field('course_modules', 'groupmode', $groupmode, array('id' => $cm->id));
        \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);
    }
    return ($cm->groupmode != $groupmode);
}

function set_coursemodule_idnumber($id, $idnumber) {
    global $DB;
    $cm = $DB->get_record('course_modules', array('id' => $id), 'id,course,idnumber', MUST_EXIST);
    if ($cm->idnumber != $idnumber) {
        $DB->set_field('course_modules', 'idnumber', $idnumber, array('id' => $cm->id));
        \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        rebuild_course_cache($cm->course, false, true);
    }
    return ($cm->idnumber != $idnumber);
}

/**
 * Set downloadcontent value to course module.
 *
 * @param int $id The id of the module.
 * @param bool $downloadcontent Whether the module can be downloaded when download course content is enabled.
 * @return bool True if downloadcontent has been updated, false otherwise.
 */
function set_downloadcontent(int $id, bool $downloadcontent): bool {
    global $DB;
    $cm = $DB->get_record('course_modules', ['id' => $id], 'id, course, downloadcontent', MUST_EXIST);
    if ($cm->downloadcontent != $downloadcontent) {
        $DB->set_field('course_modules', 'downloadcontent', $downloadcontent, ['id' => $cm->id]);
        rebuild_course_cache($cm->course, true);
    }
    return ($cm->downloadcontent != $downloadcontent);
}

/**
 * Set the visibility of a module and inherent properties.
 *
 * Note: Do not forget to trigger the event \core\event\course_module_updated as it needs
 * to be triggered manually, refer to {@link \core\event\course_module_updated::create_from_cm()}.
 *
 * From 2.4 the parameter $prevstateoverrides has been removed, the logic it triggered
 * has been moved to {@link set_section_visible()} which was the only place from which
 * the parameter was used.
 *
 * If $rebuildcache is set to false, the calling code is responsible for ensuring the cache is purged
 * and rebuilt as appropriate. Consider using this if set_coursemodule_visible is called multiple times
 * (e.g. in a loop).
 *
 * @param int $cmid course module id
 * @param int $visible state of the module
 * @param int $visibleoncoursepage state of the module on the course page
 * @param bool $rebuildcache If true (default), perform a partial cache purge and rebuild.
 * @return bool false when the module was not found, true otherwise
 */
function set_coursemodule_visible($cmid, $visible, $visibleoncoursepage = 1, bool $rebuildcache = true) {
    $coursecontext = context_module::instance($cmid)->get_course_context();
    return formatactions::cm($coursecontext->instanceid)->set_visibility($cmid, $visible, $visibleoncoursepage, $rebuildcache);
}

/**
 * Changes the course module name
 *
 * @param int $cmid course module id
 * @param string $name new value for a name
 * @return bool whether a change was made
 */
function set_coursemodule_name($cmid, $name) {
    $coursecontext = context_module::instance($cmid)->get_course_context();
    return formatactions::cm($coursecontext->instanceid)->rename($cmid, $name);
}

/**
 * This function will handle the whole deletion process of a module. This includes calling
 * the modules delete_instance function, deleting files, events, grades, conditional data,
 * the data in the course_module and course_sections table and adding a module deletion
 * event to the DB.
 *
 * @param int $cmid the course module id
 * @param bool $async whether or not to try to delete the module using an adhoc task. Async also depends on a plugin hook.
 * @throws moodle_exception
 * @since Moodle 2.5
 */
function course_delete_module($cmid, $async = false) {
    // Check the 'course_module_background_deletion_recommended' hook first.
    // Only use asynchronous deletion if at least one plugin returns true and if async deletion has been requested.
    // Both are checked because plugins should not be allowed to dictate the deletion behaviour, only support/decline it.
    // It's up to plugins to handle things like whether or not they are enabled.
    if ($async && $pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                if ($pluginfunction()) {
                    return course_module_flag_for_async_deletion($cmid);
                }
            }
        }
    }

    global $CFG, $DB;

    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->dirroot.'/blog/lib.php');
    require_once($CFG->dirroot.'/calendar/lib.php');

    // Get the course module.
    if (!$cm = $DB->get_record('course_modules', array('id' => $cmid))) {
        return true;
    }

    // Get the module context.
    $modcontext = context_module::instance($cm->id);

    // Get the course module name.
    $modulename = $DB->get_field('modules', 'name', array('id' => $cm->module), MUST_EXIST);

    // Get the file location of the delete_instance function for this module.
    $modlib = "$CFG->dirroot/mod/$modulename/lib.php";

    // Include the file required to call the delete_instance function for this module.
    if (file_exists($modlib)) {
        require_once($modlib);
    } else {
        throw new moodle_exception('cannotdeletemodulemissinglib', '', '', null,
            "Cannot delete this module as the file mod/$modulename/lib.php is missing.");
    }

    // Warning! there is very similar code in remove_course_contents.
    // If you are changing this code, you probably need to change that too.
    $deleteinstancefunction = $modulename . '_delete_instance';

    // Ensure the delete_instance function exists for this module.
    if (!function_exists($deleteinstancefunction)) {
        throw new moodle_exception('cannotdeletemodulemissingfunc', '', '', null,
            "Cannot delete this module as the function {$modulename}_delete_instance is missing in mod/$modulename/lib.php.");
    }

    // Allow plugins to use this course module before we completely delete it.
    if ($pluginsfunction = get_plugins_with_function('pre_course_module_delete')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($cm);
            }
        }
    }

    if (empty($cm->instance)) {
        throw new moodle_exception('cannotdeletemodulemissinginstance', '', '', null,
            "Cannot delete course module with ID $cm->id because it does not have a valid activity instance.");
    }

    // Call the delete_instance function, if it returns false throw an exception.
    if (!$deleteinstancefunction($cm->instance)) {
        throw new moodle_exception('cannotdeletemoduleinstance', '', '', null,
            "Cannot delete the module $modulename (instance).");
    }

    // We delete the questions after the activity database is removed,
    // because questions are referenced via question reference tables
    // and cannot be deleted while the activities that use them still exist.
    question_delete_activity($cm);

    // Remove all module files in case modules forget to do that.
    $fs = get_file_storage();
    $fs->delete_area_files($modcontext->id);

    // Delete events from calendar.
    if ($events = $DB->get_records('event', array('instance' => $cm->instance, 'modulename' => $modulename))) {
        $coursecontext = context_course::instance($cm->course);
        foreach($events as $event) {
            $event->context = $coursecontext;
            $calendarevent = calendar_event::load($event);
            $calendarevent->delete();
        }
    }

    // Delete grade items, outcome items and grades attached to modules.
    if ($grade_items = grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => $modulename,
                                                   'iteminstance' => $cm->instance, 'courseid' => $cm->course))) {
        foreach ($grade_items as $grade_item) {
            $grade_item->delete('moddelete');
        }
    }

    // Delete associated blogs and blog tag instances.
    blog_remove_associations_for_module($modcontext->id);

    // Delete completion and availability data; it is better to do this even if the
    // features are not turned on, in case they were turned on previously (these will be
    // very quick on an empty table).
    $DB->delete_records('course_modules_completion', array('coursemoduleid' => $cm->id));
    $DB->delete_records('course_modules_viewed', ['coursemoduleid' => $cm->id]);
    $DB->delete_records('course_completion_criteria', array('moduleinstance' => $cm->id,
                                                            'course' => $cm->course,
                                                            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));

    // Delete all tag instances associated with the instance of this module.
    core_tag_tag::delete_instances('mod_' . $modulename, null, $modcontext->id);
    core_tag_tag::remove_all_item_tags('core', 'course_modules', $cm->id);

    // Notify the competency subsystem.
    \core_competency\api::hook_course_module_deleted($cm);

    // Delete the context.
    context_helper::delete_instance(CONTEXT_MODULE, $cm->id);

    // Delete the module from the course_modules table.
    $DB->delete_records('course_modules', array('id' => $cm->id));

    // Delete module from that section.
    if (!delete_mod_from_section($cm->id, $cm->section)) {
        throw new moodle_exception('cannotdeletemodulefromsection', '', '', null,
            "Cannot delete the module $modulename (instance) from section.");
    }

    // Trigger event for course module delete action.
    $event = \core\event\course_module_deleted::create(array(
        'courseid' => $cm->course,
        'context'  => $modcontext,
        'objectid' => $cm->id,
        'other'    => array(
            'modulename'   => $modulename,
            'instanceid'   => $cm->instance,
        )
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->trigger();
    \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
    rebuild_course_cache($cm->course, false, true);
}

/**
 * Schedule a course module for deletion in the background using an adhoc task.
 *
 * This method should not be called directly. Instead, please use course_delete_module($cmid, true), to denote async deletion.
 * The real deletion of the module is handled by the task, which calls 'course_delete_module($cmid)'.
 *
 * @param int $cmid the course module id.
 * @return ?bool whether the module was successfully scheduled for deletion.
 * @throws \moodle_exception
 */
function course_module_flag_for_async_deletion($cmid) {
    global $CFG, $DB, $USER;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->dirroot.'/blog/lib.php');
    require_once($CFG->dirroot.'/calendar/lib.php');

    // Get the course module.
    if (!$cm = $DB->get_record('course_modules', array('id' => $cmid))) {
        return true;
    }

    // We need to be reasonably certain the deletion is going to succeed before we background the process.
    // Make the necessary delete_instance checks, etc. before proceeding further. Throw exceptions if required.

    // Get the course module name.
    $modulename = $DB->get_field('modules', 'name', array('id' => $cm->module), MUST_EXIST);

    // Get the file location of the delete_instance function for this module.
    $modlib = "$CFG->dirroot/mod/$modulename/lib.php";

    // Include the file required to call the delete_instance function for this module.
    if (file_exists($modlib)) {
        require_once($modlib);
    } else {
        throw new \moodle_exception('cannotdeletemodulemissinglib', '', '', null,
            "Cannot delete this module as the file mod/$modulename/lib.php is missing.");
    }

    $deleteinstancefunction = $modulename . '_delete_instance';

    // Ensure the delete_instance function exists for this module.
    if (!function_exists($deleteinstancefunction)) {
        throw new \moodle_exception('cannotdeletemodulemissingfunc', '', '', null,
            "Cannot delete this module as the function {$modulename}_delete_instance is missing in mod/$modulename/lib.php.");
    }

    // We are going to defer the deletion as we can't be sure how long the module's pre_delete code will run for.
    $cm->deletioninprogress = '1';
    $DB->update_record('course_modules', $cm);

    // Create an adhoc task for the deletion of the course module. The task takes an array of course modules for removal.
    $removaltask = new \core_course\task\course_delete_modules();
    $removaltask->set_custom_data(array(
        'cms' => array($cm),
        'userid' => $USER->id,
        'realuserid' => \core\session\manager::get_realuser()->id
    ));

    // Queue the task for the next run.
    \core\task\manager::queue_adhoc_task($removaltask);

    // Reset the course cache to hide the module.
    rebuild_course_cache($cm->course, true);
}

/**
 * Checks whether the given course has any course modules scheduled for adhoc deletion.
 *
 * @param int $courseid the id of the course.
 * @param bool $onlygradable whether to check only gradable modules or all modules.
 * @return bool true if the course contains any modules pending deletion, false otherwise.
 */
function course_modules_pending_deletion(int $courseid, bool $onlygradable = false): bool {
    if (empty($courseid)) {
        return false;
    }

    if ($onlygradable) {
        // Fetch modules with grade items.
        if (!$coursegradeitems = grade_item::fetch_all(['itemtype' => 'mod', 'courseid' => $courseid])) {
            // Return early when there is none.
            return false;
        }
    }

    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_cms() as $module) {
        if ($module->deletioninprogress == '1') {
            if ($onlygradable) {
                // Check if the module being deleted is in the list of course modules with grade items.
                foreach ($coursegradeitems as $coursegradeitem) {
                    if ($coursegradeitem->itemmodule == $module->modname && $coursegradeitem->iteminstance == $module->instance) {
                        // The module being deleted is within the gradable  modules.
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
    }
    return false;
}

/**
 * Checks whether the course module, as defined by modulename and instanceid, is scheduled for deletion within the given course.
 *
 * @param int $courseid the course id.
 * @param string $modulename the module name. E.g. 'assign', 'book', etc.
 * @param int $instanceid the module instance id.
 * @return bool true if the course module is pending deletion, false otherwise.
 */
function course_module_instance_pending_deletion($courseid, $modulename, $instanceid) {
    if (empty($courseid) || empty($modulename) || empty($instanceid)) {
        return false;
    }
    $modinfo = get_fast_modinfo($courseid);
    $instances = $modinfo->get_instances_of($modulename);
    return isset($instances[$instanceid]) && $instances[$instanceid]->deletioninprogress;
}

function delete_mod_from_section($modid, $sectionid) {
    global $DB;

    if ($section = $DB->get_record("course_sections", array("id"=>$sectionid)) ) {

        $modarray = explode(",", $section->sequence);

        if ($key = moodle_array_keys_filter($modarray, $modid)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            $DB->set_field("course_sections", "sequence", $newsequence, array("id"=>$section->id));
            rebuild_course_cache($section->course, true);
            return true;
        } else {
            return false;
        }

    }
    return false;
}

/**
 * This function updates the calendar events from the information stored in the module table and the course
 * module table.
 *
 * @param  string $modulename Module name
 * @param  stdClass $instance Module object. Either the $instance or the $cm must be supplied.
 * @param  stdClass $cm Course module object. Either the $instance or the $cm must be supplied.
 * @return bool Returns true if calendar events are updated.
 * @since  Moodle 3.3.4
 */
function course_module_update_calendar_events($modulename, $instance = null, $cm = null) {
    global $DB;

    if (isset($instance) || isset($cm)) {

        if (!isset($instance)) {
            $instance = $DB->get_record($modulename, array('id' => $cm->instance), '*', MUST_EXIST);
        }
        if (!isset($cm)) {
            $cm = get_coursemodule_from_instance($modulename, $instance->id, $instance->course);
        }
        if (!empty($cm)) {
            course_module_calendar_event_update_process($instance, $cm);
        }
        return true;
    }
    return false;
}

/**
 * Update all instances through out the site or in a course.
 *
 * @param  string  $modulename Module type to update.
 * @param  integer $courseid   Course id to update events. 0 for the whole site.
 * @return bool Returns True if the update was successful.
 * @since  Moodle 3.3.4
 */
function course_module_bulk_update_calendar_events($modulename, $courseid = 0) {
    global $DB;

    $instances = null;
    if ($courseid) {
        if (!$instances = $DB->get_records($modulename, array('course' => $courseid))) {
            return false;
        }
    } else {
        if (!$instances = $DB->get_records($modulename)) {
            return false;
        }
    }

    foreach ($instances as $instance) {
        if ($cm = get_coursemodule_from_instance($modulename, $instance->id, $instance->course)) {
            course_module_calendar_event_update_process($instance, $cm);
        }
    }
    return true;
}

/**
 * Calendar events for a module instance are updated.
 *
 * @param  stdClass $instance Module instance object.
 * @param  stdClass $cm Course Module object.
 * @since  Moodle 3.3.4
 */
function course_module_calendar_event_update_process($instance, $cm) {
    global $CFG;

    // We need to call *_refresh_events() first because some modules delete 'old' events at the end of the code which
    // will remove the completion events.
    include_once("$CFG->dirroot/mod/$cm->modname/lib.php");
    $refresheventsfunction = $cm->modname . '_refresh_events';
    if (function_exists($refresheventsfunction)) {
        call_user_func($refresheventsfunction, $cm->course, $instance, $cm);
    }
    $completionexpected = (!empty($cm->completionexpected)) ? $cm->completionexpected : null;
    \core_completion\api::update_completion_date_event($cm->id, $cm->modname, $instance, $completionexpected);
}

/**
 * Moves a section within a course, from a position to another.
 * Be very careful: $section and $destination refer to section number,
 * not id!.
 *
 * @param object $course
 * @param int $section Section number (not id!!!)
 * @param int $destination
 * @param bool $ignorenumsections
 * @return boolean Result
 */
function move_section_to($course, $section, $destination, $ignorenumsections = false) {
/// Moves a whole course section up and down within the course
    global $USER, $DB;

    if (!$destination && $destination != 0) {
        return true;
    }

    // compartibility with course formats using field 'numsections'
    $courseformatoptions = course_get_format($course)->get_format_options();
    if ((!$ignorenumsections && array_key_exists('numsections', $courseformatoptions) &&
            ($destination > $courseformatoptions['numsections'])) || ($destination < 1)) {
        return false;
    }

    // Get an instance of the currently configured lock_factory.
    $lockfactory = \core\lock\lock_config::get_lock_factory('core_section_moveto');

    // Get a new lock for the resource, wait for it if needed.
    if (!$lock = $lockfactory->get_lock('courseid:' . $course->id, 60)) {
        throw new \moodle_exception('cannotacquirelock', 'core', '', null, 'core_section_moveto: '.$course->id);
    }

    try {

        // Get all sections for this course and re-order them (2 of them should now share the same section number)
        if (!$sections = $DB->get_records_menu('course_sections', array('course' => $course->id),
            'section ASC, id ASC', 'id, section')) {
            return false;
        }

        $movedsections = reorder_sections($sections, $section, $destination);

        // Update all sections. Do this in 2 steps to avoid breaking database
        // uniqueness constraint
        $transaction = $DB->start_delegated_transaction();
        foreach ($movedsections as $id => $position) {
            if ((int)$sections[$id] !== $position) {
                $DB->set_field('course_sections', 'section', -$position, ['id' => $id]);
            }
        }
        foreach ($movedsections as $id => $position) {
            if ((int)$sections[$id] !== $position) {
                $DB->set_field('course_sections', 'section', $position, ['id' => $id]);
            }
        }

        // If we move the highlighted section itself, then just highlight the destination.
        // Adjust the higlighted section location if we move something over it either direction.
        if ($section == $course->marker) {
            course_set_marker($course->id, $destination);
        } else if ($section > $course->marker && $course->marker >= $destination) {
            course_set_marker($course->id, $course->marker + 1);
        } else if ($section < $course->marker && $course->marker <= $destination) {
            course_set_marker($course->id, $course->marker - 1);
        }

        $transaction->allow_commit();
    } finally {
        $lock->release();
        rebuild_course_cache($course->id, true, true);
    }
    return true;
}

/**
 * This method will delete a course section and may delete all modules inside it.
 *
 * No permissions are checked here, use {@link course_can_delete_section()} to
 * check if section can actually be deleted.
 *
 * @param int|stdClass $course
 * @param int|stdClass|section_info $sectionornum
 * @param bool $forcedeleteifnotempty if set to false section will not be deleted if it has modules in it.
 * @param bool $async whether or not to try to delete the section using an adhoc task. Async also depends on a plugin hook.
 * @return bool whether section was deleted
 */
function course_delete_section($course, $sectionornum, $forcedeleteifnotempty = true, $async = false) {
    $sectionnum = (is_object($sectionornum)) ? $sectionornum->section : (int)$sectionornum;
    $sectioninfo = get_fast_modinfo($course)->get_section_info($sectionnum);
    if (!$sectioninfo) {
        return false;
    }
    return formatactions::section($course)->delete($sectioninfo, $forcedeleteifnotempty, $async);
}

/**
 * Course section deletion, using an adhoc task for deletion of the modules it contains.
 * 1. Schedule all modules within the section for adhoc removal.
 * 2. Move all modules to course section 0.
 * 3. Delete the resulting empty section.
 *
 * @param \stdClass $section the section to schedule for deletion.
 * @param bool $forcedeleteifnotempty whether to force section deletion if it contains modules.
 * @return bool true if the section was scheduled for deletion, false otherwise.
 */
function course_delete_section_async($section, $forcedeleteifnotempty = true) {
    if (!is_object($section) || empty($section->id) || empty($section->course)) {
        return false;
    }
    $sectioninfo = get_fast_modinfo($section->course)->get_section_info_by_id($section->id);
    if (!$sectioninfo) {
        return false;
    }
    return formatactions::section($section->course)->delete_async($sectioninfo, $forcedeleteifnotempty);
}

/**
 * Updates the course section
 *
 * This function does not check permissions or clean values - this has to be done prior to calling it.
 *
 * @param int|stdClass $courseorid
 * @param stdClass|section_info $section record from course_sections table - it will be updated with the new values
 * @param array|stdClass $data
 */
function course_update_section($courseorid, $section, $data) {
    $sectioninfo = get_fast_modinfo($courseorid)->get_section_info_by_id($section->id);
    formatactions::section($courseorid)->update($sectioninfo, $data);

    // Update $section object fields (for legacy compatibility).
    $data = array_diff_key((array) $data, array_flip(['id', 'course', 'section', 'sequence']));
    foreach ($data as $key => $value) {
        if (property_exists($section, $key)) {
            $section->$key = $value;
        }
    }
}

/**
 * Checks if the current user can delete a section (if course format allows it and user has proper permissions).
 *
 * @param int|stdClass $course
 * @param int|stdClass|section_info $section
 * @return bool
 */
function course_can_delete_section($course, $section) {
    if (is_object($section)) {
        $section = $section->section;
    }
    if (!$section) {
        // Not possible to delete 0-section.
        return false;
    }
    // Course format should allow to delete sections.
    if (!course_get_format($course)->can_delete_section($section)) {
        return false;
    }
    // Make sure user has capability to update course and move sections.
    $context = context_course::instance(is_object($course) ? $course->id : $course);
    if (!has_all_capabilities(array('moodle/course:movesections', 'moodle/course:update'), $context)) {
        return false;
    }
    // Make sure user has capability to delete each activity in this section.
    $modinfo = get_fast_modinfo($course);
    if (!empty($modinfo->sections[$section])) {
        foreach ($modinfo->sections[$section] as $cmid) {
            if (!has_capability('moodle/course:manageactivities', context_module::instance($cmid))) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Reordering algorithm for course sections. Given an array of section->section indexed by section->id,
 * an original position number and a target position number, rebuilds the array so that the
 * move is made without any duplication of section positions.
 * Note: The target_position is the position AFTER WHICH the moved section will be inserted. If you want to
 * insert a section before the first one, you must give 0 as the target (section 0 can never be moved).
 *
 * @param array $sections
 * @param int $origin_position
 * @param int $target_position
 * @return array|false
 */
function reorder_sections($sections, $origin_position, $target_position) {
    if (!is_array($sections)) {
        return false;
    }

    // We can't move section position 0
    if ($origin_position < 1) {
        echo "We can't move section position 0";
        return false;
    }

    // Locate origin section in sections array
    if (!$origin_key = array_search($origin_position, $sections)) {
        echo "searched position not in sections array";
        return false; // searched position not in sections array
    }

    // Extract origin section
    $origin_section = $sections[$origin_key];
    unset($sections[$origin_key]);

    // Find offset of target position (stupid PHP's array_splice requires offset instead of key index!)
    $found = false;
    $append_array = array();
    foreach ($sections as $id => $position) {
        if ($found) {
            $append_array[$id] = $position;
            unset($sections[$id]);
        }
        if ($position == $target_position) {
            if ($target_position < $origin_position) {
                $append_array[$id] = $position;
                unset($sections[$id]);
            }
            $found = true;
        }
    }

    // Append moved section
    $sections[$origin_key] = $origin_section;

    // Append rest of array (if applicable)
    if (!empty($append_array)) {
        foreach ($append_array as $id => $position) {
            $sections[$id] = $position;
        }
    }

    // Renumber positions
    $position = 0;
    foreach ($sections as $id => $p) {
        $sections[$id] = $position;
        $position++;
    }

    return $sections;

}

/**
 * Move the module object $mod to the specified $section
 * If $beforemod exists then that is the module
 * before which $modid should be inserted
 *
 * @param stdClass|cm_info $mod
 * @param stdClass|section_info $section
 * @param int|stdClass $beforemod id or object with field id corresponding to the module
 *     before which the module needs to be included. Null for inserting in the
 *     end of the section
 * @return int new value for module visibility (0 or 1)
 */
function moveto_module($mod, $section, $beforemod=NULL) {
    global $OUTPUT, $DB;

    if ($section->section != 0 && !course_modinfo::is_mod_type_visible_on_course($mod->modname)) {
        throw new coding_exception("Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0");
    }

    // Current module visibility state - return value of this function.
    $modvisible = $mod->visible;

    // Remove original module from original section.
    if (! delete_mod_from_section($mod->id, $mod->section)) {
        echo $OUTPUT->notification("Could not delete module from existing section");
    }

    // Add the module into the new section.
    course_add_cm_to_section($section->course, $mod->id, $section->section, $beforemod, $mod->modname);

    // If moving to a hidden section then hide module.
    if ($mod->section != $section->id) {
        if (!$section->visible && $mod->visible) {
            // Module was visible but must become hidden after moving to hidden section.
            $modvisible = 0;
            set_coursemodule_visible($mod->id, 0);
            // Set visibleold to 1 so module will be visible when section is made visible.
            $DB->set_field('course_modules', 'visibleold', 1, array('id' => $mod->id));
        }
        if ($section->visible && !$mod->visible) {
            // Hidden module was moved to the visible section, restore the module visibility from visibleold.
            set_coursemodule_visible($mod->id, $mod->visibleold);
            $modvisible = $mod->visibleold;
        }
    }

    return $modvisible;
}

/**
 * Returns the list of all editing actions that current user can perform on the module
 *
 * @deprecated since Moodle 5.0
 * @todo Remove this method in Moodle 6.0 (MDL-83530).
 *
 * @param cm_info $mod The module to produce editing buttons for
 * @param int $indent The current indenting (default -1 means no move left-right actions)
 * @param int $sr The section to link back to (used for creating the links)
 * @return array array of action_link or pix_icon objects
 */
#[\core\attribute\deprecated(
    replacement: 'core_courseformat\output\local\content\cm\controlmenu',
    since: '5.0',
    mdl: 'MDL-83527',
    reason: 'Replaced by an output class equivalent.',
)]
function course_get_cm_edit_actions(cm_info $mod, $indent = -1, $sr = null) {
    global $COURSE, $SITE, $CFG;

    \core\deprecation::emit_deprecation(__FUNCTION__);

    static $str;

    $coursecontext = context_course::instance($mod->course);
    $modcontext = context_module::instance($mod->id);
    $courseformat = course_get_format($mod->get_course());
    $usecomponents = $courseformat->supports_components();
    $sectioninfo = $mod->get_section_info();
    $hasdelegatesection = sectiondelegate::has_delegate_class('mod_'.$mod->modname);

    $editcaps = array('moodle/course:manageactivities', 'moodle/course:activityvisibility', 'moodle/role:assign');
    $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');

    // No permission to edit anything.
    if (!has_any_capability($editcaps, $modcontext) and !has_all_capabilities($dupecaps, $coursecontext)) {
        return array();
    }

    $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

    if (!isset($str)) {
        $str = get_strings(
            [
                'delete', 'move', 'moveright', 'moveleft', 'editsettings',
                'duplicate', 'availability',
            ],
            'moodle'
        );
        $str->assign = get_string('assignroles', 'role');
        $str->groupmode = get_string('groupmode', 'group');
    }

    $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));

    if ($sr !== null) {
        $baseurl->param('sr', $sr);
    }
    $actions = array();

    // Update.
    if ($hasmanageactivities) {
        $actions['update'] = new action_menu_link_secondary(
            new moodle_url($baseurl, array('update' => $mod->id)),
            new pix_icon('i/settings', '', 'moodle', ['class' => 'iconsmall']),
            $str->editsettings,
            array('class' => 'editing_update', 'data-action' => 'update')
        );
    }

    // Move (only for component compatible formats).
    if ($hasmanageactivities && $usecomponents) {
        $actions['move'] = new action_menu_link_secondary(
            new moodle_url($baseurl, [
                'sesskey' => sesskey(),
                'copy' => $mod->id,
            ]),
            new pix_icon('i/dragdrop', '', 'moodle', ['class' => 'iconsmall']),
            $str->move,
            [
                'class' => 'editing_movecm',
                'data-action' => 'moveCm',
                'data-id' => $mod->id,
            ]
        );
    }

    // Indent.
    if ($hasmanageactivities && $indent >= 0 && !$hasdelegatesection) {
        $indentlimits = new stdClass();
        $indentlimits->min = 0;
        // Legacy indentation could continue using a limit of 16,
        // but components based formats will be forced to use one level indentation only.
        $indentlimits->max = ($usecomponents) ? 1 : 16;
        if (right_to_left()) {   // Exchange arrows on RTL
            $rightarrow = 't/left';
            $leftarrow  = 't/right';
        } else {
            $rightarrow = 't/right';
            $leftarrow  = 't/left';
        }

        if ($indent >= $indentlimits->max) {
            $enabledclass = 'hidden';
        } else {
            $enabledclass = '';
        }
        $actions['moveright'] = new action_menu_link_secondary(
            new moodle_url($baseurl, ['id' => $mod->id, 'indent' => '1']),
            new pix_icon($rightarrow, '', 'moodle', ['class' => 'iconsmall']),
            $str->moveright,
            [
                'class' => 'editing_moveright ' . $enabledclass,
                'data-action' => ($usecomponents) ? 'cmMoveRight' : 'moveright',
                'data-keepopen' => true,
                'data-sectionreturn' => $sr,
                'data-id' => $mod->id,
            ]
        );

        if ($indent <= $indentlimits->min) {
            $enabledclass = 'hidden';
        } else {
            $enabledclass = '';
        }
        $actions['moveleft'] = new action_menu_link_secondary(
            new moodle_url($baseurl, ['id' => $mod->id, 'indent' => '-1']),
            new pix_icon($leftarrow, '', 'moodle', ['class' => 'iconsmall']),
            $str->moveleft,
            [
                'class' => 'editing_moveleft ' . $enabledclass,
                'data-action' => ($usecomponents) ? 'cmMoveLeft' : 'moveleft',
                'data-keepopen' => true,
                'data-sectionreturn' => $sr,
                'data-id' => $mod->id,
            ]
        );

    }

    // Hide/Show/Available/Unavailable.
    if (has_capability('moodle/course:activityvisibility', $modcontext)) {
        $availabilityclass = $courseformat->get_output_classname('content\\cm\\visibility');
        /** @var core_courseformat\output\local\content\cm\visibility */
        $availability = new $availabilityclass($courseformat, $sectioninfo, $mod);
        $availabilityitem = $availability->get_menu_item();
        if ($availabilityitem) {
            $actions['availability'] = $availabilityitem;
        }
    }

    // Duplicate (require both target import caps to be able to duplicate and backup2 support, see modduplicate.php)
    if (has_all_capabilities($dupecaps, $coursecontext) &&
            plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2) &&
            course_allowed_module($mod->get_course(), $mod->modname)) {
        $actions['duplicate'] = new action_menu_link_secondary(
            new moodle_url($baseurl, ['duplicate' => $mod->id]),
            new pix_icon('t/copy', '', 'moodle', array('class' => 'iconsmall')),
            $str->duplicate,
            [
                'class' => 'editing_duplicate',
                'data-action' => ($courseformat->supports_components()) ? 'cmDuplicate' : 'duplicate',
                'data-sectionreturn' => $sr,
                'data-id' => $mod->id,
            ]
        );
    }

    // Assign.
    if (has_capability('moodle/role:assign', $modcontext) && !$hasdelegatesection) {
        $actions['assign'] = new action_menu_link_secondary(
            new moodle_url('/admin/roles/assign.php', array('contextid' => $modcontext->id)),
            new pix_icon('t/assignroles', '', 'moodle', array('class' => 'iconsmall')),
            $str->assign,
            array('class' => 'editing_assign', 'data-action' => 'assignroles', 'data-sectionreturn' => $sr)
        );
    }

    // Groupmode.
    if ($courseformat->show_groupmode($mod) && $usecomponents  && !$mod->coursegroupmodeforce) {
        $groupmodeclass = $courseformat->get_output_classname('content\\cm\\groupmode');
        /** @var core_courseformat\output\local\content\cm\groupmode */
        $groupmode = new $groupmodeclass($courseformat, $sectioninfo, $mod);
        $actions['groupmode'] = new action_menu_subpanel(
            $str->groupmode,
            $groupmode->get_choice_list(),
            ['class' => 'editing_groupmode'],
            new pix_icon('t/groupv', '', 'moodle', ['class' => 'iconsmall'])
        );
    }

    // Delete.
    if ($hasmanageactivities) {
        $actions['delete'] = new action_menu_link_secondary(
            new moodle_url($baseurl, ['delete' => $mod->id]),
            new pix_icon('t/delete', '', 'moodle', ['class' => 'iconsmall']),
            $str->delete,
            [
                'class' => 'editing_delete text-danger',
                'data-action' => ($usecomponents) ? 'cmDelete' : 'delete',
                'data-sectionreturn' => $sr,
                'data-id' => $mod->id,
            ]
        );
    }

    return $actions;
}

/**
 * Returns the move action.
 *
 * @param cm_info $mod The module to produce a move button for
 * @param int $sr The section to link back to (used for creating the links)
 * @return string The markup for the move action, or an empty string if not available.
 */
function course_get_cm_move(cm_info $mod, $sr = null) {
    global $OUTPUT;

    static $str;
    static $baseurl;

    $modcontext = context_module::instance($mod->id);
    $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

    if (!isset($str)) {
        $str = get_strings(array('move'));
    }

    if (!isset($baseurl)) {
        $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));

        if ($sr !== null) {
            $baseurl->param('sr', $sr);
        }
    }

    if ($hasmanageactivities) {
        $pixicon = 'i/dragdrop';

        if (!course_ajax_enabled($mod->get_course())) {
            // Override for course frontpage until we get drag/drop working there.
            $pixicon = 't/move';
        }

        $attributes = [
            'class' => 'editing_move',
            'data-action' => 'move',
            'data-sectionreturn' => $sr,
            'title' => $str->move,
            'aria-label' => $str->move,
        ];
        return html_writer::link(
            new moodle_url($baseurl, ['copy' => $mod->id]),
            $OUTPUT->pix_icon($pixicon, '', 'moodle', ['class' => 'iconsmall']),
            $attributes
        );
    }
    return '';
}

/**
 * given a course object with shortname & fullname, this function will
 * truncate the the number of chars allowed and add ... if it was too long
 */
function course_format_name ($course,$max=100) {

    $context = context_course::instance($course->id);
    $shortname = format_string($course->shortname, true, array('context' => $context));
    $fullname = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
    $str = $shortname.': '. $fullname;
    if (core_text::strlen($str) <= $max) {
        return $str;
    }
    else {
        return core_text::substr($str,0,$max-3).'...';
    }
}

/**
 * Is the user allowed to add this type of module to this course?
 * @param object $course the course settings. Only $course->id is used.
 * @param string $modname the module name. E.g. 'forum' or 'quiz'.
 * @param \stdClass $user the user to check, defaults to the global user if not provided.
 * @return bool whether the current user is allowed to add this type of module to this course.
 */
function course_allowed_module($course, $modname, ?\stdClass $user = null) {
    global $USER;
    $user = $user ?? $USER;
    if (is_numeric($modname)) {
        throw new coding_exception('Function course_allowed_module no longer
                supports numeric module ids. Please update your code to pass the module name.');
    }

    $capability = 'mod/' . $modname . ':addinstance';
    if (!get_capability_info($capability)) {
        // Debug warning that the capability does not exist, but no more than once per page.
        static $warned = array();
        $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        if (!isset($warned[$modname]) && $archetype !== MOD_ARCHETYPE_SYSTEM) {
            debugging('The module ' . $modname . ' does not define the standard capability ' .
                    $capability , DEBUG_DEVELOPER);
            $warned[$modname] = 1;
        }

        // If the capability does not exist, the module can always be added.
        return true;
    }

    $coursecontext = context_course::instance($course->id);
    return has_capability($capability, $coursecontext, $user);
}

/**
 * Efficiently moves many courses around while maintaining
 * sortorder in order.
 *
 * @param array $courseids is an array of course ids
 * @param int $categoryid
 * @return bool success
 */
function move_courses($courseids, $categoryid) {
    global $DB;

    if (empty($courseids)) {
        // Nothing to do.
        return false;
    }

    if (!$category = $DB->get_record('course_categories', array('id' => $categoryid))) {
        return false;
    }

    $courseids = array_reverse($courseids);
    $newparent = context_coursecat::instance($category->id);
    $i = 1;

    list($where, $params) = $DB->get_in_or_equal($courseids);
    $dbcourses = $DB->get_records_select('course', 'id ' . $where, $params, '', 'id, category, shortname, fullname');
    foreach ($dbcourses as $dbcourse) {
        $course = new stdClass();
        $course->id = $dbcourse->id;
        $course->timemodified = time();
        $course->category  = $category->id;
        $course->sortorder = $category->sortorder + get_max_courses_in_category() - $i++;
        if ($category->visible == 0) {
            // Hide the course when moving into hidden category, do not update the visibleold flag - we want to get
            // to previous state if somebody unhides the category.
            $course->visible = 0;
        }

        $DB->update_record('course', $course);

        // Update context, so it can be passed to event.
        $context = context_course::instance($course->id);
        $context->update_moved($newparent);

        // Trigger a course updated event.
        $event = \core\event\course_updated::create(array(
            'objectid' => $course->id,
            'context' => context_course::instance($course->id),
            'other' => array('shortname' => $dbcourse->shortname,
                             'fullname' => $dbcourse->fullname,
                             'updatedfields' => array('category' => $category->id))
        ));
        $event->trigger();
    }
    fix_course_sortorder();
    cache_helper::purge_by_event('changesincourse');

    return true;
}

/**
 * Returns the display name of the given section that the course prefers
 *
 * Implementation of this function is provided by course format
 * @see core_courseformat\base::get_section_name()
 *
 * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
 * @param int|stdClass|section_info $section Section object from database or just field course_sections.section
 * @return string Display name that the course format prefers, e.g. "Week 2"
 */
function get_section_name($courseorid, $section) {
    return course_get_format($courseorid)->get_section_name($section);
}

/**
 * Tells if current course format uses sections
 *
 * @param string $format Course format ID e.g. 'weeks' $course->format
 * @return bool
 */
function course_format_uses_sections($format) {
    $course = new stdClass();
    $course->format = $format;
    return course_get_format($course)->uses_sections();
}

/**
 * Returns the information about the ajax support in the given source format
 *
 * The returned object's property (boolean)capable indicates that
 * the course format supports Moodle course ajax features.
 *
 * @deprecated since Moodle 5.0 MDL-82351
 * @todo MDL-83417 Remove this function in Moodle 6.0
 * @param string $format
 * @return stdClass
 */
#[\core\attribute\deprecated(since: '5.0', mdl: 'MDL-82351')]
function course_format_ajax_support($format) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    $course = new stdClass();
    $course->format = $format;
    return course_get_format($course)->supports_ajax();
}

/**
 * Can the current user delete this course?
 * Course creators have exception,
 * 1 day after the creation they can sill delete the course.
 * @param int $courseid
 * @return boolean
 */
function can_delete_course($courseid) {
    global $USER;

    $context = context_course::instance($courseid);

    if (has_capability('moodle/course:delete', $context)) {
        return true;
    }

    // hack: now try to find out if creator created this course recently (1 day)
    if (!has_capability('moodle/course:create', $context)) {
        return false;
    }

    $since = time() - 60*60*24;
    $course = get_course($courseid);

    if ($course->timecreated < $since) {
        return false; // Return if the course was not created in last 24 hours.
    }

    $logmanger = get_log_manager();
    $readers = $logmanger->get_readers('\core\log\sql_reader');
    $reader = reset($readers);

    if (empty($reader)) {
        return false; // No log reader found.
    }

    // A proper reader.
    $select = "userid = :userid AND courseid = :courseid AND eventname = :eventname AND timecreated > :since";
    $params = array('userid' => $USER->id, 'since' => $since, 'courseid' => $course->id, 'eventname' => '\core\event\course_created');

    return (bool)$reader->get_events_select_count($select, $params);
}

/**
 * Save the Your name for 'Some role' strings.
 *
 * @param integer $courseid the id of this course.
 * @param array|stdClass $data the data that came from the course settings form.
 */
function save_local_role_names($courseid, $data) {
    global $DB;
    $context = context_course::instance($courseid);

    foreach ($data as $fieldname => $value) {
        if (strpos($fieldname, 'role_') !== 0) {
            continue;
        }
        list($ignored, $roleid) = explode('_', $fieldname);

        // make up our mind whether we want to delete, update or insert
        if (!$value) {
            $DB->delete_records('role_names', array('contextid' => $context->id, 'roleid' => $roleid));

        } else if ($rolename = $DB->get_record('role_names', array('contextid' => $context->id, 'roleid' => $roleid))) {
            $rolename->name = $value;
            $DB->update_record('role_names', $rolename);

        } else {
            $rolename = new stdClass;
            $rolename->contextid = $context->id;
            $rolename->roleid = $roleid;
            $rolename->name = $value;
            $DB->insert_record('role_names', $rolename);
        }
        // This will ensure the course contacts cache is purged..
        core_course_category::role_assignment_changed($roleid, $context);
    }
}

/**
 * Returns options to use in course overviewfiles filemanager
 *
 * @param null|stdClass|core_course_list_element|int $course either object that has 'id' property or just the course id;
 *     may be empty if course does not exist yet (course create form)
 * @return array|null array of options such as maxfiles, maxbytes, accepted_types, etc.
 *     or null if overviewfiles are disabled
 */
function course_overviewfiles_options($course) {
    global $CFG;
    if (empty($CFG->courseoverviewfileslimit)) {
        return null;
    }

    // Create accepted file types based on config value, falling back to default all.
    $acceptedtypes = (new \core_form\filetypes_util)->normalize_file_types($CFG->courseoverviewfilesext);
    if (in_array('*', $acceptedtypes) || empty($acceptedtypes)) {
        $acceptedtypes = '*';
    }

    $options = array(
        'maxfiles' => $CFG->courseoverviewfileslimit,
        'maxbytes' => $CFG->maxbytes,
        'subdirs' => 0,
        'accepted_types' => $acceptedtypes
    );
    if (!empty($course->id)) {
        $options['context'] = context_course::instance($course->id);
    } else if (is_int($course) && $course > 0) {
        $options['context'] = context_course::instance($course);
    }
    return $options;
}

/**
 * Create a course and either return a $course object
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param array $editoroptions course description editor options
 * @param object $data  - all the data needed for an entry in the 'course' table
 * @return object new course instance
 */
function create_course($data, $editoroptions = NULL) {
    global $DB, $CFG;

    //check the categoryid - must be given for all new courses
    $category = $DB->get_record('course_categories', array('id'=>$data->category), '*', MUST_EXIST);

    // Check if the shortname already exists.
    if (!empty($data->shortname)) {
        if ($DB->record_exists('course', array('shortname' => $data->shortname))) {
            throw new moodle_exception('shortnametaken', '', '', $data->shortname);
        }
    }

    // Check if the idnumber already exists.
    if (!empty($data->idnumber)) {
        if ($DB->record_exists('course', array('idnumber' => $data->idnumber))) {
            throw new moodle_exception('courseidnumbertaken', '', '', $data->idnumber);
        }
    }

    if (empty($CFG->enablecourserelativedates)) {
        // Make sure we're not setting the relative dates mode when the setting is disabled.
        unset($data->relativedatesmode);
    }

    if ($errorcode = course_validate_dates((array)$data)) {
        throw new moodle_exception($errorcode);
    }

    // Check if timecreated is given.
    $data->timecreated  = !empty($data->timecreated) ? $data->timecreated : time();
    $data->timemodified = $data->timecreated;

    // place at beginning of any category
    $data->sortorder = 0;

    if ($editoroptions) {
        // summary text is updated later, we need context to store the files first
        $data->summary = '';
        $data->summary_format = $data->summary_editor['format'];
    }

    // Get default completion settings as a fallback in case the enablecompletion field is not set.
    $courseconfig = get_config('moodlecourse');
    $defaultcompletion = !empty($CFG->enablecompletion) ? $courseconfig->enablecompletion : COMPLETION_DISABLED;
    $enablecompletion = $data->enablecompletion ?? $defaultcompletion;
    // Unset showcompletionconditions when completion tracking is not enabled for the course.
    if ($enablecompletion == COMPLETION_DISABLED) {
        unset($data->showcompletionconditions);
    } else if (!isset($data->showcompletionconditions)) {
        // Show completion conditions should have a default value when completion is enabled. Set it to the site defaults.
        // This scenario can happen when a course is created through data generators or through a web service.
        $data->showcompletionconditions = $courseconfig->showcompletionconditions;
    }

    if (!isset($data->visible)) {
        // data not from form, add missing visibility info
        $data->visible = $category->visible;
    }
    $data->visibleold = $data->visible;

    $newcourseid = $DB->insert_record('course', $data);
    $context = context_course::instance($newcourseid, MUST_EXIST);

    if ($editoroptions) {
        // Save the files used in the summary editor and store
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
        $DB->set_field('course', 'summary', $data->summary, array('id'=>$newcourseid));
        $DB->set_field('course', 'summaryformat', $data->summary_format, array('id'=>$newcourseid));
    }
    if ($overviewfilesoptions = course_overviewfiles_options($newcourseid)) {
        // Save the course overviewfiles
        $data = file_postupdate_standard_filemanager($data, 'overviewfiles', $overviewfilesoptions, $context, 'course', 'overviewfiles', 0);
    }

    // update course format options
    course_get_format($newcourseid)->update_course_format_options($data);

    $course = course_get_format($newcourseid)->get_course();

    fix_course_sortorder();
    // purge appropriate caches in case fix_course_sortorder() did not change anything
    cache_helper::purge_by_event('changesincourse');

    // Trigger a course created event.
    $event = \core\event\course_created::create(array(
        'objectid' => $course->id,
        'context' => $context,
        'other' => array('shortname' => $course->shortname,
            'fullname' => $course->fullname)
    ));

    $event->trigger();

    $data->id = $newcourseid;

    // Dispatch the hook for post course create actions.
    di::get(hook\manager::class)->dispatch(
        new \core_course\hook\after_course_created(
            course: $data,
        ),
    );

    // Setup the blocks
    blocks_add_default_course_blocks($course);

    // Create default section and initial sections if specified (unless they've already been created earlier).
    // We do not want to call course_create_sections_if_missing() because to avoid creating course cache.
    $numsections = isset($data->numsections) ? $data->numsections : 0;
    $existingsections = $DB->get_fieldset_sql('SELECT section from {course_sections} WHERE course = ?', [$newcourseid]);
    $newsections = array_diff(range(0, $numsections), $existingsections);
    foreach ($newsections as $sectionnum) {
        course_create_section($newcourseid, $sectionnum, true);
    }

    // Save any custom role names.
    save_local_role_names($course->id, (array)$data);

    // set up enrolments
    enrol_course_updated(true, $course, $data);

    // Update course tags.
    if (isset($data->tags)) {
        core_tag_tag::set_item_tags('core', 'course', $course->id, $context, $data->tags);
    }
    // Save custom fields if there are any of them in the form.
    $handler = core_course\customfield\course_handler::create();
    // Make sure to set the handler's parent context first.
    $coursecatcontext = context_coursecat::instance($category->id);
    $handler->set_parent_context($coursecatcontext);
    // Save the custom field data.
    $data->id = $course->id;
    $handler->instance_form_save($data, true);

    di::get(hook\manager::class)->dispatch(
        new \core_course\hook\after_form_submission($data, true),
    );

    return $course;
}

/**
 * Update a course.
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 * @param array $editoroptions course description editor options
 * @return void
 */
function update_course($data, $editoroptions = NULL) {
    global $DB, $CFG;

    // Prevent changes on front page course.
    if ($data->id == SITEID) {
        throw new moodle_exception('invalidcourse', 'error');
    }

    $oldcourse = course_get_format($data->id)->get_course();
    $context   = context_course::instance($oldcourse->id);

    // Make sure we're not changing whatever the course's relativedatesmode setting is.
    unset($data->relativedatesmode);

    // Capture the updated fields for the log data.
    $updatedfields = [];
    foreach (get_object_vars($oldcourse) as $field => $value) {
        if ($field == 'summary_editor') {
            if (($data->$field)['text'] !== $value['text']) {
                // The summary might be very long, we don't wan't to fill up the log record with the full text.
                $updatedfields[$field] = '(updated)';
            }
        } else if ($field == 'tags' && isset($data->tags)) {
            // Tags might not have the same array keys, just check the values.
            if (array_values($data->$field) !== array_values($value)) {
                $updatedfields[$field] = $data->$field;
            }
        } else {
            if (isset($data->$field) && $data->$field != $value) {
                $updatedfields[$field] = $data->$field;
            }
        }
    }

    $data->timemodified = time();

    if ($editoroptions) {
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
    }
    if ($overviewfilesoptions = course_overviewfiles_options($data->id)) {
        $data = file_postupdate_standard_filemanager($data, 'overviewfiles', $overviewfilesoptions, $context, 'course', 'overviewfiles', 0);
    }

    // Check we don't have a duplicate shortname.
    if (!empty($data->shortname) && $oldcourse->shortname != $data->shortname) {
        if ($DB->record_exists_sql('SELECT id from {course} WHERE shortname = ? AND id <> ?', array($data->shortname, $data->id))) {
            throw new moodle_exception('shortnametaken', '', '', $data->shortname);
        }
    }

    // Check we don't have a duplicate idnumber.
    if (!empty($data->idnumber) && $oldcourse->idnumber != $data->idnumber) {
        if ($DB->record_exists_sql('SELECT id from {course} WHERE idnumber = ? AND id <> ?', array($data->idnumber, $data->id))) {
            throw new moodle_exception('courseidnumbertaken', '', '', $data->idnumber);
        }
    }

    if ($errorcode = course_validate_dates((array)$data)) {
        throw new moodle_exception($errorcode);
    }

    if (!isset($data->category) or empty($data->category)) {
        // prevent nulls and 0 in category field
        unset($data->category);
    }
    $changesincoursecat = $movecat = (isset($data->category) and $oldcourse->category != $data->category);

    if (!isset($data->visible)) {
        // data not from form, add missing visibility info
        $data->visible = $oldcourse->visible;
    }

    if ($data->visible != $oldcourse->visible) {
        // reset the visibleold flag when manually hiding/unhiding course
        $data->visibleold = $data->visible;
        $changesincoursecat = true;
    } else {
        if ($movecat) {
            $newcategory = $DB->get_record('course_categories', array('id'=>$data->category));
            if (empty($newcategory->visible)) {
                // make sure when moving into hidden category the course is hidden automatically
                $data->visible = 0;
            }
        }
    }

    // Set newsitems to 0 if format does not support announcements.
    if (isset($data->format)) {
        $newcourseformat = course_get_format((object)['format' => $data->format]);
        if (!$newcourseformat->supports_news()) {
            $data->newsitems = 0;
        }
    }

    // Set showcompletionconditions to null when completion tracking has been disabled for the course.
    if (isset($data->enablecompletion) && $data->enablecompletion == COMPLETION_DISABLED) {
        $data->showcompletionconditions = null;
    }
    // Update custom fields if there are any of them in the form.
    $handler = core_course\customfield\course_handler::create();
    $handler->instance_form_save($data);

    di::get(hook\manager::class)->dispatch(
        new \core_course\hook\after_form_submission($data),
    );

    // Update with the new data
    $DB->update_record('course', $data);
    // make sure the modinfo cache is reset
    rebuild_course_cache($data->id);

    // Purge course image cache in case if course image has been updated.
    \cache::make('core', 'course_image')->delete($data->id);

    // update course format options with full course data
    course_get_format($data->id)->update_course_format_options($data, $oldcourse);

    $course = $DB->get_record('course', array('id'=>$data->id));

    if ($movecat) {
        $newparent = context_coursecat::instance($course->category);
        $context->update_moved($newparent);
    }
    $fixcoursesortorder = $movecat || (isset($data->sortorder) && ($oldcourse->sortorder != $data->sortorder));
    if ($fixcoursesortorder) {
        fix_course_sortorder();
    }

    // purge appropriate caches in case fix_course_sortorder() did not change anything
    cache_helper::purge_by_event('changesincourse');
    if ($changesincoursecat) {
        cache_helper::purge_by_event('changesincoursecat');
    }

    // Test for and remove blocks which aren't appropriate anymore
    blocks_remove_inappropriate($course);

    // Save any custom role names.
    save_local_role_names($course->id, $data);

    // update enrol settings
    enrol_course_updated(false, $course, $data);

    // Update course tags.
    if (isset($data->tags)) {
        core_tag_tag::set_item_tags('core', 'course', $course->id, context_course::instance($course->id), $data->tags);
    }

    // Trigger a course updated event.
    $event = \core\event\course_updated::create(array(
        'objectid' => $course->id,
        'context' => context_course::instance($course->id),
        'other' => array('shortname' => $course->shortname,
                         'fullname' => $course->fullname,
                         'updatedfields' => $updatedfields)
    ));

    $event->trigger();

    // Dispatch the hook for post course update actions.
    $hook = new \core_course\hook\after_course_updated(
        course: $data,
        oldcourse: $oldcourse,
        changeincoursecat: $changesincoursecat,
    );
    \core\di::get(\core\hook\manager::class)->dispatch($hook);

    if ($oldcourse->format !== $course->format) {
        // Remove all options stored for the previous format
        // We assume that new course format migrated everything it needed watching trigger
        // 'course_updated' and in method format_XXX::update_course_format_options()
        $DB->delete_records('course_format_options',
                array('courseid' => $course->id, 'format' => $oldcourse->format));
    }

    // Delete theme usage cache if the theme has been changed.
    if (isset($data->theme) && ($data->theme != $oldcourse->theme)) {
        theme_delete_used_in_context_cache($data->theme, $oldcourse->theme);
    }
}

/**
 * Calculate the average number of enrolled participants per course.
 *
 * This is intended for statistics purposes during the site registration. Only visible courses are taken into account.
 * Front page enrolments are excluded.
 *
 * @param bool $onlyactive Consider only active enrolments in enabled plugins and obey the enrolment time restrictions.
 * @param int $lastloginsince If specified, count only users who logged in after this timestamp.
 * @return float
 */
function average_number_of_participants(bool $onlyactive = false, ?int $lastloginsince = null): float {
    global $DB;

    $params = [];

    $sql = "SELECT DISTINCT ue.userid, e.courseid
              FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
              JOIN {course} c ON c.id = e.courseid ";

    if ($onlyactive || $lastloginsince) {
        $sql .= "JOIN {user} u ON u.id = ue.userid ";
    }

    $sql .= "WHERE e.courseid <> " . SITEID . " AND c.visible = 1 ";

    if ($onlyactive) {
        $sql .= "AND ue.status = :active
                 AND e.status = :enabled
                 AND ue.timestart < :now1
                 AND (ue.timeend = 0 OR ue.timeend > :now2) ";

        // Same as in the enrollib - the rounding should help caching in the database.
        $now = round(time(), -2);

        $params += [
            'active' => ENROL_USER_ACTIVE,
            'enabled' => ENROL_INSTANCE_ENABLED,
            'now1' => $now,
            'now2' => $now,
        ];
    }

    if ($lastloginsince) {
        $sql .= "AND u.lastlogin > :lastlogin ";
        $params['lastlogin'] = $lastloginsince;
    }

    $sql = "SELECT COUNT(*)
              FROM ($sql) total";

    $enrolmenttotal = $DB->count_records_sql($sql, $params);

    // Get the number of visible courses (exclude the front page).
    $coursetotal = $DB->count_records('course', ['visible' => 1]);
    $coursetotal = $coursetotal - 1;

    if (empty($coursetotal)) {
        $participantaverage = 0;

    } else {
        $participantaverage = $enrolmenttotal / $coursetotal;
    }

    return $participantaverage;
}

/**
 * Average number of course modules
 * @return integer
 */
function average_number_of_courses_modules() {
    global $DB, $SITE;

    //count total of visible course module (except front page)
    $sql = 'SELECT COUNT(*) FROM (
        SELECT cm.course, cm.module
        FROM {course} c, {course_modules} cm
        WHERE c.id = cm.course
            AND c.id <> :siteid
            AND cm.visible = 1
            AND c.visible = 1) total';
    $params = array('siteid' => $SITE->id);
    $moduletotal = $DB->count_records_sql($sql, $params);


    //count total of visible courses (minus front page)
    $coursetotal = $DB->count_records('course', array('visible' => 1));
    $coursetotal = $coursetotal - 1 ;

    //average of course module
    if (empty($coursetotal)) {
        $coursemoduleaverage = 0;
    } else {
        $coursemoduleaverage = $moduletotal / $coursetotal;
    }

    return $coursemoduleaverage;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param context $parentcontext Block's parent context
 * @param context $currentcontext Current context of block
 * @return array array of page types
 */
function course_page_type_list($pagetype, $parentcontext, $currentcontext) {
    if ($pagetype === 'course-index' || $pagetype === 'course-index-category') {
        // For courses and categories browsing pages (/course/index.php) add option to show on ANY category page
        $pagetypes = array('*' => get_string('page-x', 'pagetype'),
            'course-index-*' => get_string('page-course-index-x', 'pagetype'),
        );
    } else if ($currentcontext && (!($coursecontext = $currentcontext->get_course_context(false)) || $coursecontext->instanceid == SITEID)) {
        // We know for sure that despite pagetype starts with 'course-' this is not a page in course context (i.e. /course/search.php, etc.)
        $pagetypes = array('*' => get_string('page-x', 'pagetype'));
    } else {
        // Otherwise consider it a page inside a course even if $currentcontext is null
        $pagetypes = array('*' => get_string('page-x', 'pagetype'),
            'course-*' => get_string('page-course-x', 'pagetype'),
            'course-view-*' => get_string('page-course-view-x', 'pagetype')
        );
    }
    return $pagetypes;
}

/**
 * Determine whether course ajax should be enabled for the specified course
 *
 * @param stdClass $course The course to test against
 * @return boolean Whether course ajax is enabled or note
 */
function course_ajax_enabled($course) {
    global $CFG, $PAGE, $SITE;

    // The user must be editing for AJAX to be included
    if (!$PAGE->user_is_editing()) {
        return false;
    }

    // Check that the theme suports
    if (!$PAGE->theme->enablecourseajax) {
        return false;
    }

    // Check that the course format supports ajax functionality
    // The site 'format' doesn't have information on course format support
    if ($SITE->id !== $course->id) {
        $courseformatajaxsupport = course_get_format($course)->supports_ajax();
        if (!$courseformatajaxsupport->capable) {
            return false;
        }
    }

    // All conditions have been met so course ajax should be enabled
    return true;
}

/**
 * Include the relevant javascript and language strings for the resource
 * toolbox YUI module
 *
 * @param integer $id The ID of the course being applied to
 * @param array $usedmodules An array containing the names of the modules in use on the page
 * @param array $enabledmodules An array containing the names of the enabled (visible) modules on this site
 * @param stdClass $config An object containing configuration parameters for ajax modules including:
 *          * resourceurl   The URL to post changes to for resource changes
 *          * sectionurl    The URL to post changes to for section changes
 *          * pageparams    Additional parameters to pass through in the post
 * @return bool
 */
function include_course_ajax($course, $usedmodules = [], $enabledmodules = null, $config = null) {
    global $CFG, $PAGE, $SITE;

    // Init the course editor module to support UI components.
    $format = course_get_format($course);
    include_course_editor($format);

    // TODO remove this if as part of MDL-83627.
    // Ensure that ajax should be included
    if (!course_ajax_enabled($course)) {
        return false;
    }

    // TODO remove this if as part of MDL-83627.
    // Component based formats don't use YUI drag and drop anymore.
    if (!$format->supports_components() && course_format_uses_sections($course->format)) {
        debugging(
            'The old course editor will be removed in Moodle 6.0. Ensure your format return true to supports_components',
            DEBUG_DEVELOPER
        );

        if (!$config) {
            $config = new stdClass();
        }

        // The URL to use for resource changes.
        if (!isset($config->resourceurl)) {
            $config->resourceurl = '/course/rest.php';
        }

        // The URL to use for section changes.
        if (!isset($config->sectionurl)) {
            $config->sectionurl = '/course/rest.php';
        }

        // Any additional parameters which need to be included on page submission.
        if (!isset($config->pageparams)) {
            $config->pageparams = array();
        }

        $PAGE->requires->yui_module('moodle-course-dragdrop', 'M.course.init_section_dragdrop',
            array(array(
                'courseid' => $course->id,
                'ajaxurl' => $config->sectionurl,
                'config' => $config,
            )), null, true);

        $PAGE->requires->yui_module('moodle-course-dragdrop', 'M.course.init_resource_dragdrop',
            array(array(
                'courseid' => $course->id,
                'ajaxurl' => $config->resourceurl,
                'config' => $config,
            )), null, true);

        // Require various strings for the command toolbox.
        $PAGE->requires->strings_for_js(array(
            'moveleft',
            'deletechecktype',
            'deletechecktypename',
            'edittitle',
            'edittitleinstructions',
            'show',
            'hide',
            'highlight',
            'highlightoff',
            'groupsnone',
            'groupsvisible',
            'groupsseparate',
            'movesection',
            'movecoursemodule',
            'movecoursesection',
            'movecontent',
            'tocontent',
            'emptydragdropregion',
            'afterresource',
            'aftersection',
            'totopofsection',
        ), 'moodle');

        // Include section-specific strings for formats which support sections.
        if (course_format_uses_sections($course->format)) {
            $PAGE->requires->strings_for_js(array(
                    'showfromothers',
                    'hidefromothers',
                ), 'format_' . $course->format);
        }

        // For confirming resource deletion we need the name of the module in question.
        foreach ($usedmodules as $module => $modname) {
            $PAGE->requires->string_for_js('pluginname', $module);
        }

        // Load drag and drop upload AJAX.
        require_once($CFG->dirroot.'/course/dnduploadlib.php');
        dndupload_add_to_course($course, $enabledmodules);

        $PAGE->requires->js_call_amd('core_course/actions', 'initCoursePage', [$course->format]);
    }

    return true;
}

/**
 * Include and configure the course editor modules.
 *
 * @param course_format $format the course format instance.
 */
function include_course_editor(course_format $format) {
    global $PAGE, $SITE;

    $course = $format->get_course();

    if (!$format->supports_ajax()?->capable) {
        return;
    }

    $statekey = course_format::session_cache($course);

    // Edition mode and some format specs must be passed to the init method.
    $setup = (object)[
        'editing' => $format->show_editor(),
        'supportscomponents' => $format->supports_components(),
        'statekey' => $statekey,
        'overriddenStrings' => $format->get_editor_custom_strings(),
    ];
    // All the new editor elements will be loaded after the course is presented and
    // the initial course state will be generated using core_course_get_state webservice.
    $PAGE->requires->js_call_amd('core_courseformat/courseeditor', 'setViewFormat', [$course->id, $setup]);
}

/**
 * Returns the sorted list of available course formats, filtered by enabled if necessary
 *
 * @param bool $enabledonly return only formats that are enabled
 * @return array array of sorted format names
 */
function get_sorted_course_formats($enabledonly = false) {
    global $CFG;

    // Include both formats that exist on disk (but might not have been installed yet), and those
    // which were installed but no longer exist on disk.
    $installedformats = core_plugin_manager::instance()->get_installed_plugins('format');
    $existingformats = core_component::get_plugin_list('format');
    $formats = array_merge($installedformats, $existingformats);

    if (!empty($CFG->format_plugins_sortorder)) {
        $order = explode(',', $CFG->format_plugins_sortorder);
        $order = array_merge(array_intersect($order, array_keys($formats)),
                    array_diff(array_keys($formats), $order));
    } else {
        $order = array_keys($formats);
    }
    if (!$enabledonly) {
        return $order;
    }
    $sortedformats = array();
    foreach ($order as $formatname) {
        $component = "format_{$formatname}";
        $componentdir = core_component::get_component_directory($component);
        if ($componentdir !== null && !get_config($component, 'disabled')) {
            $sortedformats[] = $formatname;
        }
    }
    return $sortedformats;
}

/**
 * The URL to use for the specified course (with section)
 *
 * @param int|stdClass $courseorid The course to get the section name for (either object or just course id)
 * @param int|stdClass $section Section object from database or just field course_sections.section
 *     if omitted the course view page is returned
 * @param array $options options for view URL. At the moment core uses:
 *     'navigation' (bool) if true and section has no separate page, the function returns null
 *     'sr' (int) used by multipage formats to specify to which section to return
 * @return moodle_url The url of course
 */
function course_get_url($courseorid, $section = null, $options = array()) {
    return course_get_format($courseorid)->get_view_url($section, $options);
}

/**
 * Create a module.
 *
 * It includes:
 *      - capability checks and other checks
 *      - create the module from the module info
 *
 * @param object $module
 * @return object the created module info
 * @throws moodle_exception if user is not allowed to perform the action or module is not allowed in this course
 */
function create_module($moduleinfo) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/course/modlib.php');

    // Check manadatory attributs.
    $mandatoryfields = array('modulename', 'course', 'section', 'visible');
    if (plugin_supports('mod', $moduleinfo->modulename, FEATURE_MOD_INTRO, true)) {
        $mandatoryfields[] = 'introeditor';
    }
    foreach($mandatoryfields as $mandatoryfield) {
        if (!isset($moduleinfo->{$mandatoryfield})) {
            throw new moodle_exception('createmodulemissingattribut', '', '', $mandatoryfield);
        }
    }

    // Some additional checks (capability / existing instances).
    $course = $DB->get_record('course', array('id'=>$moduleinfo->course), '*', MUST_EXIST);
    list($module, $context, $cw) = can_add_moduleinfo($course, $moduleinfo->modulename, $moduleinfo->section);

    // Add the module.
    $moduleinfo->module = $module->id;
    $moduleinfo = add_moduleinfo($moduleinfo, $course, null);

    return $moduleinfo;
}

/**
 * Update a module.
 *
 * It includes:
 *      - capability and other checks
 *      - update the module
 *
 * @param object $module
 * @return object the updated module info
 * @throws moodle_exception if current user is not allowed to update the module
 */
function update_module($moduleinfo) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/course/modlib.php');

    // Check the course module exists.
    $cm = get_coursemodule_from_id('', $moduleinfo->coursemodule, 0, false, MUST_EXIST);

    // Check the course exists.
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    // Some checks (capaibility / existing instances).
    list($cm, $context, $module, $data, $cw) = can_update_moduleinfo($cm);

    // Retrieve few information needed by update_moduleinfo.
    $moduleinfo->modulename = $cm->modname;
    if (!isset($moduleinfo->scale)) {
        $moduleinfo->scale = 0;
    }
    $moduleinfo->type = 'mod';

    // Update the module.
    list($cm, $moduleinfo) = update_moduleinfo($cm, $moduleinfo, $course, null);

    return $moduleinfo;
}

/**
 * Duplicate a module on the course for ajax.
 *
 * @see mod_duplicate_module()
 * @param object $course The course
 * @param object $cm The course module to duplicate
 * @param int $sr The section to link back to (used for creating the links)
 * @throws moodle_exception if the plugin doesn't support duplication
 * @return stdClass Object containing:
 * - fullcontent: The HTML markup for the created CM
 * - cmid: The CMID of the newly created CM
 * - redirect: Whether to trigger a redirect following this change
 */
function mod_duplicate_activity($course, $cm, $sr = null) {
    global $PAGE;

    $newcm = duplicate_module($course, $cm);

    $resp = new stdClass();
    if ($newcm) {

        $format = course_get_format($course);
        $renderer = $format->get_renderer($PAGE);
        $modinfo = $format->get_modinfo();
        $section = $modinfo->get_section_info($newcm->sectionnum);

        // Get the new element html content.
        $resp->fullcontent = $renderer->course_section_updated_cm_item($format, $section, $newcm);

        $resp->cmid = $newcm->id;
    } else {
        // Trigger a redirect.
        $resp->redirect = true;
    }
    return $resp;
}

/**
 * Api to duplicate a module.
 *
 * @param object $course course object.
 * @param object $cm course module object to be duplicated.
 * @param int $sectionid section ID new course module will be placed in.
 * @param bool $changename updates module name with text from duplicatedmodule lang string.
 * @since Moodle 2.8
 *
 * @throws Exception
 * @throws coding_exception
 * @throws moodle_exception
 * @throws restore_controller_exception
 *
 * @return cm_info|null cminfo object if we sucessfully duplicated the mod and found the new cm.
 */
function duplicate_module($course, $cm, ?int $sectionid = null, bool $changename = true): ?cm_info {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
    require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    require_once($CFG->libdir . '/filelib.php');

    // Plugins with this feature flag set to false must ALWAYS be in section 0.
    if (!course_modinfo::is_mod_type_visible_on_course($cm->modname)) {
        if (get_fast_modinfo($course)->get_section_info(0, MUST_EXIST)->id != $sectionid) {
            throw new coding_exception('Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0');
        }
    }

    $a          = new stdClass();
    $a->modtype = get_string('modulename', $cm->modname);
    $a->modname = format_string($cm->name);

    if (!plugin_supports('mod', $cm->modname, FEATURE_BACKUP_MOODLE2)) {
        throw new moodle_exception('duplicatenosupport', 'error', '', $a);
    }

    // Backup the activity.

    $bc = new backup_controller(backup::TYPE_1ACTIVITY, $cm->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);

    $backupid       = $bc->get_backupid();
    $backupbasepath = $bc->get_plan()->get_basepath();

    $bc->execute_plan();

    $bc->destroy();

    // Restore the backup immediately.

    $rc = new restore_controller($backupid, $course->id,
            backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);

    // Make sure that the restore_general_groups setting is always enabled when duplicating an activity.
    $plan = $rc->get_plan();
    $groupsetting = $plan->get_setting('groups');
    if (empty($groupsetting->get_value())) {
        $groupsetting->set_value(true);
    }

    $cmcontext = context_module::instance($cm->id);
    if (!$rc->execute_precheck()) {
        $precheckresults = $rc->get_precheck_results();
        if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
            if (empty($CFG->keeptempdirectoriesonbackup)) {
                fulldelete($backupbasepath);
            }
        }
    }

    $rc->execute_plan();

    // Now a bit hacky part follows - we try to get the cmid of the newly
    // restored copy of the module.
    $newcmid = null;
    $tasks = $rc->get_plan()->get_tasks();
    foreach ($tasks as $task) {
        if (is_subclass_of($task, 'restore_activity_task')) {
            if ($task->get_old_contextid() == $cmcontext->id) {
                $newcmid = $task->get_moduleid();
                break;
            }
        }
    }

    $rc->destroy();

    if (empty($CFG->keeptempdirectoriesonbackup)) {
        fulldelete($backupbasepath);
    }

    // If we know the cmid of the new course module, let us move it
    // right below the original one. otherwise it will stay at the
    // end of the section.
    if ($newcmid) {
        // Proceed with activity renaming before everything else. We don't use APIs here to avoid
        // triggering a lot of create/update duplicated events.
        $newcm = get_coursemodule_from_id($cm->modname, $newcmid, $cm->course);
        if ($changename) {
            // Add ' (copy)' language string postfix to duplicated module.
            $newname = get_string('duplicatedmodule', 'moodle', $newcm->name);
            set_coursemodule_name($newcm->id, $newname);
        }

        $section = $DB->get_record('course_sections', ['id' => $sectionid ?? $cm->section, 'course' => $cm->course]);
        if (isset($sectionid)) {
            moveto_module($newcm, $section);
        } else {
            $modarray = explode(",", trim($section->sequence));
            $cmindex = array_search($cm->id, $modarray);
            if ($cmindex !== false && $cmindex < count($modarray) - 1) {
                moveto_module($newcm, $section, $modarray[$cmindex + 1]);
            }
        }

        // Update calendar events with the duplicated module.
        // The following line is to be removed in MDL-58906.
        course_module_update_calendar_events($newcm->modname, null, $newcm);

        // Copy permission overrides to new course module.
        $newcmcontext = context_module::instance($newcm->id);
        $overrides = $DB->get_records('role_capabilities', ['contextid' => $cmcontext->id]);
        foreach ($overrides as $override) {
            $override->contextid = $newcmcontext->id;
            unset($override->id);
            $DB->insert_record('role_capabilities', $override);
        }

        // Copy locally assigned roles to new course module.
        $overrides = $DB->get_records('role_assignments', ['contextid' => $cmcontext->id]);
        foreach ($overrides as $override) {
            $override->contextid = $newcmcontext->id;
            unset($override->id);
            $DB->insert_record('role_assignments', $override);
        }

        // Trigger course module created event. We can trigger the event only if we know the newcmid.
        $newcm = get_fast_modinfo($cm->course)->get_cm($newcmid);
        $event = \core\event\course_module_created::create_from_cm($newcm);
        $event->trigger();
    }

    return isset($newcm) ? $newcm : null;
}

/**
 * Compare two objects to find out their correct order based on timestamp (to be used by usort).
 * Sorts by descending order of time.
 *
 * @param stdClass $a First object
 * @param stdClass $b Second object
 * @return int 0,1,-1 representing the order
 */
function compare_activities_by_time_desc($a, $b) {
    // Make sure the activities actually have a timestamp property.
    if ((!property_exists($a, 'timestamp')) && (!property_exists($b, 'timestamp'))) {
        return 0;
    }
    // We treat instances without timestamp as if they have a timestamp of 0.
    if ((!property_exists($a, 'timestamp')) && (property_exists($b,'timestamp'))) {
        return 1;
    }
    if ((property_exists($a, 'timestamp')) && (!property_exists($b, 'timestamp'))) {
        return -1;
    }
    if ($a->timestamp == $b->timestamp) {
        return 0;
    }
    return ($a->timestamp > $b->timestamp) ? -1 : 1;
}

/**
 * Compare two objects to find out their correct order based on timestamp (to be used by usort).
 * Sorts by ascending order of time.
 *
 * @param stdClass $a First object
 * @param stdClass $b Second object
 * @return int 0,1,-1 representing the order
 */
function compare_activities_by_time_asc($a, $b) {
    // Make sure the activities actually have a timestamp property.
    if ((!property_exists($a, 'timestamp')) && (!property_exists($b, 'timestamp'))) {
      return 0;
    }
    // We treat instances without timestamp as if they have a timestamp of 0.
    if ((!property_exists($a, 'timestamp')) && (property_exists($b, 'timestamp'))) {
        return -1;
    }
    if ((property_exists($a, 'timestamp')) && (!property_exists($b, 'timestamp'))) {
        return 1;
    }
    if ($a->timestamp == $b->timestamp) {
        return 0;
    }
    return ($a->timestamp < $b->timestamp) ? -1 : 1;
}

/**
 * Changes the visibility of a course.
 *
 * @param int $courseid The course to change.
 * @param bool $show True to make it visible, false otherwise.
 * @return bool
 */
function course_change_visibility($courseid, $show = true) {
    $course = new stdClass;
    $course->id = $courseid;
    $course->visible = ($show) ? '1' : '0';
    $course->visibleold = $course->visible;
    update_course($course);
    return true;
}

/**
 * Changes the course sortorder by one, moving it up or down one in respect to sort order.
 *
 * @param stdClass|core_course_list_element $course
 * @param bool $up If set to true the course will be moved up one. Otherwise down one.
 * @return bool
 */
function course_change_sortorder_by_one($course, $up) {
    global $DB;
    $params = array($course->sortorder, $course->category);
    if ($up) {
        $select = 'sortorder < ? AND category = ?';
        $sort = 'sortorder DESC';
    } else {
        $select = 'sortorder > ? AND category = ?';
        $sort = 'sortorder ASC';
    }
    fix_course_sortorder();
    $swapcourse = $DB->get_records_select('course', $select, $params, $sort, '*', 0, 1);
    if ($swapcourse) {
        $swapcourse = reset($swapcourse);
        $DB->set_field('course', 'sortorder', $swapcourse->sortorder, array('id' => $course->id));
        $DB->set_field('course', 'sortorder', $course->sortorder, array('id' => $swapcourse->id));
        // Finally reorder courses.
        fix_course_sortorder();
        cache_helper::purge_by_event('changesincourse');
        return true;
    }
    return false;
}

/**
 * Changes the sort order of courses in a category so that the first course appears after the second.
 *
 * @param int|stdClass $courseorid The course to focus on.
 * @param int $moveaftercourseid The course to shifter after or 0 if you want it to be the first course in the category.
 * @return bool
 */
function course_change_sortorder_after_course($courseorid, $moveaftercourseid) {
    global $DB;

    if (!is_object($courseorid)) {
        $course = get_course($courseorid);
    } else {
        $course = $courseorid;
    }

    if ((int)$moveaftercourseid === 0) {
        // We've moving the course to the start of the queue.
        $sql = 'SELECT sortorder
                      FROM {course}
                     WHERE category = :categoryid
                  ORDER BY sortorder';
        $params = array(
            'categoryid' => $course->category
        );
        $sortorder = $DB->get_field_sql($sql, $params, IGNORE_MULTIPLE);

        $sql = 'UPDATE {course}
                   SET sortorder = sortorder + 1
                 WHERE category = :categoryid
                   AND id <> :id';
        $params = array(
            'categoryid' => $course->category,
            'id' => $course->id,
        );
        $DB->execute($sql, $params);
        $DB->set_field('course', 'sortorder', $sortorder, array('id' => $course->id));
    } else if ($course->id === $moveaftercourseid) {
        // They're the same - moronic.
        debugging("Invalid move after course given.", DEBUG_DEVELOPER);
        return false;
    } else {
        // Moving this course after the given course. It could be before it could be after.
        $moveaftercourse = get_course($moveaftercourseid);
        if ($course->category !== $moveaftercourse->category) {
            debugging("Cannot re-order courses. The given courses do not belong to the same category.", DEBUG_DEVELOPER);
            return false;
        }
        // Increment all courses in the same category that are ordered after the moveafter course.
        // This makes a space for the course we're moving.
        $sql = 'UPDATE {course}
                       SET sortorder = sortorder + 1
                     WHERE category = :categoryid
                       AND sortorder > :sortorder';
        $params = array(
            'categoryid' => $moveaftercourse->category,
            'sortorder' => $moveaftercourse->sortorder
        );
        $DB->execute($sql, $params);
        $DB->set_field('course', 'sortorder', $moveaftercourse->sortorder + 1, array('id' => $course->id));
    }
    fix_course_sortorder();
    cache_helper::purge_by_event('changesincourse');
    return true;
}

/**
 * Trigger course viewed event. This API function is used when course view actions happens,
 * usually in course/view.php but also in external functions.
 *
 * @param stdClass  $context course context object
 * @param int $sectionnumber section number
 * @since Moodle 2.9
 */
function course_view($context, $sectionnumber = 0) {

    $eventdata = array('context' => $context);

    if (!empty($sectionnumber)) {
        $eventdata['other']['coursesectionnumber'] = $sectionnumber;
    }

    $event = \core\event\course_viewed::create($eventdata);
    $event->trigger();

    user_accesstime_log($context->instanceid);
}

/**
 * Returns courses tagged with a specified tag.
 *
 * @param core_tag_tag $tag
 * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
 *             are displayed on the page and the per-page limit may be bigger
 * @param int $fromctx context id where the link was displayed, may be used by callbacks
 *            to display items in the same context first
 * @param int $ctx context id where to search for records
 * @param bool $rec search in subcontexts as well
 * @param int $page 0-based number of page being displayed
 * @return \core_tag\output\tagindex
 */
function course_get_tagged_courses($tag, $exclusivemode = false, $fromctx = 0, $ctx = 0, $rec = 1, $page = 0) {
    global $CFG, $PAGE;

    $perpage = $exclusivemode ? $CFG->coursesperpage : 5;
    $displayoptions = array(
        'limit' => $perpage,
        'offset' => $page * $perpage,
        'viewmoreurl' => null,
    );

    $courserenderer = $PAGE->get_renderer('core', 'course');
    $totalcount = core_course_category::search_courses_count(array('tagid' => $tag->id, 'ctx' => $ctx, 'rec' => $rec));
    $content = $courserenderer->tagged_courses($tag->id, $exclusivemode, $ctx, $rec, $displayoptions);
    $totalpages = ceil($totalcount / $perpage);

    return new core_tag\output\tagindex($tag, 'core', 'course', $content,
            $exclusivemode, $fromctx, $ctx, $rec, $page, $totalpages);
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return ?\core\output\inplace_editable
 */
function core_course_inplace_editable($itemtype, $itemid, $newvalue) {
    if ($itemtype === 'activityname') {
        return \core_courseformat\output\local\content\cm\title::update($itemid, $newvalue);
    }
}

/**
 * This function calculates the minimum and maximum cutoff values for the timestart of
 * the given event.
 *
 * It will return an array with two values, the first being the minimum cutoff value and
 * the second being the maximum cutoff value. Either or both values can be null, which
 * indicates there is no minimum or maximum, respectively.
 *
 * If a cutoff is required then the function must return an array containing the cutoff
 * timestamp and error string to display to the user if the cutoff value is violated.
 *
 * A minimum and maximum cutoff return value will look like:
 * [
 *     [1505704373, 'The date must be after this date'],
 *     [1506741172, 'The date must be before this date']
 * ]
 *
 * @param calendar_event $event The calendar event to get the time range for
 * @param stdClass $course The course object to get the range from
 * @return array Returns an array with min and max date.
 */
function core_course_core_calendar_get_valid_event_timestart_range(\calendar_event $event, $course) {
    $mindate = null;
    $maxdate = null;

    if ($course->startdate) {
        $mindate = [
            $course->startdate,
            get_string('errorbeforecoursestart', 'calendar')
        ];
    }

    return [$mindate, $maxdate];
}

/**
 * Render the message drawer to be included in the top of the body of each page.
 *
 * @return string HTML
 */
function core_course_drawer(): string {
    global $PAGE;

    // If the course index is explicitly set and if it should be hidden.
    if ($PAGE->get_show_course_index() === false) {
        return '';
    }

    // Only add course index on non-site course pages.
    if (!$PAGE->course || $PAGE->course->id == SITEID) {
        return '';
    }

    // Show course index to users can access the course only.
    if (!can_access_course($PAGE->course, null, '', true)) {
        return '';
    }

    $format = course_get_format($PAGE->course);
    $renderer = $format->get_renderer($PAGE);
    if (method_exists($renderer, 'course_index_drawer')) {
        return $renderer->course_index_drawer($format);
    }

    return '';
}

/**
 * Returns course modules tagged with a specified tag ready for output on tag/index.php page
 *
 * This is a callback used by the tag area core/course_modules to search for course modules
 * tagged with a specific tag.
 *
 * @param core_tag_tag $tag
 * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
 *             are displayed on the page and the per-page limit may be bigger
 * @param int $fromcontextid context id where the link was displayed, may be used by callbacks
 *            to display items in the same context first
 * @param int $contextid context id where to search for records
 * @param bool $recursivecontext search in subcontexts as well
 * @param int $page 0-based number of page being displayed
 * @return ?\core_tag\output\tagindex
 */
function course_get_tagged_course_modules($tag, $exclusivemode = false, $fromcontextid = 0, $contextid = 0,
                                          $recursivecontext = 1, $page = 0) {
    global $OUTPUT;
    $perpage = $exclusivemode ? 20 : 5;

    // Build select query.
    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
    $query = "SELECT cm.id AS cmid, c.id AS courseid, $ctxselect
                FROM {course_modules} cm
                JOIN {tag_instance} tt ON cm.id = tt.itemid
                JOIN {course} c ON cm.course = c.id
                JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :coursemodulecontextlevel
               WHERE tt.itemtype = :itemtype AND tt.tagid = :tagid AND tt.component = :component
                AND cm.deletioninprogress = 0
                AND c.id %COURSEFILTER% AND cm.id %ITEMFILTER%";

    $params = array('itemtype' => 'course_modules', 'tagid' => $tag->id, 'component' => 'core',
        'coursemodulecontextlevel' => CONTEXT_MODULE);
    if ($contextid) {
        $context = context::instance_by_id($contextid);
        $query .= $recursivecontext ? ' AND (ctx.id = :contextid OR ctx.path LIKE :path)' : ' AND ctx.id = :contextid';
        $params['contextid'] = $context->id;
        $params['path'] = $context->path.'/%';
    }

    $query .= ' ORDER BY';
    if ($fromcontextid) {
        // In order-clause specify that modules from inside "fromctx" context should be returned first.
        $fromcontext = context::instance_by_id($fromcontextid);
        $query .= ' (CASE WHEN ctx.id = :fromcontextid OR ctx.path LIKE :frompath THEN 0 ELSE 1 END),';
        $params['fromcontextid'] = $fromcontext->id;
        $params['frompath'] = $fromcontext->path.'/%';
    }
    $query .= ' c.sortorder, cm.id';
    $totalpages = $page + 1;

    // Use core_tag_index_builder to build and filter the list of items.
    // Request one item more than we need so we know if next page exists.
    $builder = new core_tag_index_builder('core', 'course_modules', $query, $params, $page * $perpage, $perpage + 1);
    while ($item = $builder->has_item_that_needs_access_check()) {
        context_helper::preload_from_record($item);
        $courseid = $item->courseid;
        if (!$builder->can_access_course($courseid)) {
            $builder->set_accessible($item, false);
            continue;
        }
        $modinfo = get_fast_modinfo($builder->get_course($courseid));
        // Set accessibility of this item and all other items in the same course.
        $builder->walk(function ($taggeditem) use ($courseid, $modinfo, $builder) {
            if ($taggeditem->courseid == $courseid) {
                $cm = $modinfo->get_cm($taggeditem->cmid);
                $builder->set_accessible($taggeditem, $cm->uservisible);
            }
        });
    }

    $items = $builder->get_items();
    if (count($items) > $perpage) {
        $totalpages = $page + 2; // We don't need exact page count, just indicate that the next page exists.
        array_pop($items);
    }

    // Build the display contents.
    if ($items) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($items as $item) {
            context_helper::preload_from_record($item);
            $course = $builder->get_course($item->courseid);
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->get_cm($item->cmid);
            $courseurl = course_get_url($item->courseid, $cm->sectionnum);
            $cmname = $cm->get_formatted_name();
            if (!$exclusivemode) {
                $cmname = shorten_text($cmname, 100);
            }
            $cmname = html_writer::link($cm->url?:$courseurl, $cmname);
            $coursename = format_string($course->fullname, true,
                    array('context' => context_course::instance($item->courseid)));
            $coursename = html_writer::link($courseurl, $coursename);
            $icon = html_writer::empty_tag('img', array('src' => $cm->get_icon_url()));
            $tagfeed->add($icon, $cmname, $coursename);
        }

        $content = $OUTPUT->render_from_template('core_tag/tagfeed',
                $tagfeed->export_for_template($OUTPUT));

        return new core_tag\output\tagindex($tag, 'core', 'course_modules', $content,
                $exclusivemode, $fromcontextid, $contextid, $recursivecontext, $page, $totalpages);
    }
}

/**
 * Return an object with the list of navigation options in a course that are avaialable or not for the current user.
 * This function also handles the frontpage course.
 *
 * @param  stdClass $context context object (it can be a course context or the system context for frontpage settings)
 * @param  stdClass $course  the course where the settings are being rendered
 * @return stdClass          the navigation options in a course and their availability status
 * @since  Moodle 3.2
 */
function course_get_user_navigation_options($context, $course = null) {
    global $CFG, $USER;

    $isloggedin = isloggedin();
    $isguestuser = isguestuser();
    $isfrontpage = $context->contextlevel == CONTEXT_SYSTEM;

    if ($isfrontpage) {
        $sitecontext = $context;
    } else {
        $sitecontext = context_system::instance();
    }

    // Sets defaults for all options.
    $options = (object) [
        'badges' => false,
        'blogs' => false,
        'competencies' => false,
        'grades' => false,
        'notes' => false,
        'participants' => false,
        'search' => false,
        'tags' => false,
        'communication' => false,
        'overview' => false,
    ];

    $options->blogs = !empty($CFG->enableblogs) &&
                        ($CFG->bloglevel == BLOG_GLOBAL_LEVEL ||
                        ($CFG->bloglevel == BLOG_SITE_LEVEL and ($isloggedin and !$isguestuser)))
                        && has_capability('moodle/blog:view', $sitecontext);

    $options->notes = !empty($CFG->enablenotes) && has_any_capability(array('moodle/notes:manage', 'moodle/notes:view'), $context);

    // Frontpage settings?
    if ($isfrontpage) {
        // We are on the front page, so make sure we use the proper capability (site:viewparticipants).
        $options->participants = course_can_view_participants($sitecontext);
        $options->badges = !empty($CFG->enablebadges) && has_capability('moodle/badges:viewbadges', $sitecontext);
        $options->tags = !empty($CFG->usetags) && $isloggedin;
        $options->search = !empty($CFG->enableglobalsearch) && has_capability('moodle/search:query', $sitecontext);
    } else {
        // We are in a course, so make sure we use the proper capability (course:viewparticipants).
        $options->participants = course_can_view_participants($context);

        // Only display badges if they are enabled and the current user can manage them or if they can view them and have,
        // at least, one available badge.
        if (!empty($CFG->enablebadges) && !empty($CFG->badges_allowcoursebadges)) {
            $canmanage = has_any_capability([
                    'moodle/badges:createbadge',
                    'moodle/badges:awardbadge',
                    'moodle/badges:configurecriteria',
                    'moodle/badges:configuremessages',
                    'moodle/badges:configuredetails',
                    'moodle/badges:deletebadge',
                ],
                $context
            );
            $totalbadges = [];
            $canview = false;
            if (!$canmanage) {
                // This only needs to be calculated if the user can't manage badges (to improve performance).
                $canview = has_capability('moodle/badges:viewbadges', $context);
                if ($canview) {
                    require_once($CFG->dirroot.'/lib/badgeslib.php');
                    if (is_null($course)) {
                        $totalbadges = count(badges_get_badges(BADGE_TYPE_SITE, 0, '', '', 0, 0, $USER->id));
                    } else {
                        $totalbadges = count(badges_get_badges(BADGE_TYPE_COURSE, $course->id, '', '', 0, 0, $USER->id));
                    }
                }
            }

            $options->badges = ($canmanage || ($canview && $totalbadges > 0));
        }
        // Add view grade report is permitted.
        $grades = false;

        if (has_capability('moodle/grade:viewall', $context)) {
            $grades = true;
        } else if (!empty($course->showgrades)) {
            $reports = core_component::get_plugin_list('gradereport');
            if (is_array($reports) && count($reports) > 0) {  // Get all installed reports.
                arsort($reports);   // User is last, we want to test it first.
                foreach ($reports as $plugin => $plugindir) {
                    if (has_capability('gradereport/'.$plugin.':view', $context)) {
                        // Stop when the first visible plugin is found.
                        $grades = true;
                        break;
                    }
                }
            }
        }
        $options->grades = $grades;
    }

    if (\core_communication\api::is_available()) {
        $options->communication = has_capability('moodle/course:configurecoursecommunication', $context);
    }

    if (\core_competency\api::is_enabled()) {
        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        $options->competencies = has_any_capability($capabilities, $context);
    }

    if ($isloggedin && !$isfrontpage) {
        $options->overview = has_capability('moodle/course:viewoverview', $context);
    }

    return $options;
}

/**
 * Return an object with the list of administration options in a course that are available or not for the current user.
 * This function also handles the frontpage settings.
 *
 * @param  stdClass $course  course object (for frontpage it should be a clone of $SITE)
 * @param  context_course $context context object (course context)
 * @return stdClass          the administration options in a course and their availability status
 * @since  Moodle 3.2
 */
function course_get_user_administration_options($course, $context) {
    global $CFG;

    $isfrontpage = $course->id == SITEID;
    $hascompletionoptions = count(core_completion\manager::get_available_completion_options($course->id)) > 0;
    $options = new stdClass;
    $options->update = has_capability('moodle/course:update', $context);
    $options->editcompletion = $CFG->enablecompletion && $course->enablecompletion &&
        ($options->update || $hascompletionoptions);
    $options->filters = has_capability('moodle/filter:manage', $context) &&
                        count(filter_get_available_in_context($context)) > 0;
    $options->reports = has_capability('moodle/site:viewreports', $context);
    $options->backup = has_capability('moodle/backup:backupcourse', $context);
    $options->restore = has_capability('moodle/restore:restorecourse', $context);
    $options->copy = \core_course\management\helper::can_copy_course($course->id);
    $options->files = ($course->legacyfiles == 2 && has_capability('moodle/course:managefiles', $context));

    if (!$isfrontpage) {
        $options->tags = core_tag_tag::is_enabled('core', 'course') && has_capability('moodle/course:tag', $context);
        $options->gradebook = has_capability('moodle/grade:manage', $context);
        $options->outcomes = !empty($CFG->enableoutcomes) && has_capability('moodle/course:update', $context);
        $options->badges = !empty($CFG->enablebadges);
        $options->import = has_capability('moodle/restore:restoretargetimport', $context);
        $options->reset = has_capability('moodle/course:reset', $context);
        $options->roles = has_capability('moodle/role:switchroles', $context);
    } else {
        // Set default options to false.
        $listofoptions = array('tags', 'gradebook', 'outcomes', 'badges', 'import', 'publish', 'reset', 'roles', 'grades');

        foreach ($listofoptions as $option) {
            $options->$option = false;
        }
    }

    return $options;
}

/**
 * Validates course start and end dates.
 *
 * Checks that the end course date is not greater than the start course date.
 *
 * $coursedata['startdate'] or $coursedata['enddate'] may not be set, it depends on the form and user input.
 *
 * @param array $coursedata May contain startdate and enddate timestamps, depends on the user input.
 * @return mixed False if everything alright, error codes otherwise.
 */
function course_validate_dates($coursedata) {

    // If both start and end dates are set end date should be later than the start date.
    if (!empty($coursedata['startdate']) && !empty($coursedata['enddate']) &&
            ($coursedata['enddate'] < $coursedata['startdate'])) {
        return 'enddatebeforestartdate';
    }

    // If start date is not set end date can not be set.
    if (empty($coursedata['startdate']) && !empty($coursedata['enddate'])) {
        return 'nostartdatenoenddate';
    }

    return false;
}

/**
 * Check for course updates in the given context level instances (only modules supported right Now)
 *
 * @param  stdClass $course  course object
 * @param  array $tocheck    instances to check for updates
 * @param  array $filter check only for updates in these areas
 * @return array list of warnings and instances with updates information
 * @since  Moodle 3.2
 */
function course_check_updates($course, $tocheck, $filter = array()) {
    global $CFG, $DB;

    $instances = array();
    $warnings = array();
    $modulescallbacksupport = array();
    $modinfo = get_fast_modinfo($course);

    $supportedplugins = get_plugin_list_with_function('mod', 'check_updates_since');

    // Check instances.
    foreach ($tocheck as $instance) {
        if ($instance['contextlevel'] == 'module') {
            // Check module visibility.
            try {
                $cm = $modinfo->get_cm($instance['id']);
            } catch (Exception $e) {
                $warnings[] = array(
                    'item' => 'module',
                    'itemid' => $instance['id'],
                    'warningcode' => 'cmidnotincourse',
                    'message' => 'This module id does not belong to this course.'
                );
                continue;
            }

            if (!$cm->uservisible) {
                $warnings[] = array(
                    'item' => 'module',
                    'itemid' => $instance['id'],
                    'warningcode' => 'nonuservisible',
                    'message' => 'You don\'t have access to this module.'
                );
                continue;
            }
            if (empty($supportedplugins['mod_' . $cm->modname])) {
                $warnings[] = array(
                    'item' => 'module',
                    'itemid' => $instance['id'],
                    'warningcode' => 'missingcallback',
                    'message' => 'This module does not implement the check_updates_since callback: ' . $instance['contextlevel'],
                );
                continue;
            }
            // Retrieve the module instance.
            $instances[] = array(
                'contextlevel' => $instance['contextlevel'],
                'id' => $instance['id'],
                'updates' => call_user_func($cm->modname . '_check_updates_since', $cm, $instance['since'], $filter)
            );

        } else {
            $warnings[] = array(
                'item' => 'contextlevel',
                'itemid' => $instance['id'],
                'warningcode' => 'contextlevelnotsupported',
                'message' => 'Context level not yet supported ' . $instance['contextlevel'],
            );
        }
    }
    return array($instances, $warnings);
}

/**
 * This function classifies a course as past, in progress or future.
 *
 * This function may incur a DB hit to calculate course completion.
 * @param stdClass $course Course record
 * @param stdClass $user User record (optional - defaults to $USER).
 * @param completion_info $completioninfo Completion record for the user (optional - will be fetched if required).
 * @return string (one of COURSE_TIMELINE_FUTURE, COURSE_TIMELINE_INPROGRESS or COURSE_TIMELINE_PAST)
 */
function course_classify_for_timeline($course, $user = null, $completioninfo = null) {
    global $USER;

    if ($user == null) {
        $user = $USER;
    }

    if ($completioninfo == null) {
        $completioninfo = new completion_info($course);
    }

    // Let plugins override data for timeline classification.
    $pluginsfunction = get_plugins_with_function('extend_course_classify_for_timeline', 'lib.php');
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($course, $user, $completioninfo);
        }
    }

    $today = time();
    // End date past.
    if (!empty($course->enddate) && (course_classify_end_date($course) < $today)) {
        return COURSE_TIMELINE_PAST;
    }

    // Course was completed.
    if ($completioninfo->is_enabled() && $completioninfo->is_course_complete($user->id)) {
        return COURSE_TIMELINE_PAST;
    }

    // Start date not reached.
    if (!empty($course->startdate) && (course_classify_start_date($course) > $today)) {
        return COURSE_TIMELINE_FUTURE;
    }

    // Everything else is in progress.
    return COURSE_TIMELINE_INPROGRESS;
}

/**
 * This function calculates the end date to use for display classification purposes,
 * incorporating the grace period, if any.
 *
 * @param stdClass $course The course record.
 * @return int The new enddate.
 */
function course_classify_end_date($course) {
    global $CFG;
    $coursegraceperiodafter = (empty($CFG->coursegraceperiodafter)) ? 0 : $CFG->coursegraceperiodafter;
    $enddate = (new \DateTimeImmutable())->setTimestamp($course->enddate)->modify("+{$coursegraceperiodafter} days");
    return $enddate->getTimestamp();
}

/**
 * This function calculates the start date to use for display classification purposes,
 * incorporating the grace period, if any.
 *
 * @param stdClass $course The course record.
 * @return int The new startdate.
 */
function course_classify_start_date($course) {
    global $CFG;
    $coursegraceperiodbefore = (empty($CFG->coursegraceperiodbefore)) ? 0 : $CFG->coursegraceperiodbefore;
    $startdate = (new \DateTimeImmutable())->setTimestamp($course->startdate)->modify("-{$coursegraceperiodbefore} days");
    return $startdate->getTimestamp();
}

/**
 * Group a list of courses into either past, future, or in progress.
 *
 * The return value will be an array indexed by the COURSE_TIMELINE_* constants
 * with each value being an array of courses in that group.
 * E.g.
 * [
 *      COURSE_TIMELINE_PAST => [... list of past courses ...],
 *      COURSE_TIMELINE_FUTURE => [],
 *      COURSE_TIMELINE_INPROGRESS => []
 * ]
 *
 * @param array $courses List of courses to be grouped.
 * @return array
 */
function course_classify_courses_for_timeline(array $courses) {
    return array_reduce($courses, function($carry, $course) {
        $classification = course_classify_for_timeline($course);
        array_push($carry[$classification], $course);

        return $carry;
    }, [
        COURSE_TIMELINE_PAST => [],
        COURSE_TIMELINE_FUTURE => [],
        COURSE_TIMELINE_INPROGRESS => []
    ]);
}

/**
 * Get the list of enrolled courses for the current user.
 *
 * This function returns a Generator. The courses will be loaded from the database
 * in chunks rather than a single query.
 *
 * @param int $limit Restrict result set to this amount
 * @param int $offset Skip this number of records from the start of the result set
 * @param string|null $sort SQL string for sorting
 * @param string|null $fields SQL string for fields to be returned
 * @param int $dbquerylimit The number of records to load per DB request
 * @param array $includecourses courses ids to be restricted
 * @param array $hiddencourses courses ids to be excluded
 * @return Generator
 */
function course_get_enrolled_courses_for_logged_in_user(
    int $limit = 0,
    int $offset = 0,
    ?string $sort = null,
    ?string $fields = null,
    int $dbquerylimit = COURSE_DB_QUERY_LIMIT,
    array $includecourses = [],
    array $hiddencourses = []
): Generator {

    $haslimit = !empty($limit);
    $recordsloaded = 0;
    $querylimit = (!$haslimit || $limit > $dbquerylimit) ? $dbquerylimit : $limit;

    while ($courses = enrol_get_my_courses($fields, $sort, $querylimit, $includecourses, false, $offset, $hiddencourses)) {
        yield from $courses;

        $recordsloaded += $querylimit;

        if (count($courses) < $querylimit) {
            break;
        }
        if ($haslimit && $recordsloaded >= $limit) {
            break;
        }

        $offset += $querylimit;
    }
}

/**
 * Get the list of enrolled courses the current user searched for.
 *
 * This function returns a Generator. The courses will be loaded from the database
 * in chunks rather than a single query.
 *
 * @param int $limit Restrict result set to this amount
 * @param int $offset Skip this number of records from the start of the result set
 * @param string|null $sort SQL string for sorting
 * @param string|null $fields SQL string for fields to be returned
 * @param int $dbquerylimit The number of records to load per DB request
 * @param array $searchcriteria contains search criteria
 * @param array $options display options, same as in get_courses() except 'recursive' is ignored -
 *                       search is always category-independent
 * @return Generator
 */
function course_get_enrolled_courses_for_logged_in_user_from_search(
    int $limit = 0,
    int $offset = 0,
    ?string $sort = null,
    ?string $fields = null,
    int $dbquerylimit = COURSE_DB_QUERY_LIMIT,
    array $searchcriteria = [],
    array $options = []
): Generator {

    $haslimit = !empty($limit);
    $recordsloaded = 0;
    $querylimit = (!$haslimit || $limit > $dbquerylimit) ? $dbquerylimit : $limit;
    $ids = core_course_category::search_courses($searchcriteria, $options);

    // If no courses were found matching the criteria return back.
    if (empty($ids)) {
        return;
    }

    while ($courses = enrol_get_my_courses($fields, $sort, $querylimit, $ids, false, $offset)) {
        yield from $courses;

        $recordsloaded += $querylimit;

        if (count($courses) < $querylimit) {
            break;
        }
        if ($haslimit && $recordsloaded >= $limit) {
            break;
        }

        $offset += $querylimit;
    }
}

/**
 * Search the given $courses for any that match the given $classification up to the specified
 * $limit.
 *
 * This function will return the subset of courses that match the classification as well as the
 * number of courses it had to process to build that subset.
 *
 * It is recommended that for larger sets of courses this function is given a Generator that loads
 * the courses from the database in chunks.
 *
 * @param array|Traversable $courses List of courses to process
 * @param string $classification One of the COURSE_TIMELINE_* constants
 * @param int $limit Limit the number of results to this amount
 * @return array First value is the filtered courses, second value is the number of courses processed
 */
function course_filter_courses_by_timeline_classification(
    $courses,
    string $classification,
    int $limit = 0
): array {

    if (!in_array($classification,
            [COURSE_TIMELINE_ALLINCLUDINGHIDDEN, COURSE_TIMELINE_ALL, COURSE_TIMELINE_PAST, COURSE_TIMELINE_INPROGRESS,
                COURSE_TIMELINE_FUTURE, COURSE_TIMELINE_HIDDEN, COURSE_TIMELINE_SEARCH])) {
        $message = 'Classification must be one of COURSE_TIMELINE_ALLINCLUDINGHIDDEN, COURSE_TIMELINE_ALL, COURSE_TIMELINE_PAST, '
            . 'COURSE_TIMELINE_INPROGRESS, COURSE_TIMELINE_SEARCH or COURSE_TIMELINE_FUTURE';
        throw new moodle_exception($message);
    }

    $filteredcourses = [];
    $numberofcoursesprocessed = 0;
    $filtermatches = 0;

    foreach ($courses as $course) {
        $numberofcoursesprocessed++;
        $pref = get_user_preferences('block_myoverview_hidden_course_' . $course->id, 0);

        // Added as of MDL-63457 toggle viewability for each user.
        if ($classification == COURSE_TIMELINE_ALLINCLUDINGHIDDEN || ($classification == COURSE_TIMELINE_HIDDEN && $pref) ||
            $classification == COURSE_TIMELINE_SEARCH||
            (($classification == COURSE_TIMELINE_ALL || $classification == course_classify_for_timeline($course)) && !$pref)) {
            $filteredcourses[] = $course;
            $filtermatches++;
        }

        if ($limit && $filtermatches >= $limit) {
            // We've found the number of requested courses. No need to continue searching.
            break;
        }
    }

    // Return the number of filtered courses as well as the number of courses that were searched
    // in order to find the matching courses. This allows the calling code to do some kind of
    // pagination.
    return [$filteredcourses, $numberofcoursesprocessed];
}

/**
 * Search the given $courses for any that match the given $classification up to the specified
 * $limit.
 *
 * This function will return the subset of courses that are favourites as well as the
 * number of courses it had to process to build that subset.
 *
 * It is recommended that for larger sets of courses this function is given a Generator that loads
 * the courses from the database in chunks.
 *
 * @param array|Traversable $courses List of courses to process
 * @param array $favouritecourseids Array of favourite courses.
 * @param int $limit Limit the number of results to this amount
 * @return array First value is the filtered courses, second value is the number of courses processed
 */
function course_filter_courses_by_favourites(
    $courses,
    $favouritecourseids,
    int $limit = 0
): array {

    $filteredcourses = [];
    $numberofcoursesprocessed = 0;
    $filtermatches = 0;

    foreach ($courses as $course) {
        $numberofcoursesprocessed++;

        if (in_array($course->id, $favouritecourseids)) {
            $filteredcourses[] = $course;
            $filtermatches++;
        }

        if ($limit && $filtermatches >= $limit) {
            // We've found the number of requested courses. No need to continue searching.
            break;
        }
    }

    // Return the number of filtered courses as well as the number of courses that were searched
    // in order to find the matching courses. This allows the calling code to do some kind of
    // pagination.
    return [$filteredcourses, $numberofcoursesprocessed];
}

/**
 * Search the given $courses for any that have a $customfieldname value that matches the given
 * $customfieldvalue, up to the specified $limit.
 *
 * This function will return the subset of courses that matches the value as well as the
 * number of courses it had to process to build that subset.
 *
 * It is recommended that for larger sets of courses this function is given a Generator that loads
 * the courses from the database in chunks.
 *
 * @param array|Traversable $courses List of courses to process
 * @param string $customfieldname the shortname of the custom field to match against
 * @param string $customfieldvalue the value this custom field needs to match
 * @param int $limit Limit the number of results to this amount
 * @return array First value is the filtered courses, second value is the number of courses processed
 */
function course_filter_courses_by_customfield(
    $courses,
    $customfieldname,
    $customfieldvalue,
    int $limit = 0
): array {
    global $DB;

    if (!$courses) {
        return [[], 0];
    }

    // Prepare the list of courses to search through.
    $coursesbyid = [];
    foreach ($courses as $course) {
        $coursesbyid[$course->id] = $course;
    }
    if (!$coursesbyid) {
        return [[], 0];
    }
    list($csql, $params) = $DB->get_in_or_equal(array_keys($coursesbyid), SQL_PARAMS_NAMED);

    // Get the id of the custom field.
    $sql = "
       SELECT f.id
         FROM {customfield_field} f
         JOIN {customfield_category} cat ON cat.id = f.categoryid
        WHERE f.shortname = ?
          AND cat.component = 'core_course'
          AND cat.area = 'course'
    ";
    $fieldid = $DB->get_field_sql($sql, [$customfieldname]);
    if (!$fieldid) {
        return [[], 0];
    }

    // Get a list of courseids that match that custom field value.
    if ($customfieldvalue == COURSE_CUSTOMFIELD_EMPTY) {
        $comparevalue = $DB->sql_compare_text('cd.value');
        $sql = "
           SELECT c.id
             FROM {course} c
        LEFT JOIN {customfield_data} cd ON cd.instanceid = c.id AND cd.fieldid = :fieldid
            WHERE c.id $csql
              AND (cd.value IS NULL OR $comparevalue = '' OR $comparevalue = '0')
        ";
        $params['fieldid'] = $fieldid;
        $matchcourseids = $DB->get_fieldset_sql($sql, $params);
    } else {
        $comparevalue = $DB->sql_compare_text('value');
        $select = "fieldid = :fieldid AND $comparevalue = :customfieldvalue AND instanceid $csql";
        $params['fieldid'] = $fieldid;
        $params['customfieldvalue'] = $customfieldvalue;
        $matchcourseids = $DB->get_fieldset_select('customfield_data', 'instanceid', $select, $params);
    }

    // Prepare the list of courses to return.
    $filteredcourses = [];
    $numberofcoursesprocessed = 0;
    $filtermatches = 0;

    foreach ($coursesbyid as $course) {
        $numberofcoursesprocessed++;

        if (in_array($course->id, $matchcourseids)) {
            $filteredcourses[] = $course;
            $filtermatches++;
        }

        if ($limit && $filtermatches >= $limit) {
            // We've found the number of requested courses. No need to continue searching.
            break;
        }
    }

    // Return the number of filtered courses as well as the number of courses that were searched
    // in order to find the matching courses. This allows the calling code to do some kind of
    // pagination.
    return [$filteredcourses, $numberofcoursesprocessed];
}

/**
 * Check module updates since a given time.
 * This function checks for updates in the module config, file areas, completion, grades, comments and ratings.
 *
 * @param  cm_info $cm        course module data
 * @param  int $from          the time to check
 * @param  array $fileareas   additional file ares to check
 * @param  array $filter      if we need to filter and return only selected updates
 * @return stdClass object with the different updates
 * @since  Moodle 3.2
 */
function course_check_module_updates_since($cm, $from, $fileareas = array(), $filter = array()) {
    global $DB, $CFG, $USER;

    $context = $cm->context;
    $mod = $DB->get_record($cm->modname, array('id' => $cm->instance), '*', MUST_EXIST);

    $updates = new stdClass();
    $course = get_course($cm->course);
    $component = 'mod_' . $cm->modname;

    // Check changes in the module configuration.
    if (isset($mod->timemodified) and (empty($filter) or in_array('configuration', $filter))) {
        $updates->configuration = (object) array('updated' => false);
        if ($updates->configuration->updated = $mod->timemodified > $from) {
            $updates->configuration->timeupdated = $mod->timemodified;
        }
    }

    // Check for updates in files.
    if (plugin_supports('mod', $cm->modname, FEATURE_MOD_INTRO)) {
        $fileareas[] = 'intro';
    }
    if (!empty($fileareas) and (empty($filter) or in_array('fileareas', $filter))) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, $component, $fileareas, false, "filearea, timemodified DESC", false, $from);
        foreach ($fileareas as $filearea) {
            $updates->{$filearea . 'files'} = (object) array('updated' => false);
        }
        foreach ($files as $file) {
            $updates->{$file->get_filearea() . 'files'}->updated = true;
            $updates->{$file->get_filearea() . 'files'}->itemids[] = $file->get_id();
        }
    }

    // Check completion.
    $supportcompletion = plugin_supports('mod', $cm->modname, FEATURE_COMPLETION_HAS_RULES);
    $supportcompletion = $supportcompletion or plugin_supports('mod', $cm->modname, FEATURE_COMPLETION_TRACKS_VIEWS);
    if ($supportcompletion and (empty($filter) or in_array('completion', $filter))) {
        $updates->completion = (object) array('updated' => false);
        $completion = new completion_info($course);
        // Use wholecourse to cache all the modules the first time.
        $completiondata = $completion->get_data($cm, true);
        if ($updates->completion->updated = !empty($completiondata->timemodified) && $completiondata->timemodified > $from) {
            $updates->completion->timemodified = $completiondata->timemodified;
        }
    }

    // Check grades.
    $supportgrades = plugin_supports('mod', $cm->modname, FEATURE_GRADE_HAS_GRADE);
    $supportgrades = $supportgrades or plugin_supports('mod', $cm->modname, FEATURE_GRADE_OUTCOMES);
    if ($supportgrades and (empty($filter) or (in_array('gradeitems', $filter) or in_array('outcomes', $filter)))) {
        require_once($CFG->libdir . '/gradelib.php');
        $grades = grade_get_grades($course->id, 'mod', $cm->modname, $mod->id, $USER->id);

        if (empty($filter) or in_array('gradeitems', $filter)) {
            $updates->gradeitems = (object) array('updated' => false);
            foreach ($grades->items as $gradeitem) {
                foreach ($gradeitem->grades as $grade) {
                    if ($grade->datesubmitted > $from or $grade->dategraded > $from) {
                        $updates->gradeitems->updated = true;
                        $updates->gradeitems->itemids[] = $gradeitem->id;
                    }
                }
            }
        }

        if (empty($filter) or in_array('outcomes', $filter)) {
            $updates->outcomes = (object) array('updated' => false);
            foreach ($grades->outcomes as $outcome) {
                foreach ($outcome->grades as $grade) {
                    if ($grade->datesubmitted > $from or $grade->dategraded > $from) {
                        $updates->outcomes->updated = true;
                        $updates->outcomes->itemids[] = $outcome->id;
                    }
                }
            }
        }
    }

    // Check comments.
    if (plugin_supports('mod', $cm->modname, FEATURE_COMMENT) and (empty($filter) or in_array('comments', $filter))) {
        $updates->comments = (object) ['updated' => false];
        $comments = core_comment\manager::get_component_comments_since($course, $context, $component, $from, $cm);
        if (!empty($comments)) {
            $updates->comments->updated = true;
            $updates->comments->itemids = array_keys($comments);
        }
    }

    // Check ratings.
    if (plugin_supports('mod', $cm->modname, FEATURE_RATE) and (empty($filter) or in_array('ratings', $filter))) {
        $updates->ratings = (object) array('updated' => false);
        require_once($CFG->dirroot . '/rating/lib.php');
        $manager = new rating_manager();
        $ratings = $manager->get_component_ratings_since($context, $component, $from);
        if (!empty($ratings)) {
            $updates->ratings->updated = true;
            $updates->ratings->itemids = array_keys($ratings);
        }
    }

    return $updates;
}

/**
 * Returns true if the user can view the participant page, false otherwise,
 *
 * @param context $context The context we are checking.
 * @return bool
 */
function course_can_view_participants($context) {
    $viewparticipantscap = 'moodle/course:viewparticipants';
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $viewparticipantscap = 'moodle/site:viewparticipants';
    }

    return has_any_capability([$viewparticipantscap, 'moodle/course:enrolreview'], $context);
}

/**
 * Checks if a user can view the participant page, if not throws an exception.
 *
 * @param context $context The context we are checking.
 * @throws required_capability_exception
 */
function course_require_view_participants($context) {
    if (!course_can_view_participants($context)) {
        $viewparticipantscap = 'moodle/course:viewparticipants';
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $viewparticipantscap = 'moodle/site:viewparticipants';
        }
        throw new required_capability_exception($context, $viewparticipantscap, 'nopermissions', '');
    }
}

/**
 * Return whether the user can download from the specified backup file area in the given context.
 *
 * @param string $filearea the backup file area. E.g. 'course', 'backup' or 'automated'.
 * @param \context $context
 * @param stdClass $user the user object. If not provided, the current user will be checked.
 * @return bool true if the user is allowed to download in the context, false otherwise.
 */
function can_download_from_backup_filearea($filearea, \context $context, ?stdClass $user = null) {
    $candownload = false;
    switch ($filearea) {
        case 'course':
        case 'backup':
            $candownload = has_capability('moodle/backup:downloadfile', $context, $user);
            break;
        case 'automated':
            // Given the automated backups may contain userinfo, we restrict access such that only users who are able to
            // restore with userinfo are able to download the file. Users can't create these backups, so checking 'backup:userinfo'
            // doesn't make sense here.
            $candownload = has_capability('moodle/backup:downloadfile', $context, $user) &&
                           has_capability('moodle/restore:userinfo', $context, $user);
            break;
        default:
            break;

    }
    return $candownload;
}

/**
 * Get a list of hidden courses
 *
 * @param int|object|null $user User override to get the filter from. Defaults to current user
 * @return array $ids List of hidden courses
 * @throws coding_exception
 */
function get_hidden_courses_on_timeline($user = null) {
    global $USER;

    if (empty($user)) {
        $user = $USER->id;
    }

    $preferences = get_user_preferences(null, null, $user);
    $ids = [];
    foreach ($preferences as $key => $value) {
        if (preg_match('/block_myoverview_hidden_course_(\d)+/', $key)) {
            $id = preg_split('/block_myoverview_hidden_course_/', $key);
            $ids[] = $id[1];
        }
    }

    return $ids;
}

/**
 * Returns a list of the most recently courses accessed by a user
 *
 * @param int $userid User id from which the courses will be obtained
 * @param int $limit Restrict result set to this amount
 * @param int $offset Skip this number of records from the start of the result set
 * @param string|null $sort SQL string for sorting
 * @return array
 */
function course_get_recent_courses(?int $userid = null, int $limit = 0, int $offset = 0, ?string $sort = null) {

    global $CFG, $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $basefields = [
        'id', 'idnumber', 'summary', 'summaryformat', 'startdate', 'enddate', 'category',
        'shortname', 'fullname', 'timeaccess', 'component', 'visible',
        'showactivitydates', 'showcompletionconditions', 'pdfexportfont'
    ];

    if (empty($sort)) {
        $sort = 'timeaccess DESC';
    } else {
        // The SQL string for sorting can define sorting by multiple columns.
        $rawsorts = explode(',', $sort);
        $sorts = array();
        // Validate and trim the sort parameters in the SQL string for sorting.
        foreach ($rawsorts as $rawsort) {
            $sort = trim($rawsort);
            $sortparams = explode(' ', $sort);
            // A valid sort statement can not have more than 2 params (ex. 'summary desc' or 'timeaccess').
            if (count($sortparams) > 2) {
                throw new invalid_parameter_exception(
                    'Invalid structure of the sort parameter, allowed structure: fieldname [ASC|DESC].');
            }
            $sortfield = trim($sortparams[0]);
            // Validate the value which defines the field to sort by.
            if (!in_array($sortfield, $basefields)) {
                throw new invalid_parameter_exception('Invalid field in the sort parameter, allowed fields: ' .
                    implode(', ', $basefields) . '.');
            }
            $sortdirection = isset($sortparams[1]) ? trim($sortparams[1]) : '';
            // Validate the value which defines the sort direction (if present).
            $allowedsortdirections = ['asc', 'desc'];
            if (!empty($sortdirection) && !in_array(strtolower($sortdirection), $allowedsortdirections)) {
                throw new invalid_parameter_exception('Invalid sort direction in the sort parameter, allowed values: ' .
                    implode(', ', $allowedsortdirections) . '.');
            }
            $sorts[] = $sort;
        }
        $sort = implode(',', $sorts);
    }

    $ctxfields = context_helper::get_preload_record_columns_sql('ctx');

    $coursefields = 'c.' . join(',', $basefields);

    // Ask the favourites service to give us the join SQL for favourited courses,
    // so we can include favourite information in the query.
    $usercontext = \context_user::instance($userid);
    $favservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
    list($favsql, $favparams) = $favservice->get_join_sql_by_type('core_course', 'courses', 'fav', 'ul.courseid');

    $sql = "SELECT $coursefields, $ctxfields
              FROM {course} c
              JOIN {context} ctx
                   ON ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = c.id
              JOIN {user_lastaccess} ul
                   ON ul.courseid = c.id
            $favsql
         LEFT JOIN {enrol} eg ON eg.courseid = c.id AND eg.status = :statusenrolg AND eg.enrol = :guestenrol
             WHERE ul.userid = :userid
               AND c.visible = :visible
               AND (eg.id IS NOT NULL
                    OR EXISTS (SELECT e.id
                             FROM {enrol} e
                             JOIN {user_enrolments} ue ON ue.enrolid = e.id
                            WHERE e.courseid = c.id
                              AND e.status = :statusenrol
                              AND ue.status = :status
                              AND ue.userid = :userid2
                              AND ue.timestart < :now1
                              AND (ue.timeend = 0 OR ue.timeend > :now2)
                          ))
          ORDER BY $sort";

    $now = round(time(), -2); // Improves db caching.
    $params = ['userid' => $userid, 'contextlevel' => CONTEXT_COURSE, 'visible' => 1, 'status' => ENROL_USER_ACTIVE,
               'statusenrol' => ENROL_INSTANCE_ENABLED, 'guestenrol' => 'guest', 'now1' => $now, 'now2' => $now,
               'userid2' => $userid, 'statusenrolg' => ENROL_INSTANCE_ENABLED] + $favparams;

    $recentcourses = $DB->get_records_sql($sql, $params, $offset, $limit);

    // Filter courses if last access field is hidden.
    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));

    if ($userid != $USER->id && isset($hiddenfields['lastaccess'])) {
        $recentcourses = array_filter($recentcourses, function($course) {
            context_helper::preload_from_record($course);
            $context = context_course::instance($course->id, IGNORE_MISSING);
            // If last access was a hidden field, a user requesting info about another user would need permission to view hidden
            // fields.
            return has_capability('moodle/course:viewhiddenuserfields', $context);
        });
    }

    return $recentcourses;
}

/**
 * Calculate the course start date and offset for the given user ids.
 *
 * If the course is a fixed date course then the course start date will be returned.
 * If the course is a relative date course then the course date will be calculated and
 * and offset provided.
 *
 * The dates are returned as an array with the index being the user id. The array
 * contains the start date and start offset values for the user.
 *
 * If the user is not enrolled in the course then the course start date will be returned.
 *
 * If we have a course which starts on 1563244000 and 2 users, id 123 and 456, where the
 * former is enrolled in the course at 1563244693 and the latter is not enrolled then the
 * return value would look like:
 * [
 *      '123' => [
 *          'start' => 1563244693,
 *          'startoffset' => 693
 *      ],
 *      '456' => [
 *          'start' => 1563244000,
 *          'startoffset' => 0
 *      ]
 * ]
 *
 * @param stdClass $course The course to fetch dates for.
 * @param array $userids The list of user ids to get dates for.
 * @return array
 */
function course_get_course_dates_for_user_ids(stdClass $course, array $userids): array {
    if (empty($course->relativedatesmode)) {
        // This course isn't set to relative dates so we can early return with the course
        // start date.
        return array_reduce($userids, function($carry, $userid) use ($course) {
            $carry[$userid] = [
                'start' => $course->startdate,
                'startoffset' => 0
            ];
            return $carry;
        }, []);
    }

    // We're dealing with a relative dates course now so we need to calculate some dates.
    $cache = cache::make('core', 'course_user_dates');
    $dates = [];
    $uncacheduserids = [];

    // Try fetching the values from the cache so that we don't need to do a DB request.
    foreach ($userids as $userid) {
        $cachekey = "{$course->id}_{$userid}";
        $cachedvalue = $cache->get($cachekey);

        if ($cachedvalue === false) {
            // Looks like we haven't seen this user for this course before so we'll have
            // to fetch it.
            $uncacheduserids[] = $userid;
        } else {
            [$start, $startoffset] = $cachedvalue;
            $dates[$userid] = [
                'start' => $start,
                'startoffset' => $startoffset
            ];
        }
    }

    if (!empty($uncacheduserids)) {
        // Load the enrolments for any users we haven't seen yet. Set the "onlyactive" param
        // to false because it filters out users with enrolment start times in the future which
        // we don't want.
        $enrolments = enrol_get_course_users($course->id, false, $uncacheduserids);

        foreach ($uncacheduserids as $userid) {
            // Find the user enrolment that has the earliest start date.
            $enrolment = array_reduce(array_values($enrolments), function($carry, $enrolment) use ($userid) {
                // Only consider enrolments for this user if the user enrolment is active and the
                // enrolment method is enabled.
                if (
                    $enrolment->uestatus == ENROL_USER_ACTIVE &&
                    $enrolment->estatus == ENROL_INSTANCE_ENABLED &&
                    $enrolment->id == $userid
                ) {
                    if (is_null($carry)) {
                        // Haven't found an enrolment yet for this user so use the one we just found.
                        $carry = $enrolment;
                    } else {
                        // We've already found an enrolment for this user so let's use which ever one
                        // has the earliest start time.
                        $carry = $carry->uetimestart < $enrolment->uetimestart ? $carry : $enrolment;
                    }
                }

                return $carry;
            }, null);

            if ($enrolment) {
                // The course is in relative dates mode so we calculate the student's start
                // date based on their enrolment start date.
                $start = $course->startdate > $enrolment->uetimestart ? $course->startdate : $enrolment->uetimestart;
                $startoffset = $start - $course->startdate;
            } else {
                // The user is not enrolled in the course so default back to the course start date.
                $start = $course->startdate;
                $startoffset = 0;
            }

            $dates[$userid] = [
                'start' => $start,
                'startoffset' => $startoffset
            ];

            $cachekey = "{$course->id}_{$userid}";
            $cache->set($cachekey, [$start, $startoffset]);
        }
    }

    return $dates;
}

/**
 * Calculate the course start date and offset for the given user id.
 *
 * If the course is a fixed date course then the course start date will be returned.
 * If the course is a relative date course then the course date will be calculated and
 * and offset provided.
 *
 * The return array contains the start date and start offset values for the user.
 *
 * If the user is not enrolled in the course then the course start date will be returned.
 *
 * If we have a course which starts on 1563244000. If a user's enrolment starts on 1563244693
 * then the return would be:
 * [
 *      'start' => 1563244693,
 *      'startoffset' => 693
 * ]
 *
 * If the use was not enrolled then the return would be:
 * [
 *      'start' => 1563244000,
 *      'startoffset' => 0
 * ]
 *
 * @param stdClass $course The course to fetch dates for.
 * @param int $userid The user id to get dates for.
 * @return array
 */
function course_get_course_dates_for_user_id(stdClass $course, int $userid): array {
    return (course_get_course_dates_for_user_ids($course, [$userid]))[$userid];
}

/**
 * Renders the course copy form for the modal on the course management screen.
 *
 * @param array $args
 * @return string $o Form HTML.
 */
function course_output_fragment_new_base_form($args) {

    $serialiseddata = json_decode($args['jsonformdata'], true);
    $formdata = [];
    if (!empty($serialiseddata)) {
        parse_str($serialiseddata, $formdata);
    }

    $context = context_course::instance($args['courseid']);
    $copycaps = \core_course\management\helper::get_course_copy_capabilities();
    require_all_capabilities($copycaps, $context);

    $course = get_course($args['courseid']);
    $mform = new \core_backup\output\copy_form(
        null,
        array('course' => $course, 'returnto' => '', 'returnurl' => ''),
        'post', '', ['class' => 'ignoredirty'], true, $formdata);

    if (!empty($serialiseddata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o = ob_get_contents();
    ob_end_clean();

    return $o;
}

/**
 * Get the course overview fragment.
 *
 * @param array $args the fragment arguments
 * @return string the course overview fragment
 *
 * @throws require_login_exception
 */
function course_output_fragment_course_overview($args) {
    global $PAGE;
    if (empty($args['modname']) || empty($args['courseid'])) {
        throw new coding_exception('modname and courseid are required');
    }
    $modname = $args['modname'];
    $course = get_course($args['courseid']);
    $context = context_course::instance($course->id, MUST_EXIST);

    if (!can_access_course($course, null, '', true)) {
        throw new require_login_exception('Course is not available');
    }

    // Some plugins may have a list view event.
    $eventclassname = 'mod_' . $modname . '\\event\\course_module_instance_list_viewed';
    // Do not confuse this "resource" with the "mod_resource" module.
    // This "resource" is the table that aggregate all activities considered "resources"
    // (files, folders, pages, text and media...). While the "mod_resource" is a poorly
    // named plugin representing an uploaded file, and it is also one of the activities
    // that can be aggregated in the "resource" table.
    if ($modname === 'resource') {
        $eventclassname = 'core\\event\\course_resources_list_viewed';
    }
    if (class_exists($eventclassname)) {
        try {
            $event = $eventclassname::create(['context' => $context]);
            $event->add_record_snapshot('course', $course);
            $event->trigger();
        } catch (\Throwable $th) {
            // This may happens if the plugin implements a custom event class.
            // It is highly unlikely but we should not stop the rendering because of this.
            // Instead, we will log the error and continue.
            debugging('Error while triggering the course module instance viewed event: ' . $th->getMessage());
        }
    }

    $content = '';
    $format = course_get_format($course);
    $renderer = $format->get_renderer($PAGE);

    // Plugins with not implemented overview table will have an extra link to the index.php.
    $overvietableclass = $format->get_output_classname('overview\missingoverviewnotice');
    /** @var \core_courseformat\output\local\overview\missingoverviewnotice $output */
    $output = new $overvietableclass($course, $modname);
    $content .= $renderer->render($output);

    $overvietableclass = $format->get_output_classname('overview\\overviewtable');
    /** @var \core_courseformat\output\local\overview\overviewtable $output */
    $output = new $overvietableclass($course, $modname);
    $content .= $renderer->render($output);

    return $content;
}

/**
 * Get the current course image for the given course.
 *
 * @param \stdClass $course
 * @return null|stored_file
 */
function course_get_courseimage(\stdClass $course): ?stored_file {
    $courseinlist = new core_course_list_element($course);
    foreach ($courseinlist->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            return $file;
        }
    }
    return null;
}

/**
 * Get course specific data for configuring a communication instance.
 *
 * @param integer $courseid The course id.
 * @return array Returns course data, context and heading.
 */
function course_get_communication_instance_data(int $courseid): array {
    // Do some checks and prepare instance specific data.
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($course->id);
    require_capability('moodle/course:configurecoursecommunication', $context);

    $heading = $course->fullname;
    $returnurl = new moodle_url('/course/view.php', ['id' => $courseid]);

    return [$course, $context, $heading, $returnurl];
}

/**
 * Update a course using communication configuration data.
 *
 * @param stdClass $data The data to update the course with.
 */
function course_update_communication_instance_data(stdClass $data): void {
    $data->id = $data->instanceid; // For correct use in update_course.
    // If the room name is set to empty, then set it course name.
    $provider = $data->selectedcommunication ?? null;
    $roomnameidentifier = $provider . 'roomname';
    if ($provider && empty($data->$roomnameidentifier)) {
        $data->$roomnameidentifier = $data->fullname ?? get_course($data->id)->fullname;
    }
    core_communication\helper::update_course_communication_instance(
        course: $data,
        changesincoursecat: false,
    );
}

/**
 * Trigger course section viewed event.
 *
 * @param context_course $context course context object
 * @param int $sectionid section number
 * @since Moodle 4.4.
 */
function course_section_view(context_course $context, int $sectionid) {

    $eventdata = [
        'objectid' => $sectionid,
        'context' => $context,
    ];
    $event = \core\event\section_viewed::create($eventdata);
    $event->trigger();

    user_accesstime_log($context->instanceid);
}
