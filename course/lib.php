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

define('COURSE_TIMELINE_ALL', 'all');
define('COURSE_TIMELINE_PAST', 'past');
define('COURSE_TIMELINE_INPROGRESS', 'inprogress');
define('COURSE_TIMELINE_FUTURE', 'future');
define('COURSE_FAVOURITES', 'favourites');
define('COURSE_TIMELINE_HIDDEN', 'hidden');
define('COURSE_DB_QUERY_LIMIT', 1000);

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
 * @return array array of messages with found problems. Empty output means everything is ok
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
 * For a given course, returns an array of course activity objects
 * Each item in the array contains he following properties:
 */
function get_array_of_activities($courseid) {
//  cm - course module id
//  mod - name of the module (eg forum)
//  section - the number of the section (eg week or topic)
//  name - the name of the instance
//  visible - is the instance visible or not
//  groupingid - grouping id
//  extra - contains extra string to include in any link
    global $CFG, $DB;

    $course = $DB->get_record('course', array('id'=>$courseid));

    if (empty($course)) {
        throw new moodle_exception('courseidnotfound');
    }

    $mod = array();

    $rawmods = get_course_mods($courseid);
    if (empty($rawmods)) {
        return $mod; // always return array
    }
    $courseformat = course_get_format($course);

    if ($sections = $DB->get_records('course_sections', array('course' => $courseid),
            'section ASC', 'id,section,sequence,visible')) {
        // First check and correct obvious mismatches between course_sections.sequence and course_modules.section.
        if ($errormessages = course_integrity_check($courseid, $rawmods, $sections)) {
            debugging(join('<br>', $errormessages));
            $rawmods = get_course_mods($courseid);
            $sections = $DB->get_records('course_sections', array('course' => $courseid),
                'section ASC', 'id,section,sequence,visible');
        }
        // Build array of activities.
       foreach ($sections as $section) {
           if (!empty($section->sequence)) {
               $sequence = explode(",", $section->sequence);
               foreach ($sequence as $seq) {
                   if (empty($rawmods[$seq])) {
                       continue;
                   }
                   // Adjust visibleoncoursepage, value in DB may not respect format availability.
                   $rawmods[$seq]->visibleoncoursepage = (!$rawmods[$seq]->visible
                           || $rawmods[$seq]->visibleoncoursepage
                           || empty($CFG->allowstealth)
                           || !$courseformat->allow_stealth_module_visibility($rawmods[$seq], $section)) ? 1 : 0;

                   // Create an object that will be cached.
                   $mod[$seq] = new stdClass();
                   $mod[$seq]->id               = $rawmods[$seq]->instance;
                   $mod[$seq]->cm               = $rawmods[$seq]->id;
                   $mod[$seq]->mod              = $rawmods[$seq]->modname;

                    // Oh dear. Inconsistent names left here for backward compatibility.
                   $mod[$seq]->section          = $section->section;
                   $mod[$seq]->sectionid        = $rawmods[$seq]->section;

                   $mod[$seq]->module           = $rawmods[$seq]->module;
                   $mod[$seq]->added            = $rawmods[$seq]->added;
                   $mod[$seq]->score            = $rawmods[$seq]->score;
                   $mod[$seq]->idnumber         = $rawmods[$seq]->idnumber;
                   $mod[$seq]->visible          = $rawmods[$seq]->visible;
                   $mod[$seq]->visibleoncoursepage = $rawmods[$seq]->visibleoncoursepage;
                   $mod[$seq]->visibleold       = $rawmods[$seq]->visibleold;
                   $mod[$seq]->groupmode        = $rawmods[$seq]->groupmode;
                   $mod[$seq]->groupingid       = $rawmods[$seq]->groupingid;
                   $mod[$seq]->indent           = $rawmods[$seq]->indent;
                   $mod[$seq]->completion       = $rawmods[$seq]->completion;
                   $mod[$seq]->extra            = "";
                   $mod[$seq]->completiongradeitemnumber =
                           $rawmods[$seq]->completiongradeitemnumber;
                   $mod[$seq]->completionview   = $rawmods[$seq]->completionview;
                   $mod[$seq]->completionexpected = $rawmods[$seq]->completionexpected;
                   $mod[$seq]->showdescription  = $rawmods[$seq]->showdescription;
                   $mod[$seq]->availability = $rawmods[$seq]->availability;
                   $mod[$seq]->deletioninprogress = $rawmods[$seq]->deletioninprogress;

                   $modname = $mod[$seq]->mod;
                   $functionname = $modname."_get_coursemodule_info";

                   if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
                       continue;
                   }

                   include_once("$CFG->dirroot/mod/$modname/lib.php");

                   if ($hasfunction = function_exists($functionname)) {
                       if ($info = $functionname($rawmods[$seq])) {
                           if (!empty($info->icon)) {
                               $mod[$seq]->icon = $info->icon;
                           }
                           if (!empty($info->iconcomponent)) {
                               $mod[$seq]->iconcomponent = $info->iconcomponent;
                           }
                           if (!empty($info->name)) {
                               $mod[$seq]->name = $info->name;
                           }
                           if ($info instanceof cached_cm_info) {
                               // When using cached_cm_info you can include three new fields
                               // that aren't available for legacy code
                               if (!empty($info->content)) {
                                   $mod[$seq]->content = $info->content;
                               }
                               if (!empty($info->extraclasses)) {
                                   $mod[$seq]->extraclasses = $info->extraclasses;
                               }
                               if (!empty($info->iconurl)) {
                                   // Convert URL to string as it's easier to store. Also serialized object contains \0 byte and can not be written to Postgres DB.
                                   $url = new moodle_url($info->iconurl);
                                   $mod[$seq]->iconurl = $url->out(false);
                               }
                               if (!empty($info->onclick)) {
                                   $mod[$seq]->onclick = $info->onclick;
                               }
                               if (!empty($info->customdata)) {
                                   $mod[$seq]->customdata = $info->customdata;
                               }
                           } else {
                               // When using a stdclass, the (horrible) deprecated ->extra field
                               // is available for BC
                               if (!empty($info->extra)) {
                                   $mod[$seq]->extra = $info->extra;
                               }
                           }
                       }
                   }
                   // When there is no modname_get_coursemodule_info function,
                   // but showdescriptions is enabled, then we use the 'intro'
                   // and 'introformat' fields in the module table
                   if (!$hasfunction && $rawmods[$seq]->showdescription) {
                       if ($modvalues = $DB->get_record($rawmods[$seq]->modname,
                               array('id' => $rawmods[$seq]->instance), 'name, intro, introformat')) {
                           // Set content from intro and introformat. Filters are disabled
                           // because we  filter it with format_text at display time
                           $mod[$seq]->content = format_module_intro($rawmods[$seq]->modname,
                                   $modvalues, $rawmods[$seq]->id, false);

                           // To save making another query just below, put name in here
                           $mod[$seq]->name = $modvalues->name;
                       }
                   }
                   if (!isset($mod[$seq]->name)) {
                       $mod[$seq]->name = $DB->get_field($rawmods[$seq]->modname, "name", array("id"=>$rawmods[$seq]->instance));
                   }

                    // Minimise the database size by unsetting default options when they are
                    // 'empty'. This list corresponds to code in the cm_info constructor.
                    foreach (array('idnumber', 'groupmode', 'groupingid',
                            'indent', 'completion', 'extra', 'extraclasses', 'iconurl', 'onclick', 'content',
                            'icon', 'iconcomponent', 'customdata', 'availability', 'completionview',
                            'completionexpected', 'score', 'showdescription', 'deletioninprogress') as $property) {
                       if (property_exists($mod[$seq], $property) &&
                               empty($mod[$seq]->{$property})) {
                           unset($mod[$seq]->{$property});
                       }
                   }
                   // Special case: this value is usually set to null, but may be 0
                   if (property_exists($mod[$seq], 'completiongradeitemnumber') &&
                           is_null($mod[$seq]->completiongradeitemnumber)) {
                       unset($mod[$seq]->completiongradeitemnumber);
                   }
               }
            }
        }
    }
    return $mod;
}

