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
 * Local functions for this module
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @version    moodle 2.x
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/grade/grade_scale.php');

define('TASK_SINGLE', 0);
define('TASK_REPLAY', 1);
define('TASK_SHIFT', 2);
define('TASK_SHIFT_TO', 3);

define('TR_GRADE_MODE_BINARY', 0);
define('TR_GRADE_MODE_DISCRETE', 1);
define('TR_GRADE_MODE_CONTINUOUS', 2);

define('TR_GRADE_SOURCE_COURSE', 0);
define('TR_GRADE_SOURCE_ACTIVITIES', 1);
define('TR_GRADE_SOURCE_COURSE_EXT', 2);

define('TR_TIMEGRADE_DISABLED', 0);
define('TR_TIMEGRADE_GRADE', -1);
define('TR_TIMEGRADE_BONUS', -2);
define('TR_LINEAGGREGATORS', -3);
define('TR_XLSGRADE_FORMULA1', -10);
define('TR_XLSGRADE_FORMULA2', -9);
define('TR_XLSGRADE_FORMULA3', -8);

/**
 * decodes a course structure giving an ordered and
 * recursive image of the course.
 * The course structure will recognize topic, weekly and flexipage
 * course format, keeping an accurate image of the course ordering.
 *
 * @param int $courseid
 * @param reference $itemcount a recursive propagating counter in case of flexipage
 * or recursive content.
 * @return a complex structure representing the course organisation
 */
function report_trainingsessions_get_course_structure($courseid, &$itemcount) {
    global $CFG, $DB;

    $structure = array();
    $hierarchicalsectionformats = array('flexsections', 'summary');
    // var_dump($hierarchicalsectionformats);

    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        print_error('errorbadcoursestructure', 'report_trainingsessions', $courseid);
    }

    if ($course->format == 'page') {
        include_once($CFG->dirroot.'/course/format/page/lib.php');
        include_once($CFG->dirroot.'/course/format/page/classes/page.class.php');

        // Get first top level page (contains course structure).
        $nestedpages = course_page::get_all_pages($courseid, 'nested');
        if (empty($nestedpages)) {
            print_error('errorcoursestructurefirstpage', 'report_trainingsessions');
        }

        // Adapt structure from page format internal nested.
        foreach ($nestedpages as $key => $page) {
            if (!($page->display > FORMAT_PAGE_DISP_HIDDEN)) {
                continue;
            }

            $pageelement = new StdClass;
            $pageelement->type = 'page';
            $pageelement->plugintype = 'page';
            $pageelement->name = format_string($page->nametwo);

            $pageelement->subs = page_get_structure_from_page($page, $itemcount);
            $structure[] = $pageelement;
        }
    } else if (in_array($course->format, $hierarchicalsectionformats)) {

        trainingsessions_fill_structure_from_flexiblesections($structure, null, $itemcount);
    } else {
        // Browse through course_sections and collect course items.
        $structure = array();
 
        $params = array('courseid' => $courseid, 'format' => $course->format, 'name' => 'numsections');
        $maxsections = $DB->get_field('course_format_options', 'value', $params);

        if ($sections = $DB->get_records('course_sections', array('course' => $courseid), 'section ASC')) {
            trainingsessions_fill_structure_from_sections($structure, $sections, $itemcount);
        }
    }

    return $structure;
}

/**
 * Decodes the structure of a flexsection page format and provides an understandable
 * course structure.
 *
 * @param arrayref &$structure a structure array to fill.
 * @param int $parentid the parent section id, for recursive calls.
 * @param int $itemcount the recursive item count collector
 * @return boolean
 */
function trainingsessions_fill_structure_from_flexiblesections(&$structure, $parentid = null, &$itemcount) {
    global $DB, $COURSE;
    static $parents;

    $params = array($COURSE->format, $COURSE->id);

    if (is_null($parentid)) {
        $sql = "
            SELECT
                cs.*
            FROM
                {course_sections} cs
            LEFT JOIN
                {course_format_options} cfo
            ON
                cs.course = cfo.courseid AND
                cs.id = cfo.sectionid AND
                cfo.name = 'parent' AND
                cfo.format = ?
            WHERE
                (cfo.id IS NULL OR cfo.value = 0) AND
                cs.course = ?
        ";
    } else {
        $sql = "
            SELECT
                cs.*,
                cfo.value as parent
            FROM
                {course_sections} cs,
                {course_format_options} cfo
            WHERE
                cs.course = cfo.courseid AND
                cfo.name = 'parent' AND
                cs.id = cfo.sectionid AND
                cfo.format = ? AND
                cs.course = ? AND
                cfo.value = ?
        ";
        $params[] = $parentid;
    }

    $sections = $DB->get_records_sql($sql, $params);

    if ($sections) {
        foreach ($sections as $s) {
            $element = new StdClass;
            $element->type = 'section';
            $element->plugintype = 'section';
            $element->instance = $s;
            $element->instance->visible = $s->visible;
            $element->id = $s->id;
            // Shall we try to capture any title in there ?
            if (empty($s->name)) {
                if (preg_match('/<h[1-7][^>]*?>(.*?)<\\/h[1-7][^>]*?>/i', $s->summary, $matches)) {
                    $element->name = $matches[1];
                } else {
                    if ($s->section) {
                        $element->name = get_string('section').' '.$s->section;
                    } else {
                        $element->name = get_string('headsection', 'report_trainingsessions');
                    }
                }
            } else {
                $element->name = format_string($s->name);
            }

            if (!empty($s->sequence)) {
                $element->subs = array();
                $sequence = explode(",", $s->sequence);
                foreach ($sequence as $seq) {
                    if (!$cm = $DB->get_record('course_modules', array('id' => $seq))) {
                        continue;
                    }
                    $module = $DB->get_record('modules', array('id' => $cm->module));
                    if (preg_match('/label$/', $module->name)) {
                        // Discard all labels.
                        continue;
                    }
                    $moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance));
                    $sub = new StdClass;
                    $sub->id = $cm->id;
                    $sub->plugintype = 'mod';
                    $sub->type = $module->name;
                    $sub->instance = $cm;
                    $sub->name = $moduleinstance->name;
                    $sub->visible = $cm->visible;
                    $element->subs[] = $sub;
                    $itemcount++;
                }
            }

            if ($s->section > 0) {
                // Note that section 0 CANNOT have subsections. It would create a reference loop.
                $subsections = array();
                trainingsessions_fill_structure_from_flexiblesections($subsections, $s->section, $itemcount);
                if (!empty($subsections)) {
                    foreach ($subsections as $s) {
                        $element->subs[] = $s;
                    }
                }
            }

            $structure[] = $element;
        }
        return true;
    }
    // No subsections.
    return false;
}

/**
 * This addresses standard section based courses and computes an understandable structure
 * for further aggregation.
 *
 * @param arrayref &$structure a structure array to fill.
 * @param int $sections
 * @param int &$itemcount the iterator item coutner.
 */
