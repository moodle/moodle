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
 * This file contains all the common stuff to be used in RSS System
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This function returns the icon (from theme) with the link to rss/file.php
 *
 * @global object
 * @global object
 */
function rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext='') {

    global $OUTPUT;

    static $rsspath = '';

    //In site course, if no logged (userid), use admin->id. Bug 2048.
    if ($courseid == SITEID and empty($userid)) {
        $admin = get_admin();
        $userid = $admin->id;
    }

    $rsspath = rss_get_url($courseid, $userid, $modulename, $id);
    $rsspix = $OUTPUT->pix_url('i/rss');

    return '<a href="'. $rsspath .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="'.get_string('rss').'" /></a>';

}

/**
 * This function returns the URL for the RSS XML file.
 *
 * @global object
 */
function rss_get_url($courseid, $userid, $modulename, $id) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');
    return get_file_url($courseid.'/'.$userid.'/'.$modulename.'/'.$id.'/rss.xml', null, 'rssfile');
}

/**
 * This function prints the icon (from theme) with the link to rss/file.php
 */
function rss_print_link($courseid, $userid, $modulename, $id, $tooltiptext='') {
    print rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext);

}

/**
 * This function iterates over each module in the server to see if
 * it supports generating rss feeds, searching for a MODULENAME_rss_feeds()
 * function and invoking it foreach activity as necessary
 *
 * @global object
 * @global object
 */
function cron_rss_feeds () {
    global $CFG, $DB;

    $status = true;

    mtrace('    Generating rssfeeds...');

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        mtrace('        ERROR: You need to add XML support to your PHP installation!');
        return true;
    }

    if ($allmods = $DB->get_records('modules') ) {
        foreach ($allmods as $mod) {
            mtrace('        '.$mod->name.': ', '');
            $modname = $mod->name;
            $modfile = "$CFG->dirroot/mod/$modname/rsslib.php";
            //If file exists and we have selected to restore that type of module
            if (file_exists($modfile)) {
                include_once($modfile);
                $generaterssfeeds = $modname.'_rss_feeds';
                if (function_exists($generaterssfeeds)) {
                    if ($status) {
                        mtrace('generating ', '');;
                        $status = $generaterssfeeds();
                        if (!empty($status)) {
                            mtrace('...OK');
                        } else {
                            mtrace('...FAILED');
                        }
                    } else {
                        mtrace('...SKIPPED (failed above)');
                    }
                } else {
                    mtrace('...NOT SUPPORTED (function)');
                }
            } else {
                mtrace('...NOT SUPPORTED (file)');
            }
        }
    }
    mtrace('    Ending  rssfeeds...', '');
    if (!empty($status)) {
        mtrace('...OK');
    } else {
        mtrace('...FAILED');
    }

    return $status;
}

/**
 * This function saves to file the rss feed specified in the parameters
 *
 * @global object
 */
function rss_save_file($modname, $mod, $result) {
    global $CFG;

    $status = true;

    if (! $basedir = make_upload_directory ('rss/'. $modname)) {
        //Cannot be created, so error
        $status = false;
    }

    if ($status) {
        $file = rss_file_name($modname, $mod);
        $rss_file = fopen($file, "w");
        if ($rss_file) {
            $status = fwrite ($rss_file, $result);
            fclose($rss_file);
        } else {
            $status = false;
        }
    }
    return $status;
}


function rss_file_name($modname, $mod) {
    global $CFG;

    return "$CFG->dataroot/rss/$modname/$mod->id.xml";
}

/**
 * This function return all the common headers for every rss feed in the site
 *
 * @global object
 * @global object
 */
