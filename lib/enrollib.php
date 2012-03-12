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
 * This library includes the basic parts of enrol api.
 * It is available on each page.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Course enrol instance enabled. (used in enrol->status) */
define('ENROL_INSTANCE_ENABLED', 0);

/** Course enrol instance disabled, user may enter course if other enrol instance enabled. (used in enrol->status)*/
define('ENROL_INSTANCE_DISABLED', 1);

/** User is active participant (used in user_enrolments->status)*/
define('ENROL_USER_ACTIVE', 0);

/** User participation in course is suspended (used in user_enrolments->status) */
define('ENROL_USER_SUSPENDED', 1);

/** Enrol info is cached for this number of seconds in require_login() */
define('ENROL_REQUIRE_LOGIN_CACHE_PERIOD', 1800);

/** When user disappears from external source, the enrolment is completely removed */
define('ENROL_EXT_REMOVED_UNENROL', 0);

/** When user disappears from external source, the enrolment is kept as is - one way sync */
define('ENROL_EXT_REMOVED_KEEP', 1);

/** enrol plugin feature describing requested restore type */
define('ENROL_RESTORE_TYPE', 'enrolrestore');
/** User custom backup/restore class  stored in backup/moodle2/ subdirectory */
define('ENROL_RESTORE_CLASS', 'class');
/** Restore all custom fields from enrol table without any changes and all user_enrolments records */
define('ENROL_RESTORE_EXACT', 'exact');
/** Restore enrol record like ENROL_RESTORE_EXACT, but no user enrolments */
define('ENROL_RESTORE_NOUSERS', 'nousers');

/**
 * When user disappears from external source, user enrolment is suspended, roles are kept as is.
 * In some cases user needs a role with some capability to be visible in UI - suc has in gradebook,
 * assignments, etc.
 */
define('ENROL_EXT_REMOVED_SUSPEND', 2);

/**
 * When user disappears from external source, the enrolment is suspended and roles assigned
 * by enrol instance are removed. Please note that user may "disappear" from gradebook and other areas.
 * */
define('ENROL_EXT_REMOVED_SUSPENDNOROLES', 3);

/**
 * Returns instances of enrol plugins
 * @param bool $enable return enabled only
 * @return array of enrol plugins name=>instance
 */
function enrol_get_plugins($enabled) {
    global $CFG;

    $result = array();

    if ($enabled) {
        // sorted by enabled plugin order
        $enabled = explode(',', $CFG->enrol_plugins_enabled);
        $plugins = array();
        foreach ($enabled as $plugin) {
            $plugins[$plugin] = "$CFG->dirroot/enrol/$plugin";
        }
    } else {
        // sorted alphabetically
        $plugins = get_plugin_list('enrol');
        ksort($plugins);
    }

    foreach ($plugins as $plugin=>$location) {
        if (!file_exists("$location/lib.php")) {
            continue;
        }
        include_once("$location/lib.php");
        $class = "enrol_{$plugin}_plugin";
        if (!class_exists($class)) {
            continue;
        }

        $result[$plugin] = new $class();
    }

    return $result;
}

/**
 * Returns instance of enrol plugin
 * @param  string $name name of enrol plugin ('manual', 'guest', ...)
 * @return enrol_plugin
 */
function enrol_get_plugin($name) {
    global $CFG;

    if ($name !== clean_param($name, PARAM_SAFEDIR)) {
        // ignore malformed plugin names completely
        return null;
    }

    $location = "$CFG->dirroot/enrol/$name";

    if (!file_exists("$location/lib.php")) {
        return null;
    }
    include_once("$location/lib.php");
    $class = "enrol_{$name}_plugin";
    if (!class_exists($class)) {
        return null;
    }

    return new $class();
}

/**
 * Returns enrolment instances in given course.
 * @param int $courseid
 * @param bool $enabled
 * @return array of enrol instances
 */