/**
 * Returns the localised human-readable names of all used modules
 *
 * @param bool $plural if true returns the plural forms of the names
 * @return array where key is the module name (component name without 'mod_') and
 *     the value is the human-readable string. Array sorted alphabetically by value
 */
function get_module_types_names($plural = false) {
    static $modnames = null;
    global $DB, $CFG;
    if ($modnames === null) {
        $modnames = array(0 => array(), 1 => array());
        if ($allmods = $DB->get_records("modules")) {
            foreach ($allmods as $mod) {
                if (file_exists("$CFG->dirroot/mod/$mod->name/lib.php") && $mod->visible) {
                    $modnames[0][$mod->name] = get_string("modulename", "$mod->name");
                    $modnames[1][$mod->name] = get_string("modulenameplural", "$mod->name");
                }
            }
            core_collator::asort($modnames[0]);
            core_collator::asort($modnames[1]);
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
    if (class_exists('format_base')) {
        format_base::reset_course_cache($courseid);
    }
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
 * Retrieve all metadata for the requested modules
 *
 * @param object $course The Course
 * @param array $modnames An array containing the list of modules and their
 * names
 * @param int $sectionreturn The section to return to
 * @return array A list of stdClass objects containing metadata about each
 * module
 */
function get_module_metadata($course, $modnames, $sectionreturn = null) {
    global $OUTPUT;

    // get_module_metadata will be called once per section on the page and courses may show
    // different modules to one another
    static $modlist = array();
    if (!isset($modlist[$course->id])) {
        $modlist[$course->id] = array();
    }

    $return = array();
    $urlbase = new moodle_url('/course/mod.php', array('id' => $course->id, 'sesskey' => sesskey()));
    if ($sectionreturn !== null) {
        $urlbase->param('sr', $sectionreturn);
    }
    foreach($modnames as $modname => $modnamestr) {
        if (!course_allowed_module($course, $modname)) {
            continue;
        }
        if (isset($modlist[$course->id][$modname])) {
            // This module is already cached
            $return += $modlist[$course->id][$modname];
            continue;
        }
        $modlist[$course->id][$modname] = array();

        // Create an object for a default representation of this module type in the activity chooser. It will be used
        // if module does not implement callback get_shortcuts() and it will also be passed to the callback if it exists.
        $defaultmodule = new stdClass();
        $defaultmodule->title = $modnamestr;
        $defaultmodule->name = $modname;
        $defaultmodule->link = new moodle_url($urlbase, array('add' => $modname));
        $defaultmodule->icon = $OUTPUT->pix_icon('icon', '', $defaultmodule->name, array('class' => 'icon'));
        $sm = get_string_manager();
        if ($sm->string_exists('modulename_help', $modname)) {
            $defaultmodule->help = get_string('modulename_help', $modname);
            if ($sm->string_exists('modulename_link', $modname)) {  // Link to further info in Moodle docs.
                $link = get_string('modulename_link', $modname);
                $linktext = get_string('morehelp');
                $defaultmodule->help .= html_writer::tag('div',
                    $OUTPUT->doc_link($link, $linktext, true), array('class' => 'helpdoclink'));
            }
        }
        $defaultmodule->archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);

        // Each module can implement callback modulename_get_shortcuts() in its lib.php and return the list
        // of elements to be added to activity chooser.
        $items = component_callback($modname, 'get_shortcuts', array($defaultmodule), null);
        if ($items !== null) {
            foreach ($items as $item) {
                // Add all items to the return array. All items must have different links, use them as a key in the return array.
                if (!isset($item->archetype)) {
                    $item->archetype = $defaultmodule->archetype;
                }
                if (!isset($item->icon)) {
                    $item->icon = $defaultmodule->icon;
                }
                // If plugin returned the only one item with the same link as default item - cache it as $modname,
                // otherwise append the link url to the module name.
                $item->name = (count($items) == 1 &&
                    $item->link->out() === $defaultmodule->link->out()) ? $modname : $modname . ':' . $item->link;

                // If the module provides the helptext property, append it to the help text to match the look and feel
                // of the default course modules.
                if (isset($item->help) && isset($item->helplink)) {
                    $linktext = get_string('morehelp');
                    $item->help .= html_writer::tag('div',
                        $OUTPUT->doc_link($item->helplink, $linktext, true), array('class' => 'helpdoclink'));
                }
                $modlist[$course->id][$modname][$item->name] = $item;
            }
            $return += $modlist[$course->id][$modname];
            // If get_shortcuts() callback is defined, the default module action is not added.
            // It is a responsibility of the callback to add it to the return value unless it is not needed.
            continue;
        }

        // The callback get_shortcuts() was not found, use the default item for the activity chooser.
        $modlist[$course->id][$modname][$modname] = $defaultmodule;
        $return[$modname] = $defaultmodule;
    }

    core_collator::asort_objects_by_property($return, 'title');
    return $return;
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
 * Returns full course categories trees to be used in html_writer::select()
 *
 * Calls {@link core_course_category::make_categories_list()} to build the tree and
 * adds whitespace to denote nesting
 *
 * @return array array mapping course category id to the display name
 */
function make_categories_options() {
    $cats = core_course_category::make_categories_list('', 0, ' / ');
    foreach ($cats as $key => $value) {
        // Prefix the value with the number of spaces equal to category depth (number of separators in the value).
        $cats[$key] = str_repeat('&nbsp;', substr_count($value, ' / ')). $value;
    }
    return $cats;
}

/**
 * Print the buttons relating to course requests.
 *
 * @param object $context current page context.
 */
function print_course_request_buttons($context) {
    global $CFG, $DB, $OUTPUT;
    if (empty($CFG->enablecourserequests)) {
        return;
    }
    if (!has_capability('moodle/course:create', $context) && has_capability('moodle/course:request', $context)) {
    /// Print a button to request a new course
        echo $OUTPUT->single_button(new moodle_url('/course/request.php'), get_string('requestcourse'), 'get');
    }
    /// Print a button to manage pending requests
    if ($context->contextlevel == CONTEXT_SYSTEM && has_capability('moodle/site:approvecourse', $context)) {
        $disabled = !$DB->record_exists('course_request', array());
        echo $OUTPUT->single_button(new moodle_url('/course/pending.php'), get_string('coursespending'), 'get', array('disabled' => $disabled));
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
    global $DB;
    $courseid = is_object($courseorid) ? $courseorid->id : $courseorid;

    // Find the last sectionnum among existing sections.
    if ($skipcheck) {
        $lastsection = $position - 1;
    } else {
        $lastsection = (int)$DB->get_field_sql('SELECT max(section) from {course_sections} WHERE course = ?', [$courseid]);
    }

    // First add section to the end.
    $cw = new stdClass();
    $cw->course   = $courseid;
    $cw->section  = $lastsection + 1;
    $cw->summary  = '';
    $cw->summaryformat = FORMAT_HTML;
    $cw->sequence = '';
    $cw->name = null;
    $cw->visible = 1;
    $cw->availability = null;
    $cw->timemodified = time();
    $cw->id = $DB->insert_record("course_sections", $cw);

    // Now move it to the specified position.
    if ($position > 0 && $position <= $lastsection) {
        $course = is_object($courseorid) ? $courseorid : get_course($courseorid);
        move_section_to($course, $cw->section, $position, true);
        $cw->section = $position;
    }

    core\event\course_section_created::create_from_section($cw)->trigger();

    rebuild_course_cache($courseid, true);
    return $cw;
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
    $existing = array_keys(get_fast_modinfo($courseorid)->get_section_info_all());
    if ($newsections = array_diff($sections, $existing)) {
        foreach ($newsections as $sectionnum) {
            course_create_section($courseorid, $sectionnum, true);
        }
        return true;
    }
    return false;
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
 * @return int The course_sections ID where the module is inserted
 */
function course_add_cm_to_section($courseorid, $cmid, $sectionnum, $beforemod = null) {
    global $DB, $COURSE;
    if (is_object($beforemod)) {
        $beforemod = $beforemod->id;
    }
    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
    } else {
        $courseid = $courseorid;
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
    } else if ($beforemod && ($key = array_keys($modarray, $beforemod))) {
        $insertarray = array($cmid, $beforemod);
        array_splice($modarray, $key[0], 1, $insertarray);
        $newsequence = implode(",", $modarray);
    } else {
        $newsequence = "$section->sequence,$cmid";
    }
    $DB->set_field("course_sections", "sequence", $newsequence, array("id" => $section->id));
    $DB->set_field('course_modules', 'section', $section->id, array('id' => $cmid));
    if (is_object($courseorid)) {
        rebuild_course_cache($courseorid->id, true);
    } else {
        rebuild_course_cache($courseorid, true);
    }
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
        rebuild_course_cache($cm->course, true);
    }
    return ($cm->groupmode != $groupmode);
}

function set_coursemodule_idnumber($id, $idnumber) {
    global $DB;
    $cm = $DB->get_record('course_modules', array('id' => $id), 'id,course,idnumber', MUST_EXIST);
    if ($cm->idnumber != $idnumber) {
        $DB->set_field('course_modules', 'idnumber', $idnumber, array('id' => $cm->id));
        rebuild_course_cache($cm->course, true);
    }
    return ($cm->idnumber != $idnumber);
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
 * @param int $id of the module
 * @param int $visible state of the module
 * @param int $visibleoncoursepage state of the module on the course page
 * @return bool false when the module was not found, true otherwise
 */
function set_coursemodule_visible($id, $visible, $visibleoncoursepage = 1) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/calendar/lib.php');

    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return false;
    }

    // Create events and propagate visibility to associated grade items if the value has changed.
    // Only do this if it's changed to avoid accidently overwriting manual showing/hiding of student grades.
    if ($cm->visible == $visible && $cm->visibleoncoursepage == $visibleoncoursepage) {
        return true;
    }

    if (!$modulename = $DB->get_field('modules', 'name', array('id'=>$cm->module))) {
        return false;
    }
    if (($cm->visible != $visible) &&
            ($events = $DB->get_records('event', array('instance' => $cm->instance, 'modulename' => $modulename)))) {
        foreach($events as $event) {
            if ($visible) {
                $event = new calendar_event($event);
                $event->toggle_visibility(true);
            } else {
                $event = new calendar_event($event);
                $event->toggle_visibility(false);
            }
        }
    }

    // Updating visible and visibleold to keep them in sync. Only changing a section visibility will
    // affect visibleold to allow for an original visibility restore. See set_section_visible().
    $cminfo = new stdClass();
    $cminfo->id = $id;
    $cminfo->visible = $visible;
    $cminfo->visibleoncoursepage = $visibleoncoursepage;
    $cminfo->visibleold = $visible;
    $DB->update_record('course_modules', $cminfo);

    // Hide the associated grade items so the teacher doesn't also have to go to the gradebook and hide them there.
    // Note that this must be done after updating the row in course_modules, in case
    // the modules grade_item_update function needs to access $cm->visible.
    if ($cm->visible != $visible &&
            plugin_supports('mod', $modulename, FEATURE_CONTROLS_GRADE_VISIBILITY) &&
            component_callback_exists('mod_' . $modulename, 'grade_item_update')) {
        $instance = $DB->get_record($modulename, array('id' => $cm->instance), '*', MUST_EXIST);
        component_callback('mod_' . $modulename, 'grade_item_update', array($instance));
    } else if ($cm->visible != $visible) {
        $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course));
        if ($grade_items) {
            foreach ($grade_items as $grade_item) {
                $grade_item->set_hidden(!$visible);
            }
        }
    }

    rebuild_course_cache($cm->course, true);
    return true;
}

