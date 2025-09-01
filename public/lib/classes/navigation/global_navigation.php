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

use cm_info;
use section_info;
use core\component;
use core\context\course as context_course;
use core\context\coursecat as context_coursecat;
use core\context\module as context_module;
use core\context\system as context_system;
use core\context\user as context_user;
use core\context_helper;
use core\exception\coding_exception;
use core\output\action_link;
use core\output\actions\component_action;
use core\output\pix_icon;
use core\url;
use core_cache\cache;
use core_cache\session_cache;
use core_course_category;
use course_modinfo;
use moodle_page;
use stdClass;

/**
 * The global navigation class used for... the global navigation
 *
 * This class is used by PAGE to store the global navigation for the site
 * and is then used by the settings nav and navbar to save on processing and DB calls
 *
 * See
 * {@link lib/pagelib.php} {@link moodle_page::initialise_theme_and_output()}
 * {@link lib/ajax/getnavbranch.php} Called by ajax
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_navigation extends navigation_node {
    /** @var moodle_page The Moodle page this navigation object belongs to. */
    protected $page;
    /** @var bool switch to let us know if the navigation object is initialised*/
    protected $initialised = false;
    /** @var array An array of course information */
    protected $mycourses = [];
    /** @var navigation_node[] An array for containing  root navigation nodes */
    protected $rootnodes = [];
    /** @var bool A switch for whether to show empty sections in the navigation */
    protected $showemptysections = true;
    /** @var bool A switch for whether courses should be shown within categories on the navigation. */
    protected $showcategories = null;
    /** @var null|var bool A switch for whether or not to show categories in the my courses branch. */
    protected $showmycategories = null;
    /** @var array An array of stdClasses for users that the navigation is extended for */
    protected $extendforuser = [];
    /** @var navigation_cache */
    protected $cache;
    /** @var array An array of course ids that are present in the navigation */
    protected $addedcourses = [];
    /** @var bool */
    protected $allcategoriesloaded = false;
    /** @var array An array of category ids that are included in the navigation */
    protected $addedcategories = [];
    /** @var int expansion limit */
    protected $expansionlimit = 0;
    /** @var int userid to allow parent to see child's profile page navigation */
    protected $useridtouseforparentchecks = 0;
    /** @var session_cache A cache that stores information on expanded courses */
    protected $cacheexpandcourse = null;

    /** Used when loading categories to load all top level categories [parent = 0] **/
    public const LOAD_ROOT_CATEGORIES = 0;
    /** Used when loading categories to load all categories **/
    public const LOAD_ALL_CATEGORIES = -1;

    /**
     * Constructs a new global navigation
     *
     * @param moodle_page $page The page this navigation object belongs to
     */
    public function __construct(moodle_page $page) {
        global $CFG, $SITE, $USER;

        if (during_initial_install()) {
            return;
        }

        $homepage = get_home_page();
        if ($homepage == HOMEPAGE_MY) {
            // We are using the users my moodle for the root element.
            $properties = [
                'key' => 'myhome',
                'type' => navigation_node::TYPE_SYSTEM,
                'text' => get_string('myhome'),
                'action' => new url('/my/'),
                'icon' => new pix_icon('i/dashboard', ''),
            ];
        } else if ($homepage == HOMEPAGE_MYCOURSES) {
            // We are using the user's course summary page for the root element.
            $properties = [
                'key' => 'mycourses',
                'type' => navigation_node::TYPE_SYSTEM,
                'text' => get_string('mycourses'),
                'action' => new url('/my/courses.php'),
                'icon' => new pix_icon('i/course', ''),
            ];
        } else {
            // We are using the site home for the root element.
            $properties = [
                'key' => 'home',
                'type' => navigation_node::TYPE_SYSTEM,
                'text' => get_string('home'),
                'action' => new url('/'),
                'icon' => new pix_icon('i/home', ''),
            ];
        }

        // Use the parents constructor.... good good reuse.
        parent::__construct($properties);
        $this->showinflatnavigation = true;

        // Initalise and set defaults.
        $this->page = $page;
        $this->forceopen = true;
        $this->cache = new navigation_cache(self::CACHE_NAME);
    }

    /**
     * Mutator to set userid to allow parent to see child's profile
     * page navigation. See MDL-25805 for initial issue. Linked to it
     * is an issue explaining why this is a REALLY UGLY HACK thats not
     * for you to use!
     *
     * @param int $userid userid of profile page that parent wants to navigate around.
     */
    public function set_userid_for_parent_checks($userid) {
        $this->useridtouseforparentchecks = $userid;
    }


    /**
     * Initialises the navigation object.
     *
     * This causes the navigation object to look at the current state of the page
     * that it is associated with and then load the appropriate content.
     *
     * This should only occur the first time that the navigation structure is utilised
     * which will normally be either when the navbar is called to be displayed or
     * when a block makes use of it.
     *
     * @return bool
     */
    public function initialise() {
        global $CFG, $SITE, $USER;
        // Check if it has already been initialised.
        if ($this->initialised || during_initial_install()) {
            return true;
        }
        $this->initialised = true;

        // Set up the five base root nodes. These are nodes where we will put our content and are as follows:
        // - site: Navigation for the front page.
        // - myprofile: User profile information goes here.
        // - currentcourse: The course being currently viewed.
        // - mycourses: The users courses get added here.
        // - courses: Additional courses are added here.
        // - users: Other users information loaded here.
        $this->rootnodes = [];
        $defaulthomepage = get_home_page();
        if ($defaulthomepage == HOMEPAGE_SITE) {
            // The home element should be my moodle because the root element is the site.
            if (isloggedin() && !isguestuser()) {  // Makes no sense if you aren't logged in.
                if (!empty($CFG->enabledashboard)) {
                    // Only add dashboard to home if it's enabled.
                    $this->rootnodes['home'] = $this->add(
                        get_string('myhome'),
                        new url('/my/'),
                        self::TYPE_SETTING,
                        null,
                        'myhome',
                        new pix_icon('i/dashboard', '')
                    );
                    $this->rootnodes['home']->showinflatnavigation = true;
                }
            }
        } else {
            // The home element should be the site because the root node is my moodle.
            $this->rootnodes['home'] = $this->add(
                get_string('sitehome'),
                new url('/'),
                self::TYPE_SETTING,
                null,
                'home',
                new pix_icon('i/home', '')
            );
            $this->rootnodes['home']->showinflatnavigation = true;
            if (
                !empty($CFG->defaulthomepage) &&
                    ($CFG->defaulthomepage == HOMEPAGE_MY || $CFG->defaulthomepage == HOMEPAGE_MYCOURSES)
            ) {
                // We need to stop automatic redirection.
                $this->rootnodes['home']->action->param('redirect', '0');
            }
        }
        $this->rootnodes['site'] = $this->add_course($SITE);
        $this->rootnodes['myprofile'] = $this->add(get_string('profile'), null, self::TYPE_USER, null, 'myprofile');
        $this->rootnodes['currentcourse'] = $this->add(
            get_string('currentcourse'),
            null,
            self::TYPE_ROOTNODE,
            null,
            'currentcourse',
        );
        $this->rootnodes['mycourses'] = $this->add(
            get_string('mycourses'),
            new url('/my/courses.php'),
            self::TYPE_ROOTNODE,
            null,
            'mycourses',
            new pix_icon('i/course', ''),
        );
        // We do not need to show this node in the breadcrumbs if the default homepage is mycourses.
        // It will be automatically handled by the breadcrumb generator.
        if ($defaulthomepage == HOMEPAGE_MYCOURSES) {
            $this->rootnodes['mycourses']->mainnavonly = true;
        }

        $this->rootnodes['courses'] = $this->add(
            get_string('courses'),
            new url('/course/index.php'),
            self::TYPE_ROOTNODE,
            null,
            'courses',
        );
        if (!core_course_category::user_top()) {
            $this->rootnodes['courses']->hide();
        }
        $this->rootnodes['users'] = $this->add(get_string('users'), null, self::TYPE_ROOTNODE, null, 'users');

        // We always load the frontpage course to ensure it is available without JavaScript enabled.
        $this->add_front_page_course_essentials($this->rootnodes['site'], $SITE);
        $this->load_course_sections($SITE, $this->rootnodes['site']);

        $course = $this->page->course;
        $this->load_courses_enrolled();

        // Note: $issite gets set to true if the current pages course is the sites frontpage course.
        $issite = ($this->page->course->id == $SITE->id);

        // Determine if the user is enrolled in any course.
        $enrolledinanycourse = enrol_user_sees_own_courses();

        $this->rootnodes['currentcourse']->mainnavonly = true;
        if ($enrolledinanycourse) {
            $this->rootnodes['mycourses']->isexpandable = true;
            $this->rootnodes['mycourses']->showinflatnavigation = true;
            if ($CFG->navshowallcourses) {
                // When we show all courses we need to show both the my courses and the regular courses branch.
                $this->rootnodes['courses']->isexpandable = true;
            }
        } else {
            $this->rootnodes['courses']->isexpandable = true;
        }
        $this->rootnodes['mycourses']->forceopen = true;

        $canviewcourseprofile = true;

        // Next load context specific content into the navigation.
        switch ($this->page->context->contextlevel) {
            case CONTEXT_SYSTEM:
                // Nothing left to do here I feel.
                break;
            case CONTEXT_COURSECAT:
                // This is essential, we must load categories.
                $this->load_all_categories($this->page->context->instanceid, true);
                break;
            case CONTEXT_BLOCK:
            case CONTEXT_COURSE:
                if ($issite) {
                    // Nothing left to do here.
                    break;
                }

                // Load the course associated with the current page into the navigation.
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the course node and not populate it.

                // Not enrolled, can't view, and hasn't switched roles.
                if (!can_access_course($course, null, '', true)) {
                    if ($coursenode->isexpandable === true) {
                        // Obviously the situation has changed, update the cache and adjust the node.
                        // This occurs if the user access to a course has been revoked (one way or another) after
                        // initially logging in for this session.
                        $this->get_expand_course_cache()->set($course->id, 1);
                        $coursenode->isexpandable = true;
                        $coursenode->nodetype = self::NODETYPE_BRANCH;
                    }
                    // Very ugly hack - do not force "parents" to enrol into course their child is enrolled in.
                    // This hack has been propagated from user/view.php to display the navigation node. (MDL-25805).
                    if (!$this->current_user_is_parent_role()) {
                        $coursenode->make_active();
                        $canviewcourseprofile = false;
                        break;
                    }
                } else if ($coursenode->isexpandable === false) {
                    // Obviously the situation has changed, update the cache and adjust the node.
                    // This occurs if the user has been granted access to a course (one way or another) after initially
                    // logging in for this session.
                    $this->get_expand_course_cache()->set($course->id, 1);
                    $coursenode->isexpandable = true;
                    $coursenode->nodetype = self::NODETYPE_BRANCH;
                }

                // Add the essentials such as reports etc...
                $this->add_course_essentials($coursenode, $course);
                // Extend course navigation with it's sections/activities.
                $this->load_course_sections($course, $coursenode);
                if (!$coursenode->contains_active_node() && !$coursenode->search_for_active_node()) {
                    $coursenode->make_active();
                }

                break;
            case CONTEXT_MODULE:
                if ($issite) {
                    // If this is the site course then most information will have already been loaded.
                    // However we need to check if there is more content that can yet be loaded for the specific module instance.
                    $activitynode = $this->rootnodes['site']->find($this->page->cm->id, navigation_node::TYPE_ACTIVITY);
                    if ($activitynode) {
                        $this->load_activity($this->page->cm, $this->page->course, $activitynode);
                    }
                    break;
                }

                $course = $this->page->course;
                $cm = $this->page->cm;

                // Load the course associated with the page into the navigation.
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the course node and not populate it.
                if (!can_access_course($course, null, '', true)) {
                    $coursenode->make_active();
                    $canviewcourseprofile = false;
                    break;
                }

                $this->add_course_essentials($coursenode, $course);

                // Load the course sections into the page.
                $this->load_course_sections($course, $coursenode, null, $cm);
                $activity = $coursenode->find($cm->id, navigation_node::TYPE_ACTIVITY);
                if (!empty($activity)) {
                    // Finally load the cm specific navigaton information.
                    $this->load_activity($cm, $course, $activity);

                    // Check if we have an active node.
                    if (!$activity->contains_active_node() && !$activity->search_for_active_node()) {
                        // And make the activity node active.
                        $activity->make_active();
                    }
                }
                break;
            case CONTEXT_USER:
                if ($issite) {
                    // The users profile information etc is already loaded for the front page.
                    break;
                }
                $course = $this->page->course;
                // Load the course associated with the user into the navigation.
                $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);

                // If the course wasn't added then don't try going any further.
                if (!$coursenode) {
                    $canviewcourseprofile = false;
                    break;
                }

                // If the user is not enrolled then we only want to show the course node and not populate it.
                if (!can_access_course($course, null, '', true)) {
                    $coursenode->make_active();
                    $canviewcourseprofile = false;
                    break;
                }
                $this->add_course_essentials($coursenode, $course);
                $this->load_course_sections($course, $coursenode);
                break;
        }

        // Load for the current user.
        $this->load_for_user();
        if (
            $this->page->context->contextlevel >= CONTEXT_COURSE
            && $this->page->context->instanceid != $SITE->id
            && $canviewcourseprofile
        ) {
            $this->load_for_user(null, true);
        }
        // Load each extending user into the navigation.
        foreach ($this->extendforuser as $user) {
            if ($user->id != $USER->id) {
                $this->load_for_user($user);
            }
        }

        // Give the local plugins a chance to include some navigation if they want.
        $this->load_local_plugin_navigation();

        // Remove any empty root nodes.
        foreach ($this->rootnodes as $node) {
            // Dont remove the home node.
            if (!in_array($node->key, ['home', 'mycourses', 'myhome']) && !$node->has_children() && !$node->isactive) {
                $node->remove();
            }
        }

        if (!$this->contains_active_node()) {
            $this->search_for_active_node();
        }

        // If the user is not logged in modify the navigation structure.
        if (!isloggedin()) {
            $activities = clone($this->rootnodes['site']->children);
            $this->rootnodes['site']->remove();
            $children = clone($this->children);
            $this->children = new navigation_node_collection();
            foreach ($activities as $child) {
                $this->children->add($child);
            }
            foreach ($children as $child) {
                $this->children->add($child);
            }
        }
        return true;
    }

    /**
     * This function gives local plugins an opportunity to modify navigation.
     */
    protected function load_local_plugin_navigation() {
        foreach (get_plugin_list_with_function('local', 'extend_navigation') as $function) {
            $function($this);
        }
    }

    /**
     * Returns true if the current user is a parent of the user being currently viewed.
     *
     * If the current user is not viewing another user, or if the current user does not hold any parent roles over the
     * other user being viewed this function returns false.
     * In order to set the user for whom we are checking against you must call {@link set_userid_for_parent_checks()}
     *
     * @since Moodle 2.4
     * @return bool
     */
    protected function current_user_is_parent_role() {
        global $USER, $DB;
        if ($this->useridtouseforparentchecks && $this->useridtouseforparentchecks != $USER->id) {
            $usercontext = context_user::instance($this->useridtouseforparentchecks, MUST_EXIST);
            if (!has_capability('moodle/user:viewdetails', $usercontext)) {
                return false;
            }
            if ($DB->record_exists('role_assignments', ['userid' => $USER->id, 'contextid' => $usercontext->id])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if courses should be shown within categories on the navigation.
     *
     * @param bool $ismycourse Set to true if you are calculating this for a course.
     * @return bool
     */
    protected function show_categories($ismycourse = false) {
        global $CFG, $DB;
        if ($ismycourse) {
            return $this->show_my_categories();
        }
        if ($this->showcategories === null) {
            $show = false;
            if ($this->page->context->contextlevel == CONTEXT_COURSECAT) {
                $show = true;
            } else if (!empty($CFG->navshowcategories) && $DB->count_records('course_categories') > 1) {
                $show = true;
            }
            $this->showcategories = $show;
        }
        return $this->showcategories;
    }

    /**
     * Returns true if we should show categories in the My Courses branch.
     * @return bool
     */
    protected function show_my_categories() {
        global $CFG;
        if ($this->showmycategories === null) {
            $this->showmycategories = !empty($CFG->navshowmycoursecategories) && !core_course_category::is_simple_site();
        }
        return $this->showmycategories;
    }

    /**
     * Loads the courses in Moodle into the navigation.
     *
     * @param string|array $categoryids An array containing categories to load courses
     *                     for, OR null to load courses for all categories.
     * @return array An array of navigation_nodes one for each course
     */
    protected function load_all_courses($categoryids = null) {
        global $CFG, $DB, $SITE;

        // Work out the limit of courses.
        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = $CFG->navcourselimit;
        }

        $toload = (empty($CFG->navshowallcourses)) ? self::LOAD_ROOT_CATEGORIES : self::LOAD_ALL_CATEGORIES;

        // If we are going to show all courses AND we are showing categories then
        // to save us repeated DB calls load all of the categories now.
        if ($this->show_categories()) {
            $this->load_all_categories($toload);
        }

        // Will be the return of our efforts.
        $coursenodes = [];

        // Check if we need to show categories.
        if ($this->show_categories()) {
            // Hmmm we need to show categories... this is going to be painful.
            // We now need to fetch up to $limit courses for each category to
            // be displayed.
            if ($categoryids !== null) {
                if (!is_array($categoryids)) {
                    $categoryids = [$categoryids];
                }
                [$categorywhere, $categoryparams] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'cc');
                $categorywhere = 'WHERE cc.id ' . $categorywhere;
            } else if ($toload == self::LOAD_ROOT_CATEGORIES) {
                $categorywhere = 'WHERE cc.depth = 1 OR cc.depth = 2';
                $categoryparams = [];
            } else {
                $categorywhere = '';
                $categoryparams = [];
            }

            // First up we are going to get the categories that we are going to
            // need so that we can determine how best to load the courses from them.
            $sql = "SELECT cc.id, COUNT(c.id) AS coursecount
                        FROM {course_categories} cc
                    LEFT JOIN {course} c ON c.category = cc.id
                            {$categorywhere}
                    GROUP BY cc.id";
            $categories = $DB->get_recordset_sql($sql, $categoryparams);
            $fullfetch = [];
            $partfetch = [];
            foreach ($categories as $category) {
                if (!$this->can_add_more_courses_to_category($category->id)) {
                    continue;
                }
                if ($category->coursecount > $limit * 5) {
                    $partfetch[] = $category->id;
                } else if ($category->coursecount > 0) {
                    $fullfetch[] = $category->id;
                }
            }
            $categories->close();

            if (count($fullfetch)) {
                // First up fetch all of the courses in categories where we know that we are going to
                // need the majority of courses.
                [$categoryids, $categoryparams] = $DB->get_in_or_equal($fullfetch, SQL_PARAMS_NAMED, 'lcategory');
                $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
                $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
                $categoryparams['contextlevel'] = CONTEXT_COURSE;
                $sql = "SELECT c.id, c.sortorder, c.visible, c.fullname, c.shortname, c.category $ccselect
                            FROM {course} c
                                $ccjoin
                            WHERE c.category {$categoryids}
                        ORDER BY c.sortorder ASC";
                $coursesrs = $DB->get_recordset_sql($sql, $categoryparams);
                foreach ($coursesrs as $course) {
                    if ($course->id == $SITE->id) {
                        // This should not be necessary, frontpage is not in any category.
                        continue;
                    }
                    if (array_key_exists($course->id, $this->addedcourses)) {
                        // It is probably better to not include the already loaded courses
                        // directly in SQL because inequalities may confuse query optimisers
                        // and may interfere with query caching.
                        continue;
                    }
                    if (!$this->can_add_more_courses_to_category($course->category)) {
                        continue;
                    }
                    context_helper::preload_from_record($course);
                    if (
                        !$course->visible
                        && !is_role_switched($course->id)
                        && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))
                    ) {
                        continue;
                    }
                    $coursenodes[$course->id] = $this->add_course($course);
                }
                $coursesrs->close();
            }

            if (count($partfetch)) {
                // Next we will work our way through the categories where we will likely only need a small
                // proportion of the courses.
                foreach ($partfetch as $categoryid) {
                    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
                    $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
                    $sql = "SELECT c.id, c.sortorder, c.visible, c.fullname, c.shortname, c.category $ccselect
                                FROM {course} c
                                    $ccjoin
                                WHERE c.category = :categoryid
                            ORDER BY c.sortorder ASC";
                    $courseparams = ['categoryid' => $categoryid, 'contextlevel' => CONTEXT_COURSE];
                    $coursesrs = $DB->get_recordset_sql($sql, $courseparams, 0, $limit * 5);
                    foreach ($coursesrs as $course) {
                        if ($course->id == $SITE->id) {
                            // This should not be necessary, frontpage is not in any category.
                            continue;
                        }
                        if (array_key_exists($course->id, $this->addedcourses)) {
                            // It is probably better to not include the already loaded courses
                            // directly in SQL because inequalities may confuse query optimisers
                            // and may interfere with query caching.
                            // This also helps to respect expected $limit on repeated executions.
                            continue;
                        }
                        if (!$this->can_add_more_courses_to_category($course->category)) {
                            break;
                        }
                        context_helper::preload_from_record($course);
                        if (
                            !$course->visible
                            && !is_role_switched($course->id)
                            && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))
                        ) {
                            continue;
                        }
                        $coursenodes[$course->id] = $this->add_course($course);
                    }
                    $coursesrs->close();
                }
            }
        } else {
            // Prepare the SQL to load the courses and their contexts.
            [
                $courseids,
                $courseparams,
            ] = $DB->get_in_or_equal(array_keys($this->addedcourses), SQL_PARAMS_NAMED, 'lc', false);
            $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
            $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
            $courseparams['contextlevel'] = CONTEXT_COURSE;
            $sql = "SELECT c.id, c.sortorder, c.visible, c.fullname, c.shortname, c.category $ccselect
                        FROM {course} c
                            $ccjoin
                        WHERE c.id {$courseids}
                    ORDER BY c.sortorder ASC";
            $coursesrs = $DB->get_recordset_sql($sql, $courseparams);
            foreach ($coursesrs as $course) {
                if ($course->id == $SITE->id) {
                    // Frontpage is not wanted here.
                    continue;
                }
                if ($this->page->course && ($this->page->course->id == $course->id)) {
                    // Don't include the currentcourse in this nodelist - it's displayed in the Current course node.
                    continue;
                }
                context_helper::preload_from_record($course);
                if (
                    !$course->visible
                    && !is_role_switched($course->id)
                    && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))
                ) {
                    continue;
                }
                $coursenodes[$course->id] = $this->add_course($course);
                if (count($coursenodes) >= $limit) {
                    break;
                }
            }
            $coursesrs->close();
        }

        return $coursenodes;
    }

    /**
     * Returns true if more courses can be added to the provided category.
     *
     * @param int|navigation_node|stdClass $category
     * @return bool
     */
    protected function can_add_more_courses_to_category($category) {
        global $CFG;
        $limit = 20;
        if (!empty($CFG->navcourselimit)) {
            $limit = (int)$CFG->navcourselimit;
        }
        if (is_numeric($category)) {
            if (!array_key_exists($category, $this->addedcategories)) {
                return true;
            }
            $coursecount = count($this->addedcategories[$category]->children->type(self::TYPE_COURSE));
        } else if ($category instanceof navigation_node) {
            if (($category->type != self::TYPE_CATEGORY) || ($category->type != self::TYPE_MY_CATEGORY)) {
                return false;
            }
            $coursecount = count($category->children->type(self::TYPE_COURSE));
        } else if (is_object($category) && property_exists($category, 'id')) {
            $coursecount = count($this->addedcategories[$category->id]->children->type(self::TYPE_COURSE));
        }
        return ($coursecount <= $limit);
    }

    /**
     * Loads all categories (top level or if an id is specified for that category)
     *
     * @param int $categoryid The category id to load or null/0 to load all base level categories
     * @param bool $showbasecategories If set to true all base level categories will be loaded as well
     *        as the requested category and any parent categories.
     * @return true|void
     */
    protected function load_all_categories($categoryid = self::LOAD_ROOT_CATEGORIES, $showbasecategories = false) {
        global $CFG, $DB;

        // Check if this category has already been loaded.
        if ($this->allcategoriesloaded || ($categoryid < 1 && $this->is_category_fully_loaded($categoryid))) {
            return true;
        }

        $catcontextsql = context_helper::get_preload_record_columns_sql('ctx');
        $sqlselect = "SELECT cc.*, $catcontextsql
                      FROM {course_categories} cc
                      JOIN {context} ctx ON cc.id = ctx.instanceid";
        $sqlwhere = "WHERE ctx.contextlevel = " . CONTEXT_COURSECAT;
        $sqlorder = "ORDER BY cc.depth ASC, cc.sortorder ASC, cc.id ASC";
        $params = [];

        $categoriestoload = [];
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
        if ($categoryid == self::LOAD_ALL_CATEGORIES) {
            // We are going to load all categories regardless.
        } else if ($categoryid == self::LOAD_ROOT_CATEGORIES) { // Note: can be 0.
            // We are going to load all of the first level categories (categories without parents).
            $sqlwhere .= " AND cc.parent = 0";
        } else if (array_key_exists($categoryid, $this->addedcategories)) {
            // The category itself has been loaded already so we just need to ensure its subcategories have been loaded.
            $addedcategories = $this->addedcategories;
            unset($addedcategories[$categoryid]);
            if (count($addedcategories) > 0) {
                [$sql, $params] = $DB->get_in_or_equal(array_keys($addedcategories), SQL_PARAMS_NAMED, 'parent', false);
                if ($showbasecategories) {
                    // We need to include categories with parent = 0 as well.
                    $sqlwhere .= " AND (cc.parent = :categoryid OR cc.parent = 0) AND cc.parent {$sql}";
                } else {
                    // All we need is categories that match the parent.
                    $sqlwhere .= " AND cc.parent = :categoryid AND cc.parent {$sql}";
                }
            }
            $params['categoryid'] = $categoryid;
        } else {
            // This category hasn't been loaded yet so we need to fetch it, work out its category path
            // and load this category plus all its parents and subcategories.
            $category = $DB->get_record('course_categories', ['id' => $categoryid], 'path', MUST_EXIST);
            $categoriestoload = explode('/', trim($category->path, '/'));
            [$select, $params] = $DB->get_in_or_equal($categoriestoload);
            // We are going to use select twice so double the param.
            $params = array_merge($params, $params);
            $basecategorysql = ($showbasecategories) ? ' OR cc.depth = 1' : '';
            $sqlwhere .= " AND (cc.id {$select} OR cc.parent {$select}{$basecategorysql})";
        }

        $categoriesrs = $DB->get_recordset_sql("$sqlselect $sqlwhere $sqlorder", $params);
        $categories = [];
        foreach ($categoriesrs as $category) {
            // Preload the context.. we'll need it when adding the category in order to format the category name.
            context_helper::preload_from_record($category);

            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            if (array_key_exists($category->id, $this->addedcategories)) {
                // Do nothing, its already been added.
            } else if ($category->parent == '0') {
                // This is a root category lets add it immediately.
                $this->add_category($category, $this->rootnodes['courses']);
            } else if (array_key_exists($category->parent, $this->addedcategories)) {
                // This categories parent has already been added we can add this immediately.
                $this->add_category($category, $this->addedcategories[$category->parent]);
            } else {
                $categories[] = $category;
            }
        }
        $categoriesrs->close();

        // Now we have an array of categories we need to add them to the navigation.
        while (!empty($categories)) {
            $category = reset($categories);
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            if (array_key_exists($category->id, $this->addedcategories)) {
                // Do nothing.
            } else if ($category->parent == '0') {
                $this->add_category($category, $this->rootnodes['courses']);
            } else if (array_key_exists($category->parent, $this->addedcategories)) {
                $this->add_category($category, $this->addedcategories[$category->parent]);
            } else {
                // This category isn't in the navigation and niether is it's parent (yet).
                // We need to go through the category path and add all of its components in order.
                $path = explode('/', trim($category->path, '/'));
                foreach ($path as $catid) {
                    if (!array_key_exists($catid, $this->addedcategories)) {
                        // This category isn't in the navigation yet so add it.
                        $subcategory = $categories[$catid];
                        if ($subcategory->parent == '0') {
                            // Yay we have a root category - this likely means we will now be able
                            // to add categories without problems.
                            $this->add_category($subcategory, $this->rootnodes['courses']);
                        } else if (array_key_exists($subcategory->parent, $this->addedcategories)) {
                            // The parent is in the category (as we'd expect) so add it now.
                            $this->add_category($subcategory, $this->addedcategories[$subcategory->parent]);
                            // Remove the category from the categories array.
                            unset($categories[$catid]);
                        } else {
                            // We should never ever arrive here - if we have then there is a bigger
                            // problem at hand.
                            throw new coding_exception('Category path order is incorrect and/or there are missing categories');
                        }
                    }
                }
            }
            // Remove the category from the categories array now that we know it has been added.
            unset($categories[$category->id]);
        }
        if ($categoryid === self::LOAD_ALL_CATEGORIES) {
            $this->allcategoriesloaded = true;
        }
        // Check if there are any categories to load.
        if (count($categoriestoload) > 0) {
            $readytoloadcourses = [];
            foreach ($categoriestoload as $category) {
                if ($this->can_add_more_courses_to_category($category)) {
                    $readytoloadcourses[] = $category;
                }
            }
            if (count($readytoloadcourses)) {
                $this->load_all_courses($readytoloadcourses);
            }
        }

        // Look for all categories which have been loaded.
        if (!empty($this->addedcategories)) {
            $categoryids = [];
            foreach ($this->addedcategories as $category) {
                if ($this->can_add_more_courses_to_category($category)) {
                    $categoryids[] = $category->key;
                }
            }
            if ($categoryids) {
                [$categoriessql, $params] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);
                $params['limit'] = (!empty($CFG->navcourselimit)) ? $CFG->navcourselimit : 20;
                $sql = "SELECT cc.id, COUNT(c.id) AS coursecount
                          FROM {course_categories} cc
                          JOIN {course} c ON c.category = cc.id
                         WHERE cc.id {$categoriessql}
                      GROUP BY cc.id
                        HAVING COUNT(c.id) > :limit";
                $excessivecategories = $DB->get_records_sql($sql, $params);
                foreach ($categories as &$category) {
                    if (
                        array_key_exists($category->key, $excessivecategories)
                        && !$this->can_add_more_courses_to_category($category)
                    ) {
                        $url = new url('/course/index.php', ['categoryid' => $category->key]);
                        $category->add(get_string('viewallcourses'), $url, self::TYPE_SETTING);
                    }
                }
            }
        }
    }

    /**
     * Adds a structured category to the navigation in the correct order/place
     *
     * @param stdClass $category category to be added in navigation.
     * @param navigation_node $parent parent navigation node
     * @param int $nodetype type of node, if category is under MyHome then it's TYPE_MY_CATEGORY
     * @return void.
     */
    protected function add_category(stdClass $category, navigation_node $parent, $nodetype = self::TYPE_CATEGORY) {
        global $CFG;
        if (array_key_exists($category->id, $this->addedcategories)) {
            return;
        }
        $canview = core_course_category::can_view_category($category);
        $url = $canview ? new url('/course/index.php', ['categoryid' => $category->id]) : null;
        $context = context_helper::get_navigation_filter_context(context_coursecat::instance($category->id));
        $categoryname = $canview ? format_string($category->name, true, ['context' => $context]) :
            get_string('categoryhidden');
        $categorynode = $parent->add($categoryname, $url, $nodetype, $categoryname, $category->id);
        if (!$canview) {
            // User does not have required capabilities to view category.
            $categorynode->display = false;
        } else if (!$category->visible) {
            // Category is hidden but user has capability to view hidden categories.
            $categorynode->hidden = true;
        }
        $this->addedcategories[$category->id] = $categorynode;
    }

    /**
     * Loads the given course into the navigation
     *
     * @param stdClass $course
     * @return navigation_node
     */
    protected function load_course(stdClass $course) {
        global $SITE;
        if ($course->id == $SITE->id) {
            // This is always loaded during initialisation.
            return $this->rootnodes['site'];
        } else if (array_key_exists($course->id, $this->addedcourses)) {
            // The course has already been loaded so return a reference.
            return $this->addedcourses[$course->id];
        } else {
            // Add the course.
            return $this->add_course($course);
        }
    }

    /**
     * Loads all of the courses section into the navigation.
     *
     * This function calls method from current course format, see
     * core_courseformat\base::extend_course_navigation()
     * If course module ($cm) is specified but course format failed to create the node,
     * the activity node is created anyway.
     *
     * By default course formats call the method global_navigation::load_generic_course_sections()
     *
     * @param stdClass $course Database record for the course
     * @param navigation_node $coursenode The course node within the navigation
     * @param null|int $sectionnum If specified load the contents of section with this relative number
     * @param null|cm_info $cm If specified make sure that activity node is created (either
     *    in containg section or by calling load_stealth_activity() )
     */
    protected function load_course_sections(stdClass $course, navigation_node $coursenode, $sectionnum = null, $cm = null) {
        global $CFG, $SITE;
        require_once($CFG->dirroot . '/course/lib.php');
        if (isset($cm->sectionnum)) {
            $sectionnum = $cm->sectionnum;
        }
        if ($sectionnum !== null) {
            $this->includesectionnum = $sectionnum;
        }
        course_get_format($course)->extend_course_navigation($this, $coursenode, $sectionnum, $cm);
        if (isset($cm->id)) {
            $activity = $coursenode->find($cm->id, self::TYPE_ACTIVITY);
            if (empty($activity)) {
                $activity = $this->load_stealth_activity($coursenode, get_fast_modinfo($course));
            }
        }
    }

    /**
     * Generates an array of sections and an array of activities for the given course.
     *
     * This method uses the cache to improve performance and avoid the get_fast_modinfo call
     *
     * @param stdClass $course
     * @return array Array($sections, $activities)
     */
    protected function generate_sections_and_activities(stdClass $course) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        $format = course_get_format($course);

        // For course formats using 'numsections' trim the sections list.
        $courseformatoptions = $format->get_format_options();
        if (isset($courseformatoptions['numsections'])) {
            $sections = array_slice($sections, 0, $courseformatoptions['numsections'] + 1, true);
        }

        $activities = [];

        foreach ($sections as $key => $section) {
            // Clone and unset summary to prevent $SESSION bloat (MDL-31802).
            $sections[$key] = clone($section);
            unset($sections[$key]->summary);
            $sections[$key]->hasactivites = false;
            if (!array_key_exists($section->sectionnum, $modinfo->sections)) {
                continue;
            }
            foreach ($section->get_sequence_cm_infos() as $cm) {
                $activity = new stdClass();
                $activity->id = $cm->id;
                $activity->course = $course->id;
                $activity->section = $section->sectionnum;
                $activity->name = $cm->name;
                $activity->icon = $cm->icon;
                $activity->iconcomponent = $cm->iconcomponent;
                $activity->hidden = (!$cm->visible);
                $activity->modname = $cm->modname;
                $activity->nodetype = navigation_node::NODETYPE_LEAF;
                $activity->onclick = $cm->onclick;
                $url = $cm->url;

                // Activities witout url but with delegated section uses the section url.
                $activity->delegatedsection = $cm->get_delegated_section_info();
                if (empty($cm->url) && $activity->delegatedsection) {
                    $url = $format->get_view_url(
                        $activity->delegatedsection->sectionnum,
                        ['navigation' => true]
                    );
                }

                if (!$url) {
                    $activity->url = null;
                    $activity->display = false;
                } else {
                    $activity->url = $url->out();
                    $activity->display = $cm->is_visible_on_course_page() ? true : false;
                    if (self::module_extends_navigation($cm->modname)) {
                        $activity->nodetype = navigation_node::NODETYPE_BRANCH;
                    }
                }
                $activities[$cm->id] = $activity;
                if ($activity->display) {
                    $sections[$key]->hasactivites = true;
                }
            }
        }

        return [$sections, $activities];
    }

    /**
     * Generically loads the course sections into the course's navigation.
     *
     * @param stdClass $course
     * @param navigation_node $coursenode
     * @return array An array of course section nodes
     */
    public function load_generic_course_sections(stdClass $course, navigation_node $coursenode) {
        global $CFG, $DB, $USER, $SITE;
        require_once($CFG->dirroot . '/course/lib.php');

        [$sections, $activities] = $this->generate_sections_and_activities($course);

        $navigationsections = [];
        foreach ($sections as $sectionid => $section) {
            if ($course->id == $SITE->id) {
                $this->load_section_activities_navigation($coursenode, $section, $activities);
                continue;
            }

            if (
                !$section->uservisible
                || (
                    !$this->showemptysections
                    && !$section->hasactivites
                    && $this->includesectionnum !== $section->section
                )
            ) {
                continue;
            }

            // Delegated sections are added from the activity node.
            if ($section->get_component_instance()) {
                continue;
            }

            $navigationsections[$sectionid] = $this->load_section_navigation(
                parentnode: $coursenode,
                section: $section,
                activitiesdata: $activities,
            );
        }
        return $navigationsections;
    }

    /**
     * Returns true if the section is included in the breadcrumb.
     *
     * @param section_info $section
     * @param url|null $sectionurl
     * @return bool
     */
    protected function is_section_in_breadcrumb(section_info $section, ?url $sectionurl): bool {
        // Ajax requests uses includesectionnum as param.
        if ($this->includesectionnum !== false && $this->includesectionnum == $section->sectionnum) {
            return true;
        }

        // If we are in a section page, we need to check for any child section.
        $checkchildrenurls = false;
        $format = null;
        if ($sectionurl && $this->page->url->compare($sectionurl, URL_MATCH_BASE)) {
            $checkchildrenurls = true;
            $format = course_get_format($section->course);
        }

        // Activities can have delegated sections that acts as a child section.
        foreach ($section->get_sequence_cm_infos() as $cm) {
            $delegatedsection = $cm->get_delegated_section_info();
            if (!$delegatedsection) {
                continue;
            }
            // Check if the child node is requested via Ajax.
            if ($this->includesectionnum == $delegatedsection->sectionnum) {
                return true;
            }

            if ($checkchildrenurls) {
                $childurl = $format->get_view_url($delegatedsection, ['navigation' => true]);
                if ($childurl && $this->page->url->compare($childurl, URL_MATCH_EXACT)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Loads a section into the navigation structure.
     *
     * @param navigation_node $parentnode
     * @param section_info $section
     * @param stdClass[] $activitiesdata Array of objects containing activities data indexed by cmid.
     * @return navigation_node the section navigaiton node
     */
    public function load_section_navigation($parentnode, $section, $activitiesdata): navigation_node {
        $format = course_get_format($section->course);
        $sectionname = $format->get_section_name($section);
        $url = $format->get_view_url($section, ['navigation' => true]);

        $sectionnode = $parentnode->add(
            text: $sectionname,
            action: $url,
            type: navigation_node::TYPE_SECTION,
            key: $section->id,
            icon: new pix_icon('i/section', ''),
        );
        $sectionnode->nodetype = navigation_node::NODETYPE_BRANCH;
        $sectionnode->hidden = (!$section->visible || !$section->available);
        $sectionnode->add_attribute('data-section-name-for', $section->id);

        // Sections content are usually loaded via ajax but the sections from the requested breadcrumb.
        if ($this->is_section_in_breadcrumb($section, $url)) {
            $this->load_section_activities_navigation($sectionnode, $section, $activitiesdata);
        }
        return $sectionnode;
    }

    /**
     * Loads the activities for a section into the navigation structure.
     *
     * This method is called from global_navigation::load_section_navigation(),
     * It is not intended to be called directly.
     *
     * @param navigation_node $sectionnode
     * @param section_info $section
     * @param stdClass[] $activitiesdata Array of objects containing activities data indexed by cmid.
     */
    protected function load_section_activities_navigation(
        navigation_node $sectionnode,
        section_info $section,
        array $activitiesdata
    ): array {
        global $CFG, $SITE;

        $activitynodes = [];
        if (empty($activitiesdata)) {
            return $activitynodes;
        }

        foreach ($section->get_sequence_cm_infos() as $cm) {
            $activitydata = $activitiesdata[$cm->id];

            // If activity is a delegated section, load a section node instead of the activity one.
            if ($activitydata->delegatedsection) {
                $activitynodes[$activitydata->id] = $this->load_section_navigation(
                    parentnode: $sectionnode,
                    section: $activitydata->delegatedsection,
                    activitiesdata: $activitiesdata,
                );
                continue;
            }

            $activitynodes[$activitydata->id] = $this->load_activity_navigation($sectionnode, $activitydata);
        }

        return $activitynodes;
    }

    /**
     * Loads an activity into the navigation structure.
     *
     * This method is called from global_navigation::load_section_activities_navigation(),
     * It is not intended to be called directly.
     *
     * @param navigation_node $sectionnode
     * @param stdClass $activitydata The acitivy navigation data generated from generate_sections_and_activities
     * @return navigation_node
     */
    protected function load_activity_navigation(
        navigation_node $sectionnode,
        stdClass $activitydata,
    ): navigation_node {
        global $SITE, $CFG;

        $showactivities = ($activitydata->course != $SITE->id || !empty($CFG->navshowfrontpagemods));

        $icon = new pix_icon(
            $activitydata->icon ?: 'monologo',
            get_string('modulename', $activitydata->modname),
            $activitydata->icon ? $activitydata->iconcomponent : $activitydata->modname,
        );

        // Prepare the default name and url for the node.
        $displaycontext = context_helper::get_navigation_filter_context(context_module::instance($activitydata->id));
        $activityname = format_string($activitydata->name, true, ['context' => $displaycontext]);

        $activitynode = $sectionnode->add(
            text: $activityname,
            action: $this->get_activity_action($activitydata, $activityname),
            type: navigation_node::TYPE_ACTIVITY,
            key: $activitydata->id,
            icon: $icon,
        );
        $activitynode->title(get_string('modulename', $activitydata->modname));
        $activitynode->hidden = $activitydata->hidden;
        $activitynode->display = $showactivities && $activitydata->display;
        $activitynode->nodetype = $activitydata->nodetype;

        return $activitynode;
    }

    /**
     * Returns the action for the activity.
     *
     * @param stdClass $activitydata The acitivy navigation data generated from generate_sections_and_activities
     * @param string $activityname
     * @return url|action_link
     */
    protected function get_activity_action(stdClass $activitydata, string $activityname): url|action_link {
        // A static counter for JS function naming.
        static $legacyonclickcounter = 0;

        $action = new url($activitydata->url);

        // Check if the onclick property is set (puke!).
        if (!empty($activitydata->onclick)) {
            // Increment the counter so that we have a unique number.
            $legacyonclickcounter++;
            // Generate the function name we will use.
            $functionname = 'legacy_activity_onclick_handler_' . $legacyonclickcounter;
            $propogrationhandler = '';
            // Check if we need to cancel propogation. Remember inline onclick
            // events would return false if they wanted to prevent propogation and the
            // default action.
            if (strpos($activitydata->onclick, 'return false')) {
                $propogrationhandler = 'e.halt();';
            }
            // Decode the onclick - it has already been encoded for display (puke).
            $onclick = htmlspecialchars_decode($activitydata->onclick, ENT_QUOTES);
            // Build the JS function the click event will call.
            $jscode = "function {$functionname}(e) { $propogrationhandler $onclick }";
            $this->page->requires->js_amd_inline($jscode);
            // Override the default url with the new action link.
            $action = new action_link($action, $activityname, new component_action('click', $functionname));
        }
        return $action;
    }

    /**
     * Loads all of the activities for a section into the navigation structure.
     *
     * @param navigation_node $sectionnode
     * @param int $sectionnumber
     * @param array $activities An array of activites as returned by {@link global_navigation::generate_sections_and_activities()}
     * @param stdClass $course The course object the section and activities relate to.
     * @return array Array of activity nodes
     */
    #[\core\attribute\deprecated(
        replacement: 'load_section_activities_navigation',
        since: '4.5',
        mdl: 'MDL-82845',
    )]
    protected function load_section_activities(navigation_node $sectionnode, $sectionnumber, array $activities, $course = null) {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
        if (!is_object($course)) {
            $activity = reset($activities);
            $courseid = $activity->course;
        } else {
            $courseid = $course->id;
        }
        $sectionifo = get_fast_modinfo($courseid)->get_section_info($sectionnumber);
        return $this->load_section_activities_navigation($sectionnode, $sectionifo, $activities);
    }
    /**
     * Loads a stealth module from unavailable section
     * @param navigation_node $coursenode
     * @param stdClass|course_modinfo $modinfo
     * @return navigation_node or null if not accessible
     */
    protected function load_stealth_activity(navigation_node $coursenode, $modinfo) {
        if (empty($modinfo->cms[$this->page->cm->id])) {
            return null;
        }
        $cm = $modinfo->cms[$this->page->cm->id];
        if ($cm->icon) {
            $icon = new pix_icon($cm->icon, get_string('modulename', $cm->modname), $cm->iconcomponent);
        } else {
            $icon = new pix_icon('monologo', get_string('modulename', $cm->modname), $cm->modname);
        }
        $url = $cm->url;
        $activitynode = $coursenode->add(format_string($cm->name), $url, navigation_node::TYPE_ACTIVITY, null, $cm->id, $icon);
        $activitynode->title(get_string('modulename', $cm->modname));
        $activitynode->hidden = (!$cm->visible);
        if (!$cm->is_visible_on_course_page()) {
            // Do not show any error here, let the page handle exception that activity is not visible for the current user.
            // Also there may be no exception at all in case when teacher is logged in as student.
            $activitynode->display = false;
        } else if (!$url) {
            // Don't show activities that don't have links!
            $activitynode->display = false;
        } else if (self::module_extends_navigation($cm->modname)) {
            $activitynode->nodetype = navigation_node::NODETYPE_BRANCH;
        }
        return $activitynode;
    }
    /**
     * Loads the navigation structure for the given activity into the activities node.
     *
     * This method utilises a callback within the modules lib.php file to load the
     * content specific to activity given.
     *
     * The callback is a method: {modulename}_extend_navigation()
     * Examples:
     *  * {@link forum_extend_navigation()}
     *  * {@link workshop_extend_navigation()}
     *
     * @param cm_info|stdClass $cm
     * @param stdClass $course
     * @param navigation_node $activity
     * @return bool
     */
    protected function load_activity($cm, stdClass $course, navigation_node $activity) {
        global $CFG, $DB;

        // Make sure we have a $cm from get_fast_modinfo as this contains activity access details.
        if (!($cm instanceof cm_info)) {
            $modinfo = get_fast_modinfo($course);
            $cm = $modinfo->get_cm($cm->id);
        }
        $activity->nodetype = navigation_node::NODETYPE_LEAF;
        $activity->make_active();
        $file = $CFG->dirroot . '/mod/' . $cm->modname . '/lib.php';
        $function = $cm->modname . '_extend_navigation';

        if (file_exists($file)) {
            require_once($file);
            if (function_exists($function)) {
                $activtyrecord = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
                $function($activity, $course, $activtyrecord, $cm);
            }
        }

        // Allow the active advanced grading method plugin to append module navigation.
        $featuresfunc = $cm->modname . '_supports';
        if (function_exists($featuresfunc) && $featuresfunc(FEATURE_ADVANCED_GRADING)) {
            require_once($CFG->dirroot . '/grade/grading/lib.php');
            $gradingman = get_grading_manager($cm->context, 'mod_' . $cm->modname);
            $gradingman->extend_navigation($this, $activity);
        }

        return $activity->has_children();
    }
    /**
     * Loads user specific information into the navigation in the appropriate place.
     *
     * If no user is provided the current user is assumed.
     *
     * @param stdClass $user
     * @param bool $forceforcontext probably force something to be loaded somewhere (ask SamH if not sure what this means)
     * @return bool
     */
    protected function load_for_user($user = null, $forceforcontext = false) {
        global $DB, $CFG, $USER, $SITE;

        require_once($CFG->dirroot . '/course/lib.php');

        if ($user === null) {
            // We can't require login here but if the user isn't logged in we don't want to show anything.
            if (!isloggedin() || isguestuser()) {
                return false;
            }
            $user = $USER;
        } else if (!is_object($user)) {
            // If the user is not an object then get them from the database.
            $select = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT u.*, $select
                      FROM {user} u
                      JOIN {context} ctx ON u.id = ctx.instanceid
                     WHERE u.id = :userid AND
                           ctx.contextlevel = :contextlevel";
            $user = $DB->get_record_sql($sql, ['userid' => (int)$user, 'contextlevel' => CONTEXT_USER], MUST_EXIST);
            context_helper::preload_from_record($user);
        }

        $iscurrentuser = ($user->id == $USER->id);

        $usercontext = context_user::instance($user->id);

        // Get the course set against the page, by default this will be the site.
        $course = $this->page->course;
        $baseargs = ['id' => $user->id];
        if ($course->id != $SITE->id && (!$iscurrentuser || $forceforcontext)) {
            $coursenode = $this->add_course($course, false, self::COURSE_CURRENT);
            $baseargs['course'] = $course->id;
            $coursecontext = context_course::instance($course->id);
            $issitecourse = false;
        } else {
            // Load all categories and get the context for the system.
            $coursecontext = context_system::instance();
            $issitecourse = true;
        }

        // Create a node to add user information under.
        $usersnode = null;
        if (!$issitecourse) {
            // Not the current user so add it to the participants node for the current course.
            $usersnode = $coursenode->get('participants', navigation_node::TYPE_CONTAINER);
            $userviewurl = new url('/user/view.php', $baseargs);
        } else if ($USER->id != $user->id) {
            // This is the site so add a users node to the root branch.
            $usersnode = $this->rootnodes['users'];
            if (course_can_view_participants($coursecontext)) {
                $usersnode->action = new url('/user/index.php', ['id' => $course->id]);
            }
            $userviewurl = new url('/user/profile.php', $baseargs);
        }
        if (!$usersnode) {
            // We should NEVER get here, if the course hasn't been populated
            // with a participants node then the navigaiton either wasn't generated
            // for it (you are missing a require_login or set_context call) or
            // you don't have access.... in the interests of no leaking informatin
            // we simply quit...
            return false;
        }
        // Add a branch for the current user.
        // Only reveal user details if $user is the current user, or a user to which the current user has access.
        $viewprofile = true;
        if (!$iscurrentuser) {
            require_once($CFG->dirroot . '/user/lib.php');
            if ($this->page->context->contextlevel == CONTEXT_USER && !has_capability('moodle/user:viewdetails', $usercontext)) {
                $viewprofile = false;
            } else if ($this->page->context->contextlevel != CONTEXT_USER && !user_can_view_profile($user, $course, $usercontext)) {
                $viewprofile = false;
            }
            if (!$viewprofile) {
                $viewprofile = user_can_view_profile($user, null, $usercontext);
            }
        }

        // Now, conditionally add the user node.
        if ($viewprofile) {
            $canseefullname = has_capability('moodle/site:viewfullnames', $coursecontext);
            $usernode = $usersnode->add(fullname($user, $canseefullname), $userviewurl, self::TYPE_USER, null, 'user' . $user->id);
        } else {
            $usernode = $usersnode->add(get_string('user'));
        }

        if ($this->page->context->contextlevel == CONTEXT_USER && $user->id == $this->page->context->instanceid) {
            $usernode->make_active();
        }

        // Add user information to the participants or user node.
        if ($issitecourse) {
            // If the user is the current user or has permission to view the details of the requested
            // user than add a view profile link.
            if (
                $iscurrentuser || has_capability('moodle/user:viewdetails', $coursecontext) ||
                    has_capability('moodle/user:viewdetails', $usercontext)
            ) {
                if ($issitecourse || ($iscurrentuser && !$forceforcontext)) {
                    $usernode->add(get_string('viewprofile'), new url('/user/profile.php', $baseargs));
                } else {
                    $usernode->add(get_string('viewprofile'), new url('/user/view.php', $baseargs));
                }
            }

            // Add blog nodes.
            if (!empty($CFG->enableblogs)) {
                if (!$this->cache->cached('userblogoptions' . $user->id)) {
                    require_once($CFG->dirroot . '/blog/lib.php');
                    // Get all options for the user.
                    $options = blog_get_options_for_user($user);
                    $this->cache->set('userblogoptions' . $user->id, $options);
                } else {
                    $options = $this->cache->{'userblogoptions' . $user->id};
                }

                if (count($options) > 0) {
                    $blogs = $usernode->add(get_string('blogs', 'blog'), null, navigation_node::TYPE_CONTAINER);
                    foreach ($options as $type => $option) {
                        if ($type == "rss") {
                            $blogs->add(
                                $option['string'],
                                $option['link'],
                                settings_navigation::TYPE_SETTING,
                                null,
                                null,
                                new pix_icon('i/rss', '')
                            );
                        } else {
                            $blogs->add($option['string'], $option['link']);
                        }
                    }
                }
            }

            // Add the messages link.
            // It is context based so can appear in the user's profile and in course participants information.
            if (!empty($CFG->messaging)) {
                $messageargs = ['user1' => $USER->id];
                if ($USER->id != $user->id) {
                    $messageargs['user2'] = $user->id;
                }
                $url = new url('/message/index.php', $messageargs);
                $usernode->add(get_string('messages', 'message'), $url, self::TYPE_SETTING, null, 'messages');
            }

            // Add the "My private files" link.
            // This link doesn't have a unique display for course context so only display it under the user's profile.
            if ($issitecourse && $iscurrentuser && has_capability('moodle/user:manageownfiles', $usercontext)) {
                $url = new url('/user/files.php');
                $usernode->add(get_string('privatefiles'), $url, self::TYPE_SETTING, null, 'privatefiles');
            }

            // Add a node to view the users notes if permitted.
            if (
                !empty($CFG->enablenotes) &&
                    has_any_capability(['moodle/notes:manage', 'moodle/notes:view'], $coursecontext)
            ) {
                $url = new url('/notes/index.php', ['user' => $user->id]);
                if ($coursecontext->instanceid != SITEID) {
                    $url->param('course', $coursecontext->instanceid);
                }
                $usernode->add(get_string('notes', 'notes'), $url);
            }

            // Show the grades node.
            if (($issitecourse && $iscurrentuser) || has_capability('moodle/user:viewdetails', $usercontext)) {
                require_once($CFG->dirroot . '/user/lib.php');
                // Set the grades node to link to the "Grades" page.
                if ($course->id == SITEID) {
                    $url = user_mygrades_url($user->id, $course->id);
                } else { // Otherwise we are in a course and should redirect to the user grade report (Activity report version).
                    $url = new url('/course/user.php', ['mode' => 'grade', 'id' => $course->id, 'user' => $user->id]);
                }
                if ($USER->id != $user->id) {
                    $usernode->add(get_string('grades', 'grades'), $url, self::TYPE_SETTING, null, 'usergrades');
                } else {
                    $usernode->add(get_string('grades', 'grades'), $url);
                }
            }

            // If the user is the current user add the repositories for the current user.
            $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
            if (
                !$iscurrentuser &&
                    $course->id == $SITE->id &&
                    has_capability('moodle/user:viewdetails', $usercontext) &&
                    (!in_array('mycourses', $hiddenfields) || has_capability('moodle/user:viewhiddendetails', $coursecontext))
            ) {
                // Add view grade report is permitted.
                $reports = component::get_plugin_list('gradereport');
                arsort($reports); // User is last, we want to test it first.

                $userscourses = enrol_get_users_courses($user->id, false, '*');
                $userscoursesnode = $usernode->add(get_string('courses'));

                $count = 0;
                foreach ($userscourses as $usercourse) {
                    if ($count === (int)$CFG->navcourselimit) {
                        $url = new url('/user/profile.php', ['id' => $user->id, 'showallcourses' => 1]);
                        $userscoursesnode->add(get_string('showallcourses'), $url);
                        break;
                    }
                    $count++;
                    $usercoursecontext = context_course::instance($usercourse->id);
                    $usercourseshortname = format_string($usercourse->shortname, true, ['context' => $usercoursecontext]);
                    $usercoursenode = $userscoursesnode->add($usercourseshortname, new url(
                        '/user/view.php',
                        ['id' => $user->id, 'course' => $usercourse->id]
                    ), self::TYPE_CONTAINER);

                    $gradeavailable = has_capability('moodle/grade:view', $usercoursecontext);
                    if (!$gradeavailable && !empty($usercourse->showgrades) && is_array($reports) && !empty($reports)) {
                        foreach ($reports as $plugin => $plugindir) {
                            if (has_capability('gradereport/' . $plugin . ':view', $usercoursecontext)) {
                                // Stop when the first visible plugin is found.
                                $gradeavailable = true;
                                break;
                            }
                        }
                    }

                    if ($gradeavailable) {
                        $url = new url('/grade/report/index.php', ['id' => $usercourse->id]);
                        $usercoursenode->add(
                            get_string('grades'),
                            $url,
                            self::TYPE_SETTING,
                            null,
                            null,
                            new pix_icon('i/grades', '')
                        );
                    }

                    // Add a node to view the users notes if permitted.
                    if (
                        !empty($CFG->enablenotes) &&
                            has_any_capability(['moodle/notes:manage', 'moodle/notes:view'], $usercoursecontext)
                    ) {
                        $url = new url('/notes/index.php', ['user' => $user->id, 'course' => $usercourse->id]);
                        $usercoursenode->add(get_string('notes', 'notes'), $url, self::TYPE_SETTING);
                    }

                    if (can_access_course($usercourse, $user->id, '', true)) {
                        $usercoursenode->add(get_string('entercourse'), new url(
                            '/course/view.php',
                            ['id' => $usercourse->id]
                        ), self::TYPE_SETTING, null, null, new pix_icon('i/course', ''));
                    }

                    $reporttab = $usercoursenode->add(get_string('activityreports'));

                    $reportfunctions = get_plugin_list_with_function('report', 'extend_navigation_user', 'lib.php');
                    foreach ($reportfunctions as $reportfunction) {
                        $reportfunction($reporttab, $user, $usercourse);
                    }

                    $reporttab->trim_if_empty();
                }
            }

            // Let plugins hook into user navigation.
            $pluginsfunction = get_plugins_with_function('extend_navigation_user', 'lib.php');
            foreach ($pluginsfunction as $plugintype => $plugins) {
                if ($plugintype != 'report') {
                    foreach ($plugins as $pluginfunction) {
                        $pluginfunction($usernode, $user, $usercontext, $course, $coursecontext);
                    }
                }
            }
        }
        return true;
    }

    /**
     * This method simply checks to see if a given module can extend the navigation.
     *
     * @todo (MDL-25290) A shared caching solution should be used to save details on what extends navigation.
     *
     * @param string $modname
     * @return bool
     */
    public static function module_extends_navigation($modname) {
        global $CFG;
        static $extendingmodules = [];
        if (!array_key_exists($modname, $extendingmodules)) {
            $extendingmodules[$modname] = false;
            $file = $CFG->dirroot . '/mod/' . $modname . '/lib.php';
            if (file_exists($file)) {
                $function = $modname . '_extend_navigation';
                require_once($file);
                $extendingmodules[$modname] = (function_exists($function));
            }
        }
        return $extendingmodules[$modname];
    }
    /**
     * Extends the navigation for the given user.
     *
     * @param stdClass $user A user from the database
     */
    public function extend_for_user($user) {
        $this->extendforuser[] = $user;
    }

    /**
     * Returns all of the users the navigation is being extended for
     *
     * @return array An array of extending users.
     */
    public function get_extending_users() {
        return $this->extendforuser;
    }
    /**
     * Adds the given course to the navigation structure.
     *
     * @param stdClass $course
     * @param bool $forcegeneric
     * @param bool $ismycourse
     * @return navigation_node
     */
    public function add_course(stdClass $course, $forcegeneric = false, $coursetype = self::COURSE_OTHER) {
        global $CFG, $SITE;

        // We found the course... we can return it now.
        if (!$forcegeneric && array_key_exists($course->id, $this->addedcourses)) {
            return $this->addedcourses[$course->id];
        }

        $coursecontext = context_course::instance($course->id);

        if ($coursetype != self::COURSE_MY && $coursetype != self::COURSE_CURRENT && $course->id != $SITE->id) {
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            if (is_role_switched($course->id)) {
                // User has to be able to access course in order to switch, let's skip the visibility test here.
            } else if (!core_course_category::can_view_course_info($course)) {
                return false;
            }
        }

        $issite = ($course->id == $SITE->id);
        $displaycontext = context_helper::get_navigation_filter_context($coursecontext);
        $shortname = format_string($course->shortname, true, ['context' => $displaycontext]);
        $fullname = format_string($course->fullname, true, ['context' => $displaycontext]);
        // This is the name that will be shown for the course.
        $coursename = empty($CFG->navshowfullcoursenames) ? $shortname : $fullname;

        if ($coursetype == self::COURSE_CURRENT) {
            if ($coursenode = $this->rootnodes['mycourses']->find($course->id, self::TYPE_COURSE)) {
                return $coursenode;
            } else {
                $coursetype = self::COURSE_OTHER;
            }
        }

        // Can the user expand the course to see its content.
        $canexpandcourse = true;
        if ($issite) {
            $parent = $this;
            $url = null;
            if (empty($CFG->usesitenameforsitepages)) {
                $coursename = get_string('sitepages');
            }
        } else if ($coursetype == self::COURSE_CURRENT) {
            $parent = $this->rootnodes['currentcourse'];
            $url = new url('/course/view.php', ['id' => $course->id]);
            $canexpandcourse = $this->can_expand_course($course);
        } else if ($coursetype == self::COURSE_MY && !$forcegeneric) {
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            if (
                !empty($CFG->navshowmycoursecategories)
                && ($parent = $this->rootnodes['mycourses']->find($course->category, self::TYPE_MY_CATEGORY))
            ) {
                // Nothing to do here the above statement set $parent to the category within mycourses.
            } else {
                $parent = $this->rootnodes['mycourses'];
            }
            $url = new url('/course/view.php', ['id' => $course->id]);
        } else {
            $parent = $this->rootnodes['courses'];
            $url = new url('/course/view.php', ['id' => $course->id]);
            // They can only expand the course if they can access it.
            $canexpandcourse = $this->can_expand_course($course);
            if (!empty($course->category) && $this->show_categories($coursetype == self::COURSE_MY)) {
                if (!$this->is_category_fully_loaded($course->category)) {
                    // We need to load the category structure for this course.
                    $this->load_all_categories($course->category, false);
                }
                if (array_key_exists($course->category, $this->addedcategories)) {
                    $parent = $this->addedcategories[$course->category];
                    // This could lead to the course being created so we should check whether it is the case again.
                    if (!$forcegeneric && array_key_exists($course->id, $this->addedcourses)) {
                        return $this->addedcourses[$course->id];
                    }
                }
            }
        }

        $coursenode = $parent->add($coursename, $url, self::TYPE_COURSE, $shortname, $course->id, new pix_icon('i/course', ''));
        $coursenode->showinflatnavigation = $coursetype == self::COURSE_MY;

        $coursenode->hidden = (!$course->visible);
        $coursenode->title(format_string($course->fullname, true, ['context' => $displaycontext, 'escape' => false]));
        if ($canexpandcourse) {
            // This course can be expanded by the user, make it a branch to make the system aware that its expandable by ajax.
            $coursenode->nodetype = self::NODETYPE_BRANCH;
            $coursenode->isexpandable = true;
        } else {
            $coursenode->nodetype = self::NODETYPE_LEAF;
            $coursenode->isexpandable = false;
        }
        if (!$forcegeneric) {
            $this->addedcourses[$course->id] = $coursenode;
        }

        return $coursenode;
    }

    /**
     * Returns a cache instance to use for the expand course cache.
     * @return session_cache
     */
    protected function get_expand_course_cache() {
        if ($this->cacheexpandcourse === null) {
            $this->cacheexpandcourse = cache::make('core', 'navigation_expandcourse');
        }
        return $this->cacheexpandcourse;
    }

    /**
     * Checks if a user can expand a course in the navigation.
     *
     * We use a cache here because in order to be accurate we need to call can_access_course which is a costly function.
     * Because this functionality is basic + non-essential and because we lack good event triggering this cache
     * permits stale data.
     * In the situation the user is granted access to a course after we've initialised this session cache the cache
     * will be stale.
     * It is brought up to date in only one of two ways.
     *   1. The user logs out and in again.
     *   2. The user browses to the course they've just being given access to.
     *
     * Really all this controls is whether the node is shown as expandable or not. It is uber un-important.
     *
     * @param stdClass $course
     * @return bool
     */
    protected function can_expand_course($course) {
        $cache = $this->get_expand_course_cache();
        $canexpand = $cache->get($course->id);
        if ($canexpand === false) {
            $canexpand = isloggedin() && can_access_course($course, null, '', true);
            $canexpand = (int)$canexpand;
            $cache->set($course->id, $canexpand);
        }
        return ($canexpand === 1);
    }

    /**
     * Returns true if the category has already been loaded as have any child categories
     *
     * @param int $categoryid
     * @return bool
     */
    protected function is_category_fully_loaded($categoryid) {
        return (
            array_key_exists($categoryid, $this->addedcategories)
            && (
                $this->allcategoriesloaded
                || $this->addedcategories[$categoryid]->children->count() > 0
            )
        );
    }

    /**
     * Adds essential course nodes to the navigation for the given course.
     *
     * This method adds nodes such as reports, blogs and participants
     *
     * @param navigation_node $coursenode
     * @param stdClass $course
     * @return bool returns true on successful addition of a node.
     */
    public function add_course_essentials($coursenode, stdClass $course) {
        global $CFG, $SITE;
        require_once($CFG->dirroot . '/course/lib.php');

        if ($course->id == $SITE->id) {
            return $this->add_front_page_course_essentials($coursenode, $course);
        }

        if (
            $coursenode == false
            || !($coursenode instanceof navigation_node)
            || $coursenode->get('participants', navigation_node::TYPE_CONTAINER)
        ) {
            return true;
        }

        $navoptions = course_get_user_navigation_options($this->page->context, $course);

        // Participants.
        if ($navoptions->participants) {
            $participants = $coursenode->add(
                get_string('participants'),
                new url('/user/index.php?id=' . $course->id),
                self::TYPE_CONTAINER,
                get_string('participants'),
                'participants',
                new pix_icon('i/users', '')
            );

            if ($navoptions->blogs) {
                $blogsurls = new url('/blog/index.php');
                if ($currentgroup = groups_get_course_group($course, true)) {
                    $blogsurls->param('groupid', $currentgroup);
                } else {
                    $blogsurls->param('courseid', $course->id);
                }
                $participants->add(get_string('blogscourse', 'blog'), $blogsurls->out(), self::TYPE_SETTING, null, 'courseblogs');
            }

            if ($navoptions->notes) {
                $participants->add(
                    get_string('notes', 'notes'),
                    new url('/notes/index.php', ['filtertype' => 'course', 'filterselect' => $course->id]),
                    self::TYPE_SETTING,
                    null,
                    'currentcoursenotes',
                );
            }
        } else if (count($this->extendforuser) > 0) {
            $coursenode->add(get_string('participants'), null, self::TYPE_CONTAINER, get_string('participants'), 'participants');
        } else if ($siteparticipantsnode = $this->rootnodes['site']->get('participants', self::TYPE_CUSTOM)) {
            // The participants node was added for the site, but cannot be viewed inside the course itself, so remove.
            $siteparticipantsnode->remove();
        }

        // Badges.
        if ($navoptions->badges) {
            $url = new url('/badges/index.php', ['type' => 2, 'id' => $course->id]);

            $coursenode->add(
                get_string('coursebadges', 'badges'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'badgesview',
                new pix_icon('i/badge', get_string('coursebadges', 'badges'))
            );
        }

        // Check access to the course and competencies page.
        if ($navoptions->competencies) {
            // Just a link to course competency.
            $title = get_string('competencies', 'core_competency');
            $path = new url("/admin/tool/lp/coursecompetencies.php", ['courseid' => $course->id]);
            $coursenode->add(
                $title,
                $path,
                navigation_node::TYPE_SETTING,
                null,
                'competencies',
                new pix_icon('i/competencies', '')
            );
        }
        if ($navoptions->grades) {
            $url = new url('/grade/report/index.php', ['id' => $course->id]);
            $gradenode = $coursenode->add(
                get_string('grades'),
                $url,
                self::TYPE_SETTING,
                null,
                'grades',
                new pix_icon('i/grades', '')
            );
            // If the page type matches the grade part, then make the nav drawer grade node (incl. all sub pages) active.
            if ($this->page->context->contextlevel < CONTEXT_MODULE && strpos($this->page->pagetype, 'grade-') === 0) {
                $gradenode->make_active();
            }
        }

        // Add link for configuring communication.
        if ($navoptions->communication) {
            $url = new url('/communication/configure.php', [
                'contextid' => \core\context\course::instance($course->id)->id,
                'instanceid' => $course->id,
                'instancetype' => 'coursecommunication',
                'component' => 'core_course',
            ]);
            $coursenode->add(
                get_string('communication', 'communication'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'communication'
            );
        }

        if ($navoptions->overview) {
            $coursenode->add(
                text: get_string('activities'),
                action: new url('/course/overview.php', ['id' => $course->id]),
                type: self::TYPE_CONTAINER,
                key: 'courseoverview',
                icon: new pix_icon('i/info', ''),
            );
        }

        return true;
    }
    /**
     * This generates the structure of the course that won't be generated when
     * the modules and sections are added.
     *
     * Things such as the reports branch, the participants branch, blogs... get
     * added to the course node by this method.
     *
     * @param navigation_node $coursenode
     * @param stdClass $course
     * @return bool True for successfull generation
     */
    public function add_front_page_course_essentials(navigation_node $coursenode, stdClass $course) {
        global $CFG, $USER, $COURSE, $SITE;
        require_once($CFG->dirroot . '/course/lib.php');

        if ($coursenode == false || $coursenode->get('frontpageloaded', navigation_node::TYPE_CUSTOM)) {
            return true;
        }

        $systemcontext = context_system::instance();
        $navoptions = course_get_user_navigation_options($systemcontext, $course);

        // Hidden node that we use to determine if the front page navigation is loaded.
        // This required as there are not other guaranteed nodes that may be loaded.
        $coursenode->add('frontpageloaded', null, self::TYPE_CUSTOM, null, 'frontpageloaded')->display = false;

        // Add My courses to the site pages within the navigation structure so the block can read it.
        $coursenode->add(get_string('mycourses'), new url('/my/courses.php'), self::TYPE_CUSTOM, null, 'mycourses');

        // Participants.
        if ($navoptions->participants) {
            $coursenode->add(
                get_string('participants'),
                new url('/user/index.php?id=' . $course->id),
                self::TYPE_CUSTOM,
                get_string('participants'),
                'participants'
            );
        }

        // Blogs.
        if ($navoptions->blogs) {
            $blogsurls = new url('/blog/index.php');
            $coursenode->add(get_string('blogssite', 'blog'), $blogsurls->out(), self::TYPE_SYSTEM, null, 'siteblog');
        }

        $filterselect = 0;

        // Badges.
        if ($navoptions->badges) {
            $url = new url($CFG->wwwroot . '/badges/index.php', ['type' => 1]);
            $coursenode->add(get_string('sitebadges', 'badges'), $url, navigation_node::TYPE_CUSTOM);
        }

        // Notes.
        if ($navoptions->notes) {
            $coursenode->add(get_string('notes', 'notes'), new url(
                '/notes/index.php',
                ['filtertype' => 'course', 'filterselect' => $filterselect]
            ), self::TYPE_SETTING, null, 'notes');
        }

        // Tags.
        if ($navoptions->tags) {
            $node = $coursenode->add(
                get_string('tags', 'tag'),
                new url('/tag/search.php'),
                self::TYPE_SETTING,
                null,
                'tags'
            );
        }

        // Search.
        if ($navoptions->search) {
            $node = $coursenode->add(
                get_string('search', 'search'),
                new url('/search/index.php'),
                self::TYPE_SETTING,
                null,
                'search'
            );
        }

        if (isloggedin()) {
            $usercontext = context_user::instance($USER->id);
            if (has_capability('moodle/user:manageownfiles', $usercontext)) {
                $url = new url('/user/files.php');
                $node = $coursenode->add(
                    get_string('privatefiles'),
                    $url,
                    self::TYPE_SETTING,
                    null,
                    'privatefiles',
                    new pix_icon('i/privatefiles', '')
                );
                $node->display = false;
                $node->showinflatnavigation = true;
                $node->mainnavonly = true;
            }
        }

        if (isloggedin()) {
            $context = $this->page->context;
            switch ($context->contextlevel) {
                case CONTEXT_COURSECAT:
                    // OK, expected context level.
                    break;
                case CONTEXT_COURSE:
                    // OK, expected context level if not on frontpage.
                    if ($COURSE->id != $SITE->id) {
                        break;
                    }
                    // Not the site. Fall through to default.
                default:
                    // If this context is part of a course (excluding frontpage), use the course context.
                    // Otherwise, use the system context.
                    $coursecontext = $context->get_course_context(false);
                    if ($coursecontext && $coursecontext->instanceid !== $SITE->id) {
                        $context = $coursecontext;
                    } else {
                        $context = $systemcontext;
                    }
            }

            $params = ['contextid' => $context->id];
            if (has_capability('moodle/contentbank:access', $context)) {
                $url = new url('/contentbank/index.php', $params);
                $node = $coursenode->add(
                    get_string('contentbank'),
                    $url,
                    self::TYPE_CUSTOM,
                    null,
                    'contentbank',
                    new pix_icon('i/contentbank', '')
                );
                $node->showinflatnavigation = true;
            }
        }

        return true;
    }

    /**
     * Clears the navigation cache
     */
    public function clear_cache() {
        $this->cache->clear();
    }

    /**
     * Sets an expansion limit for the navigation
     *
     * The expansion limit is used to prevent the display of content that has a type
     * greater than the provided $type.
     *
     * Can be used to ensure things such as activities or activity content don't get
     * shown on the navigation.
     * They are still generated in order to ensure the navbar still makes sense.
     *
     * @param int $type One of navigation_node::TYPE_*
     * @return bool true when complete.
     */
    public function set_expansion_limit($type) {
        global $SITE;
        $nodes = $this->find_all_of_type($type);

        // We only want to hide specific types of nodes.
        // Only nodes that represent "structure" in the navigation tree should be hidden.
        // If we hide all nodes then we risk hiding vital information.
        $typestohide = [
            self::TYPE_CATEGORY,
            self::TYPE_COURSE,
            self::TYPE_SECTION,
            self::TYPE_ACTIVITY,
        ];

        foreach ($nodes as $node) {
            // We need to generate the full site node.
            if ($type == self::TYPE_COURSE && $node->key == $SITE->id) {
                continue;
            }
            foreach ($node->children as $child) {
                $child->hide($typestohide);
            }
        }
        return true;
    }

    #[\Override]
    public function get($key, $type = null) {
        if (!$this->initialised) {
            $this->initialise();
        }
        return parent::get($key, $type);
    }

    #[\Override]
    public function find($key, $type) {
        if (!$this->initialised) {
            $this->initialise();
        }
        if ($type == self::TYPE_ROOTNODE && array_key_exists($key, $this->rootnodes)) {
            return $this->rootnodes[$key];
        }
        return parent::find($key, $type);
    }

    /**
     * They've expanded the 'my courses' branch.
     */
    protected function load_courses_enrolled() {
        global $CFG;

        $limit = (int) $CFG->navcourselimit;

        $courses = enrol_get_my_courses('*');
        $flatnavcourses = [];

        // Go through the courses and see which ones we want to display in the flatnav.
        foreach ($courses as $course) {
            $classify = course_classify_for_timeline($course);

            if ($classify == COURSE_TIMELINE_INPROGRESS) {
                $flatnavcourses[$course->id] = $course;
            }
        }

        // Get the number of courses that can be displayed in the nav block and in the flatnav.
        $numtotalcourses = count($courses);
        $numtotalflatnavcourses = count($flatnavcourses);

        // Reduce the size of the arrays to abide by the 'navcourselimit' setting.
        $courses = array_slice($courses, 0, $limit, true);
        $flatnavcourses = array_slice($flatnavcourses, 0, $limit, true);

        // Get the number of courses we are going to show for each.
        $numshowncourses = count($courses);
        $numshownflatnavcourses = count($flatnavcourses);
        if ($numshowncourses && $this->show_my_categories()) {
            // Generate an array containing unique values of all the courses' categories.
            $categoryids = [];
            foreach ($courses as $course) {
                if (in_array($course->category, $categoryids)) {
                    continue;
                }
                $categoryids[] = $course->category;
            }

            // Array of category IDs that include the categories of the user's courses and the related course categories.
            $fullpathcategoryids = [];
            // Get the course categories for the enrolled courses' category IDs.
            $mycoursecategories = core_course_category::get_many($categoryids);
            // Loop over each of these categories and build the category tree using each category's path.
            foreach ($mycoursecategories as $mycoursecat) {
                $pathcategoryids = explode('/', $mycoursecat->path);
                // First element of the exploded path is empty since paths begin with '/'.
                array_shift($pathcategoryids);
                // Merge the exploded category IDs into the full list of category IDs that we will fetch.
                $fullpathcategoryids = array_merge($fullpathcategoryids, $pathcategoryids);
            }

            // Fetch all of the categories related to the user's courses.
            $pathcategories = core_course_category::get_many($fullpathcategoryids);
            // Loop over each of these categories and build the category tree.
            foreach ($pathcategories as $coursecat) {
                // No need to process categories that have already been added.
                if (isset($this->addedcategories[$coursecat->id])) {
                    continue;
                }
                // Skip categories that are not visible.
                if (!$coursecat->is_uservisible()) {
                    continue;
                }

                // Get this course category's parent node.
                $parent = null;
                if ($coursecat->parent && isset($this->addedcategories[$coursecat->parent])) {
                    $parent = $this->addedcategories[$coursecat->parent];
                }
                if (!$parent) {
                    // If it has no parent, then it should be right under the My courses node.
                    $parent = $this->rootnodes['mycourses'];
                }

                // Build the category object based from the coursecat object.
                $mycategory = new stdClass();
                $mycategory->id = $coursecat->id;
                $mycategory->name = $coursecat->name;
                $mycategory->visible = $coursecat->visible;

                // Add this category to the nav tree.
                $this->add_category($mycategory, $parent, self::TYPE_MY_CATEGORY);
            }
        }

        // Go through each course now and add it to the nav block, and the flatnav if applicable.
        foreach ($courses as $course) {
            $node = $this->add_course($course, false, self::COURSE_MY);
            if ($node) {
                $node->showinflatnavigation = false;
                // Check if we should also add this to the flat nav as well.
                if (isset($flatnavcourses[$course->id])) {
                    $node->showinflatnavigation = true;
                }
            }
        }

        // Go through each course in the flatnav now.
        foreach ($flatnavcourses as $course) {
            // Check if we haven't already added it.
            if (!isset($courses[$course->id])) {
                // Ok, add it to the flatnav only.
                $node = $this->add_course($course, false, self::COURSE_MY);
                $node->display = false;
                $node->showinflatnavigation = true;
            }
        }

        $showmorelinkinnav = $numtotalcourses > $numshowncourses;
        $showmorelinkinflatnav = $numtotalflatnavcourses > $numshownflatnavcourses;
        // Show a link to the course page if there are more courses the user is enrolled in.
        if ($showmorelinkinnav || $showmorelinkinflatnav) {
            // Adding hash to URL so the link is not highlighted in the navigation when clicked.
            $url = new url('/my/courses.php');
            $parent = $this->rootnodes['mycourses'];
            $coursenode = $parent->add(get_string('morenavigationlinks'), $url, self::TYPE_CUSTOM, null, self::COURSE_INDEX_PAGE);

            if ($showmorelinkinnav) {
                $coursenode->display = true;
            }

            if ($showmorelinkinflatnav) {
                $coursenode->showinflatnavigation = true;
            }
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(global_navigation::class, \global_navigation::class);