function enrol_get_instances($courseid, $enabled) {
    global $DB, $CFG;

    if (!$enabled) {
        return $DB->get_records('enrol', array('courseid'=>$courseid), 'sortorder,id');
    }

    $result = $DB->get_records('enrol', array('courseid'=>$courseid, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder,id');

    $enabled = explode(',', $CFG->enrol_plugins_enabled);
    foreach ($result as $key=>$instance) {
        if (!in_array($instance->enrol, $enabled)) {
            unset($result[$key]);
            continue;
        }
        if (!file_exists("$CFG->dirroot/enrol/$instance->enrol/lib.php")) {
            // broken plugin
            unset($result[$key]);
            continue;
        }
    }

    return $result;
}

/**
 * Checks if a given plugin is in the list of enabled enrolment plugins.
 *
 * @param string $enrol Enrolment plugin name
 * @return boolean Whether the plugin is enabled
 */
function enrol_is_enabled($enrol) {
    global $CFG;

    if (empty($CFG->enrol_plugins_enabled)) {
        return false;
    }
    return in_array($enrol, explode(',', $CFG->enrol_plugins_enabled));
}

/**
 * Check all the login enrolment information for the given user object
 * by querying the enrolment plugins
 *
 * @param object $user
 * @return void
 */
function enrol_check_plugins($user) {
    global $CFG;

    if (empty($user->id) or isguestuser($user)) {
        // shortcut - there is no enrolment work for guests and not-logged-in users
        return;
    }

    if (is_siteadmin()) {
        // no sync for admin user, please use admin accounts only for admin tasks like the unix root user!
        // if plugin fails on sync admins need to be able to log in
        return;
    }

    static $inprogress = array();  // To prevent this function being called more than once in an invocation

    if (!empty($inprogress[$user->id])) {
        return;
    }

    $inprogress[$user->id] = true;  // Set the flag

    $enabled = enrol_get_plugins(true);

    foreach($enabled as $enrol) {
        $enrol->sync_user_enrolments($user);
    }

    unset($inprogress[$user->id]);  // Unset the flag
}

/**
 * Do these two students share any course?
 *
 * The courses has to be visible and enrolments has to be active,
 * timestart and timeend restrictions are ignored.
 *
 * This function calls {@see enrol_get_shared_courses()} setting checkexistsonly
 * to true.
 *
 * @param stdClass|int $user1
 * @param stdClass|int $user2
 * @return bool
 */
function enrol_sharing_course($user1, $user2) {
    return enrol_get_shared_courses($user1, $user2, false, true);
}

/**
 * Returns any courses shared by the two users
 *
 * The courses has to be visible and enrolments has to be active,
 * timestart and timeend restrictions are ignored.
 *
 * @global moodle_database $DB
 * @param stdClass|int $user1
 * @param stdClass|int $user2
 * @param bool $preloadcontexts If set to true contexts for the returned courses
 *              will be preloaded.
 * @param bool $checkexistsonly If set to true then this function will return true
 *              if the users share any courses and false if not.
 * @return array|bool An array of courses that both users are enrolled in OR if
 *              $checkexistsonly set returns true if the users share any courses
 *              and false if not.
 */
function enrol_get_shared_courses($user1, $user2, $preloadcontexts = false, $checkexistsonly = false) {
    global $DB, $CFG;

    $user1 = !empty($user1->id) ? $user1->id : $user1;
    $user2 = !empty($user2->id) ? $user2->id : $user2;

    if (empty($user1) or empty($user2)) {
        return false;
    }

    if (!$plugins = explode(',', $CFG->enrol_plugins_enabled)) {
        return false;
    }

    list($plugins, $params) = $DB->get_in_or_equal($plugins, SQL_PARAMS_NAMED, 'ee');
    $params['enabled'] = ENROL_INSTANCE_ENABLED;
    $params['active1'] = ENROL_USER_ACTIVE;
    $params['active2'] = ENROL_USER_ACTIVE;
    $params['user1']   = $user1;
    $params['user2']   = $user2;

    $ctxselect = '';
    $ctxjoin = '';
    if ($preloadcontexts) {
        list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    }

    $sql = "SELECT c.* $ctxselect
              FROM {course} c
              JOIN (
                SELECT DISTINCT c.id
                  FROM {enrol} e
                  JOIN {user_enrolments} ue1 ON (ue1.enrolid = e.id AND ue1.status = :active1 AND ue1.userid = :user1)
                  JOIN {user_enrolments} ue2 ON (ue2.enrolid = e.id AND ue2.status = :active2 AND ue2.userid = :user2)
                  JOIN {course} c ON (c.id = e.courseid AND c.visible = 1)
                 WHERE e.status = :enabled AND e.enrol $plugins
              ) ec ON ec.id = c.id
              $ctxjoin";

    if ($checkexistsonly) {
        return $DB->record_exists_sql($sql, $params);
    } else {
        $courses = $DB->get_records_sql($sql, $params);
        if ($preloadcontexts) {
            array_map('context_instance_preload', $courses);
        }
        return $courses;
    }
}

/**
 * This function adds necessary enrol plugins UI into the course edit form.
 *
 * @param MoodleQuickForm $mform
 * @param object $data course edit form data
 * @param object $context context of existing course or parent category if course does not exist
 * @return void
 */
function enrol_course_edit_form(MoodleQuickForm $mform, $data, $context) {
    $plugins = enrol_get_plugins(true);
    if (!empty($data->id)) {
        $instances = enrol_get_instances($data->id, false);
        foreach ($instances as $instance) {
            if (!isset($plugins[$instance->enrol])) {
                continue;
            }
            $plugin = $plugins[$instance->enrol];
            $plugin->course_edit_form($instance, $mform, $data, $context);
        }
    } else {
        foreach ($plugins as $plugin) {
            $plugin->course_edit_form(NULL, $mform, $data, $context);
        }
    }
}

/**
 * Validate course edit form data
 *
 * @param array $data raw form data
 * @param object $context context of existing course or parent category if course does not exist
 * @return array errors array
 */
function enrol_course_edit_validation(array $data, $context) {
    $errors = array();
    $plugins = enrol_get_plugins(true);

    if (!empty($data['id'])) {
        $instances = enrol_get_instances($data['id'], false);
        foreach ($instances as $instance) {
            if (!isset($plugins[$instance->enrol])) {
                continue;
            }
            $plugin = $plugins[$instance->enrol];
            $errors = array_merge($errors, $plugin->course_edit_validation($instance, $data, $context));
        }
    } else {
        foreach ($plugins as $plugin) {
            $errors = array_merge($errors, $plugin->course_edit_validation(NULL, $data, $context));
        }
    }

    return $errors;
}

/**
 * Update enrol instances after course edit form submission
 * @param bool $inserted true means new course added, false course already existed
 * @param object $course
 * @param object $data form data
 * @return void
 */
function enrol_course_updated($inserted, $course, $data) {
    global $DB, $CFG;

    $plugins = enrol_get_plugins(true);

    foreach ($plugins as $plugin) {
        $plugin->course_updated($inserted, $course, $data);
    }
}

/**
 * Add navigation nodes
 * @param navigation_node $coursenode
 * @param object $course
 * @return void
 */
function enrol_add_course_navigation(navigation_node $coursenode, $course) {
    global $CFG;

    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    $instances = enrol_get_instances($course->id, true);
    $plugins   = enrol_get_plugins(true);

    // we do not want to break all course pages if there is some borked enrol plugin, right?
    foreach ($instances as $k=>$instance) {
        if (!isset($plugins[$instance->enrol])) {
            unset($instances[$k]);
        }
    }

    $usersnode = $coursenode->add(get_string('users'), null, navigation_node::TYPE_CONTAINER, null, 'users');

    if ($course->id != SITEID) {
        // list all participants - allows assigning roles, groups, etc.
        if (has_capability('moodle/course:enrolreview', $coursecontext)) {
            $url = new moodle_url('/enrol/users.php', array('id'=>$course->id));
            $usersnode->add(get_string('enrolledusers', 'enrol'), $url, navigation_node::TYPE_SETTING, null, 'review', new pix_icon('i/users', ''));
        }

        // manage enrol plugin instances
        if (has_capability('moodle/course:enrolconfig', $coursecontext) or has_capability('moodle/course:enrolreview', $coursecontext)) {
            $url = new moodle_url('/enrol/instances.php', array('id'=>$course->id));
        } else {
            $url = NULL;
        }
        $instancesnode = $usersnode->add(get_string('enrolmentinstances', 'enrol'), $url, navigation_node::TYPE_SETTING, null, 'manageinstances');

        // each instance decides how to configure itself or how many other nav items are exposed
        foreach ($instances as $instance) {
            if (!isset($plugins[$instance->enrol])) {
                continue;
            }
            $plugins[$instance->enrol]->add_course_navigation($instancesnode, $instance);
        }

        if (!$url) {
            $instancesnode->trim_if_empty();
        }
    }

    // Manage groups in this course or even frontpage
    if (($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $coursecontext)) {
        $url = new moodle_url('/group/index.php', array('id'=>$course->id));
        $usersnode->add(get_string('groups'), $url, navigation_node::TYPE_SETTING, null, 'groups', new pix_icon('i/group', ''));
    }

     if (has_any_capability(array( 'moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:review'), $coursecontext)) {
        // Override roles
        if (has_capability('moodle/role:review', $coursecontext)) {
            $url = new moodle_url('/admin/roles/permissions.php', array('contextid'=>$coursecontext->id));
        } else {
            $url = NULL;
        }
        $permissionsnode = $usersnode->add(get_string('permissions', 'role'), $url, navigation_node::TYPE_SETTING, null, 'override');

        // Add assign or override roles if allowed
        if ($course->id == SITEID or (!empty($CFG->adminsassignrolesincourse) and is_siteadmin())) {
            if (has_capability('moodle/role:assign', $coursecontext)) {
                $url = new moodle_url('/admin/roles/assign.php', array('contextid'=>$coursecontext->id));
                $permissionsnode->add(get_string('assignedroles', 'role'), $url, navigation_node::TYPE_SETTING, null, 'roles', new pix_icon('i/roles', ''));
            }
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride','moodle/role:override', 'moodle/role:assign'), $coursecontext)) {
            $url = new moodle_url('/admin/roles/check.php', array('contextid'=>$coursecontext->id));
            $permissionsnode->add(get_string('checkpermissions', 'role'), $url, navigation_node::TYPE_SETTING, null, 'permissions', new pix_icon('i/checkpermissions', ''));
        }
     }

     // Deal somehow with users that are not enrolled but still got a role somehow
    if ($course->id != SITEID) {
        //TODO, create some new UI for role assignments at course level
        if (has_capability('moodle/role:assign', $coursecontext)) {
            $url = new moodle_url('/enrol/otherusers.php', array('id'=>$course->id));
            $usersnode->add(get_string('notenrolledusers', 'enrol'), $url, navigation_node::TYPE_SETTING, null, 'otherusers', new pix_icon('i/roles', ''));
        }
    }

    // just in case nothing was actually added
    $usersnode->trim_if_empty();

    if ($course->id != SITEID) {
        if (isguestuser() or !isloggedin()) {
            // guest account can not be enrolled - no links for them
        } else if (is_enrolled($coursecontext)) {
            // unenrol link if possible
            foreach ($instances as $instance) {
                if (!isset($plugins[$instance->enrol])) {
                    continue;
                }
                $plugin = $plugins[$instance->enrol];
                if ($unenrollink = $plugin->get_unenrolself_link($instance)) {
                    $shortname = format_string($course->shortname, true, array('context' => $coursecontext));
                    $coursenode->add(get_string('unenrolme', 'core_enrol', $shortname), $unenrollink, navigation_node::TYPE_SETTING, null, 'unenrolself', new pix_icon('i/user', ''));
                    break;
                    //TODO. deal with multiple unenrol links - not likely case, but still...
                }
            }
        } else {
            // enrol link if possible
            if (is_viewing($coursecontext)) {
                // better not show any enrol link, this is intended for managers and inspectors
            } else {
                foreach ($instances as $instance) {
                    if (!isset($plugins[$instance->enrol])) {
                        continue;
                    }
                    $plugin = $plugins[$instance->enrol];
                    if ($plugin->show_enrolme_link($instance)) {
                        $url = new moodle_url('/enrol/index.php', array('id'=>$course->id));
                        $shortname = format_string($course->shortname, true, array('context' => $coursecontext));
                        $coursenode->add(get_string('enrolme', 'core_enrol', $shortname), $url, navigation_node::TYPE_SETTING, null, 'enrolself', new pix_icon('i/user', ''));
                        break;
                    }
                }
            }
        }
    }
}

/**
 * Returns list of courses current $USER is enrolled in and can access
 *
 * - $fields is an array of field names to ADD
 *   so name the fields you really need, which will
 *   be added and uniq'd
 *
 * @param string|array $fields
 * @param string $sort
 * @param int $limit max number of courses
 * @return array
 */
function enrol_get_my_courses($fields = NULL, $sort = 'visible DESC,sortorder ASC', $limit = 0) {
    global $DB, $USER;

    // Guest account does not have any courses
    if (isguestuser() or !isloggedin()) {
        return(array());
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'startdate', 'visible',
                        'groupmode', 'groupmodeforce');

    if (empty($fields)) {
        $fields = $basefields;
    } else if (is_string($fields)) {
        // turn the fields from a string to an array
        $fields = explode(',', $fields);
        $fields = array_map('trim', $fields);
        $fields = array_unique(array_merge($basefields, $fields));
    } else if (is_array($fields)) {
        $fields = array_unique(array_merge($basefields, $fields));
    } else {
        throw new coding_exception('Invalid $fileds parameter in enrol_get_my_courses()');
    }
    if (in_array('*', $fields)) {
        $fields = array('*');
    }

    $orderby = "";
    $sort    = trim($sort);
    if (!empty($sort)) {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            if (strpos($rawsort, 'c.') === 0) {
                $rawsort = substr($rawsort, 2);
            }
            $sorts[] = trim($rawsort);
        }
        $sort = 'c.'.implode(',c.', $sorts);
        $orderby = "ORDER BY $sort";
    }

    $wheres = array("c.id <> :siteid");
    $params = array('siteid'=>SITEID);

    if (isset($USER->loginascontext) and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
        // list _only_ this course - anything else is asking for trouble...
        $wheres[] = "courseid = :loginas";
        $params['loginas'] = $USER->loginascontext->instanceid;
    }

    $coursefields = 'c.' .join(',c.', $fields);
    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $wheres = implode(" AND ", $wheres);

    //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
    $sql = "SELECT $coursefields $ccselect
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                     WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)
                   ) en ON (en.courseid = c.id)
           $ccjoin
             WHERE $wheres
          $orderby";
    $params['userid']  = $USER->id;
    $params['active']  = ENROL_USER_ACTIVE;
    $params['enabled'] = ENROL_INSTANCE_ENABLED;
    $params['now1']    = round(time(), -2); // improves db caching
    $params['now2']    = $params['now1'];

    $courses = $DB->get_records_sql($sql, $params, 0, $limit);

    // preload contexts and check visibility
    foreach ($courses as $id=>$course) {
        context_instance_preload($course);
        if (!$course->visible) {
            if (!$context = get_context_instance(CONTEXT_COURSE, $id)) {
                unset($courses[$id]);
                continue;
            }
            if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                unset($courses[$id]);
                continue;
            }
        }
        $courses[$id] = $course;
    }

    //wow! Is that really all? :-D

    return $courses;
}