/**
 * Changes the course module name
 *
 * @param int $id course module id
 * @param string $name new value for a name
 * @return bool whether a change was made
 */
function set_coursemodule_name($id, $name) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');

    $cm = get_coursemodule_from_id('', $id, 0, false, MUST_EXIST);

    $module = new \stdClass();
    $module->id = $cm->instance;

    // Escape strings as they would be by mform.
    if (!empty($CFG->formatstringstriptags)) {
        $module->name = clean_param($name, PARAM_TEXT);
    } else {
        $module->name = clean_param($name, PARAM_CLEANHTML);
    }
    if ($module->name === $cm->name || strval($module->name) === '') {
        return false;
    }
    if (\core_text::strlen($module->name) > 255) {
        throw new \moodle_exception('maximumchars', 'moodle', '', 255);
    }

    $module->timemodified = time();
    $DB->update_record($cm->modname, $module);
    $cm->name = $module->name;
    \core\event\course_module_updated::create_from_cm($cm)->trigger();
    rebuild_course_cache($cm->course, true);

    // Attempt to update the grade item if relevant.
    $grademodule = $DB->get_record($cm->modname, array('id' => $cm->instance));
    $grademodule->cmidnumber = $cm->idnumber;
    $grademodule->modname = $cm->modname;
    grade_update_mod_grades($grademodule);

    // Update calendar events with the new name.
    course_module_update_calendar_events($cm->modname, $grademodule, $cm);

    return true;
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

    // Delete activity context questions and question categories.
    question_delete_activity($cm);

    // Call the delete_instance function, if it returns false throw an exception.
    if (!$deleteinstancefunction($cm->instance)) {
        throw new moodle_exception('cannotdeletemoduleinstance', '', '', null,
            "Cannot delete the module $modulename (instance).");
    }

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
            'modulename' => $modulename,
            'instanceid'   => $cm->instance,
        )
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->trigger();
    rebuild_course_cache($cm->course, true);
}

/**
 * Schedule a course module for deletion in the background using an adhoc task.
 *
 * This method should not be called directly. Instead, please use course_delete_module($cmid, true), to denote async deletion.
 * The real deletion of the module is handled by the task, which calls 'course_delete_module($cmid)'.
 *
 * @param int $cmid the course module id.
 * @return bool whether the module was successfully scheduled for deletion.
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
 * @return bool true if the course contains any modules pending deletion, false otherwise.
 */
