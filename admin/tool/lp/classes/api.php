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
defined('MOODLE_INTERNAL') || die();

use stdClass;
use context;
use context_helper;
use context_system;
use context_course;
use context_user;
use coding_exception;
use require_login_exception;
use moodle_exception;
use moodle_url;
use required_capability_exception;

/**
 * Class for doing things with competency frameworks.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Validate if current user have acces to the course if hidden.
     *
     * @param mixed $courseorid The course or it ID.
     * @param bool $throwexception Throw an exception or not.
     * @return bool
     */
    protected static function validate_course($courseorid, $throwexception = true) {
        $course = $courseorid;
        if (!is_object($course)) {
            $course = get_course($course);
        }

        $coursecontext = context_course::instance($course->id);
        if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
            if ($throwexception) {
                throw new require_login_exception('Course is hidden');
            } else {
                return false;
            }
        }

        return true;
    }

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
        require_capability('tool/lp:competencymanage', $competency->get_context());

        // Reset the sortorder, use reorder instead.
        $competency->set_sortorder(null);

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
        require_capability('tool/lp:competencymanage', $competency->get_context());

        // OK - all set.
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
        require_capability('tool/lp:competencymanage', $current->get_context());

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
        $current->update();

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
        require_capability('tool/lp:competencymanage', $current->get_context());

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
        require_capability('tool/lp:competencymanage', $current->get_context());

        // This will throw an exception if the parent does not exist.

        // Check the current one too.
        $parentframeworkid = $current->get_competencyframeworkid();
        $parentpath = '/0/';
        if ($newparentid) {
            $parent = new competency($newparentid);
            $parentframeworkid = $parent->get_competencyframeworkid();
            $parentpath = $parent->get_path();
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
        $competency = new competency($record->id);

        // First we do a permissions check.
        require_capability('tool/lp:competencymanage', $competency->get_context());

        // Some things should not be changed in an update - they should use a more specific method.
        $record->sortorder = $competency->get_sortorder();
        $record->parentid = $competency->get_parentid();
        $record->competencyframeworkid = $competency->get_competencyframeworkid();

        $competency->from_record($record);
        require_capability('tool/lp:competencymanage', $competency->get_context());

        // OK - all set.
        return $competency->update();
    }

    /**
     * Read a the details for a single competency and return a record.
     *
     * Requires tool/lp:competencyread capability at the system context.
     *
     * @param int $id The id of the competency to read.
     * @param bool $includerelated Include related tags or not.
     * @return stdClass
     */
    public static function read_competency($id, $includerelated = false) {
        $competency = new competency($id);

        // First we do a permissions check.
        $context = $competency->get_context();
        if (!has_any_capability(array('tool/lp:competencyread', 'tool/lp:competencymanage'), $context)) {
             throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        if ($includerelated) {
            $relatedcompetency = new related_competency();
            if ($related = $relatedcompetency->list_relations($id)) {
                $competency->relatedcompetencies = $related;
            }
        }

        return $competency;
    }

    /**
     * Perform a text search based and return all results and their parents.
     *
     * Requires tool/lp:competencyread capability at the framework context.
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
        $competencies = competency::search($textsearch, $competencyframeworkid);
        return $competencies;
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
        return competency::get_records($filters, $sort, $order, $skip, $limit);
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
        return competency::count_records($filters);
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

        // Account for different formats of taxonomies.
        if (isset($record->taxonomies)) {
            $framework->set_taxonomies($record->taxonomies);
        }

        $id = $framework->create();
        return $framework;
    }

    /**
     * Duplicate a competency framework by id.
     *
     * Requires tool/lp:competencymanage capability at the system context.
     *
     * @param int $id The record to duplicate. All competencies associated and related will be duplicated.
     * @return competency_framework the framework duplicated
     */
    public static function duplicate_framework($id) {
        $framework = new competency_framework($id);
        require_capability('tool/lp:competencymanage', $framework->get_context());

        // Get a uniq idnumber based on the origin framework.
        $idnumber = competency_framework::get_unused_idnumber($framework->get_idnumber());
        $framework->set_idnumber($idnumber);
        // Adding the suffix copy to the shortname.
        $framework->set_shortname(get_string('duplicateditemname', 'tool_lp', $framework->get_shortname()));
        $framework->set_id(0);
        $framework->create();

        // Array that match the old competencies ids with the new one to use when copying related competencies.
        $matchids = self::duplicate_competency_tree($framework->get_id(), competency::get_framework_tree($id), 0, 0);

        // Copy the related competencies.
        $relcomps = related_competency::get_multiple_relations(array_keys($matchids));

        foreach ($relcomps as $relcomp) {
            $compid = $relcomp->get_competencyid();
            $relcompid = $relcomp->get_relatedcompetencyid();
            if (isset($matchids[$compid]) && isset($matchids[$relcompid])) {
                $newcompid = $matchids[$compid];
                $newrelcompid = $matchids[$relcompid];
                if ($newcompid < $newrelcompid) {
                    $relcomp->set_competencyid($newcompid);
                    $relcomp->set_relatedcompetencyid($newrelcompid);
                } else {
                    $relcomp->set_competencyid($newrelcompid);
                    $relcomp->set_relatedcompetencyid($newcompid);
                }
                $relcomp->set_id(0);
                $relcomp->create();
            } else {
                // Debugging message when there is no match found.
                debugging('related competency id not found');
            }
        }
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

        // Account for different formats of taxonomies.
        $framework->from_record($record);
        if (isset($record->taxonomies)) {
            $framework->set_taxonomies($record->taxonomies);
        }

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
     * @param bool $onlyvisible If true return only visible frameworks
     * @return array of competency_framework
     */
    public static function list_frameworks($sort, $order, $skip, $limit, $context, $includes = 'children', $onlyvisible = false) {
        global $DB;

        // Get all the relevant contexts.
        $contexts = self::get_related_contexts($context, $includes,
            array('tool/lp:competencyread', 'tool/lp:competencymanage'));

        if (empty($contexts)) {
            throw new required_capability_exception($context, 'tool/lp:competencyread', 'nopermissions', '');
        }

        // OK - all set.
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        $select = "contextid $insql";
        if ($onlyvisible) {
            $select .= " AND visible = :visible";
            $inparams['visible'] = 1;
        }

        return competency_framework::get_records_select($select, $inparams, $sort . ' ' . $order, '*', $skip, $limit);
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
        list($insql, $inparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        return competency_framework::count_records_select("contextid $insql", $inparams);
    }

    /**
     * Fetches all the relevant contexts.
     *
     * Note: This currently only supports system, category and user contexts. However user contexts
     * behave a bit differently and will fallback on the system context. This is what makes the most
     * sense because a user context does not have descendants, and only has system as a parent.
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

        // If context user swap it for the context_system.
        if ($context->contextlevel == CONTEXT_USER) {
            $context = context_system::instance();
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
        $courses = course_competency::list_courses_min($competencyid);
        $count = 0;

        // Now check permissions on each course.
        foreach ($courses as $course) {
            if (!self::validate_course($course, false)) {
                continue;
            }

            $context = context_course::instance($course->id);
            $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
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
        $courses = course_competency::list_courses($competencyid);
        $result = array();

        // Now check permissions on each course.
        foreach ($courses as $id => $course) {
            $context = context_course::instance($course->id);
            $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
            if (!has_any_capability($capabilities, $context)) {
                unset($courses[$id]);
                continue;
            }
            if (!self::validate_course($course, false)) {
                unset($courses[$id]);
                continue;
            }
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
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
             throw new required_capability_exception($context, 'tool/lp:coursecompetencyread', 'nopermissions', '');
        }

        // OK - all set.
        return course_competency::count_competencies($courseid);
    }

    /**
     * List the competencies associated to a course.
     *
     * @param mixed $courseorid The course, or its ID.
     * @return array( array(
     *                   'competency' => \tool_lp\competency,
     *                   'coursecompetency' => \tool_lp\course_competency
     *              ))
     */
    public static function list_course_competencies($courseorid) {
        $course = $courseorid;
        if (!is_object($courseorid)) {
            $course = get_course($courseorid);
        }

        // Check the user have access to the course.
        self::validate_course($course);
        $context = context_course::instance($course->id);

        $capabilities = array('tool/lp:coursecompetencyread', 'tool/lp:coursecompetencymanage');
        if (!has_any_capability($capabilities, $context)) {
            throw new required_capability_exception($context, 'tool/lp:coursecompetencyread', 'nopermissions', '');
        }

        $result = array();

        // TODO We could improve the performance of this into one single query.
        $coursecompetencies = course_competency::list_course_competencies($course->id);
        $competencies = course_competency::list_competencies($course->id);

        // Build the return values.
        foreach ($coursecompetencies as $key => $coursecompetency) {
            $result[] = array(
                'competency' => $competencies[$coursecompetency->get_competencyid()],
                'coursecompetency' => $coursecompetency
            );
        }

        return $result;
    }

    /**
     * List all the user competencies in a course.
     *
     * @param int $courseid The id of the course to check.
     * @param int $userid The id of the course to check.
     * @return array of competencies
     */
    public static function list_user_competencies_in_course($courseid, $userid) {
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
        $competencylist = course_competency::list_competencies($courseid, $onlyvisible);

        return user_competency::get_multiple($userid, $competencylist);
    }

    /**
     * Add a competency to this course.
     *
     * @param int $courseid The id of the course
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_course($courseid, $competencyid) {
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be linked to course');
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
        // Check the user have access to the course.
        self::validate_course($courseid);

        // First we do a permissions check.
        $context = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $context);

        $record = new stdClass();
        $record->courseid = $courseid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);
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
        // Check the user have access to the course.
        self::validate_course($courseid);

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
     * Update ruleoutcome value for a course competency.
     *
     * @param int|course_competency $coursecompetencyorid The course_competency, or its ID.
     * @param int $ruleoutcome The value of ruleoutcome.
     * @return bool True on success.
     */
    public static function set_course_competency_ruleoutcome($coursecompetencyorid, $ruleoutcome) {
        $coursecompetency = $coursecompetencyorid;
        if (!is_object($coursecompetency)) {
            $coursecompetency = new course_competency($coursecompetencyorid);
        }

        $courseid = $coursecompetency->get_courseid();
        self::validate_course($courseid);
        $coursecontext = context_course::instance($courseid);

        require_capability('tool/lp:coursecompetencymanage', $coursecontext);

        $coursecompetency->set_ruleoutcome($ruleoutcome);
        return $coursecompetency->update();
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
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');
        }

        // OK - all set.
        $id = $template->create();
        return $template;
    }

    /**
     * Duplicate a learning plan template.
     *
     * Requires tool/lp:templatemanage capability at the template context.
     *
     * @param int $id the template id.
     * @return template
     */
    public static function duplicate_template($id) {
        $template = new template($id);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');
        }

        // OK - all set.
        $competencies = template_competency::list_competencies($id, false);

        // Adding the suffix copy.
        $template->set_shortname(get_string('duplicateditemname', 'tool_lp', $template->get_shortname()));
        $template->set_id(0);

        $duplicatedtemplate = $template->create();

        // Associate each competency for the duplicated template.
        foreach ($competencies as $competency) {
            self::add_competency_to_template($duplicatedtemplate->get_id(), $competency->get_id());
        }
        return $duplicatedtemplate;
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
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');
        }

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
        global $DB;
        $template = new template($record->id);

        // First we do a permissions check.
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');

        } else if (isset($record->contextid) && $record->contextid != $template->get_contextid()) {
             // We can never change the context of a template.
            throw new coding_exception('Changing the context of an existing tempalte is forbidden.');

        }

        $updateplans = false;
        $before = $template->to_record();

        $template->from_record($record);
        $after = $template->to_record();

        // Should we update the related plans?
        if ($before->duedate != $after->duedate ||
                $before->shortname != $after->shortname ||
                $before->description != $after->description ||
                $before->descriptionformat != $after->descriptionformat) {
            $updateplans = true;
        }

        $transaction = $DB->start_delegated_transaction();
        $success = $template->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('Error while updating the template.'));
            return $success;
        }

        if ($updateplans) {
            plan::update_multiple_from_template($template);
        }

        $transaction->allow_commit();

        return $success;
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
        if (!$template->can_read()) {
             throw new required_capability_exception($template->get_context(), 'tool/lp:templateread', 'nopermissions', '');
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
     * @param bool $onlyvisible If should list only visible templates
     * @return array of competency_framework
     */
    public static function list_templates($sort, $order, $skip, $limit, $context, $includes = 'children', $onlyvisible = false) {
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
        list($insql, $params) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        $select = "contextid $insql";

        if ($onlyvisible) {
            $select .= " AND visible = :visible";
            $params['visible'] = 1;
        }
        return $template->get_records_select($select, $params, $orderby, '*', $skip, $limit);
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
        return template_competency::count_templates($competencyid, $onlyvisible);
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
        return template_competency::list_templates($competencyid, $onlyvisible);

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

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templateread', 'nopermissions', '');
        }

        // OK - all set.
        return template_competency::count_competencies($templateid);
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

        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templateread', 'nopermissions', '');
        }

        // OK - all set.
        return template_competency::list_competencies($templateid);
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
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');
        }

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be added');
        }

        $exists = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
        if (!$exists) {
            $templatecompetency = new template_competency(0, $record);
            $templatecompetency->create();
            return true;
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
        if (!$template->can_manage()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templatemanage', 'nopermissions', '');
        }

        $record = new stdClass();
        $record->templateid = $templateid;
        $record->competencyid = $competencyid;

        $competency = new competency($competencyid);

        $exists = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyid));
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
        $matches = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = template_competency::get_records(array('templateid' => $templateid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = template_competency::get_records(array('templateid' => $templateid), 'sortorder', 'ASC', 0, 0);

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
     * Create a relation between a template and a cohort.
     *
     * This silently ignores when the relation already existed.
     *
     * @param  template|int $templateorid The template or its ID.
     * @param  stdClass|int $cohortid     The cohort ot its ID.
     * @return template_cohort
     */
    public static function create_template_cohort($templateorid, $cohortorid) {
        global $DB;

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }
        require_capability('tool/lp:templatemanage', $template->get_context());

        $cohort = $cohortorid;
        if (!is_object($cohort)) {
            $cohort = $DB->get_record('cohort', array('id' => $cohort), '*', MUST_EXIST);
        }

        // Check that the user can at least view this cohort.
        $cohortcontext = context::instance_by_id($cohort->contextid);
        if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:view'), $cohortcontext)) {
            throw new required_capability_exception($cohortcontext, 'moodle/cohort:view', 'nopermissions', '');
        }

        $tplcohort = template_cohort::get_relation($template->get_id(), $cohort->id);
        if (!$tplcohort->get_id()) {
            $tplcohort->create();
        }

        return $tplcohort;
    }

    /**
     * Remove a relation between a template and a cohort.
     *
     * @param  template|int $templateorid The template or its ID.
     * @param  stdClass|int $cohortid     The cohort ot its ID.
     * @return boolean True on success or when the relation did not exist.
     */
    public static function delete_template_cohort($templateorid, $cohortorid) {
        global $DB;

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }
        require_capability('tool/lp:templatemanage', $template->get_context());

        $cohort = $cohortorid;
        if (!is_object($cohort)) {
            $cohort = $DB->get_record('cohort', array('id' => $cohort), '*', MUST_EXIST);
        }

        $tplcohort = template_cohort::get_relation($template->get_id(), $cohort->id);
        if (!$tplcohort->get_id()) {
            return true;
        }

        return $tplcohort->delete();
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

        // Check that we can read something here.
        if (!plan::can_read_user($userid) && !plan::can_read_user_draft($userid)) {
            throw new required_capability_exception($context, 'tool/lp:planview', 'nopermissions', '');
        }

        // The user cannot view the drafts.
        if (!plan::can_read_user_draft($userid)) {
            $select .= ' AND status != :statusdraft';
            $params['statusdraft'] = plan::STATUS_DRAFT;
        }
        // The user cannot view the non-drafts.
        if (!plan::can_read_user($userid)) {
            $select .= ' AND status = :statusdraft';
            $params['statusdraft'] = plan::STATUS_DRAFT;
        }

        return plan::get_records_select($select, $params, 'name ASC');
    }

    /**
     * Creates a learning plan based on the provided data.
     *
     * @param stdClass $record
     * @return \tool_lp\plan
     */
    public static function create_plan(stdClass $record) {
        global $USER;
        $plan = new plan(0, $record);

        if ($plan->is_based_on_template()) {
            throw new coding_exception('To create a plan from a template use api::create_plan_from_template().');
        }

        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');
        }

        $plan->create();
        return $plan;
    }

    /**
     * Create a learning plan from a template.
     *
     * @param  mixed $templateorid The template object or ID.
     * @param  int $userid
     * @return false|\tool_lp\plan Returns false when the plan already exists.
     */
    public static function create_plan_from_template($templateorid, $userid) {
        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // The user must be able to view the template to use it as a base for a plan.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templateread', 'nopermissions', '');
        }
        // Can not create plan from a hidden template.
        if ($template->get_visible() == false) {
            throw new coding_exception('A plan can not be created from a hidden template');
        }

        // Convert the template to a plan.
        $record = $template->to_record();
        $record->templateid = $record->id;
        $record->userid = $userid;
        $record->name = $record->shortname;
        $record->status = plan::STATUS_ACTIVE;

        unset($record->id);
        unset($record->timecreated);
        unset($record->timemodified);
        unset($record->usermodified);

        // Remove extra keys.
        $properties = plan::properties_definition();
        foreach ($record as $key => $value) {
            if (!array_key_exists($key, $properties)) {
                unset($record->$key);
            }
        }

        $plan = new plan(0, $record);
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
        }

        // We first apply the permission checks as we wouldn't want to leak information by returning early that
        // the plan already exists.
        if (plan::record_exists_select('templateid = :templateid AND userid = :userid', array(
                'templateid' => $template->get_id(), 'userid' => $userid))) {
            return false;
        }

        $plan->create();
        return $plan;
    }

    /**
     * Create learning plans from a template and cohort.
     *
     * @param  mixed $templateorid The template object or ID.
     * @param  int $cohortid The cohort ID.
     * @param  bool $recreateunlinked When true the plans that were unlinked from this template will be re-created.
     * @return int The number of plans created.
     */
    public static function create_plans_from_template_cohort($templateorid, $cohortid, $recreateunlinked = false) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/cohort/lib.php');

        $template = $templateorid;
        if (!is_object($template)) {
            $template = new template($template);
        }

        // The user must be able to view the template to use it as a base for a plan.
        if (!$template->can_read()) {
            throw new required_capability_exception($template->get_context(), 'tool/lp:templateread', 'nopermissions', '');
        }

        // Can not create plan from a hidden template.
        if ($template->get_visible() == false) {
            throw new coding_exception('A plan can not be created from a hidden template');
        }

        // The user must be able to view the cohort.
        $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
        $cohortctx = context::instance_by_id($cohort->contextid);
        if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:view'), $cohortctx)) {
            throw new required_capability_exception($cohortctx, 'tool/lp:templateread', 'nopermissions', '');
        }

        // Convert the template to a plan.
        $recordbase = $template->to_record();
        $recordbase->templateid = $recordbase->id;
        $recordbase->name = $recordbase->shortname;
        $recordbase->status = plan::STATUS_ACTIVE;

        unset($recordbase->id);
        unset($recordbase->timecreated);
        unset($recordbase->timemodified);
        unset($recordbase->usermodified);

        // Remove extra keys.
        $properties = plan::properties_definition();
        foreach ($recordbase as $key => $value) {
            if (!array_key_exists($key, $properties)) {
                unset($recordbase->$key);
            }
        }

        // Create the plans.
        $created = 0;
        $userids = template_cohort::get_missing_plans($template->get_id(), $cohortid, $recreateunlinked);
        foreach ($userids as $userid) {
            $record = (object) (array) $recordbase;
            $record->userid = $userid;

            try {
                $plan = new plan(0, $record);
                if (!$plan->can_manage()) {
                    throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
                }
            } catch (required_capability_exception $e) {
                // Silently skip members where permissions are lacking.
                continue;
            }

            $plan->create();
            $created++;
        }

        return $created;
    }

    /**
     * Unlink a plan from its template.
     *
     * @param  \tool_lp\plan|int $planorid The plan or its ID.
     * @return bool
     */
    public static function unlink_plan_from_template($planorid) {
        global $DB;

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // The user must be allowed to manage the plans of the user, nothing about the template.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
        }

        // Early exit, it's already done...
        if (!$plan->is_based_on_template()) {
            return true;
        }

        // Fetch the template.
        $template = new template($plan->get_templateid());

        // Now, proceed by copying all competencies to the plan, then update the plan.
        $transaction = $DB->start_delegated_transaction();
        $competencies = template_competency::list_competencies($template->get_id(), false);
        $i = 0;
        foreach ($competencies as $competency) {
            $record = (object) array(
                'planid' => $plan->get_id(),
                'competencyid' => $competency->get_id(),
                'sortorder' => $i++
            );
            $pc = new plan_competency(null, $record);
            $pc->create();
        }
        $plan->set_origtemplateid($template->get_id());
        $plan->set_templateid(null);
        $success = $plan->update();
        $transaction->allow_commit();

        return $success;
    }

    /**
     * Updates a plan.
     *
     * @param stdClass $record
     * @return \tool_lp\plan
     */
    public static function update_plan(stdClass $record) {

        $plan = new plan($record->id);

        // Validate that the plan as it is can be managed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            // Prevent a plan based on a template to be edited.
            throw new coding_exception('Cannot update a plan that is based on a template.');

        } else if (isset($record->userid) && $plan->get_userid() != $record->userid) {
            // Prevent change of ownership as the capabilities are checked against that.
            throw new coding_exception('A plan cannot be transfered to another user');
        }

        $beforestatus = $plan->get_status();
        $beforetemplateid = $plan->get_templateid();
        $plan->from_record($record);

        if ($beforestatus == plan::STATUS_COMPLETE) {
            throw new coding_exception('Completed plan can not be edited');
        }

        if ($plan->get_status() == plan::STATUS_COMPLETE && $beforestatus != plan::STATUS_COMPLETE) {
            throw new coding_exception('Completing plan is not allowed in api::update_status (use complete_plan instead)');
        }

        if ($plan->get_status() == plan::STATUS_ACTIVE && $beforestatus == plan::STATUS_COMPLETE) {
            throw new coding_exception('Re-opening plan is not allowed in api::update_status (use reopen_plan instead)');
        }

        if ($beforetemplateid === null && $plan->get_templateid() !== null) {
            throw new coding_exception('A plan cannot be updated to use a template.');
        }

        // Revalidate after the data has be injected. This handles status change, etc...
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
        }

        $plan->update();

        return $plan;
    }

    /**
     * Returns a plan data.
     *
     * @param int $id
     * @return \tool_lp\plan
     */
    public static function read_plan($id) {
        $plan = new plan($id);

        if (!$plan->can_read()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planview', 'nopermissions', '');
        }

        return $plan;
    }

    /**
     * Deletes a plan.
     *
     * Plans based on a template can be removed just like any other one.
     *
     * @param int $id
     * @return bool Success?
     */
    public static function delete_plan($id) {
        global $DB;

        $plan = new plan($id);

        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');
        }

        // Wrap the suppression in a DB transaction.
        $transaction = $DB->start_delegated_transaction();

        // Delete archive user competencies if the status of the plan is complete.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            self::remove_archived_user_competencies_in_plan($plan);
        }

        $success = $plan->delete();

        $transaction->allow_commit();

        return $success;
    }

    /**
     * Complete a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function complete_plan($planorid) {
        global $DB;

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // Validate that the plan can be managed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
        }

        // Check if the plan was already completed.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('The plan is already completed.');
        }

        $originalstatus = $plan->get_status();
        $plan->set_status(plan::STATUS_COMPLETE);

        // The user should also be able to manage the plan when it's completed.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');
        }

        // Put back original status because archive needs it to extract competencies from the right table.
        $plan->set_status($originalstatus);

        // Do the things.
        $transaction = $DB->start_delegated_transaction();
        self::archive_user_competencies_in_plan($plan);
        $plan->set_status(plan::STATUS_COMPLETE);
        $success = $plan->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('The plan could not be updated.'));
            return $success;
        }

        $transaction->allow_commit();
        return $success;
    }

    /**
     * Reopen a plan.
     *
     * @param int|plan $planorid The plan, or its ID.
     * @return bool
     */
    public static function reopen_plan($planorid) {
        global $DB;

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        // Validate that the plan as it is can be managed.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');
        }

        $beforestatus = $plan->get_status();
        $plan->set_status(plan::STATUS_ACTIVE);

        // Validate if status can be changed.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');
        }

        // Wrap the updates in a DB transaction.
        $transaction = $DB->start_delegated_transaction();

        // Delete archived user competencies if the status of the plan is changed from complete to another status.
        $mustremovearchivedcompetencies = ($beforestatus == plan::STATUS_COMPLETE && $plan->get_status() != plan::STATUS_COMPLETE);
        if ($mustremovearchivedcompetencies) {
            self::remove_archived_user_competencies_in_plan($plan);
        }

        $success = $plan->update();

        if (!$success) {
            $transaction->rollback(new moodle_exception('The plan could not be updated.'));
            return $success;
        }

        $transaction->allow_commit();
        return $success;
    }

    /**
     * List the competencies in a user plan.
     *
     * @param  int $planorid The plan, or its ID.
     * @return array((object) array(
     *                            'competency' => \tool_lp\competency,
     *                            'usercompetency' => \tool_lp\user_competency
     *                            'usercompetencyplan' => \tool_lp\user_competency_plan
     *                        ))
     *         The values of of keys usercompetency and usercompetencyplan cannot be defined at the same time.
     */
    public static function list_plan_competencies($planorid) {
        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }

        if (!$plan->can_read()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planview', 'nopermissions', '');
        }

        $result = array();
        $competencies = $plan->get_competencies();

        // Get user competencies from user_competency_plan if the plan status is set to complete.
        $iscompletedplan = $plan->get_status() == plan::STATUS_COMPLETE;
        if ($iscompletedplan) {
            $usercompetencies = user_competency_plan::get_multiple($plan->get_userid(), $plan->get_id(), $competencies);
            $ucresultkey = 'usercompetencyplan';
        } else {
            $usercompetencies = user_competency::get_multiple($plan->get_userid(), $competencies);
            $ucresultkey = 'usercompetency';
        }

        // Build the return values.
        foreach ($competencies as $key => $competency) {
            $found = false;

            foreach ($usercompetencies as $uckey => $uc) {
                if ($uc->get_competencyid() == $competency->get_id()) {
                    $found = true;
                    unset($usercompetencies[$uckey]);
                    break;
                }
            }

            if (!$found) {
                if ($iscompletedplan) {
                    throw new coding_exception('A user competency plan is missing');
                } else {
                    $uc = user_competency::create_relation($plan->get_userid(), $competency->get_id());
                }
            }

            $plancompetency = (object) array(
                'competency' => $competency,
                'usercompetency' => null,
                'usercompetencyplan' => null
            );
            $plancompetency->$ucresultkey = $uc;
            $result[] = $plancompetency;
        }

        return $result;
    }

    /**
     * Add a competency to a plan.
     *
     * @param int $planid The id of the plan
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function add_competency_to_plan($planid, $competencyid) {
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            throw new required_capability_exception($plan->get_context(), 'tool/lp:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be added to a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be added to a learning plan completed');
        }

        $competency = new competency($competencyid);

        // Can not add a competency that belong to a hidden framework.
        if ($competency->get_framework()->get_visible() == false) {
            throw new coding_exception('A competency belonging to hidden framework can not be added');
        }

        $exists = plan_competency::get_record(array('planid' => $planid, 'competencyid' => $competencyid));
        if (!$exists) {
            $record = new stdClass();
            $record->planid = $planid;
            $record->competencyid = $competencyid;
            $plancompetency = new plan_competency(0, $record);
            $plancompetency->create();
        }

        return true;
    }

    /**
     * Remove a competency from a plan.
     *
     * @param int $planid The plan id
     * @param int $competencyid The id of the competency
     * @return bool
     */
    public static function remove_competency_from_plan($planid, $competencyid) {
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be removed from a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be removed from a learning plan completed');
        }

        $link = plan_competency::get_record(array('planid' => $planid, 'competencyid' => $competencyid));
        if ($link) {
            return $link->delete();
        }
        return false;
    }

    /**
     * Move the plan competency up or down in the display list.
     *
     * Requires tool/lp:planmanage capability at the system context.
     *
     * @param int $planid The plan  id
     * @param int $competencyidfrom The id of the competency we are moving.
     * @param int $competencyidto The id of the competency we are moving to.
     * @return boolean
     */
    public static function reorder_plan_competency($planid, $competencyidfrom, $competencyidto) {
        $plan = new plan($planid);

        // First we do a permissions check.
        if (!$plan->can_manage()) {
            $context = context_user::instance($plan->get_userid());
            throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');

        } else if ($plan->is_based_on_template()) {
            throw new coding_exception('A competency can not be reordered in a learning plan based on a template');
        }

        if (!$plan->can_be_edited()) {
            throw new coding_exception('A competency can not be reordered in a learning plan completed');
        }

        $down = true;
        $matches = plan_competency::get_records(array('planid' => $planid, 'competencyid' => $competencyidfrom));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyfrom = array_pop($matches);
        $matches = plan_competency::get_records(array('planid' => $planid, 'competencyid' => $competencyidto));
        if (count($matches) == 0) {
            throw new coding_exception('The link does not exist');
        }

        $competencyto = array_pop($matches);

        $all = plan_competency::get_records(array('planid' => $planid), 'sortorder', 'ASC', 0, 0);

        if ($competencyfrom->get_sortorder() > $competencyto->get_sortorder()) {
            // We are moving up, so put it before the "to" item.
            $down = false;
        }

        foreach ($all as $id => $plancompetency) {
            $sort = $plancompetency->get_sortorder();
            if ($down && $sort > $competencyfrom->get_sortorder() && $sort <= $competencyto->get_sortorder()) {
                $plancompetency->set_sortorder($plancompetency->get_sortorder() - 1);
                $plancompetency->update();
            } else if (!$down && $sort >= $competencyto->get_sortorder() && $sort < $competencyfrom->get_sortorder()) {
                $plancompetency->set_sortorder($plancompetency->get_sortorder() + 1);
                $plancompetency->update();
            }
        }
        $competencyfrom->set_sortorder($competencyto->get_sortorder());
        return $competencyfrom->update();
    }

    /**
     * List all the related competencies.
     *
     * @param int $competencyid The id of the competency to check.
     * @return competency[]
     */
    public static function list_related_competencies($competencyid) {
        $competency = new competency($competencyid);

        require_capability('tool/lp:competencymanage', $competency->get_context());

        return $competency->get_related_competencies();
    }

    /**
     * Add a related competency.
     *
     * @param int $competencyid The id of the competency
     * @param int $relatedcompetencyid The id of the related competency.
     * @return bool False when create failed, true on success, or if the relation already existed.
     */
    public static function add_related_competency($competencyid, $relatedcompetencyid) {
        $competency1 = new competency($competencyid);
        $competency2 = new competency($relatedcompetencyid);

        require_capability('tool/lp:competencymanage', $competency1->get_context());

        $relatedcompetency = related_competency::get_relation($competency1->get_id(), $competency2->get_id());
        if (!$relatedcompetency->get_id()) {
            $relatedcompetency->create();
            return true;
        }

        return true;
    }

    /**
     * Remove a related competency.
     *
     * @param int $competencyid The id of the competency.
     * @param int $relatedcompetencyid The id of the related competency.
     * @return bool True when it was deleted, false when it wasn't or the relation doesn't exist.
     */
    public static function remove_related_competency($competencyid, $relatedcompetencyid) {
        $competency = new competency($competencyid);

        // This only check if we have the permission in either competency because both competencies
        // should belong to the same framework.
        require_capability('tool/lp:competencymanage', $competency->get_context());

        $relatedcompetency = related_competency::get_relation($competencyid, $relatedcompetencyid);
        if ($relatedcompetency->get_id()) {
            return $relatedcompetency->delete();
        }

        return false;
    }

    /**
     * Read a user evidence.
     *
     * @param int $id
     * @return user_evidence
     */
    public static function read_user_evidence($id) {
        $userevidence = new user_evidence($id);

        if (!$userevidence->can_read()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'tool/lp:userevidenceread', 'nopermissions', '');
        }

        return $userevidence;
    }

    /**
     * Create a new user evidence.
     *
     * @param  object $data        The data.
     * @param  int    $draftitemid The draft ID in which files have been saved.
     * @return user_evidence
     */
    public static function create_user_evidence($data, $draftitemid = null) {
        $userevidence = new user_evidence(null, $data);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
        }

        $userevidence->create();
        if (!empty($draftitemid)) {
            $fileareaoptions = array('subdirs' => true);
            $itemid = $userevidence->get_id();
            file_save_draft_area_files($draftitemid, $context->id, 'tool_lp', 'userevidence', $itemid, $fileareaoptions);
        }

        return $userevidence;
    }

    /**
     * Create a new user evidence.
     *
     * @param  object $data        The data.
     * @param  int    $draftitemid The draft ID in which files have been saved.
     * @return user_evidence
     */
    public static function update_user_evidence($data, $draftitemid = null) {
        $userevidence = new user_evidence($data->id);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');

        } else if (array_key_exists('userid', $data) && $data->userid != $userevidence->get_userid()) {
            throw new coding_exception('Can not change the userid of a user evidence.');
        }

        $userevidence->from_record($data);
        $userevidence->update();

        if (!empty($draftitemid)) {
            $fileareaoptions = array('subdirs' => true);
            $itemid = $userevidence->get_id();
            file_save_draft_area_files($draftitemid, $context->id, 'tool_lp', 'userevidence', $itemid, $fileareaoptions);
        }

        return $userevidence;
    }

    /**
     * Delete a user evidence.
     *
     * @param  int $id The user evidence ID.
     * @return bool
     */
    public static function delete_user_evidence($id) {
        $userevidence = new user_evidence($id);
        $context = $userevidence->get_context();

        if (!$userevidence->can_manage()) {
            throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
        }

        // Delete the user evidence.
        $userevidence->delete();

        // Delete associated files.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'tool_lp', 'userevidence', $id);

        // Delete relation between evidence and competencies.
        $userevidence->set_id($id);     // Restore the ID to fully mock the object.
        $competencies = user_evidence_competency::get_competencies_by_userevidenceid($id);
        foreach ($competencies as $competency) {
            static::delete_user_evidence_competency($userevidence, $competency->get_id());
        }
        $userevidence->set_id(0);       // Restore the object.

        return true;
    }

    /**
     * List the user evidence of a user.
     *
     * @param  int $userid The user ID.
     * @return user_evidence[]
     */
    public static function list_user_evidence($userid) {
        if (!user_evidence::can_read_user($userid)) {
            $context = context_user::instance($userid);
            throw new required_capability_exception($context, 'tool/lp:userevidenceread', 'nopermissions', '');
        }

        $evidence = user_evidence::get_records(array('userid' => $userid), 'name');
        return $evidence;
    }

    /**
     * Link a user evidence with a competency.
     *
     * @param  user_evidence|int $userevidenceorid User evidence or its ID.
     * @param  int $competencyid Competency ID.
     * @return user_evidence_competency
     */
    public static function create_user_evidence_competency($userevidenceorid, $competencyid) {
        global $USER;

        $userevidence = $userevidenceorid;
        if (!is_object($userevidence)) {
            $userevidence = self::read_user_evidence($userevidence);
        }

        // Perform user evidence capability checks.
        if (!$userevidence->can_manage()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
        }

        // Perform competency capability checks.
        $competency = self::read_competency($competencyid);

        // Get (and create) the relation.
        $relation = user_evidence_competency::get_relation($userevidence->get_id(), $competency->get_id());
        if (!$relation->get_id()) {
            $relation->create();

            $link = new moodle_url('/admin/tool/lp/user_evidence.php', array('id' => $userevidence->get_id()));
            self::add_evidence(
                $userevidence->get_userid(),
                $competency,
                $userevidence->get_context(),
                evidence::ACTION_LOG,
                'evidence_evidenceofpriorlearninglinked',
                'tool_lp',
                $userevidence->get_name(),
                false,
                $link->out(false),
                null,
                $USER->id
            );
        }

        return $relation;
    }

    /**
     * Delete a relationship between a user evidence and a competency.
     *
     * @param  user_evidence|int $userevidenceorid User evidence or its ID.
     * @param  int $competencyid Competency ID.
     * @return bool
     */
    public static function delete_user_evidence_competency($userevidenceorid, $competencyid) {
        global $USER;

        $userevidence = $userevidenceorid;
        if (!is_object($userevidence)) {
            $userevidence = self::read_user_evidence($userevidence);
        }

        // Perform user evidence capability checks.
        if (!$userevidence->can_manage()) {
            $context = $userevidence->get_context();
            throw new required_capability_exception($context, 'tool/lp:userevidencemanage', 'nopermissions', '');
        }

        // Get (and delete) the relation.
        $relation = user_evidence_competency::get_relation($userevidence->get_id(), $competencyid);
        if (!$relation->get_id()) {
            return true;
        }

        $success = $relation->delete();
        if ($success) {
            self::add_evidence(
                $userevidence->get_userid(),
                $competencyid,
                $userevidence->get_context(),
                evidence::ACTION_LOG,
                'evidence_evidenceofpriorlearningunlinked',
                'tool_lp',
                $userevidence->get_name(),
                false,
                null,
                null,
                $USER->id
            );
        }

        return $success;
    }

    /**
     * Recursively duplicate competencies from a tree, we start duplicating from parents to children to have a correct path.
     * This method does not copy the related competencies.
     *
     * @param int $frameworkid - framework id
     * @param competency[] $tree - array of competencies object
     * @param int $oldparent - old parent id
     * @param int $newparent - new parent id
     * @return array $matchids - List of old competencies ids matched with new ids.
     */
    protected static function duplicate_competency_tree($frameworkid, $tree, $oldparent = 0, $newparent = 0) {
        $matchids = array();
        foreach ($tree as $node) {
            if ($node->competency->get_parentid() == $oldparent) {
                $parentid = $node->competency->get_id();

                // Create the competency.
                $competency = $node->competency;
                $competency->set_competencyframeworkid($frameworkid);
                $competency->set_parentid($newparent);
                $competency->set_path('');
                $competency->set_id(0);
                $competency->create();

                // Match the old id with the new one.
                $matchids[$parentid] = $competency->get_id();

                if (!empty($node->children)) {
                    // Duplicate children competency.
                    $childrenids = self::duplicate_competency_tree($frameworkid, $node->children, $parentid, $competency->get_id());
                    // Array_merge does not keep keys when merging so we use the + operator.
                    $matchids = $matchids + $childrenids;
                }
            }
        }
        return $matchids;
    }

    /**
     * Archive user competencies in a plan.
     *
     * @param int $plan The plan object.
     * @return void
     */
    protected static function archive_user_competencies_in_plan($plan) {

        // Check if the plan was already completed.
        if ($plan->get_status() == plan::STATUS_COMPLETE) {
            throw new coding_exception('The plan is already completed.');
        }

        $competencies = $plan->get_competencies();
        $usercompetencies = user_competency::get_multiple($plan->get_userid(), $competencies);

        foreach ($competencies as $competency) {
            $found = false;

            foreach ($usercompetencies as $uckey => $uc) {
                if ($uc->get_competencyid() == $competency->get_id()) {
                    $found = true;

                    $ucprecord = $uc->to_record();
                    $ucprecord->planid = $plan->get_id();
                    unset($ucprecord->id);
                    unset($ucprecord->status);
                    unset($ucprecord->reviewerid);

                    $usercompetencyplan = new user_competency_plan(0, $ucprecord);
                    $usercompetencyplan->create();

                    unset($usercompetencies[$uckey]);
                    break;
                }
            }

            // If the user competency doesn't exist, we create a new relation in user_competency_plan.
            if (!$found) {
                $usercompetencyplan = user_competency_plan::create_relation($plan->get_userid(), $competency->get_id(),
                        $plan->get_id());
                $usercompetencyplan->create();
            }
        }
    }

    /**
     * Delete archived user competencies in a plan.
     *
     * @param int $plan The plan object.
     * @return void
     */
    protected static function remove_archived_user_competencies_in_plan($plan) {
        $competencies = $plan->get_competencies();
        $usercompetenciesplan = user_competency_plan::get_multiple($plan->get_userid(), $plan->get_id(), $competencies);

        foreach ($usercompetenciesplan as $ucpkey => $ucp) {
            $ucp->delete();
        }
    }

    /**
     * List all the evidence for a user competency
     *
     * @param int $userid The user id - only used if usercompetencyid is 0.
     * @param int $competencyid The competency id - only used it usercompetencyid is 0.
     * @param int $planid The plan id - not used yet - but can be used to only list archived evidence if a plan is completed.
     * @return array of \tool_lp\evidence
     */
    public static function list_evidence($userid = 0,
                                         $competencyid = 0,
                                         $planid = 0,
                                         $sort = 'timecreated',
                                         $order = 'DESC',
                                         $skip = 0,
                                         $limit = 0) {

        $context = context_user::instance($userid);
        require_capability('tool/lp:planview', $context);

        // TODO - handle archived plans.

        $usercompetency = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$usercompetency) {
            return array();
        }

        return evidence::get_records(array('usercompetencyid' => $usercompetency->get_id()), $sort, $order, $skip, $limit);
    }

    /**
     * Create an evidence from a list of parameters.
     *
     * Requires no capability because evidence can be added in many situations under any user.
     *
     * @param int $userid The user id for which evidence is added.
     * @param int $competencyid The competency, or its id for which evidence is added.
     * @param context|int $contextorid The context in which the evidence took place.
     * @param int $action The type of action to take on the competency. \tool_lp\evidence::ACTION_*.
     * @param string $descidentifier The strings identifier.
     * @param string $desccomponent The strings component.
     * @param mixed $desca Any arguments the string requires.
     * @param bool $recommend When true, the user competency will be sent for review.
     * @param string $url The url the evidence may link to.
     * @param int $grade The grade, or scale ID item.
     * @param int $actionuserid The ID of the user who took the action of adding the evidence. Null when system.
     *                          This should be used when the action was taken by a real person, this will allow
     *                          to keep track of all the evidence given by a certain person.
     * @return evidence
     */
    public static function add_evidence($userid,
                                        $competencyorid,
                                        $contextorid,
                                        $action,
                                        $descidentifier,
                                        $desccomponent,
                                        $desca = null,
                                        $recommend = false,
                                        $url = null,
                                        $grade = null,
                                        $actionuserid = null) {
        global $DB;

        // Some clearly important variable assignments right there.
        $competencyid = $competencyorid;
        $competency = null;
        if (is_object($competencyid)) {
            $competency = $competencyid;
            $competencyid = $competency->get_id();
        }
        $contextid = is_object($contextorid) ? $contextorid->id : $contextorid;
        $setucgrade = false;
        $ucgrade = null;
        $ucproficiency = null;

        // Fetch or create the user competency.
        $usercompetency = user_competency::get_record(array('userid' => $userid, 'competencyid' => $competencyid));
        if (!$usercompetency) {
            $usercompetency = user_competency::create_relation($userid, $competencyid);
            $usercompetency->create();
        }

        // What should we be doing?
        switch ($action) {

            // Completing a competency.
            case evidence::ACTION_COMPLETE:
                if ($grade !== null) {
                    throw new coding_exception("The grade MUST NOT be set with a 'completing' evidence.");
                }

                // Fetch the default grade to attach to the evidence.
                if (empty($competency)) {
                    $competency = new competency($competencyid);
                }
                list($grade, $proficiency) = $competency->get_default_grade();

                // When completing the competency we fetch the default grade from the competency. But we only mark
                // the user competency when a grade has not been set yet. Complete is an action to use with automated systems.
                if ($usercompetency->get_grade() === null) {
                    $setucgrade = true;
                    $ucgrade = $grade;
                    $ucproficiency = $proficiency;
                }

                break;

            // We override the grade, even overriding back to not set.
            case evidence::ACTION_OVERRIDE:
                $setucgrade = true;
                $ucgrade = $grade;
                if (empty($competency)) {
                    $competency = new competency($competencyid);
                }
                if ($ucgrade !== null) {
                    $ucproficiency = $competency->get_proficiency_of_grade($ucgrade);
                }
                break;

            // Suggesting a grade with an evidence.
            case evidence::ACTION_SUGGEST:
                if ($grade === null) {
                    throw new coding_exception("The grade MUST be set when 'suggesting' an evidence. Or use ACTION_LOG instead.");
                }
                break;

            // Simply logging an evidence.
            case evidence::ACTION_LOG:
                if ($grade !== null) {
                    throw new coding_exception("The grade MUST NOT be set when 'logging' an evidence. Or use ACTION_SUGGEST instead.");
                }
                break;

            // Whoops, this is not expected.
            default:
                throw new coding_exception('Unexpected action parameter when registering an evidence.');
                break;
        }

        // Should we recommend?
        if ($recommend && $usercompetency->get_status() == user_competency::STATUS_IDLE) {
            $usercompetency->set_status(user_competency::STATUS_WAITING_FOR_REVIEW);
        }

        // Setting the grade and proficiency for the user competency.
        $wascompleted = false;
        if ($setucgrade == true) {
            if (!$usercompetency->get_proficiency() && $ucproficiency) {
                $wascompleted = true;
            }
            $usercompetency->set_grade($ucgrade);
            $usercompetency->set_proficiency($ucproficiency);
        }

        // Prepare the evidence.
        $record = new stdClass();
        $record->usercompetencyid = $usercompetency->get_id();
        $record->contextid = $contextid;
        $record->action = $action;
        $record->descidentifier = $descidentifier;
        $record->desccomponent = $desccomponent;
        $record->grade = $grade;
        $record->actionuserid = $actionuserid;
        $evidence = new evidence(0, $record);
        $evidence->set_desca($desca);
        $evidence->set_url($url);

        // Validate both models, we should not operate on one if the other will not save.
        if (!$usercompetency->is_valid()) {
            throw new invalid_persistent_exception($usercompetency->get_errors());
        } else if (!$evidence->is_valid()) {
            throw new invalid_persistent_exception($evidence->get_errors());
        }

        // Finally save. Pheww!
        $usercompetency->update();
        $evidence->create();

        // The competency was marked as completed, apply the rules.
        if ($wascompleted) {
            self::apply_competency_rules_from_usercompetency($usercompetency, $competency);
        }

        return $evidence;
    }

    /**
     * Apply the competency rules from a user competency.
     *
     * The user competency passed should be one that was recently marked as complete.
     * A user competency is considered 'complete' when it's proficiency value is true.
     *
     * This method will check if the parent of this usercompetency's competency has any
     * rules and if so will see if they match. When matched it will take the required
     * step to add evidence and trigger completion, etc...
     *
     * @param  usercompetency  $usercompetency The user competency recently completed.
     * @param  competency|null $competency     The competency of the user competency, useful to avoid unnecessary read.
     * @return void
     */
    protected static function apply_competency_rules_from_usercompetency(user_competency $usercompetency,
            competency $competency = null) {

        // Perform some basic checks.
        if (!$usercompetency->get_proficiency()) {
            throw new coding_exception('The user competency passed is not completed.');
        }
        if ($competency === null) {
            $competency = $usercompetency->get_competency();
        }
        if ($competency->get_id() != $usercompetency->get_competencyid()) {
            throw new coding_exception('Mismatch between user competency and competency.');
        }

        // Fetch the parent.
        $parent = $competency->get_parent();
        if ($parent === null) {
            return;
        }

        // The parent should have a rule, and a meaningful outcome.
        $ruleoutcome = $parent->get_ruleoutcome();
        if ($ruleoutcome == competency::OUTCOME_NONE) {
            return;
        }
        $rule = $parent->get_rule_object();
        if ($rule === null) {
            return;
        }

        // Fetch or create the user competency for the parent.
        $userid = $usercompetency->get_userid();
        $parentuc = user_competency::get_record(array('userid' => $userid, 'competencyid' => $parent->get_id()));
        if (!$parentuc) {
            $parentuc = user_competency::create_relation($userid, $parent->get_id());
            $parentuc->create();
        }

        // Does the rule match?
        if (!$rule->matches($parentuc)) {
            return;
        }

        // Figuring out what to do.
        $recommend = false;
        if ($ruleoutcome == competency::OUTCOME_EVIDENCE) {
            $action = evidence::ACTION_LOG;

        } else if ($ruleoutcome == competency::OUTCOME_RECOMMEND) {
            $action = evidence::ACTION_LOG;
            $recommend = true;

        } else if ($ruleoutcome == competency::OUTCOME_COMPLETE) {
            $action = evidence::ACTION_COMPLETE;

        } else {
            throw new moodle_exception('Unexpected rule outcome: ' + $ruleoutcome);
        }

        // Finally add an evidence.
        static::add_evidence(
            $userid,
            $parent,
            $parent->get_context()->id,
            $action,
            'evidence_competencyrule',
            'tool_lp',
            null,
            $recommend
        );
    }

    /**
     * Observe when a course is marked as completed.
     *
     * Note that the user being logged in while this happens may be anyone.
     * Do not rely on capability checks here!
     *
     * @param  \core\event\course_completed $event
     * @return void
     */
    public static function observe_course_completed(\core\event\course_completed $event) {
        $sql = 'courseid = :courseid AND ruleoutcome != :nooutcome';
        $params = array(
            'courseid' => $event->courseid,
            'nooutcome' => course_competency::OUTCOME_NONE
        );
        $coursecompetencies = course_competency::get_records_select($sql, $params);

        $course = get_course($event->courseid);
        $courseshortname = format_string($course->shortname, null, array('context' => $event->contextid));

        foreach ($coursecompetencies as $coursecompetency) {

            $outcome = $coursecompetency->get_ruleoutcome();
            $action = null;
            $recommend = false;
            $strdesc = 'evidence_coursecompleted';

            if ($outcome == course_competency::OUTCOME_EVIDENCE) {
                $action = evidence::ACTION_LOG;

            } else if ($outcome == course_competency::OUTCOME_RECOMMEND) {
                $action = evidence::ACTION_LOG;
                $recommend = true;

            } else if ($outcome == course_competency::OUTCOME_COMPLETE) {
                $action = evidence::ACTION_COMPLETE;

            } else {
                throw new moodle_exception('Unexpected rule outcome: ' + $outcome);
            }

            static::add_evidence(
                $event->relateduserid,
                $coursecompetency->get_competencyid(),
                $event->contextid,
                $action,
                $strdesc,
                'tool_lp',
                $courseshortname,
                $recommend,
                $event->get_url()
            );
        }
    }

    /**
     * Manually grade a user competency from the plans page.
     *
     * @param mixed $planorid
     * @param int $competencyid
     * @param int $grade
     * @param boolean $override
     * @return array of \tool_lp\user_competency
     */
    public static function grade_competency_in_plan($planorid, $competencyid, $grade, $override) {
        global $USER;

        $plan = $planorid;
        if (!is_object($planorid)) {
            $plan = new plan($planorid);
        }
        $context = $plan->get_context();
        if ($override) {
            require_capability('tool/lp:competencygrade', $context);
        } else {
            require_capability('tool/lp:competencysuggestgrade', $context);
        }

        // Verify the data.


        $userplancompetencies = self::list_plan_competencies($plan);
        $competency = null;

        foreach ($userplancompetencies as $userplancompetency) {
            if ($userplancompetency->competency->get_id() == $competencyid) {
                $competency = $userplancompetency->competency;
            }
        }
        if (!$competency) {
            throw new coding_exception('The competency does not belong to this plan: ' . $competencyid . ', ' . $planid);
        }

        $action = evidence::ACTION_OVERRIDE;
        $desckey = 'evidence_manualoverrideinplan';
        if (!$override) {
            $action = evidence::ACTION_SUGGEST;
            $desckey = 'evidence_manualsuggestinplan';
        }

        return self::add_evidence($plan->get_userid(),
                                  $competency,
                                  $context->id,
                                  $action,
                                  $desckey,
                                  'tool_lp',
                                  $plan->get_name(),
                                  false,
                                  null,
                                  $grade,
                                  $USER->id);
    }
}
