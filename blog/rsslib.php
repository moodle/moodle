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
 * Blog RSS Management
 *
 * @package    core_blog
 * @category   rss
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/rsslib.php');
require_once($CFG->dirroot .'/blog/lib.php');

/**
 * Build the URL for the RSS feed
 *
 * @param int    $contextid    The context under which the URL should be created
 * @param int    $userid       The id of the user requesting the RSS Feed
 * @param string $filtertype   The source of the RSS feed (site/course/group/user)
 * @param int    $filterselect The id of the item defined by $filtertype
 * @param int    $tagid        The id of the row in the tag table that identifies the RSS Feed
 * @return string
 */
function blog_rss_get_url($contextid, $userid, $filtertype, $filterselect = 0, $tagid = 0) {
    $componentname = 'blog';

    $additionalargs = null;
    switch ($filtertype) {
        case 'site':
            $additionalargs = 'site/'.SITEID;
        break;
        case 'course':
            $additionalargs = 'course/'.$filterselect;
        break;
        case 'group':
            $additionalargs = 'group/'.$filterselect;
        break;
        case 'user':
            $additionalargs = 'user/'.$filterselect;
        break;
    }

    if ($tagid) {
        $additionalargs .= '/'.$tagid;
    }

    return rss_get_url($contextid, $userid, $componentname, $additionalargs);
}

/**
 * Print the link for the RSS feed with the correct RSS icon (Theme based)
 *
 * @param stdClass    $context      The context under which the URL should be created
 * @param string      $filtertype   The source of the RSS feed (site/course/group/user)
 * @param int         $filterselect The id of the item defined by $filtertype
 * @param int         $tagid        The id of the row in the tag table that identifies the RSS Feed
 * @param string      $tooltiptext  The tooltip to be displayed with the link
 */
function blog_rss_print_link($context, $filtertype, $filterselect = 0, $tagid = 0, $tooltiptext = '') {
    global $CFG, $USER, $OUTPUT;

    if (!isloggedin()) {
        $userid = $CFG->siteguest;
    } else {
        $userid = $USER->id;
    }

    $url = blog_rss_get_url($context->id, $userid, $filtertype, $filterselect, $tagid);
    $rsspix = $OUTPUT->pix_icon('i/rss', get_string('rss'), 'core', array('title' => $tooltiptext));
    print '<div class="float-sm-right"><a href="'. $url .'">' . $rsspix . '</a></div>';
}

/**
 * Build the URL for the RSS feed amd add it as a header
 *
 * @param stdClass    $context      The context under which the URL should be created
 * @param string      $title        Name for the link to be added to the page header
 * @param string      $filtertype   The source of the RSS feed (site/course/group/user)
 * @param int         $filterselect The id of the item defined by $filtertype
 * @param int         $tagid        The id of the row in the tag table that identifies the RSS Feed
 */
function blog_rss_add_http_header($context, $title, $filtertype, $filterselect = 0, $tagid = 0) {
    global $PAGE, $USER, $CFG;

    if (!isloggedin()) {
        $userid = $CFG->siteguest;
    } else {
        $userid = $USER->id;
    }

    $rsspath = blog_rss_get_url($context->id, $userid, $filtertype, $filterselect, $tagid);
    $PAGE->add_alternate_version($title, $rsspath, 'application/rss+xml');
}

/**
 * Utility function to extract parameters needed to generate RSS URLs from the blog filters
 *
 * @param  array $filters filters for the blog
 * @return array array containing the id of the user/course/group, the relevant context and the filter type: site/user/course/group
 */
function blog_rss_get_params($filters) {
    $thingid = $rsscontext = $filtertype = null;

    $sitecontext = context_system::instance();

    if (!$filters) {
        $thingid = SITEID;
        $filtertype = 'site';
    } else if (array_key_exists('course', $filters)) {
        $thingid = $filters['course'];
        $filtertype = 'course';
    } else if (array_key_exists('user', $filters)) {
        $thingid = $filters['user'];
        $filtertype = 'user';
    } else if (array_key_exists('group', $filters)) {
        $thingid = $filters['group'];
        $filtertype = 'group';
    }

    return array($thingid, $rsscontext, $filtertype);
}

/**
 * Generate any blog RSS feed via one function
 *
 * @param stdClass $context The context of the blog for which the feed it being generated
 * @param array    $args    An array of arguements needed to build the feed (contextid, token, componentname, type, id, tagid)
 */
