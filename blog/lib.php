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
 * Core global functions for Blog.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Library of functions and constants for blog
 */
require_once($CFG->dirroot .'/blog/rsslib.php');
require_once($CFG->dirroot.'/tag/lib.php');

/**
 * Definition of blogcourse page type (blog page with course id present).
 */
//not used at the moment, and may not need to be
define('PAGE_BLOG_COURSE_VIEW', 'blog_course-view');
define('BLOG_PUBLISHSTATE_DRAFT', 0);
define('BLOG_PUBLISHSTATE_SITE', 1);
define('BLOG_PUBLISHSTATE_PUBLIC', 2);

/**
 * Checks to see if user has visited blogpages before, if not, install 2
 * default blocks (blog_menu and blog_tags).
 */
function blog_check_and_install_blocks() {
    global $USER, $DB;

    if (isloggedin() && !isguest()) {
        // if this user has not visited this page before
        if (!get_user_preferences('blogpagesize')) {
            // find the correct ids for blog_menu and blog_from blocks
            $menublock = $DB->get_record('block', array('name'=>'blog_menu'));
            $tagsblock = $DB->get_record('block', array('name'=>'blog_tags'));
            // add those 2 into block_instance page

// Commmented out since the block changes broke it. Hopefully nico will fix it ;-)
//                // add blog_menu block
//                $newblock = new object();
//                $newblock->blockid  = $menublock->id;
//                $newblock->pageid   = $USER->id;
//                $newblock->pagetype = 'blog-view';
//                $newblock->position = 'r';
//                $newblock->weight   = 0;
//                $newblock->visible  = 1;
//                $DB->insert_record('block_instances', $newblock);
//
//                // add blog_tags menu
//                $newblock -> blockid = $tagsblock->id;
//                $newblock -> weight  = 1;
//                $DB->insert_record('block_instances', $newblock);

            // finally we set the page size pref
            set_user_preference('blogpagesize', 10);
        }
    }
}


/**
 * User can edit a blog entry if this is their own blog entry and they have
 * the capability moodle/blog:create, or if they have the capability
 * moodle/blog:manageentries.
 *
 * This also applies to deleting of entries.
 */
function blog_user_can_edit_entry($blog_entry) {
    global $CFG, $USER, $OUTPUT;

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    if (has_capability('moodle/blog:manageentries', $sitecontext)) {
        return true; // can edit any blog entry
    }

    if ($blog_entry->userid == $USER->id
      and has_capability('moodle/blog:create', $sitecontext)) {
        return true; // can edit own when having blog:create capability
    }

    return false;
}


/**
 * Checks to see if a user can view the blogs of another user.
 * Only blog level is checked here, the capabilities are enforced
 * in blog/index.php
 */
