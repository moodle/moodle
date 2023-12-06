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

/** @deprecated - enrol caching was reworked, use ENROL_MAX_TIMESTAMP instead */
define('ENROL_REQUIRE_LOGIN_CACHE_PERIOD', 1800);

/** The timestamp indicating forever */
define('ENROL_MAX_TIMESTAMP', 2147483647);

/** When user disappears from external source, the enrolment is completely removed */
define('ENROL_EXT_REMOVED_UNENROL', 0);

/** When user disappears from external source, the enrolment is kept as is - one way sync */
define('ENROL_EXT_REMOVED_KEEP', 1);

/** @deprecated since 2.4 not used any more, migrate plugin to new restore methods */
define('ENROL_RESTORE_TYPE', 'enrolrestore');

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
 * Do not send email.
 */
define('ENROL_DO_NOT_SEND_EMAIL', 0);

/**
 * Send email from course contact.
 */
define('ENROL_SEND_EMAIL_FROM_COURSE_CONTACT', 1);

/**
 * Send email from enrolment key holder.
 */
define('ENROL_SEND_EMAIL_FROM_KEY_HOLDER', 2);

/**
 * Send email from no reply address.
 */
define('ENROL_SEND_EMAIL_FROM_NOREPLY', 3);

/** Edit enrolment action. */
define('ENROL_ACTION_EDIT', 'editenrolment');

/** Unenrol action. */
define('ENROL_ACTION_UNENROL', 'unenrol');

/**
 * Returns instances of enrol plugins
 * @param bool $enabled return enabled only
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
        $plugins = core_component::get_plugin_list('enrol');
        ksort($plugins);
    }

    foreach ($plugins as $plugin=>$location) {
        $class = "enrol_{$plugin}_plugin";
        if (!class_exists($class)) {
            if (!file_exists("$location/lib.php")) {
                continue;
            }
            include_once("$location/lib.php");
            if (!class_exists($class)) {
                continue;
            }
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

    $name = clean_param($name, PARAM_PLUGIN);

    if (empty($name)) {
        // ignore malformed or missing plugin names completely
        return null;
    }

    $location = "$CFG->dirroot/enrol/$name";

    $class = "enrol_{$name}_plugin";
    if (!class_exists($class)) {
        if (!file_exists("$location/lib.php")) {
            return null;
        }
        include_once("$location/lib.php");
        if (!class_exists($class)) {
            return null;
        }
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
 * This function may be very slow, use only once after log-in or login-as.
 *
 * @param stdClass $user User object.
 * @param bool $ignoreintervalcheck Force to ignore checking configured sync intervals.
 *
 * @return void
 */
