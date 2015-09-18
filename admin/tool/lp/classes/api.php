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
 * Class for loading/storing competency frameworks from the DB.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;
use context;
use context_helper;
use context_system;
use context_course;
use context_user;
use coding_exception;
use required_capability_exception;

/**
 * Class for doing things with competency frameworks.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Create a competency from a record containing all the data for the class.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return competency
     */
    public static function create_competency(stdClass $record) {
        $competency = new competency(0, $record);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $competency->get_framework()->get_context());

        // OK - all set.
        $id = $competency->create();
        return $competency;
    }

    /**
     * Delete a competency by id.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The record to delete. This will delete alot of related data - you better be sure.
     * @return boolean
     */
    public static function delete_competency($id) {
        $competency = new competency($id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $competency->get_framework()->get_context());

        // OK - all set.
        $competency->set_id($id);
        return $competency->delete();
    }

    /**
     * Reorder this competency.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @return boolean
     */
    public static function move_down_competency($id) {
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $current->get_framework()->get_context());

        $max = self::count_competencies(array('parentid' => $current->get_parentid(),
                                             'competencyframeworkid' => $current->get_competencyframeworkid()));
        if ($max > 0) {
            $max--;
        }

        $sortorder = $current->get_sortorder();
        if ($sortorder >= $max) {
            return false;
        }
        $sortorder = $sortorder + 1;
        $current->set_sortorder($sortorder);

        $filters = array('parentid' => $current->get_parentid(),
                         'competencyframeworkid' => $current->get_competencyframeworkid(),
                         'sortorder' => $sortorder);
        $children = self::list_competencies($filters, 'id');
        foreach ($children as $needtoswap) {
            $needtoswap->set_sortorder($sortorder - 1);
            $needtoswap->update();
        }

        // OK - all set.
        return $current->update();
    }

    /**
     * Reorder this competency.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @return boolean
     */
    public static function move_up_competency($id) {
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $current->get_framework()->get_context());

        $sortorder = $current->get_sortorder();
        if ($sortorder == 0) {
            return false;
        }

        $sortorder = $sortorder - 1;
        $current->set_sortorder($sortorder);

        $filters = array('parentid' => $current->get_parentid(),
                         'competencyframeworkid' => $current->get_competencyframeworkid(),
                         'sortorder' => $sortorder);
        $children = self::list_competencies($filters, 'id');
        foreach ($children as $needtoswap) {
            $needtoswap->set_sortorder($sortorder + 1);
            $needtoswap->update();
        }

        // OK - all set.
        return $current->update();
    }

    /**
     * Move this competency so it sits in a new parent.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The id of the competency to move.
     * @param int $newparentid The new parent id for the competency.
     * @return boolean
     */
    public static function set_parent_competency($id, $newparentid) {
        $current = new competency($id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $current->get_framework()->get_context());

        // This will throw an exception if the parent does not exist.

        // Check the current one too.
        $parentframeworkid = $current->get_competencyframeworkid();
        $parentpath = '/0/';
        if ($newparentid) {
            $parent = new competency($newparentid);
            $parentframeworkid = $parent->get_competencyframeworkid();
            $parentpath = $parent->get_path();
        }

        if ($parentframeworkid != $current->get_competencyframeworkid()) {
            // Only allow moving within the same framework.
            throw new coding_exception('Moving competencies is only supported within the same framework.');
        }

        // If we are moving a node to a child of itself, promote all the child nodes by one level.

        $newparents = explode('/', $parentpath);
        if (in_array($current->get_id(), $newparents)) {
            $filters = array('parentid' => $current->get_id(), 'competencyframeworkid' => $current->get_competencyframeworkid());
            $children = self::list_competencies($filters, 'id');

            foreach ($children as $child) {
                $child->set_parentid($current->get_parentid());
                $child->update();
            }
        }

        $current->set_parentid($newparentid);

        // OK - all set.
        return $current->update();
    }

    /**
     * Update the details for a competency.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param stdClass $record The new details for the competency.
     *                         Note - must contain an id that points to the competency to update.
     *
     * @return boolean
     */
    public static function update_competency($record) {
        $current = new competency($record->id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $current->get_framework()->get_context());

        // Some things should not be changed in an update - they should use a more specific method.
        $record->sortorder = $current->get_sortorder();
        $record->parentid = $current->get_parentid();
        $record->competencyframeworkid = $current->get_competencyframeworkid();

        $competency = new competency(0, $record);
        require_capability('tool/lp:competencymanage', $competency->get_framework()->get_context());

        // OK - all set.
        return $competency->update();
    }

    /**
     * Read a the details for a single competency and return a record.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param int $id The id of the competency to read.
     * @return stdClass
     */
    public static function read_competency($id) {
        $competency = new competency($id);

        // First we do a permissions check.
        $context = $competency->get_framework()->get_context();
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        return $competency;
    }

    /**
     * Perform a text search based and return all results and their parents.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param string $textsearch A string to search for.
     * @param int $competencyframeworkid The id of the framework to limit the search.
     * @return array of competencies
     */
    public static function search_competencies($textsearch, $competencyframeworkid) {
        $framework = new competency_framework($competencyframeworkid);

        // First we do a permissions check.
        $context = $framework->get_context();
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        $competency = new competency();
        return $competency->search($textsearch, $competencyframeworkid);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires tool/lp:competencyread capability at some context.
     *
     * @param array $filters A list of filters to apply to the list.
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @return array of competencies
     */
    public static function list_competencies($filters, $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        if (!isset($filters['competencyframeworkid'])) {
            $context = context_system::instance();
        } else {
            $framework = new competency_framework($filters['competencyframeworkid']);
            $context = $framework->get_context();
        }

        // First we do a permissions check.
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        $competency = new competency();
        return $competency->get_records($filters, $sort, $order, $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires tool/lp:competencyread capability at some context.
     *
     * @param array $filters A list of filters to apply to the list.
     * @return int
     */
    public static function count_competencies($filters) {
        if (!isset($filters['competencyframeworkid'])) {
            $context = context_system::instance();
        } else {
            $framework = new competency_framework($filters['competencyframeworkid']);
            $context = $framework->get_context();
        }

        // First we do a permissions check.
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        $competency = new competency();
        return $competency->count_records($filters);
    }

    /**
     * Create a competency framework from a record containing all the data for the class.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return competency_framework
     */
    public static function create_framework(stdClass $record) {
        $framework = new competency_framework(0, $record);
        require_capability('tool/lp:competencymanage', $framework->get_context());
        $id = $framework->create();
        return $framework;
    }

    /**
     * Delete a competency framework by id.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The record to delete. This will delete alot of related data - you better be sure.
     * @return boolean
     */
    public static function delete_framework($id) {
        $framework = new competency_framework($id);
        require_capability('tool/lp:competencymanage', $framework->get_context());
        return $framework->delete();
    }

    /**
     * Update the details for a competency framework.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param stdClass $record The new details for the framework. Note - must contain an id that points to the framework to update.
     * @return boolean
     */
    public static function update_framework($record) {
        $framework = new competency_framework($record->id);
        // Check the permissions before update.
        require_capability('tool/lp:competencymanage', $framework->get_context());
        if (isset($record->contextid) && $record->contextid != $framework->get_contextid()) {
            throw new coding_exception('Changing the context of an existing framework is forbidden.');
        }
        $framework->from_record($record);
        return $framework->update();
    }

    /**
     * Read a the details for a single competency framework and return a record.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param int $id The id of the framework to read.
     * @return competency_framework
     */
    public static function read_framework($id) {
        $framework = new competency_framework($id);
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $framework->get_context())) {
             throw new required_capability_exception($framework->get_context(), 'tool/lp:competencyread', 'nopermissions', '');
        }
        return $framework;
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return array of competency_framework
     */
    public static function list_frameworks($sort, $order, $skip, $limit, $context, $includes = 'children') {
        global $DB;

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('tool/lp:competencyread', 'tool/lp:competencymanage'));

        if (empty($contexts)) {
            throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        $framework = new competency_framework();
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return $framework->get_records_select("contextid $insql", $inparams, $sort, '*', $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return int
     */
    public static function count_frameworks($context, $includes) {
        global $DB;

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('tool/lp:competencyread', 'tool/lp:competencymanage'));

        if (empty($contexts)) {
            throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        $framework = new competency_framework();
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return $framework->count_records_select("contextid $insql", $inparams);
    }

    /**
     * Fetches all the relevant contexts.
     *
     * Note: This currently only supports system and category contexts.
     *
     * @param context $context The context to start from.
     * @param string $includes Defines what other contexts to find.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @param array $hasanycapability Array of capabilities passed to {@link has_any_capability()} in each context.
     * @return context[] An array of contexts where keys are context IDs.
     */
    public static function get_related_contexts($context, $includes, array $hasanycapability = null) {
        global $DB;

        if (!in_array($includes, array('children', 'parents', 'self'))) {
            throw new coding_exception('Invalid parameter value for \'includes\'.');
        }

        $contexts = array($context->id => $context);

        if ($includes == 'children') {
            $params = array('coursecatlevel' => CONTEXT_COURSECAT, 'path' => $context->path . '/%');
            $pathlike = $DB->sql_like('path', ':path');
            $sql = "contextlevel = :coursecatlevel AND $pathlike";
            $rs = $DB->get_recordset_select('context', $sql, $params);
            foreach ($rs as $record) {
                $ctxid = $record->id;
                context_helper::preload_from_record($record);
                $contexts[$ctxid] = context::instance_by_id($ctxid);
            }
            $rs->close();

        } else if ($includes == 'parents') {
            $children = $context->get_parent_contexts();
            foreach ($children as $ctx) {
                $contexts[$ctx->id] = $ctx;
            }
        }

        // Filter according to the capabilities required.
        if (!empty($hasanycapability)) {
            foreach ($contexts as $key => $ctx) {
                if (!has_any_capability($hasanycapability, $ctx)) {
                    unset($contexts[$key]);
                }
            }
        }

        return $contexts;
    }

    /**
     * Count all the courses using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return int
     */
    public static function count_courses_using_competency($competencyid) {

        // OK - all set.
        $coursecompetency = new course_competency();
        $courses = $coursecompetency->list_courses_min($competencyid);
        $count = 0;
        // Now check permissions on each course.
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
                continue;
            }

            if (!$course->visible && !has_capability('course:viewhidden', $context)) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    /**
     * List all the courses using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return array[stdClass] Array of stdClass containing id and shortname.
     */
    public static function list_courses_using_competency($competencyid) {

        // OK - all set.
        $coursecompetency = new course_competency();
        $courses = $coursecompetency->list_courses($competencyid);
        $result = array();
        // Now check permissions on each course.
        foreach ($courses as $id => $course) {
            $context = context_course::instance($course->id);
            $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
                unset($courses[$id]);
                continue;
            }

            if (!$course->visible && !has_capability('course:viewhidden', $context)) {
                unset($courses[$id]);
                continue;
            }
            $course->fullnameformatted = format_text($course->fullname, array('context' => $context));
            $course->shortnameformatted = format_text($course->shortname, array('context' => $context));
            array_push($result, $course);
        }

        return $result;
    }

    /**
     * Count all the competencies in a course.
     *
     * @param int $courseid The id of the course to check.
     * @return int
     */
    public static function count_competencies_in_course($courseid) {
        // First we do a permissions check.
        $context = context_course::instance($courseid);
        $onlyvisible = 1;

        $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:coursecompetencyread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:coursecompetencymanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $coursecompetency = new course_competency();
        return $coursecompetency->count_competencies($courseid, $onlyvisible);
    }

    /**
     * List all the competencies in a course.
     *
     * @param int $courseid The id of the course to check.
     * @return array of competencies
     */
    public static function list_competencies_in_course($courseid) {
        // First we do a permissions check.
        $context = context_course::instance($courseid);
        $onlyvisible = 1;

        $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:coursecompetencyread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:coursecompetencymanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $coursecompetency = new course_competency();
        return $coursecompetency->list_competencies($courseid, $onlyvisible);
    }

    /**
     * Add a competency to this course.
     *
     * @param int $courseid The id of the course
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_course($courseid, $competencyid) {
        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $competency = new competency();
        $competency->set_id($competencyid);
        if (!$competency->read()) {
             throw new coding_exception('The competency does not exist');
        }

        $coursecompetency = new course_competency();
        $exists = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyid));
        if (!$exists) {
            $coursecompetency->from_record($record);
            if ($coursecompetency->create()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a competency from this course.
     *
     * @param int $courseid The id of the course
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_course($courseid, $competencyid) {
        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $competency = new competency();
        $competency->set_id($competencyid);
        if (!$competency->read()) {
             throw new coding_exception('The competency does not exist');
        }

        $coursecompetency = new course_competency();
        $exists = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyid));
        if ($exists) {
            $competency = array_pop($exists);
            return $competency->delete();
        }
        return false;
    }

    /**
     * Move the course competency up or down in the display list.
     *
     * Requires tool/lp:coursecompetencymanage capability at the course context.
     *
     * @param int $courseid The course
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_course_competency($courseid, $competencyidfrom, $competencyidto) {
        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $context);

        $down = true;
        $coursecompetency = new course_competency();
        $matches = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = $coursecompetency->get_records(array('courseid' => $courseid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
             throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = $coursecompetency->get_records(array('courseid' => $courseid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $coursecompetency) {
            $sort = $coursecompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $coursecompetency->set_sortorder($coursecompetency->get_sortorder() - 1);
                $coursecompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $coursecompetency->set_sortorder($coursecompetency->get_sortorder() + 1);
                $coursecompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Create a learning plan template from a record containing all the data for the class.
     *
     * Requires tool/lp:templatemanage capability.
     *
     * @param stdClass $record Record containing all the data for an instance of the class.
     * @return template
     */
    public static function create_template(stdClass $record) {
        $template = new template(0, $record);

        // First we do a permissions check.
        require_capability('tool/lp:templatemanage', $template->get_context());

        // OK - all set.
        $id = $template->create();
        return $template;
    }

    /**
     * Delete a learning plan template by id.
     *
     * Requires tool/lp:templatemanage capability.
     *
     * @param int $id The record to delete.
     * @return boolean
     */
    public static function delete_template($id) {
        $template = new template($id);

        // First we do a permissions check.
        require_capability('tool/lp:templatemanage', $template->get_context());

        // OK - all set.
        return $template->delete();
    }

    /**
     * Update the details for a learning plan template.
     *
     * Requires tool/lp:templatemanage capability.
     *
     * @param stdClass $record The new details for the template. Note - must contain an id that points to the template to update.
     * @return boolean
     */
    public static function update_template($record) {
        $template = new template($record->id);

        // First we do a permissions check.
        require_capability('tool/lp:templatemanage', $template->get_context());
        if (isset($record->contextid) && $record->contextid != $template->get_contextid()) {
            throw new coding_exception('Changing the context of an existing tempalte is forbidden.');
        }

        $template->from_record($record);
        return $template->update();
    }

    /**
     * Read a the details for a single learning plan template and return a record.
     *
     * Requires tool/lp:templateread capability at the system context.
     *
     * @param int $id The id of the template to read.
     * @return template
     */
    public static function read_template($id) {
        $template = new template($id);
        $context = $template->get_context();

        // First we do a permissions check.
        $caps = array('tool/lp:templateread', 'tool/lp:templatemanage');
        if (!has_any_capability($caps, $context)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        // OK - all set.
        return $template;
    }

    /**
     * Perform a search based on the provided filters and return a paginated list of records.
     *
     * Requires tool/lp:templateread capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip Number of records to skip (pagination)
     * @param int $limit Max of records to return (pagination)
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return array of competency_framework
     */
    public static function list_templates($sort, $order, $skip, $limit, $context, $includes = 'children') {
        global $DB;

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('tool/lp:templateread', 'tool/lp:templatemanage'));

        // First we do a permissions check.
        if (empty($contexts)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        // Make the order by.
        $orderby = '';
        if (!empty($sort)) {
            $orderby = $sort . ' ' . $order;
        }

        // OK - all set.
        $template = new template();
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return $template->get_records_select("contextid $insql", $inparams, $orderby, '*', $skip, $limit);
    }

    /**
     * Perform a search based on the provided filters and return how many results there are.
     *
     * Requires tool/lp:templateread capability at the system context.
     *
     * @param context $context The parent context of the frameworks.
     * @param string $includes Defines what other contexts to fetch frameworks from.
     *                         Accepted values are:
     *                          - children: All descendants
     *                          - parents: All parents, grand parents, etc...
     *                          - self: Context passed only.
     * @return int
     */
    public static function count_templates($context, $includes) {
        global $DB;

        // First we do a permissions check.
        $contexts = self::get_related_contexts($context, $includes,
            array('tool/lp:templateread', 'tool/lp:templatemanage'));

        if (empty($contexts)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        // OK - all set.
        $template = new template();
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return $template->count_records_select("contextid $insql", $inparams);
    }

    /**
     * Count all the templates using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return int
     */
    public static function count_templates_using_competency($competencyid) {
        // First we do a permissions check.
        $context = context_system::instance();
        $onlyvisible = 1;

        $capabilities = array('tool/lp:templateread', 'tool/lp:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $templatecompetency = new template_competency();
        return $templatecompetency->count_templates($competencyid, $onlyvisible);
    }

    /**
     * List all the learning plan templatesd using a competency.
     *
     * @param int $competencyid The id of the competency to check.
     * @return array[stdClass] Array of stdClass containing id and shortname.
     */
    public static function list_templates_using_competency($competencyid) {
        // First we do a permissions check.
        $context = context_system::instance();
        $onlyvisible = 1;

        $capabilities = array('tool/lp:templateread', 'tool/lp:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $templatecompetency = new template_competency();
        return $templatecompetency->list_templates($competencyid, $onlyvisible);

    }

    /**
     * Count all the competencies in a learning plan template.
     *
     * @param int $templateid The id of the template to check.
     * @return int
     */
    public static function count_competencies_in_template($templateid) {
        // First we do a permissions check.
        $template = new template($templateid);
        $context = $template->get_context();
        $onlyvisible = 1;

        $capabilities = array('tool/lp:templateread', 'tool/lp:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $templatecompetency = new template_competency();
        return $templatecompetency->count_competencies($templateid, $onlyvisible);
    }

    /**
     * List all the competencies in a template.
     *
     * @param int $templateid The id of the template to check.
     * @return array of competencies
     */
    public static function list_competencies_in_template($templateid) {
        // First we do a permissions check.
        $template = new template($templateid);
        $context = $template->get_context();
        $onlyvisible = 1;

        $capabilities = array('tool/lp:templateread', 'tool/lp:templatemanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:templateread', 'nopermissions', '');
        }

        if (has_capability('tool/lp:templatemanage', $context)) {
            $onlyvisible = 0;
        }

        // OK - all set.
        $templatecompetency = new template_competency();
        return $templatecompetency->list_competencies($templateid, $onlyvisible);
    }

    /**
     * Add a competency to this template.
     *
     * @param int $templateid The id of the template
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_template($templateid, $competencyid) {
        // First we do a permissions check.
        $template = new template($templateid);
        require_capability('tool/lp:templatemanage', $template->get_context());

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency();
        $competency->set_id($competencyid);
        if (!$competency->read()) {
            throw new coding_exception('The competency does not exist');
        }

        $templatecompetency = new template_competency();
        $exists = $templatecompetency->get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
        if (!$exists) {
            $templatecompetency->from_record($record);
            if ($templatecompetency->create()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a competency from this template.
     *
     * @param int $templateid The id of the template
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_template($templateid, $competencyid) {
        // First we do a permissions check.
        $template = new template($templateid);
        require_capability('tool/lp:templatemanage', $template->get_context());

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);
        $competency->set_id($competencyid);
        if (!$competency->read()) {
             throw new coding_exception('The competency does not exist');
        }

        $templatecompetency = new template_competency();
        $exists = $templatecompetency->get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
        if ($exists) {
            $link = array_pop($exists);
            return $link->delete();
        }
        return false;
    }

    /**
     * Move the template competency up or down in the display list.
     *
     * Requires tool/lp:templatemanage capability at the system context.
     *
     * @param int $templateid The template id
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_template_competency($templateid, $competencyidfrom, $competencyidto) {
        // First we do a permissions check.
        $context = context_system::instance();

        require_capability('tool/lp:templatemanage', $context);

        $down = true;
        $templatecompetency = new template_competency();
        $matches = $templatecompetency->get_records(array('templateid' => $templateid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = $templatecompetency->get_records(array('templateid' => $templateid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = $templatecompetency->get_records(array('templateid' => $templateid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $templatecompetency) {
            $sort = $templatecompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $templatecompetency->set_sortorder($templatecompetency->get_sortorder() - 1);
                $templatecompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $templatecompetency->set_sortorder($templatecompetency->get_sortorder() + 1);
                $templatecompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * Lists user plans.
     *
     * @param int $userid
     * @return \tool_lp\plan[]
     */
    public static function list_user_plans($userid) {
        global $USER;

        $select = 'userid = :userid';
        $params = array('userid' => $userid);

        $context = context_user::instance($userid);

        // We can allow guest user to pass they will not have LP.
        if ($USER->id != $userid) {
            require_capability('tool/lp:planviewall', $context);
        } else {
            require_capability('tool/lp:planviewown', $context);
        }

        // Users that can manage plans can only see active and completed plans.
        if (!has_any_capability(array('tool/lp:planmanageall', 'tool/lp:planmanageown', 'tool/lp:plancreatedraft'), $context)) {
            $select = ' AND status != :statusdraft';
            $params['statusdraft'] = plan::STATUS_DRAFT;
        }

        $plans = new plan();
        return $plans->get_records_select($select, $params, 'timemodified DESC');
    }

    /**
     * Creates a learning plan based on the provided data.
     *
     * @param stdClass $record
     * @return \tool_lp\plan
     */
    public static function create_plan(stdClass $record) {
        global $USER;

        $context = context_user::instance($record->userid);

        $manageplans = has_capability('tool/lp:planmanageall', $context);
        $createdraft = has_capability('tool/lp:plancreatedraft', $context);
        $manageownplan = has_capability('tool/lp:planmanageown', $context);

        // Any of them is enough.
        if ($USER->id == $record->userid && !$manageplans && !$createdraft && !$manageownplan) {
            // Exception about plancreatedraft as it is the one that is closer to basic users.
            throw new required_capability_exception($context, 'tool/lp:plancreatedraft', 'nopermissions', '');
        } else if ($USER->id != $record->userid && !$manageplans) {
            throw new required_capability_exception($context, 'tool/lp:planmanageall', 'nopermissions', '');
        }

        if (!isset($record->status)) {
            // Default to status draft.
            $record->status = plan::STATUS_DRAFT;
        } else if ($record->status !== plan::STATUS_DRAFT && !$manageplans && !$manageownplan) {
            // If the user can only create drafts we don't allow them to set other status.
            throw new required_capability_exception($context, 'tool/lp:planmanageown', 'nopermissions', '');
        }

        $plan = new plan(0, $record);
        $id = $plan->create();
        return $plan;
    }

    /**
     * Updates a plan.
     *
     * @param stdClass $record
     * @return \tool_lp\plan
     */
    public static function update_plan(stdClass $record) {
        global $USER;

        $context = context_user::instance($record->userid);

        $manageplans = has_capability('tool/lp:planmanageall', $context);
        $createdraft = has_capability('tool/lp:plancreatedraft', $context);
        $manageownplan = has_capability('tool/lp:planmanageown', $context);

        // Any of them is enough.
        if ($USER->id == $record->userid && !$manageplans && !$createdraft && !$manageownplan) {
            throw new required_capability_exception($context, 'tool/lp:planmanageown', 'nopermissions', '');
        } else if (!$manageplans) {
            throw new required_capability_exception($context, 'tool/lp:planmanageall', 'nopermissions', '');
        }

        $current = new plan($record->id);

        // We don't allow users without planmanage and without
        // planmanageown to edit plans that other users modified.
        if (!$manageplans && !$manageownplan && $USER->id != $current->get_usermodified()) {
            throw new \moodle_exception('erroreditingmodifiedplan', 'tool_lp');
        } else if (!$manageplans && $USER->id != $current->get_userid()) {
            throw new required_capability_exception($context, 'tool/lp:planmanageall', 'nopermissions', '');
        }

        // If the user can only create drafts we don't allow them to set other status.
        if ($record->status !== plan::STATUS_DRAFT && !$manageplans && !$manageownplan) {
            throw new required_capability_exception($context, 'tool/lp:planmanageown', 'nopermissions', '');
        }

        $plan = new plan($record->id, $record);
        return $plan->update();
    }

    /**
     * Returns a plan data.
     *
     * @param int $id
     * @return \tool_lp\plan
     */
    public static function read_plan($id) {
        global $USER;

        $plan = new plan($id);
        $context = context_user::instance($plan->get_userid());

        if ($USER->id == $plan->get_userid()) {
            require_capability('tool/lp:planviewown', $context);
        } else {
            require_capability('tool/lp:planviewall', $context);
        }

        // We require any of these capabilities to retrieve draft plans.
        if ($plan->get_status() === plan::STATUS_DRAFT &&
                !has_any_capability(array('tool/lp:planmanageown', 'tool/lp:planmanageall', 'tool/lp:plancreatedraft'), $context)) {
            // Exception about plancreatedraft as it is the one that is closer to basic users.
            throw new required_capability_exception($context, 'tool/lp:plancreatedraft', 'nopermissions', '');
        }
        return $plan;
    }

    /**
     * Deletes a plan.
     *
     * @param int $id
     * @return bool Success?
     */
    public static function delete_plan($id) {
        global $USER;

        $plan = new plan($id);

        $context = context_user::instance($plan->get_userid());

        $manageplans = has_capability('tool/lp:planmanageall', $context);
        $manageownplan = has_capability('tool/lp:planmanageown', $context);

        if ($USER->id == $plan->get_userid() && $USER->id != $plan->get_usermodified() &&
                !$manageplans && !$manageownplan) {
            // A normal user can only edit its plan if they created it.
            throw new required_capability_exception($context, 'tool/lp:planmanageown', 'nopermissions', '');
        } else if ($USER->id != $plan->get_userid() && !$manageplans) {
            // Other users needs to have tool/lp:planmanage.
            throw new required_capability_exception($context, 'tool/lp:planmanageall', 'nopermissions', '');
        }

        return $plan->delete();
    }
}
