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
 * @package    core_rss
 * @category   rss
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Build the URL for the RSS feed and add it as a header
 *
 * @param stdClass    $context           The context under which the URL should be created
 * @param string      $componentname     The name of the component for which the RSS feed exists
 * @param stdClass    $componentinstance The instance of the component
 * @param string      $title             Name for the link to be added to the page header
 */
function rss_add_http_header($context, $componentname, $componentinstance, $title) {
    global $PAGE, $USER;

    $componentid = null;
    if (is_object($componentinstance)) {
        $componentid = $componentinstance->id;
    } else {
        $componentid = $componentinstance;
    }

    $rsspath = rss_get_url($context->id, $USER->id, $componentname, $componentid);
    $PAGE->add_alternate_version($title, $rsspath, 'application/rss+xml');
 }

/**
 * Print the link for the RSS feed with the correct RSS icon
 *
 * @param stdClass    $contextid     The id of the context under which the URL should be created
 * @param int         $userid        The source of the RSS feed (site/course/group/user)
 * @param string      $componentname The name of the component for which the feed exists
 * @param string      $id            The name by which to call the RSS File
 * @param string      $tooltiptext   The tooltip to be displayed with the link
 * @return string HTML output for the RSS link
 */
function rss_get_link($contextid, $userid, $componentname, $id, $tooltiptext='') {
    global $OUTPUT;

    static $rsspath = '';

    $rsspath = rss_get_url($contextid, $userid, $componentname, $id);
    $rsspix = $OUTPUT->pix_url('i/rss');

    return '<a href="'. $rsspath .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="'.get_string('rss').'" /></a>';
}

/**
 * This function returns the URL for the RSS XML file.
 *
 * @param int    $contextid      the course id
 * @param int    $userid         the current user id
 * @param string $componentname  the name of the current component. For example "forum"
 * @param string $additionalargs For modules, module instance id
 * @return string the url of the RSS feed
 */
function rss_get_url($contextid, $userid, $componentname, $additionalargs) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');
    $usertoken = rss_get_token($userid);
    return get_file_url($contextid.'/'.$usertoken.'/'.$componentname.'/'.$additionalargs.'/rss.xml', null, 'rssfile');
}

/**
 * Print the link for the RSS feed with the correct RSS icon (Theme based)
 *
 * @param stdClass    $contextid     The id of the context under which the URL should be created
 * @param int         $userid        The source of the RSS feed (site/course/group/user)
 * @param string      $componentname The name of the component for which the feed exists
 * @param string      $id            The name by which to call the RSS File
 * @param string      $tooltiptext   The tooltip to be displayed with the link
 */
function rss_print_link($contextid, $userid, $componentname, $id, $tooltiptext='') {
    print rss_get_link($contextid, $userid, $componentname, $id, $tooltiptext);

}

/**
 * Given an object, deletes all RSS files associated with it.
 *
 * @param string   $componentname the name of the module ie 'forum'. Used to construct the cache path.
 * @param stdClass $instance      An object with an id member variable ie $forum, $glossary.
 */
function rss_delete_file($componentname, $instance) {
    global $CFG;

    $dirpath = "$CFG->cachedir/rss/$componentname";
    if (is_dir($dirpath)) {
        if (!$dh = opendir($dirpath)) {
            error_log("Directory permission error. RSS directory store for component '{$componentname}' exists but cannot be opened.", DEBUG_DEVELOPER);
            return;
        }
        while (false !== ($filename = readdir($dh))) {
            if ($filename!='.' && $filename!='..') {
                if (preg_match("/{$instance->id}_/", $filename)) {
                    unlink("$dirpath/$filename");
                }
            }
        }
    }
}

/**
 * Are RSS feeds enabled for the supplied module instance?
 *
 * @param string   $modname        The name of the module to be checked
 * @param stdClass $instance       An instance of an activity module ie $forum, $glossary.
 * @param bool     $hasrsstype     Should there be a rsstype member variable?
 * @param bool     $hasrssarticles Should there be a rssarticles member variable?
 * @return bool whether or not RSS is enabled for the module
 */
function rss_enabled_for_mod($modname, $instance=null, $hasrsstype=true, $hasrssarticles=true) {
    if ($hasrsstype) {
        if (empty($instance->rsstype) || $instance->rsstype==0) {
            return false;
        }
    }

    //have they set the RSS feed to return 0 results?
    if ($hasrssarticles) {
        if (empty($instance->rssarticles) || $instance->rssarticles==0) {
            return false;
        }
    }

    if (!empty($instance) && !instance_is_visible($modname,$instance)) {
        return false;
    }

    return true;
}

/**
 * This function saves to file the rss feed specified in the parameters
 *
 * @param string $componentname  the module name ie forum. Used to create a cache directory.
 * @param string $filename       the name of the file to be created ie "rss.xml"
 * @param string $contents       the data to be written to the file
 * @param bool   $expandfilename whether or not the fullname of the RSS file should be used
 * @return bool whether the save was successful or not
 */
function rss_save_file($componentname, $filename, $contents, $expandfilename=true) {
    global $CFG;

    $status = true;

    if (! $basedir = make_cache_directory ('rss/'. $componentname)) {
        //Cannot be created, so error
        $status = false;
    }

    if ($status) {
        $fullfilename = $filename;
        if ($expandfilename) {
            $fullfilename = rss_get_file_full_name($componentname, $filename);
        }

        $rss_file = fopen($fullfilename, "w");
        if ($rss_file) {
            $status = fwrite ($rss_file, $contents);
            fclose($rss_file);
        } else {
            $status = false;
        }
    }
    return $status;
}