function trainingsessions_fill_structure_from_sections(&$structure, $sections, &$itemcount) {
    global $DB, $COURSE;
  $zz = 1;

    $sectioncount = 0;
    foreach ($sections as $section) {
        // echo 'Lalalalalalalalalala'.$zz;
        $element = new StdClass;
        $element->type = 'section';
        $element->plugintype = 'section';
        $element->instance = $section;
        $element->instance->visible = $section->visible;
        $element->id = $section->id;
        
        // Shall we try to capture any title in there ?
        if (empty($section->name)) {
            if (preg_match('/<h[1-7][^>]*?>(.*?)<\\/h[1-7][^>]*?>/i', $section->summary, $matches)) {
                $element->name = $matches[1];
            } else {
                if ($section->section) {
                    $element->name = get_string('section').' '.$section->section;
                } else {
                    $element->name = get_string('headsection', 'report_trainingsessions');
                }
            }
        } else {
            $element->name = format_string($section->name);
        }


        if (!empty($section->sequence)) {
            $element->subs = array();
            $sequence = explode(",", $section->sequence);
            foreach ($sequence as $seq) {
                if (!$cm = $DB->get_record('course_modules', array('id' => $seq))) {
                    continue;
                }
                $module = $DB->get_record('modules', array('id' => $cm->module));
                if (preg_match('/label$/', $module->name)) {
                    // Discard all labels.
                    continue;
                }
                if ($moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance))) {
                    $sub = new StdClass;
                    $sub->id = $cm->id;
                    $sub->plugintype = 'mod';
                    $sub->type = $module->name;
                    $sub->instance = $cm;
                    $sub->name = $moduleinstance->name;
                    $sub->visible = $cm->visible;
                    $element->subs[] = $sub;
                    $itemcount++;
                }
            }
        }



        $structure[] = $element;
        // $params = array('courseid' => $COURSE->id, 'format' => $COURSE->format, 'name' => 'numsections');
        // $maxsections = $DB->get_field('course_format_options', 'value', $params);
        // if ($sectioncount == $maxsections) {
        //     // Do not go further, even if more sections are in database.
        //     break;
        // }
        $sectioncount++;
        $zz++;
    }
}

/**
 * Get the complete inner structure for one page of a page menu.
 * Recursive function.
 *
 * @param record $page
 * @param int &$itemcount a recursive propagating counter in case of flexipage
 * or recursive content.
 */
function page_get_structure_from_page($page, &$itemcount) {
    global $visitedpages, $DB;

    if (!isset($visitedpages)) {
        $visitedpages = array();
    }

    if (in_array($page->id, $visitedpages)) {
        return;
    }
    $visitedpages[] = $page->id;

    $structure = array();

    // Get page items from first page. They are located in the center column.
    $select = " pageid = ? AND (position = 'c' OR position = 'r') ";
    $pageitems = $DB->get_records_select('format_page_items', $select, array($page->id), 'position, sortorder');

    // Analyses course content component stack.
    if ($pageitems) {
        foreach ($pageitems as $pi) {

            if (!$pi->cmid) {

                // Is a block.
                $b = $DB->get_record('block_instances', array('id' => $pi->blockinstance));
                if (!$b) {
                    continue;
                }
                $bp = $DB->get_record('block_positions', array('blockinstanceid' => $pi->blockinstance));
                $blockinstance = block_instance($b->blockname, $b);

                $element = new StdClass;
                $element->type = $b->blockname;
                $element->plugintype = 'block';
                $element->instance = $b;
                if ($bp) {
                    // A block can be hidden by its page_module insertion.
                    $element->instance->visible = $bp->visible * $pi->visible;
                } else {
                    $element->instance->visible = $pi->visible;
                }
                $element->name = (!empty($blockinstance->config->title)) ? $blockinstance->config->title : '';
                $element->id = $b->id;

                // Tries to catch modules, pages or resources in content.

                $source = @$blockinstance->config->text;

                // If there is no subcontent, do not consider this bloc in reports.
                if ($element->subs = page_get_structure_in_content($source, $itemcount)) {
                    $structure[] = $element;
                }
            } else {
                // Is a module.
                $cm = $DB->get_record('course_modules', array('id' => $pi->cmid));
                $module = $DB->get_record('modules', array('id' => $cm->module));

                switch ($module->name) {
                    case 'customlabel':
                    case 'label':
                    case 'pagemenu':
                        break;

                    default:
                        $element = new StdClass;
                        $element->type = $module->name;
                        $element->plugin = 'mod';
                        $moduleinstance = $DB->get_record($module->name, array('id' => $cm->instance));
                        $element->name = $moduleinstance->name;
                        $element->instance = $cm;

                        // A block can be hidden by its page_module insertion.
                        $element->instance->visible = $element->instance->visible * $pi->visible;
                        $element->id = $cm->id;
                        $structure[] = $element;
                        $itemcount++;
                }
            }
        }
    }

    if (!empty($page->childs)) {
        foreach ($page->childs as $key => $child) {
            if (!($child->display > FORMAT_PAGE_DISP_HIDDEN)) {
                continue;
            }

            $pageelement = new StdClass;
            $pageelement->type = 'page';
            $pageelement->name = format_string($child->nametwo);

            $pageelement->subs = page_get_structure_from_page($child, $itemcount);
            $structure[] = $pageelement;
        }
    }

    return $structure;
}

/**
 * get substructures hidden in content. this applies to content in HTML blocks that
 * may be inserted in page based formats. Not applicable to topic and weekly format.
 *
 * @param string $source the textual source code of the content
 * @param reference $itemcount a recursive propagating counter in case of flexipage
 * or recursive content.
 */
function page_get_structure_in_content($source, &$itemcount) {
    global $visitedpages, $DB;

    $structure = array();

    // Get all links.
    $pattern = '/href=\\"(.*)\\"/';
    preg_match_all($pattern, $source, $matches);
    if (isset($matches[1])) {
        foreach ($matches[1] as $href) {
            // Jump to another page.
            if (preg_match('/course\\/view.php\\?id=(\\d+)&page=(\\d+)/', $href, $matches)) {
                if (in_array($matches[2], $visitedpages)) {
                    continue;
                }
                $page = $DB->get_record('format_page', array('id' => $matches[2]));
                $element = new StdClass;
                $element->type = 'pagemenu';
                $element->plugin = 'mod';
                $element->subs = page_get_structure_from_page($page, $itemcount);
                $structure[] = $element;
                $visitedpages[] = $matches[2];
            }
            // Points a module.
            if (preg_match('/mod\\/([a-z_]+)\\/.*\\?id=(\\d+)/', $href, $matches)) {
                $element = new StdClass;
                $element->type = $matches[1];
                $element->plugin = 'mod';
                $module = $DB->get_record('modules', array('name' => $element->type));
                $cm = $DB->get_record('course_modules', array('id' => $matches[2]));
                $moduleinstance = $DB->get_record($element->type, array('id' => $cm->instance));
                $element->name = $moduleinstance->name;
                $element->instance = &$cm;
                $element->id = $cm->id;
                $structure[] = $element;
                $itemcount++;
            }
        }
    }

    return $structure;
}

