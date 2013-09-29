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
 * Course and category management helper class.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\management;

defined('MOODLE_INTERNAL') || die;

/**
 * Course and category management interface helper class.
 *
 * This class provides methods useful to the course and category management interfaces.
 * Many of the methods on this class are static and serve one of two purposes.
 *  1.  encapsulate functionality in an effort to ensure minimal changes between the different
 *      methods of interaction. Specifically browser, AJAX and webservice.
 *  2.  abstract logic for acquiring actions away from output so that renderers may use them without
 *      having to include any logic or capability checks.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Returns course details in an array ready to be printed.
     *
     * @global \moodle_database $DB
     * @param \course_in_list $course
     * @return array
     */
    public static function get_course_detail_array(\course_in_list $course) {
        global $DB;
        $names = \role_get_names($course->get_context());
        $sql = 'SELECT ra.roleid, COUNT(ra.id) AS rolecount
                  FROM {role_assignments} ra
                 WHERE ra.contextid = :contextid
              GROUP BY ra.roleid';
        $rolecounts = $DB->get_records_sql($sql, array('contextid' => $course->get_context()->id));
        $roledetails = array();
        foreach ($rolecounts as $result) {
            $a = new \stdClass;
            $a->role = $names[$result->roleid]->localname;
            $a->count = $result->rolecount;
            $roledetails[] = \get_string('assignedrolecount', 'moodle', $a);
        }

        $groups = \groups_get_course_data($course->id);

        $enrolmentlines = array();
        $instances = \enrol_get_instances($course->id, true);
        $plugins = \enrol_get_plugins(true);
        foreach ($instances as $instance) {
            if (!isset($plugins[$instance->enrol])) {
                // Weird.
                continue;
            }
            $plugin = $plugins[$instance->enrol];
            $enrolmentlines[] = $plugin->get_instance_name($instance);
        }

        $format = \course_get_format($course->id);
        $modinfo = \get_fast_modinfo($course->id);
        $modules = $modinfo->get_used_module_names();
        $sections = array();
        if ($format->uses_sections()) {
            foreach ($modinfo->get_section_info_all() as $section) {
                $sections[] = $format->get_section_name($section);
            }
        }

        $category = \coursecat::get($course->category);
        $categoryurl = new \moodle_url('/course/management.php', array('categoryid' => $course->category));
        $categoryname = $category->get_formatted_name();

        $details = array(
            'format' => array(
                'key' => \get_string('format'),
                'value' => \course_get_format($course)->get_format_name()
            ),
            'fullname' => array(
                'key' => \get_string('fullname'),
                'value' => $course->get_formatted_fullname()
            ),
            'shortname' => array(
                'key' => \get_string('shortname'),
                'value' => $course->get_formatted_shortname()
            ),
            'idnumber' => array(
                'key' => \get_string('idnumber'),
                'value' => s($course->idnumber)
            ),
            'category' => array(
                'key' => \get_string('category'),
                'value' => \html_writer::link($categoryurl, $categoryname)
            ),
            'groupings' => array(
                'key' => \get_string('groupings', 'group'),
                'value' => count($groups->groupings)
            ),
            'groups' => array(
                'key' => \get_string('groups'),
                'value' => count($groups->groups)
            ),
            'roleassignments' => array(
                'key' => \get_string('roleassignments'),
                'value' => join('<br />', $roledetails)
            ),
            'enrolmentmethods' => array(
                'key' => \get_string('enrolmentmethods'),
                'value' => join('<br />', $enrolmentlines)
            ),
            'sections' => array(
                'key' => \get_string('sections'),
                'value' => join('<br />', $sections)
            ),
            'modulesused' => array(
                'key' => \get_string('modulesused'),
                'value' => join('<br />', $modules)
            )
        );

        return $details;
    }

    /**
     * Returns an array of actions that can be performed upon a category being shown in a list.
     *
     * @param \coursecat $category
     * @return array
     */
    public static function get_category_listitem_actions(\coursecat $category) {
        $baseurl = new \moodle_url('/course/management.php', array('categoryid' => $category->id, 'sesskey' => \sesskey()));
        $actions = array();
        // Edit.
        if ($category->can_edit()) {
            $actions['edit'] = array(
                'url' => new \moodle_url('/course/editcategory.php', array('id' => $category->id)),
                'icon' => new \pix_icon('t/edit', new \lang_string('edit')),
                'string' => new \lang_string('edit')
            );
        }

        // Show/Hide.
        if ($category->can_change_visibility()) {
            // We always show both icons and then just toggle the display of the invalid option with CSS.
            $actions['hide'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'hidecategory')),
                'icon' => new \pix_icon('t/hide', new \lang_string('hide')),
                'string' => new \lang_string('hide')
            );
            $actions['show'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'showcategory')),
                'icon' => new \pix_icon('t/show', new \lang_string('show')),
                'string' => new \lang_string('show')
            );
        }

        // Move up/down.
        if ($category->can_resort()) {
            $actions['moveup'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'movecategoryup')),
                'icon' => new \pix_icon('t/up', new \lang_string('up')),
                'string' => new \lang_string('up')
            );
            $actions['movedown'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'movecategorydown')),
                'icon' => new \pix_icon('t/down', new \lang_string('down')),
                'string' => new \lang_string('down')
            );
        }

        // Delete.
        if ($category->can_delete_full()) {
            $actions['delete'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'deletecategory')),
                'icon' => new \pix_icon('t/delete', new \lang_string('delete')),
                'string' => new \lang_string('delete')
            );
        }

        // Roles.
        if ($category->can_review_roles()) {
            $actions['assignroles'] = array(
                'url' => new \moodle_url('/admin/roles/assign.php', array('contextid' => $category->get_context()->id,
                    'return' => 'management')),
                'icon' => new \pix_icon('i/assignroles', new \lang_string('assignroles', 'role')),
                'string' => new \lang_string('assignroles', 'role')
            );
        }

        // Permissions.
        if ($category->can_review_permissions()) {
            $actions['permissions'] = array(
                'url' => new \moodle_url('/admin/roles/permissions.php', array('contextid' => $category->get_context()->id,
                    'return' => 'management')),
                'icon' => new \pix_icon('i/permissions', new \lang_string('permissions', 'role')),
                'string' => new \lang_string('permissions', 'role')
            );
        }

        // Cohorts.
        if ($category->can_review_cohorts()) {
            $actions['cohorts'] = array(
                'url' => new \moodle_url('/cohort/index.php', array('contextid' => $category->get_context()->id)),
                'icon' => new \pix_icon('i/cohort', new \lang_string('cohorts', 'cohort')),
                'string' => new \lang_string('cohorts', 'cohort')
            );
        }

        // Filters.
        if ($category->can_review_filters()) {
            $actions['filters'] = array(
                'url' => new \moodle_url('/filter/manage.php', array('contextid' => $category->get_context()->id,
                    'return' => 'management')),
                'icon' => new \pix_icon('i/filter', new \lang_string('filters', 'admin')),
                'string' => new \lang_string('filters', 'admin')
            );
        }

        return $actions;
    }

    /**
     * Returns an array of actions that can be performed on the course being displayed.
     *
     * @param \course_in_list $course
     * @return array
     */
    public static function get_course_detail_actions(\course_in_list $course) {
        $params = array('courseid' => $course->id, 'categoryid' => $course->category, 'sesskey' => \sesskey());
        $baseurl = new \moodle_url('/course/management.php', $params);
        $actions = array();
        // View.
        if ($course->can_access()) {
            $actions['view'] = array(
                'url' => new \moodle_url('/course/view.php', array('id' => $course->id)),
                'string' => \get_string('view')
            );
        }
        // Edit.
        if ($course->can_edit()) {
            $actions['edit'] = array(
                'url' => new \moodle_url('/course/edit.php', array('id' => $course->id)),
                'string' => \get_string('edit')
            );
        }
        // Permissions.
        if ($course->can_review_enrolments()) {
            $actions['enrolledusers'] = array(
                'url' => new \moodle_url('/enrol/users.php', array('id' => $course->id)),
                'string' => \get_string('enrolledusers', 'enrol')
            );
        }
        // Delete.
        if ($course->can_delete()) {
            $actions['delete'] = array(
                'url' => new \moodle_url('/course/delete.php', array('id' => $course->id)),
                'string' => \get_string('delete')
            );
        }
        // Show/Hide.
        if ($course->can_change_visibility()) {
            if ($course->visible) {
                $actions['hide'] = array(
                    'url' => new \moodle_url($baseurl, array('action' => 'hidecourse')),
                    'string' => \get_string('hide')
                );
            } else {
                $actions['show'] = array(
                    'url' => new \moodle_url($baseurl, array('action' => 'showcourse')),
                    'string' => \get_string('show')
                );
            }
        }
        // Backup.
        if ($course->can_backup()) {
            $actions['backup'] = array(
                'url' => new \moodle_url('/backup/backup.php', array('id' => $course->id)),
                'string' => \get_string('backup')
            );
        }
        // Restore.
        if ($course->can_restore()) {
            $actions['restore'] = array(
                'url' => new \moodle_url('/backup/restorefile.php', array('contextid' => $course->get_context()->id)),
                'string' => \get_string('restore')
            );
        }
        return $actions;
    }

    /**
     * Moves the provided course up one place in the sort order given a \course_in_list object.
     *
     * @param \course_in_list $course
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_moveup(\course_in_list $course, \coursecat $category) {
        if (!$category->can_resort()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return course_move_by_one($course, true);
    }

    /**
     * Moves the provided course down one place in the sort order given a \course_in_list object.
     *
     * @param \course_in_list $course
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_movedown(\course_in_list $course, \coursecat $category) {
        if (!$category->can_resort()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return course_move_by_one($course, false);
    }

    /**
     * Moves the provided course up one place in the sort order given an id or database record.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_moveup_by_record($courserecordorid) {
        global $DB;
        if (is_int($courserecordorid)) {
            $courserecordorid = $DB->get_record('course', array('id' => $courserecordorid), '*', MUST_EXIST);
        }
        $course = new \course_in_list($courserecordorid);
        $category = \coursecat::get($course->category);
        return self::action_course_moveup($course, $category);
    }

    /**
     * Moves the provided course down one place in the sort order given an id or database record.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_movedown_by_record($courserecordorid) {
        global $DB;
        if (is_int($courserecordorid)) {
            $courserecordorid = $DB->get_record('course', array('id' => $courserecordorid), '*', MUST_EXIST);
        }
        $course = new \course_in_list($courserecordorid);
        $category = \coursecat::get($course->category);
        return self::action_course_movedown($course, $category);
    }

    /**
     * Makes a course visible given a \course_in_list object.
     *
     * @param \course_in_list $course
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_show(\course_in_list $course) {
        if (!$course->can_change_visibility()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'course_in_list::can_change_visbility');
        }
        return course_change_visibility($course->id, true);
    }

    /**
     * Makes a course hidden given a \course_in_list object.
     *
     * @param \course_in_list $course
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_hide(\course_in_list $course) {
        if (!$course->can_change_visibility()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'course_in_list::can_change_visbility');
        }
        return course_change_visibility($course->id, false);
    }

    /**
     * Makes a course visible given a course id or a database record.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_show_by_record($courserecordorid) {
        global $DB;
        if (is_int($courserecordorid)) {
            $courserecordorid = $DB->get_record('course', array('id' => $courserecordorid), '*', MUST_EXIST);
        }
        $course = new \course_in_list($courserecordorid);
        return self::action_course_show($course);
    }

    /**
     * Makes a course hidden given a course id or a database record.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_hide_by_record($courserecordorid) {
        global $DB;
        if (is_int($courserecordorid)) {
            $courserecordorid = $DB->get_record('course', array('id' => $courserecordorid), '*', MUST_EXIST);
        }
        $course = new \course_in_list($courserecordorid);
        return self::action_course_hide($course);
    }

    /**
     * Moves a category up one spot in the sort order given a \coursecat object.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_moveup(\coursecat $category) {
        if (!$category->can_change_sortorder()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->move_by_one(true);
    }

    /**
     * Moves a category down one spot in the sort order given a \coursecat object.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_movedown(\coursecat $category) {
        if (!$category->can_change_sortorder()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->move_by_one(false);
    }

    /**
     * Moves a category up one spot in the sort order given a category id.
     *
     * @param int $categoryid
     * @return bool
     */
    public static function action_category_moveup_by_id($categoryid) {
        $category = \coursecat::get($categoryid);
        return self::action_category_moveup($category);
    }

    /**
     * Moves a category down one spot in the sort order given a category id.
     *
     * @param int $categoryid
     * @return bool
     */
    public static function action_category_movedown_by_id($categoryid) {
        $category = \coursecat::get($categoryid);
        return self::action_category_movedown($category);
    }

    /**
     * Makes a category hidden given a \coursecat record.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_hide(\coursecat $category) {
        if (!$category->can_change_visibility()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_change_visbility');
        }
        $category->hide();
        return true;
    }

    /**
     * Makes a category visible given a \coursecat record.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_show(\coursecat $category) {
        if (!$category->can_change_visibility()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_change_visbility');
        }
        if ((int)$category->get_parent_coursecat()->visible === 0) {
            // You cannot mark a category visible if its parent is hidden.
            return false;
        }
        $category->show();
        return true;
    }

    /**
     * Makes a category visible given a \coursecat id or database record.
     *
     * @param int|\stdClass $categoryid
     * @return bool
     */
    public static function action_category_show_by_id($categoryid) {
        return self::action_category_show(\coursecat::get($categoryid));
    }

    /**
     * Makes a category hidden given a \coursecat id or database record.
     *
     * @param int|\stdClass $categoryid
     * @return bool
     */
    public static function action_category_hide_by_id($categoryid) {
        return self::action_category_hide(\coursecat::get($categoryid));
    }

    /**
     * Resorts the sub categories of the given category.
     *
     * @param \coursecat $category
     * @param string $sort One of idnumber or name.
     * @param bool $cleanup If true cleanup will be done, if false you will need to do it manually later.
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_resort_subcategories(\coursecat $category, $sort, $cleanup = true) {
        if (!$category->can_resort()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->resort_subcategories($sort, $cleanup);
    }

    /**
     * Resorts the courses within the given category.
     *
     * @param \coursecat $category
     * @param string $sort One of fullname, shortname or idnumber
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_resort_courses(\coursecat $category, $sort) {
        if (!$category->can_resort()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->resort_courses($sort);
    }

    /**
     * Moves courses out of one category and into a new category.
     *
     * @param \coursecat $oldcategory The category we are moving courses out of.
     * @param \coursecat $newcategory The category we are moving courses into.
     * @param array $courseids The ID's of the courses we want to move.
     * @return bool True on success.
     * @throws moodle_exception
     */
    public static function action_category_move_courses_into(\coursecat $oldcategory, \coursecat $newcategory, array $courseids) {
        global $DB;

        list($where, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['categoryid'] = $oldcategory->id;
        $sql = "SELECT c.id FROM {course} c WHERE c.id {$where} AND c.category <> :categoryid";
        if ($DB->record_exists_sql($sql, $params)) {
            // Likely being tinkered with.
            throw new \moodle_exception('coursedoesnotbelongtocategory');
        }

        // All courses are currently within the old category.
        return self::move_courses_into_category($newcategory, $courseids);
    }

    /**
     * Returns the view modes for the management interface.
     * @return array
     */
    public static function get_management_viewmodes() {
        return array(
            'combined' => new \lang_string('categoriesandcoures'),
            'categories' => new \lang_string('categories'),
            'courses' => new \lang_string('courses')
        );
    }

    /**
     * Search for courses with matching params.
     *
     * Please note that only one of search, blocklist, or modulelist can be specified at a time.
     * Specifying more than one will result in only the first being used.
     *
     * @param string $search Words to search for. We search fullname, shortname, idnumber and summary.
     * @param int $blocklist The ID of a block, courses will only be returned if they use this block.
     * @param string $modulelist The name of a module (relates to database table name). Only courses containing this module
     *      will be returned.
     * @param int $page The page number to display, starting at 0.
     * @param int $perpage The number of courses to display per page.
     * @return array
     */
    public static function search_courses($search, $blocklist, $modulelist, $page = 0, $perpage = null) {
        global $CFG;

        if ($perpage === null) {
            $perpage = $CFG->coursesperpage;
        }

        $searchcriteria = array();
        if (!empty($search)) {
            $searchcriteria = array('search' => $search);
        } else if (!empty($blocklist)) {
            $searchcriteria = array('blocklist' => $blocklist);
        } else if (!empty($modulelist)) {
            $searchcriteria = array('modulelist' => $modulelist);
        }

        $courses = \coursecat::get(0)->search_courses($searchcriteria, array(
            'recursive' => true,
            'offset' => $page * $perpage,
            'limit' => $perpage,
            'sort' => array('fullname' => 1)
        ));
        $totalcount = \coursecat::get(0)->search_courses_count($searchcriteria, array('recursive' => true));

        return array($courses, \count($courses), $totalcount);
    }

    /**
     * Moves one or more courses out of the category they are currently in and into a new category.
     *
     * This function works much the same way as action_category_move_courses_into however it allows courses from multiple
     * categories to be moved into a single category.
     *
     * @param int|coursecat $categoryid The category to move them into.
     * @param array|int $courseids An array of course id's or optionally just a single course id.
     * @return bool True on success or false on failure.
     */
    public static function move_courses_into_category($categoryorid, $courseids = array()) {
        global $DB;
        if (!is_array($courseids)) {
            // Just a single course ID.
            $courseids = array($courseids);
        }
        // Bulk move courses from one category to another.
        if (count($courseids) === 0) {
            return false;
        }
        if ($categoryorid instanceof \coursecat) {
            $moveto = $categoryorid;
        } else {
            $moveto = \coursecat::get($categoryorid);
        }
        if (!$moveto->can_move_courses_out_of() || !$moveto->can_move_courses_into()) {
            \debugging('Cannot move courses into the requested category.');
            return false;
        }

        list($where, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['categoryid'] = $moveto->id;
        $sql = "SELECT c.id, c.category FROM {course} c WHERE c.id {$where} AND c.category <> :categoryid";
        $courses = $DB->get_records_sql($sql, $params);
        $checks = array();
        foreach ($courseids as $id) {
            if (!isset($courses[$id])) {
                \debugging('Invalid course id given.', DEBUG_DEVELOPER);
                return false;
            }
            $catid = $courses[$id]->category;
            if (!isset($checks[$catid])) {
                $coursecat = \coursecat::get($catid);
                $checks[$catid] = $coursecat->can_move_courses_out_of() && $coursecat->can_move_courses_into();
            }
            if (!$checks[$catid]) {
                \debugging("Cannot move course {$id} out of its category.", DEBUG_DEVELOPER);
                return false;
            }
        }
        return \move_courses($courseids, $moveto->id);
    }

    /**
     * Returns an array of courseids and visiblity for all courses within the given category.
     * @param int $categoryid
     * @return array
     */
    public static function get_category_courses_visibility($categoryid) {
        global $DB;
        $sql = "SELECT c.id, c.visible as show
                  FROM {course} c
                 WHERE c.category = :category";
        $params = array('category' => (int)$categoryid);
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns an array of all categoryids that have the given category as a parent and their visible value.
     * @param int $categoryid
     * @return array
     */
    public static function get_category_children_visibility($categoryid) {
        global $DB;
        $category = \coursecat::get($categoryid);
        $select = $DB->sql_like('path', ':path');
        $path = $category->path . '/%';

        $sql = "SELECT c.id, c.visible as show
                  FROM {course_categories} c
                 WHERE ".$select;
        $params = array('path' => $path);
        return $DB->get_records_sql($sql, $params);
    }
}
