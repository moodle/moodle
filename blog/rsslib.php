<?php

require_once($CFG->dirroot.'/lib/rsslib.php');
require_once($CFG->dirroot .'/blog/lib.php');

function blog_rss_get_url($contextid, $userid, $filtertype, $filterselect=0, $tagid=0) {
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

// This function returns the icon (from theme) with the link to rss/file.php
// needs some hacking to rss/file.php
function blog_rss_print_link($context, $filtertype, $filterselect=0, $tagid=0, $tooltiptext='') {
    global $CFG, $USER, $OUTPUT;

    if (!isloggedin()) {
        $userid = $CFG->siteguest;
    } else {
        $userid = $USER->id;
    }

    $url = blog_rss_get_url($context->id, $userid, $filtertype, $filterselect, $tagid);
    $rsspix = $OUTPUT->pix_url('i/rss');
    print '<div class="mdl-right"><a href="'. $url .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="'.get_string('rss').'" /></a></div>';
}

function blog_rss_add_http_header($context, $title, $filtertype, $filterselect=0, $tagid=0) {
    global $PAGE, $USER, $CFG;

    //$componentname = 'blog';
    //rss_add_http_header($context, $componentname, $filterselect, $title);

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
 * @param <type> $filters
 * @return array array containing the id of the user/course/group, the relevant context and the filter type (site/user/course/group)
 */
function blog_rss_get_params($filters) {
    $thingid = $rsscontext = $filtertype = null;

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    if (!$filters) {
        $thingid = SITEID;
        $rsscontext = $sitecontext;
        $filtertype = 'site';
    } else if (array_key_exists('course', $filters)) {
        $thingid = $filters['course'];

        $coursecontext = get_context_instance(CONTEXT_COURSE, $thingid);
        $rsscontext = $coursecontext;

        $filtertype = 'course';
    } else if (array_key_exists('user', $filters)) {
        $thingid = $filters['user'];

        $usercontext = get_context_instance(CONTEXT_USER, $thingid);
        $rsscontext = $usercontext;

        $filtertype = 'user';
    } else if (array_key_exists('group', $filters)) {
        $thingid = $filters['group'];

        $rsscontext = $sitecontext; //is this the context we should be using for group blogs?
        $filtertype = 'group';
    }

    return array($thingid, $rsscontext, $filtertype);
}


// Generate any blog RSS feed via one function (called by ../rss/file.php)
function blog_rss_get_feed($context, $args) {
    global $CFG, $SITE, $DB;

    if (empty($CFG->enablerssfeeds)) {
        debugging('Sorry, RSS feeds are disabled on this site');
        return '';
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    if (!has_capability('moodle/blog:view', $sitecontext)) {
        return null;
    }

    $type  = clean_param($args[3], PARAM_ALPHA);
    $id = clean_param($args[4], PARAM_INT);  // could be groupid / courseid  / userid  depending on $type

    $tagid=0;
    if ($args[5] != 'rss.xml') {
        $tagid = clean_param($args[5], PARAM_INT);
    } else {
        $tagid = 0;
    }

    $filename = blog_rss_file_name($type, $id, $tagid);

    if (file_exists($filename)) {
        if (filemtime($filename) + 3600 > time()) {
            return $filename;   // It's already done so we return cached version
        }
    }

    $courseid = $groupid = $userid = null;
    switch ($type) {
        case 'site':
            //$siteid = $id;
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

    // Get all the entries from the database
    require_once($CFG->dirroot .'/blog/locallib.php');
    $blogheaders = blog_get_headers($courseid, $groupid, $userid, $tagid);

    $bloglisting = new blog_listing($blogheaders['filters']);
    $blogentries = $bloglisting->get_entries();

    // Now generate an array of RSS items
    if ($blogentries) {
        $items = array();
        foreach ($blogentries as $blog_entry) {
            $item = NULL;
            $item->author = fullname($DB->get_record('user', array('id'=>$blog_entry->userid))); // TODO: this is slow
            $item->title = $blog_entry->subject;
            $item->pubdate = $blog_entry->lastmodified;
            $item->link = $CFG->wwwroot.'/blog/index.php?entryid='.$blog_entry->id;
            $summary = file_rewrite_pluginfile_urls($blog_entry->summary, 'pluginfile.php',
                $sitecontext->id, 'blog', 'post', $blog_entry->id);
            $item->description = format_text($summary, $blog_entry->format);
            if ( !empty($CFG->usetags) && ($blogtags = tag_get_tags_array('post', $blog_entry->id)) ) {
                if ($blogtags) {
                    $item->tags = $blogtags;
                }
                $item->tagscheme = $CFG->wwwroot . '/tag';
            }
            $items[] = $item;
        }
        $articles = rss_add_items($items);   /// Change structure to XML
    } else {
        $articles = '';
    }

/// Get header and footer information

    switch ($type) {
        case 'user':
            $info = fullname($DB->get_record('user', array('id'=>$id), 'firstname,lastname'));
            break;
        case 'course':
            $info = $DB->get_field('course', 'fullname', array('id'=>$id));
            $info = format_string($info, true, array('context' => get_context_instance(CONTEXT_COURSE, $id)));
            break;
        case 'site':
            $info = format_string($SITE->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, SITEID)));
            break;
        case 'group':
            $group = groups_get_group($id);
            $info = $group->name; //TODO: $DB->get_field('groups', 'name', array('id'=>$id))
            break;
        default:
            $info = '';
            break;
    }

    if ($tagid) {
        $info .= ': '.$DB->get_field('tags', 'text', array('id'=>$tagid));
    }

    $header = rss_standard_header(get_string($type.'blog','blog', $info),
                                  $CFG->wwwroot.'/blog/index.php',
                                  get_string('intro','blog'));

    $footer = rss_standard_footer();

    // Save the XML contents to file.
    $rssdata = $header.$articles.$footer;
    if (blog_rss_save_file($type,$id,$tagid,$rssdata)) {
        return $filename;
    } else {
        return false;   // Couldn't find it or make it
    }
}


function blog_rss_file_name($type, $id, $tagid=0) {
    global $CFG;

    if ($tagid) {
        return "$CFG->dataroot/cache/rss/blog/$type/$id/$tagid.xml";
    } else {
        return "$CFG->dataroot/cache/rss/blog/$type/$id.xml";
    }
}

//This function saves to file the rss feed specified in the parameters
function blog_rss_save_file($type, $id, $tagid=0, $contents='') {
    global $CFG;

    $status = true;

    //blog creates some additional dirs within the rss cache so make sure they all exist
    make_upload_directory('cache/rss/blog');
    make_upload_directory('cache/rss/blog/'.$type);

    $filename = blog_rss_file_name($type, $id, $tagid);
    $expandfilename = false; //we're supplying a full file path
    $status = rss_save_file('blog', $filename, $contents, $expandfilename);

    return $status;
}