/**
 * Special time formating
 *
 * - html for html text output
 * - xlsd stands for xls duration
 * - xls for excel date
 * @param type $timevalue
 * @param type $mode
 * @return string
 */
function report_trainingsessions_format_time($timevalue, $mode = 'html') {

    if ($timevalue) {

        if ($mode == 'htmld') {
            // Print without seconds.
            $secs = $timevalue % 60;
            $mins = floor($timevalue / 60);
            $hours = floor($mins / 60);
            $mins = $mins % 60;

            if ($hours > 0) {
                return "{$hours}h {$mins}min";
            }
            if ($mins > 0) {
                return "{$mins}min";
            }
            return "{$secs}s";

        } else if ($mode == 'htmlds') {

            // Print with seconds.
            $secs = $timevalue % 60;
            $mins = floor($timevalue / 60);
            $hours = floor($mins / 60);
            $mins = $mins % 60;

            if ($hours > 0) {
                return "{$hours}h {$mins}min {$secs}s";
            }
            if ($mins > 0) {
                return "{$mins}min {$secs}s";
            }
            return "{$secs}s";

        } else if ($mode == 'html') {

            return strftime('%Y-%m-%d %H:%I (%a)', $timevalue);

        } else if ($mode == 'xlsd') {

            // For excel time format we need have a fractional day value.
            return $timevalue / DAYSECS;

        } else {

            return strftime('%Y-%m-%d %H:%M:%S', $timevalue);
        }

    } else {
        if ($mode == 'html') {
            return get_string('unvisited', 'report_trainingsessions');
        }
        if ($mode == 'htmld') {
            return '0min';
        }
        if ($mode == 'htmlds') {
            return '0s';
        }
        return '';
    }
}

/**
 * Local query to get course users.
 *  TODO check if yet usefull before delete
 *
 * @param int $courseid
 */
function report_trainingsessions_get_course_users($courseid) {
    global $DB;

    $sql = "
        SELECT
            DISTINCT u.id, ".get_all_user_name_fields(true, 'u')."
        FROM
            {user} u
        JOIN
            {user_enrolments} ue
        ON
            ue.userid = u.id
        JOIN
            {enrol} e
           ON
               e.id = ue.enrolid
        ORDER BY
            u.firstname ASC,
            u.lastname ASC
    ";

    $users = $DB->get_records_sql($sql, null);

    return $users;
}

/**
 * Get instances of modules selected for grade output in reports.
 *
 * @param int $courseid
 * @return array of id/cmid pairs
 */
function report_trainingsessions_get_graded_modules($courseid) {
    global $DB;

    $select = "courseid = ? AND moduleid != 0";
    return $DB->get_records_select_menu('report_trainingsessions', $select, array($courseid), 'sortorder', 'id, moduleid');
}

/**
 * Get all graded modules into the course excluing those already linked to report and
 * module types that are not gradable.
 *
 * @param int $courseid
 * @return array of linkable cmid/cmname pairs for a select
 */
function report_trainingsessions_get_linkable_modules($courseid) {
    $modinfo = get_fast_modinfo($courseid);

    $cms = $modinfo->get_cms();
    $linkables = array(0 => get_string('disabled', 'report_trainingsessions'));
    foreach ($cms as $cminfo) {
        $func = $cminfo->modname.'_supports';
        if ($func(FEATURE_GRADE_HAS_GRADE)) {
            $linkables[$cminfo->id] = '['.$cminfo->modname.'] '.$cminfo->name;
        }
    }
    return $linkables;
}

/**
 * Add extra column headers from grade settings and feeds given arrays.
 *
 * @param arrayref &$columns a reference to the array of column headings.
 * @param arrayref &$titles a reference to the array of column titles (printable column names).
 * @param arrayref &$formats a reference to the array of data formats.
 * @return void
 */
function report_trainingsessions_add_graded_columns(&$columns, &$titles, &$formats = null) {
    global $DB, $COURSE;

    $coursemodinfo = get_fast_modinfo($COURSE->id);

    if (is_null($columns)) {
        $columns = array();
    }

    if (is_null($titles)) {
        $titles = array();
    }

    if (is_null($formats)) {
        $formats = array();
    }

    $select = " courseid = ? AND moduleid > 0 ";
    $params = array($COURSE->id);
    if ($graderecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'sortorder')) {
        foreach ($graderecs as $rec) {
            // Push in array.
            $cminfo = $coursemodinfo->get_cm($rec->moduleid);
            $fulllabel = ($cminfo->idnumber) ? $cminfo->idnumber : $cminfo->modname.' '.$cminfo->instance;
            $modlabel = (empty($rec->label)) ? $fulllabel : $rec->label;
            array_push($columns, $cminfo->modname.$cminfo->instance);
            array_push($titles, $modlabel);
            if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
                $formats[] = $xlsgradeformat;
            } else {
                $formats[] = 'n.2';
            }
        }
    }

    // Add special grades.
    $select = " courseid = ? AND moduleid < 0 ";
    $params = array($COURSE->id);
    if ($graderecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'sortorder')) {

        foreach ($graderecs as $rec) {
            if ($rec->moduleid == TR_TIMEGRADE_GRADE) {
                $ranges = (array) json_decode($rec->ranges);
                // We are requesting time grade.
                $columns[] = 'timegrade';
                $titles[] = get_string('output:timegrade', 'report_trainingsessions');
                if ($ranges['timemode'] < TR_GRADE_MODE_CONTINUOUS) {
                    // Discrete and binary output mode use scale labels as output texts.
                    if (get_config('report_trainingsessions', 'discreteforcenumber')) {
                        if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
                            $formats[] = $xlsgradeformat;
                        } else {
                            $formats[] = 'n.2';
                        }
                    } else {
                        $formats[] = 'a';
                    }
                } else {
                    if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
                        $formats[] = $xlsgradeformat;
                    } else {
                        $formats[] = 'n.2';
                    }
                }
            } else if ($rec->moduleid == TR_TIMEGRADE_BONUS) {
                $columns[] = 'rawcoursegrade';
                $titles[] = get_string('output:rawcoursegrade', 'report_trainingsessions');
                if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
                    $formats[] = $xlsgradeformat;
                } else {
                    $formats[] = 'n.2';
                }

                $columns[] = 'timebonus';
                $titles[] = get_string('output:timebonus', 'report_trainingsessions');
                if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
                    $formats[] = $xlsgradeformat;
                } else {
                    $formats[] = 'n.2';
                }
            }
        }
    }

    // Add course grade if required.
    $params = array('courseid' => $COURSE->id, 'moduleid' => 0);
    if ($graderec = $DB->get_record('report_trainingsessions', $params)) {
        $label = get_string('output:finalcoursegrade', 'report_trainingsessions');
        $courselabel = (empty($graderec->label)) ? $label : $graderec->label;
        $titles[] = $courselabel;
        $columns[] = 'finalcoursegrade';
        if ($xlsgradeformat = get_config('report_trainingsessions', 'gradexlsformat')) {
            $formats[] = $xlsgradeformat;
        } else {
            $formats[] = 'n.2';
        }
    }
}