function course_modules_pending_deletion($courseid) {
    if (empty($courseid)) {
        return false;
    }
    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_cms() as $module) {
        if ($module->deletioninprogress == '1') {
            return true;
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

        if ($key = array_keys ($modarray, $modid)) {
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
    // We need to call *_refresh_events() first because some modules delete 'old' events at the end of the code which
    // will remove the completion events.
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
        if ($sections[$id] !== $position) {
            $DB->set_field('course_sections', 'section', -$position, array('id' => $id));
        }
    }
    foreach ($movedsections as $id => $position) {
        if ($sections[$id] !== $position) {
            $DB->set_field('course_sections', 'section', $position, array('id' => $id));
        }
    }

    // If we move the highlighted section itself, then just highlight the destination.
    // Adjust the higlighted section location if we move something over it either direction.
    if ($section == $course->marker) {
        course_set_marker($course->id, $destination);
    } elseif ($section > $course->marker && $course->marker >= $destination) {
        course_set_marker($course->id, $course->marker+1);
    } elseif ($section < $course->marker && $course->marker <= $destination) {
        course_set_marker($course->id, $course->marker-1);
    }

    $transaction->allow_commit();
    rebuild_course_cache($course->id, true);
    return true;
}

/**
 * This method will delete a course section and may delete all modules inside it.
 *
 * No permissions are checked here, use {@link course_can_delete_section()} to
 * check if section can actually be deleted.
 *
 * @param int|stdClass $course
 * @param int|stdClass|section_info $section
 * @param bool $forcedeleteifnotempty if set to false section will not be deleted if it has modules in it.
 * @param bool $async whether or not to try to delete the section using an adhoc task. Async also depends on a plugin hook.
 * @return bool whether section was deleted
 */
function course_delete_section($course, $section, $forcedeleteifnotempty = true, $async = false) {
    global $DB;

    // Prepare variables.
    $courseid = (is_object($course)) ? $course->id : (int)$course;
    $sectionnum = (is_object($section)) ? $section->section : (int)$section;
    $section = $DB->get_record('course_sections', array('course' => $courseid, 'section' => $sectionnum));
    if (!$section) {
        // No section exists, can't proceed.
        return false;
    }

    // Check the 'course_module_background_deletion_recommended' hook first.
    // Only use asynchronous deletion if at least one plugin returns true and if async deletion has been requested.
    // Both are checked because plugins should not be allowed to dictate the deletion behaviour, only support/decline it.
    // It's up to plugins to handle things like whether or not they are enabled.
    if ($async && $pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                if ($pluginfunction()) {
                    return course_delete_section_async($section, $forcedeleteifnotempty);
                }
            }
        }
    }

    $format = course_get_format($course);
    $sectionname = $format->get_section_name($section);

    // Delete section.
    $result = $format->delete_section($section, $forcedeleteifnotempty);

    // Trigger an event for course section deletion.
    if ($result) {
        $context = context_course::instance($courseid);
        $event = \core\event\course_section_deleted::create(
            array(
                'objectid' => $section->id,
                'courseid' => $courseid,
                'context' => $context,
                'other' => array(
                    'sectionnum' => $section->section,
                    'sectionname' => $sectionname,
                )
            )
        );
        $event->add_record_snapshot('course_sections', $section);
        $event->trigger();
    }
    return $result;
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
    global $DB, $USER;

    // Objects only, and only valid ones.
    if (!is_object($section) || empty($section->id)) {
        return false;
    }

    // Does the object currently exist in the DB for removal (check for stale objects).
    $section = $DB->get_record('course_sections', array('id' => $section->id));
    if (!$section || !$section->section) {
        // No section exists, or the section is 0. Can't proceed.
        return false;
    }

    // Check whether the section can be removed.
    if (!$forcedeleteifnotempty && (!empty($section->sequence) || !empty($section->summary))) {
        return false;
    }

    $format = course_get_format($section->course);
    $sectionname = $format->get_section_name($section);

    // Flag those modules having no existing deletion flag. Some modules may have been scheduled for deletion manually, and we don't
    // want to create additional adhoc deletion tasks for these. Moving them to section 0 will suffice.
    $affectedmods = $DB->get_records_select('course_modules', 'course = ? AND section = ? AND deletioninprogress <> ?',
                                            [$section->course, $section->id, 1], '', 'id');
    $DB->set_field('course_modules', 'deletioninprogress', '1', ['course' => $section->course, 'section' => $section->id]);

    // Move all modules to section 0.
    $modules = $DB->get_records('course_modules', ['section' => $section->id], '');
    $sectionzero = $DB->get_record('course_sections', ['course' => $section->course, 'section' => '0']);
    foreach ($modules as $mod) {
        moveto_module($mod, $sectionzero);
    }

    // Create and queue an adhoc task for the deletion of the modules.
    $removaltask = new \core_course\task\course_delete_modules();
    $data = array(
        'cms' => $affectedmods,
        'userid' => $USER->id,
        'realuserid' => \core\session\manager::get_realuser()->id
    );
    $removaltask->set_custom_data($data);
    \core\task\manager::queue_adhoc_task($removaltask);

    // Delete the now empty section, passing in only the section number, which forces the function to fetch a new object.
    // The refresh is needed because the section->sequence is now stale.
    $result = $format->delete_section($section->section, $forcedeleteifnotempty);

    // Trigger an event for course section deletion.
    if ($result) {
        $context = \context_course::instance($section->course);
        $event = \core\event\course_section_deleted::create(
            array(
                'objectid' => $section->id,
                'courseid' => $section->course,
                'context' => $context,
                'other' => array(
                    'sectionnum' => $section->section,
                    'sectionname' => $sectionname,
                )
            )
        );
        $event->add_record_snapshot('course_sections', $section);
        $event->trigger();
    }
    rebuild_course_cache($section->course, true);

    return $result;
}

/**
 * Updates the course section
 *
 * This function does not check permissions or clean values - this has to be done prior to calling it.
 *
 * @param int|stdClass $course
 * @param stdClass $section record from course_sections table - it will be updated with the new values
 * @param array|stdClass $data
 */