function rss_standard_header($title = NULL, $link = NULL, $description = NULL) {
    global $CFG, $USER, $OUTPUT;

    $status = true;
    $result = "";

    $site = get_site();

    if ($status) {

        //Calculate title, link and description
        if (empty($title)) {
            $title = format_string($site->fullname);
        }
        if (empty($link)) {
            $link = $CFG->wwwroot;
        }
        if (empty($description)) {
            $description = $site->summary;
        }

        //xml headers
        $result .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $result .= "<rss version=\"2.0\">\n";

        //open the channel
        $result .= rss_start_tag('channel', 1, true);

        //write channel info
        $result .= rss_full_tag('title', 2, false, strip_tags($title));
        $result .= rss_full_tag('link', 2, false, $link);
        $result .= rss_full_tag('description', 2, false, $description);
        $result .= rss_full_tag('generator', 2, false, 'Moodle');
        if (!empty($USER->lang)) {
            $result .= rss_full_tag('language', 2, false, substr($USER->lang,0,2));
        }
        $today = getdate();
        $result .= rss_full_tag('copyright', 2, false, '&#169; '. $today['year'] .' '. format_string($site->fullname));
        /*
       if (!empty($USER->email)) {
            $result .= rss_full_tag('managingEditor', 2, false, fullname($USER));
            $result .= rss_full_tag('webMaster', 2, false, fullname($USER));
        }
       */

        //write image info
        $rsspix = $OUTPUT->pix_url('i/rsssitelogo');

        //write the info
        $result .= rss_start_tag('image', 2, true);
        $result .= rss_full_tag('url', 3, false, $rsspix);
        $result .= rss_full_tag('title', 3, false, 'moodle');
        $result .= rss_full_tag('link', 3, false, $CFG->wwwroot);
        $result .= rss_full_tag('width', 3, false, '140');
        $result .= rss_full_tag('height', 3, false, '35');
        $result .= rss_end_tag('image', 2, true);
    }

    if (!$status) {
        return false;
    } else {
        return $result;
    }
}

//This function returns the rss XML code for every item passed in the array
//item->title: The title of the item
//item->author: The author of the item. Optional !!
//item->pubdate: The pubdate of the item
//item->link: The link url of the item
//item->description: The content of the item
function rss_add_items($items) {
    global $CFG;

    $result = '';

    if (!empty($items)) {
        foreach ($items as $item) {
            $result .= rss_start_tag('item',2,true);
            //Include the category if exists (some rss readers will use it to group items)
            if (isset($item->category)) {
                $result .= rss_full_tag('category',3,false,$item->category);
            }
            if (isset($item->tags)) {
                $attributes = array();
                if (isset($item->tagscheme)) {
                    $attributes['domain'] = s($item->tagscheme);
                }
                foreach ($item->tags as $tag) {
                    $result .= rss_full_tag('category', 3, false, $tag, $attributes);
                }
            }
            $result .= rss_full_tag('title',3,false,strip_tags($item->title));
            $result .= rss_full_tag('link',3,false,$item->link);
            $result .= rss_add_enclosures($item);
            $result .= rss_full_tag('pubDate',3,false,gmdate('D, d M Y H:i:s',$item->pubdate).' GMT');  # MDL-12563
            //Include the author if exists
            if (isset($item->author)) {
                //$result .= rss_full_tag('author',3,false,$item->author);
                //We put it in the description instead because it's more important
                //for moodle than most other feeds, and most rss software seems to ignore
                //the author field ...
                $item->description = get_string('byname','',$item->author).'. &nbsp;<p>'.$item->description.'</p>';
            }
            $result .= rss_full_tag('description',3,false,$item->description);
            $result .= rss_full_tag('guid',3,false,$item->link,array('isPermaLink' => 'true'));
            $result .= rss_end_tag('item',2,true);

        }
    } else {
        $result = false;
    }
    return $result;
}

/**
 * This function return all the common footers for every rss feed in the site
 */
function rss_standard_footer($title = NULL, $link = NULL, $description = NULL) {
    $status = true;
    $result = '';

    //Close the chanel
    $result .= rss_end_tag('channel', 1, true);
    ////Close the rss tag
    $result .= '</rss>';

    return $result;
}

/**
 * This function return an error xml file (string)
 * to be sent when a rss is required (file.php)
 * and something goes wrong
 */
function rss_geterrorxmlfile() {
    global $CFG;

    $return = '';

    //XML Header
    $return = rss_standard_header();

    //XML item
    if ($return) {
        $item = new object();
        $item->title       = "RSS Error";
        $item->link        = $CFG->wwwroot;
        $item->pubdate     = time();
        $item->description = get_string("rsserror");
        $return .= rss_add_items(array($item));
    }

    //XML Footer
    if ($return) {
        $return .= rss_standard_footer();
    }

    return $return;
}

