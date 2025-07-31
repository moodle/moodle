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

namespace core\navigation;

use core\context\module as context_module;
use core\context\course as context_course;
use core\context_helper;
use core\url;
use moodle_page;

/**
 * The global navigation class used especially for AJAX requests.
 *
 * The primary methods that are used in the global navigation class have been overriden
 * to ensure that only the relevant branch is generated at the root of the tree.
 * This can be done because AJAX is only used when the backwards structure for the
 * requested branch exists.
 * This has been done only because it shortens the amounts of information that is generated
 * which of course will speed up the response time.. because no one likes laggy AJAX.
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_navigation_for_ajax extends global_navigation {
    /** @var int used for determining what type of navigation_node::TYPE_* is being used */
    protected $branchtype;

    /** @var int the instance id */
    protected $instanceid;

    /** @var array Holds an array of expandable nodes */
    protected $expandable = [];

    /**
     * Constructs the navigation for use in an AJAX request
     *
     * @param moodle_page $page moodle_page object
     * @param int $branchtype
     * @param int $id
     */
    public function __construct($page, $branchtype, $id) {
        $this->page = $page;
        $this->cache = new navigation_cache(self::CACHE_NAME);
        $this->children = new navigation_node_collection();
        $this->branchtype = $branchtype;
        $this->instanceid = $id;
        $this->initialise();
    }

    #[\Override]
    public function initialise() {
        global $DB, $SITE;

        if ($this->initialised || during_initial_install()) {
            return $this->expandable;
        }
        $this->initialised = true;

        $this->rootnodes = [];
        $this->rootnodes['site']    = $this->add_course($SITE);
        $this->rootnodes['mycourses'] = $this->add(
            get_string('mycourses'),
            new url('/my/courses.php'),
            self::TYPE_ROOTNODE,
            null,
            'mycourses'
        );
        $this->rootnodes['courses'] = $this->add(get_string('courses'), null, self::TYPE_ROOTNODE, null, 'courses');
        // The courses branch is always displayed, and is always expandable (although may be empty).
        // This mimicks what is done during {@link global_navigation::initialise()}.
        $this->rootnodes['courses']->isexpandable = true;

        // Branchtype will be one of navigation_node::TYPE_*.
        switch ($this->branchtype) {
            case 0:
                if ($this->instanceid === 'mycourses') {
                    $this->load_courses_enrolled();
                } else if ($this->instanceid === 'courses') {
                    $this->load_courses_other();
                }
                break;
            case self::TYPE_CATEGORY:
                $this->load_category($this->instanceid);
                break;
            case self::TYPE_MY_CATEGORY:
                $this->load_category($this->instanceid, self::TYPE_MY_CATEGORY);
                break;
            case self::TYPE_COURSE:
                $course = $DB->get_record('course', ['id' => $this->instanceid], '*', MUST_EXIST);
                if (!can_access_course($course, null, '', true)) {
                    // Thats OK all courses are expandable by default. We don't need to actually expand it we can just
                    // add the course node and break. This leads to an empty node.
                    $this->add_course($course);
                    break;
                }
                require_course_login($course, true, null, false, true);
                $this->page->set_context(context_course::instance($course->id));
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);
                $this->add_course_essentials($coursenode, $course);
                $this->load_course_sections($course, $coursenode);
                break;
            case self::TYPE_SECTION:
                $sql = 'SELECT c.*, cs.section AS sectionnumber
                        FROM {course} c
                        LEFT JOIN {course_sections} cs ON cs.course = c.id
                        WHERE cs.id = ?';
                $course = $DB->get_record_sql($sql, [$this->instanceid], MUST_EXIST);
                require_course_login($course, true, null, false, true);
                $this->page->set_context(context_course::instance($course->id));
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);
                $this->add_course_essentials($coursenode, $course);
                $this->load_course_sections($course, $coursenode, $course->sectionnumber);
                break;
            case self::TYPE_ACTIVITY:
                $sql = "SELECT c.*
                          FROM {course} c
                          JOIN {course_modules} cm ON cm.course = c.id
                         WHERE cm.id = :cmid";
                $params = ['cmid' => $this->instanceid];
                $course = $DB->get_record_sql($sql, $params, MUST_EXIST);
                $modinfo = get_fast_modinfo($course);
                $cm = $modinfo->get_cm($this->instanceid);
                require_course_login($course, true, $cm, false, true);
                $this->page->set_context(context_module::instance($cm->id));
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);
                $this->load_course_sections($course, $coursenode, null, $cm);
                $activitynode = $coursenode->find($cm->id, self::TYPE_ACTIVITY);
                if ($activitynode) {
                    $modulenode = $this->load_activity($cm, $course, $activitynode);
                }
                break;
            default:
                throw new \Exception('Unknown type');
                return $this->expandable;
        }

        if ($this->page->context->contextlevel == CONTEXT_COURSE && $this->page->context->instanceid != $SITE->id) {
            $this->load_for_user(null, true);
        }

        // Give the local plugins a chance to include some navigation if they want.
        $this->load_local_plugin_navigation();

        $this->find_expandable($this->expandable);
        return $this->expandable;
    }

    /**
     * They've expanded the general 'courses' branch.
     */
    protected function load_courses_other() {
        $this->load_all_courses();
    }

    /**
     * Loads a single category into the AJAX navigation.
     *
     * This function is special in that it doesn't concern itself with the parent of
     * the requested category or its siblings.
     * This is because with the AJAX navigation we know exactly what is wanted and only need to
     * request that.
     *
     * @param int $categoryid id of category to load in navigation.
     * @param int $nodetype type of node, if category is under MyHome then it's TYPE_MY_CATEGORY
     */
    protected function load_category($categoryid, $nodetype = self::TYPE_CATEGORY) {
        global $CFG, $DB;

        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = (int)$CFG->navcourselimit;
        }

        $catcontextsql = context_helper::get_preload_record_columns_sql('ctx');
        $sql = "SELECT cc.*, $catcontextsql
                  FROM {course_categories} cc
                  JOIN {context} ctx ON cc.id = ctx.instanceid
                 WHERE ctx.contextlevel = " . CONTEXT_COURSECAT . " AND
                       (cc.id = :categoryid1 OR cc.parent = :categoryid2)
              ORDER BY cc.depth ASC, cc.sortorder ASC, cc.id ASC";
        $params = ['categoryid1' => $categoryid, 'categoryid2' => $categoryid];
        $categories = $DB->get_recordset_sql($sql, $params, 0, $limit);
        $categorylist = [];
        $subcategories = [];
        $basecategory = null;
        foreach ($categories as $category) {
            $categorylist[] = $category->id;
            context_helper::preload_from_record($category);
            if ($category->id == $categoryid) {
                $this->add_category($category, $this, $nodetype);
                $basecategory = $this->addedcategories[$category->id];
            } else {
                $subcategories[$category->id] = $category;
            }
        }
        $categories->close();

        // If category is shown in MyHome then only show enrolled courses and hide empty subcategories,
        // else show all courses.
        if ($nodetype === self::TYPE_MY_CATEGORY) {
            $courses = enrol_get_my_courses('*');
            $categoryids = [];

            // Only search for categories if basecategory was found.
            if (!is_null($basecategory)) {
                // Get course parent category ids.
                foreach ($courses as $course) {
                    $categoryids[] = $course->category;
                }

                // Get a unique list of category ids which a part of the path
                // to user's courses.
                $coursesubcategories = [];
                $addedsubcategories = [];

                [$sql, $params] = $DB->get_in_or_equal($categoryids);
                $categories = $DB->get_recordset_select('course_categories', 'id ' . $sql, $params, 'sortorder, id', 'id, path');

                foreach ($categories as $category) {
                    $coursesubcategories = array_merge($coursesubcategories, explode('/', trim($category->path, "/")));
                }
                $categories->close();
                $coursesubcategories = array_unique($coursesubcategories);

                // Only add a subcategory if it is part of the path to user's course and
                // wasn't already added.
                foreach ($subcategories as $subid => $subcategory) {
                    if (
                        in_array($subid, $coursesubcategories) &&
                            !in_array($subid, $addedsubcategories)
                    ) {
                            $this->add_category($subcategory, $basecategory, $nodetype);
                            $addedsubcategories[] = $subid;
                    }
                }
            }

            foreach ($courses as $course) {
                // Add course if it's in category.
                if (in_array($course->category, $categorylist)) {
                    $this->add_course($course, true, self::COURSE_MY);
                }
            }
        } else {
            if (!is_null($basecategory)) {
                foreach ($subcategories as $key => $category) {
                    $this->add_category($category, $basecategory, $nodetype);
                }
            }
            $courses = $DB->get_recordset('course', ['category' => $categoryid], 'sortorder', '*', 0, $limit);
            foreach ($courses as $course) {
                $this->add_course($course);
            }
            $courses->close();
        }
    }

    /**
     * Returns an array of expandable nodes.
     *
     * @return array
     */
    public function get_expandable() {
        return $this->expandable;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(global_navigation_for_ajax::class, \global_navigation_for_ajax::class);