/**
 * Returns course enrolment information icons.
 *
 * @param object $course
 * @param array $instances enrol instances of this course, improves performance
 * @return array of pix_icon
 */
function enrol_get_course_info_icons($course, array $instances = NULL) {
    $icons = array();
    if (is_null($instances)) {
        $instances = enrol_get_instances($course->id, true);
    }
    $plugins = enrol_get_plugins(true);
    foreach ($plugins as $name => $plugin) {
        $pis = array();
        foreach ($instances as $instance) {
            if ($instance->status != ENROL_INSTANCE_ENABLED or $instance->courseid != $course->id) {
                debugging('Invalid instances parameter submitted in enrol_get_info_icons()');
                continue;
            }
            if ($instance->enrol == $name) {
                $pis[$instance->id] = $instance;
            }
        }
        if ($pis) {
            $icons = array_merge($icons, $plugin->get_info_icons($pis));
        }
    }
    return $icons;
}

/**
 * Returns course enrolment detailed information.
 *
 * @param object $course
 * @return array of html fragments - can be used to construct lists
 */
function enrol_get_course_description_texts($course) {
    $lines = array();
    $instances = enrol_get_instances($course->id, true);
    $plugins = enrol_get_plugins(true);
    foreach ($instances as $instance) {
        if (!isset($plugins[$instance->enrol])) {
            //weird
            continue;
        }
        $plugin = $plugins[$instance->enrol];
        $text = $plugin->get_description_text($instance);
        if ($text !== NULL) {
            $lines[] = $text;
        }
    }
    return $lines;
}

