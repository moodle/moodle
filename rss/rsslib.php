<?php  // $Id$
       // This file contains all the common stuff to be used in RSS System

//This function returns the icon (from theme) with the link to rss/file.php
function rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext="") {

 global $CFG, $THEME, $USER;

    static $pixpath = '';
    static $rsspath = '';

    //In site course, if no logged (userid), use admin->id. Bug 2048.
    if ($courseid == SITEID and empty($userid)) {
        $admin = get_admin();
        $userid = $admin->id;
    }

    if ($CFG->slasharguments) {
        $rsspath = "$CFG->wwwroot/rss/file.php/$courseid/$userid/$modulename/$id/rss.xml";
    } else {
        $rsspath = "$CFG->wwwroot/rss/file.php?file=/$courseid/$userid/$modulename/$id/rss.xml";
    }

    if (empty($pixpath)) {
        if (empty($THEME->custompix)) {
            $pixpath = "$CFG->wwwroot/pix";
        } else {
            $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        }
    }

    $rsspix = $pixpath."/i/rss.gif";

    return "<a href=\"".$rsspath."\"><img src=\"$rsspix\" title=\"$tooltiptext\" alt=\"\" /></a>";

}

//This function prints the icon (from theme) with the link to rss/file.php
function rss_print_link($courseid, $userid, $modulename, $id, $tooltiptext="") {

    echo rss_get_link($courseid, $userid, $modulename, $id, $tooltiptext);

}
//This function iterates over each module in the server to see if
//it supports generating rss feeds, searching for a MODULENAME_rss_feeds()
//function and invoking it foreach activity as necessary
function cron_rss_feeds () {

    global $CFG;

    $status = true;
   
    mtrace("    Generating rssfeeds...");

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        mtrace("        ERROR: You need to add XML support to your PHP installation!");
        return true;
    }

    if ($allmods = get_records("modules") ) {
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
                            mtrace("...OK");
                        } else {
                            mtrace("...FAILED");
                        }
                    } else {
                        mtrace("...SKIPPED (failed above)");
                    }
                } else {
                    mtrace("...NOT SUPPORTED (function)");
                }
            } else {
                mtrace("...NOT SUPPORTED (file)");
            }
        }
    }
    mtrace("    Ending  rssfeeds...", '');
    if (!empty($status)) {
        mtrace("...OK");
    } else {
        mtrace("...FAILED");
    }

    return $status;
}

