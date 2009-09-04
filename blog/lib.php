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

    $pagingbar = moodle_paging_bar::make($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect));
    $pagingbar->pagevar = 'blogpage';
    echo $OUTPUT->paging_bar($pagingbar);


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
            $count = 0;
            foreach ($blogEntries as $blogEntry) {
                blog_print_entry($blogEntry, 'list', $filtertype, $filterselect); //print this entry.
                $count++;
            }
            $pagingbar = moodle_paging_bar::make($totalentries, $blogpage, $bloglimit, get_baseurl($filtertype, $filterselect));
            $pagingbar->pagevar = 'blogpage';
            echo $OUTPUT->paging_bar($pagingbar);

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
    return $CFG->wwwroot.'/blog/index.php?'.
        (empty($filters['course']) ? '' : 'courseid='.$filters['course'].'&amp;').
        (empty($filters['mod']) ? '' : 'modid='.$filters['mod'].'&amp;').
        (empty($filters['group']) ? '' : 'groupid='.$filters['group'].'&amp;').
        (empty($filters['user']) ? '' : 'userid='.$filters['user'].'&amp;').
        (empty($filters['entry']) ? '' : 'entryid='.$filters['entry'].'&amp;').
        (empty($filters['tag']) ? '' : 'tagid='.$filters['tag'].'&amp;').
        (empty($filters['tagtext']) ? '' : 'tag='.$filters['tagtext']);
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
 * This function is in lib and not in BlogInfo because entries being searched
 * might be found in any number of blogs rather than just one.
 *
 * This function builds an array which can be used by the included
 * template file, making predefined and nicely formatted variables available
 * to the template. Template creators will not need to become intimate
 * with the internal objects and vars of moodle blog nor will they need to worry
 * about properly formatting their data
 *
 *   @param BlogEntry blogEntry - a hopefully fully populated BlogEntry object
 *   @param string viewtype Default is 'full'. If 'full' then display this blog entry
 *     in its complete form (eg. archive page). If anything other than 'full'
 *     display the entry in its abbreviated format (eg. index page)
 */
function blog_print_entry($blogEntry, $viewtype='full', $filtertype='', $filterselect='', $mode='loud') {
    global $USER, $CFG, $COURSE, $DB, $OUTPUT;

    $template['body'] = format_text($blogEntry->summary, $blogEntry->format);
    $template['title'] = '<a id="b'. s($blogEntry->id) .'" />';
    //enclose the title in nolink tags so that moodle formatting doesn't autolink the text
    $template['title'] .= '<span class="nolink">'. format_string($blogEntry->subject) .'</span>';
    $template['userid'] = $blogEntry->userid;
    $template['author'] = fullname($DB->get_record('user', array('id'=>$blogEntry->userid)));
    $template['created'] = userdate($blogEntry->created);

    if($blogEntry->created != $blogEntry->lastmodified){
        $template['lastmod'] = userdate($blogEntry->lastmodified);
    }

    $template['publishstate'] = $blogEntry->publishstate;

    /// preventing user to browse blogs that they aren't supposed to see
    /// This might not be too good since there are multiple calls per page

    /*
    if (!blog_user_can_view_user_post($template['userid'])) {
        print_error('cannotviewuserblog', 'blog');
    }*/

    $stredit = get_string('edit');
    $strdelete = get_string('delete');

    $user = $DB->get_record('user', array('id'=>$template['userid']));

    /// Start printing of the blog

    echo '<table cellspacing="0" class="forumpost blogpost blog'.$template['publishstate'].'" width="100%">';

    echo '<tr class="header"><td class="picture left">';
    echo $OUTPUT->user_picture(moodle_user_picture::make($user, SITEID));
    echo '</td>';

    echo '<td class="topic starter"><div class="subject">'.$template['title'].'</div><div class="author">';
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $COURSE->id)));
    $by = new object();
    $by->name =  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                $user->id.'&amp;course='.$COURSE->id.'">'.$fullname.'</a>';
    $by->date = $template['created'];
    print_string('bynameondate', 'forum', $by);
    echo '</div></td></tr>';

    echo '<tr><td class="left side">';

