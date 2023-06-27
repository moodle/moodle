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
 * Display user activity reports for a course
 *
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/rating/lib.php');
require_once($CFG->dirroot.'/user/lib.php');

$courseid  = optional_param('course', null, PARAM_INT); // Limit the posts to just this course
$userid = optional_param('id', $USER->id, PARAM_INT);        // User id whose posts we want to view
$mode = optional_param('mode', 'posts', PARAM_ALPHA);   // The mode to use. Either posts or discussions
$page = optional_param('page', 0, PARAM_INT);           // The page number to display
$perpage = optional_param('perpage', 5, PARAM_INT);     // The number of posts to display per page

if (empty($userid)) {
    if (!isloggedin()) {
        require_login();
    }
    $userid = $USER->id;
}

$discussionsonly = ($mode !== 'posts');
$isspecificcourse = !is_null($courseid);
$iscurrentuser = ($USER->id == $userid);

$url = new moodle_url('/mod/forum/user.php', array('id' => $userid));
if ($isspecificcourse) {
    $url->param('course', $courseid);
}
if ($discussionsonly) {
    $url->param('mode', 'discussions');
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

if ($page != 0) {
    $url->param('page', $page);
}
if ($perpage != 5) {
    $url->param('perpage', $perpage);
}

$user = $DB->get_record("user", array("id" => $userid), '*', MUST_EXIST);
$usercontext = context_user::instance($user->id, MUST_EXIST);
// Check if the requested user is the guest user
if (isguestuser($user)) {
    // The guest user cannot post, so it is not possible to view any posts.
    // May as well just bail aggressively here.
    print_error('invaliduserid');
}
// Make sure the user has not been deleted
if ($user->deleted) {
    $PAGE->set_title(get_string('userdeleted'));
    $PAGE->set_context(context_system::instance());
    echo $OUTPUT->header();
    echo $OUTPUT->heading($PAGE->title);
    echo $OUTPUT->footer();
    die;
}

$isloggedin = isloggedin();
$isguestuser = $isloggedin && isguestuser();
$isparent = !$iscurrentuser && $DB->record_exists('role_assignments', array('userid'=>$USER->id, 'contextid'=>$usercontext->id));
$hasparentaccess = $isparent && has_all_capabilities(array('moodle/user:viewdetails', 'moodle/user:readuserposts'), $usercontext);

// Check whether a specific course has been requested
if ($isspecificcourse) {
    // Get the requested course and its context
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $coursecontext = context_course::instance($courseid, MUST_EXIST);
    // We have a specific course to search, which we will also assume we are within.
    if ($hasparentaccess) {
        // A `parent` role won't likely have access to the course so we won't attempt
        // to enter it. We will however still make them jump through the normal
        // login hoops
        require_login();
        $PAGE->set_context($coursecontext);
        $PAGE->set_course($course);
    } else {
        // Enter the course we are searching
        require_login($course);
    }
    // Get the course ready for access checks
    $courses = array($courseid => $course);
} else {
    // We are going to search for all of the users posts in all courses!
    // a general require login here as we arn't actually within any course.
    require_login();
    $PAGE->set_context(context_user::instance($user->id));

    // Now we need to get all of the courses to search.
    // All courses where the user has posted within a forum will be returned.
    $courses = forum_get_courses_user_posted_in($user, $discussionsonly);
}

$params = array(
    'context' => $PAGE->context,
    'relateduserid' => $user->id,
    'other' => array('reportmode' => $mode),
);
$event = \mod_forum\event\user_report_viewed::create($params);
$event->trigger();

// Get the posts by the requested user that the current user can access.
$result = forum_get_posts_by_user($user, $courses, $isspecificcourse, $discussionsonly, ($page * $perpage), $perpage);

// Check whether there are not posts to display.
if (empty($result->posts)) {
    // Ok no posts to display means that either the user has not posted or there
    // are no posts made by the requested user that the current user is able to
    // see.
    // In either case we need to decide whether we can show personal information
    // about the requested user to the current user so we will execute some checks

    $canviewuser = user_can_view_profile($user, null, $usercontext);

    // Prepare the page title
    $pagetitle = get_string('noposts', 'mod_forum');

    // Get the page heading
    if ($isspecificcourse) {
        $pageheading = format_string($course->fullname, true, array('context' => $coursecontext));
    } else {
        $pageheading = get_string('pluginname', 'mod_forum');
    }

    // Next we need to set up the loading of the navigation and choose a message
    // to display to the current user.
    if ($iscurrentuser) {
        // No need to extend the navigation it happens automatically for the
        // current user.
        if ($discussionsonly) {
            $notification = get_string('nodiscussionsstartedbyyou', 'forum');
        } else {
            $notification = get_string('nopostsmadebyyou', 'forum');
        }
        // These are the user's forum interactions.
        // Shut down the navigation 'Users' node.
        $usernode = $PAGE->navigation->find('users', null);
        $usernode->make_inactive();
        // Edit navbar.
        if (isset($courseid) && $courseid != SITEID) {
            // Create as much of the navbar automatically.
            if ($newusernode = $PAGE->navigation->find('user' . $user->id, null)) {
                $newusernode->make_active();
            }
            // Check to see if this is a discussion or a post.
            if ($mode == 'posts') {
                $navbar = $PAGE->navbar->add(get_string('posts', 'forum'), new moodle_url('/mod/forum/user.php',
                        array('id' => $user->id, 'course' => $courseid)));
            } else {
                $navbar = $PAGE->navbar->add(get_string('discussions', 'forum'), new moodle_url('/mod/forum/user.php',
                        array('id' => $user->id, 'course' => $courseid, 'mode' => 'discussions')));
            }
        }
    } else if ($canviewuser) {
        $PAGE->navigation->extend_for_user($user);
        $PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.

        // Edit navbar.
        if (isset($courseid) && $courseid != SITEID) {
            // Create as much of the navbar automatically.
            if ($usernode = $PAGE->navigation->find('user' . $user->id, null)) {
                $usernode->make_active();
            }
            // Check to see if this is a discussion or a post.
            if ($mode == 'posts') {
                $navbar = $PAGE->navbar->add(get_string('posts', 'forum'), new moodle_url('/mod/forum/user.php',
                        array('id' => $user->id, 'course' => $courseid)));
            } else {
                $navbar = $PAGE->navbar->add(get_string('discussions', 'forum'), new moodle_url('/mod/forum/user.php',
                        array('id' => $user->id, 'course' => $courseid, 'mode' => 'discussions')));
            }
        }

        $fullname = fullname($user);
        if ($discussionsonly) {
            $notification = get_string('nodiscussionsstartedby', 'forum', $fullname);
        } else {
            $notification = get_string('nopostsmadebyuser', 'forum', $fullname);
        }
    } else {
        // Don't extend the navigation it would be giving out information that
        // the current uesr doesn't have access to.
        $notification = get_string('cannotviewusersposts', 'forum');
        if ($isspecificcourse) {
            $url = new moodle_url('/course/view.php', array('id' => $courseid));
        } else {
            $url = new moodle_url('/');
        }
        navigation_node::override_active_url($url);
    }

    // Display a page letting the user know that there's nothing to display;
    $PAGE->set_title($pagetitle);
    if ($isspecificcourse) {
        $PAGE->set_heading($pageheading);
    } else if ($canviewuser) {
        $PAGE->set_heading(fullname($user));
    } else {
        $PAGE->set_heading($SITE->fullname);
    }
    echo $OUTPUT->header();
    if (!$isspecificcourse) {
        echo $OUTPUT->heading($pagetitle);
    } else {
        $userheading = array(
                'heading' => fullname($user),
                'user' => $user,
                'usercontext' => $usercontext
            );
        echo $OUTPUT->context_header($userheading, 2);
    }
    echo $OUTPUT->notification($notification);
    if (!$url->compare($PAGE->url)) {
        echo $OUTPUT->continue_button($url);
    }
    echo $OUTPUT->footer();
    die;
}

$discussions = array();
foreach ($result->posts as $post) {
    $discussions[] = $post->discussion;
}
$discussions = $DB->get_records_list('forum_discussions', 'id', array_unique($discussions));

$entityfactory = mod_forum\local\container::get_entity_factory();
$rendererfactory = mod_forum\local\container::get_renderer_factory();
$postsrenderer = $rendererfactory->get_user_forum_posts_report_renderer(!$isspecificcourse && !$hasparentaccess);
$postoutput = $postsrenderer->render(
    $USER,
    array_map(function($forum) use ($entityfactory, $result) {
        $cm = $forum->cm;
        $context = context_module::instance($cm->id);
        $course = $result->courses[$forum->course];
        return $entityfactory->get_forum_from_stdclass($forum, $context, $cm, $course);
    }, $result->forums),
    array_map(function($discussion) use ($entityfactory) {
        return $entityfactory->get_discussion_from_stdclass($discussion);
    }, $discussions),
    array_map(function($post) use ($entityfactory) {
        return $entityfactory->get_post_from_stdclass($post);
    }, $result->posts)
);

$userfullname = fullname($user);

if ($discussionsonly) {
    $inpageheading = get_string('discussionsstartedby', 'mod_forum', $userfullname);
} else {
    $inpageheading = get_string('postsmadebyuser', 'mod_forum', $userfullname);
}
if ($isspecificcourse) {
    $a = new stdClass;
    $a->fullname = $userfullname;
    $a->coursename = format_string($course->fullname, true, array('context' => $coursecontext));
    $pageheading = $a->coursename;
    if ($discussionsonly) {
        $pagetitle = get_string('discussionsstartedbyuserincourse', 'mod_forum', $a);
    } else {
        $pagetitle = get_string('postsmadebyuserincourse', 'mod_forum', $a);
    }
} else {
    $pagetitle = $inpageheading;
    $pageheading = $userfullname;
}

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pageheading);