function blog_user_can_view_user_entry($targetuserid, $blog_entry=null) {
    global $CFG, $USER, $DB;

    if (empty($CFG->bloglevel)) {
        return false; // blog system disabled
    }

    if (!empty($USER->id) and $USER->id == $targetuserid) {
        return true; // can view own entries in any case
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    if (has_capability('moodle/blog:manageentries', $sitecontext)) {
        return true; // can manage all entries
    }

    // coming for 1 entry, make sure it's not a draft
    if ($blog_entry and $blog_entry->publishstate == 'draft') {
        return false;  // can not view draft of others
    }

    // coming for 1 entry, make sure user is logged in, if not a public blog
    if ($blog_entry && $blog_entry->publishstate != 'public' && !isloggedin()) {
        return false;
    }

    switch ($CFG->bloglevel) {
        case BLOG_GLOBAL_LEVEL:
            return true;
        break;

        case BLOG_SITE_LEVEL:
            if (!empty($USER->id)) { // not logged in viewers forbidden
                return true;
            }
            return false;
        break;

        case BLOG_USER_LEVEL:
        default:
            $personalcontext = get_context_instance(CONTEXT_USER, $targetuserid);
            return has_capability('moodle/user:readuserblogs', $personalcontext);
        break;

    }
}

/**
 * remove all associations for the blog entries of a particular user
 * @param int userid - id of user whose blog associations will be deleted
 */
function blog_remove_associations_for_user($userid) {
     global $DB;
     foreach(blog_fetch_entries(array('user' => $userid), 'lasmodified DESC') as $entry) {
         blog_remove_associations_for_entry($entry->id);
     }
 }

/**
 * generates the url of the page displaying entries matching the search criteria
 *  @param array filters an array of filters (filtername => filtervalue) to narrow down results by
 *  available filters:
 *    entry: id field of a specific entry
 *    course: id of a course that the entries must be associated with
 *    mod: id of a course module that the entries must be associated with
 *    user: id of a user who must be the author of an entry
 *    group: id of a group who the author must be a member of, and whose course must be associated with the entry
 *    tag: id of a tag that must be applied to the entry
 *    site: the entire site is searched
 *  @return string the url of the page displaying entries matching the search criteria
 */
function blog_get_blogs_url($filters) {
    global $CFG;
    $blogsurl = new moodle_url($CFG->wwwroot . '/blog/index.php');
    if (!empty($filters['course'])) {
        $blogsurl->param('courseid', $filters['course']);
    }
    if (!empty($filters['mod'])) {
        $blogsurl->param('modid', $filters['mod']);
    }
    if (!empty($filters['group'])) {
        $blogsurl->param('groupid', $filters['group']);
    }
    if (!empty($filters['user'])) {
        $blogsurl->param('userid', $filters['user']);
    }
    if (!empty($filters['entry'])) {
        $blogsurl->param('entryid', $filters['entry']);
    }
    if (!empty($filters['tag'])) {
        $blogsurl->param('tagid', $filters['tag']);
    }
    if (!empty($filters['tagtext'])) {
        $blogsurl->param('tag', $filters['tagtext']);
    }
    return $blogsurl;
}

/**
 * A simple function for checking if a given URL is valid and resolves
 * to a proper XML data stream.
 *
 * @param string $url
 * @return bool
 */
function blog_is_valid_url($url) {
    $url = @parse_url($url);

    if (!$url) {
        return false;
    }

    $url = array_map('trim', $url);

    if (empty($url['port'])) {
        $url['port'] = 80;
    } else {
        $url['port'] = (int)$url['port'];
    }

    $path = '';
    if (!empty($url['path'])) {
        $path = $url['path'];
    }


    if ($path == '') {
        $path = '/';
    }

    if (!empty($url['query'])) {
        $path .= "?{$url['query']}";
    }

    if (isset($url['host']) && $url['host'] != gethostbyname($url['host'])) {
        if (PHP_VERSION >= 5) {
            $headers = get_headers("{$url['scheme']}://{$url['host']}:{$url['port']}$path");
        } else {
            $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

            if (!$fp) {
                return false;
            }

            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: {$url['host']}\r\n\r\n");
            $headers = fread($fp, 128);
            fclose($fp);
        }

        if (is_array($headers)) {
            $headers = implode("\n", $headers);
        }

        return (bool) preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }
    return false;
}


/**
 * Given a record in the {blog_external} table, checks the blog's URL
 * for new entries not yet copied into Moodle.
 *
 * @param object $external_blog
 * @return void
 */
function blog_fetch_external_entries($external_blog) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/simplepie/moodle_simplepie.php');

    if (!blog_is_valid_url($external_blog->url)) {
        return null;
    }

    $rss = new moodle_simplepie($external_blog->url);

    if (empty($rss->data)) {
        return null;
    }

    foreach ($rss->get_items() as $entry) {
        $params = array('userid' => $external_blog->userid,
                        'module' => 'blog',
                        'uniquehash' => $entry->get_permalink(),
                        'publishstate' => 'site',
                        'format' => FORMAT_HTML);

        if (!$DB->record_exists('blog_entries', $params)) {
            $params['subject']      = $entry->get_title();
            $params['summary']      = $entry->get_description();
            $params['created']      = $entry->get_date('U');
            $params['lastmodified'] = $entry->get_date('U');

            $id = $DB->insert_record('blog_entries', $params);

            // Set tags
            if ($tags = tag_get_tags_array('blog_external', $external_blog->id)) {
                tag_set('blog_entries', $id, $tags);
            }
        }
    }

    $DB->update_record('blog_external', array('id' => $external_blog->id, 'timefetched' => mktime()));
}

