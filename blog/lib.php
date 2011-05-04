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

defined('MOODLE_INTERNAL') || die();

/**
 * Library of functions and constants for blog
 */
require_once($CFG->dirroot .'/blog/rsslib.php');
require_once($CFG->dirroot.'/tag/lib.php');

/**
 * User can edit a blog entry if this is their own blog entry and they have
 * the capability moodle/blog:create, or if they have the capability
 * moodle/blog:manageentries.
 *
 * This also applies to deleting of entries.
 */
function blog_user_can_edit_entry($blogentry) {
    global $USER;

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    if (has_capability('moodle/blog:manageentries', $sitecontext)) {
        return true; // can edit any blog entry
    }

    if ($blogentry->userid == $USER->id && has_capability('moodle/blog:create', $sitecontext)) {
        return true; // can edit own when having blog:create capability
    }

    return false;
}


/**
 * Checks to see if a user can view the blogs of another user.
 * Only blog level is checked here, the capabilities are enforced
 * in blog/index.php
 */
function blog_user_can_view_user_entry($targetuserid, $blogentry=null) {
    global $CFG, $USER, $DB;

    if (empty($CFG->bloglevel)) {
        return false; // blog system disabled
    }

    if (isloggedin() && $USER->id == $targetuserid) {
        return true; // can view own entries in any case
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    if (has_capability('moodle/blog:manageentries', $sitecontext)) {
        return true; // can manage all entries
    }

    // coming for 1 entry, make sure it's not a draft
    if ($blogentry && $blogentry->publishstate == 'draft' && !has_capability('moodle/blog:viewdrafts', $sitecontext)) {
        return false;  // can not view draft of others
    }

    // coming for 0 entry, make sure user is logged in, if not a public blog
    if ($blogentry && $blogentry->publishstate != 'public' && !isloggedin()) {
        return false;
    }

    switch ($CFG->bloglevel) {
        case BLOG_GLOBAL_LEVEL:
            return true;
        break;

        case BLOG_SITE_LEVEL:
            if (isloggedin()) { // not logged in viewers forbidden
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
    throw new coding_exception('function blog_remove_associations_for_user() is not finished');
    /*
    $blogentries = blog_fetch_entries(array('user' => $userid), 'lasmodified DESC');
    foreach ($blogentries as $entry) {
        if (blog_user_can_edit_entry($entry)) {
            blog_remove_associations_for_entry($entry->id);
        }
    }
     */
}

/**
 * remove all associations for the blog entries of a particular course
 * @param int courseid - id of user whose blog associations will be deleted
 */
function blog_remove_associations_for_course($courseid) {
    global $DB;
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $DB->delete_records('blog_association', array('contextid' => $context->id));
}

/**
 * Given a record in the {blog_external} table, checks the blog's URL
 * for new entries not yet copied into Moodle.
 * Also attempts to identify and remove deleted blog entries
 *
 * @param object $externalblog
 * @return boolean False if the Feed is invalid
 */
function blog_sync_external_entries($externalblog) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/simplepie/moodle_simplepie.php');

    $rssfile = new moodle_simplepie_file($externalblog->url);
    $filetest = new SimplePie_Locator($rssfile);

    $textlib = textlib_get_instance(); // Going to use textlib services

    if (!$filetest->is_feed($rssfile)) {
        $externalblog->failedlastsync = 1;
        $DB->update_record('blog_external', $externalblog);
        return false;
    } else if (!empty($externalblog->failedlastsync)) {
        $externalblog->failedlastsync = 0;
        $DB->update_record('blog_external', $externalblog);
    }

    $rss = new moodle_simplepie($externalblog->url);

    if (empty($rss->data)) {
        return null;
    }
    //used to identify blog posts that have been deleted from the source feed
    $oldesttimestamp = null;
    $uniquehashes = array();

    foreach ($rss->get_items() as $entry) {
        // If filtertags are defined, use them to filter the entries by RSS category
        if (!empty($externalblog->filtertags)) {
            $containsfiltertag = false;
            $categories = $entry->get_categories();
            $filtertags = explode(',', $externalblog->filtertags);
            $filtertags = array_map('trim', $filtertags);
            $filtertags = array_map('strtolower', $filtertags);

            foreach ($categories as $category) {
                if (in_array(trim(strtolower($category->term)), $filtertags)) {
                    $containsfiltertag = true;
                }
            }

            if (!$containsfiltertag) {
                continue;
            }
        }

        $uniquehashes[] = $entry->get_permalink();

        $newentry = new stdClass();
        $newentry->userid = $externalblog->userid;
        $newentry->module = 'blog_external';
        $newentry->content = $externalblog->id;
        $newentry->uniquehash = $entry->get_permalink();
        $newentry->publishstate = 'site';
        $newentry->format = FORMAT_HTML;
        // Clean subject of html, just in case
        $newentry->subject = clean_param($entry->get_title(), PARAM_TEXT);
        // Observe 128 max chars in DB
        // TODO: +1 to raise this to 255
        if ($textlib->strlen($newentry->subject) > 128) {
            $newentry->subject = $textlib->substr($newentry->subject, 0, 125) . '...';
        }
        $newentry->summary = $entry->get_description();

        //used to decide whether to insert or update
        //uses enty permalink plus creation date if available
        $existingpostconditions = array('uniquehash' => $entry->get_permalink());

        //our DB doesnt allow null creation or modified timestamps so check the external blog supplied one
        $entrydate = $entry->get_date('U');
        if (!empty($entrydate)) {
            $existingpostconditions['created'] = $entrydate;
        }

        //the post ID or false if post not found in DB
        $postid = $DB->get_field('post', 'id', $existingpostconditions);

        $timestamp = null;
        if (empty($entrydate)) {
            $timestamp = time();
        } else {
            $timestamp = $entrydate;
        }

        //only set created if its a new post so we retain the original creation timestamp if the post is edited
        if ($postid === false) {
            $newentry->created = $timestamp;
        }
        $newentry->lastmodified = $timestamp;

        if (empty($oldesttimestamp) || $timestamp < $oldesttimestamp) {
            //found an older post
            $oldesttimestamp = $timestamp;
        }

        $textlib = textlib_get_instance();
        if ($textlib->strlen($newentry->uniquehash) > 255) {
            // The URL for this item is too long for the field. Rather than add
            // the entry without the link we will skip straight over it.
            // RSS spec says recommended length 500, we use 255.
            debugging('External blog entry skipped because of oversized URL', DEBUG_DEVELOPER);
            continue;
        }

        if ($postid === false) {
            $id = $DB->insert_record('post', $newentry);

            // Set tags
            if ($tags = tag_get_tags_array('blog_external', $externalblog->id)) {
                tag_set('post', $id, $tags);
            }
        } else {
            $newentry->id = $postid;
            $DB->update_record('post', $newentry);
        }
    }

    // Look at the posts we have in the database to check if any of them have been deleted from the feed.
    // Only checking posts within the time frame returned by the rss feed. Older items may have been deleted or
    // may just not be returned anymore. We can't tell the difference so we leave older posts alone.
    $sql = "SELECT id, uniquehash
              FROM {post}
             WHERE module = 'blog_external'
                   AND " . $DB->sql_compare_text('content') . " = " . $DB->sql_compare_text(':blogid') . "
                   AND created > :ts";
    $dbposts = $DB->get_records_sql($sql, array('blogid' => $externalblog->id, 'ts' => $oldesttimestamp));

    $todelete = array();
    foreach($dbposts as $dbpost) {
        if ( !in_array($dbpost->uniquehash, $uniquehashes) ) {
            $todelete[] = $dbpost->id;
        }
    }
    $DB->delete_records_list('post', 'id', $todelete);

    $DB->update_record('blog_external', array('id' => $externalblog->id, 'timefetched' => time()));
}

/**
 * Given an external blog object, deletes all related blog entries from the post table.
 * NOTE: The external blog's id is saved as post.content, a field that is not oterhwise used by blog entries.
 * @param object $externablog
 */
function blog_delete_external_entries($externalblog) {
    global $DB;
    require_capability('moodle/blog:manageexternal', get_context_instance(CONTEXT_SYSTEM));
    $DB->delete_records_select('post',
                               "module='blog_external' AND " . $DB->sql_compare_text('content') . " = ?",
                               array($externalblog->id));
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

    $viewblogentriesurl = new moodle_url('/blog/index.php');

    if (empty($context)) {
        global $PAGE;
        $context = $PAGE->context;
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
 * This function checks that blogs are enabled, and that the user can see blogs at all
 * @return bool
 */
function blog_is_enabled_for_user() {
    global $CFG;
    //return (!empty($CFG->bloglevel) && $CFG->bloglevel <= BLOG_GLOBAL_LEVEL && isloggedin() && !isguestuser());
    return (!empty($CFG->bloglevel) && (isloggedin() || ($CFG->bloglevel == BLOG_GLOBAL_LEVEL)));
}

/**
 * This function gets all of the options available for the current user in respect
 * to blogs.
 *
 * It loads the following if applicable:
 * -  Module options {@see blog_get_options_for_module}
 * -  Course options {@see blog_get_options_for_course}
 * -  User specific options {@see blog_get_options_for_user}
 * -  General options (BLOG_LEVEL_GLOBAL)
 *
 * @param moodle_page $page The page to load for (normally $PAGE)
 * @param stdClass $userid Load for a specific user
 * @return array An array of options organised by type.
 */
function blog_get_all_options(moodle_page $page, stdClass $userid = null) {
    global $CFG, $DB, $USER;

    $options = array();

    // If blogs are enabled and the user is logged in and not a guest
    if (blog_is_enabled_for_user()) {
        // If the context is the user then assume we want to load for the users context
        if (is_null($userid) && $page->context->contextlevel == CONTEXT_USER) {
            $userid = $page->context->instanceid;
        }
        // Check the userid var
        if (!is_null($userid) && $userid!==$USER->id) {
            // Load the user from the userid... it MUST EXIST throw a wobbly if it doesn't!
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        } else {
            $user = null;
        }

        if ($CFG->useblogassociations && $page->cm !== null) {
            // Load for the module associated with the page
            $options[CONTEXT_MODULE] = blog_get_options_for_module($page->cm, $user);
        } else if ($CFG->useblogassociations && $page->course->id != SITEID) {
            // Load the options for the course associated with the page
            $options[CONTEXT_COURSE] = blog_get_options_for_course($page->course, $user);
        }

        // Get the options for the user
        if ($user !== null and !isguestuser($user)) {
            // Load for the requested user
            $options[CONTEXT_USER+1] = blog_get_options_for_user($user);
        }
        // Load for the current user
        if (isloggedin() and !isguestuser()) {
            $options[CONTEXT_USER] = blog_get_options_for_user();
        }
    }

    // If blog level is global then display a link to view all site entries
    if (!empty($CFG->bloglevel) && $CFG->bloglevel >= BLOG_GLOBAL_LEVEL && has_capability('moodle/blog:view', get_context_instance(CONTEXT_SYSTEM))) {
        $options[CONTEXT_SYSTEM] = array('viewsite' => array(
            'string' => get_string('viewsiteentries', 'blog'),
            'link' => new moodle_url('/blog/index.php')
        ));
    }

    // Return the options
    return $options;
}

/**
 * Get all of the blog options that relate to the passed user.
 *
 * If no user is passed the current user is assumed.
 *
 * @staticvar array $useroptions Cache so we don't have to regenerate multiple times
 * @param stdClass $user
 * @return array The array of options for the requested user
 */
function blog_get_options_for_user(stdClass $user=null) {
    global $CFG, $USER;
    // Cache
    static $useroptions = array();

    $options = array();
    // Blogs must be enabled and the user must be logged in
    if (!blog_is_enabled_for_user()) {
        return $options;
    }

    // Sort out the user var
    if ($user === null || $user->id == $USER->id) {
        $user = $USER;
        $iscurrentuser = true;
    } else {
        $iscurrentuser = false;
    }

    // If we've already generated serve from the cache
    if (array_key_exists($user->id, $useroptions)) {
        return $useroptions[$user->id];
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $canview = has_capability('moodle/blog:view', $sitecontext);

    if (!$iscurrentuser && $canview && ($CFG->bloglevel >= BLOG_SITE_LEVEL)) {
        // Not the current user, but we can view and its blogs are enabled for SITE or GLOBAL
        $options['userentries'] = array(
            'string' => get_string('viewuserentries', 'blog', fullname($user)),
            'link' => new moodle_url('/blog/index.php', array('userid'=>$user->id))
        );
    } else {
        // It's the current user
        if ($canview) {
            // We can view our own blogs .... BIG surprise
            $options['view'] = array(
                'string' => get_string('viewallmyentries', 'blog'),
                'link' => new moodle_url('/blog/index.php', array('userid'=>$USER->id))
            );
        }
        if (has_capability('moodle/blog:create', $sitecontext)) {
            // We can add to our own blog
            $options['add'] = array(
                'string' => get_string('addnewentry', 'blog'),
                'link' => new moodle_url('/blog/edit.php', array('action'=>'add'))
            );
        }
    }
    // Cache the options
    $useroptions[$user->id] = $options;
    // Return the options
    return $options;
}

/**
 * Get the blog options that relate to the given course for the given user.
 *
 * @staticvar array $courseoptions A cache so we can save regenerating multiple times
 * @param stdClass $course The course to load options for
 * @param stdClass $user The user to load options for null == current user
 * @return array The array of options
 */
function blog_get_options_for_course(stdClass $course, stdClass $user=null) {
    global $CFG, $USER;
    // Cache
    static $courseoptions = array();

    $options = array();

    // User must be logged in and blogs must be enabled
    if (!blog_is_enabled_for_user()) {
        return $options;
    }

    // Check that the user can associate with the course
    $sitecontext =      get_context_instance(CONTEXT_SYSTEM);
    if (!has_capability('moodle/blog:associatecourse', $sitecontext)) {
        return $options;
    }
    // Generate the cache key
    $key = $course->id.':';
    if (!empty($user)) {
        $key .= $user->id;
    } else {
        $key .= $USER->id;
    }
    // Serve from the cache if we've already generated for this course
    if (array_key_exists($key, $courseoptions)) {
        return $courseoptions[$key];
    }

    if (has_capability('moodle/blog:view', get_context_instance(CONTEXT_COURSE, $course->id))) {
        // We can view!
        if ($CFG->bloglevel >= BLOG_SITE_LEVEL) {
            // View entries about this course
            $options['courseview'] = array(
                'string' => get_string('viewcourseblogs', 'blog'),
                'link' => new moodle_url('/blog/index.php', array('courseid'=>$course->id))
            );
        }
        // View MY entries about this course
        $options['courseviewmine'] = array(
            'string' => get_string('viewmyentriesaboutcourse', 'blog'),
            'link' => new moodle_url('/blog/index.php', array('courseid'=>$course->id, 'userid'=>$USER->id))
        );
        if (!empty($user) && ($CFG->bloglevel >= BLOG_SITE_LEVEL)) {
            // View the provided users entries about this course
            $options['courseviewuser'] = array(
                'string' => get_string('viewentriesbyuseraboutcourse', 'blog', fullname($user)),
                'link' => new moodle_url('/blog/index.php', array('courseid'=>$course->id, 'userid'=>$user->id))
            );
        }
    }

    if (has_capability('moodle/blog:create', $sitecontext)) {
        // We can blog about this course
        $options['courseadd'] = array(
            'string' => get_string('blogaboutthiscourse', 'blog'),
            'link' => new moodle_url('/blog/edit.php', array('action'=>'add', 'courseid'=>$course->id))
        );
    }


    // Cache the options for this course
    $courseoptions[$key] = $options;
    // Return the options
    return $options;
}

/**
 * Get the blog options relating to the given module for the given user
 *
 * @staticvar array $moduleoptions Cache
 * @param stdClass|cm_info $module The module to get options for
 * @param stdClass $user The user to get options for null == currentuser
 * @return array
 */
function blog_get_options_for_module($module, $user=null) {
    global $CFG, $USER;
    // Cache
    static $moduleoptions = array();

    $options = array();
    // User must be logged in, blogs must be enabled
    if (!blog_is_enabled_for_user()) {
        return $options;
    }

    // Check the user can associate with the module
    $sitecontext =      get_context_instance(CONTEXT_SYSTEM);
    if (!has_capability('moodle/blog:associatemodule', $sitecontext)) {
        return $options;
    }

    // Generate the cache key
    $key = $module->id.':';
    if (!empty($user)) {
        $key .= $user->id;
    } else {
        $key .= $USER->id;
    }
    if (array_key_exists($key, $moduleoptions)) {
        // Serve from the cache so we don't have to regenerate
        return $moduleoptions[$module->id];
    }

    if (has_capability('moodle/blog:view', get_context_instance(CONTEXT_MODULE, $module->id))) {
        // We can view!
        if ($CFG->bloglevel >= BLOG_SITE_LEVEL) {
            // View all entries about this module
            $a = new stdClass;
            $a->type = $module->modname;
            $options['moduleview'] = array(
                'string' => get_string('viewallmodentries', 'blog', $a),
                'link' => new moodle_url('/blog/index.php', array('modid'=>$module->id))
            );
        }
        // View MY entries about this module
        $options['moduleviewmine'] = array(
            'string' => get_string('viewmyentriesaboutmodule', 'blog', $module->modname),
            'link' => new moodle_url('/blog/index.php', array('modid'=>$module->id, 'userid'=>$USER->id))
        );
        if (!empty($user) && ($CFG->bloglevel >= BLOG_SITE_LEVEL)) {
            // View the given users entries about this module
            $a = new stdClass;
            $a->mod = $module->modname;
            $a->user = fullname($user);
            $options['moduleviewuser'] = array(
                'string' => get_string('blogentriesbyuseraboutmodule', 'blog', $a),
                'link' => new moodle_url('/blog/index.php', array('modid'=>$module->id, 'userid'=>$user->id))
            );
        }
    }

    if (has_capability('moodle/blog:create', $sitecontext)) {
        // The user can blog about this module
        $options['moduleadd'] = array(
            'string' => get_string('blogaboutthismodule', 'blog', $module->modname),
            'link' => new moodle_url('/blog/edit.php', array('action'=>'add', 'modid'=>$module->id))
        );
    }
    // Cache the options
    $moduleoptions[$key] = $options;
    // Return the options
    return $options;
}

/**
 * This function encapsulates all the logic behind the complex
 * navigation, titles and headings of the blog listing page, depending
 * on URL params. It looks at URL params and at the current context level.
 * It builds and returns an array containing:
 *
 * 1. heading: The heading displayed above the blog entries
 * 2. stradd:  The text to be used as the "Add entry" link
 * 3. strview: The text to be used as the "View entries" link
 * 4. url:     The moodle_url object used as the base for add and view links
 * 5. filters: An array of parameters used to filter blog listings. Used by index.php and the Recent blogs block
 *
 * All other variables are set directly in $PAGE
 *
 * It uses the current URL to build these variables.
 * A number of mutually exclusive use cases are used to structure this function.
 *
 * @return array
 */
function blog_get_headers($courseid=null, $groupid=null, $userid=null, $tagid=null) {
    global $CFG, $PAGE, $DB, $USER;

    $id       = optional_param('id', null, PARAM_INT);
    $tag      = optional_param('tag', null, PARAM_NOTAGS);
    $tagid    = optional_param('tagid', $tagid, PARAM_INT);
    $userid   = optional_param('userid', $userid, PARAM_INT);
    $modid    = optional_param('modid', null, PARAM_INT);
    $entryid  = optional_param('entryid', null, PARAM_INT);
    $groupid  = optional_param('groupid', $groupid, PARAM_INT);
    $courseid = optional_param('courseid', $courseid, PARAM_INT);
    $search   = optional_param('search', null, PARAM_RAW);
    $action   = optional_param('action', null, PARAM_ALPHA);
    $confirm  = optional_param('confirm', false, PARAM_BOOL);

    // Ignore userid when action == add
    if ($action == 'add' && $userid) {
        unset($userid);
        $PAGE->url->remove_params(array('userid'));
    } else if ($action == 'add' && $entryid) {
        unset($entryid);
        $PAGE->url->remove_params(array('entryid'));
    }

    $headers = array('title' => '', 'heading' => '', 'cm' => null, 'filters' => array());

    $blogurl = new moodle_url('/blog/index.php');

    // If the title is not yet set, it's likely that the context isn't set either, so skip this part
    $pagetitle = $PAGE->title;
    if (!empty($pagetitle)) {
        $contexturl = blog_get_context_url();

        // Look at the context URL, it may have additional params that are not in the current URL
        if (!$blogurl->compare($contexturl)) {
            $blogurl = $contexturl;
            if (empty($courseid)) {
                $courseid = $blogurl->param('courseid');
            }
            if (empty($modid)) {
                $modid = $blogurl->param('modid');
            }
        }
    }

    $headers['stradd'] = get_string('addnewentry', 'blog');
    $headers['strview'] = null;

    $site = $DB->get_record('course', array('id' => SITEID));
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    // Common Lang strings
    $strparticipants = get_string("participants");
    $strblogentries  = get_string("blogentries", 'blog');

    // Prepare record objects as needed
    if (!empty($courseid)) {
        $headers['filters']['course'] = $courseid;
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    if (!empty($userid)) {
        $headers['filters']['user'] = $userid;
        $user = $DB->get_record('user', array('id' => $userid));
    }

    if (!empty($groupid)) { // groupid always overrides courseid
        $headers['filters']['group'] = $groupid;
        $group = $DB->get_record('groups', array('id' => $groupid));
        $course = $DB->get_record('course', array('id' => $group->courseid));
    }

    $PAGE->set_pagelayout('standard');

    if (!empty($modid) && $CFG->useblogassociations && has_capability('moodle/blog:associatemodule', $sitecontext)) { // modid always overrides courseid, so the $course object may be reset here
        $headers['filters']['module'] = $modid;
        // A groupid param may conflict with this coursemod's courseid. Ignore groupid in that case
        $courseid = $DB->get_field('course_modules', 'course', array('id'=>$modid));
        $course = $DB->get_record('course', array('id' => $courseid));
        $cm = $DB->get_record('course_modules', array('id' => $modid));
        $cm->modname = $DB->get_field('modules', 'name', array('id' => $cm->module));
        $cm->name = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance));
        $a = new stdClass();
        $a->type = get_string('modulename', $cm->modname);
        $PAGE->set_cm($cm, $course);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewallmodentries', 'blog', $a);
    }

    // Case 1: No entry, mod, course or user params: all site entries to be shown (filtered by search and tag/tagid)
    // Note: if action is set to 'add' or 'edit', we do this at the end
    if (empty($entryid) && empty($modid) && empty($courseid) && empty($userid) && !in_array($action, array('edit', 'add'))) {
        $PAGE->navbar->add($strblogentries, $blogurl);
        $PAGE->set_title("$site->shortname: " . get_string('blog', 'blog'));
        $PAGE->set_heading("$site->shortname: " . get_string('blog', 'blog'));
        $headers['heading'] = get_string('siteblog', 'blog', $site->shortname);
        // $headers['strview'] = get_string('viewsiteentries', 'blog');
    }

    // Case 2: only entryid is requested, ignore all other filters. courseid is used to give more contextual information
    if (!empty($entryid)) {
        $headers['filters']['entry'] = $entryid;
        $sql = 'SELECT u.* FROM {user} u, {post} p WHERE p.id = ? AND p.userid = u.id';
        $user = $DB->get_record_sql($sql, array($entryid));
        $entry = $DB->get_record('post', array('id' => $entryid));

        $blogurl->param('userid', $user->id);

        if (!empty($course)) {
            $mycourseid = $course->id;
            $blogurl->param('courseid', $mycourseid);
        } else {
            $mycourseid = $site->id;
        }

        $PAGE->navbar->add($strblogentries, $blogurl);

        $blogurl->remove_params('userid');
        $PAGE->navbar->add($entry->subject, $blogurl);

        $PAGE->set_title("$site->shortname: " . fullname($user) . ": $entry->subject");
        $PAGE->set_heading("$site->shortname: " . fullname($user) . ": $entry->subject");
        $headers['heading'] = get_string('blogentrybyuser', 'blog', fullname($user));

        // We ignore tag and search params
        if (empty($action) || !$CFG->useblogassociations) {
            $headers['url'] = $blogurl;
            return $headers;
        }
    }

    // Case 3: A user's blog entries
    if (!empty($userid) && empty($entryid) && ((empty($courseid) && empty($modid)) || !$CFG->useblogassociations)) {
        $blogurl->param('userid', $userid);
        $PAGE->set_title("$site->shortname: " . fullname($user) . ": " . get_string('blog', 'blog'));
        $PAGE->set_heading("$site->shortname: " . fullname($user) . ": " . get_string('blog', 'blog'));
        $headers['heading'] = get_string('userblog', 'blog', fullname($user));
        $headers['strview'] = get_string('viewuserentries', 'blog', fullname($user));

    } else

    // Case 4: No blog associations, no userid
    if (!$CFG->useblogassociations && empty($userid) && !in_array($action, array('edit', 'add'))) {
        $PAGE->set_title("$site->shortname: " . get_string('blog', 'blog'));
        $PAGE->set_heading("$site->shortname: " . get_string('blog', 'blog'));
        $headers['heading'] = get_string('siteblog', 'blog', $site->shortname);
    } else

    // Case 5: Blog entries associated with an activity by a specific user (courseid ignored)
    if (!empty($userid) && !empty($modid) && empty($entryid)) {
        $blogurl->param('userid', $userid);
        $blogurl->param('modid', $modid);

        // Course module navigation is handled by build_navigation as the second param
        $headers['cm'] = $cm;
        $PAGE->navbar->add(fullname($user), "$CFG->wwwroot/user/view.php?id=$user->id");
        $PAGE->navbar->add($strblogentries, $blogurl);

        $PAGE->set_title("$site->shortname: $cm->name: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $cm->name: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));

        $a = new stdClass();
        $a->user = fullname($user);
        $a->mod = $cm->name;
        $a->type = get_string('modulename', $cm->modname);
        $headers['heading'] = get_string('blogentriesbyuseraboutmodule', 'blog', $a);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewallmodentries', 'blog', $a);
    } else

    // Case 6: Blog entries associated with a course by a specific user
    if (!empty($userid) && !empty($courseid) && empty($modid) && empty($entryid)) {
        $blogurl->param('userid', $userid);
        $blogurl->param('courseid', $courseid);

        $PAGE->navbar->add($strblogentries, $blogurl);

        $PAGE->set_title("$site->shortname: $course->shortname: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: " . fullname($user) . ': ' . get_string('blogentries', 'blog'));

        $a = new stdClass();
        $a->user = fullname($user);
        $a->course = $course->fullname;
        $a->type = get_string('course');
        $headers['heading'] = get_string('blogentriesbyuseraboutcourse', 'blog', $a);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewblogentries', 'blog', $a);

        // Remove the userid from the URL to inform the blog_menu block correctly
        $blogurl->remove_params(array('userid'));
    } else

    // Case 7: Blog entries by members of a group, associated with that group's course
    if (!empty($groupid) && empty($modid) && empty($entryid)) {
        $blogurl->param('courseid', $course->id);

        $PAGE->navbar->add($strblogentries, $blogurl);
        $blogurl->remove_params(array('courseid'));
        $blogurl->param('groupid', $groupid);
        $PAGE->navbar->add($group->name, $blogurl);

        $PAGE->set_title("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog') . ": $group->name");
        $PAGE->set_heading("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog') . ": $group->name");

        $a = new stdClass();
        $a->group = $group->name;
        $a->course = $course->fullname;
        $a->type = get_string('course');
        $headers['heading'] = get_string('blogentriesbygroupaboutcourse', 'blog', $a);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewblogentries', 'blog', $a);
    } else

    // Case 8: Blog entries by members of a group, associated with an activity in that course
    if (!empty($groupid) && !empty($modid) && empty($entryid)) {
        $headers['cm'] = $cm;
        $blogurl->param('modid', $modid);
        $PAGE->navbar->add($strblogentries, $blogurl);

        $blogurl->param('groupid', $groupid);
        $PAGE->navbar->add($group->name, $blogurl);

        $PAGE->set_title("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog') . ": $group->name");
        $PAGE->set_heading("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog') . ": $group->name");

        $a = new stdClass();
        $a->group = $group->name;
        $a->mod = $cm->name;
        $a->type = get_string('modulename', $cm->modname);
        $headers['heading'] = get_string('blogentriesbygroupaboutmodule', 'blog', $a);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewallmodentries', 'blog', $a);

    } else

    // Case 9: All blog entries associated with an activity
    if (!empty($modid) && empty($userid) && empty($groupid) && empty($entryid)) {
        $PAGE->set_cm($cm, $course);
        $blogurl->param('modid', $modid);
        $PAGE->navbar->add($strblogentries, $blogurl);
        $PAGE->set_title("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog'));
        $headers['heading'] = get_string('blogentriesabout', 'blog', $cm->name);
        $a = new stdClass();
        $a->type = get_string('modulename', $cm->modname);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewallmodentries', 'blog', $a);
    } else

    // Case 10: All blog entries associated with a course
    if (!empty($courseid) && empty($userid) && empty($groupid) && empty($modid) && empty($entryid)) {
        $blogurl->param('courseid', $courseid);
        $PAGE->navbar->add($strblogentries, $blogurl);
        $PAGE->set_title("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog'));
        $PAGE->set_heading("$site->shortname: $course->shortname: " . get_string('blogentries', 'blog'));
        $a = new stdClass();
        $a->type = get_string('course');
        $headers['heading'] = get_string('blogentriesabout', 'blog', $course->fullname);
        $headers['stradd'] = get_string('blogaboutthis', 'blog', $a);
        $headers['strview'] = get_string('viewblogentries', 'blog', $a);
        $blogurl->remove_params(array('userid'));
    }

    if (!in_array($action, array('edit', 'add'))) {
        // Append Tag info
        if (!empty($tagid)) {
            $headers['filters']['tag'] = $tagid;
            $blogurl->param('tagid', $tagid);
            $tagrec = $DB->get_record('tag', array('id'=>$tagid));
            $PAGE->navbar->add($tagrec->name, $blogurl);
        } elseif (!empty($tag)) {
            $blogurl->param('tag', $tag);
            $PAGE->navbar->add(get_string('tagparam', 'blog', $tag), $blogurl);
        }

        // Append Search info
        if (!empty($search)) {
            $headers['filters']['search'] = $search;
            $blogurl->param('search', $search);
            $PAGE->navbar->add(get_string('searchterm', 'blog', $search), $blogurl->out());
        }
    }

    // Append edit mode info
    if (!empty($action) && $action == 'add') {

    } else if (!empty($action) && $action == 'edit') {
        $PAGE->navbar->add(get_string('editentry', 'blog'));
    }

    if (empty($headers['url'])) {
        $headers['url'] = $blogurl;
    }
    return $headers;
}

/**
 * Shortcut function for getting a count of blog entries associated with a course or a module
 * @param int $courseid The ID of the course
 * @param int $cmid The ID of the course_modules
 * @return string The number of associated entries
 */
function blog_get_associated_count($courseid, $cmid=null) {
    global $DB;
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if ($cmid) {
        $context = get_context_instance(CONTEXT_MODULE, $cmid);
    }
    return $DB->count_records('blog_association', array('contextid' => $context->id));
}

/**
 * Running addtional permission check on plugin, for example, plugins
 * may have switch to turn on/off comments option, this callback will
 * affect UI display, not like pluginname_comment_validate only throw
 * exceptions.
 * Capability check has been done in comment->check_permissions(), we
 * don't need to do it again here.
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return array
 */
function blog_comment_permissions($comment_param) {
    return array('post'=>true, 'view'=>true);
}

/**
 * Validate comment parameter before perform other comments actions
 *
 * @param stdClass $comment {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return boolean
 */
function blog_comment_validate($comment_param) {
    global $DB;
    // validate comment itemid
    if (!$entry = $DB->get_record('post', array('id'=>$comment_param->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    // validate comment area
    if ($comment_param->commentarea != 'format_blog') {
        throw new comment_exception('invalidcommentarea');
    }
    // validation for comment deletion
    if (!empty($comment_param->commentid)) {
        if ($record = $DB->get_record('comments', array('id'=>$comment_param->commentid))) {
            if ($record->commentarea != 'format_blog') {
                throw new comment_exception('invalidcommentarea');
            }
            if ($record->contextid != $comment_param->context->id) {
                throw new comment_exception('invalidcontext');
            }
            if ($record->itemid != $comment_param->itemid) {
                throw new comment_exception('invalidcommentitemid');
            }
        } else {
            throw new comment_exception('invalidcommentid');
        }
    }
    return true;
}
