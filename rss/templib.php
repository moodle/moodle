<?php

global $CFG;

//initialize config vars for rss_client block if missing
if (empty($CFG->block_rss_client_submitters) ) {
    $CFG->block_rss_client_submitters = 2; //default to admin only
}
if (empty($CFG->block_rss_client_num_entries) ) {
    $CFG->block_rss_client_num_entries = 5; //default to 5 entries per block
}

/*
* rss_get_feed
*   Determines whether or not to get a news feed remotely or from cache and reads it into a string
* rssid - id of feed in blog_rss table
* url - remote url of feed
* type - either 'A' or 'R' where A is an atom feed and R is either rss or rdf
* NOTE that this requires allow_url_fopen be On in your php.ini file (it may
*  be off for security by your web host)
*/
function rss_get_feed($rssid, $url, $type) {
    
    global $CFG;;
    $write = 0;

    $secs = $CFG->block_rss_timeout * 60;

    if (!file_exists($CFG->dataroot .'/cache/')) {
        mkdir($CFG->dataroot .'/cache');
    }
    if (!file_exists($CFG->dataroot .'/cache/rsscache/')) {
        mkdir($CFG->dataroot .'/cache/rsscache');
    }
    $file = $CFG->dataroot .'/cache/rsscache/'. $rssid .'.xml';
//    echo "file = ". $file; //debug
    
    //if feed in cache
    if (file_exists($file)) {
        //check age of cache file
    //      echo "file exists $file"; //debug
        if ($CFG->debug){
            $data = stat($file);
        } else {
            $data = @stat($file);
        }
        $now = time();
        if (($now - $data[10]) > $secs) { //if timedout
    //          echo "read from original"; //debug
            //read from source
            if ($CFG->debug){
                $xml = file($url) or die ('Could not open the feed located at the url: ' . $url . 'allow_url_fopen needs to be On in the php.ini file for this file wrapper call to work. Please refer to <a href="http://us2.php.net/filesystem">http://us2.php.net/filesystem</a>');
            } else {
                $xml = @file($url) or die ('Could not open the feed located at the url: ' . $url .'allow_url_fopen needs to be On in the php.ini file for this file wrapper call to work. Please refer to <a href="http://us2.php.net/filesystem">http://us2.php.net/filesystem</a>');
            }
            $write = 1;
        } else {
    //          echo "read from cache"; //debug
            //read in from cache
            if ($CFG->debug){
                $xml = file($file) or die ('Could not open the file located at: ' . $file);
            } else {
                $xml = @file($file) or die ('Could not open the file located at: ' . $file);
            }
        }
    } else { //DNE, read from source
    //      echo "url: ".$url; //debug

        if ($CFG->debug){
            $xml = file($url) or die ('Could not open the feed located at the url: ' . $url . 'allow_url_fopen needs to be Onin the php.ini file for this file wrapper call to work. Please refer to <a href="http://us2.php.net/filesystem">http://us2.php.net/filesystem</a>');
        } else {
            $xml = @file($url) or die ('Could not open the feed located at the url: ' . $url . 'allow_url_fopen needs to be On in the php.ini file for this file wrapper call to work. Please refer to <a href="http://us2.php.net/filesystem">http://us2.php.net/filesystem</a>');
        }
        $write = 1;
    }
    
    //print_object($xml); //debug
    if ($CFG->debug){
        $xmlstr = implode(' ', $xml);
    } else {
        $xmlstr = @implode(' ', $xml);
    }
    
    if ( $write && !empty($xmlstr) ) { //write file to cache
        // jlb: adding file:/ to the start of the file name fixed
        // some caching problems that I was experiencing.
        //$file="file:/" + $file;
        file_put_contents($file, $xmlstr);
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
        <?
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