/**
 * Returns a URL based on the context of the current page.
 * This URL points to blog/index.php and includes filter parameters appropriate for the current page.
 *
 * @param stdclass $context
 * @return string
 */
function blog_get_context_url($context=null) {
    global $CFG;

    $viewblogentriesurl = new moodle_url($CFG->wwwroot . '/blog/index.php');

    if (empty($context)) {
        global $PAGE;
        $context = $PAGE->get_context();
    }

    // Change contextlevel to SYSTEM if viewing the site course
    if ($context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID) {
        $context->contextlevel = CONTEXT_SYSTEM;
    }

    $filterparam = '';
    $strlevel = '';

    switch ($context->contextlevel) {
        case CONTEXT_SYSTEM:
        case CONTEXT_BLOCK:
        case CONTEXT_COURSECAT:
            break;
        case CONTEXT_COURSE:
            $filterparam = 'courseid';
            $strlevel = get_string('course');
            break;
        case CONTEXT_MODULE:
            $filterparam = 'modid';
            $strlevel = print_context_name($context);
            break;
        case CONTEXT_USER:
            $filterparam = 'userid';
            $strlevel = get_string('user');
            break;
    }

    if (!empty($filterparam)) {
        $viewblogentriesurl->param($filterparam, $context->instanceid);
    }

    return $viewblogentriesurl;
}

/**
 * This function encapsulates all the logic behind the complex
 * navigation, titles and headings of the blog listing page, depending
 * on URL params. It builds and returns an array containing:
 *
 * 1. The heading displayed above the blog entries
 * All other variables are set directly in $PAGE
 *
 * It uses the current URL to build these variables.
 * A number of mutually exclusive use cases are used to structure this function.
 *
 * @return array
 */