/**
 * Returns list of courses user is enrolled into.
 *
 * - $fields is an array of fieldnames to ADD
 *   so name the fields you really need, which will
 *   be added and uniq'd
 *
 * @param int $userid
 * @param bool $onlyactive return only active enrolments in courses user may see
 * @param string|array $fields
 * @param string $sort
 * @return array
 */
function enrol_get_users_courses($userid, $onlyactive = false, $fields = NULL, $sort = 'visible DESC,sortorder ASC') {
    global $DB;

    // Guest account does not have any courses
    if (isguestuser($userid) or empty($userid)) {
        return(array());
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'startdate', 'visible',
                        'groupmode', 'groupmodeforce');

    if (empty($fields)) {
        $fields = $basefields;
    } else if (is_string($fields)) {
        // turn the fields from a string to an array
        $fields = explode(',', $fields);
        $fields = array_map('trim', $fields);
        $fields = array_unique(array_merge($basefields, $fields));
    } else if (is_array($fields)) {
        $fields = array_unique(array_merge($basefields, $fields));
    } else {
        throw new coding_exception('Invalid $fileds parameter in enrol_get_my_courses()');
    }
    if (in_array('*', $fields)) {
        $fields = array('*');
    }

    $orderby = "";
    $sort    = trim($sort);
    if (!empty($sort)) {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            if (strpos($rawsort, 'c.') === 0) {
                $rawsort = substr($rawsort, 2);
            }
            $sorts[] = trim($rawsort);
        }
        $sort = 'c.'.implode(',c.', $sorts);
        $orderby = "ORDER BY $sort";
    }

    $params = array('siteid'=>SITEID);

    if ($onlyactive) {
        $subwhere = "WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)";
        $params['now1']    = round(time(), -2); // improves db caching
        $params['now2']    = $params['now1'];
        $params['active']  = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
    } else {
        $subwhere = "";
    }

    $coursefields = 'c.' .join(',c.', $fields);
    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');

    //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
    $sql = "SELECT $coursefields $ccselect
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                 $subwhere
                   ) en ON (en.courseid = c.id)
           $ccjoin
             WHERE c.id <> :siteid
          $orderby";
    $params['userid']  = $userid;

    $courses = $DB->get_records_sql($sql, $params);

    // preload contexts and check visibility
    foreach ($courses as $id=>$course) {
        context_instance_preload($course);
        if ($onlyactive) {
            if (!$course->visible) {
                if (!$context = get_context_instance(CONTEXT_COURSE, $id)) {
                    unset($courses[$id]);
                    continue;
                }
                if (!has_capability('moodle/course:viewhiddencourses', $context, $userid)) {
                    unset($courses[$id]);
                    continue;
                }
            }
        }
        $courses[$id] = $course;
    }

    //wow! Is that really all? :-D

    return $courses;

}