/**
 * Fetch scores and aggregate them to results.
 *
 * @param arrayref &$columns a reference to the array of report values. Grades will be appended here.
 * @param int $userid the user
 * @param arrayref &$aggregate a reference to the array of time aggregate.
 * @return void
 */
function report_trainingsessions_add_graded_data(&$columns, $userid, &$aggregate) {
    global $DB, $COURSE;

    if (is_null($columns)) {
        $columns = array();
    }

    $select = " courseid = ? AND moduleid > 0 ";
    $params = array($COURSE->id);
    if ($graderecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'sortorder')) {
        foreach ($graderecs as $rec) {
            $modulegrade = report_trainingsessions_get_module_grade($rec->moduleid, $userid);
            // Push in array.
            if ($modulegrade) {
                array_push($columns, sprintf('%.2f', $modulegrade));
            } else {
                array_push($columns, '');
            }
        }
    }

    // Add special grades.
    $bonus = 0;
    $select = " courseid = ? AND moduleid < 0 ";
    $params = array($COURSE->id);
    if ($graderecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'sortorder')) {
        foreach ($graderecs as $rec) {
            if ($rec->moduleid == TR_TIMEGRADE_GRADE) {
                $timegrade = report_trainingsessions_compute_timegrade($rec, $aggregate);
                array_push($columns, $timegrade);
            } else if ($rec->moduleid == TR_TIMEGRADE_BONUS) {
                // First add raw course grade.
                $coursegrade = report_trainingsessions_get_course_grade($rec->courseid, $userid);
                array_push($columns, sprintf('%.2f', $coursegrade->grade));

                // Add bonus columns.
                $bonus = 0 + report_trainingsessions_compute_timegrade($rec, $aggregate);
                array_push($columns, $bonus);
            }
        }
    }

    // Add course grade if required.
    $params = array('courseid' => $COURSE->id, 'moduleid' => 0);
    if ($graderec = $DB->get_record('report_trainingsessions', $params)) {
        // Retain the coursegrade for adding at the full end of array.
        $grade = 0;
        if ($coursegrade = report_trainingsessions_get_course_grade($graderec->courseid, $userid)) {
            $grade = min($coursegrade->maxgrade, $coursegrade->grade + $bonus);
        }
        if ($grade) {
            array_push($columns, sprintf('%.2f', $grade));
        } else {
            array_push($columns, '');
        }
    }
}

/**
 * Add extra column headers from grade settings and feeds given arrays.
 *
 * @param arrayref &$columns a reference to the array of column headings.
 * @param arrayref &$titles a reference to the array of column titles (printable column names).
 * @param arrayref &$formats a reference to the array of data formats.
 * @return void
 */
function report_trainingsessions_add_calculated_columns(&$columns, &$titles, &$formats = null) {
    global $DB, $COURSE;

    $coursemodinfo = get_fast_modinfo($COURSE->id);

    if (is_null($columns)) {
        $columns = array();
    }

    if (is_null($titles)) {
        $titles = array();
    }

    if (is_null($formats)) {
        $formats = array();
    }

    $select = " courseid = ? AND moduleid <= -8 ";
    $params = array($COURSE->id);
    if ($formulasrecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'sortorder')) {
        foreach ($formulasrecs as $rec) {
            // Push in array.
            array_push($columns, $rec->label);
            array_push($titles, $rec->label);
            array_push($formats, 'f');
        }
    }
}

/**
 * Add extra column headers from grade settings and feeds given arrays.
 *
 * @param arrayref &$columns a reference to the array of column headings.
 * @param arrayref &$formats a reference to the array of data formats.
 * @return void
 */
function report_trainingsessions_add_calculated_data(&$columns) {
    global $DB, $COURSE;

    if (is_null($columns)) {
        $columns = array();
    }

    $select = " courseid = ? AND moduleid <= -8 ";
    $params = array($COURSE->id);
    if ($formulasrecs = $DB->get_records_select('report_trainingsessions', $select, $params, 'moduleid ASC')) {
        $formatadds = array();
        foreach ($formulasrecs as $rec) {
            // Push in array the formula text Note it stored in the "ranges" columns.
            array_push($columns, $rec->ranges);
        }
    }
}

/**
 * Computes additional grades depending on time spent
 *
 * @param objectref &$graderec a grading item description
 * @param arrayref &$aggregate a full filled time aggregation result.
 * @return a grade value text formatted
 */
function report_trainingsessions_compute_timegrade(&$graderec, &$aggregate) {

    $config = get_config('report_trainingsessions');

    $ranges = (array) json_decode($graderec->ranges);

    if (empty($ranges['ranges'])) {
        return sprintf('%.2f', 0);
    }

    switch (@$ranges['timesource']) {
        case TR_GRADE_SOURCE_COURSE:
            $coursetime = 0 + @$aggregate['coursetotal'][$graderec->courseid]->elapsed;
            break;

        case TR_GRADE_SOURCE_ACTIVITIES:
            $coursetime = 0 + @$aggregate['activities'][$graderec->courseid]->elapsed;
            break;

        case TR_GRADE_SOURCE_COURSE_EXT:
            $c = @$aggregate['coursetotal'][$graderec->courseid]->elapsed;
            $o = @$aggregate['coursetotal'][0]->elapsed;
            $s = @$aggregate['coursetotal'][SITEID]->elapsed;
            $coursetime = 0 + $c + $o + $s;
            break;
    }

    // Determine base grade.
    if ($graderec->grade > 0) {
        // Using direct grading.
        $basegrade = $graderec->grade;
    } else if ($graderec->grade < 0) {
        // Using a moodle scale.
        // @TODO : better deal with scale if multiple items scale. Grade submitted should be scaled to the max item number.
        $scale = grade_scale::fetch(array('id' => -$graderec->grade));
    } else {
        return  sprintf('%.2f', 0);
    }

    switch ($graderec->moduleid) {
        case TR_TIMEGRADE_BONUS:
            $mode = $ranges['bonusmode'];
            break;

        case TR_TIMEGRADE_GRADE:
        default:
            $mode = $ranges['timemode'];
            break;
    }

    switch ($mode) {
        case TR_GRADE_MODE_BINARY:
            $timethreshold = array_shift($ranges['ranges']);
            if ($graderec->grade > 0) {
                if ($coursetime > $timethreshold * MINSECS) {
                    $fraction = 1;
                } else {
                    $fraction = 0;
                }
            } else if ($graderec->grade < 0) {
                if ($coursetime >= $timethreshold * MINSECS) {
                    // Points the second item precisely
                    if (!empty($config->discreteforcenumber)) {
                        return 1;
                    }
                    return $scale->get_nearest_item(2);
                } else {
                    if (!empty($config->discreteforcenumber)) {
                        return 0;
                    }
                    return $scale->get_nearest_item(0);
                }
            }
            break;

        case TR_GRADE_MODE_DISCRETE:
            // Search matching range (last lower).
            $i = 0;

            while (isset($ranges['ranges'][$i]) && ($coursetime > ($ranges['ranges'][$i] * MINSECS))) {
                $i++;
            }

            // Compute grade using points or scales.
            if ($graderec->grade > 0) {

                $fraction = $i / count($ranges['ranges']);
                $basegrade = $graderec->grade;
            } else if ($graderec->grade < 0) {
                return $scale->get_nearest_item($i + 1);
            }
            break;

        case TR_GRADE_MODE_CONTINUOUS:
            if ($graderec->grade > 0) {
                $timethreshold = array_shift($ranges['ranges']) * MINSECS;
                $fraction = $coursetime / $timethreshold;
                $fraction = min($fraction, 1); // Ceil to 1.
                $basegrade = $graderec->grade;
            } else if ($graderec->grade < 0) {
                // Not supported at this time.
                assert(false);
            }
            break;
    }

    return sprintf('%.2f', $fraction * $basegrade);
}

