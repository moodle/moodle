<?PHP  // $Id$
       // This file contains all the common stuff to be used in RSS System

//This function prints the icon (from theme) with the link to rss/file.php
function rss_print_link($courseid, $userid, $modulename, $id, $tooltiptext="") {

 global $CFG, $THEME, $USER;

    static $pixpath = '';
    static $rsspath = '';

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

     echo "<a href=\"".$rsspath."\"><img src=\"$rsspix\" title=\"$tooltiptext\"></a>";

}

//This function iterates over each module in the server to see if
//it supports generating rss feeds, searching for a MODULENAME_rss_feeds()
//function and invoking it foreach activity as necessary
function cron_rss_feeds () {

    global $CFG;

    $status = true;
   
    echo "    Generating rssfeeds...\n";

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        echo "        ERROR: You need to add XML support to your PHP installation!\n";
        return true;
    }

    if ($allmods = get_records("modules") ) {
        foreach ($allmods as $mod) {
            echo '        '.$mod->name.': ';
            $modname = $mod->name;
            $modfile = "$CFG->dirroot/mod/$modname/rsslib.php";
            //If file exists and we have selected to restore that type of module
            if (file_exists($modfile)) {
                include_once($modfile);
                $generaterssfeeds = $modname.'_rss_feeds';
                if (function_exists($generaterssfeeds)) {
                    if ($status) {
                        echo 'generating ';
                        $status = $generaterssfeeds();
                        if (!empty($status)) {
                            echo "...OK\n";
                        } else {
                            echo "...FAILED\n";
                        }
                    } else {
                        echo "...SKIPPED (failed above)\n";
                    }
                } else {
                    echo "...NOT SUPPORTED (function)\n";
                }
            } else {
                echo "...NOT SUPPORTED (file)\n";
            }
        }
    }
    echo "    Ending  rssfeeds...";
    if (!empty($status)) {
        echo "...OK\n";
    } else {
        echo "...FAILED\n";
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
        $file = $basedir .= "/".$mod->id.".xml";
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
        $result .= rss_full_tag("language",2,false,substr($USER->lang,0,2));
        $today = getdate();
        $result .= rss_full_tag("copyright",2,false,"&copy; ".$today['year']." ".$site->fullname);
        $result .= rss_full_tag("managingEditor",2,false,$USER->email);
        $result .= rss_full_tag("webMaster",2,false,$USER->email);

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

?>