$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.

// Edit navbar.
if (isset($courseid) && $courseid != SITEID) {
    if ($usernode = $PAGE->navigation->find('user' . $user->id , null)) {
        $usernode->make_active();
    }

    // Check to see if this is a discussion or a post.
    if ($mode == 'posts') {
        $navbar = $PAGE->navbar->add(get_string('posts', 'forum'), new moodle_url('/mod/forum/user.php',
                array('id' => $user->id, 'course' => $courseid)));
    } else {
        $navbar = $PAGE->navbar->add(get_string('discussions', 'forum'), new moodle_url('/mod/forum/user.php',
                array('id' => $user->id, 'course' => $courseid, 'mode' => 'discussions')));
    }
}

echo $OUTPUT->header();
echo html_writer::start_tag('div', array('class' => 'user-content'));

if ($isspecificcourse) {
    $userheading = array(
        'heading' => fullname($user),
        'user' => $user,
        'usercontext' => $usercontext
    );
    echo $OUTPUT->context_header($userheading, 2);
} else {
    echo $OUTPUT->heading($inpageheading);
}

if (!empty($postoutput)) {
    echo $OUTPUT->paging_bar($result->totalcount, $page, $perpage, $url);
    echo $postoutput;
    echo $OUTPUT->paging_bar($result->totalcount, $page, $perpage, $url);
} else if ($discussionsonly) {
    echo $OUTPUT->heading(get_string('nodiscussionsstartedby', 'forum', $userfullname));
} else {
    echo $OUTPUT->heading(get_string('noposts', 'forum'));
}

echo html_writer::end_tag('div');
echo $OUTPUT->footer();