/**
 * Gets the final course grade in gradebook.
 *
 * @param int $courseid
 * @param int $userid
 * @return int the grade, or empty value
 */
function report_trainingsessions_get_course_grade($courseid, $userid) {
    global $DB;

    $sql = "
        SELECT
            g.finalgrade as grade,
            g.rawgrademax as maxgrade
        FROM
            {grade_items} gi,
            {grade_grades} g
        WHERE
            g.userid = ? AND
            gi.itemtype = 'course' AND
            gi.courseid = ? AND
            g.itemid = gi.id
    ";
    if (!$result = $DB->get_record_sql($sql, array($userid, $courseid))) {
        $result = new StdClass();
        $result->grade = '';
        $result->maxgrade = '';
    }

    return $result;
}

/**
 * Gets a final grade for a specific course module if exists
 *
 * @param int $moduleid the course module ID
 * @param int $userid
 * @return the grade or empty value.
 */
function report_trainingsessions_get_module_grade($moduleid, $userid) {
    global $DB, $COURSE;

    $modinfo = get_fast_modinfo($COURSE->id);
    $cm = $modinfo->get_cm($moduleid);

    $sql = "
        SELECT
            g.finalgrade as grade
        FROM
            {grade_items} gi,
            {grade_grades} g
        WHERE
            g.userid = ? AND
            gi.itemtype = 'mod' AND
            gi.itemmodule = ? AND
            gi.iteminstance = ? AND
            g.itemid = gi.id
    ";
    $result = $DB->get_record_sql($sql, array($userid, $cm->modname, $cm->instance));

    if ($result) {
        return $result->grade;
    }
    return '';
}

/**
 * Given a prefed tzarget list of users from a previous selection, discard users
 * that should not appear in reports.
 *
 * @param arrayref &$targetusers an array of selected users to filter out.
 * @param object $course the course where results are compiled for.
 * @return void
 */
function report_trainingsessions_filter_unwanted_users(&$targetusers, $course) {
    global $DB;

    $config = get_config('report_trainingsessions');

    $context = context_course::instance($course->id);

    foreach ($targetusers as $uid => $unused) {
        if (!empty($config->disablesuspendedstudents)) {
            $suspended = $DB->get_field('user', 'suspended', array('id' => $uid));
            if ($suspended) {
                unset($targetusers[$uid]);
            }
        }

        if (!has_capability('report/trainingsessions:iscompiled', $context, $uid, false)) {
            unset($targetusers[$uid]);
        }
    }
}

/**
 * A wrapper function to the auth_ticket component. Gets a valid ticket to authentify an
 * internal CURL call to a batch or a task.
 *
 * @global type $CFG
 * @global type $USER
 * @return type
 */
function report_trainingsessions_back_office_get_ticket() {
    global $CFG, $USER;

    if (file_exists($CFG->dirroot.'/auth/ticket/lib.php')) {
        include_once($CFG->dirroot.'/auth/ticket/lib.php');
        return ticket_generate($USER, 'trainingsessions_generator', me(), 'des');
    }
}

/**
 * Controls access to script with valid interactive session OR
 * non interactive token when batch is in progress.
 *
 * @param object $course
 */
function report_trainingsessions_back_office_access($course = null, $userid = 0) {
    global $CFG, $USER;

    $securitytoken = optional_param('ticket', '', PARAM_RAW);
    if (!empty($securitytoken)) {
        if (file_exists($CFG->dirroot.'/auth/ticket/lib.php')) {
            include_once($CFG->dirroot.'/auth/ticket/lib.php');
            if (!ticket_decode($securitytoken)) {
                die('Access is denied by Ticket Auth');
            }
        } else {
            die('Ticket presented but no library for it');
        }
    } else {
        $pass = 0;
        if (!is_null($course)) {
            require_login($course);
            $context = context_course::instance($course->id);
            if (has_capability('report/trainingsessions:viewother', $context)) {
                $pass = 1;
            }
            if ($userid == $USER->id && has_capability('report/trainingsessions:downloadreports', $context)) {
                $pass = 1;
            }
        } else {
            require_login();
            $context = context_system::instance();
            if (has_capability('report/trainingsessions:viewother', $context)) {
                $pass = 1;
            }
        }

        if (!$pass) {
            print_error(get_string('notallowed', 'report_trainingsessions'));
        }
    }
}

/**
 * Counts the number of sessions in a specific course from the global &$sessions array.
 *
 * @param arrayref &$sessions array of sessions from the aggregate.
 * @param int $courseid
 * @return int
 */
function report_trainingsessions_count_sessions_in_course(&$sessions, $courseid) {
    $count = 0;

    if (!empty($sessions)) {
        foreach ($sessions as $s) {

            if (!isset($s->sessionend) && empty($s->elapsed)) {
                // This is a "not true" session reliquate. Ignore it.
                continue;
            }

            if (empty($s->courses)) {
                continue;
            }

            if ($courseid) {
                if (in_array($courseid, array_keys($s->courses))) {
                    $count++;
                }
            } else {
                $count++;
            }
        }
    }
    return $count;
}

/**
 * Process a "single user" document by lauching a CURL request to the document generator task. The result
 * is directly stored as a file in moodle filestore if a suitable filerec has been provided, or returns the
 * document content as plain row string.
 *
 * @param type $user
 * @param type $id
 * @param type $from
 * @param type $to
 * @param type $timesession
 * @param type $uri
 * @param type $filerec
 * @param type $reportscope
 * @return type
 */