/**
 * Retrieve the location and file name of a cached RSS feed
 *
 * @param string $componentname the name of the component the RSS feed is being created for
 * @param string $filename the name of the RSS FEED
 * @return string The full name and path of the RSS file
 */
function rss_get_file_full_name($componentname, $filename) {
    global $CFG;
    return "$CFG->cachedir/rss/$componentname/$filename.xml";
}

/**
 * Construct the file name of the RSS File
 *
 * @param stdClass $instance the instance of the source of the RSS feed
 * @param string $sql the SQL used to produce the RSS feed
 * @param array $params the parameters used in the SQL query
 * @return string the name of the RSS file
 */
function rss_get_file_name($instance, $sql, $params = array()) {
    if ($params) {
        // If a parameters array is passed, then we want to
        // serialize it and then concatenate it with the sql.
        // The reason for this is to generate a unique filename
        // for queries using the same sql but different parameters.
        asort($parms);
        $serializearray = serialize($params);
        return $instance->id.'_'.md5($sql . $serializearray);
    } else {
        return $instance->id.'_'.md5($sql);
    }
}

/**
 * This function return all the common headers for every rss feed in the site
 *
 * @param string $title       the title for the RSS Feed
 * @param string $link        the link for the origin of the RSS feed
 * @param string $description the description of the contents of the RSS feed
 * @return bool|string the standard header for the RSS feed
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
        $result .= rss_full_tag('copyright', 2, false, '(c) '. $today['year'] .' '. format_string($site->fullname));
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


/**
 * Generates the rss XML code for every item passed in the array
 *
 * item->title: The title of the item
 * item->author: The author of the item. Optional !!
 * item->pubdate: The pubdate of the item
 * item->link: The link url of the item
 * item->description: The content of the item
 *
 * @param array $items an array of item objects
 * @return bool|string the rss XML code for every item passed in the array
 */
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
            if (isset($item->author) && !empty($item->author)) {
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
 *
 * @param string $title       Not used at all
 * @param string $link        Not used at all
 * @param string $description Not used at all
 * @todo  MDL-31050 Fix/Remove this function
 * @return string
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
 * This function return an error xml file (string) to be sent when a rss is required (file.php) and something goes wrong
 *
 * @param string $errortype Type of error to send, default is rsserror
 * @return stdClass returns a XML Feed with an error message in it
 */
function rss_geterrorxmlfile($errortype = 'rsserror') {
    global $CFG;

    $return = '';

    //XML Header
    $return = rss_standard_header();

    //XML item
    if ($return) {
        $item = new stdClass();
        $item->title       = "RSS Error";
        $item->link        = $CFG->wwwroot;
        $item->pubdate     = time();
        $item->description = get_string($errortype);
        $return .= rss_add_items(array($item));
    }

    //XML Footer
    if ($return) {
        $return .= rss_standard_footer();
    }

    return $return;
}

/**
 * Get the ID of the user from a given RSS Token
 *
 * @param string $token the RSS token you would like to use to find the user id
 * @return int The user id
 */
function rss_get_userid_from_token($token) {
    global $DB;

    $sql = 'SELECT u.id FROM {user} u
            JOIN {user_private_key} k ON u.id = k.userid
            WHERE u.deleted = 0 AND u.confirmed = 1
            AND u.suspended = 0 AND k.value = ?';
    return $DB->get_field_sql($sql, array($token), IGNORE_MISSING);
}

/**
 * Get the RSS Token from a given user id
 *
 * @param int $userid The user id
 * @return string the RSS token for the user
 */
function rss_get_token($userid) {
    return get_user_key('rss', $userid);
}

/**
 * Removes the token for the given user from the DB
 * @param int $userid The user id for the token you wish to delete
 */
function rss_delete_token($userid) {
    delete_user_key('rss', $userid);
}

/**
 * Return the xml start tag
 *
 * @param string $tag        the xml tag name
 * @param int    $level      the indentation level
 * @param bool   $endline    whether or not to start new tags on a new line
 * @param array  $attributes the attributes of the xml tag
 * @return string the xml start tag
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
 * @param string $tag        the xml tag name
 * @param int    $level      the indentation level
 * @param bool   $endline    whether or not to start new tags on a new line
 * @return string the xml end tag
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
 * Return the while xml element, including content
 *
 * @param string $tag        the xml tag name
 * @param int    $level      the indentation level
 * @param bool   $endline    whether or not to start new tags on a new line
 * @param string $content    the text to go inside the tag
 * @param array  $attributes the attributes of the xml tag
 * @return string the whole xml element
 */
function rss_full_tag($tag,$level=0,$endline=true,$content,$attributes=null) {
    $st = rss_start_tag($tag,$level,$endline,$attributes);
    $co="";
    $co = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
    $et = rss_end_tag($tag,0,true);

    return $st.$co.$et;
}

/**
 * Adds RSS Media Enclosures for "podcasting" by including attachments that
 * are specified in the item->attachments field.
 *
 * @param stdClass $item representing an RSS item
 * @return string RSS enclosure tags
 */
function rss_add_enclosures($item){
    global $CFG;

    $returnstring = '';

    // list of media file extensions and their respective mime types
    include_once($CFG->libdir.'/filelib.php');
    $mediafiletypes = get_mimetypes_array();

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

    return $returnstring;
}