// ===== This function are used to write XML tags =========
// [stronk7]: They are similar to the glossary export and backup generation
// but I've replicated them here because they have some minor
// diferences. Someday all they should go to a common place.

/**
 * Return the xml start tag
 */
function rss_start_tag($tag,$level=0,$endline=false,$attributes=null) {
    if ($endline) {
       $endchar = "\n";
    } else {
       $endchar = "";
    }
    $attrstring = '';
    if (!empty($attributes) && is_array($attributes)) {
        foreach ($attributes as $key => $value) {
            $attrstring .= " ".$key."=\"".$value."\"";
        }
    }
    return str_repeat(" ",$level*2)."<".$tag.$attrstring.">".$endchar;
}

/**
 * Return the xml end tag
 */
function rss_end_tag($tag,$level=0,$endline=true) {
    if ($endline) {
       $endchar = "\n";
    } else {
       $endchar = "";
    }
    return str_repeat(" ",$level*2)."</".$tag.">".$endchar;
}

/**
 * Return the start tag, the contents and the end tag
 */
function rss_full_tag($tag,$level=0,$endline=true,$content,$attributes=null) {
    $st = rss_start_tag($tag,$level,$endline,$attributes);
    $co="";
    $co = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
    $et = rss_end_tag($tag,0,true);

    return $st.$co.$et;
}

/**
 * Adds RSS Media Enclosures for "podcasting" by examining links to media files,
 * and attachments which are media files. Please note that the RSS that is
 * produced cannot be strictly valid for the linked files, since we do not know
 * the files' sizes and cannot include them in the "length" attribute. At
 * present, the validity (and therefore the podcast working in most software)
 * can only be ensured for attachments, and not for links.
 * Note also that iTunes does some things very badly - one thing it does is
 * refuse to download ANY of your files if you're using "file.php?file=blah"
 * and can't use the more elegant "file.php/blah" slasharguments setting. It
 * stops after ".php" and assumes the files are not media files, despite what
 * is specified in the "type" attribute. Dodgy coding all round!
 *
 * Authors
 *     - Hannes Gassert <hannes@mediagonal.ch>
 *     - Dan Stowell
 *
 * @global object
 * @param    $item     object representing an RSS item
 * @return   string    RSS enclosure tags
 */
function rss_add_enclosures($item){
    global $CFG;

    $returnstring = '';
    $rss_text = $item->description;

    // list of media file extensions and their respective mime types
    include_once($CFG->libdir.'/filelib.php');
    $mediafiletypes = get_mimetypes_array();

    // regular expression (hopefully) matching all links to media files
    $medialinkpattern = '@href\s*=\s*(\'|")(\S+(' . implode('|', array_keys($mediafiletypes)) . '))\1@Usie';

    // take into account attachments (e.g. from forum) - with these, we are able to know the file size
    if (isset($item->attachments) && is_array($item->attachments)) {
        foreach ($item->attachments as $attachment){
            $extension = strtolower(substr($attachment->url, strrpos($attachment->url, '.')+1));
            if (isset($mediafiletypes[$extension]['type'])) {
                $type = $mediafiletypes[$extension]['type'];
            } else {
                $type = 'document/unknown';
            }
            $returnstring .= "\n<enclosure url=\"$attachment->url\" length=\"$attachment->length\" type=\"$type\" />\n";
        }
    }

    if (!preg_match_all($medialinkpattern, $rss_text, $matches)){
        return $returnstring;
    }

    // loop over matches of regular expression
    for ($i = 0; $i < count($matches[2]); $i++){
        $url = htmlspecialchars($matches[2][$i]);
        $extension = strtolower($matches[3][$i]);
        if (isset($mediafiletypes[$extension]['type'])) {
            $type = $mediafiletypes[$extension]['type'];
        } else {
            $type = 'document/unknown';
        }

        // the rss_*_tag functions can't deal with methods, unfortunately
        $returnstring .= "\n<enclosure url='$url' type='$type' />\n";
    }

    return $returnstring;
}