function report_trainingsessions_process_user_file($user, $id, $from, $to, $timesession, $uri, $filerec = null,
                                                   $reportscope = 'currentcourse') {
    mtrace('Compile_users for user : '.fullname($user)."<br/>\n");

    $fs = get_file_storage();

    $rqfields = array();
    $rqfields[] = 'id='.$id;
    $rqfields[] = 'from='.$from;
    $rqfields[] = 'to='.$to;
    $rqfields[] = 'userid='.$user->id;
    $rqfields[] = 'timesession='.$timesession;
    $rqfields[] = 'scope='.$reportscope;
    $rqfields[] = 'ticket='.report_trainingsessions_back_office_get_ticket();

    $rq = implode('&', $rqfields);

    $ch = curl_init($uri.'?'.$rq);
    if (function_exists('debug_trace')) {
        debug_trace("Firing url : {$uri}?{$rq}<br/>\n");
    }
    if (debugging()) {
        mtrace('Calling : '.$uri.'?'.$rq."<br/>\n");
        mtrace('direct link : <a href="'.$uri.'?'.$rq."\">Generate direct single doc</a><br/>\n");
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle Report Batch');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $raw = curl_exec($ch);

    // Check for curl errors.
    $curlerrno = curl_errno($ch);
    if ($curlerrno != 0) {
        debugging("Request for <a href=\"{$uri}?{$rq}\">User {$user->id}</a> failed with curl error $curlerrno");
    }

    // Check HTTP error code.
    $info = curl_getinfo($ch);
    if (!empty($info['http_code']) &&
            ($info['http_code'] != 200) &&
                    ($info['http_code'] != 303)) {
        debugging("Request for <a href=\"{$uri}?{$rq}\">User {$user->id}</a> failed with HTTP code ".$info['http_code']);
    } else {
        if (!is_null($filerec)) {
            // Feed pdf result in file storage.
            $oldfile = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea, $filerec->itemid,
                                     $filerec->filepath, $filerec->filename);
            if ($oldfile) {
                // Clean old file before.
                $oldfile->delete();
            }
            $newfile = $fs->create_file_from_string($filerec, $raw);

            $createdurl = moodle_url::make_pluginfile_url($filerec->contextid, $filerec->component, $filerec->filearea,
                                                          $filerec->itemid, $filerec->filepath, $filerec->filename);
            mtrace('Result : <a href="'.$createdurl.'" >'.$filerec->filename."</a><br/>\n");
        } else {
            return $raw;
        }
    }

    curl_close($ch);
}

/**
 * Processes a single group compilation document by lauching a CURL request to the document generator task. The result
 * is directly stored as a file in moodle filestore if a suitable filerec has been provided, or returns the
 * document content as plain row string.
 *
 * @param object $group the group being compiled
 * @param int $id the current course id
 * @param int $from from timestamp
 * @param to $to to timestamp
 * @param type $timesession
 * @param string $uri the task uri to call.
 * @param object $filerec the file descriptor to store the result of the proceesing
 * @param string $reportscope
 * @return mixed void/raw if no file descriptor is given, will return the raw response of the call.
 */
function report_trainingsessions_process_group_file($group, $id, $from, $to, $timesession, $uri, $filerec = null,
                                                    $reportscope = 'currentcourse') {
    mtrace('Compile_users for group : '.$group->name."<br/>\n");

    $fs = get_file_storage();

    $rqfields = array();
    $rqfields[] = 'id='.$id;
    $rqfields[] = 'from='.$from;
    $rqfields[] = 'to='.$to;
    $rqfields[] = 'groupid='.$group->id;
    $rqfields[] = 'timesession='.$timesession;
    $rqfields[] = 'scope='.$reportscope;
    $rqfields[] = 'ticket='.report_trainingsessions_back_office_get_ticket();

    $rq = implode('&', $rqfields);

    $ch = curl_init($uri.'?'.$rq);

    if (function_exists('debug_trace')) {
        debug_trace("Firing url : {$uri}?{$rq}<br/>\n");
    }

    if (debugging()) {
        mtrace('Calling : '.$uri.'?'.$rq."<br/>\n");
        mtrace('direct link : <a href="'.$uri.'?'.$rq."\">Generate direct single doc</a><br/>\n");
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle Report Batch');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $raw = curl_exec($ch);

    // Check for curl errors.
    $curlerrno = curl_errno($ch);
    if ($curlerrno != 0) {
        debugging("Request for <a href=\"{$uri}?{$rq}\">Group {$group->id}</a> failed with curl error $curlerrno");
    }

    // Check HTTP error code.
    $info = curl_getinfo($ch);
    if (!empty($info['http_code']) && ($info['http_code'] != 200) && ($info['http_code'] != 303)) {
        debugging("Request for <a href=\"{$uri}?{$rq}\">Group {$group->id}</a> failed with HTTP code ".$info['http_code']);
    } else {
        if (!is_null($filerec)) {
            // Feed xls result in file storage.
            $oldfile = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea, $filerec->itemid,
                                     $filerec->filepath, $filerec->filename);
            if ($oldfile) {
                // Clean old file before.
                $oldfile->delete();
            }
            $newfile = $fs->create_file_from_string($filerec, $raw);

            $createdurl = moodle_url::make_pluginfile_url($filerec->contextid, $filerec->component, $filerec->filearea,
                                                          $filerec->itemid, $filerec->filepath, $filerec->filename);
            mtrace('Result : <a href="'.$createdurl.'" >'.$filerec->filename."</a><br/>\n");
        } else {
            return $raw;
        }
    }

    curl_close($ch);
}

/**
 *
 * @param type $courseid
 * @param type $groupid
 * @param type $range
 * @return type
 */