/**
 * Called when user is about to be deleted.
 * @param object $user
 * @return void
 */
function enrol_user_delete($user) {
    global $DB;

    $plugins = enrol_get_plugins(true);
    foreach ($plugins as $plugin) {
        $plugin->user_delete($user);
    }

    // force cleanup of all broken enrolments
    $DB->delete_records('user_enrolments', array('userid'=>$user->id));
}

/**
 * Called when course is about to be deleted.
 * @param stdClass $object
 * @return void
 */
function enrol_course_delete($course) {
    global $DB;

    $instances = enrol_get_instances($course->id, false);
    $plugins = enrol_get_plugins(true);
    foreach ($instances as $instance) {
        if (isset($plugins[$instance->enrol])) {
            $plugins[$instance->enrol]->delete_instance($instance);
        }
        // low level delete in case plugin did not do it
        $DB->delete_records('user_enrolments', array('enrolid'=>$instance->id));
        $DB->delete_records('role_assignments', array('itemid'=>$instance->id, 'component'=>'enrol_'.$instance->enrol));
        $DB->delete_records('user_enrolments', array('enrolid'=>$instance->id));
        $DB->delete_records('enrol', array('id'=>$instance->id));
    }
}

/**
 * Try to enrol user via default internal auth plugin.
 *
 * For now this is always using the manual enrol plugin...
 *
 * @param $courseid
 * @param $userid
 * @param $roleid
 * @param $timestart
 * @param $timeend
 * @return bool success
 */