//This function saves to file the rss feed specified in the parameters
function rss_save_file ($modname,$mod,$result) {
 
    global $CFG;
    
    $status = true;

    if (! $basedir = make_upload_directory ("rss/".$modname)) {
        //Cannot be created, so error
        $status = false;
    }

    if ($status) {
        $file = rss_file_name($modname, $mod);
        $rss_file = fopen($file,"w");
        if ($rss_file) {
            $status = fwrite ($rss_file,$result);
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

    global $CFG, $THEME, $USER;

    static $pixpath = '';

    $status = true;
    $result = "";

    if (!$site = get_site()) {
        $status = false;
    }

    if ($status) {

        //Calculate title, link and description
        if (empty($title)) {
            $title = $site->fullname;
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
        $result .= rss_start_tag("channel",1,true);

        //write channel info
        $result .= rss_full_tag("title",2,false,$title);
        $result .= rss_full_tag("link",2,false,$link);
        $result .= rss_full_tag("description",2,false,$description);
        if (!empty($USER->lang)) {
            $result .= rss_full_tag("language",2,false,substr($USER->lang,0,2));
        }
        $today = getdate();
        $result .= rss_full_tag("copyright",2,false,"&copy; ".$today['year']." ".$site->fullname);
        if (!empty($USER->email)) {
            $result .= rss_full_tag("managingEditor",2,false,$USER->email);
            $result .= rss_full_tag("webMaster",2,false,$USER->email);
        }

        //write image info
        //Calculate the origin
        if (empty($pixpath)) {
            if (empty($THEME->custompix)) {
                $pixpath = "$CFG->wwwroot/pix";
            } else {
                $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
            }
        }
        $rsspix = $pixpath."/i/rsssitelogo.gif";

        //write the info 
        $result .= rss_start_tag("image",2,true);
        $result .= rss_full_tag("url",3,false,$rsspix);
        $result .= rss_full_tag("title",3,false,"moodle");
        $result .= rss_full_tag("link",3,false,$CFG->wwwroot);
        $result .= rss_full_tag("width",3,false,"140");
        $result .= rss_full_tag("height",3,false,"35");
        $result .= rss_end_tag("image",2,true);
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
        
    $result = "";

    if (!empty($items)) {
        foreach ($items as $item) {
            $result .= rss_start_tag("item",2,true);
            $result .= rss_full_tag("title",3,false,$item->title);
            $result .= rss_full_tag("link",3,false,$item->link);
            $result .= rss_full_tag("pubDate",3,false,date("r",$item->pubdate));
            //Include the author if exists 
            if (isset($item->author)) {
                $item->description = get_string("byname","",$item->author)."<p>".$item->description;
            }
            $result .= rss_full_tag("description",3,false,$item->description);
            $result .= rss_end_tag("item",2,true);

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
    $result = "";

    //Close the chanel
    $result .= rss_end_tag("channel",1,true);
    ////Close the rss tag
    $result .= "</rss>";

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
function rss_start_tag($tag,$level=0,$endline=false) {
    if ($endline) {
       $endchar = "\n";
    } else {
       $endchar = "";
    }
    return str_repeat(" ",$level*2)."<".$tag.">".$endchar;
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
function rss_full_tag($tag,$level=0,$endline=true,$content,$to_utf=true) {
    //Here we encode absolute links
    $st = rss_start_tag($tag,$level,$endline);
    $co="";
    if ($to_utf) {
        $co = preg_replace("/\r\n|\r/", "\n", utf8_encode(htmlspecialchars($content)));
    } else {
        $co = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
    }
    $et = rss_end_tag($tag,0,true);
    return $st.$co.$et;
}

//////////////////// LIBRARY FUNCTIONS FOR RSS_CLIENT BLOCK ////////////////

//initialize config vars for rss_client block if missing
if (empty($CFG->block_rss_client_submitters) ) {
    $CFG->block_rss_client_submitters = 1; //default to admin only
}
if (empty($CFG->block_rss_client_num_entries) ) {
    $CFG->block_rss_client_num_entries = 5; //default to 5 entries per block
}
if (empty($CFG->block_rss_timeout) ) {
    $CFG->block_rss_timeout = 30;
}

/**
 *   Determines whether or not to get a news feed remotely or from cache and reads it into a string
 * @param int rssid - id of feed in blog_rss table
 * @param string url - url of remote feed
 * @param string type - either 'A' or 'R' where A is an atom feed and R is either rss or rdf
 * @return Atom|MagpieRSS|null This function returns an Atom object in the case of an Atom feed, a MagpieRSS object in the case of an RDF/RSS feed or null if there was an error loading the remote feed.
 * NOTE that this function requires allow_url_fopen be On in your php.ini file 
 * (it may be off for security by your web host)
 */
function rss_get_feed($rssid, $url, $type) {
    
    global $CFG;
    $writetofile = false;
    $errorstring = '';

    $urlfailurestring = '<p>Failed to open remote news feed at: ' . $url .'</p><ul>Troubleshooting suggestions:<li> Ensure that the setting <strong>allow_url_fopen</strong> is <strong>On</strong> in the php.ini. For more details on why this setting is needed for this file wrapper call to work please refer to <a href="http://us2.php.net/filesystem">http://us2.php.net/filesystem</a></li><li>Ensure that you do not have a proxy enabled between your server and the remote site. If it is possible for you to launch a web browser on your server then you can test this by load the remote URL on the server itself.</li></ul>';
    $filefailurestring = 'Could not open the file located at: ';
    $secs = $CFG->block_rss_timeout * 60;

    // If moodle dataroot cache folder is missing create it
    if (!file_exists($CFG->dataroot .'/cache/')) {
        mkdir($CFG->dataroot .'/cache');
    }
    // If moodle dataroot cache/rsscache folder is missing create it
    if (!file_exists($CFG->dataroot .'/cache/rsscache/')) {
        mkdir($CFG->dataroot .'/cache/rsscache');
    }

    $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
//    echo "file = ". $file; //debug
    
    //if feed in cache
    if (file_exists($file)) {
        //check age of cache file
    //      echo "file exists $file"; //debug
    
        //get file information capturing any error information
        ob_start();
        $data = stat($file);
        $errorstring .= ob_get_contents();
        ob_end_clean();

        $now = time();
        //Note: there would be a problem here reading data[10] if the above stat() call failed
        if (($now - $data[10]) > $secs) {
            // The cached file has expired. Attempt to read fresh from source
            $xml = load_feed_from_url($url);
            if (!empty($xml) && !empty($xml->xml) && empty($xml->ERROR)) {
                //success
                $writetofile = true;
            } else {
                // Failed to load remote feed. Since the file exists attempt to read from cache
                if ($CFG->debug) {
                    if (isset($xml) && isset($xml->ERROR)) {
                       $errorstring = $xml->ERROR . $errorstring .'<br />';
                    }
                    $errorstring = $urlfailurestring .'<br /><br />'. $errorstring .'<br />';
                }
                $xml = load_feed_from_file($file);
                if (!empty($xml) && empty($xml->xml) && !empty($xml->ERROR)) {
                    // Failed to load from cache as well!
                    if ($CFG->debug) {
                        if (!empty($xml) && !empty($xml->ERROR)) {
                           $errorstring = $xml->ERROR . $errorstring;
                        }
                        $errorstring = $filefailurestring . $file .'<br /><br />'. $errorstring .'<br />';
                        $err->ERROR = $errorstring .'<br />';
                        return $err;
                    }
                }
            }
        } else {
            // Cached file has not expired. Attempt to read from cached file.
            $xml = load_feed_from_file($file);
            if (!empty($xml) && empty($xml->xml) && !empty($xml->ERROR)) {
                // Failed to load from cache, attempt to read from source
                if ($CFG->debug) {
                    if (!empty($xml) && !empty($xml->ERROR)) {
                       $errorstring = $xml->ERROR . $errorstring .'<br />';
                    }
                    $errorstring = $filefailurestring . $file .'<br /><br />'. $errorstring .'<br />';
                }
                $xml = load_feed_from_url($url);
                if (!empty($xml) && !empty($xml->xml) && empty($xml->ERROR)) {
                    // success
                    $writetofile = true;
                } else {
                    // Failed to read from source as well!
                    if ($CFG->debug) {
                        if (!empty($xml) && !empty($xml->ERROR)) {
                           $errorstring = $xml->ERROR . $errorstring;
                        }
                        $errorstring = $urlfailurestring .'<br /><br />'. $errorstring .'<br />';
                        $err->ERROR = $errorstring .'<br />';
                        return $err;
                    }
                    return;
                }
            }
        }
    } else { 
        // No cached file at all, read from source
        $xml = load_feed_from_url($url);
        if (!empty($xml) && !empty($xml->xml) && empty($xml->ERROR)) {
            //success
            $writetofile = true;
        } else {
            // Failed to read from source url!
            if ($CFG->debug) {
                if (!empty($xml) && !empty($xml->ERROR)) {
                   $errorstring = $xml->ERROR . $errorstring .'<br />';
                }
                $errorstring = $urlfailurestring .'<br /><br />'. $errorstring .'<br />';
                $err->ERROR = $errorstring .'<br />';
                return $err;
            }
            return;
        }
    }
    
    // echo 'DEBUG: raw xml was loaded successfully:<br />';//debug
    //print_object($xml); //debug
    
    //implode xml file. in some cases this operation may fail, capture failure info to errorstring.
    ob_start();
    $xmlstr = implode(' ', $xml->xml);
    $errorstring .= ob_get_contents();
    ob_end_clean();
    //print_object($xmlstr);
    
    if ( $writetofile && !empty($xmlstr) ) { //write file to cache
        // jlb: adding file:/ to the start of the file name fixed
        // some caching problems that I was experiencing.
        //$file="file:/" + $file;
        file_put_contents($file, $xmlstr);
    }
    
    if (empty($xmlstr) && !empty($errorstring)) {
        $err->ERROR = 'XML file failed to implode correctly:<br /><br />'. $errorstring .'<br />';
        return $err;
    }
    
    if ($type == 'A') {
        //note: Atom is being modified by a working group
        //http://www.mnot.net/drafts/draft-nottingham-atom-format-02.html
        include_once($CFG->dirroot .'/rss/class.Atom.php');
        $atom = new Atom($xmlstr);
        $atom->channel = $atom->feed;
        $atom->items = $atom->entries;
        $atom->channel['description'] = $atom->channel['tagline'];
        for($i=0;$i<count($atom->items);$i++) {
            $atom->items[$i]['description'] = $atom->items[$i]['content'];
        }
        return $atom;
    } else {
        include_once($CFG->dirroot .'/rss/class.RSS.php');
        $rss = new MagpieRSS($xmlstr);
        return $rss;
    }
}

/**
 * @param string $file The path to the cached feed to load
 * @return stdObject Object with ->xml string value and ->ERROR string value if applicable.
 */
function load_feed_from_file($file) {
    global $CFG;
    $errorstring = '';
//          echo "read from cache"; //debug
    //read in from cache
    ob_start();
    $xml = file($file);
    $errorstring .= ob_get_contents();
    ob_end_clean();

    $returnobj->xml = $xml;
    if (!empty($errorstring)){
        $returnobj->ERROR = 'XML file failed to load:<br /><br />'. $errorstring .'<br />';
    }
    return $returnobj;
}

/**
 * @param string $url The url of the remote news feed to load
 * @return stdObject Object with ->xml string value and ->ERROR string value if applicable.
 */
function load_feed_from_url($url) {
    global $CFG;
//          echo "read from source url"; //debug
    //read from source url
    $errorstring = '';
//          echo "read from cache"; //debug
    //read in from cache
    ob_start();
    $xml = file($url);
    $errorstring .= ob_get_contents();
    ob_end_clean();

    $returnobj->xml = $xml;
    if (!empty($errorstring)){
        $returnobj->ERROR = 'XML url failed to load:<br />'. $errorstring;
    }
    return $returnobj;

}

/**
 * @param int $rssid .
 */
function rss_display_feeds($rssid='none') {
    global $db, $USER, $CFG, $THEME;
    global $blogid; //hackish, but if there is a blogid it would be good to preserve it

    $closeTable = false;
    //Daryl Hawes note: convert this sql statement to a moodle function call
    if ($rssid != 'none'){
        $sql = 'SELECT * FROM '. $CFG->prefix .'block_rss_client WHERE id='. $rssid;
    } else {
        $sql = 'SELECT * FROM '. $CFG->prefix .'block_rss_client';
    }
    
    $res = $db->Execute($sql);
//    print_object($res); //debug
    
    if ($res->fields){
        $closeTable = true;
        ?>
            <table width="100%">
            <tr bgcolor="<?php echo $THEME->cellheading;?>" class="forumpostheadertopic">
                <td><?php print_string('block_rss_feed', 'block_rss_client'); ?></td>
                <td><?php print_string('edit'); ?></td>
                <td><?php print_string('delete'); ?></td>
            </tr>
        <?php
    }
    
    if (isset($res) && $res->fields){
        while(!$res->EOF) {
            $editString = '&nbsp;';
            $deleteString = '&nbsp;';
            if ($res->fields['userid'] == $USER->id || isadmin()){
                $editString = '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?act=rss_edit&rssid='. $res->fields['id'] .'&blogid='. $blogid .'">';
                $editString .= '<img src="'. $CFG->pixpath .'/t/edit.gif" alt="'. get_string('edit');
$editString .= '" title="'. get_string('edit') .'" align="absmiddle" height=\"16\" width=\"16\" border=\"0\" /></a>';
                
                $deleteString = '<a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?act=delfeed&rssid='. $res->fields['id'];
                $deleteString .= '&blogid='. $blogid .'" onClick="return confirm(\''. get_string('block_rss_delete_feed_confirm', 'block_rss_client') .'\');">';
                $deleteString .= '<img src="'. $CFG->pixpath .'/t/delete.gif" alt="'. get_string('delete');
$deleteString .= '" title="'. get_string('delete') .'" align="absmiddle" border=\"0\" /></a>';
            }
            print '<tr bgcolor="'. $THEME->cellcontent .'" class="forumpostmessage"><td><strong><a href="'. $CFG->wwwroot .'/blocks/rss_client/block_rss_client_action.php?act=view&rssid=';
            print $res->fields['id'] .'&blogid='. $blogid .'">'. $res->fields['title'] .'</a></strong><br />' ."\n";
            print $res->fields['description'] .'&nbsp;<br />' ."\n";
            print $res->fields['url'] .'&nbsp;&nbsp;<a href="'. $res->fields['url'] .'" target=_new><img src="'. $CFG->pixpath .'/blog/xml.gif" border="0" /></a>' ."\n";
            print '<a href="http://feeds.archive.org/validator/check?url='. $res->fields['url'] .'">(Validate)</a>';
            print '</td><td align="center">'. $editString .'</td>' ."\n";
            print '<td align=\"center\">'. $deleteString .'</td>' ."\n";
            print '</tr>'."\n";
            $res->MoveNext();
        }
    }
    if ($closeTable){
        print '</table>'."\n";
    }
}

/**
 * @param string $act .
 * @param string $url .
 * @param int $rssid .
 * @param string $rsstype .
 * @param bool $printnow .
 */
function rss_get_form($act, $url, $rssid, $rsstype, $printnow=true) {
    global $USER, $CFG, $_SERVER, $blockid, $blockaction;
    global $blogid; //hackish, but if there is a blogid it would be good to preserve it

    $returnstring = '<table><tr><td valign=\"top\">'; 
    if ($act == 'rss_edit') { 
        $returnstring .= get_string('edit'); 
    } else { 
        $returnstring .= get_string('block_rss_add_new', 'block_rss_client');
    }
    $returnstring .= '  '. get_string('block_rss_feed', 'block_rss_client');
    
    $returnstring .= '</td></tr><tr><td>';
    
    $returnstring .= '<form action="'. $_SERVER['PHP_SELF'] .'" method=POST name="block_rss">';
    $returnstring .= 'URL: <input type="text" size="32" maxlength="128" name="url" value="';
    if ($act == 'rss_edit') { 
        $returnstring .= $url; 
    } 
    
    $returnstring .= '" /><br /><select name="rsstype"><option value="R">RSS/RDF</option>
    <option value="A"';
    if ($act == 'rss_edit' and $rsstype == 'A') {
        $returnstring .= ' selected';
    } 
    
    $returnstring .= '>Atom</option></select>';
    
    $returnstring .= '<input type="hidden" name="act" value="';
    if ($act == 'rss_edit') {
        $returnstring .= 'updfeed';
    } else {
        $returnstring .= 'addfeed';
    } 
    $returnstring .= '" />';
    if ($act == 'rss_edit') { 
        $returnstring .= '<input type="hidden" name="rssid" value="'. $rssid .'" />'. "\n"; 
    } 
    $returnstring .= '<input type="hidden" name="blogid" value="'. $blogid .'" />';
    $returnstring .= '<input type="hidden" name="user" value="'. $USER->id .'" />';
    $returnstring .= '<input type="submit" value="';
    if ($act == 'rss_edit') {
        $returnstring .= get_string('update'); 
    } else { 
        $returnstring .= get_string('add'); 
    }
    $returnstring .= '" />&nbsp;</form>';
    
    $returnstring .= '<ul>' . get_string('block_rss_find_more_feeds', 'block_rss_client');
// removed as this is possibly out of place here
//    $returnstring .= '<li><a href="http://www.syndic8.com" target="_new">syndic8</a> <li><a href="http://www.newsisfree.com" target="_new">NewsIsFree</A>';
    $returnstring .= '</ul>';
    $returnstring .= '</td></tr></table>';
    
    if ($printnow){
        print $returnstring;
    }
    return $returnstring;
}

/**
 * added by Daryl Hawes for rss/atom feeds
 * found at http://us4.php.net/manual/en/function.fwrite.php
 * added check for moodle debug option. if off then use '@' to suppress error/warning messages
 * @param string $filename .
 * @param string $content .
 */
if (! function_exists('file_put_contents')){
    function file_put_contents($filename, $content) {
        global $CFG;
        $nr_of_bytes = 0;
        if ($CFG->debug){
            if (($file = fopen($filename, 'w+')) === false) return false;
        } else {
            if (($file = @fopen($filename, 'w+')) === false) return false;
        }
        if ($CFG->debug){
            if ($nr_of_bytes = fwrite($file, $content, strlen($content)) === false) return false;
        } else {
            if ($nr_of_bytes = @fwrite($file, $content, strlen($content)) === false) return false;
        }        
        fclose($file);
        return $nr_of_bytes;
    }
}
?>