function enrol_check_plugins($user, bool $ignoreintervalcheck = true) {
    global $CFG;

    if (empty($user->id) or isguestuser($user)) {
        // shortcut - there is no enrolment work for guests and not-logged-in users
        return;
    }

    // originally there was a broken admin test, but accidentally it was non-functional in 2.2,
    // which proved it was actually not necessary.

    static $inprogress = array();  // To prevent this function being called more than once in an invocation

    if (!empty($inprogress[$user->id])) {
        return;
    }

    $syncinterval = isset($CFG->enrolments_sync_interval) ? (int)$CFG->enrolments_sync_interval : HOURSECS;
    $needintervalchecking = !$ignoreintervalcheck && !empty($syncinterval);

    if ($needintervalchecking) {
        $lastsync = get_user_preferences('last_time_enrolments_synced', 0, $user);
        if (time() - $lastsync < $syncinterval) {
            return;
        }
    }

    $inprogress[$user->id] = true;  // Set the flag

    $enabled = enrol_get_plugins(true);

    foreach($enabled as $enrol) {
        $enrol->sync_user_enrolments($user);
    }

    if ($needintervalchecking) {
        set_user_preference('last_time_enrolments_synced', time(), $user);
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

    $user1 = isset($user1->id) ? $user1->id : $user1;
    $user2 = isset($user2->id) ? $user2->id : $user2;

    if (empty($user1) or empty($user2)) {
        return false;
    }

    if (!$plugins = explode(',', $CFG->enrol_plugins_enabled)) {
        return false;
    }

    list($plugins1, $params1) = $DB->get_in_or_equal($plugins, SQL_PARAMS_NAMED, 'ee1');
    list($plugins2, $params2) = $DB->get_in_or_equal($plugins, SQL_PARAMS_NAMED, 'ee2');
    $params = array_merge($params1, $params2);
    $params['enabled1'] = ENROL_INSTANCE_ENABLED;
    $params['enabled2'] = ENROL_INSTANCE_ENABLED;
    $params['active1'] = ENROL_USER_ACTIVE;
    $params['active2'] = ENROL_USER_ACTIVE;
    $params['user1']   = $user1;
    $params['user2']   = $user2;

    $ctxselect = '';
    $ctxjoin = '';
    if ($preloadcontexts) {
        $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_COURSE;
    }

    $sql = "SELECT c.* $ctxselect
              FROM {course} c
              JOIN (
                SELECT DISTINCT c.id
                  FROM {course} c
                  JOIN {enrol} e1 ON (c.id = e1.courseid AND e1.status = :enabled1 AND e1.enrol $plugins1)
                  JOIN {user_enrolments} ue1 ON (ue1.enrolid = e1.id AND ue1.status = :active1 AND ue1.userid = :user1)
                  JOIN {enrol} e2 ON (c.id = e2.courseid AND e2.status = :enabled2 AND e2.enrol $plugins2)
                  JOIN {user_enrolments} ue2 ON (ue2.enrolid = e2.id AND ue2.status = :active2 AND ue2.userid = :user2)
                 WHERE c.visible = 1
              ) ec ON ec.id = c.id
              $ctxjoin";

    if ($checkexistsonly) {
        return $DB->record_exists_sql($sql, $params);
    } else {
        $courses = $DB->get_records_sql($sql, $params);
        if ($preloadcontexts) {
            array_map('context_helper::preload_from_record', $courses);
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

    $coursecontext = context_course::instance($course->id);

    $instances = enrol_get_instances($course->id, true);
    $plugins   = enrol_get_plugins(true);

    // we do not want to break all course pages if there is some borked enrol plugin, right?
    foreach ($instances as $k=>$instance) {
        if (!isset($plugins[$instance->enrol])) {
            unset($instances[$k]);
        }
    }

    $usersnode = $coursenode->add(get_string('users'), null, navigation_node::TYPE_CONTAINER, null, 'users');

    // List all participants - allows assigning roles, groups, etc.
    // Have this available even in the site context as the page is still accessible from the frontpage.
    if (has_capability('moodle/course:enrolreview', $coursecontext)) {
        $url = new moodle_url('/user/index.php', array('id' => $course->id));
        $usersnode->add(get_string('enrolledusers', 'enrol'), $url, navigation_node::TYPE_SETTING,
            null, 'review', new pix_icon('i/enrolusers', ''));
    }

    if ($course->id != SITEID) {
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

        if (has_capability('moodle/course:renameroles', $coursecontext)) {
            $url = new moodle_url('/enrol/renameroles.php', array('id' => $course->id));
            $instancesnode->add(
                get_string('rolerenaming'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'renameroles'
            );
        }
    }

    // Manage groups in this course or even frontpage
    if (($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $coursecontext)) {
        $url = new moodle_url('/group/index.php', array('id'=>$course->id));
        $usersnode->add(get_string('groups'), $url, navigation_node::TYPE_SETTING, null, 'groups', new pix_icon('i/group', ''));
    }

    if (has_any_capability(
        [ 'moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:review'],
        $coursecontext
    )) {
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
                $permissionsnode->add(get_string('assignedroles', 'role'), $url, navigation_node::TYPE_SETTING, null, 'roles', new pix_icon('i/assignroles', ''));
            }
        }
        // Check role permissions
        if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override'), $coursecontext)) {
            $url = new moodle_url('/admin/roles/check.php', array('contextid'=>$coursecontext->id));
            $permissionsnode->add(get_string('checkpermissions', 'role'), $url, navigation_node::TYPE_SETTING, null, 'permissions', new pix_icon('i/checkpermissions', ''));
        }
    }

     // Deal somehow with users that are not enrolled but still got a role somehow
    if ($course->id != SITEID) {
        //TODO, create some new UI for role assignments at course level
        if (has_capability('moodle/course:reviewotherusers', $coursecontext)) {
            $url = new moodle_url('/enrol/otherusers.php', array('id'=>$course->id));
            $usersnode->add(get_string('notenrolledusers', 'enrol'), $url, navigation_node::TYPE_SETTING, null, 'otherusers', new pix_icon('i/assignroles', ''));
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
                    $coursenode->add(get_string('unenrolme', 'core_enrol'), $unenrollink,
                        navigation_node::TYPE_SETTING, null, 'unenrolself', new pix_icon('i/user', ''));
                    $coursenode->get('unenrolself')->set_force_into_more_menu(true);
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
 * The $fields param is a list of field names to ADD so name just the fields you really need,
 * which will be added and uniq'd.
 *
 * If $allaccessible is true, this will additionally return courses that the current user is not
 * enrolled in, but can access because they are open to the user for other reasons (course view
 * permission, currently viewing course as a guest, or course allows guest access without
 * password).
 *
 * @param string|array $fields Extra fields to be returned (array or comma-separated list).
 * @param string|null $sort Comma separated list of fields to sort by, defaults to respecting navsortmycoursessort.
 * Allowed prefixes for sort fields are: "ul" for the user_lastaccess table, "c" for the courses table,
 * "ue" for the user_enrolments table.
 * @param int $limit max number of courses
 * @param array $courseids the list of course ids to filter by
 * @param bool $allaccessible Include courses user is not enrolled in, but can access
 * @param int $offset Offset the result set by this number
 * @param array $excludecourses IDs of hidden courses to exclude from search
 * @return array
 */
function enrol_get_my_courses($fields = null, $sort = null, $limit = 0, $courseids = [], $allaccessible = false,
    $offset = 0, $excludecourses = []) {
    global $DB, $USER, $CFG;

    // Allowed prefixes and field names.
    $allowedprefixesandfields = ['c' => array_keys($DB->get_columns('course')),
                                'ul' => array_keys($DB->get_columns('user_lastaccess')),
                                'ue' => array_keys($DB->get_columns('user_enrolments'))];

    // Re-Arrange the course sorting according to the admin settings.
    $sort = enrol_get_courses_sortingsql($sort);

    // Guest account does not have any enrolled courses.
    if (!$allaccessible && (isguestuser() or !isloggedin())) {
        return array();
    }

    $basefields = [
        'id', 'category', 'sortorder',
        'shortname', 'fullname', 'idnumber',
        'startdate', 'visible',
        'groupmode', 'groupmodeforce', 'cacherev',
        'showactivitydates', 'showcompletionconditions',
    ];

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
        throw new coding_exception('Invalid $fields parameter in enrol_get_my_courses()');
    }
    if (in_array('*', $fields)) {
        $fields = array('*');
    }

    $orderby = "";
    $sort    = trim($sort);
    $sorttimeaccess = false;
    if (!empty($sort)) {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            // Make sure that there are no more white spaces in sortparams after explode.
            $sortparams = array_values(array_filter(explode(' ', $rawsort)));
            // If more than 2 values present then throw coding_exception.
            if (isset($sortparams[2])) {
                throw new coding_exception('Invalid $sort parameter in enrol_get_my_courses()');
            }
            // Check the sort ordering if present, at the beginning.
            if (isset($sortparams[1]) && (preg_match("/^(asc|desc)$/i", $sortparams[1]) === 0)) {
                throw new coding_exception('Invalid sort direction in $sort parameter in enrol_get_my_courses()');
            }

            $sortfield = $sortparams[0];
            $sortdirection = $sortparams[1] ?? 'asc';
            if (strpos($sortfield, '.') !== false) {
                $sortfieldparams = explode('.', $sortfield);
                // Check if more than one dots present in the prefix field.
                if (isset($sortfieldparams[2])) {
                    throw new coding_exception('Invalid $sort parameter in enrol_get_my_courses()');
                }
                list($prefix, $fieldname) = [$sortfieldparams[0], $sortfieldparams[1]];
                // Check if the field name matches with the allowed prefix.
                if (array_key_exists($prefix, $allowedprefixesandfields) &&
                    (in_array($fieldname, $allowedprefixesandfields[$prefix]))) {
                    if ($prefix === 'ul') {
                        $sorts[] = "COALESCE({$prefix}.{$fieldname}, 0) {$sortdirection}";
                        $sorttimeaccess = true;
                    } else {
                        // Check if the field name that matches with the prefix and just append to sorts.
                        $sorts[] = $rawsort;
                    }
                } else {
                    throw new coding_exception('Invalid $sort parameter in enrol_get_my_courses()');
                }
            } else {
                // Check if the field name matches with $allowedprefixesandfields.
                $found = false;
                foreach (array_keys($allowedprefixesandfields) as $prefix) {
                    if (in_array($sortfield, $allowedprefixesandfields[$prefix])) {
                        if ($prefix === 'ul') {
                            $sorts[] = "COALESCE({$prefix}.{$sortfield}, 0) {$sortdirection}";
                            $sorttimeaccess = true;
                        } else {
                            $sorts[] = "{$prefix}.{$sortfield} {$sortdirection}";
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    // The param is not found in $allowedprefixesandfields.
                    throw new coding_exception('Invalid $sort parameter in enrol_get_my_courses()');
                }
            }
        }
        $sort = implode(',', $sorts);
        $orderby = "ORDER BY $sort";
    }

    $wheres = ['c.id <> ' . SITEID];
    $params = [];

    if (isset($USER->loginascontext) and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
        // list _only_ this course - anything else is asking for trouble...
        $wheres[] = "courseid = :loginas";
        $params['loginas'] = $USER->loginascontext->instanceid;
    }

    $coursefields = 'c.' .join(',c.', $fields);
    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
    $params['contextlevel'] = CONTEXT_COURSE;
    $wheres = implode(" AND ", $wheres);

    $timeaccessselect = "";
    $timeaccessjoin = "";

    if (!empty($courseids)) {
        list($courseidssql, $courseidsparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $wheres = sprintf("%s AND c.id %s", $wheres, $courseidssql);
        $params = array_merge($params, $courseidsparams);
    }

    if (!empty($excludecourses)) {
        list($courseidssql, $courseidsparams) = $DB->get_in_or_equal($excludecourses, SQL_PARAMS_NAMED, 'param', false);
        $wheres = sprintf("%s AND c.id %s", $wheres, $courseidssql);
        $params = array_merge($params, $courseidsparams);
    }

    $courseidsql = "";
    // Logged-in, non-guest users get their enrolled courses.
    if (!isguestuser() && isloggedin()) {
        $courseidsql .= "
                SELECT DISTINCT e.courseid
                  FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid1)
                 WHERE ue.status = :active AND e.status = :enabled AND ue.timestart <= :now1
                       AND (ue.timeend = 0 OR ue.timeend > :now2)";
        $params['userid1'] = $USER->id;
        $params['active'] = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
        $params['now1'] = $params['now2'] = time();

        if ($sorttimeaccess) {
            $params['userid2'] = $USER->id;
            $timeaccessselect = ', ul.timeaccess as lastaccessed';
            $timeaccessjoin = "LEFT JOIN {user_lastaccess} ul ON (ul.courseid = c.id AND ul.userid = :userid2)";
        }
    }

    // When including non-enrolled but accessible courses...
    if ($allaccessible) {
        if (is_siteadmin()) {
            // Site admins can access all courses.
            $courseidsql = "SELECT DISTINCT c2.id AS courseid FROM {course} c2";
        } else {
            // If we used the enrolment as well, then this will be UNIONed.
            if ($courseidsql) {
                $courseidsql .= " UNION ";
            }

            // Include courses with guest access and no password.
            $courseidsql .= "
                    SELECT DISTINCT e.courseid
                      FROM {enrol} e
                     WHERE e.enrol = 'guest' AND e.password = :emptypass AND e.status = :enabled2";
            $params['emptypass'] = '';
            $params['enabled2'] = ENROL_INSTANCE_ENABLED;

            // Include courses where the current user is currently using guest access (may include
            // those which require a password).
            $courseids = [];
            $accessdata = get_user_accessdata($USER->id);
            foreach ($accessdata['ra'] as $contextpath => $roles) {
                if (array_key_exists($CFG->guestroleid, $roles)) {
                    // Work out the course id from context path.
                    $context = context::instance_by_id(preg_replace('~^.*/~', '', $contextpath));
                    if ($context instanceof context_course) {
                        $courseids[$context->instanceid] = true;
                    }
                }
            }

            // Include courses where the current user has moodle/course:view capability.
            $courses = get_user_capability_course('moodle/course:view', null, false);
            if (!$courses) {
                $courses = [];
            }
            foreach ($courses as $course) {
                $courseids[$course->id] = true;
            }

            // If there are any in either category, list them individually.
            if ($courseids) {
                list ($allowedsql, $allowedparams) = $DB->get_in_or_equal(
                        array_keys($courseids), SQL_PARAMS_NAMED);
                $courseidsql .= "
                        UNION
                       SELECT DISTINCT c3.id AS courseid
                         FROM {course} c3
                        WHERE c3.id $allowedsql";
                $params = array_merge($params, $allowedparams);
            }
        }
    }

    // Note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why
    // we have the subselect there.
    $sql = "SELECT $coursefields $ccselect $timeaccessselect
              FROM {course} c
              JOIN ($courseidsql) en ON (en.courseid = c.id)
           $timeaccessjoin
           $ccjoin
             WHERE $wheres
          $orderby";

    $courses = $DB->get_records_sql($sql, $params, $offset, $limit);

    // preload contexts and check visibility
    foreach ($courses as $id=>$course) {
        context_helper::preload_from_record($course);
        if (!$course->visible) {
            if (!$context = context_course::instance($id, IGNORE_MISSING)) {
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
 * Returns SQL ORDER arguments which reflect the admin settings to sort my courses.
 *
 * @param string|null $sort SQL ORDER arguments which were originally requested (optionally).
 * @return string SQL ORDER arguments.
 */
function enrol_get_courses_sortingsql($sort = null) {
    global $CFG;

    // Prepare the visible SQL fragment as empty.
    $visible = '';
    // Only create a visible SQL fragment if the caller didn't already pass a sort order which contains the visible field.
    if ($sort === null || strpos($sort, 'visible') === false) {
        // If the admin did not explicitly want to have shown and hidden courses sorted as one list, we will sort hidden
        // courses to the end of the course list.
        if (!isset($CFG->navsortmycourseshiddenlast) || $CFG->navsortmycourseshiddenlast == true) {
            $visible = 'visible DESC, ';
        }
    }

    // Only create a sortorder SQL fragment if the caller didn't already pass one.
    if ($sort === null) {
        // If the admin has configured a course sort order, we will use this.
        if (!empty($CFG->navsortmycoursessort)) {
            $sort = $CFG->navsortmycoursessort . ' ASC';

            // Otherwise we will fall back to the sortorder sorting.
        } else {
            $sort = 'sortorder ASC';
        }
    }

    return $visible . $sort;
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
 * Note: Use {@link enrol_get_all_users_courses()} if you need the list without any capability checks.
 *
 * The $fields param is a list of field names to ADD so name just the fields you really need,
 * which will be added and uniq'd.
 *
 * @param int $userid User whose courses are returned, defaults to the current user.
 * @param bool $onlyactive Return only active enrolments in courses user may see.
 * @param string|array $fields Extra fields to be returned (array or comma-separated list).
 * @param string|null $sort Comma separated list of fields to sort by, defaults to respecting navsortmycoursessort.
 * @return array
 */
function enrol_get_users_courses($userid, $onlyactive = false, $fields = null, $sort = null) {
    global $DB;

    $courses = enrol_get_all_users_courses($userid, $onlyactive, $fields, $sort);

    // preload contexts and check visibility
    if ($onlyactive) {
        foreach ($courses as $id=>$course) {
            context_helper::preload_from_record($course);
            if (!$course->visible) {
                if (!$context = context_course::instance($id)) {
                    unset($courses[$id]);
                    continue;
                }
                if (!has_capability('moodle/course:viewhiddencourses', $context, $userid)) {
                    unset($courses[$id]);
                    continue;
                }
            }
        }
    }

    return $courses;
}

/**
 * Returns list of roles per users into course.
 *
 * @param int $courseid Course id.
 * @return array Array[$userid][$roleid] = role_assignment.
 */
function enrol_get_course_users_roles(int $courseid) : array {
    global $DB;

    $context = context_course::instance($courseid);

    $roles = array();

    $records = $DB->get_recordset('role_assignments', array('contextid' => $context->id));
    foreach ($records as $record) {
        if (isset($roles[$record->userid]) === false) {
            $roles[$record->userid] = array();
        }
        $roles[$record->userid][$record->roleid] = $record;
    }
    $records->close();

    return $roles;
}

/**
 * Can user access at least one enrolled course?
 *
 * Cheat if necessary, but find out as fast as possible!
 *
 * @param int|stdClass $user null means use current user
 * @return bool
 */
function enrol_user_sees_own_courses($user = null) {
    global $USER;

    if ($user === null) {
        $user = $USER;
    }
    $userid = is_object($user) ? $user->id : $user;

    // Guest account does not have any courses
    if (isguestuser($userid) or empty($userid)) {
        return false;
    }

    // Let's cheat here if this is the current user,
    // if user accessed any course recently, then most probably
    // we do not need to query the database at all.
    if ($USER->id == $userid) {
        if (!empty($USER->enrol['enrolled'])) {
            foreach ($USER->enrol['enrolled'] as $until) {
                if ($until > time()) {
                    return true;
                }
            }
        }
    }

    // Now the slow way.
    $courses = enrol_get_all_users_courses($userid, true);
    foreach($courses as $course) {
        if ($course->visible) {
            return true;
        }
        context_helper::preload_from_record($course);
        $context = context_course::instance($course->id);
        if (has_capability('moodle/course:viewhiddencourses', $context, $user)) {
            return true;
        }
    }

    return false;
}

/**
 * Returns list of courses user is enrolled into without performing any capability checks.
 *
 * The $fields param is a list of field names to ADD so name just the fields you really need,
 * which will be added and uniq'd.
 *
 * @param int $userid User whose courses are returned, defaults to the current user.
 * @param bool $onlyactive Return only active enrolments in courses user may see.
 * @param string|array $fields Extra fields to be returned (array or comma-separated list).
 * @param string|null $sort Comma separated list of fields to sort by, defaults to respecting navsortmycoursessort.
 * @return array
 */
function enrol_get_all_users_courses($userid, $onlyactive = false, $fields = null, $sort = null) {
    global $DB;

    // Re-Arrange the course sorting according to the admin settings.
    $sort = enrol_get_courses_sortingsql($sort);

    // Guest account does not have any courses
    if (isguestuser($userid) or empty($userid)) {
        return(array());
    }

    $basefields = array('id', 'category', 'sortorder',
            'shortname', 'fullname', 'idnumber',
            'startdate', 'visible',
            'defaultgroupingid',
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
        throw new coding_exception('Invalid $fields parameter in enrol_get_all_users_courses()');
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

    $params = [];

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
    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
    $params['contextlevel'] = CONTEXT_COURSE;

    //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
    $sql = "SELECT $coursefields $ccselect
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                 $subwhere
                   ) en ON (en.courseid = c.id)
           $ccjoin
             WHERE c.id <> " . SITEID . "
          $orderby";
    $params['userid']  = $userid;

    $courses = $DB->get_records_sql($sql, $params);

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
 * If a user id is passed, only enrolments that the user has permission to un-enrol will be removed,
 * otherwise all enrolments in the course will be removed.
 *
 * @param stdClass $course
 * @param int|null $userid
 * @return void
 */
function enrol_course_delete($course, $userid = null) {
    global $DB;

    $context = context_course::instance($course->id);
    $instances = enrol_get_instances($course->id, false);
    $plugins = enrol_get_plugins(true);

    if ($userid) {
        // If the user id is present, include only course enrolment instances which allow manual unenrolment and
        // the given user have a capability to perform unenrolment.
        $instances = array_filter($instances, function($instance) use ($userid, $plugins, $context) {
            $unenrolcap = "enrol/{$instance->enrol}:unenrol";
            return $plugins[$instance->enrol]->allow_unenrol($instance) &&
                has_capability($unenrolcap, $context, $userid);
        });
    }

    foreach ($instances as $instance) {
        if (isset($plugins[$instance->enrol])) {
            $plugins[$instance->enrol]->delete_instance($instance);
        }
        // low level delete in case plugin did not do it
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

    if ($roleid && !$DB->record_exists('role', ['id' => $roleid])) {
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
            continue;
        }
        if ((isguestuser() || !isloggedin()) &&
            ($plugins[$instance->enrol]->is_self_enrol_available($instance) === true)) {
            $result = true;
            break;
        }
        if ($plugins[$instance->enrol]->show_enrolme_link($instance) === true) {
            $result = true;
            break;
        }
    }

    return $result;
}

/**
 * This function returns the end of current active user enrolment.
 *
 * It deals correctly with multiple overlapping user enrolments.
 *
 * @param int $courseid
 * @param int $userid
 * @return int|bool timestamp when active enrolment ends, false means no active enrolment now, 0 means never
 */
function enrol_get_enrolment_end($courseid, $userid) {
    global $DB;

    $sql = "SELECT ue.*
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
              JOIN {user} u ON u.id = ue.userid
             WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0";
    $params = array('enabled'=>ENROL_INSTANCE_ENABLED, 'active'=>ENROL_USER_ACTIVE, 'userid'=>$userid, 'courseid'=>$courseid);

    if (!$enrolments = $DB->get_records_sql($sql, $params)) {
        return false;
    }

    $changes = array();

    foreach ($enrolments as $ue) {
        $start = (int)$ue->timestart;
        $end = (int)$ue->timeend;
        if ($end != 0 and $end < $start) {
            debugging('Invalid enrolment start or end in user_enrolment id:'.$ue->id);
            continue;
        }
        if (isset($changes[$start])) {
            $changes[$start] = $changes[$start] + 1;
        } else {
            $changes[$start] = 1;
        }
        if ($end === 0) {
            // no end
        } else if (isset($changes[$end])) {
            $changes[$end] = $changes[$end] - 1;
        } else {
            $changes[$end] = -1;
        }
    }

    // let's sort then enrolment starts&ends and go through them chronologically,
    // looking for current status and the next future end of enrolment
    ksort($changes);

    $now = time();
    $current = 0;
    $present = null;

    foreach ($changes as $time => $change) {
        if ($time > $now) {
            if ($present === null) {
                // we have just went past current time
                $present = $current;
                if ($present < 1) {
                    // no enrolment active
                    return false;
                }
            }
            if ($present !== null) {
                // we are already in the future - look for possible end
                if ($current + $change < 1) {
                    return $time;
                }
            }
        }
        $current += $change;
    }

    if ($current > 0) {
        return 0;
    } else {
        return false;
    }
}

/**
 * Is current user accessing course via this enrolment method?
 *
 * This is intended for operations that are going to affect enrol instances.
 *
 * @param stdClass $instance enrol instance
 * @return bool
 */
function enrol_accessing_via_instance(stdClass $instance) {
    global $DB, $USER;

    if (empty($instance->id)) {
        return false;
    }

    if (is_siteadmin()) {
        // Admins may go anywhere.
        return false;
    }

    return $DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id));
}

/**
 * Returns true if user is enrolled (is participating) in course
 * this is intended for students and teachers.
 *
 * Since 2.2 the result for active enrolments and current user are cached.
 *
 * @param context $context
 * @param int|stdClass $user if null $USER is used, otherwise user object or id expected
 * @param string $withcapability extra capability name
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return bool
 */
function is_enrolled(context $context, $user = null, $withcapability = '', $onlyactive = false) {
    global $USER, $DB;

    // First find the course context.
    $coursecontext = $context->get_course_context();

    // Make sure there is a real user specified.
    if ($user === null) {
        $userid = isset($USER->id) ? $USER->id : 0;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    if (empty($userid)) {
        // Not-logged-in!
        return false;
    } else if (isguestuser($userid)) {
        // Guest account can not be enrolled anywhere.
        return false;
    }

    // Note everybody participates on frontpage, so for other contexts...
    if ($coursecontext->instanceid != SITEID) {
        // Try cached info first - the enrolled flag is set only when active enrolment present.
        if ($USER->id == $userid) {
            $coursecontext->reload_if_dirty();
            if (isset($USER->enrol['enrolled'][$coursecontext->instanceid])) {
                if ($USER->enrol['enrolled'][$coursecontext->instanceid] > time()) {
                    if ($withcapability and !has_capability($withcapability, $context, $userid)) {
                        return false;
                    }
                    return true;
                }
            }
        }

        if ($onlyactive) {
            // Look for active enrolments only.
            $until = enrol_get_enrolment_end($coursecontext->instanceid, $userid);

            if ($until === false) {
                return false;
            }

            if ($USER->id == $userid) {
                if ($until == 0) {
                    $until = ENROL_MAX_TIMESTAMP;
                }
                $USER->enrol['enrolled'][$coursecontext->instanceid] = $until;
                if (isset($USER->enrol['tempguest'][$coursecontext->instanceid])) {
                    unset($USER->enrol['tempguest'][$coursecontext->instanceid]);
                    remove_temp_course_roles($coursecontext);
                }
            }

        } else {
            // Any enrolment is good for us here, even outdated, disabled or inactive.
            $sql = "SELECT 'x'
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                      JOIN {user} u ON u.id = ue.userid
                     WHERE ue.userid = :userid AND u.deleted = 0";
            $params = array('userid' => $userid, 'courseid' => $coursecontext->instanceid);
            if (!$DB->record_exists_sql($sql, $params)) {
                return false;
            }
        }
    }

    if ($withcapability and !has_capability($withcapability, $context, $userid)) {
        return false;
    }

    return true;
}

/**
 * Returns an array of joins, wheres and params that will limit the group of
 * users to only those enrolled and with given capability (if specified).
 *
 * Note this join will return duplicate rows for users who have been enrolled
 * several times (e.g. as manual enrolment, and as self enrolment). You may
 * need to use a SELECT DISTINCT in your query (see get_enrolled_sql for example).
 *
 * In case is guaranteed some of the joins never match any rows, the resulting
 * join_sql->cannotmatchanyrows will be true. This happens when the capability
 * is prohibited.
 *
 * @param context $context
 * @param string $prefix optional, a prefix to the user id column
 * @param string|array $capability optional, may include a capability name, or array of names.
 *      If an array is provided then this is the equivalent of a logical 'OR',
 *      i.e. the user needs to have one of these capabilities.
 * @param int|array $groupids The groupids, 0 or [] means all groups and USERSWITHOUTGROUP no group
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @param bool $onlysuspended inverse of onlyactive, consider only suspended enrolments
 * @param int $enrolid The enrolment ID. If not 0, only users enrolled using this enrolment method will be returned.
 * @return \core\dml\sql_join Contains joins, wheres, params and cannotmatchanyrows
 */
function get_enrolled_with_capabilities_join(context $context, $prefix = '', $capability = '', $groupids = 0,
        $onlyactive = false, $onlysuspended = false, $enrolid = 0) {
    $uid = $prefix . 'u.id';
    $joins = array();
    $wheres = array();
    $cannotmatchanyrows = false;

    $enrolledjoin = get_enrolled_join($context, $uid, $onlyactive, $onlysuspended, $enrolid);
    $joins[] = $enrolledjoin->joins;
    $wheres[] = $enrolledjoin->wheres;
    $params = $enrolledjoin->params;
    $cannotmatchanyrows = $cannotmatchanyrows || $enrolledjoin->cannotmatchanyrows;

    if (!empty($capability)) {
        $capjoin = get_with_capability_join($context, $capability, $uid);
        $joins[] = $capjoin->joins;
        $wheres[] = $capjoin->wheres;
        $params = array_merge($params, $capjoin->params);
        $cannotmatchanyrows = $cannotmatchanyrows || $capjoin->cannotmatchanyrows;
    }

    if ($groupids) {
        $groupjoin = groups_get_members_join($groupids, $uid, $context);
        $joins[] = $groupjoin->joins;
        $params = array_merge($params, $groupjoin->params);
        if (!empty($groupjoin->wheres)) {
            $wheres[] = $groupjoin->wheres;
        }
        $cannotmatchanyrows = $cannotmatchanyrows || $groupjoin->cannotmatchanyrows;
    }

    $joins = implode("\n", $joins);
    $wheres[] = "{$prefix}u.deleted = 0";
    $wheres = implode(" AND ", $wheres);

    return new \core\dml\sql_join($joins, $wheres, $params, $cannotmatchanyrows);
}

/**
 * Returns array with sql code and parameters returning all ids
 * of users enrolled into course.
 *
 * This function is using 'eu[0-9]+_' prefix for table names and parameters.
 *
 * @param context $context
 * @param string $withcapability
 * @param int|array $groupids The groupids, 0 or [] means all groups and USERSWITHOUTGROUP no group
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @param bool $onlysuspended inverse of onlyactive, consider only suspended enrolments
 * @param int $enrolid The enrolment ID. If not 0, only users enrolled using this enrolment method will be returned.
 * @return array list($sql, $params)
 */
function get_enrolled_sql(context $context, $withcapability = '', $groupids = 0, $onlyactive = false, $onlysuspended = false,
                          $enrolid = 0) {

    // Use unique prefix just in case somebody makes some SQL magic with the result.
    static $i = 0;
    $i++;
    $prefix = 'eu' . $i . '_';

    $capjoin = get_enrolled_with_capabilities_join(
            $context, $prefix, $withcapability, $groupids, $onlyactive, $onlysuspended, $enrolid);

    $sql = "SELECT DISTINCT {$prefix}u.id
              FROM {user} {$prefix}u
            $capjoin->joins
             WHERE $capjoin->wheres";

    return array($sql, $capjoin->params);
}

/**
 * Returns array with sql joins and parameters returning all ids
 * of users enrolled into course.
 *
 * This function is using 'ej[0-9]+_' prefix for table names and parameters.
 *
 * @throws coding_exception
 *
 * @param context $context
 * @param string $useridcolumn User id column used the calling query, e.g. u.id
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @param bool $onlysuspended inverse of onlyactive, consider only suspended enrolments
 * @param int $enrolid The enrolment ID. If not 0, only users enrolled using this enrolment method will be returned.
 * @return \core\dml\sql_join Contains joins, wheres, params
 */
function get_enrolled_join(context $context, $useridcolumn, $onlyactive = false, $onlysuspended = false, $enrolid = 0) {
    // Use unique prefix just in case somebody makes some SQL magic with the result.
    static $i = 0;
    $i++;
    $prefix = 'ej' . $i . '_';

    // First find the course context.
    $coursecontext = $context->get_course_context();

    $isfrontpage = ($coursecontext->instanceid == SITEID);

    if ($onlyactive && $onlysuspended) {
        throw new coding_exception("Both onlyactive and onlysuspended are set, this is probably not what you want!");
    }
    if ($isfrontpage && $onlysuspended) {
        throw new coding_exception("onlysuspended is not supported on frontpage; please add your own early-exit!");
    }

    $joins  = array();
    $wheres = array();
    $params = array();

    $wheres[] = "1 = 1"; // Prevent broken where clauses later on.

    // Note all users are "enrolled" on the frontpage, but for others...
    if (!$isfrontpage) {
        $where1 = "{$prefix}ue.status = :{$prefix}active AND {$prefix}e.status = :{$prefix}enabled";
        $where2 = "{$prefix}ue.timestart < :{$prefix}now1 AND ({$prefix}ue.timeend = 0 OR {$prefix}ue.timeend > :{$prefix}now2)";

        $enrolconditions = array(
            "{$prefix}e.id = {$prefix}ue.enrolid",
            "{$prefix}e.courseid = :{$prefix}courseid",
        );
        if ($enrolid) {
            $enrolconditions[] = "{$prefix}e.id = :{$prefix}enrolid";
            $params[$prefix . 'enrolid'] = $enrolid;
        }
        $enrolconditionssql = implode(" AND ", $enrolconditions);
        $ejoin = "JOIN {enrol} {$prefix}e ON ($enrolconditionssql)";

        $params[$prefix.'courseid'] = $coursecontext->instanceid;

        if (!$onlysuspended) {
            $joins[] = "JOIN {user_enrolments} {$prefix}ue ON {$prefix}ue.userid = $useridcolumn";
            $joins[] = $ejoin;
            if ($onlyactive) {
                $wheres[] = "$where1 AND $where2";
            }
        } else {
            // Suspended only where there is enrolment but ALL are suspended.
            // Consider multiple enrols where one is not suspended or plain role_assign.
            $enrolselect = "SELECT DISTINCT {$prefix}ue.userid FROM {user_enrolments} {$prefix}ue $ejoin WHERE $where1 AND $where2";
            $joins[] = "JOIN {user_enrolments} {$prefix}ue1 ON {$prefix}ue1.userid = $useridcolumn";
            $enrolconditions = array(
                "{$prefix}e1.id = {$prefix}ue1.enrolid",
                "{$prefix}e1.courseid = :{$prefix}_e1_courseid",
            );
            if ($enrolid) {
                $enrolconditions[] = "{$prefix}e1.id = :{$prefix}e1_enrolid";
                $params[$prefix . 'e1_enrolid'] = $enrolid;
            }
            $enrolconditionssql = implode(" AND ", $enrolconditions);
            $joins[] = "JOIN {enrol} {$prefix}e1 ON ($enrolconditionssql)";
            $params["{$prefix}_e1_courseid"] = $coursecontext->instanceid;
            $wheres[] = "$useridcolumn NOT IN ($enrolselect)";
        }

        if ($onlyactive || $onlysuspended) {
            $now = round(time(), -2); // Rounding helps caching in DB.
            $params = array_merge($params, array($prefix . 'enabled' => ENROL_INSTANCE_ENABLED,
                    $prefix . 'active' => ENROL_USER_ACTIVE,
                    $prefix . 'now1' => $now, $prefix . 'now2' => $now));
        }
    }

    $joins = implode("\n", $joins);
    $wheres = implode(" AND ", $wheres);

    return new \core\dml\sql_join($joins, $wheres, $params);
}

/**
 * Returns list of users enrolled into course.
 *
 * @param context $context
 * @param string $withcapability
 * @param int|array $groupids The groupids, 0 or [] means all groups and USERSWITHOUTGROUP no group
 * @param string $userfields requested user record fields
 * @param string $orderby
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return array of user records
 */
function get_enrolled_users(context $context, $withcapability = '', $groupids = 0, $userfields = 'u.*', $orderby = null,
        $limitfrom = 0, $limitnum = 0, $onlyactive = false) {
    global $DB;

    list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupids, $onlyactive);
    $sql = "SELECT $userfields
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
             WHERE u.deleted = 0";

    if ($orderby) {
        $sql = "$sql ORDER BY $orderby";
    } else {
        list($sort, $sortparams) = users_order_by_sql('u');
        $sql = "$sql ORDER BY $sort";
        $params = array_merge($params, $sortparams);
    }

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts list of users enrolled into course (as per above function)
 *
 * @param context $context
 * @param string $withcapability
 * @param int|array $groupids The groupids, 0 or [] means all groups and USERSWITHOUTGROUP no group
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return int number of users enrolled into course
 */
function count_enrolled_users(context $context, $withcapability = '', $groupids = 0, $onlyactive = false) {
    global $DB;

    $capjoin = get_enrolled_with_capabilities_join(
            $context, '', $withcapability, $groupids, $onlyactive);

    $sql = "SELECT COUNT(DISTINCT u.id)
              FROM {user} u
            $capjoin->joins
             WHERE $capjoin->wheres AND u.deleted = 0";

    return $DB->count_records_sql($sql, $capjoin->params);
}

/**
 * Send welcome email "from" options.
 *
 * @return array list of from options
 */
function enrol_send_welcome_email_options() {
    return [
        ENROL_DO_NOT_SEND_EMAIL                 => get_string('no'),
        ENROL_SEND_EMAIL_FROM_COURSE_CONTACT    => get_string('sendfromcoursecontact', 'enrol'),
        ENROL_SEND_EMAIL_FROM_KEY_HOLDER        => get_string('sendfromkeyholder', 'enrol'),
        ENROL_SEND_EMAIL_FROM_NOREPLY           => get_string('sendfromnoreply', 'enrol')
    ];
}

/**
 * Serve the user enrolment form as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function enrol_output_fragment_user_enrolment_form($args) {
    global $CFG, $DB;

    $args = (object) $args;
    $context = $args->context;
    require_capability('moodle/course:enrolreview', $context);

    $ueid = $args->ueid;
    $userenrolment = $DB->get_record('user_enrolments', ['id' => $ueid], '*', MUST_EXIST);
    $instance = $DB->get_record('enrol', ['id' => $userenrolment->enrolid], '*', MUST_EXIST);
    $plugin = enrol_get_plugin($instance->enrol);
    $customdata = [
        'ue' => $userenrolment,
        'modal' => true,
        'enrolinstancename' => $plugin->get_instance_name($instance)
    ];

    // Set the data if applicable.
    $data = [];
    if (isset($args->formdata)) {
        $serialiseddata = json_decode($args->formdata);
        parse_str($serialiseddata, $data);
    }

    require_once("$CFG->dirroot/enrol/editenrolment_form.php");
    $mform = new \enrol_user_enrolment_form(null, $customdata, 'post', '', null, true, $data);

    if (!empty($data)) {
        $mform->set_data($data);
        $mform->is_validated();
    }

    return $mform->render();
}

/**
 * Returns the course where a user enrolment belong to.
 *
 * @param int $ueid user_enrolments id
 * @return stdClass
 */
function enrol_get_course_by_user_enrolment_id($ueid) {
    global $DB;
    $sql = "SELECT c.* FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
              JOIN {course} c ON c.id = e.courseid
             WHERE ue.id = :ueid";
    return $DB->get_record_sql($sql, array('ueid' => $ueid));
}

/**
 * Return all users enrolled in a course.
 *
 * @param int $courseid Course id or false if using $uefilter (user enrolment ids may belong to different courses)
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @param array $usersfilter Limit the results obtained to this list of user ids. $uefilter compatibility not guaranteed.
 * @param array $uefilter Limit the results obtained to this list of user enrolment ids. $usersfilter compatibility not guaranteed.
 * @param array $usergroups Limit the results of users to the ones that belong to one of the submitted group ids.
 * @return stdClass[]
 */
function enrol_get_course_users($courseid = false, $onlyactive = false, $usersfilter = [], $uefilter = [],
                                $usergroups = []) {
    global $DB;

    if (!$courseid && !$usersfilter && !$uefilter) {
        throw new \coding_exception('You should specify at least 1 filter: courseid, users or user enrolments');
    }

    $sql = "SELECT ue.id AS ueid, ue.status AS uestatus, ue.enrolid AS ueenrolid, ue.timestart AS uetimestart,
             ue.timeend AS uetimeend, ue.modifierid AS uemodifierid, ue.timecreated AS uetimecreated,
             ue.timemodified AS uetimemodified, e.status AS estatus,
             u.* FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
              JOIN {user} u ON ue.userid = u.id
             WHERE ";
    $params = array();

    if ($courseid) {
        $conditions[] = "e.courseid = :courseid";
        $params['courseid'] = $courseid;
    }

    if ($onlyactive) {
        $conditions[] = "ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND " .
            "(ue.timeend = 0 OR ue.timeend > :now2)";
        // Improves db caching.
        $params['now1']    = round(time(), -2);
        $params['now2']    = $params['now1'];
        $params['active']  = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
    }

    if ($usersfilter) {
        list($usersql, $userparams) = $DB->get_in_or_equal($usersfilter, SQL_PARAMS_NAMED);
        $conditions[] = "ue.userid $usersql";
        $params = $params + $userparams;
    }

    if ($uefilter) {
        list($uesql, $ueparams) = $DB->get_in_or_equal($uefilter, SQL_PARAMS_NAMED);
        $conditions[] = "ue.id $uesql";
        $params = $params + $ueparams;
    }

    // Only select enrolled users that belong to a specific group(s).
    if (!empty($usergroups)) {
        $usergroups = array_map(function ($item) { // Sanitize groupid to int to be save for sql.
            return (int)$item;
        }, $usergroups);
        list($ugsql, $ugparams) = $DB->get_in_or_equal($usergroups, SQL_PARAMS_NAMED);
        $conditions[] = 'ue.userid IN (SELECT userid FROM {groups_members} WHERE groupid ' . $ugsql . ')';
        $params = $params + $ugparams;
    }

    return $DB->get_records_sql($sql . ' ' . implode(' AND ', $conditions), $params);
}

/**
 * Get the list of options for the enrolment period dropdown
 *
 * @return array List of options for the enrolment period dropdown
 */
function enrol_get_period_list() {
    $periodmenu = [];
    $periodmenu[''] = get_string('unlimited');
    for ($i = 1; $i <= 365; $i++) {
        $seconds = $i * DAYSECS;
        $periodmenu[$seconds] = get_string('numdays', '', $i);
    }
    return $periodmenu;
}

/**
 * Calculate duration base on start time and end time
 *
 * @param int $timestart Time start
 * @param int $timeend Time end
 * @return float|int Calculated duration
 */
function enrol_calculate_duration($timestart, $timeend) {
    $duration = floor(($timeend - $timestart) / DAYSECS) * DAYSECS;
    return $duration;
}

/**
 * Enrolment plugins abstract class.
 *
 * All enrol plugins should be based on this class,
 * this is also the main source of documentation.
 *
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            $context = context_course::instance($instance->courseid);
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
     * @return bool - true means user with 'enrol/xxx:enrol' may enrol others freely, false means nobody may add more enrolments manually
     */
    public function allow_enrol(stdClass $instance) {
        return false;
    }

    /**
     * Does this plugin allow manual unenrolment of all users?
     * All plugins allowing this must implement 'enrol/xxx:unenrol' capability
     *
     * @param stdClass $instance course enrol instance
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol others freely, false means nobody may touch user_enrolments
     */
    public function allow_unenrol(stdClass $instance) {
        return false;
    }

    /**
     * Does this plugin allow manual unenrolment of a specific user?
     * All plugins allowing this must implement 'enrol/xxx:unenrol' capability
     *
     * This is useful especially for synchronisation plugins that
     * do suspend instead of full unenrolment.
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $ue record from user_enrolments table, specifies user
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol this user, false means nobody may touch this user enrolment
     */
    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        return $this->allow_unenrol($instance);
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
     * Does this plugin support some way to self enrol?
     * This function doesn't check user capabilities. Use can_self_enrol to check capabilities.
     *
     * @param stdClass $instance enrolment instance
     * @return bool - true means "Enrol me in this course" link could be available.
     */
    public function is_self_enrol_available(stdClass $instance) {
        return false;
    }

    /**
     * Attempt to automatically enrol current user in course without any interaction,
     * calling code has to make sure the plugin and instance are active.
     *
     * This should return either a timestamp in the future or false.
     *
     * @param stdClass $instance course enrol instance
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
     * @param bool $recovergrades restore grade history
     * @return void
     */
    public function enrol_user(stdClass $instance, $userid, $roleid = null, $timestart = 0, $timeend = 0, $status = null, $recovergrades = null) {
        global $DB, $USER, $CFG; // CFG necessary!!!

        if ($instance->courseid == SITEID) {
            throw new coding_exception('invalid attempt to enrol into frontpage course!');
        }

        $name = $this->get_name();
        $courseid = $instance->courseid;

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid, MUST_EXIST);
        if (!isset($recovergrades)) {
            $recovergrades = $CFG->recovergradesdefault;
        }

        $inserted = false;
        $updated  = false;
        if ($ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            //only update if timestart or timeend or status are different.
            if ($ue->timestart != $timestart or $ue->timeend != $timeend or (!is_null($status) and $ue->status != $status)) {
                $this->update_user_enrol($instance, $userid, $status, $timestart, $timeend);
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

        if ($inserted) {
            // Trigger event.
            $event = \core\event\user_enrolment_created::create(
                    array(
                        'objectid' => $ue->id,
                        'courseid' => $courseid,
                        'context' => $context,
                        'relateduserid' => $ue->userid,
                        'other' => array('enrol' => $name)
                        )
                    );
            $event->trigger();
            // Check if course contacts cache needs to be cleared.
            core_course_category::user_enrolment_changed($courseid, $ue->userid,
                    $ue->status, $ue->timestart, $ue->timeend);
        }

        if ($roleid) {
            // this must be done after the enrolment event so that the role_assigned event is triggered afterwards
            if ($this->roles_protected()) {
                role_assign($roleid, $userid, $context->id, 'enrol_'.$name, $instance->id);
            } else {
                role_assign($roleid, $userid, $context->id);
            }
        }

        // Recover old grades if present.
        if ($recovergrades) {
            require_once("$CFG->libdir/gradelib.php");
            grade_recover_history_grades($userid, $courseid);
        }

        // Add users to a communication room.
        if (core_communication\api::is_available()) {
            $communication = \core_communication\api::load_by_instance(
                context: $context,
                component: 'core_course',
                instancetype: 'coursecommunication',
                instanceid: $courseid,
            );
            $communication->add_members_to_room([$userid]);
        }

        // reset current user enrolment caching
        if ($userid == $USER->id) {
            if (isset($USER->enrol['enrolled'][$courseid])) {
                unset($USER->enrol['enrolled'][$courseid]);
            }
            if (isset($USER->enrol['tempguest'][$courseid])) {
                unset($USER->enrol['tempguest'][$courseid]);
                remove_temp_course_roles($context);
            }
        }
    }

    /**
     * Store user_enrolments changes and trigger event.
     *
     * @param stdClass $instance
     * @param int $userid
     * @param int $status
     * @param int $timestart
     * @param int $timeend
     * @return void
     */
    public function update_user_enrol(stdClass $instance, $userid, $status = NULL, $timestart = NULL, $timeend = NULL) {
        global $DB, $USER, $CFG;

        $name = $this->get_name();

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }

        if (!$ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            // weird, user not enrolled
            return;
        }

        $modified = false;
        $statusmodified = false;
        $timeendmodified = false;
        if (isset($status) and $ue->status != $status) {
            $ue->status = $status;
            $modified = true;
            $statusmodified = true;
        }
        if (isset($timestart) and $ue->timestart != $timestart) {
            $ue->timestart = $timestart;
            $modified = true;
        }
        if (isset($timeend) and $ue->timeend != $timeend) {
            $ue->timeend = $timeend;
            $modified = true;
            $timeendmodified = true;
        }

        if (!$modified) {
            // no change
            return;
        }

        // Add/remove users to/from communication room.
        if (core_communication\api::is_available()) {
            $course = enrol_get_course_by_user_enrolment_id($ue->id);
            $context = \core\context\course::instance($course->id);
            $communication = \core_communication\api::load_by_instance(
                context: $context,
                component: 'core_course',
                instancetype: 'coursecommunication',
                instanceid: $course->id,
            );
            if (($statusmodified && ((int) $ue->status === 1)) ||
                    ($timeendmodified && $ue->timeend !== 0 && (time() > $ue->timeend))) {
                $communication->remove_members_from_room([$userid]);
            } else {
                $communication->add_members_to_room([$userid]);
            }
        }

        $ue->modifierid = $USER->id;
        $ue->timemodified = time();
        $DB->update_record('user_enrolments', $ue);

        // User enrolments have changed, so mark user as dirty.
        mark_user_dirty($userid);

        // Invalidate core_access cache for get_suspended_userids.
        cache_helper::invalidate_by_definition('core', 'suspended_userids', array(), array($instance->courseid));

        // Trigger event.
        $event = \core\event\user_enrolment_updated::create(
                array(
                    'objectid' => $ue->id,
                    'courseid' => $instance->courseid,
                    'context' => context_course::instance($instance->courseid),
                    'relateduserid' => $ue->userid,
                    'other' => array('enrol' => $name)
                    )
                );
        $event->trigger();

        core_course_category::user_enrolment_changed($instance->courseid, $ue->userid,
                $ue->status, $ue->timestart, $ue->timeend);
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
        require_once("$CFG->dirroot/group/lib.php");

        $name = $this->get_name();
        $courseid = $instance->courseid;

        if ($instance->enrol !== $name) {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid, MUST_EXIST);

        if (!$ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid))) {
            // weird, user not enrolled
            return;
        }

        // Remove all users groups linked to this enrolment instance.
        if ($gms = $DB->get_records('groups_members', array('userid'=>$userid, 'component'=>'enrol_'.$name, 'itemid'=>$instance->id))) {
            foreach ($gms as $gm) {
                groups_remove_member($gm->groupid, $gm->userid);
            }
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

        } else {
            // the big cleanup IS necessary!
            require_once("$CFG->libdir/gradelib.php");

            // remove all remaining roles
            role_unassign_all(array('userid'=>$userid, 'contextid'=>$context->id), true, false);

            //clean up ALL invisible user data from course if this is the last enrolment - groups, grades, etc.
            groups_delete_group_members($courseid, $userid);

            grade_user_unenrol($courseid, $userid);

            $DB->delete_records('user_lastaccess', array('userid'=>$userid, 'courseid'=>$courseid));

            $ue->lastenrol = true; // means user not enrolled any more
        }
        // Trigger event.
        $event = \core\event\user_enrolment_deleted::create(
                array(
                    'courseid' => $courseid,
                    'context' => $context,
                    'relateduserid' => $ue->userid,
                    'objectid' => $ue->id,
                    'other' => array(
                        'userenrolment' => (array)$ue,
                        'enrol' => $name
                        )
                    )
                );
        $event->trigger();

        // Remove users from a communication room.
        if (core_communication\api::is_available()) {
            $communication = \core_communication\api::load_by_instance(
                context: $context,
                component: 'core_course',
                instancetype: 'coursecommunication',
                instanceid: $courseid,
            );
            $communication->remove_members_from_room([$userid]);
        }

        // User enrolments have changed, so mark user as dirty.
        mark_user_dirty($userid);

        // Check if courrse contacts cache needs to be cleared.
        core_course_category::user_enrolment_changed($courseid, $ue->userid, ENROL_USER_SUSPENDED);

        // reset current user enrolment caching
        if ($userid == $USER->id) {
            if (isset($USER->enrol['enrolled'][$courseid])) {
                unset($USER->enrol['enrolled'][$courseid]);
            }
            if (isset($USER->enrol['tempguest'][$courseid])) {
                unset($USER->enrol['tempguest'][$courseid]);
                remove_temp_course_roles($context);
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
     * This returns false for backwards compatibility, but it is really recommended.
     *
     * @since Moodle 3.1
     * @return boolean
     */
    public function use_standard_editing_ui() {
        return false;
    }

    /**
     * Return whether or not, given the current state, it is possible to add a new instance
     * of this enrolment plugin to the course.
     *
     * Default implementation is just for backwards compatibility.
     *
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {
        $link = $this->get_newinstance_link($courseid);
        return !empty($link);
    }

    /**
     * Return whether or not, given the current state, it is possible to edit an instance
     * of this enrolment plugin in the course. Used by the standard editing UI
     * to generate a link to the edit instance form if editing is allowed.
     *
     * @param stdClass $instance
     * @return boolean
     */
    public function can_edit_instance($instance) {
        $context = context_course::instance($instance->courseid);

        return has_capability('enrol/' . $instance->enrol . ':config', $context);
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
     * @deprecated since Moodle 2.8 MDL-35864 - please use can_delete_instance() instead.
     */
    public function instance_deleteable($instance) {
        throw new coding_exception('Function enrol_plugin::instance_deleteable() is deprecated, use
                enrol_plugin::can_delete_instance() instead');
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass  $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        return false;
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        debugging("The enrolment plugin '".$this->get_name()."' should override the function can_hide_show_instance().", DEBUG_DEVELOPER);
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
     * @param stdClass $instance
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

        $context = context_course::instance($instance->courseid, MUST_EXIST);

        if (!has_capability("enrol/$name:unenrolself", $context)) {
            return NULL;
        }

        if (!$DB->record_exists('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$USER->id, 'status'=>ENROL_USER_ACTIVE))) {
            return NULL;
        }

        return new moodle_url("/enrol/$name/unenrolself.php", array('enrolid'=>$instance->id));
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
     * Adds form elements to add/edit instance form.
     *
     * @since Moodle 3.1
     * @param object $instance enrol instance or null if does not exist yet
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return void
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        // Do nothing by default.
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @since Moodle 3.1
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance data loaded from the DB.
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        // No errors by default.
        debugging('enrol_plugin::edit_instance_validation() is missing. This plugin has no validation!', DEBUG_DEVELOPER);
        return array();
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

        $instance->id = $DB->insert_record('enrol', $instance);

        \core\event\enrol_instance_created::create_from_record($instance)->trigger();

        return $instance->id;
    }

    /**
     * Update instance of enrol plugin.
     *
     * @since Moodle 3.1
     * @param stdClass $instance
     * @param stdClass $data modified instance fields
     * @return boolean
     */
    public function update_instance($instance, $data) {
        global $DB;
        $properties = array('status', 'name', 'password', 'customint1', 'customint2', 'customint3',
                            'customint4', 'customint5', 'customint6', 'customint7', 'customint8',
                            'customchar1', 'customchar2', 'customchar3', 'customdec1', 'customdec2',
                            'customtext1', 'customtext2', 'customtext3', 'customtext4', 'roleid',
                            'enrolperiod', 'expirynotify', 'notifyall', 'expirythreshold',
                            'enrolstartdate', 'enrolenddate', 'cost', 'currency');

        foreach ($properties as $key) {
            if (isset($data->$key)) {
                $instance->$key = $data->$key;
            }
        }
        $instance->timemodified = time();

        $update = $DB->update_record('enrol', $instance);
        if ($update) {
            \core\event\enrol_instance_updated::create_from_record($instance)->trigger();
        }
        return $update;
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
     * Add new instance of enrol plugin with custom settings,
     * called when adding new instance manually or when adding new course.
     * Used for example on course upload.
     *
     * Not all plugins support this.
     *
     * @param stdClass $course Course object
     * @param array|null $fields instance fields
     * @return int|null id of new instance or null if not supported
     */
    public function add_custom_instance(stdClass $course, ?array $fields = null): ?int {
        return null;
    }

    /**
     * Check if enrolment plugin is supported in csv course upload.
     *
     * @return bool
     */
    public function is_csv_upload_supported(): bool {
        return false;
    }

    /**
     * Update instance status
     *
     * Override when plugin needs to do some action when enabled or disabled.
     *
     * @param stdClass $instance
     * @param int $newstatus ENROL_INSTANCE_ENABLED, ENROL_INSTANCE_DISABLED
     * @return void
     */
    public function update_status($instance, $newstatus) {
        global $DB;

        $instance->status = $newstatus;
        $DB->update_record('enrol', $instance);

        $context = context_course::instance($instance->courseid);
        \core\event\enrol_instance_updated::create_from_record($instance)->trigger();

        // Invalidate all enrol caches.
        $context->mark_dirty();
    }

    /**
     * Update instance members.
     *
     * Update communication room membership for an instance action being performed.
     *
     * @param int $instanceid ID of the enrolment instance
     * @param string $action The update action being performed
     * @param int $courseid The id of the course
     * @return void
     */
    public function update_communication(int $instanceid, string $action, int $courseid): void {
        global $DB;
        // Get enrolled instance users.
        $instanceusers = $DB->get_records('user_enrolments', ['enrolid' => $instanceid, 'status' => 0]);
        $enrolledusers = [];

        foreach ($instanceusers as $user) {
            $enrolledusers[] = $user->userid;
        }

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($courseid),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $courseid,
        );

        switch ($action) {
            case 'add':
                $communication->add_members_to_room($enrolledusers);
                break;

            case 'remove':
                $communication->remove_members_from_room($enrolledusers);
                break;
            default:
                throw new \coding_exception('Invalid action');
        }
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
        if ($gms = $DB->get_records('groups_members', array('itemid' => $instance->id, 'component' => 'enrol_' . $name))) {
            foreach ($gms as $gm) {
                groups_remove_member($gm->groupid, $gm->userid);
            }
        }
        $DB->delete_records('role_assignments', array('itemid'=>$instance->id, 'component'=>'enrol_'.$name));
        $DB->delete_records('user_enrolments', array('enrolid'=>$instance->id));

        // finally drop the enrol row
        $DB->delete_records('enrol', array('id'=>$instance->id));

        $context = context_course::instance($instance->courseid);
        \core\event\enrol_instance_deleted::create_from_record($instance)->trigger();

        // Invalidate all enrol caches.
        $context->mark_dirty();
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
     * Checks if user can self enrol.
     *
     * @param stdClass $instance enrolment instance
     * @param bool $checkuserenrolment if true will check if user enrolment is inactive.
     *             used by navigation to improve performance.
     * @return bool|string true if successful, else error message or false
     */
    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true) {
        return false;
    }

    /**
     * Return information for enrolment instance containing list of parameters required
     * for enrolment, name of enrolment plugin etc.
     *
     * @param stdClass $instance enrolment instance
     * @return stdClass|null instance info.
     */
    public function get_enrol_info(stdClass $instance) {
        return null;
    }

    /**
     * Adds navigation links into course admin block.
     *
     * By defaults looks for manage links only.
     *
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($this->use_standard_editing_ui()) {
            $context = context_course::instance($instance->courseid);
            $cap = 'enrol/' . $instance->enrol . ':config';
            if (has_capability($cap, $context)) {
                $linkparams = array('courseid' => $instance->courseid, 'id' => $instance->id, 'type' => $instance->enrol);
                $managelink = new moodle_url('/enrol/editinstance.php', $linkparams);
                $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
            }
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        $icons = array();
        if ($this->use_standard_editing_ui()) {
            $context = context_course::instance($instance->courseid);
            $cap = 'enrol/' . $instance->enrol . ':config';
            if (has_capability($cap, $context)) {
                $linkparams = array('courseid' => $instance->courseid, 'id' => $instance->id, 'type' => $instance->enrol);
                $editlink = new moodle_url("/enrol/editinstance.php", $linkparams);
                $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                    array('class' => 'iconsmall')));
            }
        }
        return $icons;
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
        $actions = [];
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;

        // Edit enrolment action.
        if ($this->allow_manage($instance) && has_capability("enrol/{$instance->enrol}:manage", $context)) {
            $title = get_string('editenrolment', 'enrol');
            $icon = new pix_icon('t/edit', $title);
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actionparams = [
                'class' => 'editenrollink',
                'rel' => $ue->id,
                'data-action' => ENROL_ACTION_EDIT
            ];
            $actions[] = new user_enrolment_action($icon, $title, $url, $actionparams);
        }

        // Unenrol action.
        if ($this->allow_unenrol_user($instance, $ue) && has_capability("enrol/{$instance->enrol}:unenrol", $context)) {
            $title = get_string('unenrol', 'enrol');
            $icon = new pix_icon('t/delete', $title);
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actionparams = [
                'class' => 'unenrollink',
                'rel' => $ue->id,
                'data-action' => ENROL_ACTION_UNENROL
            ];
            $actions[] = new user_enrolment_action($icon, $title, $url, $actionparams);
        }
        return $actions;
    }

    /**
     * Returns true if the plugin has one or more bulk operations that can be performed on
     * user enrolments.
     *
     * @param course_enrolment_manager $manager
     * @return bool
     */
    public function has_bulk_operations(course_enrolment_manager $manager) {
       return false;
    }

    /**
     * Return an array of enrol_bulk_enrolment_operation objects that define
     * the bulk actions that can be performed on user enrolments by the plugin.
     *
     * @param course_enrolment_manager $manager
     * @return array
     */
    public function get_bulk_operations(course_enrolment_manager $manager) {
        return array();
    }

    /**
     * Do any enrolments need expiration processing.
     *
     * Plugins that want to call this functionality must implement 'expiredaction' config setting.
     *
     * @param progress_trace $trace
     * @param int $courseid one course, empty mean all
     * @return bool true if any data processed, false if not
     */
    public function process_expirations(progress_trace $trace, $courseid = null) {
        global $DB;

        $name = $this->get_name();
        if (!enrol_is_enabled($name)) {
            $trace->finished();
            return false;
        }

        $processed = false;
        $params = array();
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
        }

        // Deal with expired accounts.
        $action = $this->get_config('expiredaction', ENROL_EXT_REMOVED_KEEP);

        if ($action == ENROL_EXT_REMOVED_UNENROL) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :enrol)
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now $coursesql";
            $params = array('now'=>time(), 'courselevel'=>CONTEXT_COURSE, 'enrol'=>$name, 'courseid'=>$courseid);

            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!$processed) {
                    $trace->output("Starting processing of enrol_$name expirations...");
                    $processed = true;
                }
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                if (!$this->roles_protected()) {
                    // Let's just guess what extra roles are supposed to be removed.
                    if ($instance->roleid) {
                        role_unassign($instance->roleid, $ue->userid, $ue->contextid);
                    }
                }
                // The unenrol cleans up all subcontexts if this is the only course enrolment for this user.
                $this->unenrol_user($instance, $ue->userid);
                $trace->output("Unenrolling expired user $ue->userid from course $instance->courseid", 1);
            }
            $rs->close();
            unset($instances);

        } else if ($action == ENROL_EXT_REMOVED_SUSPENDNOROLES or $action == ENROL_EXT_REMOVED_SUSPEND) {
            $instances = array();
            $sql = "SELECT ue.*, e.courseid, c.id AS contextid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :enrol)
                      JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :courselevel)
                     WHERE ue.timeend > 0 AND ue.timeend < :now
                           AND ue.status = :useractive $coursesql";
            $params = array('now'=>time(), 'courselevel'=>CONTEXT_COURSE, 'useractive'=>ENROL_USER_ACTIVE, 'enrol'=>$name, 'courseid'=>$courseid);
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $ue) {
                if (!$processed) {
                    $trace->output("Starting processing of enrol_$name expirations...");
                    $processed = true;
                }
                if (empty($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];

                if ($action == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
                    if (!$this->roles_protected()) {
                        // Let's just guess what roles should be removed.
                        $count = $DB->count_records('role_assignments', array('userid'=>$ue->userid, 'contextid'=>$ue->contextid));
                        if ($count == 1) {
                            role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0));

                        } else if ($count > 1 and $instance->roleid) {
                            role_unassign($instance->roleid, $ue->userid, $ue->contextid, '', 0);
                        }
                    }
                    // In any case remove all roles that belong to this instance and user.
                    role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'enrol_'.$name, 'itemid'=>$instance->id), true);
                    // Final cleanup of subcontexts if there are no more course roles.
                    if (0 == $DB->count_records('role_assignments', array('userid'=>$ue->userid, 'contextid'=>$ue->contextid))) {
                        role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$ue->contextid, 'component'=>'', 'itemid'=>0), true);
                    }
                }

                $this->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                $trace->output("Suspending expired user $ue->userid in course $instance->courseid", 1);
            }
            $rs->close();
            unset($instances);

        } else {
            // ENROL_EXT_REMOVED_KEEP means no changes.
        }

        if ($processed) {
            $trace->output("...finished processing of enrol_$name expirations");
        } else {
            $trace->output("No expired enrol_$name enrolments detected");
        }
        $trace->finished();

        return $processed;
    }

    /**
     * Send expiry notifications.
     *
     * Plugin that wants to have expiry notification MUST implement following:
     * - expirynotifyhour plugin setting,
     * - configuration options in instance edit form (expirynotify, notifyall and expirythreshold),
     * - notification strings (expirymessageenrollersubject, expirymessageenrollerbody,
     *   expirymessageenrolledsubject and expirymessageenrolledbody),
     * - expiry_notification provider in db/messages.php,
     * - upgrade code that sets default thresholds for existing courses (should be 1 day),
     * - something that calls this method, such as cron.
     *
     * @param progress_trace $trace (accepts bool for backwards compatibility only)
     */
    public function send_expiry_notifications($trace) {
        global $DB, $CFG;

        $name = $this->get_name();
        if (!enrol_is_enabled($name)) {
            $trace->finished();
            return;
        }

        // Unfortunately this may take a long time, it should not be interrupted,
        // otherwise users get duplicate notification.

        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);


        $expirynotifylast = $this->get_config('expirynotifylast', 0);
        $expirynotifyhour = $this->get_config('expirynotifyhour');
        if (is_null($expirynotifyhour)) {
            debugging("send_expiry_notifications() in $name enrolment plugin needs expirynotifyhour setting");
            $trace->finished();
            return;
        }

        if (!($trace instanceof progress_trace)) {
            $trace = $trace ? new text_progress_trace() : new null_progress_trace();
            debugging('enrol_plugin::send_expiry_notifications() now expects progress_trace instance as parameter!', DEBUG_DEVELOPER);
        }

        $timenow = time();
        $notifytime = usergetmidnight($timenow, $CFG->timezone) + ($expirynotifyhour * 3600);

        if ($expirynotifylast > $notifytime) {
            $trace->output($name.' enrolment expiry notifications were already sent today at '.userdate($expirynotifylast, '', $CFG->timezone).'.');
            $trace->finished();
            return;

        } else if ($timenow < $notifytime) {
            $trace->output($name.' enrolment expiry notifications will be sent at '.userdate($notifytime, '', $CFG->timezone).'.');
            $trace->finished();
            return;
        }

        $trace->output('Processing '.$name.' enrolment expiration notifications...');

        // Notify users responsible for enrolment once every day.
        $sql = "SELECT ue.*, e.expirynotify, e.notifyall, e.expirythreshold, e.courseid, c.fullname
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :name AND e.expirynotify > 0 AND e.status = :enabled)
                  JOIN {course} c ON (c.id = e.courseid)
                  JOIN {user} u ON (u.id = ue.userid AND u.deleted = 0 AND u.suspended = 0)
                 WHERE ue.status = :active AND ue.timeend > 0 AND ue.timeend > :now1 AND ue.timeend < (e.expirythreshold + :now2)
              ORDER BY ue.enrolid ASC, u.lastname ASC, u.firstname ASC, u.id ASC";
        $params = array('enabled'=>ENROL_INSTANCE_ENABLED, 'active'=>ENROL_USER_ACTIVE, 'now1'=>$timenow, 'now2'=>$timenow, 'name'=>$name);

        $rs = $DB->get_recordset_sql($sql, $params);

        $lastenrollid = 0;
        $users = array();

        foreach($rs as $ue) {
            if ($lastenrollid and $lastenrollid != $ue->enrolid) {
                $this->notify_expiry_enroller($lastenrollid, $users, $trace);
                $users = array();
            }
            $lastenrollid = $ue->enrolid;

            $enroller = $this->get_enroller($ue->enrolid);
            $context = context_course::instance($ue->courseid);

            $user = $DB->get_record('user', array('id'=>$ue->userid));

            $users[] = array('fullname'=>fullname($user, has_capability('moodle/site:viewfullnames', $context, $enroller)), 'timeend'=>$ue->timeend);

            if (!$ue->notifyall) {
                continue;
            }

            if ($ue->timeend - $ue->expirythreshold + 86400 < $timenow) {
                // Notify enrolled users only once at the start of the threshold.
                $trace->output("user $ue->userid was already notified that enrolment in course $ue->courseid expires on ".userdate($ue->timeend, '', $CFG->timezone), 1);
                continue;
            }

            $this->notify_expiry_enrolled($user, $ue, $trace);
        }
        $rs->close();

        if ($lastenrollid and $users) {
            $this->notify_expiry_enroller($lastenrollid, $users, $trace);
        }

        $trace->output('...notification processing finished.');
        $trace->finished();

        $this->set_config('expirynotifylast', $timenow);
    }

    /**
     * Returns the user who is responsible for enrolments for given instance.
     *
     * Override if plugin knows anybody better than admin.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        return get_admin();
    }

    /**
     * Notify user about incoming expiration of their enrolment,
     * it is called only if notification of enrolled users (aka students) is enabled in course.
     *
     * This is executed only once for each expiring enrolment right
     * at the start of the expiration threshold.
     *
     * @param stdClass $user
     * @param stdClass $ue
     * @param progress_trace $trace
     */
    protected function notify_expiry_enrolled($user, $ue, progress_trace $trace) {
        global $CFG;

        $name = $this->get_name();

        $oldforcelang = force_current_language($user->lang);

        $enroller = $this->get_enroller($ue->enrolid);
        $context = context_course::instance($ue->courseid);

        $a = new stdClass();
        $a->course   = format_string($ue->fullname, true, array('context'=>$context));
        $a->user     = fullname($user, true);
        $a->timeend  = userdate($ue->timeend, '', $user->timezone);
        $a->enroller = fullname($enroller, has_capability('moodle/site:viewfullnames', $context, $user));

        $subject = get_string('expirymessageenrolledsubject', 'enrol_'.$name, $a);
        $body = get_string('expirymessageenrolledbody', 'enrol_'.$name, $a);

        $message = new \core\message\message();
        $message->courseid          = $ue->courseid;
        $message->notification      = 1;
        $message->component         = 'enrol_'.$name;
        $message->name              = 'expiry_notification';
        $message->userfrom          = $enroller;
        $message->userto            = $user;
        $message->subject           = $subject;
        $message->fullmessage       = $body;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = markdown_to_html($body);
        $message->smallmessage      = $subject;
        $message->contexturlname    = $a->course;
        $message->contexturl        = (string)new moodle_url('/course/view.php', array('id'=>$ue->courseid));

        if (message_send($message)) {
            $trace->output("notifying user $ue->userid that enrolment in course $ue->courseid expires on ".userdate($ue->timeend, '', $CFG->timezone), 1);
        } else {
            $trace->output("error notifying user $ue->userid that enrolment in course $ue->courseid expires on ".userdate($ue->timeend, '', $CFG->timezone), 1);
        }

        force_current_language($oldforcelang);
    }

    /**
     * Notify person responsible for enrolments that some user enrolments will be expired soon,
     * it is called only if notification of enrollers (aka teachers) is enabled in course.
     *
     * This is called repeatedly every day for each course if there are any pending expiration
     * in the expiration threshold.
     *
     * @param int $eid
     * @param array $users
     * @param progress_trace $trace
     */
    protected function notify_expiry_enroller($eid, $users, progress_trace $trace) {
        global $DB;

        $name = $this->get_name();

        $instance = $DB->get_record('enrol', array('id'=>$eid, 'enrol'=>$name));
        $context = context_course::instance($instance->courseid);
        $course = $DB->get_record('course', array('id'=>$instance->courseid));

        $enroller = $this->get_enroller($instance->id);
        $admin = get_admin();

        $oldforcelang = force_current_language($enroller->lang);

        foreach($users as $key=>$info) {
            $users[$key] = '* '.$info['fullname'].' - '.userdate($info['timeend'], '', $enroller->timezone);
        }

        $a = new stdClass();
        $a->course    = format_string($course->fullname, true, array('context'=>$context));
        $a->threshold = get_string('numdays', '', $instance->expirythreshold / (60*60*24));
        $a->users     = implode("\n", $users);
        $a->extendurl = (string)new moodle_url('/user/index.php', array('id'=>$instance->courseid));

        $subject = get_string('expirymessageenrollersubject', 'enrol_'.$name, $a);
        $body = get_string('expirymessageenrollerbody', 'enrol_'.$name, $a);

        $message = new \core\message\message();
        $message->courseid          = $course->id;
        $message->notification      = 1;
        $message->component         = 'enrol_'.$name;
        $message->name              = 'expiry_notification';
        $message->userfrom          = $admin;
        $message->userto            = $enroller;
        $message->subject           = $subject;
        $message->fullmessage       = $body;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = markdown_to_html($body);
        $message->smallmessage      = $subject;
        $message->contexturlname    = $a->course;
        $message->contexturl        = $a->extendurl;

        if (message_send($message)) {
            $trace->output("notifying user $enroller->id about all expiring $name enrolments in course $instance->courseid", 1);
        } else {
            $trace->output("error notifying user $enroller->id about all expiring $name enrolments in course $instance->courseid", 1);
        }

        force_current_language($oldforcelang);
    }

    /**
     * Backup execution step hook to annotate custom fields.
     *
     * @param backup_enrolments_execution_step $step
     * @param stdClass $enrol
     */
    public function backup_annotate_custom_fields(backup_enrolments_execution_step $step, stdClass $enrol) {
        // Override as necessary to annotate custom fields in the enrol table.
    }

    /**
     * Automatic enrol sync executed during restore.
     * Useful for automatic sync by course->idnumber or course category.
     * @param stdClass $course course record
     */
    public function restore_sync_course($course) {
        // Override if necessary.
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        // Do not call this from overridden methods, restore and set new id there.
        $step->set_mapping('enrol', $oldid, 0);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        // Override as necessary if plugin supports restore of enrolments.
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        // No role assignment by default, override if necessary.
    }

    /**
     * Restore user group membership.
     * @param stdClass $instance
     * @param int $groupid
     * @param int $userid
     */
    public function restore_group_member($instance, $groupid, $userid) {
        // Implement if you want to restore protected group memberships,
        // usually this is not necessary because plugins should be able to recreate the memberships automatically.
    }

    /**
     * Returns defaults for new instances.
     * @since Moodle 3.1
     * @return array
     */
    public function get_instance_defaults() {
        return array();
    }

    /**
     * Validate a list of parameter names and types.
     * @since Moodle 3.1
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $rules array of ("fieldname"=>PARAM_X types - or "fieldname"=>array( list of valid options )
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     */
    public function validate_param_types($data, $rules) {
        $errors = array();
        $invalidstr = get_string('invaliddata', 'error');
        foreach ($rules as $fieldname => $rule) {
            if (is_array($rule)) {
                if (!in_array($data[$fieldname], $rule)) {
                    $errors[$fieldname] = $invalidstr;
                }
            } else {
                if ($data[$fieldname] != clean_param($data[$fieldname], $rule)) {
                    $errors[$fieldname] = $invalidstr;
                }
            }
        }
        return $errors;
    }

    /**
     * Fill custom fields data for a given enrolment plugin.
     *
     * @param array $enrolmentdata enrolment data.
     * @param int $courseid Course ID.
     * @return array Updated enrolment data with custom fields info.
     */
    public function fill_enrol_custom_fields(array $enrolmentdata, int $courseid) : array {
        return $enrolmentdata;
    }

    /**
     * Check if data is valid for a given enrolment plugin
     *
     * @param array $enrolmentdata enrolment data to validate.
     * @param int|null $courseid Course ID.
     * @return array Errors
     */
    public function validate_enrol_plugin_data(array $enrolmentdata, ?int $courseid = null) : array {
        $errors = [];
        if (!$this->is_csv_upload_supported()) {
            $errors['errorunsupportedmethod'] =
                new lang_string('errorunsupportedmethod', 'tool_uploadcourse',
                    get_class($this));

        }
        return $errors;
    }

    /**
     * Check if plugin custom data is allowed in relevant context.
     *
     * @param array $enrolmentdata enrolment data to validate.
     * @param int|null $courseid Course ID.
     * @return lang_string|null Error
     */
    public function validate_plugin_data_context(array $enrolmentdata, ?int $courseid = null) : ?lang_string {
        return null;
    }

}