function enrol_try_internal_enrol($courseid, $userid, $roleid = null, $timestart = 0, $timeend = 0) {
    global $DB;

    //note: this is hardcoded to manual plugin for now

    if (!enrol_is_enabled('manual')) {
        return false;
    }

    if (!$enrol = enrol_get_plugin('manual')) {
        return false;
    }
    if (!$instances = $DB->get_records('enrol', array('enrol'=>'manual', 'courseid'=>$courseid, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder,id ASC')) {
        return false;
    }
    $instance = reset($instances);

    $enrol->enrol_user($instance, $userid, $roleid, $timestart, $timeend);

    return true;
}

/**
 * Is there a chance users might self enrol
 * @param int $courseid
 * @return bool
 */
function enrol_selfenrol_available($courseid) {
    $result = false;

    $plugins = enrol_get_plugins(true);
    $enrolinstances = enrol_get_instances($courseid, true);
    foreach($enrolinstances as $instance) {
        if (!isset($plugins[$instance->enrol])) {
            continue;
        }
        if ($instance->enrol === 'guest') {
            // blacklist known temporary guest plugins
            continue;
        }
        if ($plugins[$instance->enrol]->show_enrolme_link($instance)) {
            $result = true;
            break;
        }
    }

    return $result;
}

/**
 * All enrol plugins should be based on this class,
 * this is also the main source of documentation.
 */
abstract class enrol_plugin {
    protected $config = null;

    /**
     * Returns name of this enrol plugin
     * @return string
     */
    public function get_name() {
        // second word in class is always enrol name, sorry, no fancy plugin names with _
        $words = explode('_', get_class($this));
        return $words[1];
    }

    /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        if (empty($instance->name)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $instance->courseid);
            return format_string($instance->name, true, array('context'=>$context));
        }
    }

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        return array();
    }

    /**
     * Returns optional enrolment instance description text.
     *
     * This is used in detailed course information.
     *
     *
     * @param object $instance
     * @return string short html text
     */
    public function get_description_text($instance) {
        return null;
    }

    /**
     * Makes sure config is loaded and cached.
     * @return void
     */
    protected function load_config() {
        if (!isset($this->config)) {
            $name = $this->get_name();
            $this->config = get_config("enrol_$name");
        }
    }

    /**
     * Returns plugin config value
     * @param  string $name
     * @param  string $default value if config does not exist yet
     * @return string value or default
     */
    public function get_config($name, $default = NULL) {
        $this->load_config();
        return isset($this->config->$name) ? $this->config->$name : $default;
    }

    /**
     * Sets plugin config value
     * @param  string $name name of config
     * @param  string $value string config value, null means delete
     * @return string value
     */
    public function set_config($name, $value) {
        $pluginname = $this->get_name();
        $this->load_config();
        if ($value === NULL) {
            unset($this->config->$name);
        } else {
            $this->config->$name = $value;
        }
        set_config($name, $value, "enrol_$pluginname");
    }

    /**
     * Does this plugin assign protected roles are can they be manually removed?
     * @return bool - false means anybody may tweak roles, it does not use itemid and component when assigning roles
     */
    public function roles_protected() {
        return true;
    }

    /**
     * Does this plugin allow manual enrolments?
     *
     * @param stdClass $instance course enrol instance
     * All plugins allowing this must implement 'enrol/xxx:enrol' capability
     *
     * @return bool - true means user with 'enrol/xxx:enrol' may enrol others freely, trues means nobody may add more enrolments manually
     */
    public function allow_enrol(stdClass $instance) {
        return false;
    }

    /**
     * Does this plugin allow manual unenrolments?
     *
     * @param stdClass $instance course enrol instance
     * All plugins allowing this must implement 'enrol/xxx:unenrol' capability
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol others freely, trues means nobody may touch user_enrolments
     */
    public function allow_unenrol(stdClass $instance) {
        return false;
    }

    /**
     * Does this plugin allow manual changes in user_enrolments table?
     *
     * All plugins allowing this must implement 'enrol/xxx:manage' capability
     *
     * @param stdClass $instance course enrol instance
     * @return bool - true means it is possible to change enrol period and status in user_enrolments table
     */
    public function allow_manage(stdClass $instance) {
        return false;
    }

    /**
     * Does this plugin support some way to user to self enrol?
     *
     * @param stdClass $instance course enrol instance
     *
     * @return bool - true means show "Enrol me in this course" link in course UI
     */
    public function show_enrolme_link(stdClass $instance) {
        return false;
    }

    /**
     * Attempt to automatically enrol current user in course without any interaction,
     * calling code has to make sure the plugin and instance are active.
     *
     * This should return either a timestamp in the future or false.
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $user record
     * @return bool|int false means not enrolled, integer means timeend
     */
    public function try_autoenrol(stdClass $instance) {
        global $USER;

        return false;
    }

    /**
     * Attempt to automatically gain temporary guest access to course,
     * calling code has to make sure the plugin and instance are active.
     *
     * This should return either a timestamp in the future or false.
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $user record
     * @return bool|int false means no guest access, integer means timeend
     */
    public function try_guestaccess(stdClass $instance) {
        global $USER;

        return false;
    }

    /**
     * Enrol user into course via enrol instance.
     *
     * @param stdClass $instance
     * @param int $userid
     * @param int $roleid optional role id
     * @param int $timestart 0 means unknown
     * @param int $timeend 0 means forever
     * @param int $status default to ENROL_USER_ACTIVE for new enrolments, no change by default in updates
     * @return void
     */
    public function enrol_user(stdClass $instance, $userid, $roleid = NULL, $timestart = 0, $timeend = 0, $status = NULL) {
        global $DB, $USER, $CFG; // CFG necessary!!!

        if ($instance->courseid == SITEID) {
            throw new coding_exception('invalid attempt to enrol into frontpage course!');
        }

        $name = $this->get_name();
        $courseid = $instance->courseid;

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid, MUST_EXIST);

        $inserted = false;
        if ($ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            //only update if timestart or timeend or status are different.
            if ($ue->timestart != $timestart or $ue->timeend != $timeend or (!is_null($status) and $ue->status != $status)) {
                $ue->timestart    = $timestart;
                $ue->timeend      = $timeend;
                if (!is_null($status)) {
                    $ue->status   = $status;
                }
                $ue->modifierid   = $USER->id;
                $ue->timemodified = time();
                $DB->update_record('user_enrolments', $ue);
            }
        } else {
            $ue = new stdClass();
            $ue->enrolid      = $instance->id;
            $ue->status       = is_null($status) ? ENROL_USER_ACTIVE : $status;
            $ue->userid       = $userid;
            $ue->timestart    = $timestart;
            $ue->timeend      = $timeend;
            $ue->modifierid   = $USER->id;
            $ue->timecreated  = time();
            $ue->timemodified = $ue->timecreated;
            $ue->id = $DB->insert_record('user_enrolments', $ue);

            $inserted = true;
        }

        if ($roleid) {
            if ($this->roles_protected()) {
                role_assign($roleid, $userid, $context->id, 'enrol_'.$name, $instance->id);
            } else {
                role_assign($roleid, $userid, $context->id);
            }
        }

        if ($inserted) {
            // add extra info and trigger event
            $ue->courseid  = $courseid;
            $ue->enrol     = $name;
            events_trigger('user_enrolled', $ue);
        }

        // reset primitive require_login() caching
        if ($userid == $USER->id) {
            if (isset($USER->enrol['enrolled'][$courseid])) {
                unset($USER->enrol['enrolled'][$courseid]);
            }
            if (isset($USER->enrol['tempguest'][$courseid])) {
                unset($USER->enrol['tempguest'][$courseid]);
                $USER->access = remove_temp_roles($context, $USER->access);
            }
        }
    }

    /**
     * Store user_enrolments changes and trigger event.
     *
     * @param object $ue
     * @param int $user id
     * @param int $status
     * @param int $timestart
     * @param int $timeend
     * @return void
     */
    public function update_user_enrol(stdClass $instance, $userid, $status = NULL, $timestart = NULL, $timeend = NULL) {
        global $DB, $USER;

        $name = $this->get_name();

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        if (!$ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            // weird, user not enrolled
            return;
        }

        $modified = false;
        if (isset($status) and $ue->status != $status) {
            $ue->status = $status;
            $modified = true;
        }
        if (isset($timestart) and $ue->timestart != $timestart) {
            $ue->timestart = $timestart;
            $modified = true;
        }
        if (isset($timeend) and $ue->timeend != $timeend) {
            $ue->timeend = $timeend;
            $modified = true;
        }

        if (!$modified) {
            // no change
            return;
        }

        $ue->modifierid = $USER->id;
        $DB->update_record('user_enrolments', $ue);

        // trigger event
        $ue->courseid  = $instance->courseid;
        $ue->enrol     = $instance->name;
        events_trigger('user_unenrol_modified', $ue);
    }

    /**
     * Unenrol user from course,
     * the last unenrolment removes all remaining roles.
     *
     * @param stdClass $instance
     * @param int $userid
     * @return void
     */
    public function unenrol_user(stdClass $instance, $userid) {
        global $CFG, $USER, $DB;

        $name = $this->get_name();
        $courseid = $instance->courseid;

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid, MUST_EXIST);

        if (!$ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            // weird, user not enrolled
            return;
        }

        role_unassign_all(array('userid'=>$userid, 'contextid'=>$context->id, 'component'=>'enrol_'.$name, 'itemid'=>$instance->id));
        $DB->delete_records('user_enrolments', array('id'=>$ue->id));

        // add extra info and trigger event
        $ue->courseid  = $courseid;
        $ue->enrol     = $name;

        $sql = "SELECT 'x'
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid)
                  WHERE ue.userid = :userid AND e.courseid = :courseid";
        if ($DB->record_exists_sql($sql, array('userid'=>$userid, 'courseid'=>$courseid))) {
            $ue->lastenrol = false;
            events_trigger('user_unenrolled', $ue);
            // user still has some enrolments, no big cleanup yet
        } else {
            // the big cleanup IS necessary!

            require_once("$CFG->dirroot/group/lib.php");
            require_once("$CFG->libdir/gradelib.php");

            // remove all remaining roles
            role_unassign_all(array('userid'=>$userid, 'contextid'=>$context->id), true, false);

            //clean up ALL invisible user data from course if this is the last enrolment - groups, grades, etc.
            groups_delete_group_members($courseid, $userid);

            grade_user_unenrol($courseid, $userid);

            $DB->delete_records('user_lastaccess', array('userid'=>$userid, 'courseid'=>$courseid));

            $ue->lastenrol = true; // means user not enrolled any more
            events_trigger('user_unenrolled', $ue);
        }
        // reset primitive require_login() caching
        if ($userid == $USER->id) {
            if (isset($USER->enrol['enrolled'][$courseid])) {
                unset($USER->enrol['enrolled'][$courseid]);
            }
            if (isset($USER->enrol['tempguest'][$courseid])) {
                unset($USER->enrol['tempguest'][$courseid]);
                $USER->access = remove_temp_roles($context, $USER->access);
            }
        }
    }

    /**
     * Forces synchronisation of user enrolments.
     *
     * This is important especially for external enrol plugins,
     * this function is called for all enabled enrol plugins
     * right after every user login.
     *
     * @param object $user user record
     * @return void
     */
    public function sync_user_enrolments($user) {
        // override if necessary
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        // override for most plugins, check if instance already exists in cases only one instance is supported
        return NULL;
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function instance_deleteable($instance) {
        return true;
    }

    /**
     * Returns link to manual enrol UI if exists.
     * Does the access control tests automatically.
     *
     * @param object $instance
     * @return moodle_url
     */
    public function get_manual_enrol_link($instance) {
        return NULL;
    }

    /**
     * Returns list of unenrol links for all enrol instances in course.
     *
     * @param int $instance
     * @return moodle_url or NULL if self unenrolment not supported
     */
    public function get_unenrolself_link($instance) {
        global $USER, $CFG, $DB;

        $name = $this->get_name();
        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        if ($instance->courseid == SITEID) {
            return NULL;
        }

        if (!enrol_is_enabled($name)) {
            return NULL;
        }

        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return NULL;
        }

        if (!file_exists("$CFG->dirroot/enrol/$name/unenrolself.php")) {
            return NULL;
        }

        $context = get_context_instance(CONTEXT_COURSE, $instance->courseid, MUST_EXIST);

        if (!has_capability("enrol/$name:unenrolself", $context)) {
            return NULL;
        }

        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$USER->id, 'status'=>ENROL_USER_ACTIVE))) {
            return NULL;
        }

        return new moodle_url("/enrol/$name/unenrolself.php", array('enrolid'=>$instance->id));;
    }

    /**
     * Adds enrol instance UI to course edit form
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param object $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return void
     */
    public function course_edit_form($instance, MoodleQuickForm $mform, $data, $context) {
        // override - usually at least enable/disable switch, has to add own form header
    }

    /**
     * Validates course edit form data
     *
     * @param object $instance enrol instance or null if does not exist yet
     * @param array $data
     * @param object $context context of existing course or parent category if course does not exist
     * @return array errors array
     */
    public function course_edit_validation($instance, array $data, $context) {
        return array();
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param object $course
     * @param object $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        if ($inserted) {
            if ($this->get_config('defaultenrol')) {
                $this->add_default_instance($course);
            }
        }
    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = NULL) {
        global $DB;

        if ($course->id == SITEID) {
            throw new coding_exception('Invalid request to add enrol instance to frontpage.');
        }

        $instance = new stdClass();
        $instance->enrol          = $this->get_name();
        $instance->status         = ENROL_INSTANCE_ENABLED;
        $instance->courseid       = $course->id;
        $instance->enrolstartdate = 0;
        $instance->enrolenddate   = 0;
        $instance->timemodified   = time();
        $instance->timecreated    = $instance->timemodified;
        $instance->sortorder      = $DB->get_field('enrol', 'COALESCE(MAX(sortorder), -1) + 1', array('courseid'=>$course->id));

        $fields = (array)$fields;
        unset($fields['enrol']);
        unset($fields['courseid']);
        unset($fields['sortorder']);
        foreach($fields as $field=>$value) {
            $instance->$field = $value;
        }

        return $DB->insert_record('enrol', $instance);
    }

    /**
     * Add new instance of enrol plugin with default settings,
     * called when adding new instance manually or when adding new course.
     *
     * Not all plugins support this.
     *
     * @param object $course
     * @return int id of new instance or null if no default supported
     */
    public function add_default_instance($course) {
        return null;
    }

    /**
     * Delete course enrol plugin instance, unenrol all users.
     * @param object $instance
     * @return void
     */
    public function delete_instance($instance) {
        global $DB;

        $name = $this->get_name();
        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        //first unenrol all users
        $participants = $DB->get_recordset('user_enrolments', array('enrolid'=>$instance->id));
        foreach ($participants as $participant) {
            $this->unenrol_user($instance, $participant->userid);
        }
        $participants->close();

        // now clean up all remainders that were not removed correctly
        $DB->delete_records('role_assignments', array('itemid'=>$instance->id, 'component'=>$name));
        $DB->delete_records('user_enrolments', array('enrolid'=>$instance->id));

        // finally drop the enrol row
        $DB->delete_records('enrol', array('id'=>$instance->id));
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        return null;
    }

    /**
     * Adds navigation links into course admin block.
     *
     * By defaults looks for manage links only.
     *
     * @param navigation_node $instancesnode
     * @param object $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        // usually adds manage users
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        return array();
    }

    /**
     * Reads version.php and determines if it is necessary
     * to execute the cron job now.
     * @return bool
     */
    public function is_cron_required() {
        global $CFG;

        $name = $this->get_name();
        $versionfile = "$CFG->dirroot/enrol/$name/version.php";
        $plugin = new stdClass();
        include($versionfile);
        if (empty($plugin->cron)) {
            return false;
        }
        $lastexecuted = $this->get_config('lastcron', 0);
        if ($lastexecuted + $plugin->cron < time()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Called for all enabled enrol plugins that returned true from is_cron_required().
     * @return void
     */
    public function cron() {
    }

    /**
     * Called when user is about to be deleted
     * @param object $user
     * @return void
     */
    public function user_delete($user) {
        global $DB;

        $sql = "SELECT e.*
                  FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id)
                 WHERE e.enrol = :name AND ue.userid = :userid";
        $params = array('name'=>$this->get_name(), 'userid'=>$user->id);

        $rs = $DB->get_recordset_sql($sql, $params);
        foreach($rs as $instance) {
            $this->unenrol_user($instance, $user->id);
        }
        $rs->close();
    }

    /**
     * Returns an enrol_user_button that takes the user to a page where they are able to
     * enrol users into the managers course through this plugin.
     *
     * Optional: If the plugin supports manual enrolments it can choose to override this
     * otherwise it shouldn't
     *
     * @param course_enrolment_manager $manager
     * @return enrol_user_button|false
     */
    public function get_manual_enrol_button(course_enrolment_manager $manager) {
        return false;
    }

    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        return array();
    }

    /**
     * Returns true if the plugin has one or more bulk operations that can be performed on
     * user enrolments.
     *
     * @return bool
     */
    public function has_bulk_operations() {
       return false;
    }

    /**
     * Return an array of enrol_bulk_enrolment_operation objects that define
     * the bulk actions that can be performed on user enrolments by the plugin.
     *
     * @return array
     */
    public function get_bulk_operations() {
        return array();
    }
}
