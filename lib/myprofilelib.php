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
 * Defines core nodes for my profile navigation tree.
 *
 * @package   core
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines core nodes for my profile navigation tree.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser is the user viewing profile, current user ?
 * @param stdClass $course course object
 *
 * @return bool
 */
function core_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $USER, $DB, $PAGE, $OUTPUT;

    $usercontext = context_user::instance($user->id, MUST_EXIST);
    $systemcontext = context_system::instance();
    $courseorusercontext = !empty($course) ? context_course::instance($course->id) : $usercontext;
    $courseorsystemcontext = !empty($course) ? context_course::instance($course->id) : $systemcontext;
    $courseid = !empty($course) ? $course->id : SITEID;

    $contactcategory = new core_user\output\myprofile\category('contact', get_string('userdetails'));
    // No after property specified intentionally. It is a hack to make administration block appear towards the end. Refer MDL-49928.
    $coursedetailscategory = new core_user\output\myprofile\category('coursedetails', get_string('coursedetails'));
    $miscategory = new core_user\output\myprofile\category('miscellaneous', get_string('miscellaneous'), 'coursedetails');
    $reportcategory = new core_user\output\myprofile\category('reports', get_string('reports'), 'miscellaneous');
    $admincategory = new core_user\output\myprofile\category('administration', get_string('administration'), 'reports');
    $loginactivitycategory = new core_user\output\myprofile\category('loginactivity', get_string('loginactivity'), 'administration');

    // Add categories.
    $tree->add_category($contactcategory);
    $tree->add_category($coursedetailscategory);
    $tree->add_category($miscategory);
    $tree->add_category($reportcategory);
    $tree->add_category($admincategory);
    $tree->add_category($loginactivitycategory);

    // Add core nodes.
    // Full profile node.
    if (!empty($course)) {
        if (user_can_view_profile($user, null, $usercontext)) {
            $url = new moodle_url('/user/profile.php', array('id' => $user->id));
            $node = new core_user\output\myprofile\node('miscellaneous', 'fullprofile', get_string('fullprofile'), null, $url);
            $tree->add_node($node);
        }
    }

    // Edit profile.
    if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
        if (($iscurrentuser || is_siteadmin($USER) || !is_siteadmin($user)) && has_capability('moodle/user:update',
                    $systemcontext)) {
            $url = new moodle_url('/user/editadvanced.php', array('id' => $user->id, 'course' => $courseid,
                'returnto' => 'profile'));
            $node = new core_user\output\myprofile\node('contact', 'editprofile', get_string('editmyprofile'), null, $url,
                null, null, 'editprofile');
            $tree->add_node($node);
        } else if ((has_capability('moodle/user:editprofile', $usercontext) && !is_siteadmin($user))
                   || ($iscurrentuser && has_capability('moodle/user:editownprofile', $systemcontext))) {
            $userauthplugin = false;
            if (!empty($user->auth)) {
                $userauthplugin = get_auth_plugin($user->auth);
            }
            if ($userauthplugin && $userauthplugin->can_edit_profile()) {
                $url = $userauthplugin->edit_profile_url();
                if (empty($url)) {
                    if (empty($course)) {
                        $url = new moodle_url('/user/edit.php', array('id' => $user->id, 'returnto' => 'profile'));
                    } else {
                        $url = new moodle_url('/user/edit.php', array('id' => $user->id, 'course' => $course->id,
                            'returnto' => 'profile'));
                    }
                }
                $node = new core_user\output\myprofile\node('contact', 'editprofile',
                        get_string('editmyprofile'), null, $url, null, null, 'editprofile');
                $tree->add_node($node);
            }
        }
    }

    // Preference page.
    if (!$iscurrentuser && $PAGE->settingsnav->can_view_user_preferences($user->id)) {
        $url = new moodle_url('/user/preferences.php', array('userid' => $user->id));
        $title = get_string('preferences', 'moodle');
        $node = new core_user\output\myprofile\node('administration', 'preferences', $title, null, $url);
        $tree->add_node($node);
    }

    // Login as ...
    if (!$user->deleted && !$iscurrentuser &&
                !\core\session\manager::is_loggedinas() && has_capability('moodle/user:loginas',
                $courseorsystemcontext) && !is_siteadmin($user->id)) {
        $url = new moodle_url('/course/loginas.php',
                array('id' => $courseid, 'user' => $user->id, 'sesskey' => sesskey()));
        $node = new  core_user\output\myprofile\node('administration', 'loginas', get_string('loginas'), null, $url);
        $tree->add_node($node);
    }

    // Contact details.
    if (has_capability('moodle/user:viewhiddendetails', $courseorusercontext)) {
        $hiddenfields = array();
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }
    // TODO Does not support custom user profile fields (MDL-70456).
    $identityfields = array_flip(\core_user\fields::get_identity_fields($courseorusercontext, false));

    if (is_mnet_remote_user($user)) {
        $sql = "SELECT h.id, h.name, h.wwwroot,
                       a.name as application, a.display_name
                  FROM {mnet_host} h, {mnet_application} a
                 WHERE h.id = ? AND h.applicationid = a.id";

        $remotehost = $DB->get_record_sql($sql, array($user->mnethostid));
        $remoteuser = new stdclass();
        $remoteuser->remotetype = $remotehost->display_name;
        $hostinfo = new stdclass();
        $hostinfo->remotename = $remotehost->name;
        $hostinfo->remoteurl  = $remotehost->wwwroot;

        $node = new core_user\output\myprofile\node('contact', 'mnet', get_string('remoteuser', 'mnet', $remoteuser), null, null,
            get_string('remoteuserinfo', 'mnet', $hostinfo), null, 'remoteuserinfo');
        $tree->add_node($node);
    }

    if ($iscurrentuser
        or (!isset($hiddenfields['email']) and (
            $user->maildisplay == core_user::MAILDISPLAY_EVERYONE
            or ($user->maildisplay == core_user::MAILDISPLAY_COURSE_MEMBERS_ONLY and enrol_sharing_course($user, $USER))
            or has_capability('moodle/course:useremail', $courseorusercontext) // TODO: Deprecate/remove for MDL-37479.
        ))
        or (isset($identityfields['email']))
       ) {
        $maildisplay = obfuscate_mailto($user->email, '');
        if ($iscurrentuser) {
            if ($user->maildisplay == core_user::MAILDISPLAY_EVERYONE) {
                $maildisplay .= ' ' . get_string('emaildisplayeveryone');
            } else if ($user->maildisplay == core_user::MAILDISPLAY_COURSE_MEMBERS_ONLY) {
                $maildisplay .= ' ' . get_string('emaildisplaycoursemembersonly');
            } else {
                $maildisplay .= ' ' . get_string('emaildisplayhide');
            }
        }
        $node = new core_user\output\myprofile\node('contact', 'email', get_string('email'),
            null, null, $maildisplay);
        $tree->add_node($node);
    }

    if (!isset($hiddenfields['moodlenetprofile']) && $user->moodlenetprofile) {
        $node = new core_user\output\myprofile\node('contact', 'moodlenetprofile', get_string('moodlenetprofile', 'user'), null,
                null, $user->moodlenetprofile);
        $tree->add_node($node);
    }

    if (!isset($hiddenfields['country']) && $user->country) {
        $node = new core_user\output\myprofile\node('contact', 'country', get_string('country'), null, null,
                get_string($user->country, 'countries'));
        $tree->add_node($node);
    }

    if (!isset($hiddenfields['city']) && $user->city) {
        $node = new core_user\output\myprofile\node('contact', 'city', get_string('city'), null, null, $user->city);
        $tree->add_node($node);
    }

    if (!isset($hiddenfields['timezone'])) {
        $node = new core_user\output\myprofile\node('contact', 'timezone', get_string('timezone'), null, null,
            core_date::get_user_timezone($user));
        $tree->add_node($node);
    }

    if (isset($identityfields['address']) && $user->address) {
        $node = new core_user\output\myprofile\node('contact', 'address', get_string('address'), null, null, $user->address);
        $tree->add_node($node);
    }

    if (isset($identityfields['phone1']) && $user->phone1) {
        $node = new core_user\output\myprofile\node('contact', 'phone1', get_string('phone1'), null, null, $user->phone1);
        $tree->add_node($node);
    }

    if (isset($identityfields['phone2']) && $user->phone2) {
        $node = new core_user\output\myprofile\node('contact', 'phone2', get_string('phone2'), null, null, $user->phone2);
        $tree->add_node($node);
    }

    if (isset($identityfields['institution']) && $user->institution) {
        $node = new core_user\output\myprofile\node('contact', 'institution', get_string('institution'), null, null,
                $user->institution);
        $tree->add_node($node);
    }

    if (isset($identityfields['department']) && $user->department) {
        $node = new core_user\output\myprofile\node('contact', 'department', get_string('department'), null, null,
            $user->department);
        $tree->add_node($node);
    }

    if (isset($identityfields['idnumber']) && $user->idnumber) {
        $node = new core_user\output\myprofile\node('contact', 'idnumber', get_string('idnumber'), null, null,
            s($user->idnumber));
        $tree->add_node($node);
    }

    // Printing tagged interests. We want this only for full profile.
    if (empty($course) && ($interests = core_tag_tag::get_item_tags('core', 'user', $user->id))) {
        $node = new core_user\output\myprofile\node('contact', 'interests', get_string('interests'), null, null,
                $OUTPUT->tag_list($interests, ''));
        $tree->add_node($node);
    }

    if ($iscurrentuser || !isset($hiddenfields['mycourses'])) {
        $showallcourses = optional_param('showallcourses', 0, PARAM_INT);
        if ($mycourses = enrol_get_all_users_courses($user->id, true, null)) {
            $shown = 0;
            $courselisting = html_writer::start_tag('ul');
            foreach ($mycourses as $mycourse) {
                if ($mycourse->category) {
                    context_helper::preload_from_record($mycourse);
                    $ccontext = context_course::instance($mycourse->id);
                    if (!isset($course) || $mycourse->id != $course->id) {
                        $linkattributes = null;
                        if ($mycourse->visible == 0) {
                            if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                                continue;
                            }
                            $linkattributes['class'] = 'dimmed';
                        }
                        $params = array('id' => $user->id, 'course' => $mycourse->id);
                        if ($showallcourses) {
                            $params['showallcourses'] = 1;
                        }
                        $url = new moodle_url('/user/view.php', $params);
                        $courselisting .= html_writer::tag('li', html_writer::link($url, $ccontext->get_context_name(false),
                                $linkattributes));
                    } else {
                        $courselisting .= html_writer::tag('li', $ccontext->get_context_name(false));
                    }
                }
                $shown++;
                if (!$showallcourses && $shown == $CFG->navcourselimit) {
                    $url = null;
                    if (isset($course)) {
                        $url = new moodle_url('/user/view.php',
                                array('id' => $user->id, 'course' => $course->id, 'showallcourses' => 1));
                    } else {
                        $url = new moodle_url('/user/profile.php', array('id' => $user->id, 'showallcourses' => 1));
                    }
                    $courselisting .= html_writer::tag('li', html_writer::link($url, get_string('viewmore'),
                            array('title' => get_string('viewmore'))), array('class' => 'viewmore'));
                    break;
                }
            }
            $courselisting .= html_writer::end_tag('ul');
            if (!empty($mycourses)) {
                // Add this node only if there are courses to display.
                $node = new core_user\output\myprofile\node('coursedetails', 'courseprofiles',
                    get_string('courseprofiles'), null, null, rtrim($courselisting, ', '));
                $tree->add_node($node);
            }
        }
    }

    if (!empty($course)) {

        // Show roles in this course.
        if ($rolestring = get_user_roles_in_course($user->id, $course->id)) {
            $node = new core_user\output\myprofile\node('coursedetails', 'roles', get_string('roles'), null, null, $rolestring);
            $tree->add_node($node);
        }

        // Show groups this user is in.
        if (!isset($hiddenfields['groups']) && !empty($course)) {
            $accessallgroups = has_capability('moodle/site:accessallgroups', $courseorsystemcontext);
            if ($usergroups = groups_get_all_groups($course->id, $user->id)) {
                $groupstr = '';
                foreach ($usergroups as $group) {
                    if ($course->groupmode == SEPARATEGROUPS and !$accessallgroups and $user->id != $USER->id) {
                        // In separate groups mode, I only have to see the groups shared between both users.
                        if (!groups_is_member($group->id, $USER->id)) {
                            continue;
                        }
                    }

                    if ($course->groupmode != NOGROUPS) {
                        $groupstr .= ' <a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'
                                     .format_string($group->name).'</a>,';
                    } else {
                        // The user/index.php shows groups only when course in group mode.
                        $groupstr .= ' '.format_string($group->name);
                    }
                }
                if ($groupstr !== '') {
                    $node = new core_user\output\myprofile\node('coursedetails', 'groups',
                            get_string('group'), null, null, rtrim($groupstr, ', '));
                    $tree->add_node($node);
                }
            }
        }

        if (!isset($hiddenfields['suspended'])) {
            if ($user->suspended) {
                $node = new core_user\output\myprofile\node('coursedetails', 'suspended',
                        null, null, null, get_string('suspended', 'auth'));
                $tree->add_node($node);
            }
        }
    }

    $categories = profile_get_user_fields_with_data_by_category($user->id);
    foreach ($categories as $categoryid => $fields) {
        foreach ($fields as $formfield) {
            if ($formfield->show_field_content()) {
                $node = new core_user\output\myprofile\node('contact', 'custom_field_' . $formfield->field->shortname,
                    format_string($formfield->field->name), null, null, $formfield->display_data());
                $tree->add_node($node);
            }
        }
    }

    // First access. (Why only for sites ?)
    if (!isset($hiddenfields['firstaccess']) && empty($course)) {
        if ($user->firstaccess) {
            $datestring = userdate($user->firstaccess)."&nbsp; (".format_time(time() - $user->firstaccess).")";
        } else {
            $datestring = get_string("never");
        }
        $node = new core_user\output\myprofile\node('loginactivity', 'firstaccess', get_string('firstsiteaccess'), null, null,
            $datestring);
        $tree->add_node($node);
    }

    // Last access.
    if (!isset($hiddenfields['lastaccess'])) {
        if (empty($course)) {
            $string = get_string('lastsiteaccess');
            if ($user->lastaccess) {
                $datestring = userdate($user->lastaccess) . "&nbsp; (" . format_time(time() - $user->lastaccess) . ")";
            } else {
                $datestring = get_string("never");
            }
        } else {
            $string = get_string('lastcourseaccess');
            if ($lastaccess = $DB->get_record('user_lastaccess', array('userid' => $user->id, 'courseid' => $course->id))) {
                $datestring = userdate($lastaccess->timeaccess)."&nbsp; (".format_time(time() - $lastaccess->timeaccess).")";
            } else {
                $datestring = get_string("never");
            }
        }

        $node = new core_user\output\myprofile\node('loginactivity', 'lastaccess', $string, null, null,
            $datestring);
        $tree->add_node($node);
    }

    // Last ip.
    if (has_capability('moodle/user:viewlastip', $usercontext) && !isset($hiddenfields['lastip'])) {
        if ($user->lastip) {
            $iplookupurl = new moodle_url('/iplookup/index.php', array('ip' => $user->lastip, 'user' => $user->id));
            $ipstring = html_writer::link($iplookupurl, $user->lastip);
        } else {
            $ipstring = get_string("none");
        }
        $node = new core_user\output\myprofile\node('loginactivity', 'lastip', get_string('lastip'), null, null,
            $ipstring);
        $tree->add_node($node);
    }
}