function course_update_section($course, $section, $data) {
    global $DB;

    $courseid = (is_object($course)) ? $course->id : (int)$course;

    // Some fields can not be updated using this method.
    $data = array_diff_key((array)$data, array('id', 'course', 'section', 'sequence'));
    $changevisibility = (array_key_exists('visible', $data) && (bool)$data['visible'] != (bool)$section->visible);
    if (array_key_exists('name', $data) && \core_text::strlen($data['name']) > 255) {
        throw new moodle_exception('maximumchars', 'moodle', '', 255);
    }

    // Update record in the DB and course format options.
    $data['id'] = $section->id;
    $data['timemodified'] = time();
    $DB->update_record('course_sections', $data);
    rebuild_course_cache($courseid, true);
    course_get_format($courseid)->update_section_format_options($data);

    // Update fields of the $section object.
    foreach ($data as $key => $value) {
        if (property_exists($section, $key)) {
            $section->$key = $value;
        }
    }

    // Trigger an event for course section update.
    $event = \core\event\course_section_updated::create(
        array(
            'objectid' => $section->id,
            'courseid' => $courseid,
            'context' => context_course::instance($courseid),
            'other' => array('sectionnum' => $section->section)
        )
    );
    $event->trigger();

    // If section visibility was changed, hide the modules in this section too.
    if ($changevisibility && !empty($section->sequence)) {
        $modules = explode(',', $section->sequence);
        foreach ($modules as $moduleid) {
            if ($cm = get_coursemodule_from_id(null, $moduleid, $courseid)) {
                if ($data['visible']) {
                    // As we unhide the section, we use the previously saved visibility stored in visibleold.
                    set_coursemodule_visible($moduleid, $cm->visibleold, $cm->visibleoncoursepage);
                } else {
                    // We hide the section, so we hide the module but we store the original state in visibleold.
                    set_coursemodule_visible($moduleid, 0, $cm->visibleoncoursepage);
                    $DB->set_field('course_modules', 'visibleold', $cm->visible, array('id' => $moduleid));
                }
                \core\event\course_module_updated::create_from_cm($cm)->trigger();
            }
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
 * @return array
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

    // Current module visibility state - return value of this function.
    $modvisible = $mod->visible;

    // Remove original module from original section.
    if (! delete_mod_from_section($mod->id, $mod->section)) {
        echo $OUTPUT->notification("Could not delete module from existing section");
    }

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

    // Add the module into the new section.
    course_add_cm_to_section($section->course, $mod->id, $section->section, $beforemod);
    return $modvisible;
}

/**
 * Returns the list of all editing actions that current user can perform on the module
 *
 * @param cm_info $mod The module to produce editing buttons for
 * @param int $indent The current indenting (default -1 means no move left-right actions)
 * @param int $sr The section to link back to (used for creating the links)
 * @return array array of action_link or pix_icon objects
 */
function course_get_cm_edit_actions(cm_info $mod, $indent = -1, $sr = null) {
    global $COURSE, $SITE, $CFG;

    static $str;

    $coursecontext = context_course::instance($mod->course);
    $modcontext = context_module::instance($mod->id);
    $courseformat = course_get_format($mod->get_course());

    $editcaps = array('moodle/course:manageactivities', 'moodle/course:activityvisibility', 'moodle/role:assign');
    $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');

    // No permission to edit anything.
    if (!has_any_capability($editcaps, $modcontext) and !has_all_capabilities($dupecaps, $coursecontext)) {
        return array();
    }

    $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

    if (!isset($str)) {
        $str = get_strings(array('delete', 'move', 'moveright', 'moveleft',
            'editsettings', 'duplicate', 'modhide', 'makeavailable', 'makeunavailable', 'modshow'), 'moodle');
        $str->assign         = get_string('assignroles', 'role');
        $str->groupsnone     = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsnone"));
        $str->groupsseparate = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsseparate"));
        $str->groupsvisible  = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsvisible"));
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
            new pix_icon('t/edit', $str->editsettings, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->editsettings,
            array('class' => 'editing_update', 'data-action' => 'update')
        );
    }

    // Indent.
    if ($hasmanageactivities && $indent >= 0) {
        $indentlimits = new stdClass();
        $indentlimits->min = 0;
        $indentlimits->max = 16;
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
            new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '1')),
            new pix_icon($rightarrow, $str->moveright, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->moveright,
            array('class' => 'editing_moveright ' . $enabledclass, 'data-action' => 'moveright',
                'data-keepopen' => true, 'data-sectionreturn' => $sr)
        );

        if ($indent <= $indentlimits->min) {
            $enabledclass = 'hidden';
        } else {
            $enabledclass = '';
        }
        $actions['moveleft'] = new action_menu_link_secondary(
            new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '-1')),
            new pix_icon($leftarrow, $str->moveleft, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->moveleft,
            array('class' => 'editing_moveleft ' . $enabledclass, 'data-action' => 'moveleft',
                'data-keepopen' => true, 'data-sectionreturn' => $sr)
        );

    }

    // Hide/Show/Available/Unavailable.
    if (has_capability('moodle/course:activityvisibility', $modcontext)) {
        $allowstealth = !empty($CFG->allowstealth) && $courseformat->allow_stealth_module_visibility($mod, $mod->get_section_info());

        $sectionvisible = $mod->get_section_info()->visible;
        // The module on the course page may be in one of the following states:
        // - Available and displayed on the course page ($displayedoncoursepage);
        // - Not available and not displayed on the course page ($unavailable);
        // - Available but not displayed on the course page ($stealth) - this can also be a visible activity in a hidden section.
        $displayedoncoursepage = $mod->visible && $mod->visibleoncoursepage && $sectionvisible;
        $unavailable = !$mod->visible;
        $stealth = $mod->visible && (!$mod->visibleoncoursepage || !$sectionvisible);
        if ($displayedoncoursepage) {
            $actions['hide'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('hide' => $mod->id)),
                new pix_icon('t/hide', $str->modhide, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->modhide,
                array('class' => 'editing_hide', 'data-action' => 'hide')
            );
        } else if (!$displayedoncoursepage && $sectionvisible) {
            // Offer to "show" only if the section is visible.
            $actions['show'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('show' => $mod->id)),
                new pix_icon('t/show', $str->modshow, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->modshow,
                array('class' => 'editing_show', 'data-action' => 'show')
            );
        }

        if ($stealth) {
            // When making the "stealth" module unavailable we perform the same action as hiding the visible module.
            $actions['hide'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('hide' => $mod->id)),
                new pix_icon('t/unblock', $str->makeunavailable, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->makeunavailable,
                array('class' => 'editing_makeunavailable', 'data-action' => 'hide', 'data-sectionreturn' => $sr)
            );
        } else if ($unavailable && (!$sectionvisible || $allowstealth) && $mod->has_view()) {
            // Allow to make visually hidden module available in gradebook and other reports by making it a "stealth" module.
            // When the section is hidden it is an equivalent of "showing" the module.
            // Activities without the link (i.e. labels) can not be made available but hidden on course page.
            $action = $sectionvisible ? 'stealth' : 'show';
            $actions[$action] = new action_menu_link_secondary(
                new moodle_url($baseurl, array($action => $mod->id)),
                new pix_icon('t/block', $str->makeavailable, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->makeavailable,
                array('class' => 'editing_makeavailable', 'data-action' => $action, 'data-sectionreturn' => $sr)
            );
        }
    }

    // Duplicate (require both target import caps to be able to duplicate and backup2 support, see modduplicate.php)
    if (has_all_capabilities($dupecaps, $coursecontext) &&
            plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2) &&
            course_allowed_module($mod->get_course(), $mod->modname)) {
        $actions['duplicate'] = new action_menu_link_secondary(
            new moodle_url($baseurl, array('duplicate' => $mod->id)),
            new pix_icon('t/copy', $str->duplicate, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->duplicate,
            array('class' => 'editing_duplicate', 'data-action' => 'duplicate', 'data-sectionreturn' => $sr)
        );
    }

    // Groupmode.
    if ($hasmanageactivities && !$mod->coursegroupmodeforce) {
        if (plugin_supports('mod', $mod->modname, FEATURE_GROUPS, false)) {
            if ($mod->effectivegroupmode == SEPARATEGROUPS) {
                $nextgroupmode = VISIBLEGROUPS;
                $grouptitle = $str->groupsseparate;
                $actionname = 'groupsseparate';
                $nextactionname = 'groupsvisible';
                $groupimage = 'i/groups';
            } else if ($mod->effectivegroupmode == VISIBLEGROUPS) {
                $nextgroupmode = NOGROUPS;
                $grouptitle = $str->groupsvisible;
                $actionname = 'groupsvisible';
                $nextactionname = 'groupsnone';
                $groupimage = 'i/groupv';
            } else {
                $nextgroupmode = SEPARATEGROUPS;
                $grouptitle = $str->groupsnone;
                $actionname = 'groupsnone';
                $nextactionname = 'groupsseparate';
                $groupimage = 'i/groupn';
            }

            $actions[$actionname] = new action_menu_link_primary(
                new moodle_url($baseurl, array('id' => $mod->id, 'groupmode' => $nextgroupmode)),
                new pix_icon($groupimage, $grouptitle, 'moodle', array('class' => 'iconsmall')),
                $grouptitle,
                array('class' => 'editing_'. $actionname, 'data-action' => $nextactionname,
                    'aria-live' => 'assertive', 'data-sectionreturn' => $sr)
            );
        } else {
            $actions['nogroupsupport'] = new action_menu_filler();
        }
    }

    // Assign.
    if (has_capability('moodle/role:assign', $modcontext)){
        $actions['assign'] = new action_menu_link_secondary(
            new moodle_url('/admin/roles/assign.php', array('contextid' => $modcontext->id)),
            new pix_icon('t/assignroles', $str->assign, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->assign,
            array('class' => 'editing_assign', 'data-action' => 'assignroles', 'data-sectionreturn' => $sr)
        );
    }

    // Delete.
    if ($hasmanageactivities) {
        $actions['delete'] = new action_menu_link_secondary(
            new moodle_url($baseurl, array('delete' => $mod->id)),
            new pix_icon('t/delete', $str->delete, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            $str->delete,
            array('class' => 'editing_delete', 'data-action' => 'delete', 'data-sectionreturn' => $sr)
        );
    }

    return $actions;
}

/**
 * Returns the move action.
 *
 * @param cm_info $mod The module to produce a move button for
 * @param int $sr The section to link back to (used for creating the links)
 * @return The markup for the move action, or an empty string if not available.
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

        return html_writer::link(
            new moodle_url($baseurl, array('copy' => $mod->id)),
            $OUTPUT->pix_icon($pixicon, $str->move, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            array('class' => 'editing_move', 'data-action' => 'move', 'data-sectionreturn' => $sr)
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
 * @return bool whether the current user is allowed to add this type of module to this course.
 */
function course_allowed_module($course, $modname) {
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
    return has_capability($capability, $coursecontext);
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
        $course->category  = $category->id;
        $course->sortorder = $category->sortorder + MAX_COURSES_IN_CATEGORY - $i++;
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
                             'fullname' => $dbcourse->fullname)
        ));
        $event->set_legacy_logdata(array($course->id, 'course', 'move', 'edit.php?id=' . $course->id, $course->id));
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
 * @see format_base::get_section_name()
 *
 * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
 * @param int|stdClass $section Section object from database or just field course_sections.section
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
 * @param string $format
 * @return stdClass
 */