function blog_rss_get_feed($context, $args) {
    global $CFG, $SITE, $DB;

    if (empty($CFG->enableblogs)) {
        debugging('Blogging disabled on this site, RSS feeds are not available');
        return null;
    }

    if (empty($CFG->enablerssfeeds)) {
        debugging('Sorry, RSS feeds are disabled on this site');
        return '';
    }

    if ($CFG->bloglevel == BLOG_SITE_LEVEL) {
        if (isguestuser()) {
            debugging(get_string('nopermissiontoshow', 'error'));
            return '';
        }
    }

    $sitecontext = context_system::instance();
    if (!has_capability('moodle/blog:view', $sitecontext)) {
        return null;
    }

    $type  = clean_param($args[3], PARAM_ALPHA);
    $id = clean_param($args[4], PARAM_INT);  // Could be groupid / courseid  / userid  depending on $type.

    $tagid = 0;
    if ($args[5] != 'rss.xml') {
        $tagid = clean_param($args[5], PARAM_INT);
    } else {
        $tagid = 0;
    }

    $filename = blog_rss_file_name($type, $id, $tagid);

    if (file_exists($filename)) {
        if (filemtime($filename) + 3600 > time()) {
            return $filename;   // It's already done so we return cached version.
        }
    }

    $courseid = $groupid = $userid = null;
    switch ($type) {
        case 'site':
            break;
        case 'course':
            $courseid = $id;
            break;
        case 'group':
            $groupid = $id;
            break;
        case 'user':
            $userid = $id;
            break;
    }

    // Get all the entries from the database.
    require_once($CFG->dirroot .'/blog/locallib.php');
    $blogheaders = blog_get_headers($courseid, $groupid, $userid, $tagid);

    $bloglisting = new blog_listing($blogheaders['filters']);
    $blogentries = $bloglisting->get_entries();

    // Now generate an array of RSS items.
    if ($blogentries) {
        $items = array();
        foreach ($blogentries as $blogentry) {
            $item = new stdClass();
            $item->author = fullname($DB->get_record('user', array('id' => $blogentry->userid))); // TODO: this is slow.
            $item->title = $blogentry->subject;
            $item->pubdate = $blogentry->lastmodified;
            $item->link = $CFG->wwwroot.'/blog/index.php?entryid='.$blogentry->id;
            $summary = file_rewrite_pluginfile_urls($blogentry->summary, 'pluginfile.php',
                $sitecontext->id, 'blog', 'post', $blogentry->id);
            $item->description = format_text($summary, $blogentry->format);
            if ($blogtags = core_tag_tag::get_item_tags_array('core', 'post', $blogentry->id)) {
                $item->tags = $blogtags;
                $item->tagscheme = $CFG->wwwroot . '/tag';
            }
            $items[] = $item;
        }
        $articles = rss_add_items($items);   // Change structure to XML.
    } else {
        $articles = '';
    }

    // Get header and footer information.

    switch ($type) {
        case 'user':
            $userfieldsapi = \core_user\fields::for_name();
            $info = fullname($DB->get_record('user', array('id' => $id),
                    $userfieldsapi->get_sql('', false, '', '', false)->selects));
            break;
        case 'course':
            $info = $DB->get_field('course', 'fullname', array('id' => $id));
            $info = format_string($info, true, array('context' => context_course::instance($id)));
            break;
        case 'site':
            $info = format_string($SITE->fullname, true, array('context' => context_course::instance(SITEID)));
            break;
        case 'group':
            $group = groups_get_group($id);
            $info = $group->name; // TODO: $DB->get_field('groups', 'name', array('id' => $id)).
            break;
        default:
            $info = '';
            break;
    }

    if ($tagid) {
        $info .= ': '.$DB->get_field('tags', 'text', array('id' => $tagid));
    }

    $header = rss_standard_header(get_string($type.'blog', 'blog', $info),
                                  $CFG->wwwroot.'/blog/index.php',
                                  get_string('intro', 'blog'));

    $footer = rss_standard_footer();

    // Save the XML contents to file.
    $rssdata = $header.$articles.$footer;
    if (blog_rss_save_file($type, $id, $tagid, $rssdata)) {
        return $filename;
    } else {
        return false;   // Couldn't find it or make it.
    }
}

/**
 * Retrieve the location and file name of a cached RSS feed
 *
 * @param string $type  The source of the RSS feed (site/course/group/user)
 * @param int    $id    The id of the item defined by $type
 * @param int    $tagid The id of the row in the tag table that identifies the RSS Feed
 * @return string
 */
function blog_rss_file_name($type, $id, $tagid = 0) {
    global $CFG;

    if ($tagid) {
        return "$CFG->cachedir/rss/blog/$type/$id/$tagid.xml";
    } else {
        return "$CFG->cachedir/rss/blog/$type/$id.xml";
    }
}

/**
 * This function saves to file the rss feed specified in the parameters
 *
 * @param string $type     The source of the RSS feed (site/course/group/user)
 * @param int    $id       The id of the item defined by $type
 * @param int    $tagid    The id of the row in the tag table that identifies the RSS Feed
 * @param string $contents The contents of the RSS Feed file
 * @return bool whether the save was successful or not
 */
function blog_rss_save_file($type, $id, $tagid = 0, $contents = '') {
    global $CFG;

    $status = true;

    // Blog creates some additional dirs within the rss cache so make sure they all exist.
    make_cache_directory('rss/blog');
    make_cache_directory('rss/blog/'.$type);

    $filename = blog_rss_file_name($type, $id, $tagid);
    $expandfilename = false; // We are supplying a full file path.
    $status = rss_save_file('blog', $filename, $contents, $expandfilename);

    return $status;
}

/**
 * Delete the supplied user's cached blog post RSS feed.
 * Only user blogs are available by RSS.
 * This doesn't call rss_delete_file() as blog RSS caching uses it's own file structure.
 *
 * @param int $userid
 */
function blog_rss_delete_file($userid) {
    $filename = blog_rss_file_name('user', $userid);
    if (file_exists($filename)) {
        unlink($filename);
    }
}