function blog_get_headers() {
    global $CFG, $PAGE, $DB, $USER;

    $id       = optional_param('id', null, PARAM_INT);
    $tag      = optional_param('tag', null, PARAM_NOTAGS);
    $tagid    = optional_param('tagid', null, PARAM_INT);
    $userid   = optional_param('userid', null, PARAM_INT);
    $modid    = optional_param('modid', null, PARAM_INT);
    $entryid  = optional_param('entryid', null, PARAM_INT);
    $groupid  = optional_param('groupid', null, PARAM_INT);
    $courseid = optional_param('courseid', null, PARAM_INT);
    $search   = optional_param('search', null, PARAM_RAW);
    $action   = optional_param('action', null, PARAM_ALPHA);
    $confirm  = optional_param('confirm', false, PARAM_BOOL);

    $headers = array('title' => '', 'heading' => '', 'cm' => null);

    $blog_url = new moodle_url($CFG->wwwroot . '/blog/index.php');
    $site = $DB->get_record('course', array('id' => SITEID));

    // Common Lang strings
    $strparticipants = get_string("participants");
    $strblogentries  = get_string("blogentries", 'blog');

    // Prepare record objects as needed
    if (!empty($courseid)) {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    if (!empty($userid)) {
        $user = $DB->get_record('user', array('id' => $userid));
    }

    if (!empty($groupid)) { // groupid always overrides courseid
        $group = $DB->get_record('groups', array('id' => $groupid));
        $course = $DB->get_record('course', array('id' => $group->courseid));
    }

    if (!empty($modid)) { // modid always overrides courseid, so the $course object may be reset here
        // A groupid param may conflict with this coursemod's courseid. Ignore groupid in that case
        $course_id = $DB->get_field('course_modules', 'course', array('id'=>$modid));
        $course = $DB->get_record('course', array('id' => $course_id));
        $cm = $DB->get_record('course_modules', array('id' => $modid));
        $cm->modname = $DB->get_field('modules', 'name', array('id' => $cm->module));
        $cm->name = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance));
        $cm->context = get_context_instance(CONTEXT_MODULE, $modid);
        $PAGE->set_cm($cm, $course);
    }

    // Case 0: No entry, mod, course or user params: all site entries to be shown (filtered by search and tag/tagid)
    if (empty($entryid) && empty($modid) && empty($courseid) && empty($userid)) {
        $PAGE->navbar->add($strblogentries, $blog_url);
        $PAGE->set_title("$site->shortname: " . get_string('blog', 'blog'));
        $PAGE->set_heading("$site->shortname: " . get_string('blog', 'blog'));
        $headers['heading'] = get_string('siteblog', 'blog');
    }

    // Case 1: only entryid is requested, ignore all other filters. courseid is used to give more contextual information
    // TODO Blog entries link has entryid instead of userid
    if (!empty($entryid)) {
        $sql = 'SELECT u.* FROM {user} u, {blog_entries} p WHERE p.id = ? AND p.userid = u.id';
        $user = $DB->get_record_sql($sql, array($entryid));
        $entry = $DB->get_record('blog_entries', array('id' => $entryid));

        $blog_url->param('userid', $user->id);

        if (!empty($course)) {
            $mycourseid = $course->id;
            $blog_url->param('courseid', $mycourseid);
        } else {
            $mycourseid = $site->id;
        }

        $PAGE->navbar->add($strparticipants, "$CFG->wwwroot/user/index.php?id=$mycourseid");
        $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        $PAGE->navbar->add($strblogentries, $blog_url);
        $blog_url->param('entryid', $entryid);
        $blog_url->remove_params('userid');
        $PAGE->navbar->add($entry->subject, $blog_url);

        $PAGE->set_title("$site->shortname: " . fullname($user) . ": $entry->subject");
        $PAGE->set_heading("$site->shortname: " . fullname($user) . ": $entry->subject");
        $headers['heading'] = get_string('blogentrybyuser', 'blog', fullname($user));

        // We ignore tag and search params
        if (empty($action)) {
            return $headers;
        }
    }

    // Case 2: A user's blog entries
    if (!empty($userid) && empty($modid) && empty($courseid) && empty($entryid)) {
        $blog_url->param('userid', $userid);
        $PAGE->navbar->add($strparticipants, "$CFG->wwwroot/user/index.php?id=$site->id");
        $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        $PAGE->navbar->add($strblogentries, $blog_url);
        $PAGE->set_title("$site->shortname: " . fullname($user) . ": " . get_string('blog', 'blog'));
        $PAGE->set_heading("$site->shortname: " . fullname($user) . ": " . get_string('blog', 'blog'));
        $headers['heading'] = get_string('userblog', 'blog', fullname($user));

    } else

    // Case 3: Blog entries associated with an activity by a specific user (courseid ignored)
    if (!empty($userid) && !empty($modid) && empty($entryid)) {
        $blog_url->param('userid', $userid);
        $blog_url->param('modid', $modid);

        // Course module navigation is handled by build_navigation as the second param
        $headers['cm'] = $cm;
        $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        $PAGE->navbar->add($strblogentries, $blog_url);

        $PAGE->set_title("$site->shortname: $cm->name: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $cm->name: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));

        $a->user = fullname($user);
        $a->mod = $cm->name;
        $headers['heading'] = get_string('blogentriesbyuseraboutmodule', 'blog', $a);
    } else

    // Case 4: Blog entries associated with a course by a specific user
    if (!empty($userid) && !empty($courseid) && empty($modid) && empty($entryid)) {
        $blog_url->param('userid', $userid);
        $blog_url->param('courseid', $courseid);

        $PAGE->navbar->add($strparticipants, "$CFG->wwwroot/user/index.php?id=$course->id");
        $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        $PAGE->navbar->add($strblogentries, $blog_url);

        $PAGE->set_title("$site->shortname: $course->shortname: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));

        $a->user = fullname($user);
        $a->course = $course->fullname;
        $headers['heading'] = get_string('blogentriesbyuseraboutcourse', 'blog', $a);
    } else

    // Case 5: Blog entries by members of a group, associated with that group's course
    if (!empty($groupid) && empty($modid) && empty($entryid)) {
        $blog_url->param('courseid', $course->id);

        $PAGE->navbar->add($strblogentries, $blog_url);
        $blog_url->remove_params(array('courseid'));
        $blog_url->param('groupid', $groupid);
        $PAGE->navbar->add($group->name, $blog_url);

        $PAGE->set_title("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog') . ": $group->name");
        $PAGE->set_heading("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog') . ": $group->name");

        $a->group = $group->name;
        $a->course = $course->fullname;
        $headers['heading'] = get_string('blogentriesbygroupaboutcourse', 'blog', $a);
    } else

    // Case 6: Blog entries by members of a group, associated with an activity in that course
    if (!empty($groupid) && !empty($modid) && empty($entryid)) {
        $headers['cm'] = $cm;
        $blog_url->param('modid', $modid);
        $PAGE->navbar->add($strblogentries, $blog_url);

        $blog_url->param('groupid', $groupid);
        $PAGE->navbar->add($group->name, $blog_url);

        $PAGE->set_title("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog') . ": $group->name");
        $PAGE->set_heading("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog') . ": $group->name");

        $a->group = $group->name;
        $a->mod = $cm->name;
        $headers['heading'] = get_string('blogentriesbygroupaboutmodule', 'blog', $a);

    } else

    // Case 7: All blog entries associated with an activity
    if (!empty($modid) && empty($userid) && empty($groupid) && empty($entryid)) {
        $PAGE->set_cm($cm, $course);
        $blog_url->param('modid', $modid);
        $PAGE->navbar->add($strblogentries, $blog_url);
        $PAGE->set_title("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog'));
        $headers['heading'] = get_string('blogentriesabout', 'blog', $cm->name);
    } else

    // Case 8: All blog entries associated with a course
    if (!empty($courseid) && empty($userid) && empty($groupid) && empty($modid) && empty($entryid)) {
        $blog_url->param('courseid', $courseid);
        $PAGE->navbar->add($strblogentries, $blog_url);
        $PAGE->set_title("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog'));
        $headers['heading'] = get_string('blogentriesabout', 'blog', $course->fullname);
    }

    // Append Tag info
    if (!empty($tagid)) {
        $blog_url->param('tagid', $tagid);
        $tagrec = $DB->get_record('tag', array('id'=>$tagid));
        $PAGE->navbar->add($tagrec->name, $blog_url);
    } elseif (!empty($tag)) {
        $blog_url->param('tag', $tag);
        $PAGE->navbar->add(get_string('tagparam', 'blog', $tag), $blog_url);
    }

    // Append Search info
    if (!empty($search)) {
        $blog_url->param('search', $search);
        $PAGE->navbar->add(get_string('searchterm', 'blog', $search), $blog_url->out());
    }

    // Append edit mode info
    if (!empty($action) && $action == 'add') {
        if (empty($modid) && empty($courseid)) {
            if (empty($user)) {
                $user = $USER;
            }
            $PAGE->navbar->add($strparticipants, "$CFG->wwwroot/user/index.php?id=$site->id");
            $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        }
        $PAGE->navbar->add(get_string('addnewentry', 'blog'));
    } else if (!empty($action) && $action == 'edit') {
        $PAGE->navbar->add(get_string('editentry', 'blog'));
    }

    return $headers;
}

function blog_extend_settings_navigation($settingsnav) {
    global $USER, $PAGE, $FULLME, $CFG, $DB, $OUTPUT;
    $blogkey = $settingsnav->add(get_string('blogadministration', 'blog'));
    $blog = $settingsnav->get($blogkey);
    $blog->forceopen = true;

    $blog->add(get_string('preferences', 'blog'), new moodle_url('preferences.php'), navigation_node::TYPE_SETTING);
    if ($CFG->useexternalblogs && $CFG->maxexternalblogsperuser > 0) {
        $blog->add(get_string('externalblogs', 'blog'), new moodle_url('external.php'), navigation_node::TYPE_SETTING);
    }

    return $blogkey;
}