function course_format_ajax_support($format) {
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
 * @param array $data the data that came from the course settings form.
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
    $accepted_types = preg_split('/\s*,\s*/', trim($CFG->courseoverviewfilesext), -1, PREG_SPLIT_NO_EMPTY);
    if (in_array('*', $accepted_types) || empty($accepted_types)) {
        $accepted_types = '*';
    } else {
        // Since config for $CFG->courseoverviewfilesext is a text box, human factor must be considered.
        // Make sure extensions are prefixed with dot unless they are valid typegroups
        foreach ($accepted_types as $i => $type) {
            if (substr($type, 0, 1) !== '.') {
                require_once($CFG->libdir. '/filelib.php');
                if (!count(file_get_typegroup('extension', $type))) {
                    // It does not start with dot and is not a valid typegroup, this is most likely extension.
                    $accepted_types[$i] = '.'. $type;
                    $corrected = true;
                }
            }
        }
        if (!empty($corrected)) {
            set_config('courseoverviewfilesext', join(',', $accepted_types));
        }
    }
    $options = array(
        'maxfiles' => $CFG->courseoverviewfileslimit,
        'maxbytes' => $CFG->maxbytes,
        'subdirs' => 0,
        'accepted_types' => $accepted_types
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
        $data->summary_format = FORMAT_HTML;
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
        'context' => context_course::instance($course->id),
        'other' => array('shortname' => $course->shortname,
            'fullname' => $course->fullname)
    ));

    $event->trigger();

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
        core_tag_tag::set_item_tags('core', 'course', $course->id, context_course::instance($course->id), $data->tags);
    }

    // Save custom fields if there are any of them in the form.
    $handler = core_course\customfield\course_handler::create();
    // Make sure to set the handler's parent context first.
    $coursecatcontext = context_coursecat::instance($category->id);
    $handler->set_parent_context($coursecatcontext);
    // Save the custom field data.
    $data->id = $course->id;
    $handler->instance_form_save($data, true);

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

    $data->timemodified = time();

    // Prevent changes on front page course.
    if ($data->id == SITEID) {
        throw new moodle_exception('invalidcourse', 'error');
    }

    $oldcourse = course_get_format($data->id)->get_course();
    $context   = context_course::instance($oldcourse->id);

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

    // Update custom fields if there are any of them in the form.
    $handler = core_course\customfield\course_handler::create();
    $handler->instance_form_save($data);

    // Update with the new data
    $DB->update_record('course', $data);
    // make sure the modinfo cache is reset
    rebuild_course_cache($data->id);

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
                         'fullname' => $course->fullname)
    ));

    $event->set_legacy_logdata(array($course->id, 'course', 'update', 'edit.php?id=' . $course->id, $course->id));
    $event->trigger();

    if ($oldcourse->format !== $course->format) {
        // Remove all options stored for the previous format
        // We assume that new course format migrated everything it needed watching trigger
        // 'course_updated' and in method format_XXX::update_course_format_options()
        $DB->delete_records('course_format_options',
                array('courseid' => $course->id, 'format' => $oldcourse->format));
    }
}

/**
 * Average number of participants
 * @return integer
 */
function average_number_of_participants() {
    global $DB, $SITE;

    //count total of enrolments for visible course (except front page)
    $sql = 'SELECT COUNT(*) FROM (
        SELECT DISTINCT ue.userid, e.courseid
        FROM {user_enrolments} ue, {enrol} e, {course} c
        WHERE ue.enrolid = e.id
            AND e.courseid <> :siteid
            AND c.id = e.courseid
            AND c.visible = 1) total';
    $params = array('siteid' => $SITE->id);
    $enrolmenttotal = $DB->count_records_sql($sql, $params);


    //count total of visible courses (minus front page)
    $coursetotal = $DB->count_records('course', array('visible' => 1));
    $coursetotal = $coursetotal - 1 ;

    //average of enrolment
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
 * This class pertains to course requests and contains methods associated with
 * create, approving, and removing course requests.
 *
 * Please note we do not allow embedded images here because there is no context
 * to store them with proper access control.
 *
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 *
 * @property-read int $id
 * @property-read string $fullname
 * @property-read string $shortname
 * @property-read string $summary
 * @property-read int $summaryformat
 * @property-read int $summarytrust
 * @property-read string $reason
 * @property-read int $requester
 */
class course_request {

    /**
     * This is the stdClass that stores the properties for the course request
     * and is externally accessed through the __get magic method
     * @var stdClass
     */
    protected $properties;

    /**
     * An array of options for the summary editor used by course request forms.
     * This is initially set by {@link summary_editor_options()}
     * @var array
     * @static
     */
    protected static $summaryeditoroptions;

    /**
     * Static function to prepare the summary editor for working with a course
     * request.
     *
     * @static
     * @param null|stdClass $data Optional, an object containing the default values
     *                       for the form, these may be modified when preparing the
     *                       editor so this should be called before creating the form
     * @return stdClass An object that can be used to set the default values for
     *                   an mforms form
     */
    public static function prepare($data=null) {
        if ($data === null) {
            $data = new stdClass;
        }
        $data = file_prepare_standard_editor($data, 'summary', self::summary_editor_options());
        return $data;
    }

    /**
     * Static function to create a new course request when passed an array of properties
     * for it.
     *
     * This function also handles saving any files that may have been used in the editor
     *
     * @static
     * @param stdClass $data
     * @return course_request The newly created course request
     */
    public static function create($data) {
        global $USER, $DB, $CFG;
        $data->requester = $USER->id;

        // Setting the default category if none set.
        if (empty($data->category) || empty($CFG->requestcategoryselection)) {
            $data->category = $CFG->defaultrequestcategory;
        }

        // Summary is a required field so copy the text over
        $data->summary       = $data->summary_editor['text'];
        $data->summaryformat = $data->summary_editor['format'];

        $data->id = $DB->insert_record('course_request', $data);

        // Create a new course_request object and return it
        $request = new course_request($data);

        // Notify the admin if required.
        if ($users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse')) {

            $a = new stdClass;
            $a->link = "$CFG->wwwroot/course/pending.php";
            $a->user = fullname($USER);
            $subject = get_string('courserequest');
            $message = get_string('courserequestnotifyemail', 'admin', $a);
            foreach ($users as $user) {
                $request->notify($user, $USER, 'courserequested', $subject, $message);
            }
        }

        return $request;
    }

    /**
     * Returns an array of options to use with a summary editor
     *
     * @uses course_request::$summaryeditoroptions
     * @return array An array of options to use with the editor
     */
    public static function summary_editor_options() {
        global $CFG;
        if (self::$summaryeditoroptions === null) {
            self::$summaryeditoroptions = array('maxfiles' => 0, 'maxbytes'=>0);
        }
        return self::$summaryeditoroptions;
    }

    /**
     * Loads the properties for this course request object. Id is required and if
     * only id is provided then we load the rest of the properties from the database
     *
     * @param stdClass|int $properties Either an object containing properties
     *                      or the course_request id to load
     */
    public function __construct($properties) {
        global $DB;
        if (empty($properties->id)) {
            if (empty($properties)) {
                throw new coding_exception('You must provide a course request id when creating a course_request object');
            }
            $id = $properties;
            $properties = new stdClass;
            $properties->id = (int)$id;
            unset($id);
        }
        if (empty($properties->requester)) {
            if (!($this->properties = $DB->get_record('course_request', array('id' => $properties->id)))) {
                print_error('unknowncourserequest');
            }
        } else {
            $this->properties = $properties;
        }
        $this->properties->collision = null;
    }

    /**
     * Returns the requested property
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->properties->$key;
    }

    /**
     * Override this to ensure empty($request->blah) calls return a reliable answer...
     *
     * This is required because we define the __get method
     *
     * @param mixed $key
     * @return bool True is it not empty, false otherwise
     */
    public function __isset($key) {
        return (!empty($this->properties->$key));
    }

    /**
     * Returns the user who requested this course
     *
     * Uses a static var to cache the results and cut down the number of db queries
     *
     * @staticvar array $requesters An array of cached users
     * @return stdClass The user who requested the course
     */
    public function get_requester() {
        global $DB;
        static $requesters= array();
        if (!array_key_exists($this->properties->requester, $requesters)) {
            $requesters[$this->properties->requester] = $DB->get_record('user', array('id'=>$this->properties->requester));
        }
        return $requesters[$this->properties->requester];
    }