function report_trainingsessions_compute_groups($courseid, $groupid, $range) {

    $config = get_config('report_trainingsessions');

    // If no groups existing, get all course.
    $groups = groups_get_all_groups($courseid);
    if (!$groups && !$groupid) {
        $groups = array();
        $group = new StdClass;
        $group->id = 0;
        $group->name = get_string('course');
        if ($range == 'user') {
            $context = context_course::instance($courseid);
            $group->target = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
        }
        $groups[] = $group;
    } else if ($groups && !$groupid) {
        if ($range == 'user') {
            foreach ($groups as $group) {
                $group->target = get_enrolled_users($context, '', $group->id, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
            }
        }
    } else {
        // Only one group. Reduce group list to this group.
        if ($range == 'user') {
            $group = $groups[$groupid];
            $group->target = get_enrolled_users($context, '', $groupid, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
            $groups = array();
            $groups[] = $group;
        }
    }
    return $groups;
}

/**
 * Given a session that might overpass day boundaries, splice into single day sessions.
 * @see report_learningtimecheck for similar implementation and full unit tests.
 * @param object $session a session object with sessionstart, sessionend and elapsed members.
 */
function report_trainingsessions_splice_session($session) {
    $daytimestart = date('G', $session->sessionstart) * HOURSECS;
    $daytimestart += date('i', $session->sessionstart) * MINSECS;
    $daytimestart += date('s', $session->sessionstart);
    $endofday = 24 * HOURSECS;
    $daygap = $endofday - $daytimestart;
    $startstamp = $session->sessionstart;

    $sessions = array();

    while ($startstamp + $daygap < $session->sessionend) {
        $daysess = new StdClass();
        $daysess->sessionstart = $startstamp;
        $daysess->sessionend = $startstamp + $daygap;
        $daysess->courses = $session->courses;
        $daysess->elapsed = $daygap;
        $daytimestart = 0; // Back to midnight.
        $daygap = $endofday - $daytimestart;
        $startstamp = $daysess->sessionend;
        $sessions[] = $daysess;
    }

    // We now need to keep the last segment.
    if ($startstamp < $session->sessionend) {
        $daysess = new stdClass();
        $daysess->sessionstart = $startstamp;
        $daysess->sessionend = $session->sessionend;
        $daysess->courses = $session->courses;
        $daysess->elapsed = $session->sessionend - $daysess->sessionstart;
        $sessions[] = $daysess;
    }

    return $sessions;
}

/**
 * Gives the available format options.
 */
function report_trainingsessions_get_batch_formats() {
    global $CFG;
    static $options;

    if (!isset($options)) {
        $options = array();
        if (report_trainingsessions_supports_feature('format/csv')) {
            $options['csv'] = get_string('csv', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('format/xls')) {
            $options['xls'] = get_string('xls', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('format/pdf')) {
            $options['pdf'] = get_string('pdf', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('format/json')) {
            $options['json'] = get_string('json', 'report_trainingsessions');
        }
    }

    return $options;
}

/**
 * Gives the available format options.
 */
function report_trainingsessions_get_batch_replays() {
    global $CFG;
    static $options;

    if (!isset($options)) {
        $options = array();
        if (report_trainingsessions_supports_feature('replay/single')) {
            $options[TASK_SINGLE] = get_string('singleexec', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('replay/replay')) {
            $options[TASK_REPLAY] = get_string('replay', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('replay/shift')) {
            $options[TASK_SHIFT] = get_string('periodshift', 'report_trainingsessions');
        }

        if (report_trainingsessions_supports_feature('replay/shiftto')) {
            $options[TASK_SHIFT_TO] = get_string('periodshiftto', 'report_trainingsessions');
        }
    }

    return $options;
}

function report_trainingsessions_batch_input($course) {
    $input = new StdClass;

    $startday = optional_param('startday', -1, PARAM_INT); // From (-1 is from course start).
    $startmonth = optional_param('startmonth', -1, PARAM_INT); // From (-1 is from course start).
    $startyear = optional_param('startyear', -1, PARAM_INT); // From (-1 is from course start).
    $endday = optional_param('endday', -1, PARAM_INT); // To (-1 is till now).
    $endmonth = optional_param('endmonth', -1, PARAM_INT); // To (-1 is till now).
    $endyear = optional_param('endyear', -1, PARAM_INT); // To (-1 is till now).
    $fromstart = optional_param('fromstart', 0, PARAM_INT); // Force reset to course startdate.
    $input->from = optional_param('from', -1, PARAM_INT); // Alternate way of saying from when for XML generation.
    $input->to = optional_param('to', -1, PARAM_INT); // Alternate way of saying from when for XML generation.
    $input->timesession = optional_param('timesession', time(), PARAM_INT); // Time of the generation batch.
    $input->readabletimesession = date('Y/m/d H:i:s', $input->timesession);
    $input->filenametimesession = date('Ymd_His', $input->timesession);
    $input->sessionday = date('Ymd', $input->timesession);

    if ($input->from == -1) {
        // Maybe we get it from parameters.
        if (($startday == -1) || $fromstart) {
            $input->from = $course->startdate;
        } else {
            if (($startmonth != -1) && ($startyear != -1)) {
                $input->from = mktime(0, 0, 8, $startmonth, $startday, $startyear);
            } else {
                die('Bad start date');
            }
        }
    }

    if ($input->to == -1) {
        // Maybe we get it from parameters.
        if ($endday == -1) {
            $input->to = time();
        } else {
            if (($endmonth != -1) && ($endyear != -1)) {
                $input->to = mktime(0, 0, 8, $endmonth, $endday, $endyear);
            } else {
                die('Bad end date');
            }
        }
    }

    return $input;
}

/**
 * Loads prioritarily the pro version if exists.
 * $param string $file library file to load.
 */
function report_trainingsessions_plugin_require($file) {
    global $CFG;

    if (strpos($file, '/') === false) {
        throw new coding_exception('Path must be relative and start with /');
    }

    $relname = preg_replace('#^'.$CFG->dirroot.'#', '', $file);
    $relcompname = str_replace('/report/trainingsessions/', '', $relname);
    $profile = $CFG->dirroot.'/report/trainingsessions/pro/'.$relcompname;
    $communfile = $CFG->dirroot.$relname;

    if (file_exists($profile)) {
        include_once($profile);
        return 'pro';
    } else {
        if (file_exists($CFG->dirroot.$relname)) {
            include_once($CFG->dirroot.$relname);
            return 'community';
        } else {
            throw new coding_exception('Require path could not be found in either package.');
        }
    }
}

/**
 * Loads an available version of the library, pro prefered if exists.
 * If not exists, silently fails without blocking. this may be used to
 * call for prolibs indistinctly from common code.
 */
function report_trainingsessions_plugin_include($file) {
    global $CFG;

    if (strpos($file, '/') === false) {
        throw new coding_exception('Path must be relative and start with /');
    }

    $relname = str_replace($CFG->dirroot, '', $file);
    $relcompname = str_replace('/report/trainingsessions/', '', $relname);
    $profile = $CFG->dirroot.'/report/trainingsessions/pro/'.$relcompname;
    $communfile = $CFG->dirroot.$relname;

    if (file_exists($profile)) {
        include_once($profile);
        return 'pro';
    } else {
        if (file_exists($file)) {
            include_once($file);
            return 'community';
        }
    }
    return '';
}

/**
 * Extract summary columns keys from configuration. the configuration keys are
 * couple of colname,<colformat> specially for Excel output. format codes are:
 * - n : numeric (scalar)
 * - d : duration
 * - t : time/date
 * - a : textual
 *
 * @param string $what type of info to return.
 * - false return column names
 * - 'title' returns translated names
 * - 'format' returns expected format for column
 */
function report_trainingsessions_get_summary_cols($what = false) {

    $config = get_config('report_trainingsessions', 'summarycolumns');
    $cols = explode("\n", $config);

    $corekeys = array('idnumber', 'lastname', 'firstname', 'institution', 'department', 'firstaccess');

    $result = array();
    foreach ($cols as $c) {
        $c = trim($c);

        if (empty($c)) {
            // Ignore blank lines.
            continue;
        }

        list($key, $format) = explode(',', $c);

        if (preg_match('/^#/', $c)) {
            // Ignore commented lines.
            continue;
        }

        if ($what == 'title') {
            if (in_array($c, $corekeys)) {
                $result[] = get_string($key);
            } else {
                $result[] = get_string($key, 'report_trainingsessions');
            }
        } else if ($what == 'format') {
            $result[] = $format;
        } else {
            $result[] = $key;
        }
    }

    return $result;
}

/**
 * Extract cols data in order and return the expected data as flat array or associative array.
 * It computes the whole set of exportable results for one user in the course context.
 *
 * @param array $cols an array of expected columns names, out from settigns.
 * @param objectref $user a full user record to get some interesting data from
 * @param arrayref $aggregate time aggregation from use_stats module
 * @param arrayref $weekaggregate an additional aggregation compiled on one week
 * @param int $courseid the courseid. If null, the current course
 * @param boolean $associative if true, returns an associative array mapped on column names
 * @return array or hash table
 */
function report_trainingsessions_map_summary_cols($cols, &$user, &$aggregate, &$weekaggregate,
                                                  $courseid = 0, $associative = false) {
    global $COURSE;

    if ($courseid == 0) {
        $courseid = $COURSE->id;
    }

    $t = @$aggregate['coursetotal'];
    $w = @$weekaggregate['coursetotal'];

    if (!empty($aggregate['sessions'])) {
        $sessions = report_trainingsessions_count_sessions_in_course($aggregate['sessions'], $courseid);
    } else {
        $sessions = 0;
    }

    $colsources = array(
        'id' => $user->id,
        'idnumber' => $user->idnumber,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
        'email' => $user->email,
        'institution' => $user->institution,
        'department' => $user->department,
        'lastlogin' => $user->lastlogin,
        'activitytime' => 0 + @$aggregate['activities'][$courseid]->elapsed,
        'activityelapsed' => 0 + @$aggregate['activities'][$courseid]->elapsed,
        'coursetime' => 0 + @$aggregate['course'][$courseid]->elapsed,
        'courseelapsed' => 0 + @$aggregate['course'][$courseid]->elapsed,
        'othertime' => 0 + @$t[0]->elapsed,
        'otherelapsed' => 0 + @$t[0]->elapsed,
        'elapsed' => 0 + @$t[$courseid]->elapsed,
        'exttime' => 0 + @$t[$courseid]->elapsed + @$t[0]->elapsed + @$t[SITEID]->elapsed,
        'extelapsed' => 0 + @$t[$courseid]->elapsed + @$t[0]->elapsed + @$t[SITEID]->elapsed,
        'extother' => 0 + @$t[0]->elapsed + @$t[SITEID]->elapsed,
        'items' => 0 + @$t[$courseid]->items,
        'visiteditems' => 0 + @$t[$courseid]->visiteditems,
        'elapsedlastweek' => 0 + @$w[$courseid]->elapsed,
        'timelastweek' => 0 + @$w[$courseid]->elapsed,
        'extelapsedlastweek' => 0 + @$w[$courseid]->elapsed + @$w[0]->elapsed + @$w[1]->elapsed,
        'exttimelastweek' => 0 + @$w[$courseid]->elapsed + @$w[0]->elapsed + @$w[1]->elapsed,
        'extotherlastweek' => 0 + @$w[0]->elapsed + @$w[SITEID]->elapsed,
        'sessions' => $sessions,
        'workingsessions' => $sessions
    );

    $data = array();
    $colkeys = array_keys($colsources);
    foreach ($cols as $colkey) {
        // Inexisting col sources may be processed later by additional functions.
        if (in_array($colkey, $colkeys)) {
            if ($associative) {
                $data[$colkey] = $colsources[$colkey];

                if (is_siteadmin()) {
                    $data['eventslastweek'] = $data['hitslastweek'] = 0 + @$w[$courseid]->events;
                    $data['activityevents'] = $data['activityhits'] = 0 + @$aggregate['activities'][$courseid]->events;
                    $data['courseevents'] = $data['coursehits'] = 0 + @$aggregate['course'][$courseid]->events;
                    $data['otherevents'] = $data['otherhits'] = 0 + @$t[0]->events;
                    $data['events'] = $data['hits'] = $data['otherevents'] + $data['courseevents'];
                    $data['extevents'] = $data['exthits'] = 0 + @$t[$courseid]->events + @$t[0]->events + @$t[SITEID]->events;
                    $data['exteventslastweek'] = $data['exthitslastweek'] = 0 + @$w[$courseid]->events + @$w[0]->events + @$w[1]->events;
                }

            } else {
                $data[] = $colsources[$colkey];
            }
        }
    }

    return $data;
}

/**
 * Processes the range boundaries returning from form.
 * @param array $data
 */
function report_trainingsessions_process_bounds(&$data, &$course) {
    // Calculate start time.

    // Calculate start time.
    if (!empty($data->fromstart)) {
        $data->from = $course->startdate;
    } else {
        if ($data->from == -1) {
            $data->from = $course->startdate;
        } else {
            if ($data->from > 0) {
                // Maybe we get it from parameters.
                $dateelms = getdate($data->from);
                $data->startmonth = $dateelms['mon'];
                $data->startyear = $dateelms['year'];
                $data->startday = $dateelms['mday'];

                $data->from = mktime(0, 0, 0, $data->startmonth, $data->startday, $data->startyear);
            } else {
                print_error('Bad start date');
            }
        }
    }

    if (($data->to == -1) || @$data->tonow) {
        // Maybe we get it from parameters.
        $data->to = time();
    } else {

        $dateelms = getdate($data->to);
        $data->endmonth = $dateelms['mon'];
        $data->endyear = $dateelms['year'];
        $data->endday = $dateelms['mday'];

        /*
         * The displayed time in form is giving a 0h00 time. We should push till
         * 23h59 of the given day
         */
        if ($data->endday == -1 || !empty($data->tonow)) {
            $data->to = time();
        } else if ($data->endmonth != -1 && $data->endyear != -1) {
            $data->to = mktime(23, 59, 59, $data->endmonth, $data->endday, $data->endyear);
        } else {
            print_error('Bad end date');
        }
    }
}

/**
 * Precalculates subtree aggregates without printing anything
 * @param objectref &$pdf the pdf document
 * @param int $y the current vertical position in page
 * @param objectref &$structure the course structure subtree
 * @param objectref &$aggregate the log aggregation
 * @param intref &$done the "done items" counter
 * @param int $level the current recursion level in structure
 */
function report_trainingsessions_calculate_course_structure(&$structure, &$aggregate, &$done, &$items) {

    if (empty($structure)) {
        return;
    }

    // makes a blank dataobject.
    $dataobject = new StdClass;
    $dataobject->elapsed = 0;
    $dataobject->events = 0;

    if (is_array($structure)) {
        // recurse in sub structures
        foreach ($structure as &$element) {
            if (isset($element->instance) && empty($element->instance->visible)) {
                // non visible items should not be displayed.
                continue;
            }
            $res = report_trainingsessions_calculate_course_structure($element, $aggregate, $done, $items);
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += $res->events;
        }
    } else {
        if (!empty($structure->visible) || !isset($structure->instance) || !empty($structure->instance->visible)) {
            // Non visible items should not be displayed.
            if (!empty($structure->name)) {
                $items++;
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $done++;
                    $dataobject->elapsed = $aggregate[$structure->type][$structure->id]->elapsed;
                    $dataobject->events = $aggregate[$structure->type][$structure->id]->events;
                } else {
                    $dataobject->elapsed = 0;
                    $dataobject->events = 0;
                }

                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_calculate_course_structure($structure->subs, $aggregate, $done, $items);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            } else {
                // It is only a structural module that should not impact on level
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $dataobject->elapsed = $aggregate[$structure->type][$structure->id]->elapsed;
                    $dataobject->events = $aggregate[$structure->type][$structure->id]->events;
                }
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_calculate_course_structure($structure->subs, $aggregate, $done, $items);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }

            // Report in element.
            $structure->elapsed = $dataobject->elapsed;
            $structure->events = $dataobject->events;
        }
    }

    // Returns acumulated aggregates.
    return $dataobject;
}
