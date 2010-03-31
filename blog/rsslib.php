<?php

require_once($CFG->dirroot.'/lib/rsslib.php');
require_once($CFG->dirroot .'/blog/lib.php');

// This function returns the icon (from theme) with the link to rss/file.php
// needs some hacking to rss/file.php
function blog_rss_print_link($filtertype, $filterselect, $tagid=0, $tooltiptext='') {

    global $CFG, $USER, $OUTPUT;

    if (!isloggedin()) {
        $userid = $CFG->siteguest;
    } else {
        $userid = $USER->id;
    }

    switch ($filtertype) {
        case 'site':
            $path = SITEID.'/'.$userid.'/blog/site/'.SITEID;
        break;
        case 'course':
            $path = $filterselect.'/'.$userid.'/blog/course/'.$filterselect;
        break;
        case 'group':
            $path = SITEID.'/'.$userid.'/blog/group/'.$filterselect;
        break;
        case 'user':
            $path = SITEID.'/'.$userid.'/blog/user/'.$filterselect;
        break;
    }

    if ($tagid) {
        $path .= '/'.$tagid;
    }

    $path .= '/rss.xml';
    $rsspix = $OUTPUT->pix_url('i/rss');

    require_once($CFG->libdir.'/filelib.php');
    $path = get_file_url($path, null, 'rssfile');
    print '<div class="mdl-right"><a href="'. $path .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="'.get_string('rss').'" /></a></div>';

}


// Generate any blog RSS feed via one function (called by ../rss/file.php)
function blog_generate_rss_feed($type, $id, $tagid=0) {
    global $CFG, $SITE, $DB;

    if (empty($CFG->enablerssfeeds)) {
        debugging('Sorry, RSS feeds are disabled on this site');
        return '';
    }

    $filename = blog_rss_file_name($type, $id, $tagid);

    if (file_exists($filename)) {
        if (filemtime($filename) + 3600 > time()) {
            return $filename;   /// It's already done so we return cached version
        }
    }

/// Get all the entries from the database

    $blogentries = blog_fetch_entries('', 20, '', $type, $id, $tagid);

/// Now generate an array of RSS items
    if ($blogentries) {
        $items = array();
        foreach ($blogentries as $blog_entry) {
            $item = NULL;
            $item->author = fullname($DB->get_record('user', array('id'=>$blog_entry->userid))); // TODO: this is slow
            $item->title = $blog_entry->subject;
            $item->pubdate = $blog_entry->lastmodified;
            $item->link = $CFG->wwwroot.'/blog/index.php?entryid='.$blog_entry->id;
            $item->description = format_text($blog_entry->summary, $blog_entry->format);
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
            break;
        case 'site':
            $info = $SITE->fullname;
            break;
        case 'group':
            $group = groups_get_group($id, false);
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


/// Save the XML contents to file.

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
        return "$CFG->dataroot/rss/blog/$type/$id/$tagid.xml";
    } else {
        return "$CFG->dataroot/rss/blog/$type/$id.xml";
    }
}

//This function saves to file the rss feed specified in the parameters
function blog_rss_save_file($type, $id, $tagid=0, $contents='') {
    global $CFG;

    if (! $basedir = make_upload_directory("rss/blog/$type/$id")) {
        return false;
    }

    $file = blog_rss_file_name($type, $id, $tagid);
    $rss_file = fopen($file, 'w');
    if ($rss_file) {
        $status = fwrite ($rss_file, $contents);
        fclose($rss_file);
    } else {
        $status = false;
    }

    return $status;
}