    /**
     * Checks that the shortname used by the course does not conflict with any other
     * courses that exist
     *
     * @param string|null $shortnamemark The string to append to the requests shortname
     *                     should a conflict be found
     * @return bool true is there is a conflict, false otherwise
     */
    public function check_shortname_collision($shortnamemark = '[*]') {
        global $DB;

        if ($this->properties->collision !== null) {
            return $this->properties->collision;
        }

        if (empty($this->properties->shortname)) {
            debugging('Attempting to check a course request shortname before it has been set', DEBUG_DEVELOPER);
            $this->properties->collision = false;
        } else if ($DB->record_exists('course', array('shortname' => $this->properties->shortname))) {
            if (!empty($shortnamemark)) {
                $this->properties->shortname .= ' '.$shortnamemark;
            }
            $this->properties->collision = true;
        } else {
            $this->properties->collision = false;
        }
        return $this->properties->collision;
    }

    /**
     * Returns the category where this course request should be created
     *
     * Note that we don't check here that user has a capability to view
     * hidden categories if he has capabilities 'moodle/site:approvecourse' and
     * 'moodle/course:changecategory'
     *
     * @return core_course_category
     */
    public function get_category() {
        global $CFG;
        // If the category is not set, if the current user does not have the rights to change the category, or if the
        // category does not exist, we set the default category to the course to be approved.
        // The system level is used because the capability moodle/site:approvecourse is based on a system level.
        if (empty($this->properties->category) || !has_capability('moodle/course:changecategory', context_system::instance()) ||
                (!$category = core_course_category::get($this->properties->category, IGNORE_MISSING, true))) {
            $category = core_course_category::get($CFG->defaultrequestcategory, IGNORE_MISSING, true);
        }
        if (!$category) {
            $category = core_course_category::get_default();
        }
        return $category;
    }

    /**
     * This function approves the request turning it into a course
     *
     * This function converts the course request into a course, at the same time
     * transferring any files used in the summary to the new course and then removing
     * the course request and the files associated with it.
     *
     * @return int The id of the course that was created from this request
     */
    public function approve() {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $user = $DB->get_record('user', array('id' => $this->properties->requester, 'deleted'=>0), '*', MUST_EXIST);

        $courseconfig = get_config('moodlecourse');

        // Transfer appropriate settings
        $data = clone($this->properties);
        unset($data->id);
        unset($data->reason);
        unset($data->requester);

        // Set category
        $category = $this->get_category();
        $data->category = $category->id;
        // Set misc settings
        $data->requested = 1;

        // Apply course default settings
        $data->format             = $courseconfig->format;
        $data->newsitems          = $courseconfig->newsitems;
        $data->showgrades         = $courseconfig->showgrades;
        $data->showreports        = $courseconfig->showreports;
        $data->maxbytes           = $courseconfig->maxbytes;
        $data->groupmode          = $courseconfig->groupmode;
        $data->groupmodeforce     = $courseconfig->groupmodeforce;
        $data->visible            = $courseconfig->visible;
        $data->visibleold         = $data->visible;
        $data->lang               = $courseconfig->lang;
        $data->enablecompletion   = $courseconfig->enablecompletion;
        $data->numsections        = $courseconfig->numsections;
        $data->startdate          = usergetmidnight(time());
        if ($courseconfig->courseenddateenabled) {
            $data->enddate        = usergetmidnight(time()) + $courseconfig->courseduration;
        }

        list($data->fullname, $data->shortname) = restore_dbops::calculate_course_names(0, $data->fullname, $data->shortname);

        $course = create_course($data);
        $context = context_course::instance($course->id, MUST_EXIST);

        // add enrol instances
        if (!$DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'))) {
            if ($manual = enrol_get_plugin('manual')) {
                $manual->add_default_instance($course);
            }
        }

        // enrol the requester as teacher if necessary
        if (!empty($CFG->creatornewroleid) and !is_viewing($context, $user, 'moodle/role:assign') and !is_enrolled($context, $user, 'moodle/role:assign')) {
            enrol_try_internal_enrol($course->id, $user->id, $CFG->creatornewroleid);
        }

        $this->delete();

        $a = new stdClass();
        $a->name = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
        $a->url = $CFG->wwwroot.'/course/view.php?id=' . $course->id;
        $this->notify($user, $USER, 'courserequestapproved', get_string('courseapprovedsubject'), get_string('courseapprovedemail2', 'moodle', $a), $course->id);

        return $course->id;
    }

    /**
     * Reject a course request
     *
     * This function rejects a course request, emailing the requesting user the
     * provided notice and then removing the request from the database
     *
     * @param string $notice The message to display to the user
     */
    public function reject($notice) {
        global $USER, $DB;
        $user = $DB->get_record('user', array('id' => $this->properties->requester), '*', MUST_EXIST);
        $this->notify($user, $USER, 'courserequestrejected', get_string('courserejectsubject'), get_string('courserejectemail', 'moodle', $notice));
        $this->delete();
    }

    /**
     * Deletes the course request and any associated files
     */
    public function delete() {
        global $DB;
        $DB->delete_records('course_request', array('id' => $this->properties->id));
    }