/// Actual content

    echo '</td><td class="content">'."\n";

    if ($blogEntry->attachment) {
        echo '<div class="attachments">';
        $attachedimages = blog_print_attachments($blogEntry);
        echo '</div>';
    } else {
        $attachedimages = '';
    }

    switch ($template['publishstate']) {
        case 'draft':
            $blogtype = get_string('publishtonoone', 'blog');
        break;
        case 'site':
            $blogtype = get_string('publishtosite', 'blog');
        break;
        case 'public':
            $blogtype = get_string('publishtoworld', 'blog');
        break;
        default:
            $blogtype = '';
        break;

    }

    echo '<div class="audience">'.$blogtype.'</div>';

    // Print whole message
    echo $template['body'];

    /// Print attachments
    echo $attachedimages;
/// Links to tags

    if ( !empty($CFG->usetags) && ($blogtags = tag_get_tags_csv('post', $blogEntry->id)) ) {
        echo '<div class="tags">';
        if ($blogtags) {
            print(get_string('tags', 'tag') .': '. $blogtags);
       }
        echo '</div>';
    }

/// Commands

    echo '<div class="commands">';

    if (blog_user_can_edit_post($blogEntry)) {
        echo '<a href="'.$CFG->wwwroot.'/blog/edit.php?action=edit&amp;id='.$blogEntry->id.'">'.$stredit.'</a>';
        echo '| <a href="'.$CFG->wwwroot.'/blog/edit.php?action=delete&amp;id='.$blogEntry->id.'">'.$strdelete.'</a> | ';
    }

    echo '<a href="'.$CFG->wwwroot.'/blog/index.php?postid='.$blogEntry->id.'">'.get_string('permalink', 'blog').'</a>';

    echo '</div>';

    if( isset($template['lastmod']) ){
        echo '<div style="font-size: 55%;">';
        echo ' [ '.get_string('modified').': '.$template['lastmod'].' ]';
        echo '</div>';
    }

    echo '</td></tr></table>'."\n\n";

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
    require_once($CFG->libdir . '/magpie/rss_fetch.inc');

    if (!blog_is_valid_url($external_blog->url)) {
        return null;
    }

    if (!$rss = fetch_rss($external_blog->url)) {
        return null;
    }

    if (empty($rss->channel) || empty($rss->items)) {
        return null;
    }

    foreach ($rss->items as $entry) {
        $params = array('userid' => $external_blog->userid,
                        'module' => 'blog',
                        'uniquehash' => $entry['link'],
                        'publishstate' => 'site',
                        'format' => FORMAT_HTML);

        if (!$DB->record_exists('post', $params)) {
            $params['subject']      = $entry['title'];
            $params['summary']      = $entry['description'];
            $params['created']      = $entry['date_timestamp'];
            $params['lastmodified'] = $entry['date_timestamp'];

            $id = $DB->insert_record('post', $params);

            // Set tags
            if ($tags = tag_get_tags_array('blog_external', $external_blog->id)) {
                tag_set('post', $id, $tags);
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

    $viewblogentries_url = $CFG->wwwroot . '/blog/index.php?';

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
        $viewblogentries_url .= "$filterparam=$context->instanceid";
    }

    return $viewblogentries_url;
}

/**
 * This function encapsulates all the logic behind the complex
 * navigation, titles and headings of the blog listing page, depending
 * on URL params. It builds and returns an array containing:
 *
 * 1. The navlinks used to build the breadcrumbs
 * 2. The title shown in the browser and at the top of the web page
 * 3. The heading displayed above the blog entries
 *
 * It uses the current URL to build these variables.
 * A number of mutually exclusive use cases are used to structure this function.
 *
 * @return array
 */
function blog_get_headers() {
    global $CFG, $PAGE, $DB, $USER;

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

    $headers = array('navlinks' => array(), 'title' => '', 'heading' => '', 'cm' => null);

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
    }

    // Case 1: only entryid is requested, ignore all other filters. courseid is used to give more contextual information
    // Breadcrumbs: [site shortname] -> [?course shortname] -> participants -> [user fullname] -> Blog entries -> [Entry subject]
    // Title: [site shortname]: [user fullname]: Blog entry
    // Heading: [Entry subject] by [user fullname]
    if (!empty($entryid)) {
        $sql = 'SELECT u.* FROM {user} u, {post} p WHERE p.id = ? AND p.userid = u.id';
        $user = $DB->get_record_sql($sql, array($entryid));
        $entry = $DB->get_record('post', array('id' => $entryid));

        $blog_url->param('userid', $user->id);

        if (!empty($course)) {
            $courseid = $course->id;
            $blog_url->param('courseid', $courseid);
        } else {
            $courseid = $site->id;
        }

        $headers['navlinks'][] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$courseid", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$user->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');
        $headers['navlinks'][] = array('name' => $entry->subject, 'link' => null, 'type' => 'misc');

        $headers['title'] = "$site->shortname: " . fullname($user) . ": $entry->subject";
        $headers['heading'] = get_string('blogentrybyuser', 'blog', fullname($user));

        // We ignore tag and search params
        return $headers;
    }

    // Case 2: A user's blog entries
    // Breadcrumbs: [site shortname] -> participants -> [user fullname] -> Blog entries
    // Title: [site shortname]: [user fullname]: Blog
    // Heading: [user fullname]'s blog
    if (!empty($userid) && empty($modid) && empty($courseid)) {
        $blog_url->param('userid', $userid);
        $headers['navlinks'][] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$site->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$user->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');
        $headers['title'] = "$site->shortname: " . fullname($user) . ": " . get_string('blog', 'blog');
        $headers['heading'] = get_string('userblog', 'blog', fullname($user));

    } else

    // Case 3: Blog entries associated with an activity by a specific user (courseid ignored)
    // Breadcrumbs: [site shortname] -> [course shortname] -> [activity name] -> [user fullname] -> Blog entries
    // Title: [site shortname]: [course shortname]: [activity name]: [user fullname]: blog entries
    // Heading: Blog entries by [user fullname] about [activity name]
    if (!empty($userid) && !empty($modid)) {
        $blog_url->param('userid', $userid);
        $blog_url->param('modid', $modid);

        // Course module navigation is handled by build_navigation as the second param
        $headers['cm'] = $cm;
        $headers['navlinks'][] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$user->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');

        $headers['title'] = "$site->shortname: $cm->name: " . fullname($user) . ': ' . get_string('blogentries', 'blog');

        $a->user = fullname($user);
        $a->mod = $cm->name;
        $headers['heading'] = get_string('blogentriesbyuseraboutmodule', 'blog', $a);
    } else

    // Case 4: Blog entries associated with a course by a specific user
    // Breadcrumbs: [site shortname] -> [course shortname] -> participants -> [user fullname] -> Blog entries
    // Title: [site shortname]: [course shortname]: participants: [user fullname]: blog entries
    // Heading: Blog entries by [user fullname] about [course fullname]
    if (!empty($userid) && !empty($courseid) && empty($modid)) {
        $blog_url->param('userid', $userid);
        $blog_url->param('courseid', $courseid);

        $headers['navlinks'][] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$course->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$user->id", 'type' => 'misc');
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');

        $headers['title'] = "$site->shortname: $course->shortname: " . fullname($user) . ': ' . get_string('blogentries', 'blog');

        $a->user = fullname($user);
        $a->course = $course->fullname;
        $headers['heading'] = get_string('blogentriesbyuseraboutcourse', 'blog', $a);
    } else

    // Case 5: Blog entries by members of a group, associated with that group's course
    // Breadcrumbs: [site shortname] -> [course shortname] -> Blog entries -> [group name]
    // Title: [site shortname]: [course shortname]: blog entries : [group name]
    // Heading: Blog entries by [group name] about [course fullname]
    if (!empty($groupid) && empty($modid)) {
        $blog_url->param('courseid', $course->id);
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');
        $blog_url->remove_params(array('courseid'));
        $blog_url->param('groupid', $groupid);
        $headers['navlinks'][] = array('name' => $group->name, 'link' => $blog_url->out(), 'type' => 'misc');

        $headers['title'] = "$site->shortname: $course->shortname: " . get_string('blogentries', 'blog') . ": $group->name";

        $a->group = $group->name;
        $a->course = $course->fullname;
        $headers['heading'] = get_string('blogentriesbygroupaboutcourse', 'blog', $a);
    } else

    // Case 6: Blog entries by members of a group, associated with an activity in that course
    // Breadcrumbs: [site shortname] -> [course shortname] -> [activity name] -> Blog entries -> [group name]
    // Title: [site shortname]: [course shortname]: [activity name] : blog entries : [group name]
    // Heading: Blog entries by [group name] about [activity fullname]
    if (!empty($groupid) && !empty($modid)) {
        $headers['cm'] = $cm;
        $blog_url->param('modid', $modid);
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');

        $blog_url->param('groupid', $groupid);
        $headers['navlinks'][] = array('name' => $group->name, 'link' => $blog_url->out(), 'type' => 'misc');

        $headers['title'] = "$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog') . ": $group->name";

        $a->group = $group->name;
        $a->mod = $cm->name;
        $headers['heading'] = get_string('blogentriesbygroupaboutmodule', 'blog', $a);

    } else

    // Case 7: All blog entries associated with an activity
    // Breadcrumbs: [site shortname] -> [course shortname] -> [activity name] -> Blog entries
    // Title: [site shortname]: [course shortname]: [activity name] : blog entries
    // Heading: Blog entries about [activity fullname]
    if (!empty($modid) && empty($userid) && empty($groupid)) {
        $headers['cm'] = $cm;
        $blog_url->param('modid', $modid);
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');
        $headers['title'] = "$site->shortname: $course->shortname: $cm->name: " . get_string('blogentries', 'blog');
        $headers['heading'] = get_string('blogentriesabout', 'blog', $cm->name);
    } else

    // Case 8: All blog entries associated with a course
    // Breadcrumbs: [site shortname] -> [course shortname] -> Blog entries
    // Title: [site shortname]: [course shortname]: blog entries
    // Heading: Blog entries about [course fullname]
    if (!empty($courseid) && empty($userid) && empty($groupid) && empty($modid)) {
        $blog_url->param('courseid', $courseid);
        $headers['navlinks'][] = array('name' => $strblogentries, 'link' => $blog_url->out(), 'type' => 'misc');
        $headers['title'] = "$site->shortname: $course->shortname: " . get_string('blogentries', 'blog');
        $headers['heading'] = get_string('blogentriesabout', 'blog', $course->fullname);
    }

    // Append Tag info
    if (!empty($tagid)) {
        $blog_url->param('tagid', $tagid);
        $tagrec = $DB->get_record('tag', array('id'=>$tagid));
        $headers['navlinks'][] = array('name' => $tagrec->name, 'link' => $blog_url->out(), 'type' => 'misc');
    } elseif (!empty($tag)) {
        $blog_url->param('tag', $tag);
        $headers['navlinks'][] = array('name' => get_string('tagparam', 'blog', $tag), 'link' => $blog_url->out(), 'type' => 'misc');
    }

    // Append Search info
    if (!empty($search)) {
        $blog_url->param('search', $search);
        $headers['navlinks'][] = array('name' => get_string('searchterm', 'blog', $search), 'link' => $blog_url->out(), 'type' => 'misc');
    }

    // Append edit mode info
    if (!empty($action) && $action == 'add') {
        $headers['navlinks'][] = array('name' => get_string('addnewentry', 'blog'), 'link' => null, 'type' => 'misc');
    } else if (!empty($action) && $action == 'edit') {
        $headers['navlinks'][] = array('name' => get_string('editentry', 'blog'), 'link' => null, 'type' => 'misc');
    }
    return $headers;
}
