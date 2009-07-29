<?php  // $Id$
       // This file contains all the common stuff to be used in RSS System

//This function returns the icon (from theme) with the link to rss/file.php
function rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext='') {

    global $CFG, $USER;

    static $pixpath = '';
    static $rsspath = '';

    //In site course, if no logged (userid), use admin->id. Bug 2048.
    if ($courseid == SITEID and empty($userid)) {
        $admin = get_admin();
        $userid = $admin->id;
    }

    $rsspath = rss_get_url($courseid, $userid, $modulename, $id);
    $rsspix = $CFG->pixpath .'/i/rss.gif';

    return '<a href="'. $rsspath .'"><img src="'. $rsspix .'" title="'. strip_tags($tooltiptext) .'" alt="'.get_string('rss').'" /></a>';

}

//This function returns the URL for the RSS XML file.
function rss_get_url($courseid, $userid, $modulename, $id) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');
    return get_file_url($courseid.'/'.$userid.'/'.$modulename.'/'.$id.'/rss.xml', null, 'rssfile');
}

//This function prints the icon (from theme) with the link to rss/file.php
function rss_print_link($courseid, $userid, $modulename, $id, $tooltiptext='') {

    print rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext);

}
//This function iterates over each module in the server to see if
//it supports generating rss feeds, searching for a MODULENAME_rss_feeds()
//function and invoking it foreach activity as necessary
function cron_rss_feeds () {

    global $CFG;

    $status = true;

    mtrace('    Generating rssfeeds...');

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        mtrace('        ERROR: You need to add XML support to your PHP installation!');
        return true;
    }

    if ($allmods = get_records('modules') ) {
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

//This function saves to file the rss feed specified in the parameters
function rss_save_file ($modname, $mod, $result) {

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

//This function return all the common headers for every rss feed in the site
function rss_standard_header($title = NULL, $link = NULL, $description = NULL) {

    global $CFG, $USER;

    static $pixpath = '';

    $status = true;
    $result = "";

    if (!$site = get_site()) {
        $status = false;
    }

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
        $rsspix = $CFG->pixpath."/i/rsssitelogo.gif";

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
                    $attributes['domain'] = $item->tagscheme;
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

//This function return all the common footers for every rss feed in the site
function rss_standard_footer($title = NULL, $link = NULL, $description = NULL) {

    global $CFG, $USER;

    $status = true;
    $result = '';

    //Close the chanel
    $result .= rss_end_tag('channel', 1, true);
    ////Close the rss tag
    $result .= '</rss>';

    return $result;
}

//This function return an error xml file (string)
//to be sent when a rss is required (file.php)
//and something goes wrong
function rss_geterrorxmlfile() {

    global $CFG;

    $return = '';

    //XML Header
    $return = rss_standard_header();

    //XML item
    if ($return) {
        $item = new object();
        $item->title = "RSS Error";
        $item->link = $CFG->wwwroot;
        $item->pubdate = time();
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

//Return the xml start tag
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

//Return the xml end tag
function rss_end_tag($tag,$level=0,$endline=true) {
    if ($endline) {
       $endchar = "\n";
    } else {
       $endchar = "";
    }
    return str_repeat(" ",$level*2)."</".$tag.">".$endchar;
}

//Return the start tag, the contents and the end tag
function rss_full_tag($tag,$level=0,$endline=true,$content,$attributes=null) {
    global $CFG;
    $st = rss_start_tag($tag,$level,$endline,$attributes);
    $co="";
    $co = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
    $et = rss_end_tag($tag,0,true);

    return $st.$co.$et;
}

//////////////////// LIBRARY FUNCTIONS FOR RSS_CLIENT BLOCK ////////////////

//initialize default config vars for rss_client block if needed
if (!isset($CFG->block_rss_client_submitters) ) {
    $CFG->block_rss_client_submitters = 1; //default to admin only
}
if (empty($CFG->block_rss_client_num_entries) ) {
    $CFG->block_rss_client_num_entries = 5; //default to 5 entries per block
}
if (!isset($CFG->block_rss_client_timeout) ) {
    $CFG->block_rss_client_timeout = 30; //default to 30 mins
}

// Defines for moodle's use of magpierss classes
define('MAGPIE_DIR', $CFG->libdir.'/magpie/');
define('MAGPIE_CACHE_DIR', $CFG->dataroot .'/cache/rsscache');
define('MAGPIE_CACHE_ON', true); //might want to expose as an admin config option, but perhaps this is something that should truly just be on unless the code is tweaked
define('MAGPIE_CACHE_FRESH_ONLY', false); //should be exposed as an admin config option
define('MAGPIE_CACHE_AGE', $CFG->block_rss_client_timeout * 60);
define('MAGPIE_DEBUG', $CFG->debug); // magpie, like moodle, takes an integer debug

// defines for config var block_rss_client_submitters
define('SUBMITTERS_ALL_ACCOUNT_HOLDERS', 0);
define('SUBMITTERS_ADMIN_ONLY', 1);
define('SUBMITTERS_ADMIN_AND_TEACHER', 2);

/**
 * @param int $courseid The id of the course the user is currently viewing
 * @param int $userid We need this to know which feeds the user is allowed to manage
 * @param int $rssid If present the rss entry matching this id alone will be displayed
 *            as long as the user is allowed to manage this feed
 * @param object $context we need the context object to check what the user is allowed to do.
 */
function rss_display_feeds($courseid, $userid, $rssid='', $context) {
    global $db, $USER, $CFG;
    global $blogid; //hackish, but if there is a blogid it would be good to preserve it

    require_once($CFG->libdir.'/tablelib.php');

    $select = '';
    $managesharedfeeds = has_capability('block/rss_client:manageanyfeeds', $context);
    $manageownfeeds = has_capability('block/rss_client:manageownfeeds', $context);

    if ($rssid != '') {
        $select = 'id = '.$rssid.' AND ';
    }
    if ($managesharedfeeds) {
        $select .= '(userid = '.$userid.' OR shared = 1)';
    } else if ($manageownfeeds) {
        $select .= 'userid = '.$userid;
    }

    $table = new flexible_table('rss-display-feeds');

    $table->define_columns(array('feed', 'actions'));
    $table->define_headers(array(get_string('feed', 'block_rss_client'), get_string('actions', 'moodle')));

    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'rssfeeds');
    $table->set_attribute('class', 'generaltable generalbox');
    $table->column_class('feed', 'feed');
    $table->column_class('actions', 'actions');

    $table->setup();

    $feeds = get_records_select('block_rss_client', $select, sql_order_by_text('title'));

    if(!empty($feeds)) {
        foreach($feeds as $feed) {

            if (!empty($feed->preferredtitle)) {
                $feedtitle = stripslashes_safe($feed->preferredtitle);
            } else {
                $feedtitle =  stripslashes_safe($feed->title);
            }

            if ( ($feed->userid == $USER->id && $manageownfeeds)
                    || ($feed->shared && $managesharedfeeds) ) {

                $feedicons = '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?id='. $courseid .'&amp;act=rssedit&amp;rssid='. $feed->id .'&amp;shared='.$feed->shared.'&amp;blogid='. $blogid .'">'.
                             '<img src="'. $CFG->pixpath .'/t/edit.gif" alt="'. get_string('edit').'" title="'. get_string('edit') .'" /></a>&nbsp;'.

                             '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?id='. $courseid .'&amp;act=delfeed&amp;rssid='. $feed->id.'&amp;shared='.$feed->shared.'blogid='. $blogid .'" 
                onclick="return confirm(\''. get_string('deletefeedconfirm', 'block_rss_client') .'\');">'.
                             '<img src="'. $CFG->pixpath .'/t/delete.gif" alt="'. get_string('delete').'" title="'. get_string('delete') .'" /></a>';
            }
            else {
                $feedicons = '';
            }

            $feedinfo = '
    <div class="title">
        <a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?id='. $courseid .'&amp;act=view&amp;rssid='.$feed->id .'&amp;blogid='. $blogid .'">
        '. $feedtitle .'</a>
    </div>
    <div class="url">
        <a href="'. $feed->url .'">'. $feed->url .'</a>
    </div>
    <div class="description">'.$feed->description.'</div>';

            $table->add_data(array($feedinfo, $feedicons));
        }
    }

    $table->print_html();
}


/**
 * Wrapper function for rss_get_form
 */
function rss_print_form($act='none', $url='', $rssid='', $preferredtitle='', $shared=0, $courseid='', $context) {
    print rss_get_form($act, $url, $rssid, $preferredtitle, $shared, $courseid, $context);
}


/**
 * Prints or returns a form for managing rss feed entries.
 * @param string $act The current action. If "rssedit" then and "update" button is used, otherwise "add" is used.
 * @param string $url The url of the feed that is being updated or NULL
 * @param int $rssid The dataabse id of the feed that is being updated or NULL
 * @param string $preferredtitle The preferred title to display for this feed
 * @param int $shared Whether this feed is to be shared or not
 * @param int $courseid The id of the course that is currently being viewed if applicable
 * @param object $context The context that we will use to check for permissions
 * @return string Either the form is printed directly and nothing is returned or the form is returned as a string
 */
function rss_get_form($act='none', $url='', $rssid='', $preferredtitle='', $shared=0, $courseid='', $context) {
    global $USER, $CFG, $_SERVER, $blockid, $blockaction;
    global $blogid; //hackish, but if there is a blogid it would be good to preserve it
    $stredit = get_string('edit');
    $stradd = get_string('add');
    $strupdatefeed = get_string('updatefeed', 'block_rss_client');
    $straddfeed = get_string('addfeed', 'block_rss_client');

    $returnstring = '';

    $returnstring .= '<form action="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php" method="post" id="block_rss">'."\n";
    $returnstring .= '<div id="rss_table">'."\n";
    if ($act == 'rssedit') {
        $returnstring .= $strupdatefeed;
    } else {
        $returnstring .= $straddfeed;
    }

    $returnstring .= "\n".'<br /><input type="text" size="60" maxlength="256" name="url" value="';
    if ($act == 'rssedit') {
        $returnstring .= $url;
    }

    $returnstring .= '" />'."\n";
    $returnstring .= '<br />'. get_string('customtitlelabel', 'block_rss_client');
    $returnstring .= '<br /><input type="text" size="60" maxlength="128" name="preferredtitle" value="';

    if ($act == 'rssedit') {
        $returnstring .= $preferredtitle;
    }

    $returnstring .= '" />'."\n";

    if (has_capability('block/rss_client:createsharedfeeds', $context)) {
        $returnstring .= '<br /><input type="checkbox" name="shared" value="1" ';
        if ($shared) {
            $returnstring .= 'checked="checked" ';
        }
        $returnstring .= '/> ';
        $returnstring .= get_string('sharedfeed', 'block_rss_client');
        $returnstring .= '<br />'."\n";
    }

    $returnstring .= '<input type="hidden" name="act" value="';

    if ($act == 'rssedit') {
        $returnstring .= 'updfeed';
    } else {
        $returnstring .= 'addfeed';
    }

    $returnstring .= '" />'."\n";
    if ($act == 'rssedit') {
        $returnstring .= '<input type="hidden" name="rssid" value="'. $rssid .'" />'. "\n";
    }

    $returnstring .= '<input type="hidden" name="id" value="'. $courseid .'" />'."\n";
    $returnstring .= '<input type="hidden" name="blogid" value="'. $blogid .'" />'."\n";
    $returnstring .= '<input type="hidden" name="user" value="'. $USER->id .'" />'."\n";
    $returnstring .= '<br /><input type="submit" value="';
    $validatestring = "<a href=\"#\" onclick=\"window.open('http://feedvalidator.org/check.cgi?url='+getElementById('block_rss').elements['url'].value,'validate','width=640,height=480,scrollbars=yes,status=yes,resizable=yes');return true;\">". get_string('validatefeed', 'block_rss_client')."</a>";

    if ($act == 'rssedit') {
        $returnstring .= $stredit;
    } else {
        $returnstring .= $stradd;
    }

    $returnstring .= '" />&nbsp;'. $validatestring ."\n";
    $returnstring .= '</div></form>'."\n";
    
    return $returnstring;
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
* @param    $item     object representing an RSS item
* @return   string    RSS enclosure tags
* @author   Hannes Gassert <hannes@mediagonal.ch>
* @author   Dan Stowell
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
?>