    /**
     * Send a message from one user to another using events_trigger
     *
     * @param object $touser
     * @param object $fromuser
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param int|null $courseid
     */
    protected function notify($touser, $fromuser, $name='courserequested', $subject, $message, $courseid = null) {
        $eventdata = new \core\message\message();
        $eventdata->courseid          = empty($courseid) ? SITEID : $courseid;
        $eventdata->component         = 'moodle';
        $eventdata->name              = $name;
        $eventdata->userfrom          = $fromuser;
        $eventdata->userto            = $touser;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;
        message_send($eventdata);
    }
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
        $courseformatajaxsupport = course_format_ajax_support($course->format);
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
function include_course_ajax($course, $usedmodules = array(), $enabledmodules = null, $config = null) {
    global $CFG, $PAGE, $SITE;

    // Ensure that ajax should be included
    if (!course_ajax_enabled($course)) {
        return false;
    }

    if (!$config) {
        $config = new stdClass();
    }

    // The URL to use for resource changes
    if (!isset($config->resourceurl)) {
        $config->resourceurl = '/course/rest.php';
    }

    // The URL to use for section changes
    if (!isset($config->sectionurl)) {
        $config->sectionurl = '/course/rest.php';
    }

    // Any additional parameters which need to be included on page submission
    if (!isset($config->pageparams)) {
        $config->pageparams = array();
    }

    // Include course dragdrop
    if (course_format_uses_sections($course->format)) {
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
    }

    // Require various strings for the command toolbox
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
            'clicktochangeinbrackets',
            'markthistopic',
            'markedthistopic',
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

    // For confirming resource deletion we need the name of the module in question
    foreach ($usedmodules as $module => $modname) {
        $PAGE->requires->string_for_js('pluginname', $module);
    }

    // Load drag and drop upload AJAX.
    require_once($CFG->dirroot.'/course/dnduploadlib.php');
    dndupload_add_to_course($course, $enabledmodules);

    $PAGE->requires->js_call_amd('core_course/actions', 'initCoursePage', array($course->format));

    return true;
}

/**
 * Returns the sorted list of available course formats, filtered by enabled if necessary
 *
 * @param bool $enabledonly return only formats that are enabled
 * @return array array of sorted format names
 */
function get_sorted_course_formats($enabledonly = false) {
    global $CFG;
    $formats = core_component::get_plugin_list('format');

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
        if (!get_config('format_'.$formatname, 'disabled')) {
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
 * @return Object containing:
 * - fullcontent: The HTML markup for the created CM
 * - cmid: The CMID of the newly created CM
 * - redirect: Whether to trigger a redirect following this change
 */
function mod_duplicate_activity($course, $cm, $sr = null) {
    global $PAGE;

    $newcm = duplicate_module($course, $cm);

    $resp = new stdClass();
    if ($newcm) {
        $courserenderer = $PAGE->get_renderer('core', 'course');
        $completioninfo = new completion_info($course);
        $modulehtml = $courserenderer->course_section_cm($course, $completioninfo,
                $newcm, null, array());

        $resp->fullcontent = $courserenderer->course_section_cm_list_item($course, $completioninfo, $newcm, $sr);
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
 * @since Moodle 2.8
 *
 * @throws Exception
 * @throws coding_exception
 * @throws moodle_exception
 * @throws restore_controller_exception
 *
 * @return cm_info|null cminfo object if we sucessfully duplicated the mod and found the new cm.
 */
function duplicate_module($course, $cm) {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
    require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    require_once($CFG->libdir . '/filelib.php');

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
        // Add ' (copy)' to duplicates. Note we don't cleanup or validate lengths here. It comes
        // from original name that was valid, so the copy should be too.
        $newname = get_string('duplicatedmodule', 'moodle', $newcm->name);
        $DB->set_field($cm->modname, 'name', $newname, ['id' => $newcm->instance]);

        $section = $DB->get_record('course_sections', array('id' => $cm->section, 'course' => $cm->course));
        $modarray = explode(",", trim($section->sequence));
        $cmindex = array_search($cm->id, $modarray);
        if ($cmindex !== false && $cmindex < count($modarray) - 1) {
            moveto_module($newcm, $section, $modarray[$cmindex + 1]);
        }

        // Update calendar events with the duplicated module.
        // The following line is to be removed in MDL-58906.
        course_module_update_calendar_events($newcm->modname, null, $newcm);

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
 * @return \core\output\inplace_editable
 */
function core_course_inplace_editable($itemtype, $itemid, $newvalue) {
    if ($itemtype === 'activityname') {
        return \core_course\output\course_module_name::update($itemid, $newvalue);
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
 * @return \core_tag\output\tagindex
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
    global $CFG;

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
        'calendar' => false,
        'competencies' => false,
        'grades' => false,
        'notes' => false,
        'participants' => false,
        'search' => false,
        'tags' => false,
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
        $options->calendar = $isloggedin;
    } else {
        // We are in a course, so make sure we use the proper capability (course:viewparticipants).
        $options->participants = course_can_view_participants($context);
        $options->badges = !empty($CFG->enablebadges) && !empty($CFG->badges_allowcoursebadges) &&
                            has_capability('moodle/badges:viewbadges', $context);
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

    if (\core_competency\api::is_enabled()) {
        $capabilities = array('moodle/competency:coursecompetencyview', 'moodle/competency:coursecompetencymanage');
        $options->competencies = has_any_capability($capabilities, $context);
    }
    return $options;
}

/**
 * Return an object with the list of administration options in a course that are available or not for the current user.
 * This function also handles the frontpage settings.
 *
 * @param  stdClass $course  course object (for frontpage it should be a clone of $SITE)
 * @param  stdClass $context context object (course context)
 * @return stdClass          the administration options in a course and their availability status
 * @since  Moodle 3.2
 */
function course_get_user_administration_options($course, $context) {
    global $CFG;
    $isfrontpage = $course->id == SITEID;
    $completionenabled = $CFG->enablecompletion && $course->enablecompletion;
    $hascompletiontabs = count(core_completion\manager::get_available_completion_tabs($course, $context)) > 0;

    $options = new stdClass;
    $options->update = has_capability('moodle/course:update', $context);
    $options->editcompletion = $CFG->enablecompletion &&
                               $course->enablecompletion &&
                               ($options->update || $hascompletiontabs);
    $options->filters = has_capability('moodle/filter:manage', $context) &&
                        count(filter_get_available_in_context($context)) > 0;
    $options->reports = has_capability('moodle/site:viewreports', $context);
    $options->backup = has_capability('moodle/backup:backupcourse', $context);
    $options->restore = has_capability('moodle/restore:restorecourse', $context);
    $options->files = ($course->legacyfiles == 2 && has_capability('moodle/course:managefiles', $context));

    if (!$isfrontpage) {
        $options->tags = has_capability('moodle/course:tag', $context);
        $options->gradebook = has_capability('moodle/grade:manage', $context);
        $options->outcomes = !empty($CFG->enableoutcomes) && has_capability('moodle/course:update', $context);
        $options->badges = !empty($CFG->enablebadges);
        $options->import = has_capability('moodle/restore:restoretargetimport', $context);
        $options->publish = has_capability('moodle/course:publish', $context);
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

    $today = time();
    // End date past.
    if (!empty($course->enddate) && (course_classify_end_date($course) < $today)) {
        return COURSE_TIMELINE_PAST;
    }

    if ($completioninfo == null) {
        $completioninfo = new completion_info($course);
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
    string $sort = null,
    string $fields = null,
    int $dbquerylimit = COURSE_DB_QUERY_LIMIT,
    array $includecourses = [],
    array $hiddencourses = []
) : Generator {

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
) : array {

    if (!in_array($classification,
            [COURSE_TIMELINE_ALL, COURSE_TIMELINE_PAST, COURSE_TIMELINE_INPROGRESS,
                COURSE_TIMELINE_FUTURE, COURSE_TIMELINE_HIDDEN])) {
        $message = 'Classification must be one of COURSE_TIMELINE_ALL, COURSE_TIMELINE_PAST, '
            . 'COURSE_TIMELINE_INPROGRESS or COURSE_TIMELINE_FUTURE';
        throw new moodle_exception($message);
    }

    $filteredcourses = [];
    $numberofcoursesprocessed = 0;
    $filtermatches = 0;

    foreach ($courses as $course) {
        $numberofcoursesprocessed++;
        $pref = get_user_preferences('block_myoverview_hidden_course_' . $course->id, 0);

        // Added as of MDL-63457 toggle viewability for each user.
        if (($classification == COURSE_TIMELINE_HIDDEN && $pref) ||
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
) : array {

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
        $updates->comments = (object) array('updated' => false);
        require_once($CFG->dirroot . '/comment/lib.php');
        require_once($CFG->dirroot . '/comment/locallib.php');
        $manager = new comment_manager();
        $comments = $manager->get_component_comments_since($course, $context, $component, $from, $cm);
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
function can_download_from_backup_filearea($filearea, \context $context, stdClass $user = null) {
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
function course_get_recent_courses(int $userid = null, int $limit = 0, int $offset = 0, string $sort = null) {

    global $CFG, $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $basefields = array('id', 'idnumber', 'summary', 'summaryformat', 'startdate', 'enddate', 'category',
            'shortname', 'fullname', 'timeaccess', 'component');

    $sort = trim($sort);
    if (empty($sort)) {
        $sort = 'timeaccess DESC';
    } else {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            $sorts[] = trim($rawsort);
        }
        $sort = implode(',', $sorts);
    }

    $orderby = "ORDER BY $sort";

    $ctxfields = context_helper::get_preload_record_columns_sql('ctx');

    $coursefields = 'c.' .join(',', $basefields);

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
             WHERE ul.userid = :userid
               AND c.visible = :visible
               AND EXISTS (SELECT e.id
                             FROM {enrol} e
                        LEFT JOIN {user_enrolments} ue ON ue.enrolid = e.id
                            WHERE e.courseid = c.id
                              AND e.status = :statusenrol
                              AND ((ue.status = :status
                                    AND ue.userid = ul.userid
                                    AND ue.timestart < :now1
                                    AND (ue.timeend = 0 OR ue.timeend > :now2)
                                   )
                                   OR e.enrol = :guestenrol
                                  )
                          )
            $orderby";

    $now = round(time(), -2); // Improves db caching.
    $params = ['userid' => $userid, 'contextlevel' => CONTEXT_COURSE, 'visible' => 1, 'status' => ENROL_USER_ACTIVE,
               'statusenrol' => ENROL_INSTANCE_ENABLED, 'guestenrol' => 'guest', 'now1' => $now, 'now2' => $now] + $favparams;

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
