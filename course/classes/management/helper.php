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
     * The expanded category structure if its already being loaded from the cache.
     * @var null|array
     */
    protected static $expandedcategories = null;

    /**
     * Returns course details in an array ready to be printed.
     *
     * @global \moodle_database $DB
     * @param \course_in_list $course
     * @return array
     */
    public static function get_course_detail_array(\course_in_list $course) {
        global $DB;

        $canaccess = $course->can_access();

        $format = \course_get_format($course->id);
        $modinfo = \get_fast_modinfo($course->id);
        $modules = $modinfo->get_used_module_names();
        $sections = array();
        if ($format->uses_sections()) {
            foreach ($modinfo->get_section_info_all() as $section) {
                if ($section->uservisible) {
                    $sections[] = $format->get_section_name($section);
                }
            }
        }

        $category = \coursecat::get($course->category);
        $categoryurl = new \moodle_url('/course/management.php', array('categoryid' => $course->category));
        $categoryname = $category->get_formatted_name();

        $details = array(
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
            )
        );
        if (has_capability('moodle/site:accessallgroups', $course->get_context())) {
            $groups = \groups_get_course_data($course->id);
            $details += array(
                'groupings' => array(
                    'key' => \get_string('groupings', 'group'),
                    'value' => count($groups->groupings)
                ),
                'groups' => array(
                    'key' => \get_string('groups'),
                    'value' => count($groups->groups)
                )
            );
        }
        if ($canaccess) {
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

            $details['roleassignments'] = array(
                'key' => \get_string('roleassignments'),
                'value' => join('<br />', $roledetails)
            );
        }
        if ($course->can_review_enrolments()) {
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
            $details['enrolmentmethods'] = array(
                'key' => \get_string('enrolmentmethods'),
                'value' => join('<br />', $enrolmentlines)
            );
        }
        if ($canaccess) {
            $details['format'] = array(
                'key' => \get_string('format'),
                'value' => \course_get_format($course)->get_format_name()
            );
            $details['sections'] = array(
                'key' => \get_string('sections'),
                'value' => join('<br />', $sections)
            );
            $details['modulesused'] = array(
                'key' => \get_string('modulesused'),
                'value' =>  join('<br />', $modules)
            );
        }
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
        if ($category->can_change_sortorder()) {
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

        if ($category->can_create_subcategory()) {
            $actions['createnewsubcategory'] = array(
                'url' => new \moodle_url('/course/editcategory.php', array('parent' => $category->id)),
                'icon' => new \pix_icon('i/withsubcat', new \lang_string('createnewsubcategory')),
                'string' => new \lang_string('createnewsubcategory')
            );
        }

        // Resort.
        if ($category->can_resort_subcategories() && $category->has_children()) {
            $actions['resortbyname'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'resortcategories', 'resort' => 'name')),
                'icon' => new \pix_icon('t/sort', new \lang_string('sort')),
                'string' => new \lang_string('resortsubcategoriesby', 'moodle' , get_string('categoryname'))
            );
            $actions['resortbynamedesc'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'resortcategories', 'resort' => 'namedesc')),
                'icon' => new \pix_icon('t/sort', new \lang_string('sort')),
                'string' => new \lang_string('resortsubcategoriesbyreverse', 'moodle', get_string('categoryname'))
            );
            $actions['resortbyidnumber'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'resortcategories', 'resort' => 'idnumber')),
                'icon' => new \pix_icon('t/sort', new \lang_string('sort')),
                'string' => new \lang_string('resortsubcategoriesby', 'moodle', get_string('idnumbercoursecategory'))
            );
            $actions['resortbyidnumberdesc'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'resortcategories', 'resort' => 'idnumberdesc')),
                'icon' => new \pix_icon('t/sort', new \lang_string('sort')),
                'string' => new \lang_string('resortsubcategoriesbyreverse', 'moodle', get_string('idnumbercoursecategory'))
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
                'icon' => new \pix_icon('t/assignroles', new \lang_string('assignroles', 'role')),
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
                'icon' => new \pix_icon('t/cohort', new \lang_string('cohorts', 'cohort')),
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

        if ($category->can_restore_courses_into()) {
            $actions['restore'] = array(
                'url' => new \moodle_url('/backup/restorefile.php', array('contextid' => $category->get_context()->id)),
                'icon' => new \pix_icon('i/restore', new \lang_string('restorecourse', 'admin')),
                'string' => new \lang_string('restorecourse', 'admin')
            );
        }

        return $actions;
    }

    /**
     * Returns an array of actions for a course listitem.
     *
     * @param \coursecat $category
     * @param \course_in_list $course
     * @return string
     */
    public static function get_course_listitem_actions(\coursecat $category, \course_in_list $course) {
        $baseurl = new \moodle_url(
            '/course/management.php',
            array('courseid' => $course->id, 'categoryid' => $course->category, 'sesskey' => \sesskey())
        );
        $actions = array();
        // Edit.
        if ($course->can_edit()) {
            $actions[] = array(
                'url' => new \moodle_url('/course/edit.php', array('id' => $course->id, 'returnto' => 'catmanage')),
                'icon' => new \pix_icon('t/edit', \get_string('edit')),
                'attributes' => array('class' => 'action-edit')
            );
        }
        // Delete.
        if ($course->can_delete()) {
            $actions[] = array(
                'url' => new \moodle_url('/course/delete.php', array('id' => $course->id)),
                'icon' => new \pix_icon('t/delete', \get_string('delete')),
                'attributes' => array('class' => 'action-delete')
            );
        }
        // Show/Hide.
        if ($course->can_change_visibility()) {
            $actions[] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'hidecourse')),
                'icon' => new \pix_icon('t/hide', \get_string('hide')),
                'attributes' => array('data-action' => 'hide', 'class' => 'action-hide')
            );
            $actions[] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'showcourse')),
                'icon' => new \pix_icon('t/show', \get_string('show')),
                'attributes' => array('data-action' => 'show', 'class' => 'action-show')
            );
        }
        // Move up/down.
        if ($category->can_resort_courses()) {
            $actions[] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'movecourseup')),
                'icon' => new \pix_icon('t/up', \get_string('up')),
                'attributes' => array('data-action' => 'moveup', 'class' => 'action-moveup')
            );
            $actions[] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'movecoursedown')),
                'icon' => new \pix_icon('t/down', \get_string('down')),
                'attributes' => array('data-action' => 'movedown', 'class' => 'action-movedown')
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
        if ($course->is_uservisible()) {
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
     * Resorts the courses within a category moving the given course up by one.
     *
     * @param \course_in_list $course
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_change_sortorder_up_one(\course_in_list $course, \coursecat $category) {
        if (!$category->can_resort_courses()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return \course_change_sortorder_by_one($course, true);
    }

    /**
     * Resorts the courses within a category moving the given course down by one.
     *
     * @param \course_in_list $course
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_change_sortorder_down_one(\course_in_list $course, \coursecat $category) {
        if (!$category->can_resort_courses()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return \course_change_sortorder_by_one($course, false);
    }

    /**
     * Resorts the courses within a category moving the given course up by one.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_change_sortorder_up_one_by_record($courserecordorid) {
        if (is_int($courserecordorid)) {
            $courserecordorid = get_course($courserecordorid);
        }
        $course = new \course_in_list($courserecordorid);
        $category = \coursecat::get($course->category);
        return self::action_course_change_sortorder_up_one($course, $category);
    }

    /**
     * Resorts the courses within a category moving the given course down by one.
     *
     * @global \moodle_database $DB
     * @param int|\stdClass $courserecordorid
     * @return bool
     */
    public static function action_course_change_sortorder_down_one_by_record($courserecordorid) {
        if (is_int($courserecordorid)) {
            $courserecordorid = get_course($courserecordorid);
        }
        $course = new \course_in_list($courserecordorid);
        $category = \coursecat::get($course->category);
        return self::action_course_change_sortorder_down_one($course, $category);
    }

    /**
     * Changes the sort order so that the first course appears after the second course.
     *
     * @param int|\stdClass $courserecordorid
     * @param int $moveaftercourseid
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_course_change_sortorder_after_course($courserecordorid, $moveaftercourseid) {
        $course = \get_course($courserecordorid);
        $category = \coursecat::get($course->category);
        if (!$category->can_resort_courses()) {
            $url = '/course/management.php?categoryid='.$course->category;
            throw new \moodle_exception('nopermissions', 'error', $url, \get_string('resortcourses', 'moodle'));
        }
        return \course_change_sortorder_after_course($course, $moveaftercourseid);
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
        if (is_int($courserecordorid)) {
            $courserecordorid = get_course($courserecordorid);
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
        if (is_int($courserecordorid)) {
            $courserecordorid = get_course($courserecordorid);
        }
        $course = new \course_in_list($courserecordorid);
        return self::action_course_hide($course);
    }

    /**
     * Resort a categories subcategories shifting the given category up one.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_change_sortorder_up_one(\coursecat $category) {
        if (!$category->can_change_sortorder()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_change_sortorder');
        }
        return $category->change_sortorder_by_one(true);
    }

    /**
     * Resort a categories subcategories shifting the given category down one.
     *
     * @param \coursecat $category
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_change_sortorder_down_one(\coursecat $category) {
        if (!$category->can_change_sortorder()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_change_sortorder');
        }
        return $category->change_sortorder_by_one(false);
    }

    /**
     * Resort a categories subcategories shifting the given category up one.
     *
     * @param int $categoryid
     * @return bool
     */
    public static function action_category_change_sortorder_up_one_by_id($categoryid) {
        $category = \coursecat::get($categoryid);
        return self::action_category_change_sortorder_up_one($category);
    }

    /**
     * Resort a categories subcategories shifting the given category down one.
     *
     * @param int $categoryid
     * @return bool
     */
    public static function action_category_change_sortorder_down_one_by_id($categoryid) {
        $category = \coursecat::get($categoryid);
        return self::action_category_change_sortorder_down_one($category);
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
        if (!$category->can_resort_subcategories()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->resort_subcategories($sort, $cleanup);
    }

    /**
     * Resorts the courses within the given category.
     *
     * @param \coursecat $category
     * @param string $sort One of fullname, shortname or idnumber
     * @param bool $cleanup If true cleanup will be done, if false you will need to do it manually later.
     * @return bool
     * @throws \moodle_exception
     */
    public static function action_category_resort_courses(\coursecat $category, $sort, $cleanup = true) {
        if (!$category->can_resort_courses()) {
            throw new \moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
        }
        return $category->resort_courses($sort, $cleanup);
    }

    /**
     * Moves courses out of one category and into a new category.
     *
     * @param \coursecat $oldcategory The category we are moving courses out of.
     * @param \coursecat $newcategory The category we are moving courses into.
     * @param array $courseids The ID's of the courses we want to move.
     * @return bool True on success.
     * @throws \moodle_exception
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
     * @param int|\coursecat $categoryorid The category to move them into.
     * @param array|int $courseids An array of course id's or optionally just a single course id.
     * @return bool True on success or false on failure.
     * @throws \moodle_exception
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
            throw new \moodle_exception('cannotmovecourses');
        }

        list($where, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "SELECT c.id, c.category FROM {course} c WHERE c.id {$where}";
        $courses = $DB->get_records_sql($sql, $params);
        $checks = array();
        foreach ($courseids as $id) {
            if (!isset($courses[$id])) {
                throw new \moodle_exception('invalidcourseid');
            }
            $catid = $courses[$id]->category;
            if (!isset($checks[$catid])) {
                $coursecat = \coursecat::get($catid);
                $checks[$catid] = $coursecat->can_move_courses_out_of() && $coursecat->can_move_courses_into();
            }
            if (!$checks[$catid]) {
                throw new \moodle_exception('cannotmovecourses');
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
        $sql = "SELECT c.id, c.visible
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

        $sql = "SELECT c.id, c.visible
                  FROM {course_categories} c
                 WHERE ".$select;
        $params = array('path' => $path);
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Records when a category is expanded or collapsed so that when the user
     *
     * @param \coursecat $coursecat The category we're working with.
     * @param bool $expanded True if the category is expanded now.
     */
    public static function record_expanded_category(\coursecat $coursecat, $expanded = true) {
        // If this ever changes we are going to reset it and reload the categories as required.
        self::$expandedcategories = null;
        $categoryid = $coursecat->id;
        $path = $coursecat->get_parents();
        /* @var \cache_session $cache */
        $cache = \cache::make('core', 'userselections');
        $categories = $cache->get('categorymanagementexpanded');
        if (!is_array($categories)) {
            if (!$expanded) {
                // No categories recorded, nothing to remove.
                return;
            }
            $categories = array();
        }
        if ($expanded) {
            $ref =& $categories;
            foreach ($coursecat->get_parents() as $path) {
                if (!isset($ref[$path]) || !is_array($ref[$path])) {
                    $ref[$path] = array();
                }
                $ref =& $ref[$path];
            }
            if (!isset($ref[$categoryid])) {
                $ref[$categoryid] = true;
            }
        } else {
            $found = true;
            $ref =& $categories;
            foreach ($coursecat->get_parents() as $path) {
                if (!isset($ref[$path])) {
                    $found = false;
                    break;
                }
                $ref =& $ref[$path];
            }
            if ($found) {
                $ref[$categoryid] = null;
                unset($ref[$categoryid]);
            }
        }
        $cache->set('categorymanagementexpanded', $categories);
    }

    /**
     * Returns the categories that should be expanded when displaying the interface.
     *
     * @param int|null $withpath If specified a path to require as the parent.
     * @return \coursecat[] An array of Category ID's to expand.
     */
    public static function get_expanded_categories($withpath = null) {
        if (self::$expandedcategories === null) {
            /* @var \cache_session $cache */
            $cache = \cache::make('core', 'userselections');
            self::$expandedcategories = $cache->get('categorymanagementexpanded');
            if (self::$expandedcategories === false) {
                self::$expandedcategories = array();
            }
        }
        if (empty($withpath)) {
            return array_keys(self::$expandedcategories);
        }
        $parents = explode('/', trim($withpath, '/'));
        $ref =& self::$expandedcategories;
        foreach ($parents as $parent) {
            if (!isset($ref[$parent])) {
                return array();
            }
            $ref =& $ref[$parent];
        }
        if (is_array($ref)) {
            return array_keys($ref);
        } else {
            return array($parent);
        }
    }
}
